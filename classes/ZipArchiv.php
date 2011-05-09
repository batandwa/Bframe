<?php

/* vim: set expandtab sw=4 ts=4 sts=4: */
/**
 *
 * @version $Id: ZipArchiv.php,v 1.1 2009/09/11 18:36:12 Administrator Exp $
 */

/**
 * Zip file creation class.
 * Makes zip files.
 *
 * Based on :
 *
 *  http://www.zend.com/codex.php?id=535&single=1
 *  By Eric Mueller <eric@themepark.com>
 *
 *  http://www.zend.com/codex.php?id=470&single=1
 *  by Denis125 <webmaster@atlant.ru>
 *
 *  a patch from Peter Listiak <mlady@users.sourceforge.net> for last modified
 *  date and time of the compressed file
 *
 * Official ZIP file format: http://www.pkware.com/appnote.txt
 *
 * @access  public
 */
class ZipArchiv extends Archive
{
	public function __construct()
	{
		parent::__construct();
	}
	
	private static $error = "";
	
	/**
	 * Array to store compressed data
	 *
	 * @var  array    $datasec
	 */
	public $datasec = array ();

	/**
	 * Central directory
	 *
	 * @var  array    $ctrl_dir
	 */
	public $ctrl_dir = array ();

	/**
	 * End of central directory record
	 *
	 * @var  string   $eof_ctrl_dir
	 */
	public $eof_ctrl_dir = "\x50\x4b\x05\x06\x00\x00\x00\x00";

	/**
	 * Last offset position
	 *
	 * @var  integer  $old_offset
	 */
	public $old_offset = 0;
	
	public static function getError()
	{
		return self::$error;
	} 
	
	/**
	 * Extract files from the zip archive. 
	 * @param $archive The path to the archive.
	 * @param $destFolderPath The destination path for the extracted files
	 * @param $autoCreatePath Automatically create a directory with the same name
	 *                        as the archive file in the destination directory.
	 * @return void 
	 */
	public static function unpack($archive, $destFolderPath=null, $autoCreatePath=false)
	{
		$zipHandle = zip_open($archive);
		$error = "";
		$return = array();
		$return["errorNum"] = 0;

//		if($zipHandle != ZipArchive::ER_OK)
		if(!is_resource($zipHandle))
		{
			switch($zipHandle)
			{
				case ZipArchive::ER_MULTIDISK:
				{
					$error = "Multi-disk zip archives not supported.";
				}
				case ZipArchive::ER_RENAME:
				{
					$error = "Renaming temporary file failed.";
				}
				case ZipArchive::ER_CLOSE:
				{
					$error = "Closing zip archive failed";
				}
				case ZipArchive::ER_MULTIDISK:
				{
					$error = "Multi-disk zip archives not supported.";
				}
				case ZipArchive::ER_SEEK:
				{
					$error = "Seek error.";
				}
				case ZipArchive::ER_READ:
				{
					$error = "Read error.";
				}
				case ZipArchive::ER_WRITE:
				{
					$error = "Write error.";
				}
				case ZipArchive::ER_CRC:
				{
					$error = "CRC error.";
				}
				case ZipArchive::ER_ZIPCLOSED:
				{
					$error = "Containing zip archive was closed.";
				}
				case ZipArchive::ER_NOENT:
				{
					$error = "No such file.";
				}
				case ZipArchive::ER_EXISTS:
				{
					$error = "File already exists.";
				}
				case ZipArchive::ER_OPEN:
				{
					$error = "Can't open file ". $archive . ".";
				}
				default:
				{
					$error = "Error opening archive ". $archive . ".";
				}
			}
		}
		
		if(empty($error))
		{
			$destFolderPath = empty($destFolderPath) ? dirname($archive).Path::DS : $destFolderPath; 
			
			//Go through the files in the zip
			while($fileHandle = zip_read($zipHandle))
			{
				zip_entry_open($zipHandle, $fileHandle);
				$extension = pathinfo($archive, PATHINFO_EXTENSION);
				
				$destFilePath = $destFolderPath.Path::DS;
				$destFilePath .= $autoCreatePath ? "/".basename($archive, ".".$extension)."/" : "";
				$destFilePath .= zip_entry_name($fileHandle);
	
				$destPathCreated = Path::create($destFilePath);
				if($destPathCreated)
				{
//				if(Path::isDirPath($destFilePath))
//				{
//					Path::create($destFilePath);
//				}
//				else
//				{
					$destFilePath = Path::fix($destFilePath);
					if(!is_dir($destFilePath))
					{
						$destFileHandle = fopen($destFilePath, "w");
						if($destFileHandle !== false)
						{
							fwrite($destFileHandle, zip_entry_read($fileHandle, zip_entry_filesize($fileHandle)));
							fclose($destFileHandle);
							chmod($destFilePath, 0777);
							zip_entry_close($fileHandle);
						}
					}
//				}
				}
				else
				{
					$error .= " Error creating destination path.";
					$return["errorNum"] = 99;
				}
			}
			
			zip_close($zipHandle);
		}
		
		self::$error = $error;
		
		$return["error"] = trim($error);
		$return["errorNum"] += is_numeric($zipHandle) ? $zipHandle : 0;
		$return["dest"] = $destFolderPath;
		
		return $return;
	}
	
	/**
	 * Converts an Unix timestamp to a four byte DOS date and time format (date
	 * in high two bytes, time in low two bytes allowing magnitude comparison).
	 *
	 * @param  integer  the current Unix timestamp
	 * @return integer  the current date in a four byte DOS format
	 * @access private
	 */
	function unix2DosTime($unixtime = 0)
	{
		$timearray = ($unixtime == 0) ? getdate() : getdate($unixtime);

		if ($timearray['year'] < 1980)
		{
			$timearray['year'] = 1980;
			$timearray['mon'] = 1;
			$timearray['mday'] = 1;
			$timearray['hours'] = 0;
			$timearray['minutes'] = 0;
			$timearray['seconds'] = 0;
		} // end if

		return (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) | ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
	} // end of the 'unix2DosTime()' method

	/**
	 * Adds a file to the archive containing the specified data.
	 * @param  string   file contents
	 * @param  string   name of the file in the archive (may contains the path)
	 * @param  integer  The current timestamp
	 * @access public
	 */
	public function createFile($data, $name, $time = 0)
	{
		$name = str_replace('\\', '/', $name);

		$dtime = dechex($this->unix2DosTime($time));
		$hexdtime = '\x' . $dtime[6] . $dtime[7] . '\x' . $dtime[4] . $dtime[5] . '\x' . $dtime[2] . $dtime[3] . '\x' . $dtime[0] . $dtime[1];
		eval ('$hexdtime = "' . $hexdtime . '";');

		$fr = "\x50\x4b\x03\x04";
		$fr .= "\x14\x00"; // ver needed to extract
		$fr .= "\x00\x00"; // gen purpose bit flag
		$fr .= "\x08\x00"; // compression method
		$fr .= $hexdtime; // last mod time and date

		// "local file header" segment
		$unc_len = strlen($data);
		$crc = crc32($data);
		$zdata = gzcompress($data);
		$zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2); // fix crc bug
		$c_len = strlen($zdata);
		$fr .= pack('V', $crc); // crc32
		$fr .= pack('V', $c_len); // compressed filesize
		$fr .= pack('V', $unc_len); // uncompressed filesize
		$fr .= pack('v', strlen($name)); // length of filename
		$fr .= pack('v', 0); // extra field length
		$fr .= $name;

		// "file data" segment
		$fr .= $zdata;

		// "data descriptor" segment (optional but necessary if archive is not
		// served as file)
		// nijel(2004-10-19): this seems not to be needed at all and causes
		// problems in some cases (bug #1037737)
		//$fr .= pack('V', $crc);                 // crc32
		//$fr .= pack('V', $c_len);               // compressed filesize
		//$fr .= pack('V', $unc_len);             // uncompressed filesize

		// add this entry to array
		$this->datasec[] = $fr;

		// now add to central directory record
		$cdrec = "\x50\x4b\x01\x02";
		$cdrec .= "\x00\x00"; // version made by
		$cdrec .= "\x14\x00"; // version needed to extract
		$cdrec .= "\x00\x00"; // gen purpose bit flag
		$cdrec .= "\x08\x00"; // compression method
		$cdrec .= $hexdtime; // last mod time & date
		$cdrec .= pack('V', $crc); // crc32
		$cdrec .= pack('V', $c_len); // compressed filesize
		$cdrec .= pack('V', $unc_len); // uncompressed filesize
		$cdrec .= pack('v', strlen($name)); // length of filename
		$cdrec .= pack('v', 0); // extra field length
		$cdrec .= pack('v', 0); // file comment length
		$cdrec .= pack('v', 0); // disk number start
		$cdrec .= pack('v', 0); // internal file attributes
		$cdrec .= pack('V', 32); // external file attributes - 'archive' bit set

		$cdrec .= pack('V', $this->old_offset); // relative offset of local header
		$this->old_offset += strlen($fr);

		$cdrec .= $name;

		// optional extra field, file comment goes here
		// save to central directory
		$this->ctrl_dir[] = $cdrec;
	} // end of the 'addFile()' method

	/**
	 * Dumps out file
	 * @return string The zipped file
	 * @access public
	 */
	public function dump()
	{
		$data = implode('', $this->datasec);
		$ctrldir = implode('', $this->ctrl_dir);

		return $data .
		$ctrldir .
		$this->eof_ctrl_dir .
		pack('v', sizeof($this->ctrl_dir)) . // total # of entries "on this disk"
		pack('v', sizeof($this->ctrl_dir)) . // total # of entries overall
		pack('V', strlen($ctrldir)) . // size of central dir
		pack('V', strlen($data)) . // offset to start of central dir
		"\x00\x00"; // .zip file comment length
	} // end of the 'file()' method
	

} // end of the 'zipfile' class
?>

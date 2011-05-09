<?php
class Path extends Base
{
	/**
	 * The direcory separator that serves as the default 
	 * @var string
	 */
	const DS = "/";
	/**
	 * Whether or not to remove /../ when fixing the path. This will 
	 * make the path absolute.
	 * @var int 
	 */
	const FIX_SUBDIR_MARKERS = 1;
	const FIX_DBL_DS = 2; //Double directory separators
	const FIX_SAME_DIR_SPEC = 4; //Removes occurances of /./
	const FIX_ALL = 255; //Removes occurances of /./
	
	/**
	 * The actual path with all components split into an array
	 * @var array
	 */
	private $path;
	/**
	 * The actual separator of directories ("\" for windows and "/" for sane
	 *  systems)
	 * @var string
	 */
	private $separator = "/";
	private $dir;
	private $root;
	
	
	static public function fix($path, $options=self::FIX_ALL)
	{
//		echo "TO BE FIXED :".$path;
//		$fixed = $path;
		$fixed = new Path($path);
		
		
//		echo "       FIXED :".$fixed;
//		$fixed = trim($fixed,"/");
//		$fixed = (strpos($path,"/")==0) ? "/".$fixed : $fixed;
//		$fixed = (strrpos($path,"/")==(strlen($path)-1)) ? $fixed."/" : $fixed;

		return $fixed;
		
	}
	
	/**
	 * Checks if this is a directory path ie. ends with a seperator. Will
	 * return true even if the physical directory does not exist.
	 * @return bool Returns true if the path ends with a directory seperator.
	 */
	static public function isDirPath($path)
	{
		echo '<pre style="font-size:11px;">'.debug_print_backtrace();
		$start = strlen($path)-1;
		return (strpos($path, self::DS, $start)==$start) !== false;
	}
	
	static public function create($path, $permisions=0777)
	{
		$path = self::fix($path);
		$pathCreated = false;
		
		var_dump(func_get_args());
		if(self::isDirPath($path) && !file_exists($path))
		{
			$pathCreated = mkdir($path, $permisions, true);
//			echo "create folder ".$path."<br>";
		}
		else if(!file_exists($path))
		{
			$parentExists = false;
			
			//If this is a file (not a directory) create its parent directory.
			if(!is_dir(dirname($path)))
			{
				$parentExists = self::create(dirname($path)."/");
//				echo "create file's folder ".$path."<br>";
			}
			
			if($parentExists)
			{
				//Create the file. Open (for writing) and close it. 
				$pathCreated = fclose(fopen($path, "w"));
			}
//			echo "create file ".$path."<br>";
		}
		
		if($pathCreated)
		{
			$pathCreated = chmod($path, $permisions);
		}
		
		return $pathCreated;
	}
	
	/**
	 * 
	 * @param $path The initial path.
	 * @return void
	 */
	public function __construct($path, $separator=null)
	{
		$this->separator = ($separator === null) ? self::DS : $separator;
		$this->path = explode($this->separator,$path);
		$this->root = strpos($path,"/")==0;
		$this->dir = strrpos($path,"/")==(strlen($path)-1);
		
		$this->fixPath();
	}
	
	/**
	 * Outputs the path as a conventional string path.
	 * @return void
	 */
	public function __toString()
	{
		$path = implode($this->path, $this->separator);
		$path .= $this->dir ? "/" : "";
		$path = $this->root ? "/".$path : $path;
		$path = str_replace(str_repeat($this->separator, 2), $this->separator, $path);
		
//		$path = (strpos($path,"/")==0) ? $fixed : ltrim($fixed,"/");
//		$path = (strrpos($path,"/")==(strlen($path)-1)) || (strrpos($path,"..")==(strlen($path)-2)) ? $fixed : rtrim($fixed,"/");
		return $path; 
	}
	
	private function fixPath($options = self::FIX_ALL)
	{
	
//		if(($options & self::FIX_DBL_DS) != 0)
//		{
//			//Remove double dir seperators
//			$fixed = str_replace("//", "/", $fixed);
//			$fixed = str_replace("\\\\", "/", $fixed);
//		}
//		if(($options & self::FIX_SUBDIR_MARKERS) != 0)
//		{
//			$fixed = preg_replace("/\/[^\/]+?\/\.\.\/?/", "/", $fixed);
////			$fixed = realpath($fixed);
//		}

		foreach($this->path as $level => $name)
		{
			if(($options & self::FIX_SUBDIR_MARKERS) != 0)
			{
				if($name == "..")
				{
					if($level == 0)
					{
						throw new Exception("Sub directory indicator (..) not supported as root.");
					}
					unset($this->path[$level]);
					unset($this->path[$level-1]);
				}
			}
			
			if(($options & self::FIX_SAME_DIR_SPEC) != 0)
			{
				if($name == ".")
				{
					unset($this->path[$level]);
				}
			}
			
			if(empty($name))
			{
				unset($this->path[$level]);
			}
			
//			if(($options & self::FIX_SAME_DIR_SPEC) != 0)
//			{
//				$endless =0;
//				while(strrpos($fixed, "/.") !== (strlen($fixed)-2))
//				{
//					
//					$fixed = str_replace("/.", "", $fixed);
//	//				$fixed = preg_replace("/\/\.[\/$]/", "/", $fixed);
//					if($endless++>10) break;
//					echo $fixed."<br />";
//					echo strrpos($fixed, "/.")." ----<br />";
//					echo (strlen($fixed)-2)."<br />";
//					
//				}
//				
//				//Remove double forward seperators
//	//			$fixed = realpath($fixed);
//			}
		}
		
	}
}



//$testsPassed += Path::fix("/var/")=="/var/" ? 1 : 0;
//$testCount++;
//$testsPassed += Path::fix("/var/../")=="/" ? 1 : 0;
//$testCount++;
//$testsPassed += Path::fix("/var/..")=="/" ? 1 : 0;
//$testCount++;
//$testsPassed += Path::fix("/var/./.")=="/" ? 1 : 0;
//$testCount++;
//$testsPassed += Path::fix("./var/./.")=="var" ? 1 : 0;
//$testCount++;
//$testsPassed += Path::fix("/var/././file")=="/var/file" ? 1 : 0;
//$testCount++;
//$testsPassed += Path::fix("/var/../file")=="/file" ? 1 : 0;
//$testCount++;


$testsFinished = false;
$testsPassed = 0;
$testCount = 0;
$tests = array();
$suppressOutput = false;
$testDetailsFormat = "%6s %s\n";
$testDetails = "";


ob_start();
for($testId=0; !$testsFinished; $testId++)
{
	switch($testId)
	{
		case 0:
			$testPassed = Path::fix("/var/")=="/var/";
			break;
		case 1:
			$testPassed = Path::fix("/var/../")=="/";
			break;
		case 2:
			$testPassed = Path::fix("/var/..")=="/";
			break;
		case 3:
			$testPassed = Path::fix("/var/./.")=="/var";
			break;
		case 4:
			$testPassed = Path::fix("./var/./.")=="var";
			break;
		case 5:
			$testPassed = Path::fix("/var/././file")=="/var/file";
			break;
		case 6:
			$testPassed = Path::fix("/var/../file")=="/file";
			break;
		case 7:
			$testPassed = Path::fix("/var/../file", Path::FIX_DBL_DS)=="/file";
			break;
		default:
			$testsFinished = true;
			break;
	}
	
	$testsPassed += $testPassed ? 1 : 0;
	$testDetails .= sprintf($testDetailsFormat, ($testId+0), ($testPassed)?"passed":"failed"); 
	$testCount++;
}

if($suppressOutput)
{
	ob_end_clean();
}
else
{
	ob_end_flush();
}
if($testsPassed/$testCount != 1)
{
	echo "<pre>".$testDetails."</pre>";
	trigger_error("Class(es) in ".__FILE__." failed the test scoring " . $testsPassed/$testCount . ".", E_USER_ERROR);
}
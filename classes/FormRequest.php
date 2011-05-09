<?php
class FormRequest extends Base
{
	/**
	 * The upload target is a file.
	 */
	const UPLD_ERR_TARG_IS_FILE = "FR1";
	
	/**
	 * Error creating file upload target.
	 */
	const UPLD_ERR_TARG_CREATE = "FR2";
	
	/**
	 * Can't write to target dir.
	 */
	const UPLD_ERR_TARG_NOT_WRITE = "FR3";
	
	/**
	 * Temp file does not exists.
	 */
	const UPLD_ERR_NO_TEMP = "FR4";
	
	/**
	 * Unknown.
	 */
	const UPLD_ERR_UNKNOWN = "FR5";
	
	/**
	 * Unsupported file type.
	 */
	const UPLD_ERR_WRONG_FILE_TYPE = "FR6";
	
	/**
	 * Request data.
	 * @var array An array of the request data.
	 */
	private $data;
	/**
	 * @var array The identifier for this form.
	 */
	private $id;
	/**
	 * @var string The super global array from where the data will come.
	 */
	private $array;
	
	private $validation;
	
	private $multi_dim;
	
	/**
	 * The directory where files from this form will be uploaded.
	 * @var Hashtable The directories where the respective uploaded files
	 *                are going to be stored.
	 */
	private $upload_targets;
	
	/**
	 * The names that will be given to the uploaded files.
	 * @var Hashtable The filenames that are going to be used for the uploaded
	 *                files.
	 */
	private $upload_filenames;
	
	private $upload_types;
	
	private $auto_add_upload_ext;
	
	/**
	 * Retreives and populates the class with values from $array[$id] if 
	 * $multi_dim is true else it gets all the values in $array and adds 
	 * them to $this->data.
	 *
	 * @param string $id The element within $array where all the values will
	 *                   be found.
	 * @param string $array The array where the values will be found e.g $_POST.
	 * @param bool $multi_dim When true, the values will be retreived from 
	 *                        $array[$id] other wise all the values in 
	 *                        $array will be fetched.
	 */
	public function __construct($id, $array, $multi_dim=false)
	{
		$this->data = null;
		$this->id = $id;
		$this->array = $array;
		$this->validation = new Validation();
		$this->upload_targets = new Hashtable();
		$this->upload_filenames = new Hashtable();
		$this->auto_add_upload_ext = false;
		$this->multi_dim = $multi_dim;
		
		$this->getData();
	}
	
	public function getValidation()
	{
		return $this->validation;
	}
	
	public function setValidation($valid)
	{
		$this->validation = $valid;
	}
	
	/**
	 * Gets the data in $_POST[$this->id] or $_GET[$this->id]. 
	 * This will become the form's data.
	 * @param $id The element containing the forms data
	 * @param $array The array containing the element
	 * @return void
	 */
	public function getData()
	{
		if(is_null($this->data))
		{
			switch($this->array)
			{
				case("post"):
				{
					$data = $_POST;
					break;
				}
				case("get"):
				{
					$data = $_GET;
					break;
				}
				case("files"):
				{
					$data = $_FILES;
					break;
				}
			}

			if($this->multi_dim) 
			{
				$this->data = isset($data[$this->id]) ? $data[$this->id] : null;
			}
			else 
			{
				$this->data = isset($data) ? $data : null;
			}
		}

		return $this->data;
	}
	
	public function set_upload_types($types)
	{
		if(!is_array($types))
		{
			throw new Exception("Array expected for \$types parameter.");
		}
		
		$this->upload_types = $types;
	}
	
	public function set_auto_add_upload_ext($val)
	{
		if(!is_bool($val))
		{
			trigger_error("Bool value expected for \$val parameter.");
			return;
		}
		$this->auto_add_upload_ext = $val;
	}

	/**
	 * Get $this->data[$id] if data has been populated from the request GET or 
	 * POST data.
	 * @param $id
	 * @return unknown_type
	 */
	public function get($id, $attr=null, $default=null)
	{
		if(is_null($this->data))
		{
			throw new Exception("Data for this form has not yet been fetched.");
		}
		
		if($this->array == "files")
		{
			return isset($this->data[$attr][$id]) ? $this->data[$attr][$id] : $default;
		}
		else
		{
			return isset($this->data[$id]) ? $this->data[$id] : $default;
		}
		
	}
	
	/**
	 * Checks if the form was sent.
	 * @return bool True if a form was sent, otherwise false.
	 */
	public function form_sent()
	{
		if(!$this->multi_dim)
		{
			trigger_error("Using this method is not advisable for requests that don't have \$multi_dim set. Use alernative methods of checking if the form is sent");
		}
		return !empty($this->data);
	}
	
	/**
	 * Checks if the given field exists in the FormRequest.
	 *
	 * @param string $fld The field name
	 * @return bool Whether or not the field was submitted.
	 */
	public function exists($fld)
	{
		if(!is_string($fld))
		{
			trigger_error("Field names must be string.");
			return false;
		}
		
		return isset($this->data[$fld]);
	}
	
	public function set_upload_target($field, $dir)
	{
		if(!is_string($dir))
		{
			trigger_error("\$dir not a string.");
			return false;
		}
		if(!is_string($field))
		{
			trigger_error("\$field not a string.");
			return false;
		}
		
		$this->upload_targets->set($field, $dir);
		return true;
	}
	
	public function set_upload_filename($field, $name)
	{
		if(!is_string($name))
		{
			trigger_error("\$name not a string.");
			return false;
		}
		if(!is_string($field))
		{
			trigger_error("\$field not a string.");
			return false;
		}
		
		$this->upload_filenames->set($field, $name);
		return true;
	}
	
	public function set_all_upload_targets($dir)
	{
		$fields = array_keys($this->data["name"]);
		$return = true;
		
		foreach($fields as $fld)
		{
			$return = $return && $this->set_upload_target($fld, $dir);
		}
		
		return $return;
	}
	
	public function upload_file($field, $dir=null, $name=null)
	{
		$temp_file = $this->data["tmp_name"][$field];
		$error = $this->data["error"][$field];
		$notifications =& Notification::instance();
		$type = $this->get($field, "type");
		$orig_name = $this->get($field, "name");
		
		if(is_null($dir))
		{
			$dir = $this->upload_targets->get($field);
		}
		
		if(is_null($name))
		{
			$name = $this->upload_filenames->get($field, null);
		}
		if(is_null($name))
		{
			$name = $this->data["name"][$field];
		}
		assert(!is_null($name));
		
		if($this->auto_add_upload_ext)
		{
			$ext = File::extension($orig_name);
			if(!empty($ext))
			{
				$name = $name . "." . $ext;
			}
		}
		
		if(is_null($dir))
		{
			trigger_error("No target specified for field " . $field .".");
			return false;
		}

		if(is_file($dir))
		{
			$msg = "File upload error " . self::UPLD_ERR_TARG_IS_FILE;
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			return self::UPLD_ERR_TARG_IS_FILE;
		}
		
		if(!is_dir($dir))
		{
			$target_created = mkdir($dir, 0777, true);
			
			if(!$target_created)
			{
				$msg = "File upload error " . self::UPLD_ERR_TARG_CREATE;
				$notifications->addNotification($msg, Notification::NOTIF_ERROR);
				return self::UPLD_ERR_TARG_CREATE;
			}
		}
		
		if(!is_writable($dir))
		{
			$msg = "File upload error " . self::UPLD_ERR_TARG_NOT_WRITE;
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			return self::UPLD_ERR_TARG_NOT_WRITE;
		}
		
		if(!is_file($temp_file))
		{
			$msg = "File upload error " . self::UPLD_ERR_NO_TEMP;
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			return self::UPLD_ERR_NO_TEMP;
		}
		
		if($error != UPLOAD_ERR_OK)
		{
			$msg = "File upload error " . "FR" . ($error*237);
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			return $error;
		}
		
		if(!empty($this->upload_types) && !in_array($type, $this->upload_types))
		{
			$msg = "File upload error " . self::UPLD_ERR_WRONG_FILE_TYPE;
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			return self::UPLD_ERR_WRONG_FILE_TYPE;
		}
		
		$file_uploaded =  move_uploaded_file($temp_file, $dir . $name);
		if(!$file_uploaded)
		{
			$msg = "File upload error " . self::UPLD_ERR_UNKNOWN;
			$notifications->addNotification($msg, Notification::NOTIF_ERROR);
			return self::UPLD_ERR_UNKNOWN;
		}
		
		return UPLOAD_ERR_OK;
	}
	
	public function upload_all()
	{
		$fields = array_keys($this->data["name"]);
		$return = true;
		$upload_count = 0;
		
		foreach($fields as $fld)
		{
			if($this->data["error"][$fld] != UPLOAD_ERR_NO_FILE)
			{
				$status = $this->upload_file($fld);
				$sucess = $status === 0;
				$return = $return && ($sucess);
				$sucess ? $upload_count++ : "";
			}
		}
		
		return $return ? $upload_count : $return;
	}
	
	public function files_posted()
	{
		if($this->array != "files")
		{
//			return false;
			return 0;
		}
		
		assert(isset($this->data["error"]) && is_array($this->data["error"]));
		
		$posted_count = 0;
		foreach($this->data["error"] as $err)
		{
			$posted_count += $err==4 ? 0 : 1;
		}
		
		return $posted_count;
	}
	
}

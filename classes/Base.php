<?php
class Base
{
	/**
	 * Restricts the setting of data members that have not been declared. Gets
	 * called automatically when this happens.
	 * @param $member Name of the data member.
	 * @param $value Value to be set.
	 */
	public function __set($member, $value)
	{
		trigger_error("'".get_class($this)."' does not contain data member '$member'.", E_USER_WARNING);
	}

	/**
	 * Triggered and stops the retreival of data members that are not 
	 * explicitly declared in the class.
	 * 
	 * @param $member Name of the data member.
	 */
	public function __get($member)
	{
		trigger_error("The data member '" .  $member . "' does not exist in this class.", E_USER_WARNING);
//		throw new Exception("The data member '" .  $member . "' does not exist in this class.");
	}
	
	public function setAll(&$collection, $setter, $toValue)
	{
		$setter = "set".$setter;
		foreach($collection as $ctrlId => $ctrl)
		{
			$this->controls[$ctrlId]->$setter($toValue);
		}
	}
	
	public function set($var, $to)
	{
		$setter = "set".$var;
		$this->$setter($to);
	}
	
	public static function loadClasses()
	{
		$allFound = true;
		$class_names = func_get_args();
		if(count($class_names)==1 && is_array($class_names[0]))
		{
			$class_names = $class_names[0];
		}
		foreach($class_names as $cl_nm)
		{
			$cl_file_path = "includes/wdclasses/$cl_nm.php";
			if(!class_exists($cl_nm) && is_file($cl_file_path))
			{
				require_once($cl_file_path);
			}
			else
			{
				$cl_file_path = "../".$cl_file_path;
				if(!class_exists($cl_nm) && is_file($cl_file_path))
				{
					require_once($cl_file_path);
				}
				else
				{
					$allFound = false;
				}
			}
		}
	}
}
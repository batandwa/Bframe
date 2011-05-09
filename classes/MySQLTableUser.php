<?php
class MySQLTableUser extends MySQLTable
{
	private static $instance;
	
	public static function &instance()
	{
		$session =& Session::instance();
		$table = "admin";
		
		if((!isset(self::$instance)))
		{
			$c = __CLASS__;
			self::$instance = new $c($table);
		}
		
		$user_id = $session->get("user_id");
		if(!is_null($user_id))
		{
			self::$instance->select_first("id=" . $user_id);
		}
		
		return self::$instance;
	}
}


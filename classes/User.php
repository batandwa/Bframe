<?php
class MySQLUser extends MySQLTable
{
	private static $instance;
	
	public function __construct()
	{
		$session =& Session::instance();
		$user = new MySQLTable("admin");
		$user_id = $session->get("user_id");
		if(!is_null($user_id))
		{
			$user->select("id=" . $user_id);
		}
		else 
		{
			return null;
		}
		
		self::$instance = $user;
	}
	
	public static function &instance()
	{
		if((!isset(self::$instance)))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}
}

<?php
class Registry extends Base
{
	private static $data;
	private static $instance;
	
	private function __construct()
	{
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
	
	public function set($fld, $val)
	{
		self::init_data();
		
		return self::$data->set($fld, $val);
	}
	
	public function get($fld)
	{
		self::init_data();
		
		return self::$data->get($fld);
	}
	
	private static function init_data()
	{
		static $data;
		if(is_null(self::$data) || !isset(self::$data))
		self::$data = new Hashtable();
	}
}

<?php
	throw new Exception("Class Singleton is no longer to be used.");
	
	abstract class Singleton extends Base
	{
		private static $instance;

		private function __construct()
		{
		}
		
		public static function instance($class)
		{
			if((!isset(self::$instance)))
			{
				$c = __CLASS__;
				self::$instance = new $c;
			}
			return self::$instance;
		}
		
		public function __clone()
		{
		    trigger_error('Clone is not allowed.', E_USER_ERROR);
		}
	}

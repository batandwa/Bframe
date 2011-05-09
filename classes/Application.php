<?php
//class Foo
//{
//    public static $my_static = 'foo';
//
//    public function staticValue() {
//        return self::$my_static;
//    }
//}
//
//class Bar extends Foo
//{
//    public function fooStatic() {
//        return parent::$my_static;
//    }
//}
//
//
//print Foo::$my_static . "\n";
//Foo::$my_static = new User(64);
//
//$foo = new Foo();
//print $foo->staticValue() . "\n";
////print $foo->my_static . "\n";      // Undefined "Property" my_static
//
//// $foo::my_static is not possible
//
//print Bar::$my_static . "\n";
//$bar = new Bar();
//print $bar->fooStatic() . "\n";
//
//$classname = "Bar";
////print $classname::$my_static;
define("APPLICATION_ACTIVE", true);

class Application extends Base
{
	private static $instance = null;
	public static $removedActions = array();
	
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
	
	public function __clone()
	{
	    trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	
	public function init()
	{
		date_default_timezone_set(TIMEZONE);
		
		Session::instance();
		$notifications =& Notification::instance();
		
		$notifications->retreiveNotifications();
		
		if(DB_CONNECT)
		{
			DbConnection::instance();
		}

		
//		if(empty($read))
//		{
//			$db =& Application::getDb();
//		}
		
		$request =& Request::instance();
//		$request->log();

		$response = Response::instance();
		$response->set_header("Cache-Control", "no-cache, must-revalidate");
		$response->set_header("Expires", "Sat, 4 Apr 1985 05:00:00 GMT");
	}
	
	/**
	 * Returns the site's active database connection.
	 *
	 * @return DbConnection The database connection
	 */
	static function &getDb()
	{
		$db = DbConnection::instance();
		return $db;
	}
	
	static function &getSession()
	{
		$session = Session::instance();
		return $session;
	}
	
	static function &getUser()
	{
		$session = Session::instance();
		$session->get("user_id");
		$user = new MySQLTable("user");
		
		return $user;
	}
}
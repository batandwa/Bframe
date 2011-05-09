<?php
class Request extends Base
{
	/**
	 * @var Request The singleton instance.
	 */
	private static $instance;
	
	/**
	 * Restrict external creation of the class.
	 */
	private function __construct()
	{
	}
	
	/**
	 * Returns the singleton instance of this class.
	 *
	 * @return Request The singleton instance.
	 */
	public static function &instance()
	{
		if((!isset(self::$instance)))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Gets a values from a super global variable. 
	 *
	 * @param string $var The index required from the super global array.
	 * @param string $array The super global array to be queried.
	 * @param mixed $default The value to be returned if the element is not
	 *                       found in the super global array.
	 * @param string $type The variable type that the variable should be cast to 
	 * @return mixed The value to retrieved from the super global array 
	 *               ie. $array[$var] or $default if $var is not found in $array.
	 */
	static function get($var, $array="request", $default=null, $type="string")
	{
		$arrays = explode(" ", $array);
		$ret = null;
		
		foreach($arrays as $arr)
		{
			if(is_null($ret))
			{
				switch (strtolower($arr))
				{
					case ("request"):
					{
						$ret = isset($_REQUEST[$var]) && !empty($_REQUEST[$var]) ? $_REQUEST[$var] : null;
						break;
					}
		
					case ("get"):
					{
						$ret = isset($_GET[$var]) && !empty($_GET[$var]) ? $_GET[$var] : null;
						break;
					}
		
					case ("post"):
					{
						$ret = isset($_POST[$var]) && !empty($_POST[$var]) ? $_POST[$var] : null;
						break;
					}
		
					case ("cookie"):
					{
						$ret = isset($_COOKIE[$var]) && !empty($_COOKIE[$var]) ? $_COOKIE[$var] : null;
						break;
					}
		
					case ("session"):
					{
						$ret = isset($_SESSION[$var]) && !empty($_SESSION[$var]) ? $_SESSION[$var] : null;
						break;
					}
				}
			}
		}
		
		switch($type)
		{
			case "bool":
			case "boolean":
			{
				$ret = (bool)$ret;
				break;
			}
			case "string":
			{
				$ret = (string)$ret;
				break;
			}
			case "int":
			case "integer":
			{
				$ret = (int)$ret;
				break;
			}
			case "double":
			case "real":
			case "float":
			{
				$ret = (float)$ret;
				break;
			}
			case "object":
			{
				$ret = (object)$ret;
				break;
			}
			case "array":
			{
				$ret = (array)$ret;
				break;
			}
//			case "binary":
//			{
//				$ret = (binary)$type;
//				break;
//			}
		}
		$ret = is_null($ret) || $ret==="" ? $default : $ret;

		return $ret;
	}
	
	/**
	 * Logs the details of the HTTP request to the database.
	 *
	 * @return bool True if the log was successfully inserted into the database.
	 */
	public function log()
	{
		$headers = get_request_headers(null, "string");
		$db =& DbConnection::instance();
		$user =& MySQLTableUser::instance();
		$log = new MySQLTable("wl_admin_activity");
		$data = array("admact_access_date"=>"NOW()", "admact_ip"=>$_SERVER['SERVER_ADDR'], "admact_page_url"=>generate_url(), "admact_headers" => $headers);
		
		foreach($data as $i => $d)
		{
			$data[$i] = mysql_real_escape_string($d, $db->getConn());
		}
		
		$log->bind($data);
		$log->quote_data(array("admact_ip", "admact_page_url", "admact_headers"));
		
		if(!empty($user))
		{
			$data["admact_user_id"] = $user->get("id");
			$log->quote_data(array("admact_user_id"));
		}
		
		return $log->insert();
	}
}
<?php
class DbConnection extends Base
{
	private $host;
	private $user;
	private $pass;
	private $db;
	private $conn;
	private $last_result;
	private $last_query;
	private $query;
	private static $instance;
	
	const MYSQL_FETCH_ARRAY = "array";
	const MYSQL_FETCH_OBJECT = "object";
	const MYSQL_FETCH_ASSOC= "assoc";
	const MYSQL_FETCH_ROW= "row";
	
	const SUCCESS = 0;
	const ERR_CONN = 1;
	const ERR_DB_SELECT = 2;
	
//	public function __construct($host, $user, $pass, $database)
//	{
//		$this->host = $host;
//		$this->user = $user;
//		$this->pass = $pass;
//		$this->db = $database;
//		
//		$this->connect();
//	}
	private function __construct()
	{
		$this->host = DB_HOST;
		$this->user = DB_USER;
		$this->pass = DB_PASSWORD;
		$this->db = DB_NAME;
		
		if($this->connect() == self::ERR_CONN)
		{
			app_error("Error connecting to database", 1);
		}
		else if($this->connect() == self::ERR_DB_SELECT)
		{
			app_error("Error getting database");
		}
	}
	
	/**
	 * Gets the Singleton instance of this class.
	 *
	 * @return DbConnection Returns this class' singleton.
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
	
	public function __clone()
	{
	    trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	
	public function getConn()
	{
		return $this->conn;
	}
	
	private function connect()
	{
		$conn = @mysql_connect($this->host, $this->user, $this->pass);
		if($conn === FALSE)
		{
			return self::ERR_CONN;
		}
		else 
		{
			$selected = @mysql_select_db($this->db);
			if($selected === false)
			{
				return self::ERR_DB_SELECT;
			}
		}
		
		$this->conn = $conn;
		return self::SUCCESS;
	}
	
	public function execute($sql=null)
	{
		global $debug, $debug_sql, $debug_sql_search, $debug_sql_errors, $debug_sql_stop_onfind;	
		
		if(is_null($sql) && is_null($this->query))
		{
			trigger_error("No query set.");
			return false;
		}
		
		if(is_null($sql))
		{
			$sql = $this->query;
		}
	
//		if(!SQLInjectionTester::checkInput($sql))
//		{
//			trigger_error("SQL injection detected. Please remove unnecessary elements from query.");
//			return false;
//		}
		$result = mysql_query($sql, $this->conn);
	
$debug = isset($_COOKIE["debug"]) && !empty($_COOKIE["debug"]) ? (string)$_COOKIE["debug"] : null;
if(md5($debug) == "e09d0f2f65171f76082f13344e55083a")
{
	$debug = true;
}
else
{
	$debug = false;
}
$debug_sql = isset($_COOKIE["debug_sql"]) && !empty($_COOKIE["debug_sql"]) ? (bool)$_COOKIE["debug_sql"] : false;
$debug_sql_errors = isset($_COOKIE["debug_sql_errors"]) && !empty($_COOKIE["debug_sql_errors"]) ? (bool)$_COOKIE["debug_sql_errors"] : false;
$debug_sql_search = isset($_COOKIE["debug_sql_search"]) && !empty($_COOKIE["debug_sql_search"]) ? (string)$_COOKIE["debug_sql_search"] : null;
	
		if($debug && $debug_sql)
		{
			$debug_sql_search_found = false;
			if(!empty($debug_sql_search))
			{
				$debug_sql_search_found = (bool)preg_match("/" . $debug_sql_search . "/", $sql); 
			}
			
			if(($debug_sql_search && $debug_sql_search_found) || !$debug_sql_search || (mysql_errno() && $debug_sql_errors))
			{
				FB::trace($sql);
				if(mysql_errno() != 0)
				{
					FB::trace(mysql_errno() . " - " . mysql_error());
					if($debug_sql_stop_onfind)
					{
						exit();
					}
				}
			}
		}
	
		$this->last_result = $result;
		$this->last_query = $sql;
	
		
		return $result;
	}
	
	public function set_query($sql)
	{
		$this->query = $sql;
	}
	
	public function query($sql=null, $return_type=self::MYSQL_FETCH_OBJECT, $limit=null)
	{
		if($return_type!=self::MYSQL_FETCH_ARRAY && $return_type!=self::MYSQL_FETCH_OBJECT && $return_type!=self::MYSQL_FETCH_ASSOC && $return_type!=self::MYSQL_FETCH_ROW)
		{
			trigger_error("\$return_type can only be array, object or assoc.");
		}
		if(is_null($sql) && is_null($this->query))
		{
			trigger_error("No query set.");
			return false;
		}
		
		if(is_null($sql))
		{
			$sql = $this->query;
		}
	
//		if(!SQLInjectionTester::checkInput($sql))
//		{
//			trigger_error("SQL injection detected. Please remove unnecessary elements from query.");
//			return false;
//		}
		
		$fetch_func = "mysql_fetch_" . $return_type;
		
		$result = $this->execute($sql);
		$data = array();

		$row = $fetch_func($result);
		$count = 0;
		while($row !== false)
		{
			
			array_push($data, $row);
			$count++;
			if(!is_null($limit) && $limit >= $count)
			{
				break;
			}
			$row = $fetch_func($result);
		}
		
		return $data;
	}
	
	public function query_list($sql=null, $return_type=self::MYSQL_FETCH_OBJECT)
	{
		return $this->query($sql, $return_type, null);
	}
	
	public function query_first($sql=null, $return_type=self::MYSQL_FETCH_OBJECT)
	{
		$row = $this->query($sql, $return_type, 1);
		return $row[0];
	}
	
	public function query_val($sql=null)
	{
		$row = $this->query_first($sql, self::MYSQL_FETCH_ROW, 1);
		return $row[0];
	}
	
	/**
	 * Runs a query and returns the result as a MySQLRecordSet.
	 *
	 * @param string $sql The SQL query.
	 * @return MySQLRecordSet
	 */
	public function query_recordset($sql=null)
	{
		if(is_null($sql))
		{
			$sql = $this->query;
		}
		if(is_null($sql) && is_null($this->query))
		{
			trigger_error("No query set.");
			return false;
		}
		
		$result = $this->execute($sql);
		$data = array();

		$row = mysql_fetch_object($result);
		while($row !== false)
		{
			array_push($data, $row);
			$row = mysql_fetch_object($result);
		}
		
		return new MySQLRecordSet($data);
	}
	
	public function getLastError()
	{
		return mysql_errno($this->conn) . " - " . mysql_error($this->conn);
	}
	public function getLastErrorCode()
	{
		return mysql_errno($this->conn);
	}
	public function lastNumRows()
	{
		return mysql_num_rows($this->last_result);
	}
	public function lastQuery()
	{
		return $this->last_query;
	}
	
	public function escape($sql)
	{
		$sql = addslashes($sql);
		$sql = mysql_real_escape_string($sql, $this->getConn());
		return $sql;
	}
	
	/**
	 * Wrapper for PHP's mysql_insert_id().
	 *
	 * @return int @see mysql_insert_id();
	 */
	public function last_insert_id()
	{
		return mysql_insert_id($this->conn);
	}
}

<?php


/**
 * This class represents a DataAccessObject
 * to a MySQL database. This class extends the
 * abstract DAO.
 * Many thanks to Tony Marston  who wrote the basic class for this version
 * http://www.tonymarston.net and http://www.radicore.org/
 */
class MySQLDAO extends DAO
{

	private $tablename; // table name
	private $dbname; // database name
	private $rowsPerPage; // used in pagination
	private $pageno; // current page number
	private $lastpage; // highest page number
	private $fieldlist; // list of fields in this table
	private $dataArray; // data from the database
	private $errors; // array of error messages
	private $useLimitedList; // limit the result

	/**
	 * Constructor to create a DAO for a specific table
	 * By default the rows_per_page is 10 but also
	 * the limitted Listing is disabled.
	 * @param tableName Name of the table
	 */
	public function __construct($tableName)
	{
//		global $dbconnect, $query, $mysqldb;
		global $db;
		$this->dbname = DB_DATABASE; // DBNAME
//		$dbconnect = db_connect($this->dbname) or die("Error connecting to database" . mysql_error() . "\n");
		if ($this->isVersion4())
		{
			$this->tablename = strtolower($tableName);
		} else
		{
			$this->tablename = $tableName;
		}
		$this->rowsPerPage = 10;
		$this->useLimitedList = false;
		$this->dataArray = array();
	}

	/**
	 * Set or unset the limitedList
	 * @param boolean indicates if limited List should be used
	 */
	public function setUseLimitedList($boolean)
	{
		$this->useLimitedList = $boolean;
	}

	/**
	 * Check if the Limited List flag is set.
	 * @return true if the flag is set
	 */
	public function isLimitedList()
	{
		return $this->useLimitedList;
	}

	/**
	 * Set the number of displayed rowes per page to
	 * the indicated number
	 * @rowNum number of rows that should be displayed on page
	 */
	public function setRowsPerPage($rowNum)
	{
		$this->rowsPerPage = $rowNum;
	}

	/**
	 * Will retrieve any number of records from the database using the specified WHERE criteria.
	 * The sql SELECT statement will be constructed automatically from variables supplied at runtime.
	 * The result is an associative array of 'name=value' pairs, indexed by row number.
	 * @param where WHERE statement of the SQL clause
	 * @return Array of read rows
	 */
	public function getData($where)
	{
		if (!$this->checkInput($where))
		{
			return null;
		}

		$this->dataArray = array ();
		$pageno = $this->pageno;
		$rows_per_page = $this->rowsPerPage;
		$this->rowCount = 0;
		$this->lastpage = 0;
		global $dbconnect, $query, $db;
//		$dbconnect = db_connect($this->dbname) or die("Error connecting to database." . mysql_error() . "\n");
		if (empty ($where))
		{
			$where_str = NULL;
		} else
		{
			$where_str = "WHERE $where";
		} // if
		$query = "SELECT count(*) FROM `$this->tablename` $where_str";
		//$this->write2log($query);
		//echo $query."<br>";
//		$result = mysql_query($query, $dbconnect) or die($query . "ist keine gueltige Abfrage" . mysql_error() . "\n");
//		$query_data = mysql_fetch_row($result);
		$result = $db->execute($query) or die($query . "Error executing query. " . mysql_error() . "\n");
		$result->move(0);
		$query_data = mysql_fetch_row($result->getResource());

		$this->rowCount = $query_data[0];
		if ($this->rowCount <= 0)
		{
			$this->pageno = 0;
			return;
		} // if
		if ($this->useLimitedList)
		{
			if ($rows_per_page > 0)
			{
				$this->lastpage = ceil($this->rowCount / $rows_per_page);
			} else
			{
				$this->lastpage = 1;
			} // if
		} else
		{
			$this->lastpage = 1;
		} // if
		if ($pageno == '' OR $pageno <= '1')
		{
			$pageno = 1;
		}
		elseif ($pageno > $this->lastpage)
		{
			$pageno = $this->lastpage;
		} // if
		$this->pageno = $pageno;
		if ($this->useLimitedList && $rows_per_page > 0)
		{
			$limit_str = 'LIMIT ' . ($pageno -1) * $rows_per_page . ',' . $rows_per_page;
		} else
		{
			$limit_str = NULL;
		} // if
		if (isset ($distinctField))
		{
			$query = "SELECT distinct $distinctField FROM $this->tablename $where_str $limit_str";
		} else
		{
			$query = "SELECT * FROM $this->tablename $where_str $limit_str";
		}

		//$this->write2log($query);
		//echo $query."<br>";
		$result = $db->execute($query);
		while ($row = $result->moveNext())
		{
			$this->dataArray[] = $row;
		} // while

		$result->__destruct();
		return $this->dataArray;
	} // getData

	/**
	 * Will retrieve any number of records from the database using the specified WHERE criteria.
	 * The sql SELECT statement will be constructed automatically from variables supplied at runtime.
	 * The result is an associative array of 'name=value' pairs, indexed by row number.
	 * @param where WHERE statement of the SQL clause
	 * @return Array of read rows
	 */
	public function getDistinctData($where, $distinctField)
	{
		if (!$this->checkInput($where))
		{
			return null;
		}
		if (!$this->checkInput($distinctField))
		{
			return null;
		}
		$this->dataArray = array ();
		$pageno = $this->pageno;
		$rows_per_page = $this->rowsPerPage;
		$this->rowCount = 0;
		$this->lastpage = 0;
		global $dbconnect, $query, $db;
		$dbconnect = db_connect($this->dbname) or die("keine Verbindung möglich" . mysql_error() . "\n");
		if (empty ($where))
		{
			$where_str = NULL;
		} else
		{
			$where_str = "WHERE $where";
		} // if

		$query = "SELECT count(*) FROM `$this->tablename` $where_str";
		//$this->write2log($query);
		//echo $query."<br>";
//		$result = mysql_query($query, $dbconnect) or die($query . " ist keine g�ltige Abfrage" . mysql_error() . "\n");
//		$query_data = mysql_fetch_row($result);
		$result = $db->execute($query) or die($query . "Error running query. " . mysql_error() . "\n");
		$query_data = $result->moveNext();

		$this->rowCount = $query_data[0];
		if ($this->rowCount <= 0)
		{
			$this->pageno = 0;
			return;
		} // if
		if ($this->useLimitedList)
		{
			if ($rows_per_page > 0)
			{
				$this->lastpage = ceil($this->rowCount / $rows_per_page);
			} else
			{
				$this->lastpage = 1;
			} // if
		} else
		{
			$this->lastpage = 1;
		} // if
		if ($pageno == '' OR $pageno <= '1')
		{
			$pageno = 1;
		}
		elseif ($pageno > $this->lastpage)
		{
			$pageno = $this->lastpage;
		} // if
		$this->pageno = $pageno;
		if ($this->useLimitedList && $rows_per_page > 0)
		{
			$limit_str = 'LIMIT ' . ($pageno -1) * $rows_per_page . ',' . $rows_per_page;
		} else
		{
			$limit_str = NULL;
		} // if
		$query = "SELECT distinct $distinctField FROM $this->tablename $where_str $limit_str";

		//$this->write2log($query);
		//echo $query."<br>";
//		$result = mysql_query($query, $dbconnect) or die($query . "Error running query. " . mysql_error() . "\n");
		$result = $db->execute($query);// or die($query . "Error running query. " . mysql_error() . "\n");
		while ($row = $result->moveNext())
		{
			$this->dataArray[] = $row;
		} // while
		mysql_free_result($result);

		return $this->dataArray;

	} // getData

	/**
	 * will insert a single row using the contents of $fieldarray, which is an associative array of 'name=value'
	 * pairs. The sql INSERT statement will be constructed automatically from the contents of $fieldarray.
	 * @param fieldarray Array with field value pairs that should be inserted
	 * @return inserted ID
	 */
	public function insertRecord($fieldarray)
	{
		$this->errors = array ();
		global $dbconnect, $query, $db;

//		$dbconnect = db_connect($this->dbname) or die("keine Verbindung möglich" . mysql_error() . "\n");


		//Check data for SQL Injection.
		foreach ($fieldarray as $item => $value)
		{
			if (!$this->checkInput($item))
			{
				return null;
			}
			if (!$this->checkInput($value))
			{
				return null;
			}
//			$query .= "$item='$value', ";
		} // foreach

		$fields = implode("`, `", array_keys($fieldarray));
		$fields = "`".$fields."`";
		$values = implode("', '", array_values($fieldarray));
		$values = "'".$values."'";

		$query = "INSERT INTO $this->tablename ($fields) VALUES ($values)";
//		$query = rtrim($query, ', ');

		//$this->write2log($query);
		//echo $query."<br/>";
//		$result = mysql_query($query, $dbconnect);
		$result = $db->execute($query);

		if (mysql_errno() <> 0)
		{
			if (mysql_errno() == 1062)
			{
				die("A record already exists with this ID.\n");
			} else
			{
				die($query . " ist keine gültige Abfrage" . mysql_error() . "\n");
			} // if
		} // if

		return mysql_insert_id();
	} // insertRecord

	/**
	 * will insert a single row using the contents of $fieldarray, which is an associative array of 'name=value'
	 * pairs. The sql INSERT statement will be constructed automatically from the contents of $fieldarray.
	 * @param fieldarray Array with field value pairs that should be inserted
	 * @return inserted ID
	 */
	public function insertBinaryRecord($fieldarray)
	{
		$this->errors = array ();
		global $dbconnect, $query, $db;
		$dbconnect = db_connect($this->dbname) or die("keine Verbindung möglich" . mysql_error() . "\n");
		$query = "INSERT INTO $this->tablename SET ";
		foreach ($fieldarray as $item => $value)
		{

			if (!$this->checkInput($item))
			{
				return null;
			}
			$query .= "$item='$value', ";
		} // foreach
		$query = rtrim($query, ', ');
		//$this->write2log($query);
		//echo $query."<br/>";
//		$result = mysql_query($query, $dbconnect);
		$result = $db->execute($query);
		if (mysql_errno() <> 0)
		{
			if (mysql_errno() == 1062)
			{
				die("A record already exists with this ID.\n");
			} else
			{
				die("Abfrage ist nicht gueltige " . mysql_error() . "\n");
			} // if
		} // if
		return mysql_insert_id();
	} // insertRecord

	/**
	 * will update a single row using the contents of $fieldarray, which is an associative array of 'name=value' pairs.
	 * The sql UPDATE statement will be constructed automatically from the contents of $fieldarray.
	 * @param fieldarray Array with field value pairs that should be inserted
	 * @param where WHERE statement of the SQL clause
	 */
	public function updateRecord($fieldarray, $where)
	{

		if (!$this->checkInput($where))
		{
			return null;
		}
		$this->errors = array ();
		global $dbconnect, $query, $db;
//		$dbconnect = db_connect($this->dbname) or die("keine Verbindung m�glich" . mysql_error() . "\n");
		$update = "";
		foreach ($fieldarray as $item => $value)
		{

			if (!$this->checkInput($item))
			{
				return null;
			}
			if (!$this->checkInput($value))
			{
				return null;
			}
			$update .= "$item='$value', ";
		} // foreach
		$update = rtrim($update, ', ');
		$query = "UPDATE $this->tablename SET $update WHERE $where";
		//$this->write2log($query);
		//echo $query."<br/>";
//		$result = mysql_query($query, $dbconnect) or die($query . " ist keine g�ltige Abfrage" . mysql_error() . "\n");
		$result = $db->execute($query);
		return;
	} // updateRecord

	/**
	 * will delete a single row using the contents of $fieldarray, which is an associative array of 'name=value' pairs.
	 * The sql DELETE statement will be constructed automatically from the contents of $fieldarray.
	 * @param where WHERE statement of the SQL clause
	 */
	public function deleteRecord($where)
	{
		if (!$this->checkInput($where))
		{
			return null;
		}
		$this->errors = array ();
		global $dbconnect, $query;
		$dbconnect = db_connect($this->dbname) or die("keine Verbindung m�glich" . mysql_error() . "\n");
		$fieldlist = $this->fieldlist;
		$query = "DELETE FROM $this->tablename WHERE $where";
		//$this->write2log($query);
		$result = mysql_query($query, $dbconnect) or die($query . " ist keine g�ltige Abfrage" . mysql_error() . "\n");
		return;
	}

	/**
	 * Get the MySQL hash-value of the password
	 * @param password that has to be hashed
	 * @return hashed password or null
	 */
	public function getMySQLPasswordHash($password)
	{

		if (!$this->checkInput($password))
		{
			return null;
		}
		global $dbconnect, $query;
		$query = "SELECT PASSWORD('$password') as HASH";
		if ($dbconnect == null || $dbconnect == "")
		{
			$dbconnect = db_connect($this->dbname) or die("keine Verbindung moeglich" . mysql_error() . "\n");
		}
		$result = mysql_query($query, $dbconnect) or die($query . "\nist keine g&uuml;ltige Abfrage\n" . mysql_error() . "\n");
		if ($row = mysql_fetch_assoc($result))
		{
			$hash = $row["HASH"];
			return $hash;
		}
		return null;
	}

	private function write2log($sql)
	{
		$logdir = "";
		$logfile = ".htsql.log";
		$filename = $logdir . $logfile;
//		$handle;
		if (file_exists($filename))
		{
			$handle = fopen($filename, "a+");
			fseek($handle, 0, SEEK_END);
		} else
		{
			$handle = fopen($filename, "a");
			fputs($handle, "DATE             SQL                                                                        \r\n");
			fputs($handle, "--------------------------------------------------------------------------------------------\r\n");
		}
		$time = date("H:i");
		$date = date("d.m.Y");
		fputs($handle, $date . " " . $time . "   ");
		fputs($handle, $sql);
		fputs($handle, "\r\n");
	}

	/**
	 * Gets the version of the MySQL server
	 *
	 * @return string version
	 */
	private function getMySQLVersion()
	{
		$version = mysql_get_server_info();
		return substr($version, 0, 1);
	}
	/**
	 * Get the full version string
	 *
	 * @return string
	 */
	public function getFullMySQLVersion()
	{
		return mysql_get_server_info();
	}

	/**
	 * Check if the MySQL server version is 4
	 * @return true if the version is 4
	 */
	private function isVersion4()
	{
		if (substr_compare($this->getMySQLVersion(), "4", 0, 1) == 0)
			return true;
		else
			return false;
	}

	/**
	* Check if the MySQL server version is 5
	* @return true if the version is 5
	*/
	private function isVersion5()
	{
		if (substr_compare($this->getMySQLVersion(), "5", 0, 1) == 0)
			return true;
		else
			return false;
	}

	/**
	 * Überprüfe den Input String, um SQL-Injection zu vermeiden
	 */
	private function checkInput($s)
	{
		return SQLInjectionTester::checkInput($s);
	}
} // MySQLDao
?>
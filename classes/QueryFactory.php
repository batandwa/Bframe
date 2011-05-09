<?php


/**
 * MySQL query_factory Class.
 * Class used for database abstraction to MySQL
 *
 * @package classes
 * @copyright Copyright 2003-2007 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: QueryFactory.php,v 1.1 2009/09/11 18:36:12 Administrator Exp $
 */
/**
 * Queryfactory - A simple database abstraction layer
 *
 */
class QueryFactory
{
	private $dbConnected;
	private $totalQueryTime;
	private $countQueries;
	private $link;
//	private $resource;

	public function __construct()
	{
		$this->countQueries = 0;
		$this->dbConnected = false;
		$this->totalQueryTime = 0;
	}

//	public function getResource()
//	{
//		return $this->resource;
//	}
	public function getLink()
	{
		return $this->link;
	}

	public function connect($zf_host, $zf_user, $zf_password, $zf_database, $zf_pconnect = false, $zp_real = false)
	{
		//@TODO error class required to virtualise & centralise all error reporting/logging/debugging
		$this->database = $zf_database;
		if (!function_exists('mysql_connect'))
			die('Call to undefined function: mysql_connect().  Please install the MySQL Connector for PHP');
		if ($zf_pconnect != false)
		{
			$this->link = mysql_connect($zf_host, $zf_user, $zf_password, true);
		} else
		{
			// pconnect disabled ... leaving it as "connect" here instead of "pconnect"
			$this->link = mysql_connect($zf_host, $zf_user, $zf_password, true);
		}
		if ($this->link)
		{
			if (mysql_select_db($zf_database, $this->link))
			{
				$this->dbConnected = true;
				return true;
			} else
			{
				$this->setError(mysql_errno(), mysql_error(), $zp_real);
				return false;
			}
		} else
		{
			$this->setError(mysql_errno(), mysql_error(), $zp_real);
			return false;
		}
	}

	private function selectDb($zf_database)
	{
		mysql_select_db($zf_database, $this->link);
	}

	private function prepareInput($zp_string)
	{
		if (function_exists('mysql_real_escape_string'))
		{
			return mysql_real_escape_string($zp_string);
		}
		elseif (function_exists('mysql_escape_string'))
		{
			return mysql_escape_string($zp_string);
		} else
		{
			return addslashes($zp_string);
		}
	}

	private function close()
	{
		mysql_close($this->link);
	}

	private function setError($zp_err_num, $zp_err_text, $zp_fatal = true)
	{
		$this->error_number = $zp_err_num;
		$this->error_text = $zp_err_text;
		if ($zp_fatal && $zp_err_num != 1141)
		{ // error 1141 is okay ... should not die on 1141, but just continue on instead
			$this->showError();
			die();
		}
	}

	private function showError()
	{
		if ($this->error_number == 0 && $this->error_text == DB_ERROR_NOT_CONNECTED && !headers_sent() && file_exists('nddbc.html'))
			include ('nddbc.html'."");
		echo '<div class="systemError">';
		echo $this->error_number . ' ' . $this->error_text;
		echo '<br />in:<br />[' . (strstr($this->zf_sql, 'db_cache') ? 'db_cache table' : $this->zf_sql) . ']<br />';
		if (defined('IS_ADMIN_FLAG') && IS_ADMIN_FLAG == true)
			echo 'If you were entering information, press the BACK button in your browser and re-check the information you had entered to be sure you left no blank fields.<br />';
		echo '</div>';
	}

	public function execute($zf_sql, $zf_limit = false, $zf_cache = false, $zf_cachetime = 0)
	{
		// bof: collect database queries
		if (defined('STORE_DB_TRANSACTIONS') && STORE_DB_TRANSACTIONS == 'true')
		{
			var_dump("enchanted territory"); die(":: " . __FILE__." (".__LINE__.") 23 Aug 2009 08:31:32");
			global $PHP_SELF, $box_id, $current_page_base;
			if (strtoupper(substr($zf_sql, 0, 6)) == 'SELECT' /*&& strstr($zf_sql,'products_id')*/
				)
			{
				$f = fopen(DIR_FS_SQL_CACHE . '/query_selects_' . $current_page_base . '_' . time() . '.txt', 'a');
				if ($f)
				{
					fwrite($f, "\n\n" . 'I AM HERE ' . $current_page_base . /*zen_get_all_get_params() .*/
					"\n" . 'sidebox: ' . $box_id . "\n\n" . "Explain \n" . $zf_sql . ";\n\n");
					fclose($f);
				}
				unset ($f);
			}
		}
		// eof: collect products_id queries
		global $zc_cache;
		if ($zf_limit)
		{
			$zf_sql = $zf_sql . ' LIMIT ' . $zf_limit;
		}
		$this->zf_sql = $zf_sql;
		if ($zf_cache AND $zc_cache->sql_cache_exists($zf_sql) AND !$zc_cache->sql_cache_is_expired($zf_sql, $zf_cachetime))
		{
			var_dump("enchanted territory"); die(":: " . __FILE__." (".__LINE__.") 23 Aug 2009 08:31:32");
			$obj = new QueryFactoryResult;
			$obj->resetCursor();
			$obj->is_cached = true;
			$obj->sql_query = $zf_sql;
			$zp_result_array = $zc_cache->sql_cache_read($zf_sql);
			$obj->result = $zp_result_array;
			if (sizeof($zp_result_array) > 0)
			{
				$obj->EOF = false;
				while (list ($key, $value) = each($zp_result_array[0]))
				{
					$obj->fields[$key] = $value;
				}
				return $obj;
			} else
			{
				$obj->EOF = true;
			}
		}
		elseif ($zf_cache)
		{
			var_dump("enchanted territory"); die(":: " . __FILE__." (".__LINE__.") 23 Aug 2009 08:31:32");
			$zc_cache->sql_cache_expire_now($zf_sql);
			$time_start = explode(' ', microtime());
			$obj = new QueryFactoryResult;
			$obj->sql_query = $zf_sql;

			if (!$this->dbConnected)
				$this->setError('0', DB_ERROR_NOT_CONNECTED);
			$zp_db_resource = mysql_query($zf_sql, $this->link);
			if (!$zp_db_resource)
				$this->setError(mysql_errno(), mysql_error());
			$obj->resource = $zp_db_resource;
			$obj->resetCursor();
			$obj->is_cached = true;
			if ($obj->recordCount() > 0)
			{
				$obj->EOF = false;
				$zp_ii = 0;
				while (!$obj->isEOF())
				{
					$zp_result_array = mysql_fetch_array($zp_db_resource);
					if ($zp_result_array)
					{
						while (list ($key, $value) = each($zp_result_array))
						{
							if (!ereg('^[0-9]', $key))
							{
								$obj->result[$zp_ii][$key] = $value;
							}
						}
					} else
					{
						$obj->limit = $zp_ii;
						$obj->EOF = true;
					}
					$zp_ii++;
				}
				while (list ($key, $value) = each($obj->result[$obj->cursor]))
				{
					if (!ereg('^[0-9]', $key))
					{
						$obj->fields[$key] = $value;
					}
				}
				$obj->EOF = false;
			} else
			{
				$obj->EOF = true;
			}
			$zc_cache->sql_cache_store($zf_sql, $obj->result);
			$time_end = explode(' ', microtime());
			$query_time = $time_end[1] + $time_end[0] - $time_start[1] - $time_start[0];
			$this->totalQueryTime += $query_time;
			$this->countQueries++;
			return ($obj);
		} else
		{
			$time_start = explode(' ', microtime());
			$result = new QueryFactoryResult();

			if (!$this->dbConnected)
			{
				$this->setError('0', DB_ERROR_NOT_CONNECTED);
			}
			$zp_db_resource = mysql_query($zf_sql, $this->link);
			if ($zp_db_resource === false)
			{
				$this->setError(mysql_errno($this->link), mysql_error($this->link));
			}

			if(gettype($zp_db_resource) != "boolean")
			{
				$result->setResource($zp_db_resource);
			}
//			$result->resetCursor();
//			if ($result->recordCount() > 0)
//			{
////				$result->setEOF(false);
//				$zp_result_array = $result->moveNext();
//				if ($zp_result_array !== false)
//				{
//					while (list ($key, $value) = each($zp_result_array))
//					{
//						if (!ereg('^[0-9]', $key))
//						{
//							$fields = $result->getFields();
//							$fields[$key] = $value;
////							$result->fields[$key] = $value;
//						}
//					}
//
//					$result->setEOF(false);
//				} else
//				{
//					$result->setEOF(false);
//				}
//				$result->reset();
//
//			} else
//			{
//				$result->setEOF(false);
//			}

			$time_end = explode(' ', microtime());
			$query_time = $time_end[1] + $time_end[0] - $time_start[1] - $time_start[0];
			$this->totalQueryTime += $query_time;
			$this->countQueries++;

			return ($result);
		}
	}

	private function executeRandomMulti($zf_sql, $zf_limit = 0, $zf_cache = false, $zf_cachetime = 0)
	{
		$this->zf_sql = $zf_sql;
		$time_start = explode(' ', microtime());
		$obj = new QueryFactoryResult;
		$obj->result = array ();
		if (!$this->dbConnected)
			$this->setError('0', DB_ERROR_NOT_CONNECTED);
		$zp_db_resource = mysql_query($zf_sql, $this->link);
		if (!$zp_db_resource)
			$this->setError(mysql_errno(), mysql_error());
		$obj->resource = $zp_db_resource;
		$obj->resetCursor();
		$obj->limit = $zf_limit;
		if ($obj->recordCount() > 0 && $zf_limit > 0)
		{
			$obj->EOF = false;
			$zp_Start_row = 0;
			if ($zf_limit)
			{
				$zp_start_row = zen_rand(0, $obj->recordCount() - $zf_limit);
			}
			$obj->move($zp_start_row);
			$obj->limit = $zf_limit;
			$zp_ii = 0;
			while (!$obj->EOF)
			{
				$zp_result_array = mysql_fetch_array($zp_db_resource);
				if ($zp_ii == $zf_limit)
					$obj->EOF = true;
				if ($zp_result_array)
				{
					while (list ($key, $value) = each($zp_result_array))
					{
						$obj->result[$zp_ii][$key] = $value;
					}
				} else
				{
					$obj->limit = $zp_ii;
					$obj->EOF = true;
				}
				$zp_ii++;
			}
			$obj->result_random = array_rand($obj->result, sizeof($obj->result));
			if (is_array($obj->result_random))
			{
				$zp_ptr = $obj->result_random[$obj->cursor];
			} else
			{
				$zp_ptr = $obj->result_random;
			}
			while (list ($key, $value) = each($obj->result[$zp_ptr]))
			{
				if (!ereg('^[0-9]', $key))
				{
					$obj->fields[$key] = $value;
				}
			}
			$obj->EOF = false;
		} else
		{
			$obj->EOF = true;
		}

		$time_end = explode(' ', microtime());
		$query_time = $time_end[1] + $time_end[0] - $time_start[1] - $time_start[0];
		$this->totalQueryTime += $query_time;
		$this->countQueries++;
		return ($obj);
	}

	private function getInsertID()
	{
		return mysql_insert_id($this->link);
	}

	private function metaColumns($zp_table)
	{
		$res = mysql_query("select * from " . $zp_table . " limit 1", $this->link);
		$num_fields = mysql_num_fields($res);
		for ($i = 0; $i < $num_fields; $i++)
		{
			$obj[strtoupper(mysql_field_name($res, $i))] = new QueryFactoryMeta($i, $res);
		}
		return $obj;

	}

	private function getServerInfo()
	{
		if ($this->link)
		{
			return mysql_get_server_info($this->link);
		} else
		{
			return UNKNOWN;
		}
	}

	private function queryCount()
	{
		return $this->countQueries;
	}

	private function queryTime()
	{
		return $this->totalQueryTime;
	}
	private function perform($tableName, $tableData, $performType = 'INSERT', $performFilter = '', $debug = false)
	{
		switch (strtolower($performType))
		{
			case 'insert' :
				$insertString = "";
				$insertString = "INSERT INTO " . $tableName . " (";
				foreach ($tableData as $key => $value)
				{
					if ($debug === true)
					{
						echo $value['fieldName'] . '#';
					}
					$insertString .= $value['fieldName'] . ", ";
				}
				$insertString = substr($insertString, 0, strlen($insertString) - 2) . ') VALUES (';
				reset($tableData);
				foreach ($tableData as $key => $value)
				{
					$bindVarValue = $this->getBindVarValue($value['value'], $value['type']);
					$insertString .= $bindVarValue . ", ";
				}
				$insertString = substr($insertString, 0, strlen($insertString) - 2) . ')';
				if ($debug === true)
				{
					echo $insertString;
					die();
				} else
				{
					$this->execute($insertString);
				}
				break;
			case 'update' :
				$updateString = "";
				$updateString = 'UPDATE ' . $tableName . ' SET ';
				foreach ($tableData as $key => $value)
				{
					$bindVarValue = $this->getBindVarValue($value['value'], $value['type']);
					$updateString .= $value['fieldName'] . '=' . $bindVarValue . ', ';
				}
				$updateString = substr($updateString, 0, strlen($updateString) - 2);
				if ($performFilter != '')
				{
					$updateString .= ' WHERE ' . $performFilter;
				}
				if ($debug === true)
				{
					echo $updateString;
					die();
				} else
				{
					$this->execute($updateString);
				}
				break;
		}
	}
	private function getBindVarValue($value, $type)
	{
		$typeArray = explode(':', $type);
		$type = $typeArray[0];
		switch ($type)
		{
			case 'csv' :
				return $value;
				break;
			case 'passthru' :
				return $value;
				break;
			case 'float' :
				return (!zen_not_null($value) || $value == '' || $value == 0) ? 0 : $value;
				break;
			case 'integer' :
				return (int) $value;
				break;
			case 'string' :
				if (isset ($typeArray[1]))
				{
					$regexp = $typeArray[1];
				}
				return '\'' . $this->prepare_input($value) . '\'';
				break;
			case 'noquotestring' :
				return $this->prepare_input($value);
				break;
			case 'currency' :
				return '\'' . $this->prepare_input($value) . '\'';
				break;
			case 'date' :
				return '\'' . $this->prepare_input($value) . '\'';
				break;
			case 'enum' :
				if (isset ($typeArray[1]))
				{
					$enumArray = explode('|', $typeArray[1]);
				}
				return '\'' . $this->prepare_input($value) . '\'';
			case 'regexp' :
				$searchArray = array (
					'[',
					']',
					'(',
					')',
					'{',
					'}',
					'|',
					'*',
					'?',
					'.',
					'$',
					'^'
				);
				foreach ($searchArray as $searchTerm)
				{
					$value = str_replace($searchTerm, '\\' . $searchTerm, $value);
				}
				return $this->prepare_input($value);
			default :
				die('var-type undefined: ' . $type . '(' . $value . ')');
		}
	}
	/**
	 * method to do bind variables to a query
	**/
	private function bindVars($sql, $bindVarString, $bindVarValue, $bindVarType, $debug = false)
	{
		$bindVarTypeArray = explode(':', $bindVarType);
		$sqlNew = $this->getBindVarValue($bindVarValue, $bindVarType);
		$sqlNew = str_replace($bindVarString, $sqlNew, $sql);
		return $sqlNew;
	}

	private function prepareInput_($string)
	{
		return mysql_real_escape_string($string);
	}
}

class QueryFactoryResult
{
	private $resource;
	private $limit;
//	private $cursor;
//	private $result;
//	private $result_random;
	private $EOF;
	private $fields;

	public function __construct()
	{
		$this->is_cached = false;
		$this->EOF = false;
//		$this->cursor = 0;
	}

	public function getResource()
	{
		return $this->resource;
	}
	public function setResource($res)
	{
		$this->resource = $res;
		$this->reset();
	}
//	public function getLimit()
//	{
//		return $this->limit;
//	}
//	public function setLimit($limit)
//	{
//		$this->limit= $limit;
//	}
	public function getFields()
	{
		return $this->fields;
	}
//	public function setFields($flds)
//	{
//		$this->fields= $flds;
//	}
	public function isEOF()
	{
		return $this->EOF;
	}
//	public function setEOF($eof)
//	{
//		$this->EOF = $eof;
//	}

//	public function moveNext

	public function moveNext()
	{
		global $zc_cache;
//		$this->cursor++;

		if ($this->is_cached)
		{
			if ($this->cursor >= sizeof($this->result))
			{
				$this->EOF = true;
			} else
			{
				while (list ($key, $value) = each($this->result[$this->cursor]))
				{
					$this->fields[$key] = $value;
				}
				return $this->fields;
			}
		} else
		{
//			$zp_result_array = mysql_fetch_array($this->resource);
//			if (!$zp_result_array)
//			{
//				$this->EOF = true;
//			} else
//			{
//				while (list ($key, $value) = each($zp_result_array))
//				{
//					if (!ereg('^[0-9]', $key))
//					{
//						$this->fields[$key] = $value;
//					}
//				}
//
//				return $this->fields;
//			}
			return mysql_fetch_assoc($this->resource);
		}

		return false;
	}

	public function moveNextRandom()
	{
//		$this->cursor++;
		if ($this->cursor < $this->limit)
		{
			$zp_result_array = $this->result[$this->result_random[$this->cursor]];
			while (list ($key, $value) = each($zp_result_array))
			{
				if (!ereg('^[0-9]', $key))
				{
					$this->fields[$key] = $value;
				}
			}
		} else
		{
			$this->EOF = true;
		}
	}

	public function recordCount()
	{
		return mysql_num_rows($this->resource);
	}

	public function move($zp_row)
	{
		global $db;
		if (mysql_data_seek($this->resource, $zp_row))
		{
			$zp_result_array = mysql_fetch_array($this->resource);
			while (list ($key, $value) = each($zp_result_array))
			{
				$this->fields[$key] = $value;
			}

			mysql_data_seek($this->resource, $zp_row);
			$this->EOF = false;
			return;
		} else
		{
			$this->EOF = true;
			$db->setError(mysql_errno(), mysql_error());
		}
	}

//	public function resetCursor()
//	{
//		$this->cursor = 0;
//	}

	public function reset()
	{
		if(mysql_num_rows($this->resource)>0)
		{
			if(!is_null($this->resource))
			{
				mysql_data_seek($this->resource, 0);
			}
			$this->EOF = false;
		}
		else
		{
			$this->EOF = true;
		}
	}

	public function __destruct()
	{
//		mysql_free_result($this->resource);
	}
}

class QueryFactoryMeta
{

	public function QueryFactoryMeta($zp_field, $zp_res)
	{
		$this->type = mysql_field_type($zp_res, $zp_field);
		$this->max_length = mysql_field_len($zp_res, $zp_field);
	}
}
?>
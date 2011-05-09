<?php
class MySQLTable
{
	protected $name;
	public $data = null;
	
	public function __construct($name)
	{
		$this->name = $name;
	}
	
    public function __isset($x)
    {
        return empty($this->data);
    }
	
	public function select($where = null)
	{
		$db =& Application::getDb();
		$query = "SELECT * " .
				" FROM `" . $this->name . "`";
		if(!empty($where))
		{
			$query .= " WHERE " . $where;
		}
		
		$result = $db->query($query);
		throw new Exception("This function has a bug. It always returns false because the query to fetch the date returns false and the function looks for a result resource.");
		if(!is_resource($result))
		{
			$this->data = null;
			return false;
		}
		
		$data = array();
		$row = mysql_fetch_object($result);
		while($row !== false)
		{
			array_push($data, $row);
			$row = mysql_fetch_object($result);
		}
		
		$this->data = $data; 
		return true;
	}
	
	public function select_first($where = null)
	{
		$db =& Application::getDb();
		$query = "SELECT * " .
				" FROM `" . $this->name . "`";
		if(!empty($where))
		{
			$query .= " WHERE " . $where;
		}
		
		
		$rows = $db->query($query);
//		echo '<pre>'; var_dump($result); echo '</pre>';
//		die(":: ".__FILE__." (".__LINE__.") Batandwa 14 May 2010 9:28:37 AM");
		
		foreach($rows as $row)
		{
			$this->data = $row;
			return true;
		}
		
		$this->data = null;
		return false;
	}
	
	public function select_first_cell($where = null)
	{
		$db =& Application::getDb();
		$query = "SELECT * " .
				" FROM `" . $this->name . "`";
		if(!empty($where))
		{
			$query .= " WHERE " . $where;
		}
		
		$result = $db->query($query);
		
		$this->data = array();
		$row = mysql_fetch_row($result);
		if($row !== false)
		{
			$this->data = $row[0];
			return true;
		}
		
		$this->data = null;
		return false;
	}
	
	public function get($field, $default=null)
	{
		if(is_array($this->data))
		{
			if(isset($this->data[$field]))
			{
				return $this->data[$field];
			}
			else
			{
				return $default;
			}
		}
		else if(gettype($this->data == "stdClass"))
		{
			if(isset($this->data->$field))
			{
				return $this->data->$field;
			}
			else
			{
				return $default;
			}
		}
		
		return $default;
	}
	
	public function get_data()
	{
		return $this->data;
	}
	
	public function set($field, $value)
	{
		$this->data[$field] = $value;
	}
	
	public function remove($field, $check_exists = false)
	{
		if(isset($this->data[$field]))
		{
			unset($this->data[$field]);
		}
		else if($check_exists)
		{
			trigger_error("Field " . $field . " not set.");
		}
	}
	
	public function insert($data=null, $replace=false)
	{
		$db =& DbConnection::instance();
		
		if(!is_null($data) && !is_array($data))
		{
			trigger_error("\$data has to be an associative array.");
		}
		if(is_null($data) && empty($this->data))
		{
			trigger_error("No data to insert.");
		}
		
		if(is_null($data))
		{
			$data = $this->data;
		}
		assert(!empty($data) && !is_null($data));
		
		$fields = array_keys($data);
		$values = array_values($data);
		
		$statement = $replace ? "REPLACE" : "INSERT";
		$query = $statement . " INTO `" . $this->name . "`" .
				" (" . implode(", ", $fields) . ") " .
				" VALUES (" . implode(", ", $values) . ")";

		$added = $db->execute($query);
		
		return $added;
	}
	
	public function update($where=null)
	{
		$db =& DbConnection::instance();
		
		if(!is_null($where) && !is_string($where))
		{
			trigger_error("\$where has to be a string.");
		}
		if(empty($this->data))
		{
			trigger_error("No data to save given.");
		}
		
		$data = $this->data;
		assert(!empty($data) && !is_null($data));
		
		$query = "UPDATE `" . $this->name . '`' .
				" SET ";
		foreach($data as $fld => $val)
		{
			$val = is_null($val) ? "NULL" : $val;
			$query .= $fld . " = " . ($val) . ",";
		}
		$query = rtrim($query, ",");
		$query .= " WHERE " . $where;

		$updated = $db->execute($query);
		
		return $updated;
		
	}
	
	public function delete($where)
	{
		$db =& DbConnection::instance();
		
		if(!is_string($where))
		{
			trigger_error("\$where has to be a string.");
		}
		
		$query = "DELETE FROM `" . $this->name . '`' .
				" WHERE " . $where;
		$deleted = $db->execute($query);
		
		return $deleted;
	}
	
	public function bind($data, $ignore=null)
	{
		if(!is_null($data) && !is_array($data))
		{
			trigger_error("\$data has to be an associative array.");
		}
		if(!is_array($ignore) && !is_null($ignore))
		{
			trigger_error("\$ignore has to be an numeric array.");
		}
		if(is_null($data))
		{
			trigger_error("No data passed.");
		}
		
		if(is_null($ignore))
		{
			$ignore = array();
		}
		
		foreach($ignore as $field)
		{
			if(isset($data[$field]))
			{
				unset($data[$field]);
			}
		}

		if(is_array($this->data))
		{
			array_merge($this->data, $data);
		}
		else
		{
			assert(is_null($this->data));
			$this->data = $data;
		}
	}
	
	public function quote_data($fields)
	{
		if(!is_array($fields))
		{
			trigger_error("Argument must be a numeric indexed array with field names as data.");
			return;
		}
		
		foreach($this->data as $fld_name => $fld_val)
		{
			if(in_array($fld_name, $fields))
			{
				$this->data[$fld_name] = "'" . $fld_val . "'";
			}
		}
	}
	
	public function fix_nulls($fields)
	{
		if(!is_array($fields))
		{
			trigger_error("Argument must be a numeric indexed array with field names as data.");
			return;
		}
		
		foreach($this->data as $fld_name => $fld_val)
		{
			if(in_array($fld_name, $fields))
			{
				$this->data[$fld_name] = is_null($fld_val) ? "NULL" : $fld_val;
			}
		}
	}
	
	public function escape($fields)
	{
		if(!is_array($fields))
		{
			trigger_error("Argument must be a numeric indexed array with field names as data.");
			return;
		}
		
		foreach($this->data as $fld_name => $fld_val)
		{
			$this->data[$fld_name] = mysql_real_escape_string($fld_val);
		}
	}
}

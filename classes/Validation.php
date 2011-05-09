<?php
class Validation extends Base
{
	const VALID_TYPE_REQUIRED = 1;
//	const VALID_TYPE_MATCH = 2;
	const VALID_TYPE_DATE= 2;
	const VALID_TYPE_NUMERIC = 4;
	const VALID_TYPE_REGEX = 8;
	const VALID_TYPE_BOOL = 16;
	const VALID_TYPE_DOMAIN = 32;
	const VALID_TYPE_EMAIL = 64;
	const VALID_TYPE_ALPHA = 128;
	const VALID_TYPE_RANGE = 256;
	const VALID_TYPE_MATCH = 512;
	const VALID_TYPE_ORDERING = 1024;
	
	const VALID_ORDERING_DATE = 1;
	const VALID_ORDERING_NUMERIC = 2;
	const VALID_ORDERING_ALPHA = 4;
	
	const VALID_ORDER_ASC = 1;
	const VALID_ORDER_DESC = 2;
	
	private $mappings;
	private $errors;
	private $valid_types;
	
	public function __construct()
	{
		$this->clear();
//		$this->mappings = array();
//		$this->errors = array();	
		
		$this->valid_types = array();
		$this->valid_types[self::VALID_TYPE_REQUIRED] = "VALID_TYPE_REQUIRED";
		$this->valid_types[self::VALID_TYPE_DATE] = "VALID_TYPE_DATE";
		$this->valid_types[self::VALID_TYPE_NUMERIC] = "VALID_TYPE_NUMERIC";
		$this->valid_types[self::VALID_TYPE_REGEX] = "VALID_TYPE_REGEX";
		$this->valid_types[self::VALID_TYPE_BOOL] = "VALID_TYPE_BOOL";
		$this->valid_types[self::VALID_TYPE_DOMAIN] = "VALID_TYPE_DOMAIN";
		$this->valid_types[self::VALID_TYPE_EMAIL] = "VALID_TYPE_EMAIL";
		$this->valid_types[self::VALID_TYPE_ALPHA] = "VALID_TYPE_ALPHA";
		$this->valid_types[self::VALID_TYPE_RANGE] = "VALID_TYPE_RANGE";
		$this->valid_types[self::VALID_TYPE_MATCH] = "VALID_TYPE_MATCH";
		$this->valid_types[self::VALID_TYPE_ORDERING] = "VALID_TYPE_ORDERING";
	}
	
	public function getErrors()
	{
		return $this->errors;
	}
	
	public function attach($values, $type, $error=null, $id=null)
	{
		if($type == self::VALID_TYPE_REGEX && (!is_array($values) || count($values) != 2))
		{
			throw new Exception("Regex validation requires an array with 2 elements passed as \$values");
		}
		if(!is_null($id))
		{
			$this->mappings[$id] = array("values" => $values, "type" => $type, "error" => $error);
		}
		else
		{
			array_push($this->mappings, array("values" => $values, "type" => $type, "error" => $error));
		}
	}
	
	public function validate()
	{
		$valid = true;
		$this->errors = array();
		
		foreach($this->mappings as $mapId => $map)
		{
			if(is_array($map["values"]) && count($map["values"])==1)
			{
				$map["values"] = $map["values"][0]; 
			}
			
			$map["id"] = $mapId;

			$valid_type = $map["type"];
			$valid_type = $this->valid_types[$valid_type];
			$valid_type = strtolower(str_replace("VALID_TYPE_", "", $valid_type));
			
			$map_valid = !$this->$valid_type($map);
			$valid = $valid && $map_valid || !$valid ? false : true; 
		}
		
		return $valid;
	}
	
	private function required($map)
	{
		$valid = !empty($map["values"]);
		
		if(!$valid)
		{
			$this->errors[$map["id"]] = $map["error"];
		}
		
		return $valid;
	}
	
	private function regex($map)
	{
		$valid = (bool)preg_match($map["values"][1], $map["values"][0]);
		
		if(!$valid)
		{
			$this->errors[$map["id"]] = $map["error"];
		}
		return $valid;
	}
	
	private function bool($map)
	{
		return (bool)$map;
	}
	
	private function domain($map)
	{
		if(!is_string($map["values"]))
		{
			trigger_error("Value to get domain validation must be a single string.");
			return false;
		}
		
		$domain_regex = "/.*/i";
		$map["values"] = array($map["values"], $domain_regex);
		
		$result = $this->regex($map);

		return (bool)$result;
	}
	
	private function numeric($map)
	{
		//If there are many values submitted for testing.
		if(is_array($map["values"]))
		{
			$valid = true;
			$filter_vals = array_filter($map["values"], "is_numeric");
			$valid = (count($filter_vals) == count($map["values"]));
		}
		else 
		{
			$valid = is_numeric($map["values"]);
		}
		
		if(!$valid)
		{
			$this->errors[$map["id"]] = $map["error"];
		}
		return $valid;
	}
	
	private function alpha($map)
	{
		$valid = is_alpha($map["values"]);
		if(!$valid)
		{
			$this->errors[$map["id"]] = $map["error"];
		}
		return $valid;
	}
	
	private function email($map)
	{
		if(!is_string($map["values"]))
		{
			trigger_error("Value to get domain validation must be a single string.");
			return false;
		}
		
		$domain_regex = "/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i";
		$map["values"] = array($map["values"], $domain_regex);
		
		$result = $this->regex($map);

		return (bool)$result;
	}
	
	public function date($map)
	{
		$valid = is_date($map["values"]);
		
		if(!$valid)
		{
			$this->errors[$map["id"]] = $map["error"];
		}
		
		return $valid;
	}
	
	public function range($map)
	{
		$valid = $this->numeric($map);
		if($valid)
		{
			$min = $map["values"]["min"];
			$max = $map["values"]["max"];
			if(!is_array($map["values"]["values"]))
			{
				$values = array($map["values"]["values"]);
			}
			else 
			{
				$values = $map["values"]["values"];
			}
			$filtered_map = array_filter_range($values, $min, $max);
			$valid = (count($filtered_map) == count($values));
		}
		
		if(!$valid)
		{
			$this->errors[$map["id"]] = $map["error"];
		}
		
		return $valid;
	}
	
	public function ordering($map)
	{
		$values = $map["values"]["values"];
		$data_type = $map["values"]["data_type"];
		$direction = $map["values"]["direction"];
		$valid = true;
		
		switch ($data_type)
		{
			case (Validation::VALID_ORDERING_DATE):
			{
				$prev_date = null;
				foreach($values as $date)
				{
//					$date = strtotime($date);
					if(!is_date($date))
					{
						$valid = false;
//						fb($date . " not date");
						break;
					}
					
					if(is_null($prev_date))
					{
						$prev_date = $date;
//						fb($date . " first date");
						continue;
					}
					
					if($direction==Validation::VALID_ORDER_ASC)
					{
						if(strtotime($prev_date) > strtotime($date))
						{
							$valid = false;
//							fb($date . " not asc");
							break;
						}
					}
					else if($direction == Validation::VALID_ORDER_DESC)
					{
						if(strtotime($prev_date) < strtotime($date))
						{
							$valid = false;
//							fb($date . " not desc");
							break;
						}
					}
					else
					{
						$valid = false;
//						fb($date . " not unknown direct");
						break;
					}
					
					$prev_date = $date;
				}
				break;
			}
			case (Validation::VALID_ORDERING_ALPHA):
			{
				throw new Exception("This order validation type has not yet been implemented.");
				break;
			}
			case (Validation::VALID_ORDERING_NUMERIC):
			{
				throw new Exception("This order validation type has not yet been implemented.");
				break;
			}
			default:
			{
				trigger_error("Incorrect ordering type specified.");
//				fb($date . " unkown dat type");
				$valid = false;
				break;
			}
		}
		
		if(!$valid)
		{
//			fb($date . " dont validat");
			$this->errors[$map["id"]] = $map["error"];
		}
//		fb("=================");
		return $valid;
	}
	
	public function clear()
	{
		$this->mappings = array();
		$this->errors = array();		
	}
}


/***********************
        TESTING
***********************/
//$val = new Validation();
//$domains = array("asdasd.com", "dasdfasd.co.za", 'daddd', "localhost", "localhost.net", "=saadca.com", "_asdfasd.com", ".asdfasd.com", "asdfasdf.co.");
//foreach($domains as $domain)
//{
//	$val->attach($domain, Validation::VALID_TYPE_EMAIL, "Error");
//	assert($val->validate());
//	$val->clear();
//}

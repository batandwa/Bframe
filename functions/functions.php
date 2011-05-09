<?php
if(!defined("IN_APP")) die("Access denied!");

function __autoload($className)
{
	$classFilePath = "classes/" . $className . ".php";
	if(is_file($classFilePath))
	{
		require_once $classFilePath;
	}
	else
	{
//		echo ("Error loading class: " . $className . ". Class file not found at expected location: " . $classFilePath . ".\n");
//		debug_print_backtrace();
//		die;
	}
}

/**
 * Connect to the database
 */
//function db_connect()
//{
//	global $mysqlhost;
//	global $mysqluser;
//	global $mysqlpassword;
//	global $mysqldb;
//
//	$dbconnect = mysql_connect($mysqlhost, $mysqluser, $mysqlpassword); // localhost
//	//$dbconnect = mysql_connect($mysqlhost, $mysqluser, $mysqlpassword);	// sahits
//	if (!$dbconnect)
//	{
//		return 0;
//	}
//	elseif (!mysql_select_db($mysqldb))
//	{ // DBNAME
//		return 0;
//	} else
//	{
//		return $dbconnect;
//	} // if
//}

function collect(& $v, $k, $method)
{
	$v = call_user_func(array (
		$v,
		$method
	));
	return $v;
}

/**
 * Shortens a string to a given length. The method does not cut in between
 * but looks for a punctuation mark before cutting.
 * Returns the string after it has been shortened or the whole string if the
 * length is longer or equal to the lenngth of the string.
 *
 * @access public
 * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
 * @param  int cutoffLength The point at which the text must be cut.
 * @param  string text The string to be cut.
 * @param  string $cutoffPoints Places at which the $text may be cut. If left blank the $text will be cut at $cutoffLength.
 * @return string
 */
function cutText($cutoffLength, $text, $cutoffPointChars, $maxCutoffDeviation = 10)
{
	$returnValue = (string) '';

	// section 127-0-0-1-237b952:120c51d7ed4:-8000:00000000000011AC begin
	//Punctuation marks where the string will cut off
	$term_punc_loc = strlen($text) - 1;
	$cutoffChars = is_null($cutoffPointChars) ? array () : $cutoffPointChars;
	$cutoffLength = $cutoffLength > strlen($text) ? strlen($text) - 1 : $cutoffLength;

	//Find out which terminating punctuation mark comes first.
	foreach ($cutoffChars as $punc)
	{
		$new_term_punc_loc = strpos($text, $punc, $cutoffLength);
		if ($new_term_punc_loc !== false && $new_term_punc_loc < $term_punc_loc)
		{
			$term_punc_loc = strpos($text, $punc, $cutoffLength);
		}
	}

	$term_punc_loc = abs($cutoffLength - $term_punc_loc) > 10 ? $cutoffLength : $term_punc_loc;
	$returnValue = $text;
	if ($term_punc_loc < (strlen($text) - 2))
	{
		$returnValue = substr($text, 0, $term_punc_loc) . "...";
	}

	// section 127-0-0-1-237b952:120c51d7ed4:-8000:00000000000011AC end

	return (string) $returnValue;
}

/**
 * Generates a URL string based on the current URL and its GET parameters.
 * Parameters specified on IGNORE_PARAMETERS are ignored. 
 *
 * @param array $ignore_indexes GET param names to be ignored.
 * @param array $params An associativea array of GET parameters to be used.
 * @param bool $url_encode Whether to encode the rusult URL.
 * @param bool $include_domain True, to include the domain name.
 * @param bool $include_proto True, to include the protocol if domain is included
 * @return string The result URL.
 */
function generate_url(array $ignore_indexes = array(), array $params = array(), $url_encode=false, $include_domain=false, $include_proto=false)
{
	if(!is_bool($url_encode))
	{
		trigger_error("\$url_encode has to be a boolean value"); 
	}
	
	$gets_list = array();
	$gets = "";
//	$encode_function = "urlencode";
	$encode_function = "htmlentities";
	
	//Add items from IGNORE_PARAMETERS on the ignore list.
	$conf_ignore_indexes = explode(",", IGNORE_PARAMETERS);
	if(defined("IGNORE_PARAMETERS"))
	{
		$ignore_indexes = array_merge($ignore_indexes, $conf_ignore_indexes);
	}

	foreach ($_GET as $index => $value)
	{
		if(!is_array($value))
		{
			//Add this element to the url if it is not in the ignore list.
			$addIndex = !in_array($index, $ignore_indexes);
			$addIndex = $addIndex && !in_array($index, Application::$removedActions);
			
//			$index = $url_encode ? $encode_function($index) : $index;
//			$value = $url_encode ? $encode_function($value) : $value;
//			$gets .= $addIndex ? "&" . $index . "=" . $value : "";
			if($addIndex)
			{
				$gets_list[$index] = $value;
			}
		}
	}
	
	foreach($params as $fld => $val)
	{
		$gets_list[$fld] = $val;
	}
	
	foreach($gets_list as $fld => $val)
	{
		$gets .= "&" . $fld . "=" . $val;
	}
	
	$gets = $url_encode ? $encode_function($gets) : $gets;
	$url = $_SERVER["PHP_SELF"] . "?" . trim($gets, "&");
	
	$url = $include_domain ? $_SERVER['HTTP_HOST'] . $url : $url; 
	$url = $include_domain && $include_proto ? "http://" . $url : $url;
//	$url = $url_encode ? $encode_function($url) : $url;
	
	return $url;
}

function array_insert(array &$array, $values, $offset=null)
{
	if(!isset($offset) && is_array($values))
	{
		$insert_count=0;
		foreach($values as $id => $val)
		{
			$beginning = array_slice($array, 0, $id+1+$insert_count);
			$end = array_diff($array, $beginning);
//			$array = array_merge($beginning, $val, $end);
			$val = array($val);
			$array = array_merge($beginning, $val, $end);
//			$array = $beginning + $val;

			$insert_count++;
		}

		return $array;
	}
	else if(isset($offset))
	{
		$beginning = array_slice($array, 0, $offset+1);
		$end = array_diff($array, $beginning);
		array_unshift($end, null);
		$values = array($offset => $values);
		$array = array_merge($beginning, $values, $end);
//		$array = $beginning + $values + $end;
//		$array = $return;
	}
	else
	{
		trigger_error("Offset not specified for array_insert.");
	}

	return $array;
//	array_push($array, implode("</td><td>", $values));
//	return $array;
}

/**
 * Checks if the values passed contain letters only otherwise returns false.
 *
 * @return bool True if all the arguments only contain the letters of the 
 *              alphabet, otherwise false.
 */
function is_alpha()
{
	$args = func_get_args();
	if(func_num_args() == 0)
	{
		trigger_error("No arguments passed.");
		return false;
	}
	
	foreach($args as $counter => $arg)
	{
		if(!is_string($arg))
		{
			trigger_error("Argument " . $counter . " not string.");
			return false;
		}

		$matches = null;
		if(preg_match("/^[A-Z\s_]+$/i", $arg, $matches) == 0)
		{
			return false;
		}
	}
	
	return true;
}

function get_request_headers($exclude = array(), $format = "array")
{
	if(!is_array($exclude) && !is_null($exclude))
	{
		trigger_error("\$exclude is expected to be an array.");
	}
	if(is_null($exclude))
	{
		$exclude = array();
	}
	if(strtolower($format) != "array" && strtolower($format) != "string")
	{
		trigger_error("\$format is expected to have the value 'array' or 'string'.");
		return false;
	}
	
	$headers = null;
	if($format == "array")
	{
    	$headers = array();
	}
	else if($format == "string") 
	{
		$headers = "";
	}
	else 
	{
		assert(false);
	}
    
    foreach($_SERVER as $k => $v)
    {
        if (!in_array($k, $exclude) && substr($k, 0, 5) == "HTTP_")
        {
            $k = str_replace('_', ' ', substr($k, 5));
            $k = str_replace(' ', '-', ucwords(strtolower($k)));
        
			if($format == "array")
			{
            	$headers[$k] = $v;
			}
			else if($format == "string") 
			{
            	$headers .= $k. " = " . $v . "\n";
			}
        }
    }
	
    if($format == "string") 
	{
		$headers = trim($headers);
	}
    
    return $headers;
}

function encrypt($val)
{
	return md5(sprintf(SALT_FORMAT, $val));
}

function generate_html_select($name, $data, $selected_value, $use_codes=false, $attribs=null)
{
	$xhtml = "";
	
	$str_attribs = !is_null($attribs) ? $attribs : "";
	$xhtml .=  "<select name=\"$name\" $str_attribs >\n";

   if (is_array($data))
   {
		   $xhtml .=  "<option value=\"\">-- Please select --</option>\n";
		   if ($use_codes) 
		   { 
		      foreach($data as $k => $v) 
			  {
			  	 if (is_array($v))
			  	 {
			  		foreach($v as $d => $val) 	 	
			  		{
				         $sel = ($data == $selected_value) ? ' selected="selected" ' : "";
				         $xhtml .=  "<option value=\"$d\" $sel>$val</option>\n";
			  		}
			  	 }
			  	 else {
			         if(strtoupper($k) == strtoupper($selected_value))
			         {
			         	$sel = " selected=\"selected\" ";
			         }
			         else 
			         {
			         	$sel = " ";
			         }
			         $xhtml .=  "<option value=\"$k\"$sel>$v</option>\n";
			  	 }
		      }
		   }
		   else 
		   {
		      foreach($data as $k => $v) 
			  {
		         if((strtoupper($k) == strtoupper($selected_value)))
		         {
		         	$sel = " selected=\"selected\" ";
		         }
		         else 
		         {
		         	$sel = " ";
		         }

		         $xhtml .=  "<option value=\"$k\" $sel >$v</option>\n";
		      }
		   }
	}
	else
	{
        $xhtml .=  "<option value=\"0\"$sel>None</options>\n";
	}
   $xhtml .=  "</select>";
   
   return $xhtml;
}

function array_filter_range(array $data, $min, $max)
{
	if(!is_numeric($min) || !is_numeric($max))
	{
		trigger_error("\$min and \$max must both be numeric arguments.");
		return null;
	}
	
	$filtered_dat = array();
	foreach ($data as $k => $v)
	{
		if((int)$v >= $min && (int)$v <= $max)
		{
			$filtered_dat[$k] = $v;
		}
	}
	
	return $filtered_dat;
}

function is_date($date)
{
	$date = strtotime($date);
	
	$valid = true;
	if($date === false || $date === -1)
	{
		$valid = false;
	}
	
	return $valid;
}

function array_pad_nearest($arr, $mult, $val=null) {
	$count = 0;
	if(is_array($arr) && is_int(count($arr))) {
		while($count < count($arr)) {
			$count += 5;
		}
	}
	
	return array_pad($arr, $count, $val);
}


function is_base64($code) {
	return base64_decode($code, true)===false ? false : true;
}

function strip_funcs($str, $funcs=array("eval", "base64_decode")) {
	$ret = $str;
	foreach($funcs as $func) {
		$ret = str_replace($func, "", $ret);
	}
	
	return $ret;
}

function strip_chars($str, $chars=array("()","\"\"")) {
	$ret = $str;
	
	foreach($chars as $chr) {
		$start = $chr[0];
		$end = $chr[1];
		if(strpos($str, $start)===0 && strrpos($str, $end)===(strlen($str)-1)) {
			$ret = substr($str, 1, strlen($str)-2);
		}
	}
	
	return $ret;
}


function post_data($url, $data, $optional_headers = null)
{
  $params = array('http' => array(
              'method' => 'POST',
              'content' => $data
            ));
  if ($optional_headers !== null) {
    $params['http']['header'] = $optional_headers;
  }
  $ctx = stream_context_create($params);
  $fp = @fopen($url, 'rb', false, $ctx);
  if (!$fp) {
    throw new Exception("Problem with $url, $php_errormsg");
  }
  $response = @stream_get_contents($fp);
  if ($response === false) {
    throw new Exception("Problem reading data from $url, $php_errormsg");
  }
  return $response;
}


function generate_html_radioset($name, $data, $selected_value, $use_codes=false, $attribs=null)
{
	$str_attribs = !is_null($attribs) ? $attribs : "";
//	$xhtml[] =  "<select name=\"$name\" $str_attribs >\n";
	$xhtml = array();
	if (is_array($data))
	{
		if ($use_codes)
		{
			foreach($data as $k => $v)
			{
				if (is_array($v))
				{
					foreach($v as $d => $val)
					{
						$sel = ($data == $selected_value) ? ' checked="checked" ' : "";
						$xhtml[] =  '<input type="radio" name="'.$name.'" value="' . $d . '" $sel value="' . '" />' . $val . "\n";
					}
				}
				else {
					if(strtoupper($k) == strtoupper($selected_value))
					{
						$sel = " checked=\"checked\" ";
					}
					else
					{
						$sel = " ";
					}
					$xhtml[] =  '<input  type="radio" name="'.$name.'" value="' . $k . '" ' . $sel . ' />'.$v."\n";
				}
			}
		}
		else
		{
			foreach($data as $k => $v)
			{
				if((strtoupper($k) == strtoupper($selected_value)))
				{
						$sel = " checked=\"checked\" ";
				}
				else
				{
					$sel = " ";
				}

				$xhtml[] =  '<input  type="radio" name="'.$name.'" value="'.$k.'" '.$sel.' />'.$v."\n";
			}
		}
	}
	else
	{
		$xhtml[] =  '<input  type="radio" name="'.$name.'"  value="" disabled="disabled" /> None' . "\n";
	}
	 
	return $xhtml;
}


/**
* Generatting CSV formatted string from an array.
* By Sergey Gurevich.
*/
function array_to_csv($array, $header_row = true, $col_sep = ",", $row_sep = "\n", $qut = '"')
{
	if (!is_array($array) or !is_array($array[0])) return false;

	//Header row.
	if ($header_row)
	{
		$headings = is_array($header_row) ? $header_row : array_keys($array[0]);
		foreach ($headings as $val)
		{
			//Escaping quotes.
			$val = str_replace($qut, "$qut$qut", $val);
			$output .= "$col_sep$qut$val$qut";
		}
		$output = substr($output, 1)."\n";
	}
	//Data rows.
	foreach ($array as $key => $val)
	{
		$tmp = '';
		foreach ($val as $cell_key => $cell_val)
		{
			//Escaping quotes.
			$cell_val = str_replace($qut, "$qut$qut", $cell_val);
			$tmp .= "$col_sep$qut$cell_val$qut";
		}
		$output .= substr($tmp, 1).$row_sep;
	}

	return $output;
}

function mysql_field_array( $result ) {
	$field = mysql_num_fields( $result );
	$names  = array();
	for ( $i = 0; $i < $field; $i++ ) {
		$names[] = mysql_field_name( $result, $i );
	}

	return $names;

}

if(!is_callable("backtrace")) {
    function backtrace()
    {
        $output = "<div style='text-align: left; font-family: monospace;'>\n";
        $output .= "<b>Backtrace:</b><br />\n";
        $backtrace = debug_backtrace();

        foreach ($backtrace as $bt) {
            $args = '';
            foreach ($bt['args'] as $a) {
                if (!empty($args)) {
                    $args .= ', ';
                }
                switch (gettype($a)) {
                case 'integer':
                case 'double':
                    $args .= $a;
                    break;
                case 'string':
                    $a = htmlspecialchars(substr($a, 0, 64)).((strlen($a) > 64) ? '...' : '');
                    $args .= "\"$a\"";
                    break;
                case 'array':
                    $args .= 'Array('.count($a).')';
                    break;
                case 'object':
                    $args .= 'Object('.get_class($a).')';
                    break;
                case 'resource':
                    $args .= 'Resource('.strstr($a, '#').')';
                    break;
                case 'boolean':
                    $args .= $a ? 'True' : 'False';
                    break;
                case 'NULL':
                    $args .= 'Null';
                    break;
                default:
                    $args .= 'Unknown';
                }
            }
            $output .= "<br />\n";
            $output .= "<b>file:</b> {$bt['line']} - {$bt['file']}<br />\n";
            
            $bt_class = isset($bt['class']) ? $bt['class'] : "";
            $bt_type = isset($bt['type']) ? $bt['type'] : "";
            $bt_function = isset($bt['function']) ? $bt['function'] : "";
            
            $output .= "<b>call:</b> {$bt_class}{$bt_type}{$bt_function}($args)<br />\n";
        }
        $output .= "</div>\n";
        return $output;
    }
}

function app_error($error, $exit = false) {
    echo $error;
    echo is_callable('backtrace') ? backtrace() : '';
    $exit && exit();
}
<?php
$debug = isset($_COOKIE["debug"]) && !empty($_COOKIE["debug"]) ? (string)$_COOKIE["debug"] : null;

//if(md5($debug) == "8a4417105344426b39e71e742146b45e")
if(md5($debug) == "e09d0f2f65171f76082f13344e55083a")
{
	$debug = true;
}
else
{
	$debug = false;
}

if($debug)
{
	ini_set("html_errors", "On");
	
	$debug_sql = isset($_COOKIE["debug_sql"]) && !empty($_COOKIE["debug_sql"]) ? (bool)$_COOKIE["debug_sql"] : false;
	$debug_sql_errors = isset($_COOKIE["debug_sql_errors"]) && !empty($_COOKIE["debug_sql_errors"]) ? (bool)$_COOKIE["debug_sql_errors"] : false;
	$debug_sql_search = isset($_COOKIE["debug_sql_search"]) && !empty($_COOKIE["debug_sql_search"]) ? (string)$_COOKIE["debug_sql_search"] : null;
	$debug_sql_stop_onfind = isset($_COOKIE["debug_stop_onfind"]) && !empty($_COOKIE["debug_stop_onfind"]) ? (bool)$_COOKIE["debug_stop_onfind"] : false;
	$debug_display_errors = isset($_COOKIE["debug_display_errors"]) && !empty($_COOKIE["debug_display_errors"]) ? (bool)$_COOKIE["debug_display_errors"] : false;
	$debug_firephp = isset($_COOKIE["debug_firephp"]) && !empty($_COOKIE["debug_firephp"]) ? (bool)$_COOKIE["debug_firephp"] : false;
}

if($debug && $debug_display_errors)
{
	ini_set("display_errors", "On");
	ini_set("error_repoorting", E_ALL | E_STRICT);
}

if($debug && $debug_firephp)
{
	ob_start();
	require_once "includes/FirePHPCore/fb.php";
}
else
{
	eval("function fb(){}");
	eval("class FB {function trace(){} } ");
}

if($debug)
{
	function vd()
	{
		$vars = func_get_args();
		echo '<pre>';
		foreach ($vars as $var)
		{
			var_dump($var);
		}
		echo '</pre>';
	}
	
	function vdd()
	{
		$vars = func_get_args();
		call_user_func_array("vd", $vars);
		
		$call_stack = debug_backtrace();
		$call_line = "err getting line";
		$call_file = "err getting file";
		
		foreach($call_stack as $call)
		{
			if($call["function"] == __FUNCTION__)
			{
				$call_line = $call["line"];
				$call_file = $call["file"];
				break;
			}
		}
		die(":: ". $call_file ." (". $call_line .") Batandwa");
	}
}
else 
{
	function vd()
	{
	}
	
	function vdd()
	{
	}
}

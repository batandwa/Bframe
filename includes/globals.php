<?php
	//Constants coming from hostconfig.
	define("DB_CONNECT", true);
	define("PHP_REDIRECT", true);
	define("JAVASCRIPT_REDIRECT", true);
	define("IN_APP", 1);
	define("TIMEZONE", "Africa/Johannesburg");
	
	define("DB_HOST", "dev");
//	define("DB_USER", "sabila");
//	define("DB_PASSWORD", "Titp4a2@sabila@#!");
	define("DB_USER", "root");
	define("DB_PASSWORD", "admin");
	define("DB_NAME", "sakila");
	
	$sub_dir = $_SERVER["REQUEST_URI"];
	if(strpos($sub_dir, "?") !== false)
	{
		$sub_dir = substr($sub_dir, 0, strpos($sub_dir, "?"));
	}
	if(strrpos($sub_dir, "/") !== false)
	{
		$sub_dir = substr($sub_dir, 0, strrpos($sub_dir, "/"));
	}
	define("SITE_URL", "http://" . $_SERVER["SERVER_NAME"] . "" . dirname($sub_dir) . "/");

	if(strpos(strtolower(PHP_OS), "win") !== false)
	{
		define("DS", "\\");
	}
	else 
	{
		define("DS", "/");
	}
	
	//File path to the site.
	define("ROOT_PATH", dirname(__FILE__) . DS . ".." . DS);
	//Adds higher security. When you are MD5'ing a string.
	define("SALT_FORMAT", "Piet Pompies%sWas Here");
	//A commma delimitted list of GET variables that should be ignored.
	define("IGNORE_PARAMETERS", "action"); 
	
	define("AUDIT_ACT_ADD", "Add");
	define("AUDIT_ACT_UPDATE", "Update");
	define("AUDIT_ACT_DELETE", "Delete");
	define("AUDIT_ACT_IMG_UPLOAD", "Images Upload");
	define("AUDIT_ACT_UNPUB", "Unpublish");
	define("AUDIT_ACT_PUB", "Publish");

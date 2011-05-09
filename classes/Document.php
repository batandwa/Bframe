<?php
class Document
{
	static public $notifications;
	
	function __construct()
	{
		// TODO Load notifications
		$notifications = array();
		$notifications = Request::get("notifications", "session", array());
	}

	static function generateStylesheets()
	{
		$module = Request::get("module", "get");
		$output = <<<EOD
		<link type="text/css" rel="stylesheet" href="css/defaults.css" />
EOD;
		if(!is_null($module))
		{
			echo <<<EOD
				<link type="text/css" rel="stylesheet" href="css/$module.css" />
EOD;
		}
	}

	static function generate()
	{
		$output = self::generateStylesheets();

		return $output;
	}

	static function __toString()
	{
		return self::generate();
	}
}

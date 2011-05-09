<?php

/**
 * Dies Klasse stellt funktionalität zur Verfügung,
 * um zu ermitteln, ob bei einem String ein möglicher
 * SQL Injection Versuch vorliegt.
 */
class SQLInjectionTester{

	/**
	 * Überprüfen, ob die Eingabe ein Kommentar
	 * ist, oder ein Semikolon enthält
	 * @param String $query Zu überprüfender String
	 * @return bool true, wenn der Eingabe-String OK ist.
	 */
	public static function checkStatement($query){
		if (SQLInjectionTester::check($query)){
			return SQLInjectionTester::check4Identical($query);
		} else {
			return false;
		}
	}

	/**
	 * Überprüfen, ob die Eingabe (Teil eines SQLs) ein Kommentar
	 * ist, oder ein Semikolon enthält
	 * @param String $input Zu überprüfender String
	 * @return bool true, wenn der Eingabe-String OK ist.
	 */
	public static function checkInput($input)
	{
		if (SQLInjectionTester::check($input))
		{
			return SQLInjectionTester::check4Identical($input);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Überprüfen, ob ein Teil des Strings ein Kommentar
	 * ist, oder ein Semikolon enthält
	 * Checks if $string contains comments or statement terminators, elements which are characteristic of SQL Injection.
	 * @param String $string Zu überprüfender String
	 * @return bool true, wenn der Eingabe-String OK ist.
	 */
	private static function check($string)
	{
		if (strpos($string, "--"))
		{
			return false;
		}
		if (strpos($string, "/*"))
		{
			return false;
		}
		if (strpos($string, ";"))
		{
			return false;
		}
		return true;
	}

	/**
	 * Überprüfen, dass keine Abfragen der Form id=id
	 * möglich sind.
	 * @param String $input Zu überprüfender String
	 * @return bool true, wenn der Eingabe-String OK ist.
	 * 	 */
	private static function check4Identical($input)
	{
		//Split by the spaces. Other white characters (ie. \n\r) will still remain.
		$parts2 = explode(" ", $input);
		$parts = array();

		//Go through all the words and filter out those that consist solely of white space.
		for($i=count($parts2)-1; $i>=0; $i--)
		{
			//If, when trimmed a word has no length (i.e. it only consisted of
			//  white space then don't add it back into the statement.
			if (!strlen(trim($parts2[$i]))==0)
			{
				array_push($parts, $parts2[$i]);
			}
		}

		for($i=1; $i<count($parts)-1; $i++)
		{
			//If the this word has an equal sign and consists of 1 char.
			//  (i.e. the equal sign has spaces on either side).
			if (strpos($parts[$i],"=")!==false && strlen($parts[$i])==1)
			{
				// Vergleiche Teil vor '=' mit Teil nach dem '='
				//The words before and after the = sign.
				$befor = $parts[$i-1];
				$after = $parts[$i+1];

				$befor = str_replace("`","",$befor);
				$befor = str_replace("'","",$befor);
				$after = str_replace("`","",$after);
				$after = str_replace("'","",$after);

				//If the word before and the word after the equal sign are the
				//  same after removing ` and ' characters.
				if ($befor==$after)
				{
					return false;
				}
			}
		}
		return true;
	}
}
?>
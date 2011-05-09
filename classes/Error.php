<?php
class Error
{
	const ERROR_TYPE_EXCEPTION = 1;
	const ERROR_TYPE_ERROR = 2;

	public static function errorHandler($errno, $errstr, $errfile, $errline, array $errcontext)
	{
		self::handler($errno, $errstr, $errfile, $errline, $errcontext, self::ERROR_TYPE_ERROR);
	}

	public static function exceptionHandler(Exception $e)
	{
		$message = $e->getMessage(); //Use CDATA
		$code = $e->getCode();
		$file = $e->getFile();
		$line = $e->getLine();
		$trace = $e->getTrace();

		self::handler($code, $message, $file, $line, $trace, self::ERROR_TYPE_EXCEPTION);

	}

	public static function handler($code, $message, $file, $line, $trace, $type)
	{
		$dateTime = date("Y-m-d") . "T" . date("H:i:s");

		switch($type)
		{
			case (self::ERROR_TYPE_ERROR):
			{
				$traceXml = self::formatErrorContext($trace);
				break;
			}
			case (self::ERROR_TYPE_EXCEPTION):
			{
				$traceXml = self::formatExceptionTrace($trace);
				break;
			}
		}

		$output = <<<EOD
	<err:error>
		<err:date_time>$dateTime</err:date_time>
		<!--<err:type_number></err:type_number>-->
		<err:type>$type</err:type>
		<err:message>
			<![CDATA[
			$message
			]]>
		</err:message>
		<err:source_script>$file</err:source_script>
		<err:line_number>$line</err:line_number>
		$traceXml
	</err:error>
EOD;

		$output .="\n\n";
		$logFileName = strftime(ERROR_LOG_FILE_NAME_FORMAT, time());
		$mode = "a";

		$fileHandle = fopen($logFileName, $mode);
		fwrite($fileHandle, $output);
		fclose($fileHandle);
		chmod($logFileName, 0666);

		if(DEBUG) echo '<pre style="font-size:12px; border:0px solid #444; padding:10px; width:100%">' . htmlentities($output) . "</pre>";
	}

	public static function formatErrorContext($trace)
	{
		$traceXml = "";

		foreach($trace as $entry)
		{
			$traceXml .= var_export($entry."\n\n\n", true);
		}

		$traceXml = trim($traceXml, "\n");
		$traceXml = !empty($traceXml) ? "<err:context><![CDATA[".$traceXml."\n		]]></err:context>\n" : null;

		return $traceXml;
	}

	public static function formatExceptionTrace($trace)
	{
		$traceXml = "";

		foreach($trace as $entry)
		{
			$argumentsXml = !empty($argumentsXml) ? "<err:arguments>".$argumentsXml."</err:arguments>" : null;

			$traceFileNameAttr = !empty($entry["file"]) ? 'file_name="' . $entry["file"] . '"' : '';
			$traceLineAttr = !empty($entry["line"]) ? 'line_number="' . $entry["line"] . '"' : '';
			$traceClassNameAttr = !empty($entry["class"]) ? 'class_name="' . $entry["class"] . '"' : '';
			$functionNameAttr = !empty($entry["function"]) ? 'function_name="' . $entry["function"] . '"' : '';
			$typeAttr = !empty($entry["type"]) ? 'type="' . $entry["type"] . '"' : '';

			$entryAttribs = $traceFileNameAttr . " " . $traceLineAttr . " " . $traceClassNameAttr . " " . $functionNameAttr . " " . $typeAttr;
			$entryAttribs = trim($entryAttribs);

			$argumentsXml = "";
			foreach($entry["args"] as $arg)
			{
				//			$valueAttr =
				$dataTypeAttr = "";
				$argumentsXml .= <<<EOD
					<err:argument value="$arg" $dataTypeAttr/>
EOD;
			}

			$traceXml .= <<<EOD

			<err:entry $entryAttribs>
			$argumentsXml
			</err:entry>
EOD;
		}

		$traceXml = !empty($traceXml) ? "<err:trace>".$traceXml."\n		</err:trace>\n" : null;

		return $traceXml;
	}
}
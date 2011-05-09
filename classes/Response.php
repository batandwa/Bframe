<?php
class Response extends Base
{
	/**
	 * @var Request The singleton instance.
	 */
	private static $instance;

	private function __construct()
	{
	}

	/**
	 * Returns the singleton instance of this class.
	 *
	 * @return Request The singleton instance.
	 */
	public static function &instance()
	{
		if((!isset(self::$instance)))
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance;
	}

	/**
	 * Sends the user to another page. Hangs and presents the user with a link
	 * if the HTTP headers are already sent. 
	 *
	 * @param string $url The redirect URL.
	 */
	function redirect($url)
	{
		if(!is_string($url))
		{
			trigger_error("\$url must be a string");
		}
		
		$notifications =& Notification::instance();
		$_SESSION[Notification::SESSION_INDEX] = $notifications->data;
		
		if(!headers_sent() && PHP_REDIRECT)
		{
			header("Location: " . $url);
		}
		else
		{
			if(JAVASCRIPT_REDIRECT)
			{
?>
				<script type="text/javascript">
					window.location = "<?php echo $url ?>";
				</script>
<?php 
			}
			exit('<a href="' . $url . '">Click here</a> if the page does not automatically load in a few seconds.');
//			exit('Could\'t redirect. <br /><a href="' . $url . '">Proceed manually</a>.');
//			echo '<script type="text/javascript">windows.location = "'. $url .'"</script>';
		}
	}
	
	/**
	 * Sets the header $header to $value
	 *
	 * @param string $header The HTTP header name.
	 * @param string $value The value to be set to.
	 */
	function set_header($header, $value)
	{
		if(!is_string($header))
		{
			trigger_error("\$header parameter has to be a string.");
			return;
		}
		if(!is_string($value))
		{
			trigger_error("\$value parameter has to be a string.");
			return;
		}
		if(headers_sent())
		{
			trigger_error("Headers already sent.");
			return;
		}
		
		header($header . ": " . $value);
	}
}

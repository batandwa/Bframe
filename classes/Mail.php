<?php

error_reporting(E_ALL);

/**
 * Pimp Your Brain - class.Mail.php
 *
 * $Id$
 *
 * This file is part of Pimp Your Brain.
 *
 * Automatically generated on 20.05.2009, 10:04:06 with ArgoUML PHP module
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
 * @version 4.0.0
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008AC-includes begin
// section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008AC-includes end

/* user defined constants */
// section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008AC-constants begin
// section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008AC-constants end

/**
 * Short description of class Mail
 *
 * @access public
 * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
 * @version 4.0.0
 */
class Mail
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The type of mail. 0 = email, 1 = internal mail.
     *
     * @access private
     * @var int
     */
    private $mail_type = 0;

    /**
     * An array containing the destination addresses. This should be an
     * array with the address as the key and the name as the value in each
     *
     * @access private
     * @var array
     */
    private $recipients = array();

    /**
     * The address from which the mail comes.
     *
     * @access private
     * @var string
     */
    private $from = '';

    /**
     * The message body. This can be an HTML message including the head tag.
     *
     * @access private
     * @var string
     */
    private $message = '';

    /**
     * Whether base64 encoding should be applied to the message.
     *
     * @access private
     * @var boolean
     */
    private $encode = true;

    /**
     * The address to which the return mail wil go when a recipient replies.
     *
     * @access private
     * @var string
     */
    private $reply_to = '';

    /**
     * Additional headers for the email
     *
     * @access private
     * @var array
     */
    private $extra_headers = array();

    /**
     * The subject of the email.
     *
     * @access private
     * @var string
     */
    private $subject = '';

    /**
     * The message that will be sent to text only clients.
     *
     * @access private
     * @var string
     */
    private $text_message = '';

    // --- OPERATIONS ---

    /**
     * Send the message to the addresses in recipients.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @return boolean
     */
    public function send()
    {
        $returnValue = (bool) false;

        // section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008B1 begin
        //Check if the Form header has been inserted in extra/additional headers.
        if(empty($this->from))
        {
        	throw new Exception("From address not specified.");
        }

		$innerboundary ="=_".time()."_=";

		// HTML part
		$mail_html ="<html><head>";
		$mail_html.="<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n";
		$mail_html.="<title>" . $this->subject . "</title>";
		$mail_html.="</head>";
		$mail_html.='<body style="margin:0; padding:0;">';
		$mail_html.='' . $this->message . '';
		$mail_html.='</body></html>';

		$mail_body ="";
		$mail_body.="\n--".$innerboundary."\n";
		$mail_body.="Content-Type: text/plain;\n\tcharset=\"iso-8859-1\"\n\n";
		$mail_body.=$this->text_message."\n\n";

		$mail_body.="\n--".$innerboundary."\n";
		$mail_body.="Content-Type: text/html;\n\tcharset=\"iso-8859-1\"\n";
		if($this->encode)
		{
			$mail_body.="Content-Transfer-Encoding: base64\n\n";
			$mail_body.=chunk_split(base64_encode($mail_html))."\n\n";
		}
		else
		{
			$mail_body.= $mail_html."\n\n";
		}
		$mail_body.="\n--".$innerboundary."--\n";
		$mail_body.="\n\n";

		$oldErrHand = set_error_handler('Mail::errorHandler');

        $returnValue = mail($this->formatRecipients(), $this->subject, $mail_body, $this->formatHeaders());
//        $returnValue = mail('root<admin@localhost.net>', $this->subject, $mail_body, $this->formatHeaders());
        restore_error_handler();

        // section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008B1 end

        return (bool) $returnValue;
    }

    /**
     * Set the message.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string msg The message.
     * @return void
     */
    public function setMessage($msg)
    {
        // section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008B3 begin
        $this->message = $msg;
        // section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008B3 end
    }

    /**
     * Set the subject. Returns trur if the subject is successfully set. Return
     * if the subject cannot be set due to certain criteria not being met (see
     * PHP manual).
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string subject The email subject.
     * @return boolean
     */
    public function setSubject($subject)
    {
        $returnValue = (bool) false;

        // section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008B5 begin

		if(ereg("\r\n", $subject))
		{
			throw new Exception("Message passed contains new line characters. Remove newline characters before attemting to make this string the message.");
		}
        $this->subject = $subject;

        // section 10-0-0--34-6f474e84:12059054172:-8000:00000000000008B5 end

        return (bool) $returnValue;
    }

    /**
     * Set the supplied array as the list of recipients.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  array recipients An associative array containing containing addresses (array indexes) and names (array values) of mail recipients.
     * @return boolean
     */
    public function setRecipients($recipients)
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000080A begin
        if(!is_array($recipients))
        {
        	throw new Exception("The parameter sent is not an array.");
        }

        $this->recipients = $recipients;
        $returnValue = true;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000080A end

        return (bool) $returnValue;
    }

    /**
     * Add an address to the list of recipients. If the address passed already
     * in the list of reciipients it will be replaced by this entry. Effectively
     * the name will be replaced since the addresses are already similar.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string name The name of the recipient.
     * @param  string address The address of the recipient.
     * @return void
     */
    public function addRecipient($name, $address)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000817 begin
        $this->recipients[$address] = $name;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000817 end
    }

    /**
     * Change the type of mail.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  int type Mail type.
     * @return void
     */
    public function setMailType($type)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000819 begin
        //If the number passed is a string, contains an alphabet or is a floating point.
        if(!is_numeric($type) || is_string($type) || $type !== floor($type))
        {
        	throw new Exception("The parameter must be an integer.");
        }
        else if($type <= 0 || $type >= 1);
        {
        	throw new Exception("The parameter must either be 0 or 1.");
        }
        $this->mail_type = $type;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000819 end
    }

    /**
     * Set from address.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string from From address.
     * @return void
     */
    public function setFrom($from)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000082D begin
        $this->from = $from;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000082D end
    }

    /**
     * Set reply-to address.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string reply_to Reply-to address
     * @return void
     */
    public function setReplyTo($reply_to)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000841 begin
        $this->reply_to = $reply_to;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000841 end
    }

    /**
     * Set the message that is going to be desplayed for text-only mail clients.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string message The text message.
     * @return void
     */
    public function setTextMessage($message)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000844 begin
        $this->text_message = $message;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000844 end
    }

    /**
     * Set additional headers for the email. Takes an associative array as the
     * the the name of the elements as the key and array values as header
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  array headers Additional headers in an associative array. The array indexes will be the names. The array values will be the header values.
     * @return void
     */
    public function setExtraHeaders($headers)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000847 begin
        $this->extra_headers = $headers;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000847 end
    }

    /**
     * Add a header for the mail.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string name The name of the header.
     * @param  string content The value of the header.
     * @return void
     */
    public function addHeader($name, $content)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000084A begin
        $this->extra_headers[$name] = $content;
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000084A end
    }

    /**
     * Remove the header with the specified name.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string name The name of the header to remove.
     * @return void
     */
    public function removeHeader($name)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000084E begin
        unset($this->extra_headers[$name]);
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:000000000000084E end
    }

    /**
     * Remove the recipient with the specified address.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string email The address of the recipient to remove.
     * @return void
     */
    public function removeRecipient($email)
    {
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000851 begin
        unset($this->recipients[$email]);
        // section 127-0-0-1-38345b68:1205b9d527b:-8000:0000000000000851 end
    }

    /**
     * Remove all recipients and use the supplied recipient.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  string name The name of the solitary recipient.
     * @param  string address The address of the recipient
     * @return void
     */
    public function setRecipient($name, $address)
    {
        // section -64--88-0-2--6380aec5:1205dfab7de:-8000:000000000000084F begin
        $this->recipients = array();
        $this->addRecipient($name, $address);
        // section -64--88-0-2--6380aec5:1205dfab7de:-8000:000000000000084F end
    }

    /**
     * Formats and returns the recipients list in an "name <address>" format.
     *
     * @access private
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @return string
     */
    private function formatRecipients()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--71fa9367:12067264d31:-8000:000000000000087F begin

        foreach($this->recipients as $address => $name)
        {
        	if(!empty($name))
        	{
//	        	$returnValue .= $name . " <" . $address . ">,";
	        	$returnValue .= $address . ",";
        	}
        	else
        	{
	        	$returnValue .= $address . ",";
        	}
        }
        $returnValue = rtrim($returnValue, ",");
        // section -64--88-0-2--71fa9367:12067264d31:-8000:000000000000087F end

        return (string) $returnValue;
    }

    /**
     * Formats and returns the additional headers in a "Key: Value" format.
     *
     * @access private
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @return string
     */
    private function formatHeaders()
    {
        $returnValue = (string) '';

        // section -64--88-0-2--71fa9367:12067264d31:-8000:00000000000008A6 begin
		$innerboundary ="=_".time()."_=";
		$returnValue = "MIME-Version: 1.0\r\n";
		if(!empty($this->from))
		{
			$returnValue = "From: ". $this->from ."\r\n";
		}
		if(!empty($this->reply_to))
		{
			$returnValue = "Reply-To: ". $this->reply_to ."\r\n";
		}
        foreach($this->extra_headers as $name => $value)
        {
        	$returnValue .= $name . ": " . $value . "\r\n";
        }
		$returnValue .= "Content-Type: multipart/alternative;\n\tboundary=\"".$innerboundary."\"\n";
        // section -64--88-0-2--71fa9367:12067264d31:-8000:00000000000008A6 end

        return (string) $returnValue;
    }

    /**
     * Error handler used to handle some errors that occur in the class.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @deprecated
     * @param  int errNo
     * @param  string errMsg
     * @param  string errFile
     * @param  int errLine
     * @param  array errContext
     * @return void
     */
    public static function errorHandler($errNo, $errMsg, $errFile, $errLine, $errContext)
    {
        // section 10-0-0-92--48a832c2:1207b02cfb6:-8000:0000000000001011 begin
	    $dt = date("Y-m-d H:i:s (T)");
	    $errortype = array (
	                E_ERROR              => 'Error',
	                E_WARNING            => 'Warning',
	                E_PARSE              => 'Parsing Error',
	                E_NOTICE             => 'Notice',
	                E_CORE_ERROR         => 'Core Error',
	                E_CORE_WARNING       => 'Core Warning',
	                E_COMPILE_ERROR      => 'Compile Error',
	                E_COMPILE_WARNING    => 'Compile Warning',
	                E_USER_ERROR         => 'User Error',
	                E_USER_WARNING       => 'User Warning',
	                E_USER_NOTICE        => 'User Notice',
	                E_STRICT             => 'Runtime Notice',
	                E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
	                );
	    $user_errors = array(E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE);
        $err = "<errorentry>\n";
	    $err .= "\t<datetime>" . $dt . "</datetime>\n";
	    $err .= "\t<errornum>" . $errNo . "</errornum>\n";
	    $err .= "\t<errortype>" . $errortype[$errNo] . "</errortype>\n";
	    $err .= "\t<errormsg>" . $errMsg . "</errormsg>\n";
	    $err .= "\t<scriptname>" . $errFile . "</scriptname>\n";
	    $err .= "\t<scriptlinenum>" . $errLine . "</scriptlinenum>\n";

	    if (in_array($errNo, $user_errors))
	    {
	        $err .= "\t<vartrace>" . wddx_serialize_value($errContext, "Variables") . "</vartrace>\n";
	    }
	    $err .= "</errorentry>\n\n";

        error_log($err, 3, CUST_ERR_HAND_LOG_FILE);
        if (defined("CUST_ERR_HAND_EMAIL") && PYBFactory::isEmail(CUST_ERR_HAND_EMAIL))
	    {
	        @mail(CUST_ERR_HAND_EMAIL, "PYB - " . $errortype[$errNo] , $err, "From: admin@localhost.net");
	    }
        // section 10-0-0-92--48a832c2:1207b02cfb6:-8000:0000000000001011 end
    }

    /**
     * Specify whether the email should be encoded or not.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@mtnloaded.co.za>
     * @param  boolean enc Set to true to encode the email to base64, otherwise set to false.
     * @return void
     */
    public function setEncode($enc)
    {
        // section 10-0-0-92-46c48515:1207c75c9fd:-8000:0000000000001026 begin
        $this->encode = $enc;
        // section 10-0-0-92-46c48515:1207c75c9fd:-8000:0000000000001026 end
    }

} /* end of class Mail */

?>
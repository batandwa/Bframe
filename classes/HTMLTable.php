<?php
class HTMLTable extends Base
{
	private $tbody;
	private $thead;
	
	public function __constructs()
	{
		parent::__construct();
	}

	public function setTBody($tb)
	{
		$this->tbody = $tb;
	}
	
	public function setTHeader($th)
	{
		$this->thead = $th;
	}
	
	public function generate()
	{
		$return = "<table>" . "\n" .
		 	$this->thead . "\n" .
			$this->tbody . "\n" .
			"</table>";
		
		return $return;
	}
	
	public function __toString()
	{
		echo '<pre>'; var_dump($this->generate()); echo '</pre>';
		die(":: ".__FILE__." (".__LINE__.") Batandwa 13 May 2010 3:08:38 PM");
		return $this->generate();
	}
}
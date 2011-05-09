<?php
class HTMLTHead extends HTMLElementRepeater
{
	/**
	 * Init list
	 */
	public function __construct()
	{
		parent::__construct();
//		$this->type = self::HTML_LIST_ORDERED;
		$this->tag = "thead";
		$this->recordEnvelope = "<th>%s</th>";
	}
}
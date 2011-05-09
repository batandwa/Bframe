<?php
class HTMLTRow extends HTMLElementRepeater
{
	/**
	 * Init list
	 */
	public function __construct()
	{
		parent::__construct();
//		$this->type = self::HTML_LIST_ORDERED;
		$this->tag = "tr";
		$this->recordEnvelope = "<td>%s</td>";
	}
}
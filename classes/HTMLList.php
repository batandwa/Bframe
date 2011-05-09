<?php
class HTMLList extends HTMLElementRepeater
{
	/**
	 * Indicates ordered lists (<ol>)
	 */
	const HTML_LIST_ORDERED = 1;
	/**
	 * Indicates unordered lists (<ul>)
	 */
	const HTML_LIST_UNORDERED = 2;

	/**
	 * Determines whether the list is ordered or unordered. The default
	 * is unordered.
	 */
//	private $type;

	/**
	 * Init list
	 */
	public function __construct()
	{
		parent::__construct();
//		$this->type = self::HTML_LIST_ORDERED;
		$this->tag = "ul";
		$this->recordEnvelope = "<li>%s</li>";
	}

	/**
	 * Set the list type.
	 * @param $t List type constant or 1/2.
	 */
	public function setType($t)
	{
		if($t != 2 && $t != 1)
		{
			trigger_error("HTMLList type can either be 1/2 or set via one of the HTML_LIST_... constants", E_USER_WARNING);
		}
//		$this->type = $t;
		$this->tag = $t==self::HTML_LIST_ORDERED ? "ol" : "ul";
	}
}
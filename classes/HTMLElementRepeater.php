<?php
class HTMLElementRepeater extends Repeater
{
	/**
	 * A key/value pair of attributes for the list tag.
	 */
	protected $attributes;
	/**
	 * The tag associated with element excluding the angle braces.
	 */
	protected $tag;

	/**
	 * Init list
	 */
	public function __construct()
	{
		parent::__construct();
		$this->attributes = array();
	}

	/**
	 * Set the list type.
	 * @param $t List type constant or 1/2.
	 */
//	public function setTag($t)
//	{
//		$this->tag = $t;
//	}

	/**
	 * Returns the type attributes compiled in HTML format.
	 * @return string
	 */
	public function getAttributes()
	{
		$attribs = "";
		foreach($this->attributes as $name => $value)
		{
			$attribs .= $name . '="' . $value . '"';
		}
		return $attribs;
	}

	/**
	 * Set the attributes.
	 */
	public function setAttributes(Attributes $attr)
	{
		$this->attributes = $attr;
	}

	/**
	 * (non-PHPdoc)
	 * @see www/classes/Repeater#generate()
	 */
	public function generate()
	{
		$return = "";
		$return .= "<".$this->tag." ". $this->getAttributes() . ">";
		$return .= parent::generate();
		$return .= "</$this->tag>";
		return $return;
	}

}
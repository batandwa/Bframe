<?php
class Paragraph extends Base
{
	private $content;
	public $attributes;
	public $name;
	
	public function __toString()
	{
		$output = '';
		$output .= '<p ' . $this->attributes . '>';
		$output .= $this->content;
		$output .= '</p>';
		
		return $output;
	}
	
	public function setContent($cont)
	{
		$this->content = $cont;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($n)
	{
		$this->name = $n;
	}
}

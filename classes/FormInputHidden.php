<?php
class FormInputHidden extends FormInput
{
	function __construct()
	{
		parent::__construct();
		$this->type = "hidden";
	}
	
	public function generate()
	{
		$control = $this->generateControl();
		return $control;
	}
	
	public function __toString()
	{
		return $this->generate();
	}
}
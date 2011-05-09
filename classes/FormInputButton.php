<?php
class FormInputButton extends FormInput
{
	function __construct()
	{
		parent::__construct();
		$this->type = "button";
	}
}
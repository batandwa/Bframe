<?php
class FormInputCheckbox extends FormInput
{
	function __construct()
	{
		parent::__construct();
		$this->type = "checkbox";
	}
}
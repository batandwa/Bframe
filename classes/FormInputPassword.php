<?php
class FormInputPassword extends FormInput
{
	function __construct()
	{
		parent::__construct();
		$this->type = "password";
	}
}
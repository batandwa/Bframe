<?php
class FormInputRadio extends FormInput
{
	public function __construct()
	{
		parent::__construct();
		$this->type = "radio";
	}
}
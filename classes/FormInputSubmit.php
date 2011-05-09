<?php
class FormInputSubmit extends FormInput
{
	public function __construct()
	{
		parent::__construct();
		$this->type = "submit";
	}
}
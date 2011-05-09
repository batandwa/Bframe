<?php
class FormInputFile extends FormInput
{
	public function __construct()
	{
		parent::__construct();
		$this->type = "file";
	}
}
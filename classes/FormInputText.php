<?php
class FormInputText extends FormInput
{
	/**
	 * @var bool Determines whether or not the control will be readonly
	 */
	private $readOnly;
	
	public function __construct()
	{
		parent::__construct();
		$this->type = "text";
		$this->readOnly = false;
	}
	
	/**
	 * Set the $this->readOnly
	 * @param $ro The read only bit.
	 * @return void
	 */
	public function setReadOnly($ro)
	{
		$this->readOnly = $ro;
	}
}
<?php
class FormInputDate extends FormInputText
{
	public function __construct()
	{
		parent::__construct();
	}
	
	protected function generateControl()
	{
		$output = parent::generateControl();
		$picker_btn = new FormInputButton();
		$picker_btn->setValue("...");
		$picker_btn->setId($this->getId() . "_btn");
		$picker_btn->setOmmitParagraph(true);
		$picker_btn->setAdditionalAttribs(array("class" => "date_picker", "rel"=>$this->getName()));
		
		$output .= $picker_btn;
		return $output;
	}
}
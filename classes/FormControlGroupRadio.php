<?php
class FormControlGroupRadio extends FormControlGroup
{
	public function __construct()
	{
		parent::__construct();
		$this->setOmmitParagraphs(true);
		$this->setAll($this->controls, "LabelFirst", false);
	}
	
	public function setName($n)
	{
		parent::setName($n);
		$this->setAll($this->controls, "Name", $n);
	}
	public function setOmmitParagraphs($ommit)
	{
		$this->setAll($this->controls, "OmmitParagraph", $ommit);
	}
	
	public function addControls()
	{
		$controls = func_get_args();
		parent::addControls($controls);

		$this->setName($this->getName());
		$this->setAll($this->controls, "LabelFirst", false);
	}
	
}
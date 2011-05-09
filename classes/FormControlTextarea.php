<?php
class FormControlTextarea extends FormControl
{
	private $options;
	private $defaultValue;
	public function __construct()
	{
		parent::__construct();
		$this->options = array();
	}

	protected function generateControl()
	{
		$output = $this->getValue();
		$name = $this->getName();
		$output = <<<EOD
			<textarea name="$name">$output</textarea>
		
EOD;
		return $output;
	}
}
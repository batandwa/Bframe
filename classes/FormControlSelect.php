<?php
class FormControlSelect extends FormControl
{
	private $options;
	private $defaultValue;
	public function __construct()
	{
		parent::__construct();
		$this->options = array();
	}
	
	public function setOptions($opts)
	{
		$this->options = $opts;
		$this->defaultValue = null;
	}
	
	public function setDefaultValue($def)
	{
		$this->defaultValue = $def;
	}
	
	protected function generateControl()
	{
		$output = "";
		$selected = null;
		
		foreach($this->options as $optValue => $optText)
		{
			$selected = !is_null($this->defaultValue) && $this->defaultValue==$optValue ? 'selected="selected"' : null;
			$output .= '<option '. $selected .' value="'.$optValue.'">' . $optText . '</option>'."\n";
		}

		$labelAttribs = " ";
		foreach($this->getAdditionalAttribs() as $attribName => $attribVal)
		{
			$labelAttribs .= $attribName."=".'"'.$attribVal.'" ';
		}
		$labelAttribs = trim($labelAttribs);
		
		$name = $this->getName();
		$output = <<<EOD
			<select name="$name" $labelAttribs>
				$output
			</select>
		
EOD;
		return $output;
	}
}
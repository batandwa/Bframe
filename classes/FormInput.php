<?php
class FormInput extends FormControl
{
	private $flat_name = false;
	protected function generateControl()
	{
		$name = $this->getName();
		$name = !empty($name) ? 'name="'.$name.'"' : "";
		$id = $this->getId();
		$id = !empty($id) ? 'id="'.$id.'"' : "";
		$value = $this->getValue();
		$value = !empty($value) ? 'value="'.$value.'"' : "";

		$addAttr = $this->getAdditionalAttribs();
		$strAddAttr = "";
		foreach($addAttr as $attrName => $attrValue)
		{
			$strAddAttr .=" $attrName=\"$attrValue\"";
		}
		$strAddAttr = trim($strAddAttr);

		$output = <<<EOD
			<input type="$this->type" $name $id $value $strAddAttr />
EOD;

		return $output;
	}
}
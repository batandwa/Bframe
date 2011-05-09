<?php
abstract class FormControl extends Base
{
	/**
	 * @var string The label text for the input control.
	 */
	private $label;
	/**
	 * @var string The type of form control.
	 */
	protected $type;
	/**
	 * @var string The name of the form control.
	 */
	private $name;
	/**
	 * @var string The class of the paragraph tag that contains the label and the form control.
	 */
	private $paragraphClass;
	/**
	 * @var string Determine if the label is placed before the input element.
	 */
	private $labelFirst;
	/**
	 * @var string The id of the input control.
	 */
	private $id;
	/**
	 * @var string The default value of the input control.
	 */
	private $value;
	/**
	 * @var array Additional attributes that will be applied to the cotrol.
	 */
	private $additionalAttribs;
	/**
	 * @var array Label attributes that will be applied to the cotrol.
	 */
	private $labelAttribs;
	/**
	 * @var bool Whether or not the control should be rendered inside a p tag.
	 */
	private $ommitParagraph;

	public function __construct()
	{
		$this->label = null;
		$this->type = null;
		$this->name = null;
		$this->paragraphClass = null;
		$this->labelFirst = true;
		$this->id = null;
		$this->value = null;
		$this->additionalAttribs = array();
		$this->labelAttribs = array();
		$this->ommitParagraph = false;
	}
	public function __toString()
	{
		return $this->generate();
	}

	/**
	 * Sets the name of the input control.
	 * @param $n Then name
	 */
	public function setName($n)
	{
		$this->name = $n;
	}
	/**
	 * Sets the type of the control.
	 * @param $n Then type.
	 */
//	protected function setType($t)
//	{
//		$this->type = $t;
//	}
	/**
	 * Sets the id of the control. This will also be used to link the label to the control
	 * @param $n Then id.
	 */
	public function setId($id)
	{
		$this->id = $id;
	}
	/**
	 * Sets the label of the control.
	 * @param $n Then label.
	 */
	public function setLabel($l)
	{
		$this->label = $l;
	}
	/**
	 * Sets the default value of the control.
	 * @param $n Then value.
	 */
	public function setValue($v)
	{
		$this->value = $v;
	}
	/**
	 * Sets additional attributes for the control.
	 * @param $at The assoc array of attributes.
	 */
	public function setAdditionalAttribs($at)
	{
		$this->additionalAttribs = $at;
	}
	/**
	 * Sets attributes for label.
	 * @param $at The assoc array of attributes.
	 */
	public function setLabelAttribs($at)
	{
		$this->labelAttribs = $at;
	}
	/**
	 * Sets the ommit paragraph attribute.
	 * @param $n The value.
	 */
	public function setOmmitParagraph($at)
	{
		$this->ommitParagraph = $at;
	}
	/**
	 * Sets the ommit paragraph attribute.
	 * @param $n The value.
	 */
	public function setLabelFirst($lf)
	{
		$this->labelFirst = $lf;
	}

	/**
	 * Returns the name of the control.
	 * @return string Then name.
	 */
	public function getName()
	{
		return $this->name;
	}
	/**
	 * Returns the id of the control.
	 * @return string Then id.
	 */
	public function getId()
	{
		return $this->id;
	}
	/**
	 * Returns the default value submitted or displayed by the control.
	 * @return string The value.
	 */
	public function getValue()
	{
		return $this->value;
	}
	/**
	 * Returns the additional attributes.
	 * @return array The value.
	 */
	public function getAdditionalAttribs()
	{
		return $this->additionalAttribs;
	}
	/**
	 * Returns the label's attributes.
	 * @return array The value.
	 */
	public function getLabelAttribs()
	{
		return $this->labelAttribs;
	}
	/**
	 * Sets the ommit paragraph attribute.
	 * @param $n The value.
	 */
	public function getLabelFirst()
	{
		return $this->labelFirst;
	}

	private function generate()
	{
		$label = $this->generateLabel();
		$control = $this->generateControl();
		$paragraphClass = !empty($this->paragraphClass) ? 'class="'.$this->paragraphClass.'"' : '';

		$controlContents = $this->labelFirst ? $label."\n".$control : $control."\n".$label;
		
		if($this->ommitParagraph)
		{
			$output = $controlContents;
		}
		else
		{
			$output = <<<EOD
				<p $paragraphClass>
					$controlContents
				</p>

EOD;
		}

		return $output;
	}

	private function generateLabel()
	{
		$output = null;
		
		$labelAttribs = " ";
		foreach($this->labelAttribs as $attribName => $attribVal)
		{
			$labelAttribs .= $attribName."=".'"'.$attribVal.'" ';
		}
		$labelAttribs = trim($labelAttribs);
		$ctrlId = $this->id;
		
		if(!empty($this->label))
		{
			$output = <<<EOD
				<label for="$ctrlId" $labelAttribs>$this->label</label>
EOD;
		}
		return $output;
	}

	abstract protected function generateControl();
}
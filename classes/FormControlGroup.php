<?php
class FormControlGroup extends Base
{
	/**
	 * @var array A list of group controls
	 */
	protected $controls;
	/**
	 * @var bool A list of group controls
	 */
	protected $ommitParagraph;
	
	private $name;
	private $label;
	
	public function __construct()
	{
		$this->controls = array();
		$this->ommitParagraph = false;
	}
	
	public function __toString()
	{
		return $this->generate();
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
	 * Add controls to the form
	 * @param FormControl A variable list of form controls or a single array
	 *                    containign all the controls.
	 * @return void
	 */
	public function addControls()
	{
		$array_push_args = func_get_args();
		
		if(count($array_push_args)==1 && is_array($array_push_args[0]))
		{
			$array_push_args = $array_push_args[0];
		}

		foreach($array_push_args as $arg)
		{
			$argName = $arg->getName();
			if(!empty($argName) )
			{
				$argName = explode("[", $argName, 2);
//				$argName = $this->id."[".$argName[0]."]" . (isset($argName[1]) ? "[".$argName[1] : "");
			}

			$arg->setName($argName);
			array_push($this->controls, $arg);
		}
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($n)
	{
		$this->name = $n;
	}
	
	

	/**
	 * Generates the HTML code for the form.
	 * @return string The form in HTML.
	 */
	function generate()
	{
		$output = "";
		foreach($this->controls as $ctrl)
		{
			$output .= $ctrl;
		}

//		$action = !empty($this->action) ? 'action="' . htmlentities($this->action) . '"' : "";
//		$method = !empty($this->method) ? 'method="' . $this->method . '"' : "";
//		$id = !empty($this->id) ? 'id="' . $this->id . '"' : "";

		$output = <<<EOD
				$output
EOD;
		
		if(!$this->ommitParagraph)
		{
			$output = <<<EOD
				<p>
					<label>$this->label</label>
					$output
				</p>

EOD;
		}

		return $output;
	}
}
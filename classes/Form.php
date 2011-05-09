<?php
class Form extends Base
{
	/**
	 * @var array A list of form controls
	 */
	protected $controls;
	/**
	 * @var string The forms action
	 */
	private $action;
	/**
	 * @var string The method the will use to send the data.
	 */
	private $method;
	/**
	 * @var string The form id.
	 */
	private $id;
	/**
	 * @var string How data will be encoded.
	 */
	private $enctype;
	/**
	 * @var Hashtable How data will be encoded.
	 */
	private $fieldsets;

	function __construct()
	{
		$this->controls = array();
		$this->method = "get";
		$this->action = null;
		$this->id = "bformd";
		$this->fieldsets = new Hashtable();
	}

	/**
	 * Alias of generate()
	 * @return string The form HTML.
	 */
	function __toString()
	{
		$return = $this->generate();
		return $return;
	}

	/**
	 * Sets the action of the form.
	 * @param $a The action
	 * @return void
	 */
	function setAction($a)
	{
		$this->action = $a;
	}
	
	/**
	 * Sets the method of the form.
	 * @param $am The method
	 * @return void
	 */
	function setMethod($m)
	{
		$this->method = $m;
	}
	
	/**
	 * Sets the id for the form.
	 * @param $id The form's id
	 * @return void
	 */
	function setId($id)
	{
		$this->id = $id;
	}
	
	/**
	 * Sets the enctype of the form.
	 * @param $enctype The form's enctype
	 * @return void
	 */
	function setEnctype($enctype)
	{
		$this->enctype = $enctype;
	}


	/**
	 * Generates the HTML code for the form.
	 * @return string The form in HTML.
	 */
	function generate()
	{
		$output = "";
		$this->check_duplicate_ctrl_names();
		$added_controls = array();
		
		if($this->fieldsets->count() > 0)
		{
			for($iterator = $this->fieldsets->getIterator(); $iterator->valid(); $iterator->next())
			{
				$controls = $this->parseFormControlNames();
				$output .= '<fieldset>';
				$output .= '<legend>' . $iterator->key() . '</legend>';
				
				foreach($iterator->current() as $fldset_fld)
				{
					$fldset_fld = $this->parse_name($fldset_fld);
					foreach($controls as $frm_ctrl)
					{
						if($frm_ctrl->getName() == $fldset_fld)
						{
							$output .= $frm_ctrl;
							array_push($added_controls, $fldset_fld);
						}
					}
				}
				$output .= '</fieldset>';
			}
		}
		
		$controls = $this->parseFormControlNames();
		foreach($controls as $ctrl)
		{
			if(!in_array($ctrl->getName(), $added_controls))
			{
				$output .= $ctrl;
				array_push($added_controls, $ctrl->getName());
			}
		}
		
		$action = !empty($this->action) ? 'action="' . htmlentities($this->action) . '"' : "";
		$method = !empty($this->method) ? 'method="' . $this->method . '"' : "";
		$enctype = !empty($this->enctype) ? 'enctype="' . $this->enctype . '"' : "";
		$id = !empty($this->id) ? 'id="' . $this->id . '"' : "";

		$output = <<<EOD
			<form $id $action $method $enctype>
				$output
			</form>
EOD;

		return $output;
	}
	
	private function check_duplicate_ctrl_names()
	{
		$taken = array();
		$return = true;
		foreach($this->controls as $ctrl)
		{
			if(in_array($ctrl->getName(), $taken))
			{
				trigger_error("Control name " . $ctrl->getName() . " appears twice in this form");
				$return = false;
			}
			else
			{
				array_push($taken, $ctrl->getName());
			}
		}
		
		return $return;
	}

	/**
	 * Add controls to the form
	 * @param FormControl A variable list of form controls
	 * @return void
	 */
	public function addControls()
	{
		$array_push_args = func_get_args();

		//Loop through the controls to be added.
		foreach($array_push_args as $arg)
		{
			array_push($this->controls, $arg);
		}
	}

	/**
	 * Add controls to the form
	 * @param ctlrFormControl A variable list of form controls
	 * @return void
	 */
	public function addControl($ctrl)
	{
		$this->addControls($ctrl);
	}
	
	/**
	 * Parses the form's controls' names and returns the controls with
	 * the new names
	 * 
	 * @return array The form controls with the formatted names.
	 */
	private function parseFormControlNames()
	{
		$return = array();
//		$controls = clone $this->controls;
		
		//Loop through the controls to be added.
		foreach($this->controls as $arg)
		{
			//Get the control name
			$arg = clone $arg;
			
			$argName = $arg->getName();
			//If the control has a name
			if(!empty($argName) )
			{
				//Convert all names that are arrays and add the extra dimension
				//  that is added to encapsulate all form controls
				$argName = $this->parse_name($argName);
			}
			
			$arg->setName($argName);
			array_push($return, $arg);
		}

		return $return;
	}
	
	private function parse_name($name)
	{
		$name = explode("[", $name, 2);
		$name = $this->id."[".$name[0]."]" . (isset($name[1]) ? "[".$name[1] : "");
		return $name;
	}
	
	public function assign_to_fieldset($fielset, $fields)
	{
		if(!is_array($fields))
		{
			trigger_error("\$fields must be an array");
		}
		if(!is_string($fielset))
		{
			trigger_error("\$fielset must be an string");
		}
		foreach($fields as $fld)
		{
			if(is_null($this->get_control($fld)))
			{
				trigger_error("Field with name '" . $fld . "' does not exist in the form.");
			}
		}
		
		$existing_fields = $this->fieldsets->get($fielset, array());

		$result = array_merge($existing_fields, $fields);
		$result = array_unique($result);
		
		$this->fieldsets->set($fielset, $result);
	}
	
	public function get_control($field_name)
	{
		foreach($this->controls as $frm_ctrl)
		{
			if($frm_ctrl->getName() == $field_name)
			{
				return $frm_ctrl;
			}
		}
		
		return null;
	}
}


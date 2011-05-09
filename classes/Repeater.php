<?php
class Repeater extends Base
{
	/**
	 * A two dimentional array containing table data. All the second dimention
	 * lengths (count($data[0])) should be the same as the number of headings
	 * in $headings.
	 */
	protected $data;
	/**
	 * The format the data will take. This should be a formate parsable
	 * using sprintf(). The format string can also accept field names specified
	 * in the fields array list.
	 */
	private $format;
	/**
	 * The names of the fields in the data. These can be used in format
	 * string.
	 */
	private $fields;
	/**
	 * Text to be printed before repeater body.
	 */
	private $header;
	/**
	 * Text to be printed after repeater body.
	 */
	private $footer;
	/**
	 * The format of each record. This format will be applied after the
	 * user-set format.
	 */
	protected $recordEnvelope;
	/**
	 * @var string The field name by which to group records.
	 */
	private $groupBy;
	/**
	 * @var string Text inserted before the beginning of the group
	 */
	private $groupHeader;
	/**
	 * @var string Text inserted after a group.
	 */
	private $groupFooter;
	/**
	 * @var array A list of fields that should have aggregates calculated for
	 * them and the type of aggregate (ie. sum, count, avarage,...)
	 * to be applied
	 */
	private $aggregates;
	/**
	 * @var array An containing respective formats for each record.
	 */
	private $perRecordFormats;

	/**
	 * Initialises the Repeater and set defaults.
	 */
	public function __construct()
	{
		$this->data = array();
		$this->format = '%s';
		$this->fields = array();
		$this->recordEnvelope = "%s";
		$this->groupHeader = "%s";
		$this->groupFooter = "%s";
		$this->aggregates = array();
		$this->perRecordFormats = array();
	}

	/**
	 * Calls @see $this->generate() and returns the generated Repeater.
	 * @return string The generated element.
	 */
	public function __toString()
	{
		return $this->generate();
	}

	/**
	 * Restricts the setting of data members that have not been declared. Gets
	 * called automatically when this happens.
	 * @param $member Name of the data member.
	 * @param $value Value to be set.
	 */
	public function __set($member, $value)
	{
		trigger_error("'".get_class($this)."' does not contain data member '$member'.", E_USER_WARNING);
	}

	/**
	 * Sets the data for the repeater on condition it is a properly formatted
	 * array
	 * @param array A two dimentional array containing the the date to be
	 * 		display.
	 */
	public function setData($data)
	{
		//Make sure the data is an array
		if(!is_array($data))
		{
			throw new Exception("Passed data varible should be an array, type '" . gettype($data) . "' passed instead.");
		}

		//If the second dimension of $data is not arrays, convert it to
		//  to single element arrays
		foreach ($data as $dataId => $dataRow)
		{
			if(is_object($data[$dataId]) && get_parent_class($data[$dataId])=== "HTMLElementRepeater")
			{
//				$dataRow = $data[$dataId]->getData();
//				foreach($dataRow as $dataCellId => $dataCell)
//				{
//					$dataRow[$dataCellId] = implode("", $dataCell);
//				}
			}
			else
			{
				$dataRow = !is_array($dataRow) ? array($dataRow) : $dataRow;

				//No text indexes allowed for data.
				foreach($dataRow as $cellId => $cellText)
				{
					if(!is_numeric($cellId))
					{
						throw new Exception("Only integer indexes allowed in data array. Row '" . $rowId . "' has a field with index: '" . $cellId . "'.");
					}
				}
			}
			$data[$dataId] = $dataRow;
		}

		$this->data = $data;
	}

	/**
	 * Checks if the passed value is an array and sets the fields. These can be
	 * used instead of the numeric ids used in format strings.
	 * @param array An array of headings.
	 */
	public function setFields($fields)
	{
		if(!is_array($fields))
		{
			throw new Exception("Passed fields varible should be an array, type '" . gettype($fields) . "' passed instead.");
		}
		$this->fields = $fields;
	}
	/**
	 * Sets the display format.
	 */
	public function setFormat($f)
	{
		$this->format = $f;
	}
	/**
	 * Set the header.
	 * @param $h The header.
	 */
	public function setHeader($h)
	{
		$this->header = $h;
	}
	/**
	 * Set the footer.
	 * @param $h The footer.
	 */
	public function setFooter($f)
	{
		$this->footer = $f;
	}
	public function setGroupBy($g)
	{
		$this->groupBy = $g;
	}
	public function setAggregates($a)
	{
		//Throw error if the arg is not an array.
		if(!is_array($a))
		{
			trigger_error("Aggregates value give not an array.", E_USER_WARNING);
		}

		//Make sure it is a multi-dimentional array.
		if(is_array($a) && (!isset($a[0]) || !is_array($a[0])))
		{
			$a = array($a);
		}
		$this->aggregates = $a;
	}
	/**
	 * Set the format of the group header.
	 * @param $h string The header format.
	 */
	public function setGroupHeader($h)
	{
		$this->groupHeader = $h;
	}
	/**
	 * Set the format of the group footer.
	 * @param $h string The footer format.
	 */
	public function setGroupFooter($f)
	{
		$this->groupFooter = $f;
	}
	/**
	 * Set the per-record formats.
	 * @param $f array Per-record format list.
	 * @return void
	 */
	public function setPerRecordFormats($f)
	{
		$this->perRecordFormats = $f;
	}

	/**
	 * Determines the field ids of the fields in the aggregates list.
	 * @return array A list of the field ids.
	 */
	public function getAggregateFieldIds()
	{
		$aggrFldIds = array();
		foreach ($this->aggregates as $aggrFld)
		{
			//Get the field name
			$aggrFld = array_keys($aggrFld);
			$aggrFld = implode('', $aggrFld);

			//Get the field id
			$aggrFld = $this->getFieldId($aggrFld);
			array_push($aggrFldIds, $aggrFld);
		}

		return $aggrFldIds;
	}

	/**
	 * Get the index of the field with the given name.
	 * @param $fldName
	 */
	private function getFieldId($fldName)
	{
		return array_search($fldName, $this->fields)===false ? null : array_search($fldName, $this->fields);
	}
	/**
	 * Returns the formatted record text.
	 * @param $recId The record id.
	 * @return string The record text.
	 */
	public function getRecordText($recId)
	{
		//Put the data as it would be passed into sprintf into an array
		$formattedParams = $this->data[$recId];

		//Add the format at the beginning of the parameters.
		if(is_array($formattedParams))
		{
			array_unshift($formattedParams, $this->parseFormat());

			//Call sprintf() with the combined params.
			$rowText = call_user_func_array("sprintf", $formattedParams);
		}
		else
		{
			$rowText = $formattedParams->generate();
		}

		return $rowText;
	}
	/**
	 * Returns the array containin data.
	 * @return array Data array.
	 */
	public function getData()
	{
		return $this->data;
	}


	/*
	 * Checks the format string and replaces the field names with indexes.
	 * Generates a warning if the a field name included in the format string
	 * is not found in the field array list.
	 */
	private function parseFormat()
	{
		if(!is_string($this->format))
		{
			trigger_error("Format is type: '" . gettype($this->format) . "' where a 'string' is expected.", E_USER_WARNING);
		}

		$format = $this->format;
		foreach ($this->fields as $fld)
		{
			$format = str_replace($fld, $this->getFieldId($fld), $this->format);
		}

		//Find unmatched field names
		$unmatched = array();
		preg_match_all("/{{\w+?}}/", $format, $unmatched);

		//If there are unmatched field names found.
		if(!empty($unmatched) && !empty($unmatched[0]))
		{
			//Extract the details from the regulare expression match.
//			foreach($unmatched as $key => $match)
//			{
//				$unmatched[$key] = implode("", $match);
//			}
			$unmatched = implode(", ", $unmatched[0]);
			$unmatched = trim($unmatched, ", ");

			trigger_error("The following field placeholders are not valid field names : ".$unmatched.".", E_USER_WARNING);
		}
		return $format;
	}

	/*
	 * Generate the Repeater using $data and $fomart.
	 * @return string Returns the generated Repeater.
	 */
	public function generate()
	{
		$return = array();
		$this->determineGroups();
		$aggregates = $this->calcAggregates();
		$aggregates = is_null($aggregates) ? array() : $aggregates;
		$groups = $this->determineGroups();
		$groups = is_null($groups) ? array() : $groups;

		if(!empty($groups) && !empty($aggregates))
		{
			$aggregates = array_combine($groups, $aggregates);
		}


////		if(empty($this->perRecordFormats))
//		{
//			foreach ($this->perRecordFormats as $dataId => $format)
//			{
//				$rowText = $this->getRecordText($dataId);
//				$return .= $rowText;
//
//				if(empty($this->perRecordFormats))
//				{
//
//				}
//				else
//				{
//
//				}
//
//				if(array_key_exists($dataId, $aggregates))
//				{
//					$return .= "<td>" . implode("</td><td>", $aggregates[$dataId]) . "</td>";
//				}
//			}
//		}
////		else
//		{
////			foreach ($this->data as $dataId => $dataRecord)
////			{
////				$rowText = $this->getRecordText($dataId);
////				$return .= $rowText;
////			}
//		}

		//Collect record text.
		foreach ($this->data as $dataId => $dataRecord)
		{
			$rowText = $this->getRecordText($dataId);

			if(!empty($this->perRecordFormats) && array_key_exists($dataId, $this->perRecordFormats))
			{
				array_push($return, $rowText);
			}
			else if(!empty($this->perRecordFormats))
			{
			}
			else
			{
				array_push($return, $rowText);
//				$return .= $rowText;
			}

			if(array_key_exists($dataId, $aggregates))
			{
//				array_push($return, "<tr><td>" . implode("</td><td>", $aggregates[$dataId]) . "</td></tr>");
//				$return .= "<td>" . implode("</td><td>", $aggregates[$dataId]) . "</td>";
			}
		}

		if(!empty($aggregates))
		{
			foreach($aggregates as $aggrId => $aggr)
			{
//				array_insert($return, "<tr><td>" . implode("</td><td>", $aggr) . "</td></tr>", $aggrId);
//				array_insert($return, $aggregates, null);
//				array_push($return, implode("</td><td>", $aggr));
//				$aggr = "<tr><td>" . implode("</td><td>", $aggr) . "</td></tr>";
				$aggregates[$aggrId] = "<tr><td>" . implode("</td><td>", $aggr) . "</td></tr>";
			}

			array_insert($return, $aggregates, null);

		}

		//Apply recordEnvelope format to each record.
		foreach($return as &$record)
		{
			$record = sprintf($this->recordEnvelope, $record);
		}

		array_unshift($return, $this->header);
		array_push($return, $this->footer);

		$return = implode("", $return);

		return $return;
	}

	/**
	 * Determine which records are grouped together in terms of . Indicates this with the last item's record id for each group.
	 * @return array A list of records where each group ends.0734994527
	 */
	public function determineGroups()
	{
		//If group by is set.
		if(!is_null($this->groupBy))
		{
			//If the group by field is not defined in fields.
			if(array_search($this->groupBy, $this->fields) === false)
			{
				trigger_error("The specified group by field, '$this->groupBy' is not defined (using setFields()) for this object.", E_USER_WARNING);
				return null;
			}

			$groupFieldId = $this->getFieldId($this->groupBy);
			$return = array();

			//Loop through all the records.
			foreach($this->data as $recordId => $record)
			{
				//If this is an array of Repeater objects, get the data from each
				//  Repeater.
				if(isset($record) && get_parent_class($record) === "HTMLElementRepeater")
				{
					$record = $record->getData();
				}
				//If this is the first loop record the initial value of the
				//  group field.
				if(!isset($currentGroupByValue))
				{
					$currentGroupByValue = $record[$groupFieldId];
				}
				//If the value in the group field has changed, save the position
				//  as a group boundary and record the new value.
				if($record[$groupFieldId] !== $currentGroupByValue)
				{
					$currentGroupByValue = $record[$groupFieldId];
					array_push($return, $recordId-1);
				}
			}
			//The last group.
			array_push($return, $recordId);

			return $return;
		}
		else
		{
			return null;
		}
	}

	private function calcAggregates()
	{
		$aggr = array();
		$groups = $this->determineGroups();
		if(!is_null($groups))
		{
			foreach ($groups as $grpId => $grp)
			{
				array_push($aggr, $this->calcGroupAggregates($grpId));
			}
		}
		else
		{
			$aggr = null;
		}
		return $aggr;
	}

	public function calcGroupAggregates($grpId)
	{
		$groups = $this->determineGroups();
		$groupValues = array();
		$aggregates = array();
		$groupById = $this->getFieldId($this->groupBy);
		$aggrFldIds = $this->getAggregateFieldIds();

		//Determine where the group starts using the previous group's ending
		//  point or 0 if this is the first group.
		$prevGroupLoc = $grpId>0 ? $groups[$grpId-1]+1 : 0;

		//Loop through the ids for the group.
		$entry = array();
		for($i=$prevGroupLoc; $i<=$groups[$grpId]; $i++)
		{
			//If this is an array of Repeater objects, get the data from each
			//  Repeater.
			$record = $this->data[$i];
			if(isset($this->data[$i]) && get_parent_class($this->data[$i]) === "HTMLElementRepeater")
			{
				$record = $this->data[$i]->getData();
			}

//			array_push($groupValues, $this->getRecordText($i));
			foreach($aggrFldIds as $aggrFldId)
			{
				$entry[$aggrFldId][] = is_array($record[$aggrFldId]) ? $record[$aggrFldId][0] : $record[$aggrFldId];
			}
//			array_push($aggregates, $entry);
		}

		$aggregates = array();
		foreach ($this->aggregates as $aggrId => $aggr)
		{
			$aggrType = (int)implode("", array_values($aggr));
			array_push($aggregates, MathsAggregate::apply($entry[$aggrId], $aggrType));
		}

		return $aggregates;
	}
}
?>
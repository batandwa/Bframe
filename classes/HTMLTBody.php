<?php
class HTMLTBody extends HTMLElementRepeater
{
	/**
	 * Init list
	 */
	public function __construct()
	{
		parent::__construct();
//		$this->type = self::HTML_LIST_ORDERED;
		$this->tag = "tbody";
//		$this->recordEnvelope = "<th>%s</th>";
	}

	public function setData($data)
	{
		//If data is an array of arrays (two-dimensional array) as opposed to
		//  to an array of TRow objects, convert it to an array of TRow objects.
		foreach ($data as $recId => $record)
		{
			if(!$record instanceof HTMLTRow && is_array($record))
			{
				//Convert $record to HTMLRow puttings its data into an HTMLRow
				//  object.
				$new_rec = new HTMLTRow();
				$new_rec->setData($record);
				$record = $new_rec;
				$data[$recId] = $record;
			}
			else if(!is_array($record))
			{
				trigger_error("Data not two-dimensional array.", E_USER_WARNING);
			}

		}

		parent::setData($data);
	}

	public function setFormat($f)
	{
		foreach($this->data as $rowId => $row)
		{
//			$this->data[$rowId]->setFormat($f);
			$this->data[$rowId]->setPerRecordFormats($f);
		}
	}

	public function setRowAttributes($attr)
	{
		foreach($this->data as $rowId => $row)
		{
//				$this->data[$rowId][0]->setAttributes($attr);
		}
	}
}
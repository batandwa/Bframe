<?php
class MySQLRecordSet extends Queue 
{
	public  function __construct($data)
	{
		$this->elements = $data;
	}

	/**
	 * Returns the data in the MySQLRecordSet
	 *
	 * @return array
	 */
	public function get_data()
	{
		return $this->get_elements();
	}
}

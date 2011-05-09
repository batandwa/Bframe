<?php

class Quote extends MySQLTable{

	protected $dao;
	protected $i = 1;
	private $quoteId  = 0;
	private $customerId;
	private $quoteDescription;
	private $quoteLastModified;
	private $quoteStatus;
	private $quoteCreationDateTime;

	/**
	 * Retrieves the value from the field QuoteId
	 * @return String with the value of the field
	 */
	public function getQuoteId(){
		return $this->quoteId;
	}
	/**
	 * Retrieves the value from the field CustomerId
	 * @return String with the value of the field
	 */
	public function getCustomerId(){
		return $this->customerId;
	}
	/**
	 * Retrieves the value from the field QuoteDescription
	 * @return String with the value of the field
	 */
	public function getQuoteDescription(){
		return $this->quoteDescription;
	}
	/**
	 * Retrieves the value from the field QuoteLastModified
	 * @return String with the value of the field
	 */
	public function getQuoteLastModified(){
		return $this->quoteLastModified;
	}
	/**
	 * Retrieves the value from the field QuoteStatus
	 * @return String with the value of the field
	 */
	public function getQuoteStatus(){
		return $this->quoteStatus;
	}
	/**
	 * Retrieves the value from the field QuoteCreationDateTime
	 * @return String with the value of the field
	 */
	public function getQuoteCreationDateTime(){
		return $this->quoteCreationDateTime;
	}
	/**
	 * Set the value from the field QuoteId
	 * @param quoteId String with the value for the field
	 */
	public function setQuoteId($quoteId){
		$this->quoteId = $quoteId;
	}
	/**
	 * Set the value from the field CustomerId
	 * @param customerId String with the value for the field
	 */
	public function setCustomerId($customerId){
		$this->customerId = $customerId;
	}
	/**
	 * Set the value from the field QuoteDescription
	 * @param quoteDescription String with the value for the field
	 */
	public function setQuoteDescription($quoteDescription){
		$this->quoteDescription = $quoteDescription;
	}
	/**
	 * Set the value from the field QuoteLastModified
	 * @param quoteLastModified String with the value for the field
	 */
	public function setQuoteLastModified($quoteLastModified){
		$this->quoteLastModified = $quoteLastModified;
	}
	/**
	 * Set the value from the field QuoteStatus
	 * @param quoteStatus String with the value for the field
	 */
	public function setQuoteStatus($quoteStatus){
		$this->quoteStatus = $quoteStatus;
	}
	/**
	 * Set the value from the field QuoteCreationDateTime
	 * @param quoteCreationDateTime String with the value for the field
	 */
	public function setQuoteCreationDateTime($quoteCreationDateTime){
		$this->quoteCreationDateTime = $quoteCreationDateTime;
	}
	/**
	 * Default constructor
	 * @param id Unique value to identify the Quote.
	 */
	function __construct($id){

		$this->whereClause = "`QuoteId`='$id'";
		$this->tableName = "quote";
		parent::__construct($id);

	}

	function getData(){

		$list = array("QuoteId"=>$this->quoteId, "CustomerId"=>$this->customerId, "QuoteDescription"=>$this->quoteDescription, "QuoteLastModified"=>$this->quoteLastModified, "QuoteStatus"=>$this->quoteStatus, "QuoteCreationDateTime"=>$this->quoteCreationDateTime);
		return $list;

	}
	/**
	 * Initialize the business object with data read from the DB.
	 * @param row array containing one read record.
	 */
	protected function init($row){
		$this->quoteId = $row['QuoteId'];
		$this->customerId = $row['CustomerId'];
		$this->quoteDescription = $row['QuoteDescription'];
		$this->quoteLastModified = $row['QuoteLastModified'];
		$this->quoteStatus = $row['QuoteStatus'];
		$this->quoteCreationDateTime = $row['QuoteCreationDateTime'];
	}
	/**
	 *
	 * Load all records of Quote uniquely by its foreign keys:
	 * CustomerId
	 * @param customerId foreign key
	 * @return array of Instance of {@link Quote}
	 */
	public static function loadByFKCustomerid($customerId){

		$quote = new Quote();
		$rows = $quote->dao->getData($this->whereClause);
		$quotes = array();
		for ($index = 0; $index < count($rows); $index++) {
			$d = new Quote();
			$d->init($rows[$index]);
			array_push($quotes, $d);
		}
		return $quotes;

	}
}
?>
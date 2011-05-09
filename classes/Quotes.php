<?php

class Quote {

private $dao;
private $i = 1;
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

$this->dao = new MySQLDAO("quote");
if(!is_null($id))
{
	$this->loadUnique($id);
}
		
}
/**
 * Initialize the business object with data read from the DB.
 * @param row array containing one read record.
 */
private function init($row){
  $this->quoteId = $row['QuoteId'];
  $this->customerId = $row['CustomerId'];
  $this->quoteDescription = $row['QuoteDescription'];
  $this->quoteLastModified = $row['QuoteLastModified'];
  $this->quoteStatus = $row['QuoteStatus'];
  $this->quoteCreationDateTime = $row['QuoteCreationDateTime'];
}
/**
 * Returns the string representation of this obbject
 * @return String repesentation ofQuote
 */
public function toString(){
  $s = '';
  $s .= "\nQuoteId: ".$this->quoteId;
  $s .= "\nCustomerId: ".$this->customerId;
  $s .= "\nQuoteDescription: ".$this->quoteDescription;
  $s .= "\nQuoteLastModified: ".$this->quoteLastModified;
  $s .= "\nQuoteStatus: ".$this->quoteStatus;
  $s .= "\nQuoteCreationDateTime: ".$this->quoteCreationDateTime;
  return $s;
}

public static function loadAll(){

$dao = new MySQLDAO("quote");
$rows = $dao->getData("");
$quotes = array();

for ($i = 0; $i < count($rows); $i++)
{
	$d = new Quote(null);
	$d->init($rows[$i]);
	array_push($quotes, $d);
}
return $quotes;
		
}
/**
 * 
 * Load the Quote uniquely by its primary key.
 * @param quoteId primary key
 * @return Instance of {@link Quote}
 */
private function loadUnique($quoteId){

$rows = $this->dao->getData("`QuoteId`='$quoteId'");
$this->init($rows[0]);
  	  	
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
$rows = $quote->dao->getData("`CustomerId`='$customerId'");
$quotes = array();
for ($index = 0; $index < count($rows); $index++) {
	$d = new Quote();
	$d->init($rows[$index]);
	array_push($quotes, $d);
}
return $quotes;
 	  	
}
/**
 * Insert this object into the DB
 * @return new id (auto increment value) genereated
 */
public function insert(){

$list = array("QuoteId"=>$this->quoteId, "CustomerId"=>$this->customerId, "QuoteDescription"=>$this->quoteDescription, "QuoteLastModified"=>$this->quoteLastModified, "QuoteStatus"=>$this->quoteStatus, "QuoteCreationDateTime"=>$this->quoteCreationDateTime);
return $this->dao->insertRecord($list);	
	
}
/**
 * Update this object into the DB
 * @return number of updated records
 */
public function update(){

$list = array("QuoteId"=>$this->quoteId, "CustomerId"=>$this->customerId, "QuoteDescription"=>$this->quoteDescription, "QuoteLastModified"=>$this->quoteLastModified, "QuoteStatus"=>$this->quoteStatus, "QuoteCreationDateTime"=>$this->quoteCreationDateTime);
$where = "`QuoteId`='$quoteId'";
return $this->dao->updateRecord($list, $where);	
		
}
}
?>
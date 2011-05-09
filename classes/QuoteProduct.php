<?php

class QuoteProduct {

private $dao;
private $i = 1;
private $quoteId  = 0;
private $productId  = 0;
private $productsModel;
private $productsName;
private $quoteProductPrice;
private $finalPrice;
private $productsTax;
private $quoteProductQuantity;
private $onetimeCharges;
private $productsPricedByAttribute;
private $productIsFree;
private $productsDiscountType;
private $productsDiscountTypeFrom;
private $productsPrid;

/**
 * Retrieves the value from the field QuoteId
 * @return String with the value of the field
 */
public function getQuoteId(){
  return $this->quoteId;
}
/**
 * Retrieves the value from the field ProductId
 * @return String with the value of the field
 */
public function getProductId(){
  return $this->productId;
}
/**
 * Retrieves the value from the field products_model
 * @return String with the value of the field
 */
public function getProductsModel(){
  return $this->productsModel;
}
/**
 * Retrieves the value from the field products_name
 * @return String with the value of the field
 */
public function getProductsName(){
  return $this->productsName;
}
/**
 * Retrieves the value from the field QuoteProductPrice
 * @return String with the value of the field
 */
public function getQuoteProductPrice(){
  return $this->quoteProductPrice;
}
/**
 * Retrieves the value from the field final_price
 * @return String with the value of the field
 */
public function getFinalPrice(){
  return $this->finalPrice;
}
/**
 * Retrieves the value from the field products_tax
 * @return String with the value of the field
 */
public function getProductsTax(){
  return $this->productsTax;
}
/**
 * Retrieves the value from the field QuoteProductQuantity
 * @return String with the value of the field
 */
public function getQuoteProductQuantity(){
  return $this->quoteProductQuantity;
}
/**
 * Retrieves the value from the field onetime_charges
 * @return String with the value of the field
 */
public function getOnetimeCharges(){
  return $this->onetimeCharges;
}
/**
 * Retrieves the value from the field products_priced_by_attribute
 * @return String with the value of the field
 */
public function getProductsPricedByAttribute(){
  return $this->productsPricedByAttribute;
}
/**
 * Retrieves the value from the field product_is_free
 * @return String with the value of the field
 */
public function getProductIsFree(){
  return $this->productIsFree;
}
/**
 * Retrieves the value from the field products_discount_type
 * @return String with the value of the field
 */
public function getProductsDiscountType(){
  return $this->productsDiscountType;
}
/**
 * Retrieves the value from the field products_discount_type_from
 * @return String with the value of the field
 */
public function getProductsDiscountTypeFrom(){
  return $this->productsDiscountTypeFrom;
}
/**
 * Retrieves the value from the field products_prid
 * @return String with the value of the field
 */
public function getProductsPrid(){
  return $this->productsPrid;
}
/**
 * Set the value from the field QuoteId
 * @param quoteId String with the value for the field
 */
public function setQuoteId($quoteId){
  $this->quoteId = $quoteId;
}
/**
 * Set the value from the field ProductId
 * @param productId String with the value for the field
 */
public function setProductId($productId){
  $this->productId = $productId;
}
/**
 * Set the value from the field products_model
 * @param productsModel String with the value for the field
 */
public function setProductsModel($productsModel){
  $this->productsModel = $productsModel;
}
/**
 * Set the value from the field products_name
 * @param productsName String with the value for the field
 */
public function setProductsName($productsName){
  $this->productsName = $productsName;
}
/**
 * Set the value from the field QuoteProductPrice
 * @param quoteProductPrice String with the value for the field
 */
public function setQuoteProductPrice($quoteProductPrice){
  $this->quoteProductPrice = $quoteProductPrice;
}
/**
 * Set the value from the field final_price
 * @param finalPrice String with the value for the field
 */
public function setFinalPrice($finalPrice){
  $this->finalPrice = $finalPrice;
}
/**
 * Set the value from the field products_tax
 * @param productsTax String with the value for the field
 */
public function setProductsTax($productsTax){
  $this->productsTax = $productsTax;
}
/**
 * Set the value from the field QuoteProductQuantity
 * @param quoteProductQuantity String with the value for the field
 */
public function setQuoteProductQuantity($quoteProductQuantity){
  $this->quoteProductQuantity = $quoteProductQuantity;
}
/**
 * Set the value from the field onetime_charges
 * @param onetimeCharges String with the value for the field
 */
public function setOnetimeCharges($onetimeCharges){
  $this->onetimeCharges = $onetimeCharges;
}
/**
 * Set the value from the field products_priced_by_attribute
 * @param productsPricedByAttribute String with the value for the field
 */
public function setProductsPricedByAttribute($productsPricedByAttribute){
  $this->productsPricedByAttribute = $productsPricedByAttribute;
}
/**
 * Set the value from the field product_is_free
 * @param productIsFree String with the value for the field
 */
public function setProductIsFree($productIsFree){
  $this->productIsFree = $productIsFree;
}
/**
 * Set the value from the field products_discount_type
 * @param productsDiscountType String with the value for the field
 */
public function setProductsDiscountType($productsDiscountType){
  $this->productsDiscountType = $productsDiscountType;
}
/**
 * Set the value from the field products_discount_type_from
 * @param productsDiscountTypeFrom String with the value for the field
 */
public function setProductsDiscountTypeFrom($productsDiscountTypeFrom){
  $this->productsDiscountTypeFrom = $productsDiscountTypeFrom;
}
/**
 * Set the value from the field products_prid
 * @param productsPrid String with the value for the field
 */
public function setProductsPrid($productsPrid){
  $this->productsPrid = $productsPrid;
}
/**
 * Default constructor
 * @param id Unique value to identify the QuoteProduct.
 */
 function __construct($id){

$this->dao = new MySQLDAO("quote_product");
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
  $this->productId = $row['ProductId'];
  $this->productsModel = $row['products_model'];
  $this->productsName = $row['products_name'];
  $this->quoteProductPrice = $row['QuoteProductPrice'];
  $this->finalPrice = $row['final_price'];
  $this->productsTax = $row['products_tax'];
  $this->quoteProductQuantity = $row['QuoteProductQuantity'];
  $this->onetimeCharges = $row['onetime_charges'];
  $this->productsPricedByAttribute = $row['products_priced_by_attribute'];
  $this->productIsFree = $row['product_is_free'];
  $this->productsDiscountType = $row['products_discount_type'];
  $this->productsDiscountTypeFrom = $row['products_discount_type_from'];
  $this->productsPrid = $row['products_prid'];
}
/**
 * Returns the string representation of this obbject
 * @return String repesentation ofQuoteProduct
 */
public function toString(){
  $s = '';
  $s .= 'QuoteId: '.$this->quoteId;
  $s .= 'ProductId: '.$this->productId;
  $s .= 'products_model: '.$this->productsModel;
  $s .= 'products_name: '.$this->productsName;
  $s .= 'QuoteProductPrice: '.$this->quoteProductPrice;
  $s .= 'final_price: '.$this->finalPrice;
  $s .= 'products_tax: '.$this->productsTax;
  $s .= 'QuoteProductQuantity: '.$this->quoteProductQuantity;
  $s .= 'onetime_charges: '.$this->onetimeCharges;
  $s .= 'products_priced_by_attribute: '.$this->productsPricedByAttribute;
  $s .= 'product_is_free: '.$this->productIsFree;
  $s .= 'products_discount_type: '.$this->productsDiscountType;
  $s .= 'products_discount_type_from: '.$this->productsDiscountTypeFrom;
  $s .= 'products_prid: '.$this->productsPrid;
  return $s;
}

public function loadAll(){

$quoteproduct = new QuoteProduct();
$rows = $quoteproduct->dao->getData("");
$quoteproducts = array();
for ($index = 0; $index < sizeof($rows); $index++)
{
	$d = new QuoteProduct();
	$d->init($rows[$index]);
	array_push($quoteproducts,$d);
}
return $quoteproducts;
		
}
/**
 * 
 * Load the QuoteProduct uniquely by its primary key.
 * @param productId primary key
 * @param quoteId primary key
 * @return Instance of {@link QuoteProduct}
 */
private static function loadUnique($productId, $quoteId){

$rows = $this->dao->getData("`ProductId`='$productId' AND `QuoteId`='$quoteId'");
$this->init($rows[0]);
  	  	
}
/**
 * Insert this object into the DB
 * @return new id (auto increment value) genereated
 */
private function insert(){

$list = array("QuoteId"=>$this->quoteId, "ProductId"=>$this->productId, "products_model"=>$this->productsModel, "products_name"=>$this->productsName, "QuoteProductPrice"=>$this->quoteProductPrice, "final_price"=>$this->finalPrice, "products_tax"=>$this->productsTax, "QuoteProductQuantity"=>$this->quoteProductQuantity, "onetime_charges"=>$this->onetimeCharges, "products_priced_by_attribute"=>$this->productsPricedByAttribute, "product_is_free"=>$this->productIsFree, "products_discount_type"=>$this->productsDiscountType, "products_discount_type_from"=>$this->productsDiscountTypeFrom, "products_prid"=>$this->productsPrid);
return $this->dao->insertRecord($list);	
	
}
/**
 * Update this object into the DB
 * @return number of updated records
 */
private function update(){

$list = array("QuoteId"=>$this->quoteId, "ProductId"=>$this->productId, "products_model"=>$this->productsModel, "products_name"=>$this->productsName, "QuoteProductPrice"=>$this->quoteProductPrice, "final_price"=>$this->finalPrice, "products_tax"=>$this->productsTax, "QuoteProductQuantity"=>$this->quoteProductQuantity, "onetime_charges"=>$this->onetimeCharges, "products_priced_by_attribute"=>$this->productsPricedByAttribute, "product_is_free"=>$this->productIsFree, "products_discount_type"=>$this->productsDiscountType, "products_discount_type_from"=>$this->productsDiscountTypeFrom, "products_prid"=>$this->productsPrid);
$where = "`ProductId`='$productId' AND `QuoteId`='$quoteId'";
return $this->dao->updateRecord($list, $where);	
		
}
}
?>

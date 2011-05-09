<?php
class Customer extends MySQLTable {

	protected $dao;
	protected $i = 1;
//	protected $tableName;
	protected $whereClause;

	private $customerId  = 0;
	private $customerGender;
	private $customerFirstname;
	private $customerLastname;
	private $customerEmail;
	private $customerStatus;

	/**
	 * Retrieves the value from the field CustomerId
	 * @return String with the value of the field
	 */
	public function getCustomerId(){
		return $this->customerId;
	}
	/**
	 * Retrieves the value from the field CustomerGender
	 * @return String with the value of the field
	 */
	public function getCustomerGender(){
		return $this->customerGender;
	}
	/**
	 * Retrieves the value from the field CustomerFirstname
	 * @return String with the value of the field
	 */
	public function getCustomerFirstname(){
		return $this->customerFirstname;
	}
	/**
	 * Retrieves the value from the field CustomerLastname
	 * @return String with the value of the field
	 */
	public function getCustomerLastname(){
		return $this->customerLastname;
	}
	/**
	 * Retrieves the value from the field CustomerEmail
	 * @return String with the value of the field
	 */
	public function getCustomerEmail(){
		return $this->customerEmail;
	}
	/**
	 * Retrieves the value from the field CustomerStatus
	 * @return String with the value of the field
	 */
	public function getCustomerStatus(){
		return $this->customerStatus;
	}
	/**
	 * Set the value from the field CustomerId
	 * @param customerId String with the value for the field
	 */
	public function setCustomerId($customerId){
		$this->customerId = $customerId;
	}
	/**
	 * Set the value from the field CustomerGender
	 * @param customerGender String with the value for the field
	 */
	public function setCustomerGender($customerGender){
		$this->customerGender = $customerGender;
	}
	/**
	 * Set the value from the field CustomerFirstname
	 * @param customerFirstname String with the value for the field
	 */
	public function setCustomerFirstname($customerFirstname){
		$this->customerFirstname = $customerFirstname;
	}
	/**
	 * Set the value from the field CustomerLastname
	 * @param customerLastname String with the value for the field
	 */
	public function setCustomerLastname($customerLastname){
		$this->customerLastname = $customerLastname;
	}
	/**
	 * Set the value from the field CustomerEmail
	 * @param customerEmail String with the value for the field
	 */
	public function setCustomerEmail($customerEmail){
		$this->customerEmail = $customerEmail;
	}
	/**
	 * Set the value from the field CustomerStatus
	 * @param customerStatus String with the value for the field
	 */
	public function setCustomerStatus($customerStatus){
		$this->customerStatus = $customerStatus;
	}
	/**
	 * Default constructor
	 * @param id Unique value to identify the Customer.
	 */
	function __construct($id){

		$this->whereClause = "`CustomerId`='$id'";
		$this->tableName = "customer";
		parent::__construct($id);

	}

	function getData(){

		$list = array("CustomerId"=>$this->customerId, "CustomerGender"=>$this->customerGender, "CustomerFirstname"=>$this->customerFirstname, "CustomerLastname"=>$this->customerLastname, "CustomerEmail"=>$this->customerEmail, "CustomerStatus"=>$this->customerStatus);
		return $list;
	}

	/**
	 * (non-PHPdoc)
	 * @see www/classes/MySQLTable#init($row)
	 */
	protected function init($row){
		$this->customerId = $row['CustomerId'];
		$this->customerGender = $row['CustomerGender'];
		$this->customerFirstname = $row['CustomerFirstname'];
		$this->customerLastname = $row['CustomerLastname'];
		$this->customerEmail = $row['CustomerEmail'];
		$this->customerStatus = $row['CustomerStatus'];
	}
}
?>
<?php

error_reporting(E_ALL);

/**
 * population - class.DatabaseField.php
 *
 * $Id: DatabaseField.php,v 1.1 2009/09/11 18:36:13 Administrator Exp $
 *
 * This file is part of population.
 *
 * Automatically generated on 16.07.2009, 22:33:28 with ArgoUML PHP module 
 * (last revised $Date: 2009/09/11 18:36:13 $)
 *
 * @author Batandwa Colani, <batandwa@localhost.co.za>
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DDA-includes begin
// section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DDA-includes end

/* user defined constants */
// section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DDA-constants begin
// section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DDA-constants end

/**
 * Short description of class DatabaseField
 *
 * @access public
 * @author Batandwa Colani, <batandwa@localhost.co.za>
 */
class DatabaseField
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The name of the field.
     *
     * @access private
     * @var string
     */
    private $name = '';

    /**
     * The field data type.
     *
     * @access private
     * @var string
     */
    private $dataType = '';

    /**
     * The length of the data type.
     *
     * @access private
     * @var int
     */
    private $dataTypeLength = 0;

    /**
     * The precision of the values in thee field.
     *
     * @access private
     * @var int
     */
    private $dataTypePrecision = -1;

    /**
     * Whether or not this field can be set to null.
     *
     * @access private
     * @var boolean
     */
    private $nullable = false;

    /**
     * Short description of attribute primaryKey
     *
     * @access private
     * @var boolean
     */
    private $primaryKey = false;

    /**
     * Indicates if this field is an auto increment field or not.
     *
     * @access private
     * @var boolean
     */
    private $autoIncrement = false;

    /**
     * Indicates whether or not the field value is unsigned.
     *
     * @access private
     * @var boolean
     */
    private $unsigned = false;

    /**
     * Indicates if this field is going to be zerofilled.
     *
     * @access private
     * @var boolean
     */
    private $zerofill = false;

    /**
     * Indicates if this field if for binary data.
     *
     * @access private
     * @var boolean
     */
    private $binary = false;

    /**
     * The default value for this field.
     *
     * @access private
     * @var mixed
     */
    private $defaultValue = null;

    // --- OPERATIONS ---

    /**
     * Short description of method DatabaseField
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @param  string db The database the field's table is from.
     * @param  string table The table this field is on.
     * @param  string name The name of the field.
     * @return void
     */
    public function DatabaseField($db, $table, $name)
    {
        // section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DEF begin
		$this->name = $name;
        $this->retreiveInfo($db, $table);
        // section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DEF end
    }

    /**
     * Short description of method getName
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return string
     */
    public function getName()
    {
        $returnValue = (string) '';

        // section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DF8 begin
        $returnValue = $this->name;
        // section 127-0-0-1-4665948e:121e638230e:-8000:0000000000000DF8 end

        return (string) $returnValue;
    }

    /**
     * Returns the data type of the field.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return string
     */
    public function getDataType()
    {
        $returnValue = (string) '';

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F67 begin
        $returnValue = $this->dataType;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F67 end

        return (string) $returnValue;
    }

    /**
     * Returns the length of the field.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return int
     */
    public function getDataTypeLength()
    {
        $returnValue = (int) 0;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F6B begin
        $returnValue = $this->dataTypeLength;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F6B end

        return (int) $returnValue;
    }

    /**
     * Returns the precisionn of the data type.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return int
     */
    public function getDataTypePrecision()
    {
        $returnValue = (int) 0;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F73 begin
        $returnValue = $this->dataTypePrecision;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F73 end

        return (int) $returnValue;
    }

    /**
     * Returns a boolean indicator of whether this field is nullable.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return boolean
     */
    public function getNullable()
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F77 begin
        $returnValue = $this->nullable;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F77 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getPrimaryKey
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return boolean
     */
    public function getPrimaryKey()
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F88 begin
        $returnValue = $this->primaryKey;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F88 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method getAutoIncrement
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return boolean
     */
    public function getAutoIncrement()
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F9B begin
        $returnValue = $this->autoIncrement;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F9B end

        return (bool) $returnValue;
    }

    /**
     * Returns a value indicating whether or not the field is unsigned.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return boolean
     */
    public function getUnsigned()
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000FA3 begin
        $returnValue = $this->unsigned;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000FA3 end

        return (bool) $returnValue;
    }

    /**
     * Returns a value indicating if this field is going to be zerofilled.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return boolean
     */
    public function getZerofill()
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000FA7 begin
        $returnValue = $this->zerofill;
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000FA7 end

        return (bool) $returnValue;
    }

    /**
     * Returns a value indicating if this field if for binary data.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return boolean
     */
    public function getBinary()
    {
        $returnValue = (bool) false;

        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000FB3 begin
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000FB3 end

        return (bool) $returnValue;
    }

    /**
     * Gets and returns the default value for this field.
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @return Boolean
     */
    public function getDefaultValue()
    {
        $returnValue = null;

        // section 127-0-0-2-597cc1ff:122853c6aea:-8000:0000000000001006 begin
        // section 127-0-0-2-597cc1ff:122853c6aea:-8000:0000000000001006 end

        return $returnValue;
    }

    /**
     * Retreive information on the field and store it on the data members
     *
     * @access public
     * @author Batandwa Colani, <batandwa@localhost.co.za>
     * @param  string db The name of the database.
     * @param  string table The name of the table
     * @return void
     */
    public function retreiveInfo($db, $table)
    {
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F7B begin

        // Get the details of the current table
        $db_Table = "`".$db . '`.`'.$table.'`';
		$query = 'DESCRIBE ' . $db_Table;
		$result = mysql_query($query);
		$row = array();
		$j=0;
		if(mysql_errno() !== 0)
		{
			throw new Exception("MySQl Error " . mysql_errno() . ": Error retrieving table data for table '" . $db_Table . "'.");	
		}
		else while(($row = mysql_fetch_assoc($result)) && ($row["Field"] !== $this->name))
		{
		}
		
		if($row === false)
		{
			throw new Exception("The field '" . $this->name . "'does not exist in the table '" . $db_Table . "'.");
		}
		else
		{
			//Process the datatype and length
			$lengthOpenBracket = strpos($row["Type"], "(");
			$lengthCloseBracket = strpos($row["Type"], ")");
			$this->dataType = $row["Type"];
			$this->dataTypeLength = -1;
			if($lengthOpenBracket !== false)
			{
				$this->dataType = substr($row["Type"], 0, $lengthOpenBracket);
				$this->dataTypeLength = substr($row["Type"], $lengthOpenBracket+1, $lengthCloseBracket);
				
				$this->dataTypeLength = explode(",", $this->dataTypeLength);
				if(count($this->dataTypeLength) > 1)
				{
					$this->dataTypePrecision = (int)$this->dataTypeLength[1];
					$this->dataTypeLength = (int)$this->dataTypeLength[0];
				}
				else
				{
					$this->dataTypeLength = (int)$this->dataTypeLength[0];
				}
				//If the length has a comma and therefore a precision.
	//				if(strpos($this->dataTypeLength, ",") !== false)
	//				{
	//					$this
	//				}
			}
		
			$this->nullable = ($row["Null"] === "NO") ? false : true;
			$this->primaryKey = (strpos(strtolower($row["Key"]), "pri") === false) ? false : true;
			$this->defaultValue = ($row["Default"] === NULL) ? null : $row["Default"];
			$this->autoIncrement = (strpos(strtolower($row["Extra"]), "auto_increment") === false) ? false : true;
			$this->zerofill = (strpos(strtolower($row["Type"]), "zerofill") === false) ? false : true;
			$this->unsigned = (strpos(strtolower($row["Type"]), "unsigned") === false) ? false : true;
		}
		
        // section 127-0-0-1-77c1c7a0:121e9a09134:-8000:0000000000000F7B end
    }

} /* end of class DatabaseField */

?>
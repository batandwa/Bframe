<?php
/**
 * This Class represents an abstract
 * DataAccessObject that can be subclassed
 * for any specific data source. Although
 * the main focus lies on database as
 * data source
 * CVS $Revision: 1.1 $ / last modified at $Date: 2009/09/11 18:36:12 $ / last modified by $Author: Administrator $/ Copyright (c) SAHITS 2006. All Rights Reserved.
 */
abstract class DAO{
	
	/**
	 * Will retrieve any number of records from the database using the specified WHERE criteria. 
	 * The sql SELECT statement will be constructed automatically from variables supplied at runtime. 
	 * The result is an associative array of 'name=value' pairs, indexed by row number.
	 * @param where WHERE statement of the SQL clause
	 * @return Array of read rows
	 */
	abstract public function getData ($where);
	
	/**
	 * will insert a single row using the contents of $fieldarray, which is an associative array of 'name=value' 
	 * pairs. The sql INSERT statement will be constructed automatically from the contents of $fieldarray.
	 * @param fieldarray Array with field value pairs that should be inserted
	 */
	abstract public function insertRecord ($fieldarray);
	
	/**
	 * will update a single row using the contents of $fieldarray, which is an associative array of 'name=value' pairs. 
	 * The sql UPDATE statement will be constructed automatically from the contents of $fieldarray.
	 * @param fieldarray Array with field value pairs that should be inserted
	 * @param where WHERE statement of the SQL clause
	 */
	abstract public function updateRecord ($fieldarray,$where);
	
	/**
	 * will delete a single row using the contents of $fieldarray, which is an associative array of 'name=value' pairs. 
	 * The sql DELETE statement will be constructed automatically from the contents of $fieldarray.
	 * @param where WHERE statement of the SQL clause
	 */
	abstract public function deleteRecord ($where);
}
?>

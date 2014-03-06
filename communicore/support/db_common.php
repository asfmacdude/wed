<?php
/*
 * db_common.php
 *
 * This class will be extended by each of the db objects and
 * provide a common set of tools for each object.
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_xcrud.php');

abstract class db_common extends db_xcrud
{	
	public $record_list = null;
	
	public function __get($name)
	{
		return  (isset($this->options[$name])) ? $this->options[$name] : null;
	}
	
	public function __set($name,$value)
	{
		$this->options[$name] = $value;
	}
	
	// *******************************************************************
    // ********  addOptions to the options array *************************
    // *******************************************************************
	public function addOptions($options)
	{
		$this->options = array_merge( $this->options, $options);
	}
	
	// *******************************************************************
    // ********  moveRecordList ******************************************
    // *******************************************************************
    /*
     * This function is my iterator for SELECT results that have more than
     * one record. The search results is saved into a public array and then
     * by using a do while loop, one can step through and load the record
     * into the object and get the necessary info needed.
     */
	public function moveRecordList($item=0)
	{
		$success = false;
		
		if (isset($this->record_list[$item]))
		{
			$success = true;
			$this->addValues_Data($this->record_list[$item]);
		}
		
		return $success;
	}
	
	// *******************************************************************
    // ********  getValue gets one value from the fields list ************
    // *******************************************************************
	public function getValue($name,$default=null)
	{
		$name = strtolower($name);
		return (isset($this->options['FIELDS'][$name]['VALUE'])) ? $this->options['FIELDS'][$name]['VALUE'] : $default ;
	}
	
	// *******************************************************************
    // ****  getKey gets a particular key/value from the field list ******
    // *******************************************************************
	public function getKey($name,$key,$default=null)
	{
		$name = strtolower($name);
		return (isset($this->options['FIELDS'][$name][$key])) ? $this->options['FIELDS'][$name][$key] : $default ;
	}
	
	// *******************************************************************
    // ********  getActualFieldName gets the real field name *************
    // *******************************************************************
	public function getActualFieldName($name)
	{
		return (isset($this->options['FIELDS'][$name]['DB_FIELD'])) ? $this->options['FIELDS'][$name]['DB_FIELD'] : null;
	}
	
	// *******************************************************************
    // ***  getDateToday gets the SQL Formatted timestamp for today ******
    // *******************************************************************
	public function getDateToday()
	{
		return date("Y-m-d H:i:s");
	}
	
	// *******************************************************************
    // ********  getFormattedValue calls either a method or getValue *****
    // *******************************************************************
    public function getFormattedValue($fields,$formats=array())
    {
	    if (is_array($fields))
	    {
		    $ret_value = array();
		    
		    foreach ($fields as $name)
		    {
			    $method = 'get'.$name;
			    
			    if (method_exists($this,$method))
			    {
				    $ret_value[$name] = call_user_func(array($this,$method),$formats);
			    }
			    else
			    {
				    $ret_value[$name] = $this->getValue(strtolower($name));
			    }
		    }
	    }
	    else
	    {
		    $ret_value = null;
		    $method    = 'get'.$fields;
		    
		    if (method_exists($this,$method))
			{
				$ret_value = call_user_func(array($this,$method),$formats);
			}
			else
			{
				$ret_value = $this->getValue(strtolower($fields));
			}
	    }
	    
	    return $ret_value;
    }
	
	// *******************************************************************
    // ********  Search Functions Stubs **********************************
    // *******************************************************************
	public function runSearch($options)
    {
	    return false;
    }
	
	// *******************************************************************
    // ******** addValues_Default move the default values into VALUE *****
    // *******************************************************************
	public function addValues_Default()
    {
        foreach ($this->options['FIELDS'] as $key=>$value)
        {
            $this->options['FIELDS'][$key]['VALUE'] = (isset($this->options['FIELDS'][$key]['DEFAULT'])) ? $this->options['FIELDS'][$key]['DEFAULT'] : '' ;
        }

        return $this->options['FIELDS'];
    }
	
	// *******************************************************************
    // ** addValues_Request adds matching values in $_REQUEST to VALUE ***
    // *******************************************************************
	public function addValues_Request()
    {
        foreach ($this->options['FIELDS'] as $key=>$value)
        {
            if (isset($_REQUEST[$key]))
            {
                if (is_array($_REQUEST[$key]))
                {
                    // We use implode here to capture arrays of values from checkbox fields
                    $this->options['FIELDS'][$key]['VALUE'] = implode(',',$_REQUEST[$key]);
                }
                else
                {
                    $this->options['FIELDS'][$key]['VALUE'] = $_REQUEST[$key];
                }
            }
        }

        return $this->options['FIELDS'];
    }
    
    // *******************************************************************
    // ** addValues_Data adds values from a given data array *************
    // *******************************************************************
    public function addValues_Data($data)
    {	
    	// data array keys are actual field names
    	foreach ($this->options['FIELDS'] as $key=>$value)
    	{
    		if (isset($value['DB_FIELD']))
    		{
    			 if (isset($data[$value['DB_FIELD']]))
    			 {
	    			 $this->options['FIELDS'][$key]['VALUE'] = $data[$value['DB_FIELD']];
    			 }
    			 else
    			 {
	    			 // When there is a NULL value in the field
	    			 $this->options['FIELDS'][$key]['VALUE'] = '';
    			 }
    		}
    	}
    }
    
    // *******************************************************************
    // ** joinFields merges other fields listing into one  ***************
    // *******************************************************************
    /*
     * This function takes the $fields array from the current object and
     * merges it with fields list from other designated db objects. The db objects
     * can be found in the JOIN_TABLES array in options. This array is setup with
     * a prefix => table setup and this functions loads each table, gets the fileds list
     * and list them with a new key with the prefix attached to the name.
     *
     */
    public function joinFields($fields)
    {
	    if (isset($this->options['JOIN_TABLES']))
	    {
		    $other_fields = array();
		    
		    foreach ($this->options['JOIN_TABLES'] as $prefix=>$table)
		    {
			    $table_obj = wed_getDBObject($table);
			    $other_fields[$prefix] = $table_obj->options['FIELDS'];
		    }
		    
		    foreach ($other_fields as $prefix=>$the_fields)
		    {
			    foreach ($the_fields as $title=>$data)
			    {
				    $fields[$prefix.$title] = $data;
			    }
		    }
		    
		    return $fields;
	    }
    }
    
    // *******************************************************************
    // **** validateValues calls the validate class and validates ********
    // *******************************************************************
    public function validateValues()
    {
        $validate = new validate($this->options['FIELDS']);
        $this->options['FIELDS'] = $validate->getErrors();
    }
    
    // *******************************************************************
    // **** insertValue allows you to insert another value ***************
    // *******************************************************************
    public function insertValue($name, $value)
    {
    	if (isset($this->options['FIELDS'][$name]))
    	{
    		$this->options['FIELDS'][$name]['VALUE'] = $value;
    	}
    }
    
    // *******************************************************************
    // **** insertValuesArray allows you to insert multiple values *******
    // *******************************************************************
    public function insertValuesArray($data)
    {
    	if (is_array($data))
    	{
	    	foreach ($data as $name=>$value)
	    	{
		    	$this->insertValue($name,$value);
	    	}
    	}
    }
    
    // *******************************************************************
    // **** insertError allows you to insert an error value **************
    // *******************************************************************
    public function insertError($name,$value,$message=null)
    {
    	if (isset($this->options['FIELDS'][$name]))
    	{
    		$this->options['FIELDS'][$name]['ERROR'] = $value;
    		
    		if (!is_null($message))
    		{
    			$this->options['FIELDS'][$name]['MESSAGE'] = $message;
    		}
    	}
    }
    
    // *******************************************************************
    // **** buildDataPairs returns a SQL Format pair field=>value ********
    // *******************************************************************
    public function buildDataPairs()
    {
        $data_list = $this->options['FIELDS'];
        $data_pairs = array();

        foreach ($data_list as $key=>$value)
        {
            // Fields with a NO_UPDATE value should not be included in the pairs
            // These are fields like id fields and date modified fields
            if ( (isset($value['DB_FIELD'])) && (isset($value['VALUE'])) && (!isset($value['NO_UPDATE'])) )
            {
                if (isset($value['SAVE_ACTION']))
                {
                	$data_pairs[$value['DB_FIELD']] = $this->process_Save_Actions($value['SAVE_ACTION'], $value['VALUE']);
                }
                else
                {
                	$data_pairs[$value['DB_FIELD']] = $value['VALUE'];
                } 
            }
        }

        return $data_pairs;
    }
    
    public function buildSettingPairs()
    {
        // This differs from the buildDataPairs in that it uses the $field $key for it's key.
        // This is helpful when you want a nice associative name pairs using the common name
        // we have given for the field instead of the actual field name in the table.
        // NOTE: the keys are all caps to conform to system wide format [KEY] => value
        $data_list = $this->options['FIELDS'];
        $data_pairs = array();

        foreach ($data_list as $key=>$value)
        {
            // Fields with a NO_UPDATE value should not be included in the pairs
            // These are fields like id fields and date modified fields
            if ( (isset($value['DB_FIELD'])) && (isset($value['VALUE'])) && (!isset($value['NO_UPDATE'])) )
            {
                if (isset($value['SAVE_ACTION']))
                {
                	$data_pairs[strtoupper($key)] = $this->process_Save_Actions($value['SAVE_ACTION'], $value['VALUE']);
                }
                else
                {
                	$data_pairs[strtoupper($key)] = $value['VALUE'];
                } 
            }
        }

        return $data_pairs;
    }
    
    public function implodeForTable()
    {
    	$data_list = $this->options['FIELDS'];
    	$tmp_array = array();
    	
    	foreach ($data_list as $key=>$value)
    	{
    		// Fields with a value for NO_TABLE do not get included in sql calls
    		// for data to display in tables. Usually these are large text fields
    		// which you would not display anyway
    		if (!isset($value['NO_TABLE']))
    		{
    			$tmp_array[] = $value['DB_FIELD'];
    		}
    	}
    	
    	return implode(',',$tmp_array);
    }
    
    public function insertNewRecord($load=true)
    {
        $data_pairs = $this->buildDataPairs();

        $values = array_map('addslashes', array_values($data_pairs));
        $keys   = array_keys($data_pairs);

        $table  = $this->options['TABLE_NAME'];
        $sql    = 'INSERT INTO '.$table.' ('.implode(',', $keys).') VALUES (\''.implode('\',\'', $values).'\')';
        
        // 9/13/13 Originally attempted to return the new id generated by the INSERT
        // however; I could not get to work at the time. The workaround was just to reSELECT the
        // record again and acquire the id
        return $this->dbQuery($sql);
    }
    
    public function chooseNewUpdate($id)
    {
	    $data = $this->selectByID($id);
	    
	    if (!$data) // id does not already exist
	    {
		    return $this->insertNewRecord();
	    }
	    else // id does exist
	    {
		    return $this->updateRecordID($id);
	    }
    }
    
    public function loadRecordID($id=null)
	{
		$data = $this->selectByID($id);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ;
	}
    
    public function updateRecordID($id=null)
    {
        if (is_null($id))
        {
            return false;
        }
		
		$record_id = $id;

        $data_pairs = $this->buildDataPairs();
        
        $values     = array_map('addslashes', array_values($data_pairs));
        $keys       = array_keys($data_pairs);

        $update_array = array();

        foreach ($data_pairs as $field=>$value)
        {
            $update_array[] = $field.'="'.$value.'"';
        }

        $update_str = implode(',',$update_array);

       	$table      = $this->options['TABLE_NAME'];
        $table_id   = $this->options['TABLE_ID_FIELD'];

        $sql = 'UPDATE '.$table.' SET '.$update_str.' WHERE '.$table_id.'= :record_id';
        
        $data = array(':record_id' => $record_id);
        $this->dbExecute($sql,$data);
        return $this->rows_affected;
    }
    
    public function selectByID($id=null)
    {   
        $form_id = (isset($_REQUEST[$this->options['TABLE_ID_FIELD']])) ? $_REQUEST[$this->options['TABLE_ID_FIELD']] : null;
		
		$record_id = (is_null($id)) ? $form_id : $id ;
        
        $table     = $this->options['TABLE_NAME'];
        $table_id  = $this->options['TABLE_ID_FIELD'];
        
        // $sql = 'SELECT * FROM '.$table.' WHERE '.$table_id.'="'.$record_id.'"';
        $sql  = 'SELECT * FROM '.$table.' WHERE '.$table_id.'= :record_id';
        $data = array(':record_id' => $record_id);
        return $this->dbExecute($sql,$data);
    }
    
    public function selectByPairs($pairs=null,$order=null, $getAll=true)
    {
    	// NOTE: The key in the pairs must match the main key
    	// in the field list which is NOT the actual field name.
    	// We need to do this to maintain consistency
    	if ( (is_null($pairs)) || (!is_array($pairs)) )
    	{
    		return false;
    	}
    	
    	$table     = $this->options['TABLE_NAME'];
        $table_id  = $this->options['TABLE_ID_FIELD'];
        $order_str = '';
        $where_str = '';
        $where_arr = array();
    	
    	if (!is_null($order))
        {
        	$order_str = ' ORDER BY '. $order;
        }
        
        foreach ($pairs as $field=>$value)
        {
        	$actual_field = $this->options['FIELDS'][$field]['DB_FIELD'];
        	$place_holder = ':'.$field;
        	$where_arr[] = $actual_field.'='.$place_holder;
        	$data[$place_holder] = $value;
        }
        
        $where_str = ' WHERE '.implode(' AND ', $where_arr);  
        $sql       = 'SELECT * FROM '.$table.$where_str.$order_str;
        return $this->dbExecute($sql,$data,$getAll);
    }
    
    public function selectAll($order=null)
    {	
        $table     = $this->options['TABLE_NAME'];
        $table_id  = $this->options['TABLE_ID_FIELD'];
        $order_str = '';
        
        if (!is_null($order))
        {
        	$order_str = ' ORDER BY '. $order;
        }

        $sql = 'SELECT * FROM '.$table.$order_str;
        $data = array();
        
        return $this->dbExecute($sql,$data,true);
        
        // return $this->dbAll($sql);
    }
    
    public function selectAllTable($order=null)
    {	
        $table     = $this->options['TABLE_NAME'];
        $table_id  = $this->options['TABLE_ID_FIELD'];
        $order_str = '';
        
        if (!is_null($order))
        {
        	$order_str = ' ORDER BY '. $order;
        }
        
        $field_str = $this->implodeForTable();

        $sql  = 'SELECT '.$field_str.' FROM '.$table.$order_str;
        $data = array();
        
        return $this->dbExecute($sql,$data,true);
        
        // return $this->dbAll($sql);
    }
    
    public function selectAllForList($fields='*',$order=null)
    {	
        $table     = $this->options['TABLE_NAME'];
        $table_id  = $this->options['TABLE_ID_FIELD'];
        $order_str = '';
        
        if (!is_null($order))
        {
        	$order_str = ' ORDER BY '. $order;
        }

        $sql  = 'SELECT '.$fields.' FROM '.$table.$order_str;
        $data = array();
        
        // return $this->dbAll($sql);
        
        return $this->dbExecute($sql,$data,true);
    }
    
    public function deleteByID($id=null)
    {
        if (is_null($id))
        {
            return false;
        }
		
		$record_id = $id;
        
        $table     = $this->options['TABLE_NAME'];
        $table_id  = $this->options['TABLE_ID_FIELD'];

        $sql = 'DELETE FROM '.$table.' WHERE '.$table_id.'= :record_id';
        $data = array(':record_id' => $record_id);
        
        return $this->dbExecute($sql,$data,false);
    }
    
    public function runQuery($query=null)
    {
    	if (!is_null($query))
    	{
    		return $this->dbAll($query);
    	}
    }
    
    public function cleanResults($results)
    {
    	return $this->stripslashes_deep($results);
    }
    
    public function process_Save_Actions($name, $value)
	{
		$action = 'save_action_'.$name;
		
		if (method_exists($this,$action))
		{
			return call_user_func(array($this, $action), $data);
		}
		else
		{
			return $value;
		}
	}
	
	// *******************************************************************
    // ********  XCrud Function Stubs ************************************
    // *******************************************************************
	public function getXCrudRelations()
	{
		return array();
	}
}
?>
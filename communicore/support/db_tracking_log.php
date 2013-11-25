<?php
/*
 * db_tracking_log
 *
 * Database object for the session management
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_tracking_log extends db_common
{
	public $options;
	public $db;
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	public function setOptions($options)
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['TABLE_NAME']     = 'tracking_log';
		$this->options['TABLE_ID_FIELD'] = 'tlog_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
	}
	
	private function setFields()
	{
		/*
		 * The 'FIELDS' array setup
		 *
		 * each field/element has a simple name for a key. This helps when placing them on a form because
		 * because you don't have to remember the actual name of the field in the database. Plus, sometimes
		 * you have elements that are not actual fields in the database, but collectors of information that
		 * you can later process and put in whatever field you want. 
		 *
		 * The subkeys are:
		 * TITLE - this is usually the label on the form
		 * VALIDATE - this is the standard validation for the field. It can be overridden if needed, but if no
		 * other validation is specified, it will always run. It can also be an array so that it runs more than one
		 * validation.
		 * MESSAGE - this is the message that will appear if the validation fails
		 * DB_FIELD - this is the ACTUAL name of the field in the database. If this is an ELEMENT (See above note)
		 * then DB_FIELD will have no value.
         * DEFAULT - will contain a default value when a new record is created.		 
         * ERROR - will be added when the list is validated. ERROR can either be 1 (has an error) or 0 (no error)
		 * BE AWARE that VALIDATE can be an array of validations, so ERROR would be returned as an array of error values.
		 * 
		 * Remember to never include to auto-increment id field or the modified timestamp field. These fields will
		 * never be edited or updated from sql statements. They will always be updated or created automaticlly by mysql.
		 *
		 * Whenever addValues is run, a VALUE key is inserted and the current value from the $_REQUEST array is inserted.
		 *
		 * NOTE: Other values can be added as needed.
		 */
		$fields = array();
		
		$fields['id'] = array(
			'TITLE'    => 'ID',
			'DB_FIELD' => 'tlog_id',
			'NO_UPDATE' => 1
			);
		
		$fields['site'] = array(
			'TITLE'    => 'Site Name',
			'DB_FIELD' => 'tlog_site'
			);
			
		$fields['code1'] = array(
			'TITLE'    => 'URL Code 1',
			'DB_FIELD' => 'tlog_url_code1'
			);
			
		$fields['code2'] = array(
			'TITLE'    => 'URL Code 2',
			'DB_FIELD' => 'tlog_url_code2'
			);
			
		$fields['code3'] = array(
			'TITLE'    => 'URL Code 3',
			'DB_FIELD' => 'tlog_url_code3'
			);
			
		$fields['code4'] = array(
			'TITLE'    => 'URL Code 4',
			'DB_FIELD' => 'tlog_url_code4'
			);
		
		$fields['query'] = array(
			'TITLE'    => 'URL Query String',
			'DB_FIELD' => 'tlog_query'
			);
			
		$fields['search'] = array(
			'TITLE'    => 'Search Field',
			'DB_FIELD' => 'tlog_search'
			);
			
		$fields['userid'] = array(
			'TITLE'    => 'User ID',
			'DB_FIELD' => 'tlog_user_id'
			);
			
		$fields['userip'] = array(
			'TITLE'    => 'User IP',
			'DB_FIELD' => 'tlog_user_ip'
			);	
						
		return $fields;
	}
	
	public function newTrackingLog()
	{
		$call_parts  = wed_getSystemValue('CALL_PARTS');
		$query_vars  = wed_getSystemValue('QUERY_VARS');
		$user_id     = wed_getSystemValue('USER_ID', 0);
		$user_ip     = wed_getSystemValue('USER_IP');
		
		$search      = wed_getSystemValue('SEARCH_NORMAL');
		$search      = (!is_null($search)) ? $search . ' Clean(' . wed_getSystemValue('SEARCH_CLEAN') . ')' : null;
		
		$call_part1  = (!empty($call_parts[0])) ? $call_parts[0] : 'home_page' ;
		$call_part2  = (isset($call_parts[1])) ? $call_parts[1] : null ;
		$call_part3  = (isset($call_parts[2])) ? $call_parts[2] : null ;
		$call_part4  = (isset($call_parts[3])) ? $call_parts[3] : null ;
		
		$query_str   = null;
		
		if (is_array($query_vars))
		{
			$temp_arr = array();
			
			foreach ($query_vars as $key=>$value)
			{
				$temp_arr[] = $key . '=' .$value; 
			}
			
			$query_str = implode(',', $temp_arr);
		}
		
		$data = array(
			'site'   => SITE_DOMAIN,
			'code1'  => $call_part1,
			'code2'  => $call_part2,
			'code3'  => $call_part3,
			'code4'  => $call_part4,
			'query'  => $query_str,
			'search' => $search,
			'userid' => $user_id,
			'userip' => $user_ip
			);
			
		$this->insertValuesArray($data);
		return $this->insertNewRecord();
	}
}
?>
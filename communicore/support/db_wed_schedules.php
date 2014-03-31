<?php
/*
 * db_wed_schedules
 *
 * Database object for the online database schedules
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_wed_schedules extends db_common
{
	public $options;
	public $db;
	
	public function __construct($options=array())
	{
		global $walt;
		$this->db     = $walt->getImagineer('communicore');
		$this->setOptions($options);
	}
	
	public function setOptions($options)
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['TABLE_NAME']     = 'wed_schedules';
		$this->options['TABLE_DISPLAY']  = 'Schedule Management';
		$this->options['TABLE_ID_FIELD'] = 'schd_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
	}
	
	public function setFields($join=true)
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
		$fields     = array();
		$today_date = wed_getDateToday();
		
		$fields['id'] = array(
			'LABEL'     => 'ID',
			'DB_FIELD'  => 'schd_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'schd_modified',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
			
		$fields['name'] = array(
			'LABEL'     => 'Schedule Name',
			'DB_FIELD'  => 'schd_name',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'    => 1
			);
		
		$fields['code'] = array(
			'LABEL'    => 'Schedule Code',
			'VALIDATE' => 'Required',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'schd_code',
			'INSTRUCT' => 'This is the unique code that the system uses to index this article.',
			'DEFAULT'  => 'Enter a code',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['description'] = array(
			'LABEL'    => 'Schedule Description',
			'DB_FIELD' => 'schd_description',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['active'] = array(
			'LABEL'    => 'Active',
			'DB_FIELD' => 'schd_active',
			'DEFAULT'  => 'Y',
			'LIST_SELECT' => array('Y','N'),
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'    => 1
			);
		
		$fields['start'] = array(
			'LABEL'    => 'Start Date',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The start date is a required field',
			'DB_FIELD' => 'schd_start_date',
			'DEFAULT'  => $today_date,
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'    => 1
			);
			
		$fields['end'] = array(
			'LABEL'    => 'End Date',
			'DB_FIELD' => 'schd_end_date',
			'DEFAULT'  => null,
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'    => 1
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'schd_details',
			'INSTRUCT' => 'Details are various options for this schedule. Example:  LINK| apple.com;',
			'SHOW_FIELD'    => 1,
			'NO_EDITOR'    => 1
			);
			
		return $fields;
	}	
}
?>
<?php
/*
 * db_events_activity
 *
 * Database object for the online database events_activity
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_events_activity extends db_common
{
	public $options;
	public $db;
	public $sql;
	
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
		$this->options['TABLE_NAME']     = 'events_activity';
		$this->options['TABLE_ID_FIELD'] = 'actv_id';
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
		$fields = array();
		
		$fields['id'] = array(
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'actv_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'TITLE'     => 'Modification',
			'DB_FIELD'  => 'actv_modification',
			'NO_UPDATE' => 1
			);
		
		$fields['title'] = array(
			'TITLE'    => 'Activity Title',
			'DB_FIELD' => 'actv_title'
			);
			
		$fields['description'] = array(
			'TITLE'    => 'Description',
			'DB_FIELD' => 'actv_description'
			);
		
		$fields['active'] = array(
			'TITLE'    => 'Active?',
			'DB_FIELD' => 'actv_active'
			);
			
		$fields['year'] = array(
			'TITLE'    => 'Year',
			'DB_FIELD' => 'actv_year'
			);
			
		$fields['start'] = array(
			'TITLE'    => 'Activity Start',
			'DB_FIELD' => 'actv_start_timestamp'
			);
			
		$fields['end'] = array(
			'TITLE'    => 'Activity End',
			'DB_FIELD' => 'actv_end_timestamp'
			);
			
		$fields['staff'] = array(
			'TITLE'    => 'Staff Name',
			'DB_FIELD' => 'actv_staff_name'
			);
		
		$fields['email'] = array(
			'TITLE'    => 'Staff Email',
			'DB_FIELD' => 'actv_staff_email'
			);
			
		$fields['details'] = array(
			'TITLE'    => 'Details',
			'DB_FIELD' => 'actv_details'
			);
			
		return $fields;
	}
	
	    
    // *******************************************************************
    // ********  getTITLE produces a description for this record ***
    // *******************************************************************
    public function getTITLE()
    {
		return $this->getValue('title');
    }
    
    // *******************************************************************
    // *****  getTAB_HEADER produces the tab header for this record ***
    // *******************************************************************
    public function getTAB_HEADER()
    {
		return $this->getDetail('TAB_HEADER',$this->getValue('title'));
    }
    
    // *******************************************************************
    // *****  getMENU_TITLE produces the Menu Title for this record ******
    // *******************************************************************
    public function getMENU_TITLE()
    {
		return $this->getDetail('MENU_TITLE',$this->getValue('title'));
    }
    
    // *******************************************************************
    // ********  getDescription produces a description for this record ***
    // *******************************************************************
    public function getDESCRIPTION()
    {
	    return $this->getValue('description');
    }
    
    public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
}
?>
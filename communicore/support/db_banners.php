<?php
/*
 * db_banners
 *
 * Database object for the online database banners
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_banners extends db_common
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
		$this->options['TABLE_NAME']     = 'banners';
		$this->options['TABLE_ID_FIELD'] = 'banr_id';
		
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
		$fields     = array();
		$today_date = wed_getDateToday();
		
		$fields['id'] = array(
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'banr_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'TITLE'     => 'Modified',
			'DB_FIELD'  => 'banr_modified',
			'NO_UPDATE' => 1
			);
			
		$fields['title'] = array(
			'TITLE'     => 'Banner Title',
			'DB_FIELD'  => 'banr_title'
			);
			
		$fields['description'] = array(
			'TITLE'     => 'Description',
			'DB_FIELD'  => 'banr_description'
			);
			
		$fields['width'] = array(
			'TITLE'    => 'Width',
			'DB_FIELD' => 'banr_width'
			);
		
		$fields['type'] = array(
			'TITLE'    => 'Type',
			'DB_FIELD' => 'banr_type',
			'INSTRUCT' => 'Banners can either be Image or HTML',
			'DEFAULT'  => 'Image'
			);
			
		$fields['height'] = array(
			'TITLE'    => 'Height',
			'DB_FIELD' => 'banr_height'
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'banr_details',
			'INSTRUCT' => 'Details are various options for this schedule. Example:  LINK| apple.com;'
			);
			
		$fields['html'] = array(
			'LABEL'    => 'HTML',
			'DB_FIELD' => 'banr_html',
			'INSTRUCT' => 'Use this to store HTML for HTML banners'
			);
			
		$fields['active'] = array(
			'TITLE'    => 'Active',
			'DB_FIELD' => 'banr_active',
			'DEFAULT'  => 'Y'
			);
			
		return $fields;
	}
	
	public function loadImageID($id=null)
	{
		if (is_null($id))
        {
            return false;
        }
        
		$data = $this->selectByID($id);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ;
	}
	
	public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
	
	
}
?>
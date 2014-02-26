<?php
/*
 * db_wed_admin_views
 *
 * Database object for the sites listing
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_wed_admin_views extends db_common
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
		$this->options['TABLE_NAME']     = 'wed_admin_views';
		$this->options['DISPLAY_NAME']   = 'Administration Views';
		$this->options['TABLE_ID_FIELD'] = 'advw_id';
		
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
			'LABEL'     => 'ID',
			'DB_FIELD'  => 'advw_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'advw_modified',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
		
		$fields['code'] = array(
			'LABEL'    => 'View Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The view code is a required field',
			'DB_FIELD' => 'advw_code',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['title'] = array(
			'LABEL'    => 'View Title',
			'DB_FIELD' => 'advw_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['description'] = array(
			'LABEL'    => 'View Description',
			'DB_FIELD' => 'advw_description',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['instructions'] = array(
			'LABEL'    => 'View Instructions',
			'DB_FIELD' => 'advw_instructions',
			'SHOW_FIELD'  => 1
			);
			
		$fields['security'] = array(
			'LABEL'    => 'View Security',
			'DB_FIELD' => 'advw_security',
			'DEFAULT'  => 'all',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['db'] = array(
			'LABEL'    => 'View Database',
			'DB_FIELD' => 'advw_db_object',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'advw_details',
			'INSTRUCT' => 'Details are various options for this view.',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
					
		return $fields;
	}
	
	public function loadView($code=null)
	{
		$data = $this->selectByCode($code);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ;
	}
	
	public function selectByCode($code=null)
    {
        if (is_null($code))
        {
            return false;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['code']['DB_FIELD'].'="'.$code.'"';

        $sql = 'SELECT * FROM '.$table.$where_str;
        
        return $this->dbRow($sql);
    }
	
	public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
	
}
?>
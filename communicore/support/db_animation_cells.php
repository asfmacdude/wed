<?php
/*
 * db_themes
 *
 * Database object for the animation cells table
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_animation_cells extends db_common
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
		$this->options['TABLE_NAME']     = 'animation_cells';
		$this->options['TABLE_ID_FIELD'] = 'acl_id';
		
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
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'acl_id',
			'NO_UPDATE' => 1
			);
		
		$fields['title'] = array(
			'TITLE'    => 'Cell Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The cell title is a required field',
			'DB_FIELD' => 'acl_title'
			);
			
		$fields['description'] = array(
			'TITLE'    => 'Cell Description',
			'DB_FIELD' => 'acl_description'
			);
		
		$fields['code'] = array(
			'TITLE'    => 'Cell Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The animation cell code is a required field',
			'DB_FIELD' => 'acl_code'
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'acl_details',
			'INSTRUCT' => 'Details are various options for this theme in JSON format.'
			);	
						
		return $fields;
	}
    
    public function loadAllForList()
	{
		$fields = 'acl_id,acl_code';
		$order  = 'theme_name';
		$data   = $this->selectAllForList($fields,$order);	
		return (!$data) ? false : $data ;
	}
	
	public function loadCellCode($code=null)
	{
		$data = $this->selectByCode($code);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let showdirector know
	}
	
	public function selectByCode($code=null)
    {
        if (is_null($code))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['code']['DB_FIELD'].'="'.$code.'"';

        $sql = 'SELECT * FROM '.$table.$where_str;
        
        return $this->dbRow($sql);
    }
    
    public function getDetails()
    {
	    // Note: this table details field uses JSON formatting
	    $detail_field = $this->getValue('details');
	    return wed_decodeJSON($detail_field,true);
    }
	
}
?>
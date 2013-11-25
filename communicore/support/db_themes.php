<?php
/*
 * db_themes
 *
 * Database object for the themes database
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_themes extends db_common
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
		$this->options['TABLE_NAME']     = 'themes';
		$this->options['TABLE_ID_FIELD'] = 'theme_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->options['THEME_LIST']     = $this->getThemeList();
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
			'DB_FIELD'  => 'theme_id',
			'NO_UPDATE' => 1
			);
		
		$fields['name'] = array(
			'TITLE'    => 'Theme Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The theme name is a required field',
			'DB_FIELD' => 'theme_name'
			);
			
		$fields['description'] = array(
			'TITLE'    => 'Theme Description',
			'DB_FIELD' => 'theme_description',
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'theme_details',
			'INSTRUCT' => 'Details are various options for this theme. Example:  COLOR| Outrageous Red;'
			);	
						
		return $fields;
	}
		
	public function getThemeList()
	{
		$list   = array();
		$fields = 'theme_id,theme_name';
		$order  = 'theme_name';
		$data   = $this->selectAllForList($fields,$order);
		
		if ($data)
		{
			foreach ($data as $row=>$fields)
			{
				$list[$fields['theme_id']] = $fields['theme_name'];
			}
		}
			
		return $list;
	}
	
	public function loadThemeByID($id=null)
    {
        if (is_null($id))
        {
            return null;
        }
	
		$pairs = array( 'id' => $id );
		$data  = $this->selectByPairs($pairs, null, false);
		
		if ($data)
		{
			$this->addValues_Data($data);
		}
		
		return ($data) ? true : false;
    }
	
	public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
}
?>
<?php
/*
 * db_wed_themes
 *
 * Database object for the themes database
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_wed_themes extends db_common
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
		$this->options['TABLE_NAME']     = 'wed_themes';
		$this->options['TABLE_DISPLAY']  = 'Themes';
		$this->options['TABLE_ID_FIELD'] = 'theme_id';		
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
			'DB_FIELD'  => 'theme_id',
			'NO_UPDATE' => 1,
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['name'] = array(
			'LABEL'    => 'Theme Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The theme name is a required field',
			'DB_FIELD' => 'theme_name',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['description'] = array(
			'LABEL'    => 'Theme Description',
			'DB_FIELD' => 'theme_description',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1		
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'theme_details',
			'INSTRUCT' => 'Details are various options for this theme. Example:  COLOR| Outrageous Red;',
			'NO_EDITOR' => 1,
			'SHOW_FIELD'  => 1
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
	
	 // *******************************************************************
    // ********  XCRUD Section *******************************************
    // *******************************************************************
    
    // *******************************************************************
    // ********  setupXCrud initial setup of XCrud Object ****************
    // *******************************************************************
    public function setupXCrud($code=null)
    {
	    // Based on the code, we can present different views of the content_main
	    // table with different settings.
	    if ($code=='themes_100')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME'],$this->options['TABLE_DISPLAY']);
		    $xcrud->configFields($this->setFields(false));
		    
		    return $xcrud->renderXCrud();

	    }
    }
}
?>
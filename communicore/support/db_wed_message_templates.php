<?php
/*
 * db_wed_message_templates
 *
 * Database object for the online database wed_message_templates
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_wed_message_templates extends db_common
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
		$this->options['TABLE_NAME']     = 'wed_message_templates';
		$this->options['TABLE_DISPLAY']  = 'Message Templates';
		$this->options['TABLE_ID_FIELD'] = 'msgt_id';
		
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
			'DB_FIELD'  => 'msgt_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'msgt_modify',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
		
		$fields['siteid'] = array(
			'LABEL'    => 'Message Site',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The site is a required field',
			'INSTRUCT' => 'Each setting is site specific so choose which site this will affect.',
			'DB_FIELD' => 'msgt_site_id',
			'DEFAULT'  => 4,
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['title'] = array(
			'LABEL'    => 'Message Title',
			'INSTRUCT' => 'This is the title for the setting.',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The setting name is a required field',
			'DB_FIELD' => 'msgt_title',
			'DEFAULT'  => 'ENTER TITLE',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['code'] = array(
			'LABEL'    => 'Message Code',
			'VALIDATE' => 'Required',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'msgt_code',
			'INSTRUCT' => 'This is the unique code that the system uses to find this template.',
			'DEFAULT'  => 'Enter a code',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
	
		$fields['description'] = array(
			'LABEL'    => 'Description',
			'INSTRUCT' => 'Insert a description of this template for others to understand.',
			'DB_FIELD' => 'msgt_description',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['message'] = array(
			'LABEL'    => 'Message Text',
			'INSTRUCT' => 'Insert the actual text of the message here.',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The setting value is a required field',
			'DB_FIELD' => 'msgt_value',
			'DEFAULT'  => 'none',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
		
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'msgt_details',
			'INSTRUCT' => 'Details are various options for this template. Example:  AUTHOR| William Shakespeare;',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
					
		return $fields;
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
	    if ($code=='message_templates')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME']);
		    $xcrud->configFields($this->setFields(false));
		    
		    $local_relations = $this->getXCrudRelations();
		    
		    foreach ($local_relations as $key=>$data)
		    {
			    $xcrud->setRelation($data);
		    }
		    
		    return $xcrud->renderXCrud();

	    }
    }
    
    public function getXCrudRelations()
    {
	    $relations[] = array(
	    	'RELATE_FROM'   => 'msgt_site_id', 
	    	'RELATE_TABLE'  => 'sites', 
	    	'RELATE_TO'     => 'site_id', 
	    	'DISPLAY_FIELD' => 'site_title');
	    return $relations;
    }
}
?>
<?php
/*
 * db_wed_menus_base
 *
 * Database object for the sites listing
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_wed_menus_base extends db_common
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
		$this->options['TABLE_NAME']     = 'wed_menus_base';
		$this->options['DISPLAY_NAME']   = 'Menu Listing';
		$this->options['TABLE_ID_FIELD'] = 'mnub_id';
		
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
			'DB_FIELD'  => 'mnub_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'mnub_modified',
			'NO_UPDATE' => 1,
			'SHOW_COLUMN'  => 1
			);
		
		$fields['title'] = array(
			'LABEL'    => 'Menu Display Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The menu title is a required field',
			'DB_FIELD' => 'mnub_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['list'] = array(
			'LABEL'    => 'Menu List Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The menu title is a required field',
			'DB_FIELD' => 'mnub_list_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['link'] = array(
			'LABEL'    => 'Menu Link',
			'DB_FIELD' => 'mnub_link',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['security'] = array(
			'LABEL'    => 'Menu Security Levels',
			'DB_FIELD' => 'mnub_security',
			'SHOW_FIELD'  => 1
			);
			
		$fields['description'] = array(
			'LABEL'    => 'Menu Description',
			'DB_FIELD' => 'mnub_description',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['megamenu'] = array(
			'LABEL'    => 'Allow Mega Menu',
			'DB_FIELD' => 'mnub_mega_menu',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['megahtml'] = array(
			'LABEL'    => 'Mega Menu HTML',
			'DB_FIELD' => 'mnub_mega_html',
			'INSTRUCT' => 'HTML code for the mega menu content.',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
		
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'mnub_details',
			'INSTRUCT' => 'Details are various options for this article. Example:  AUTHOR| William Shakespeare;',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
			
		$fields['active'] = array(
			'LABEL'    => 'Active Y/N?',
			'DB_FIELD' => 'mnub_active',
			'DEFAULT'  => 'Y',
			'LIST_SELECT'  => array('Y','N'),
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
					
		return $fields;
	}
	
	public function loadMenuID($id=null)
	{
		$status = false;
		
		if (!is_null($id))
        {
            $table      = $this->options['TABLE_NAME'];        
			$where_str  = ' WHERE ';
			$where_str .= $this->options['FIELDS']['id']['DB_FIELD'].'="'.$id.'"';
			$sql        = 'SELECT * FROM '.$table.$where_str;    
			$data       = $this->dbRow($sql);
			$this->addValues_Data($data);	
			$status     = (!$data) ? false : true ;
        }
        
        return $status;
	}
	
	public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
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
	    if ($code=='menus_200')
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
		    
		    // Try Nested Table
		    $nest_name1 = 'wed_menus_connect';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Base Menu Connections',
		    	'RELATE_FROM'     => 'mnub_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'mnuc_menu_base_id'
		    );
			
			$xcrud->createNestedTable($nest1_options);
			$xcrud->configFields($db_object->setFields(false),$nest_name1);
			
			$nest1_relations = $db_object->getXCrudRelations();
			
			foreach ($nest1_relations as $key=>$data)
			{
				$data['OBJECT_NAME'] = $nest_name1;
				$xcrud->setRelation($data);
			}
		    
		    return $xcrud->renderXCrud();

	    }
    }
}
?>
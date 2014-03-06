<?php
/*
 * db_wed_menus
 *
 * Database object for the sites listing
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_wed_menus extends db_common
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
		$this->options['TABLE_NAME']     = 'wed_menus';
		$this->options['DISPLAY_NAME']   = 'Menus';
		$this->options['TABLE_ID_FIELD'] = 'mnu_id';
		
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
			'DB_FIELD'  => 'mnu_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'mnu_modified',
			'NO_UPDATE' => 1,
			'SHOW_COLUMN'  => 1
			);
		
		$fields['code'] = array(
			'LABEL'    => 'Menu Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The menu code is a required field',
			'DB_FIELD' => 'mnu_code',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['title'] = array(
			'LABEL'    => 'Menu Title',
			'DB_FIELD' => 'mnu_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['description'] = array(
			'LABEL'    => 'Menu Description',
			'DB_FIELD' => 'mnu_description',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'  => 1
			);
					
		return $fields;
	}
	
	public function loadMenu($code=null)
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
	
	public function getSiteList()
	{
		$list   = array();
		$fields = 'mnu_id,mnu_name';
		$order  = 'mnu_name';
		$data   = $this->selectAllForList($fields,$order);
		
		if ($data)
		{
			foreach ($data as $row=>$fields)
			{
				$list[$fields['mnu_id']] = $fields['mnu_name'];
			}
		}
			
		return $list;
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
	    if ($code=='menus')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME']);
		    $xcrud->configFields($this->setFields(false));
		    
		    
		    // Try Nested Table
		    $nest_name1 = 'wed_menus_connect';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Menu Connections',
		    	'RELATE_FROM'     => 'mnu_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'mnuc_menu_id'
		    );
			
			$xcrud->createNestedTable($nest1_options);
			$xcrud->configFields($db_object->setFields(false),$nest_name1);
			
			$nest1_relations = $db_object->getXCrudRelations();
			
			foreach ($nest1_relations as $key=>$data)
			{
				$data['OBJECT_NAME'] = $nest_name1;
				$xcrud->setRelation($data);
			}
			
			// Try Nested Table 2
		    $nest_name2 = 'wed_menus_base';
		    $db_object2  = wed_getDBObject($nest_name2);
		    
		    $nest2_options = array(
		    	'OBJECT_NAME'     => $nest_name2,
		    	'PARENT_NAME'   => $nest_name1,
		    	'CONNECTION_NAME' => 'Base Menu Connections',
		    	'RELATE_FROM'     => 'mnuc_menu_base_id',
		    	'RELATE_TABLE'    => $nest_name2,
		    	'RELATE_TO'       => 'mnub_id'
		    );
			
			$xcrud->createNestedTable($nest2_options);
			$xcrud->configFields($db_object2->setFields(false),$nest_name2);
			
			$nest2_relations = $db_object2->getXCrudRelations();
			
			foreach ($nest2_relations as $key=>$data)
			{
				$data['OBJECT_NAME'] = $nest_name2;
				$xcrud->setRelation($data);
			}
		    
		    return $xcrud->renderXCrud();

	    }
    }
}
?>
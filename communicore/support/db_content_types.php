<?php
/*
 * db_content_types
 *
 * Database object for the types of content, Page Content, Post, News, etc.
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_content_types extends db_common
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
		$this->options['TABLE_NAME']     = 'content_types';
		$this->options['TABLE_DISPLAY']  = 'Content Management Types';
		$this->options['TABLE_ID_FIELD'] = 'ctp_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->options['TYPE_LIST']      = $this->getTypeList();
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
			'DB_FIELD'  => 'ctp_id',
			'NO_UPDATE' => 1
			);
		
		$fields['title'] = array(
			'LABEL'    => 'Type Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The Type name is a required field',
			'DB_FIELD' => 'ctp_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['sysname'] = array(
			'LABEL'    => 'System Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The system name is a required field',
			'DB_FIELD' => 'ctp_sysname',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'ctp_details',
			'INSTRUCT' => 'Details are various options for this site. Example:  SITE_TITLE| ASF Foundation;',
			'SHOW_FIELD'   => 1,
			'NO_EDITOR'    => 1
			);	
						
		return $fields;
	}
	
	public function getTypeID($name)
	{
		$data = $this->loadType($name);
		return (!$data) ? false : $this->getValue('id');
	}
	
	public function loadType($name=null)
	{
		$data = $this->selectByType($name);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ;
	}
	
	public function selectByType($name=null)
    {
        // This searches by the sysname, not the title
        if (is_null($name))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['sysname']['DB_FIELD'].'="'.$name.'"';

        $sql = 'SELECT * FROM '.$table.$where_str;
        
        return $this->dbRow($sql);
    }
	
	public function getTypeList()
	{
		$list   = array();
		$fields = 'ctp_id,ctp_title,ctp_sysname';
		$order  = 'ctp_title';
		$data   = $this->selectAllForList($fields,$order);
		
		if ($data)
		{
			foreach ($data as $row=>$fields)
			{
				$list[$fields['ctp_id']] = array(
					'TITLE'   => $fields['ctp_title'],
					'SYSNAME' => $fields['ctp_sysname']
				);
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
	    if ($code=='content_100')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME'],$this->options['TABLE_DISPLAY']);
		    $xcrud->configFields($this->setFields(false));
		    
		    // Try Nested Table
		    $nest_name1 = 'content_main';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Content Connections',
		    	'RELATE_FROM'     => 'ctp_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'cnt_type_id'
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
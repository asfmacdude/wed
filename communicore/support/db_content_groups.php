<?php
/*
 * db_content_groups
 *
 * Database object for the online database content_groups
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_content_groups extends db_common
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
		$this->options['TABLE_NAME']     = 'content_groups';
		$this->options['TABLE_DISPLAY']  = 'Content Management Groups';
		$this->options['TABLE_ID_FIELD'] = 'cng_id';
		
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
			'DB_FIELD'  => 'cng_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'LABEL'     => 'Modification',
			'DB_FIELD'  => 'cng_modification',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
		
		$fields['code'] = array(
			'LABEL'    => 'Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The 4 character code is a required field',
			'DB_FIELD' => 'cng_code',
			'DEFAULT'  => 'xxxx',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['sysname'] = array(
			'LABEL'    => 'System Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The system name is a required field',
			'DB_FIELD' => 'cng_sys_name',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['title'] = array(
			'LABEL'    => 'Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'cng_group_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['pagetitle'] = array(
			'LABEL'    => 'Page Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'cng_page_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'cng_details',
			'SHOW_FIELD'   => 1,
			'NO_EDITOR'    => 1
			);
		
		$fields['active'] = array(
			'LABEL'    => 'Active Y/N?',
			'DB_FIELD' => 'cng_active',
			'DEFAULT'  => 'Y',
			'LIST_SELECT'  => array('Y','N'),
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		return $fields;
	}
	
	public function getGroupID($name)
	{
		$data = $this->selectBySysName($name);
		
		if ($data)
		{
			$this->addValues_Data($data);
			return $this->getValue('id');
		}
		else
		{
			return false;
		}
		
	}
	
	public function getGroupTitle($name)
	{
		$data = $this->selectBySysName($name);
		
		if ($data)
		{
			$this->addValues_Data($data);
			return $this->getValue('title');
		}
		else
		{
			return false;
		}
		
	}
	
	public function checkGroupSysName($name)
	{
		$data = $this->selectBySysName($name);
		$this->addValues_Data($data);
		return (!$data) ? false : true;
	}
	
	public function loadGroupList($format_list=true)
	{
		$fields = 'cng_code,cng_sys_name,cng_title,cng_page_title';
		$order  = 'cng_sys_name';
		$data   = $this->selectAllForList($fields,$order);
		
		if (!$data)
		{
			// query failed to find anything
			return false;
		}	
		elseif ($format_list)
		{
			// Return a associative array based on sys_name
			return $this->formatGroupList($data);
		}
		else
		{
			// Return raw rows data from query
			return $data;
		}
	}
	
	private function formatGroupList($data)
	{
		// This formats the db rows into a nice associative array based of the sys_name
		// as the key
		$list = array();
		
		foreach ($data as $row=>$fields)
		{
			$list[$fields['cng_sys_name']] = $fields;
		}
		
		return $list;
	}
	
	public function selectBySysName($name=null)
    {
        if (is_null($name))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 'sysname'=> $name );
		
		return $this->selectByPairs($pairs, null, false); 
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
	    if ($code=='content_200')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME'],$this->options['TABLE_DISPLAY']);
		    $xcrud->configFields($this->setFields(false));
		    
		    // Try Nested Table
		    $nest_name1 = 'content_connect';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Content Group Connections',
		    	'RELATE_FROM'     => 'cng_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'cnn_group_id'
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
		    $nest_name2 = 'content_main';
		    $db_object2  = wed_getDBObject($nest_name2);
		    
		    $nest2_options = array(
		    	'OBJECT_NAME'     => $nest_name2,
		    	'PARENT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Selected Content',
		    	'RELATE_FROM'     => 'cnn_content_id',
		    	'RELATE_TABLE'    => $nest_name2,
		    	'RELATE_TO'       => 'cnt_id'
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
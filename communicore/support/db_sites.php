<?php
/*
 * db_sites
 *
 * Database object for the sites listing
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_sites extends db_common
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
		$this->options['TABLE_NAME']     = 'sites';
		$this->options['TABLE_ID_FIELD'] = 'site_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->options['SITE_LIST']      = $this->getSiteList();
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
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'site_id',
			'NO_UPDATE' => 1
			);
		
		$fields['name'] = array(
			'TITLE'    => 'Site Name',
			'VALIDATE' => 'Required',
			'MESSAGE'  => 'The site name is a required field',
			'DB_FIELD' => 'site_name',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['title'] = array(
			'TITLE'    => 'Site Title',
			'DB_FIELD' => 'site_title',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['description'] = array(
			'TITLE'    => 'Site Description',
			'DB_FIELD' => 'site_description',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['url'] = array(
			'TITLE'    => 'Site URL',
			'DB_FIELD' => 'site_url',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['theme'] = array(
			'TITLE'    => 'Site Theme',
			'DB_FIELD' => 'site_theme',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'site_details',
			'INSTRUCT' => 'Details are various options for this site. Example:  SITE_TITLE| ASF Foundation;',
			'SHOW_FIELD'   => 1,
			'NO_EDITOR'  => 1
			);	
						
		return $fields;
	}
	
	public function loadSite($site=null)
	{
		$data = $this->selectBySite($site);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ;
	}
	
	public function selectBySite($site=null)
    {
        if (is_null($site))
        {
            return false;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['name']['DB_FIELD'].'="'.$site.'"';

        $sql = 'SELECT * FROM '.$table.$where_str;
        
        return $this->dbRow($sql);
    }
	
	public function getSiteList()
	{
		$list   = array();
		$fields = 'site_id,site_name';
		$order  = 'site_name';
		$data   = $this->selectAllForList($fields,$order);
		
		if ($data)
		{
			foreach ($data as $row=>$fields)
			{
				$list[$fields['site_id']] = $fields['site_name'];
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
	    if ($code=='sites')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME']);
		    $xcrud->configFields($this->setFields(false));
		    
		    // Try Nested Table
		    $nest_name1 = 'sites_connect';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Site Control Connections',
		    	'RELATE_FROM'     => 'site_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'stcn_site_id'
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
		    $nest_name2 = 'content_control';
		    $db_object2  = wed_getDBObject($nest_name2);
		    
		    $nest2_options = array(
		    	'OBJECT_NAME'     => $nest_name2,
		    	'PARENT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Selected Page Control',
		    	'RELATE_FROM'     => 'stcn_control_id',
		    	'RELATE_TABLE'    => $nest_name2,
		    	'RELATE_TO'       => 'cnc_id'
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
	    
	    if ($code=='sites_100')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME']);
		    $xcrud->configFields($this->setFields(false));
		    
		    // Try Nested Table
		    $nest_name1 = 'system_config';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Site Settings',
		    	'RELATE_FROM'     => 'site_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'sys_cfg_site_id'
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
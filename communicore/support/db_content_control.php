<?php
/*
 * db_content_control
 *
 * Database object for the online database content_control
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_content_control extends db_common
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
		$this->options['TABLE_NAME']     = 'content_control';
		$this->options['TABLE_DISPLAY']  = 'Page Control';
		$this->options['TABLE_ID_FIELD'] = 'cnc_id';
			
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
			'DB_FIELD'  => 'cnc_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'cnc_modified',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
		
		$fields['siteid'] = array(
			'LABEL'     => 'Site',
			'DB_FIELD'  => 'cnc_site_id',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
		
		$fields['title'] = array(
			'LABEL'    => 'Page Title',
			'INSTRUCT' => 'Unique name or title for this page.',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'cnc_title',
			'DEFAULT'  => '',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['code'] = array(
			'LABEL'    => 'Page Code',
			'INSTRUCT' => 'Unique code for calling this page. (Must be UNIQUE!)',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'cnc_code',
			'DEFAULT'  => 'code_xxxx',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
		
		$fields['themeid'] = array(
			'LABEL'     => 'Theme ID',
			'DB_FIELD'  => 'cnc_theme_id',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['themepage'] = array(
			'LABEL'    => 'Theme Page',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The theme page is a required field',
			'DB_FIELD' => 'cnc_theme_page',
			'DEFAULT'  => 'index',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
			
		$fields['themecontrol'] = array(
			'LABEL'    => 'Theme Page Control',
			'DB_FIELD' => 'cnc_theme_page_control',
			'SHOW_FIELD'   => 1,
			'NO_EDITOR'    => 1
			);
			
		$fields['structure'] = array(
			'LABEL'    => 'Page Structure Code',
			'DB_FIELD' => 'cnc_content',
			'INSTRUCT' => 'Build the structure of your page using shortcodes.',
			'SHOW_FIELD'   => 1,
			'NO_EDITOR'    => 1
			);
			
		$fields['status'] = array(
			'LABEL'    => 'Status',
			'DB_FIELD' => 'cnc_status',
			'INSTRUCT' => 'Publish means your page can be viewed online. Hold or Draft will not be shown online.',
			'DEFAULT'  => 'Publish',
			'LIST_SELECT' => array('Publish','Hold','Draft'),
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
		
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'cnc_details',
			'INSTRUCT' => 'Details are various options for this page. Example:  PAGE_TITLE| Official Web Site;',
			'SHOW_FIELD'   => 1,
			'NO_EDITOR'    => 1
			);
			
		
		return $fields;
	}
	
	public function getPageTemplate()
	{
		$template = $this->getValue('themepage');
		$control  = $this->getValue('themecontrol');
		
		if ( (!is_null($control)) && (!empty($control)) )
		{
			$alt_template = null;
			$short        = getImagineer('shortcodes');
			$alt_template = $short->getHTML(array('HTML'=>$control,'PRE'=>true));
			
			if ( (!is_null($alt_template)) && (!empty($alt_template)) )
			{
				$template = $alt_template;
			}
		}
		
		return $template;
	}	
	
	public function getControlID($name)
	{
		$data = $this->loadPage($name);
		return (!$data) ? false : $this->getValue('id');
	}
	
	public function loadPageID($id=null)
	{
		$data = $this->selectByID($id);
			
		if ($data)
		{	
			$this->addValues_Data($data);
		}
		
		return (!$data) ? false : true ;
	}
	
	public function loadPage($code=null)
	{
		$data = $this->selectByCode($code);
			
		if ($data)
		{	
			$this->addValues_Data($data);
		}
		
		return (!$data) ? false : true ;
	}
	
	public function loadAllForTable($order=null)
	{
		$data = $this->selectAllTable($order);	
		return (!$data) ? false : $data ;
	}
	
	public function loadStructureID($id=null)
	{
		$data = $this->selectByID($id);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let showdirector know
	}
	
	public function selectByCode($code=null)
    {
		$site_id  = wed_getSystemValue('SITE_ID');
		$pairs    = array('code' => $code);
		return $this->selectByPairs($pairs,null,false);
    }
    
    public function selectByCodeSite($code=null)
    {
		$site_id  = wed_getSystemValue('SITE_ID');
		$pairs    = array('code' => $code, 'siteid' => $site_id);
		$data =  $this->selectByPairs($pairs,null,false);
		
		if ($data)
		{	
			$this->addValues_Data($data);
		}
		
		return (!$data) ? false : true ;
    }
    
    public function getDetails()
    {
	    return wed_getOptionsFromString($this->getValue('details'));
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
	    if ($code=='pages')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME'],$this->options['TABLE_DISPLAY']);
		    $xcrud->configFields($this->setFields(false));  
		    
		    $local_relations = $this->getXCrudRelations();
		    
		    foreach ($local_relations as $key=>$data)
		    {
			    $xcrud->setRelation($data);
		    }
		    
		    // Try Nested Table
		    $nest_name1 = 'sites_connect';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Site Connections',
		    	'RELATE_FROM'     => 'cnc_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'stcn_control_id'
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
    
    public function getXCrudRelations()
    {
	    $relations[] = array(
	    	'RELATE_FROM'   => 'cnc_theme_id', 
	    	'RELATE_TABLE'  => 'wed_themes', 
	    	'RELATE_TO'     => 'theme_id', 
	    	'DISPLAY_FIELD' => 'theme_name');
	    $relations[] = array(
	    	'RELATE_FROM'   => 'cnc_site_id', 
	    	'RELATE_TABLE'  => 'sites', 
	    	'RELATE_TO'     => 'site_id', 
	    	'DISPLAY_FIELD' => 'site_title');
	    return $relations;
    }	
}
?>
<?php
/*
 * db_wed_menus_connect
 *
 * Database object for the online database sites_connect which connects the
 * sites table and the content_control
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_wed_menus_connect extends db_common
{
	public $options;
	public $db;
	public $sql;
	public $where;
	public $order;
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	public function setOptions($options)
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['TABLE_NAME']     = 'wed_menus_connect';
		$this->options['DISPLAY_NAME']   = 'Menu Connections';
		$this->options['TABLE_ID_FIELD'] = 'mnuc_id';
		$this->options['JOIN_TABLES']    = array('mnu_' => 'wed_menus','mnub_' => 'wed_menus_base');
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
		
		// SQL statements
		$this->sql['MENU_JOIN'] = 'SELECT * FROM wed_menus_connect a 
			JOIN wed_menus b ON b.mnu_id = a.mnuc_menu_id  
			JOIN wed_menus_base c ON a.mnuc_menu_base_id = c.mnub_id';
		
		// Where statements
		$this->where['MENU_ID']         = ' WHERE a.mnuc_menu_id = "%MENU_ID%"';
		$this->where['MENU_CODE']       = ' WHERE b.mnu_code = "%MENU_CODE%"';
		
		// Order statements
		$this->order['MENU_SORT']       = ' ORDER BY a.mnuc_sort';
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
			'DB_FIELD'  => 'mnuc_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'LABEL'     => 'Modification',
			'DB_FIELD'  => 'mnuc_modified',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
		
		$fields['menuid'] = array(
			'LABEL'    => 'Menu ID',
			'DB_FIELD' => 'mnuc_menu_id',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['baseid'] = array(
			'LABEL'    => 'Menu Base ID',
			'DB_FIELD' => 'mnuc_menu_base_id',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['level'] = array(
			'LABEL'    => 'Menu Level',
			'DB_FIELD' => 'mnuc_level',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['link'] = array(
			'LABEL'    => 'Link?',
			'DB_FIELD' => 'mnuc_link',
			'DEFAULT'  => 'Y',
			'LIST_SELECT'  => array('Y','N'),
			'SHOW_FIELD'  => 1
			);
			
		$fields['parentid'] = array(
			'LABEL'    => 'Menu Parent ID',
			'DB_FIELD' => 'mnuc_parent_id',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['dropmenu'] = array(
			'LABEL'    => 'Allow Drop Menu',
			'DB_FIELD' => 'mnuc_allow_drop',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['sort'] = array(
			'LABEL'    => 'Menu Sort',
			'DB_FIELD' => 'mnuc_sort',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);

			
		if ($join)
		{
			$fields = $this->joinFields($fields);
		}
			
		return $fields;
	}
	
	// *******************************************************************
    // ********  search()  ***********************************************
    // *******************************************************************
    public function searchMenuID($menu_id=null)
    {
	    $status = false;
	    
	    if (!is_null($menu_id))
	    {
		    $options['QUERY'] = $this->sql['MENU_JOIN'] ;
			$options['WHERE'] = str_replace('%MENU_ID%',$menu_id, $this->where['MENU_ID']);
			$options['ORDER'] = $this->order['MENU_SORT'];
	    
			$status = $this->search($options);
	    }
	   
	    return $status;    
    }
    
    public function searchMenuCode($menu_code=null)
    {
	    $status = false;
	    
	    if (!is_null($menu_code))
	    {
		    $options['QUERY'] = $this->sql['MENU_JOIN'] ;
			$options['WHERE'] = str_replace('%MENU_CODE%',$menu_code, $this->where['MENU_CODE']);
			$options['ORDER'] = $this->order['MENU_SORT'];
	    
			$status = $this->search($options);
	    }
	    
	    return $status;    
    }
    
    private function search($options)
    {
	    $query = $options['QUERY'] . $options['WHERE'] . $options['ORDER'];		    	    
	    $data  = $this->dbAll($query);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;
    }
    
    // *******************************************************************
    // **** getTITLE produces the menu title for the control record ******
    // *******************************************************************
    public function getTITLE()
    {
		return $this->getValue('mnub_title');
    }
    
    // *******************************************************************
    // ** getDESCRIPTION produces the menu description for this record ***
    // *******************************************************************
    public function getDESCRIPTION()
    {
		return $this->getValue('mnub_description');
    }
    
    // *******************************************************************
    // *****  getPAGE_TITLE produces the Menu Title for this record ******
    // *******************************************************************
    public function getMENU_TITLE()
    {
		return $this->getValue('mnu_title');
    }
    
    // *******************************************************************
    // ** getDESCRIPTION produces the menu description for this record ***
    // *******************************************************************
    public function getMENU_DESCRIPTION()
    {
		return $this->getValue('mnu_description');
    }
    
    // *******************************************************************
    // ********  getLink produces a LINK for this record *****************
    // *******************************************************************
    public function getLINK()
    {
	    // this returns the menu link
	    return $this->getValue('mnub_link');
    }
    
    public function getMenuDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('mnub_details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
    
    
    // *******************************************************************
    // *****  getTAB_HEADER produces the tab header for this record ***
    // *******************************************************************
    public function getTAB_HEADER()
    {
		return $this->getDetail('TAB_HEADER',$this->getValue('site_title'));
    }
    
    
    // *******************************************************************
    // ********  getIMAGE_PATH produces a image path for a image *********
    // *******************************************************************
    public function getIMAGE_PATH($formats=array())
    {
	    // This is used to produce an image for the menu when shown in a list
	    $image_path  = null;
	    $thumb_specs = array();
	    $sizes       = array();
	    
	    $details['IMAGE_PATH'] = $this->getMenuDetail('IMAGE_PATH');
	    $details['CATEGORY']   = $this->getMenuDetail('CATEGORY');
	    $details['IMAGE_SIZE'] = $this->getMenuDetail('IMAGE_SIZE');
	    
	    if (!is_null($details['IMAGE_SIZE']))
	    {
		    $sz = explode('_', $details['IMAGE_SIZE']);
		    $sizes['WIDTH']  = (isset($sz[0])) ? $sz[0] : null;
		    $sizes['HEIGHT'] = (isset($sz[1])) ? $sz[1] : null;
	    }
	    
	    if (!is_null($details['IMAGE_PATH']))
	    {
		    $image_path = FILE_BASE_WEB . 'images' . DS . $details['IMAGE_PATH'];
		    $img_obj    = wed_getImageObject();
		    
		    $thumb_specs['SOURCE']    = $image_path;
			$thumb_specs['ZOOM_CROP'] = 1;
			$thumb_specs['WIDTH']     = ( (isset($sizes['WIDTH'])) && (!is_null($sizes['WIDTH'])) ) ? $sizes['WIDTH'] : null;
			$thumb_specs['HEIGHT']    = ( (isset($sizes['HEIGHT'])) && (!is_null($sizes['HEIGHT'])) ) ? $sizes['HEIGHT'] : null;
		
			$image_path = $img_obj->getFileThumbPath($thumb_specs);
	    }
	    
		return $image_path;
    }
    
    // *******************************************************************
    // **  buildMenuArray turns record_list into a useable menu array ****
    // *******************************************************************
    public function buildMenuArray()
    {
	    $menu_list  = $this->record_list;
	    $menu_build = null;
	    $menu_level = array();
	    
	    foreach ($menu_list as $key=>$data)
	    {
		    if (($data['mnuc_level']=='1') && ($data['mnub_active']=='Y'))
		    {
			    $menu_level = array();
			    $menu_level['ID']          = $data['mnuc_menu_base_id'];
			    $menu_level['PARENT_ID']   = $data['mnuc_parent_id'];
			    $menu_level['ALLOW_DROP']  = $data['mnuc_allow_drop'];
			    $menu_level['TITLE']       = $data['mnub_title'];
			    $menu_level['LINK']        = $data['mnub_link'];
			    $menu_level['SECURITY']    = $data['mnub_security'];
			    $menu_level['LEVEL']       = $data['mnuc_level'];
			    $menu_level['DESCRIPTION'] = $data['mnub_description'];
			    $menu_level['DETAILS']     = wed_getOptionsFromString($data['mnub_details']);
			    $menu_level['SUB_MENU']    = null;
			    
			    // the mnuc_allow_drop field helps to designate which fields can actually have
			    // a drop down menu
			    if ($data['mnuc_allow_drop'])
			    {
				    $menu_level['SUB_MENU'] = $this->buildSubMenuArray($menu_level['ID']);
			    }

			    $menu_build[] = $menu_level;
		    }
	    }
	    
	    return $menu_build;
    }
    
    public function buildSubMenuArray($parent_id=null)
    {
	    $menu_list  = $this->record_list;
	    $menu_build = null;
	    $menu_level = array();
	    
	    foreach ($menu_list as $key=>$data)
	    {
		    if (($data['mnuc_parent_id']==$parent_id) && ($data['mnub_active']=='Y'))
		    {
				$menu_level = array();
			    $menu_level['ID']        = $data['mnuc_id'];
			    $menu_level['PARENT_ID'] = $data['mnuc_parent_id'];
			    $menu_level['TITLE']     = $data['mnub_title'];
			    $menu_level['LINK']      = $data['mnub_link'];
			    $menu_level['SECURITY']  = $data['mnub_security'];
			    $menu_level['LEVEL']     = $data['mnuc_level'];
			    $menu_level['DESCRIPTION'] = $data['mnub_description'];
			    $menu_level['DETAILS']     = wed_getOptionsFromString($data['mnub_details']);
			    
			    // Here it calls itself to see if there are any sub menus of sub menus
			    $menu_level['SUB_MENU'] = $this->buildSubMenuArray($menu_level['ID']);

			    $menu_build[] = $menu_level;
		    }
	    }
	    
	    return $menu_build;
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
	    if ($code=='menus_100')
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
		    $nest_name1 = 'wed_menus_base';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Base Menu Connections',
		    	'RELATE_FROM'     => 'mnuc_menu_base_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'mnub_id'
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
	    	'RELATE_FROM'   => 'mnuc_menu_id', 
	    	'RELATE_TABLE'  => 'wed_menus', 
	    	'RELATE_TO'     => 'mnu_id', 
	    	'DISPLAY_FIELD' => 'mnu_title');
	    $relations[] = array(
	    	'RELATE_FROM'   => 'mnuc_menu_base_id', 
	    	'RELATE_TABLE'  => 'wed_menus_base', 
	    	'RELATE_TO'     => 'mnub_id', 
	    	'DISPLAY_FIELD' => 'mnub_list_title');
	    $relations[] = array(
	    	'RELATE_FROM'   => 'mnuc_parent_id', 
	    	'RELATE_TABLE'  => 'wed_menus_base', 
	    	'RELATE_TO'     => 'mnub_id', 
	    	'DISPLAY_FIELD' => 'mnub_list_title');
	    return $relations;
    }
}
?>
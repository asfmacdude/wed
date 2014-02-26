<?php
/*
 * db_sites_connect
 *
 * Database object for the online database sites_connect which connects the
 * sites table and the content_control
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_sites_connect extends db_common
{
	public $options;
	public $db;
	public $sql;
	public $where;
	public $order;
	
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
		$this->options['TABLE_NAME']     = 'sites_connect';
		$this->options['TABLE_ID_FIELD'] = 'stcn_id';
		$this->options['JOIN_TABLES']    = array('site_' => 'sites','cnc_' => 'content_control','cng_' => 'content_groups','cnt_' => 'content_main','ctp_' => 'content_types');
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
		
		// SQL statements
		$this->sql['SITE_JOIN'] = 'SELECT * FROM sites_connect a 
			JOIN sites b ON b.site_id = a.stcn_site_id  
			JOIN content_control c ON a.stcn_control_id = c.cnc_id';
		
		// Where statements
		$this->where['SITE_ID']         = ' WHERE a.stcn_site_id = "%SITE_ID%"';
		$this->where['CONTROL_ID']      = ' WHERE a.stcn_control_id = "%CONTROL_ID%" AND a.stcn_site_id = "%SITE_ID%"';
		$this->where['CONTROL_NAME']    = ' WHERE c.cnc_control_code = "%CONTROL_NAME%" AND a.stcn_site_id = "%SITE_ID%"';
		$this->where['BY_CURRENT_SITE'] = ' AND s.site_id = "'.wed_getSystemValue('SITE_ID').'"';
		
		// Order statements
		$this->order['CONTROL_NAME']      = ' ORDER BY c.cnc_code';
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
			'DB_FIELD'  => 'stcn_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'LABEL'     => 'Modification',
			'DB_FIELD'  => 'stcn_modification',
			'NO_UPDATE' => 1
			);
		
		$fields['siteid'] = array(
			'LABEL'    => 'Site ID',
			'DB_FIELD' => 'stcn_site_id',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
			);
		
		$fields['controlid'] = array(
			'LABEL'    => 'Control ID',
			'DB_FIELD' => 'stcn_control_id',
			'SHOW_COLUMN'  => 1,
			'SHOW_FIELD'   => 1
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
    public function searchSiteID($site_id=null)
    {
	    $site_id          = (is_null($site_id)) ? wed_getSystemValue('SITE_ID') : $site_id;
	    $options['QUERY'] = $this->sql['SITE_JOIN'] ;
	    $options['WHERE'] = str_replace('%SITE_ID%',$site_id, $this->where['SITE_ID']);
	    $options['ORDER'] = $this->order['CONTROL_NAME'];
	    
	    return $this->search($options);
    }
    
    public function searchControlID($control_id,$site_id=null)
    {
	    $site_id          = (is_null($site_id)) ? wed_getSystemValue('SITE_ID') : $site_id;
	    $options['QUERY'] = $this->sql['SITE_JOIN'] ;
	    $options['WHERE'] = str_replace(array('%CONTROL_ID%','%SITE_ID%'), array($control_id,$site_id), $this->where['CONTROL_ID']);
	    $options['ORDER'] = $this->order['CONTROL_NAME'];
	    
	    return $this->search($options);
    }
    
    public function searchControlName($control_name,$site_id=null)
    {
	    $site_id          = (is_null($site_id)) ? wed_getSystemValue('SITE_ID') : $site_id;
	    $options['QUERY'] = $this->sql['SITE_JOIN'] ;
	    $options['WHERE'] = str_replace(array('%CONTROL_NAME%','%SITE_ID%'), array($control_name,$site_id), $this->where['CONTROL_NAME']);
	    $options['ORDER'] = $this->order['CONTROL_NAME'];
	    
	    return $this->search($options);
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
    // ********  buildSiteControlList produces a list of the accepted ****
    // ********  control codes for this site *****************************
    // *******************************************************************
    public function buildSiteControlList()
    {
	    $list = array();
	    
	    if ($this->searchSiteID())
	    {
		    $item = 0;
		
			while ($this->moveRecordList($item))
			{
				$list[$this->getValue('cnc_code')] = $this->getValue('cnc_id');
				$item++;
			}
	    }
	    
	    return $list;
    }
    
    // *******************************************************************
    // ********  getSITE produces the site title for this record *********
    // *******************************************************************
    public function getSITE()
    {
		return $this->getValue('site_title');
    }
    
    // *******************************************************************
    // ********  getTITLE produces the title for the control record ******
    // *******************************************************************
    public function getTITLE()
    {
		return $this->getValue('cnc_title');
    }
    
    // *******************************************************************
    // ** getDESCRIPTION produces the site description for this record ***
    // *******************************************************************
    public function getDESCRIPTION()
    {
		return $this->getValue('site_description');
    }
    
    // *******************************************************************
    // *****  getPAGE_TITLE produces the Page Title for this record ******
    // *******************************************************************
    public function getPAGE_TITLE()
    {
		$site_page_title    = $this->getSiteDetail('PAGE_TITLE','Official Web Page');
		$control_page_title = $this->getControlDetail('PAGE_TITLE');
		
		return (!is_null($control_page_title)) ? $control_page_title : $site_page_title;
    }
    
    // *******************************************************************
    // ********  getLink produces a LINK for this record *****************
    // *******************************************************************
    public function getLINK()
    {
	    // this returns the FULL url for the site plus the control code
	    return $this->getValue('site_url') . '/' . $this->getValue('cnc_code');
    }
    
    public function getSiteDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('site_details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
    
    public function getControlDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('cnc_details');
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
    // *****  getMENU_TITLE produces the Menu Title for this record ******
    // *******************************************************************
    public function getMENU_TITLE()
    {
		return $this->getDetail('MENU_TITLE',$this->getValue('site_title'));
    }
    
    // *******************************************************************
    // ********  getIMAGE_PATH produces a image path for a image *********
    // *******************************************************************
    public function getIMAGE_PATH($formats=array())
    {
	    // This is used to produce an image for the site when shown in a list
	    $image_path  = null;
	    $thumb_specs = array();
	    $sizes       = array();
	    
	    $details['IMAGE_PATH'] = $this->getSiteDetail('IMAGE_PATH');
	    $details['CATEGORY']   = $this->getSiteDetail('CATEGORY');
	    $details['IMAGE_SIZE'] = $this->getSiteDetail('IMAGE_SIZE');
	    
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
}
?>
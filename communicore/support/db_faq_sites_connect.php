<?php
ini_set( 'display_errors', 1 );
error_reporting( E_ALL );
/*
 * db_faq_sites_connect
 *
 * Database object for the online database faq_sites_connect which connects the
 * sites table and the faq_content
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_faq_sites_connect extends db_common
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
		$this->options['TABLE_NAME']     = 'faq_sites_connect';
		$this->options['TABLE_ID_FIELD'] = 'fqcn_id';
		$this->options['JOIN_TABLES']    = array('site_' => 'sites','faq_' => 'faq_content');
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
		
		// SQL statements
		$this->sql['SITE_JOIN'] = 'SELECT * FROM faq_sites_connect a 
			JOIN sites b ON b.site_id = a.fqcn_site_id  
			JOIN faq_content c ON a.fqcn_faq_id = c.faq_id';
		
		// Where statements
		$this->where['SITE_ID']         = ' WHERE a.fqcn_site_id = "%SITE_ID%"';
		$this->where['FAQ_ID']          = ' WHERE a.fqcn_faq_id = "%FAQ_ID%" AND a.fqcn_site_id = "%SITE_ID%"';
		$this->where['PUBLISHED']       = ' AND c.faq_status = "Publish"';
		$this->where['BY_CURRENT_SITE'] = ' AND b.site_id = "'.wed_getSystemValue('SITE_ID').'"';
		
		// Order statements
		$this->order['QUESTION_ORDER']  = ' ORDER BY a.fqcn_question_order';
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
			'DB_FIELD'  => 'fqcn_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'LABEL'     => 'Modification',
			'DB_FIELD'  => 'fqcn_modified',
			'NO_UPDATE' => 1
			);
		
		$fields['siteid'] = array(
			'LABEL'    => 'Site ID',
			'DB_FIELD' => 'fqcn_site_id',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['faqid'] = array(
			'LABEL'    => 'FAQ ID',
			'DB_FIELD' => 'fqcn_faq_id',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['order'] = array(
			'LABEL'    => 'FAQ Sort',
			'DB_FIELD' => 'fqcn_question_order',
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
    // ********  searchFaqKeywords()  ************************************
    // *******************************************************************
    public function searchFaqKeywords($keywords=array())
    {
	    if ((!is_array($keywords)) || (empty($keywords)))
	    {
		    return false;
	    }

	    $options['QUERY'] = $this->sql['SITE_JOIN'];
	    $where_array      = array();
	    
	    foreach ($keywords as $word)
	    {
		    $where_array[] = 'c.faq_keywords LIKE "%'.$word.'%"';
	    }
	    
	    $options['WHERE'] = ' WHERE '.implode(' AND ', $where_array).$this->where['PUBLISHED'].$this->where['BY_CURRENT_SITE'] ;
	    $options['ORDER'] = $this->order['QUESTION_ORDER'];
	    
	    return $this->search($options);
    }
    
    // *******************************************************************
    // ********  search()  ***********************************************
    // *******************************************************************
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
    // ********  getSITE produces the site title for this record *********
    // *******************************************************************
    public function getSITE()
    {
		return $this->getValue('site_title');
    }
    
    // *******************************************************************
    // ********  getQUESTION produces the question for the faq ***********
    // *******************************************************************
    public function getQUESTION()
    {
		return $this->getValue('faq_question');
    }
    
    // *******************************************************************
    // ** getANSWER produces the faq  ************************************
    // *******************************************************************
    public function getANSWER()
    {
		return $this->getValue('faq_answer');
    }
    
    public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('faq_details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
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
	    
	    $details['IMAGE_PATH'] = $this->getFaqDetail('IMAGE_PATH');
	    $details['CATEGORY']   = $this->getFaqDetail('CATEGORY');
	    $details['IMAGE_SIZE'] = $this->getFaqDetail('IMAGE_SIZE');
	    
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
    // ********  XCRUD Section *******************************************
    // *******************************************************************
    
    // *******************************************************************
    // ********  setupXCrud initial setup of XCrud Object ****************
    // *******************************************************************
    public function setupXCrud($code=null)
    {
	    // Based on the code, we can present different views of the content_main
	    // table with different settings.
	    if ($code=='faq_100')
	    {
		    $this->initXCrud();
		    $this->xcrud->relation('fqcn_site_id','sites','site_id','site_title');
		    $this->xcrud->relation('fqcn_faq_id','faq_content','faq_id','faq_question');
	    }
    }
}
?>
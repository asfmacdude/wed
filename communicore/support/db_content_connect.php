<?php
/*
 * db_content_connect
 *
 * Database object for the online database content_connect
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_content_connect extends db_common
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
		$this->options['TABLE_NAME']     = 'content_connect';
		$this->options['TABLE_ID_FIELD'] = 'cnn_id';
		$this->options['JOIN_TABLES']    = array('cnt_' => 'content_main','cnc_' => 'content_control','cng_' => 'content_groups','ctp_' => 'content_types','site_' => 'sites');
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
		
		/*
		 * SQL array
		 *
		 * This array holds preset configurations for some very complex JOINS. Instead of typing them again and again in different places
		 * we can select the one we want.
		 *
		 * CONTENT_JOIN is a normal JOIN that must find connections or results in all the JOINed tables. This helps us in many ways filter
		 * out unwanted stuff like for instance on searches where we don't want to list SNIPPET content.
		 *
		 * CONTENT_OUTER_JOIN is similar to the CONTENT_JOIN except we use OUTER JOINS to not require that there be a connection in
		 * the other tables. It does require that there is a connection in the content_types table, but this should be normal.
		 *
		 * CONNECT_JOIN focuses on the content_connect table. This is good for searches and generated lists when you are looking for
		 * all the records in a certain group or a certain control code such as all videos or news. It also adds the sites table
		 * so that we can make sure we are only gathering data for a particular site.
		 *
		 */
		$this->sql['CONTENT_JOIN'] = 'SELECT * FROM content_connect a JOIN content_main b ON a.cnn_content_id = b.cnt_id JOIN content_control c ON a.cnn_control_id = c.cnc_id JOIN content_groups d ON a.cnn_group_id = d.cng_id JOIN content_types e ON e.ctp_id = b.cnt_type_id';
		
		$this->sql['CONTENT_OUTER_JOIN'] = 'SELECT * FROM content_main a JOIN content_types e ON e.ctp_id = a.cnt_type_id LEFT OUTER JOIN content_connect b ON a.cnt_id = b.cnn_content_id LEFT OUTER JOIN content_control c ON c.cnc_id = b.cnn_control_id LEFT OUTER JOIN content_groups d ON d.cng_id = b.cnn_group_id';
		
		$this->sql['CONNECT_JOIN'] = 'SELECT * FROM content_connect a JOIN content_control c ON a.cnn_control_id = c.cnc_id JOIN content_main b ON a.cnn_content_id = b.cnt_id JOIN content_groups d ON a.cnn_group_id = d.cng_id JOIN content_types e ON e.ctp_id = b.cnt_type_id JOIN sites s ON s.site_id = a.cnn_site_id';
		
		// Where statements
		// Search content_connect
		$this->where['CNN_CONTROL_ID']  = ' WHERE a.cnn_control_id = "%CONTROL_ID%" AND a.cnn_primary_group = "Y"';
		$this->where['CNN_CONTENT_ID']  = ' WHERE a.cnn_content_id = "%CONTENT_ID%"';
		$this->where['CNN_GROUP_ID']    = ' WHERE a.cnn_group_id = "%GROUP_ID%"';
		$this->where['FEATURE']         = ' WHERE a.cnn_feature_type = "%FEATURE%" AND a.cnn_feature_start_date <= "%DATE%" AND a.cnn_feature_end_date > "%DATE%"';
		
		// ADD search by site_id
		$this->where['BY_CURRENT_SITE'] = ' AND s.site_id = "'.wed_getSystemValue('SITE_ID').'"';
		
		// Search by content id or code
		$this->where['CONTENT_ID']      = ' WHERE a.cnt_id = :id';
		$this->where['CONTENT_CODE']    = ' WHERE a.cnt_code = :code';
		
		// Order statements
		$this->order['TITLE']      = ' ORDER BY b.cnt_title';
		$this->order['CNN_SORT']   = ' ORDER BY a.cnn_sort ASC';
	}
	
	private function setFields()
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
		$today_date = wed_getDateToday();
		
		$fields['id'] = array(
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'cnn_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'TITLE'     => 'Modification',
			'DB_FIELD'  => 'cnn_modification',
			'NO_UPDATE' => 1
			);
		
		$fields['siteid'] = array(
			'TITLE'    => 'Site ID',
			'DB_FIELD' => 'cnn_site_id'
			);
		
		$fields['controlid'] = array(
			'TITLE'    => 'Control ID',
			'DB_FIELD' => 'cnn_control_id'
			);
			
		$fields['contentid'] = array(
			'TITLE'    => 'Content ID',
			'DB_FIELD' => 'cnn_content_id'
			);
		
		$fields['groupid'] = array(
			'TITLE'    => 'Group ID',
			'DB_FIELD' => 'cnn_group_id'
			);
			
		$fields['sort'] = array(
			'TITLE'    => 'Sort by',
			'DB_FIELD' => 'cnn_sort',
			'DEFAULT'  => 1
			);
			
		$fields['primary'] = array(
			'TITLE'    => 'Primary Group?',
			'DB_FIELD' => 'cnn_primary_group',
			'DEFAULT'  => 'N'
			);
			
		$fields['linktype'] = array(
			'TITLE'    => 'Primary Group?',
			'DB_FIELD' => 'cnn_link_type',
			'DEFAULT'  => 'Group'
			);
			
		$fields['feature'] = array(
			'TITLE'    => 'Feature Type',
			'DB_FIELD' => 'cnn_feature_type',
			'DEFAULT'  => 'none'
			);
			
		$fields['start'] = array(
			'TITLE'    => 'Start Date',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The start date is a required field',
			'DB_FIELD' => 'cnn_feature_start_date',
			'DEFAULT'  => $today_date
			);
			
		$fields['end'] = array(
			'TITLE'    => 'End Date',
			'DB_FIELD' => 'cnn_feature_end_date',
			'DEFAULT'  => null
			);
			
		$fields = $this->joinFields($fields);
			
		return $fields;
	}
	
	
	// *******************************************************************
    // ********  getContent **********************************************
    // *******************************************************************
	/*
	 * This will replace all getArticle functions
	 *
	 *
	 */
	public function getContent($options=array())
	{
		/*
		 * Options
		 *
		 * We are looking at three things here:
		 * CODE
		 * ID
		 * CODE_LIST
		 *
		 */
		$code      = (isset($options['CODE'])) ? $options['CODE'] : null ;
		$id        = (isset($options['ID'])) ? $options['ID'] : null ;
		$code_list = (isset($options['CODE_LIST'])) ? $options['CODE_LIST'] : null ;
		$sql       = $this->sql['CONTENT_OUTER_JOIN'];
		$data      = array();
		$all       = false;
		
		if (!is_null($code))
		{
			$sql .= $this->where['CONTENT_CODE'];
			$data = array(':code' => $code);
			$all  = false;
		}
		elseif (!is_null($id))
		{
			$sql .= $this->where['CONTENT_ID'];
			$data = array(':id' => $id);
			$all  = false;
		}
		elseif (!is_null($code_list))
		{
			$where_arr = array();
			$all       = true;
			
			dbug($code_list);
			
			foreach ($code_list as $code)
			{
				$where_arr[] = 'a.cnt_code = "'.$code.'"';
			}
		
			$sql .= ' WHERE '.implode(' OR ', $where_arr);
		}
		
		dbug($sql);
				
		$data  = $this->dbExecute($sql,$data,$all);
		
		if ($data)
		{
			$this->addValues_Data($data);
			
			if ($all)
			{
				/*
				 * NOTE: Weird thing happens when you do a SELECT with no ORDER
				 * Records come back the way they were inserted which will not always be
				 * what you want. Thus, I had to write the lines below to make the SQL data
				 * reflect the order of the code_list.
				 *
				 */
				foreach ($code_list as $code)
				{
					foreach ($data as $record)
					{
						if ($record['cnt_code'] == $code)
						{
							$this->record_list[] = $record;
							break;
						}
					}
				}
			}
			else
			{
				$this->record_list[] = $data;
			}
		}
		
		return (!$data) ? false : true ;
	}
	
	
	
	// *******************************************************************
    // ********  getArticle - replaces screenwriter **********************
    // *******************************************************************
	public function getArticle($options=array())
    {
    	$code   = (isset($options['ARTICLE_CODE'])) ? $options['ARTICLE_CODE'] : null ;
		$id     = (isset($options['ARTICLE_ID'])) ? $options['ARTICLE_ID'] : null ;
		$sql    = $this->sql['CONTENT_OUTER_JOIN'];
		
		if (!is_null($code))
		{
			$sql .= $this->where['CONTENT_CODE'];
			$data = array(':code' => $code);
		}
		elseif (!is_null($id))
		{
			$sql .= $this->where['CONTENT_ID'];
			$data = array(':id' => $id);
		}
		else
		{
			return null;
		}
		
		$data  = $this->dbExecute($sql,$data,$all=false);
		
		if ($data)
		{
			$this->addValues_Data($data);
		}
		
		return (!$data) ? false : true ;
    }
    
    
    // *******************************************************************
    // **  getArticlePrefix - gets a list of articles based on a prefix **
    // *******************************************************************
	public function getArticlePrefix($options=array())
    {
    	$code      = (isset($options['CODE'])) ? $options['CODE'] : null ;
		$sql       = $this->sql['CONTENT_OUTER_JOIN'];
		$data      = array();
		
		$where = 'WHERE a.cnt_code LIKE "%'.$code.'%"';
		
		$data  = $this->dbExecute($sql,$data,$all=true);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : true ;
    }
	
	// *******************************************************************
    // ********  search()  ***********************************************
    // *******************************************************************
    public function searchControl($control_id,$by_site=true)
    {
	    $options['QUERY'] = $this->sql['CONNECT_JOIN'] ;
	    $options['WHERE'] = str_replace('%CONTROL_ID%', $control_id, $this->where['CNN_CONTROL_ID']);
	    // $options['WHERE'] = ($by_site) ? $options['WHERE'].$this->where['BY_CURRENT_SITE'] : $options['WHERE'];
	    $options['ORDER'] = $this->order['TITLE'];
	    
	    return $this->search($options);
    }
    
    public function searchContent($content_id,$by_site=true)
    {
	    $options['QUERY'] = $this->sql['CONNECT_JOIN'] ;
	    $options['WHERE'] = str_replace('%CONTENT_ID%', $content_id, $this->where['CNN_CONTENT_ID']);
	    // $options['WHERE'] = ($by_site) ? $options['WHERE'].$this->where['BY_CURRENT_SITE'] : $options['WHERE'];
	    $options['ORDER'] = $this->order['TITLE'];
	    
	    return $this->search($options);
    }
    
    public function searchContentKeywords($keyword_string,$by_site=true)
    {
	    $keyword_string = trim($keyword_string);
		    
		// split $content by spaces
		$search_words  = explode(' ', $keyword_string);
		$searches = array();
		    
		foreach ($search_words as $item)
		{
			$item = wed_cleanItUp($item,'SEARCH');
			$searches[] = 'b.cnt_search_keywords LIKE "%'.$item.'%"';
		}
		
		$where_str = ' WHERE ';
		$where_str .= implode(' OR ', $searches);
	    
	    $options['QUERY'] = $this->sql['CONNECT_JOIN'] ;
	    $options['WHERE'] = $where_str;
	    // $options['WHERE'] = ($by_site) ? $options['WHERE'].$this->where['BY_CURRENT_SITE'] : $options['WHERE'];
	    $options['ORDER'] = $this->order['TITLE'];
	    
	    return $this->search($options);
    }
    
    public function searchGroup($group_id)
    {
	    $options['QUERY'] = $this->sql['CONNECT_JOIN'] ;
	    $options['WHERE'] = str_replace('%GROUP_ID%', $group_id, $this->where['CNN_GROUP_ID']);
	    $options['ORDER'] = $this->order['CNN_SORT'];
	    
	    return $this->search($options);
    }
    
    public function searchFeature($feature,$date=null)
    {
	    $date = (is_null($date)) ? wed_getDateTodaySQL() : $date;
	    $options['QUERY'] = $this->sql['CONNECT_JOIN'] ;
	    $options['WHERE'] = str_replace(array('%FEATURE%','%DATE%'), array($feature,$date), $this->where['FEATURE']);
	    $options['WHERE'] = $options['WHERE'].$this->where['BY_CURRENT_SITE'];
	    $options['ORDER'] = $this->order['CNN_SORT'];
	    
	    return $this->search($options);
    }
    
    private function search($options)
    {
	    $query = $options['QUERY'] . $options['WHERE'] . $options['ORDER'];
	    	    
	    $data = $this->dbAll($query);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;
    }
	
	
	
	
	
	public function selectByControlJoinGroups($control_id)
	{
		$query = $this->sql['CONNECT_JOIN'] . ' WHERE a.cnn_control_id = "'.$control_id . '" AND a.cnn_primary_group = "Y" ORDER BY b.cnt_title';
		
		$data = $this->dbAll($query);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;	
	}
	
	public function selectByContentJoinGroups($content_id)
	{
		$query = $this->sql['CONTENT_JOIN'] . ' WHERE a.cnn_content_id = "'.$content_id . '"';
		
		$data = $this->dbAll($query);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;	
	}
	
	public function selectByGroupJoinContent($group_id)
	{
		$query = $this->sql['CONTENT_JOIN'] . ' WHERE a.cnn_group_id = "'.$group_id . '" ORDER BY a.cnn_sort ASC';
		
		$data = $this->dbAll($query);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;	
	}
	
	public function selectByControlGroup($control=null,$group=null)
    {
        if (is_null($control))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 
			'controlid'  => $control,
			'groupid'    => $group 
		);
		
		$order_str = ' ORDER BY '.$this->options['FIELDS']['sort']['DB_FIELD'] . ' ASC';
		
		return $this->selectByPairs($pairs, $order_str); 
    }
    
    public function selectByControl($control=null)
    {
        if (is_null($control))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 
			'controlid'  => $control 
		);
		
		// Cannot sort just ids, you will have to do a join and get the title or name from
		// another table to sort by
		// $order_str = ' ORDER BY '.$this->options['FIELDS']['sort']['DB_FIELD'] . ' ASC';
		
		return $this->selectByPairs($pairs, $order_str); 
    }
    
    public function selectByContentPrimary($content=null)
    {
        if (is_null($content))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 
			'contentid'  => $content,
			'primary'    => 'Y' 
		);
		
		return $this->selectByPairs($pairs, null, false); 
    }
    
    public function selectByContent($content=null)
    {
        if (is_null($content))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 
			'contentid'  => $content
		);
		
		// Cannot sort just ids, you will have to do a join and get the title or name from
		// another table to sort by
		// $order_str = ' ORDER BY '.$this->options['FIELDS']['sort']['DB_FIELD'] . ' ASC';
		
		return $this->selectByPairs($pairs, $order_str); 
    }
    
    public function selectByGroup($group=null)
    {
        if (is_null($group))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 
			'groupid'  => $group
		);
		
		$order_str = ' ORDER BY '.$this->options['FIELDS']['sort']['DB_FIELD'] . ' ASC';
		
		return $this->selectByPairs($pairs, $order_str); 
    }
    
    // *******************************************************************
    // ********  getTITLE produces a description for this record ***
    // *******************************************************************
    public function getTITLE()
    {
		return $this->getValue('cnt_title');
    }
    
    // *******************************************************************
    // *****  getFULLARTICLE produces the full content for this record ***
    // *******************************************************************
    public function getFULLARTICLE()
    {
		return $this->getValue('cnt_fullarticle');
    }
    
    // *******************************************************************
    // *****  getEXCERPT produces the excerpt content for this record ***
    // *******************************************************************
    public function getEXCERPT()
    {
		return $this->getValue('cnt_excerpt');
    }
    
    // *******************************************************************
    // *****  getSNIPPET only returns the content with NO Title **********
    // *******************************************************************
    public function getSNIPPET()
    {
		return $this->getValue('cnt_fullarticle');
    }
    
    // *******************************************************************
    // *****  getTAB_HEADER produces the tab header for this record ***
    // *******************************************************************
    public function getTAB_HEADER()
    {
		return $this->getDetail('TAB_HEADER',$this->getValue('cnt_title'));
    }
    
    // *******************************************************************
    // *****  getMENU_TITLE produces the Menu Title for this record ******
    // *******************************************************************
    public function getMENU_TITLE()
    {
		return $this->getDetail('MENU_TITLE',$this->getValue('cnt_title'));
    }
    
    // *******************************************************************
    // *****  getTYPE produces the type for this record ******
    // *******************************************************************
    public function getTYPE()
    {
		return $this->getDetail('TYPE','link');
    }
    
    // *******************************************************************
    // ********  getDescription produces a description for this record ***
    // *******************************************************************
    public function getDESCRIPTION()
    {
	    return $this->getValue('cnt_excerpt');
    }
    
    // *******************************************************************
    // ********  getIMAGE_PATH produces a image path for a image *********
    // *******************************************************************
    public function getIMAGE_PATH($formats=array())
    {
	    $image_path  = null;
	    $thumb_specs = array();
	    $sizes       = array();
	    
	    $details['IMAGE_PATH'] = $this->getDetail('IMAGE_PATH');
	    $details['CATEGORY']   = $this->getDetail('CATEGORY');
	    $details['SIZE']       = $this->getDetail('SIZE');
	    
	    if (isset($formats['IMAGE_SIZE']))
	    {
		    $sz = explode('_', $formats['IMAGE_SIZE']);
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
	    else
	    {
		    $group = $this->getValue('cng_sysname');
	    
			$options['CATEGORY'] = (is_null($details['CATEGORY'])) ? $group : $details['CATEGORY'];
			$options['SIZE']     = (is_null($details['SIZE'])) ? '1200_500' : $details['SIZE']; 
			$img_obj = wed_getImageObject($options);
		
			$thumb_specs['SOURCE'] = $img_obj->getRandomFilePath();
			$thumb_specs['ZOOM_CROP'] = 1;
			$thumb_specs['WIDTH']     = ( (isset($sizes['WIDTH'])) && (!is_null($sizes['WIDTH'])) ) ? $sizes['WIDTH'] : null;
			$thumb_specs['HEIGHT']    = ( (isset($sizes['HEIGHT'])) && (!is_null($sizes['HEIGHT'])) ) ? $sizes['HEIGHT'] : null;
			
			$image_path = $img_obj->getFileThumbPath($thumb_specs);
	    }
	    
		return $image_path;
    }
    
    // *******************************************************************
    // ********  getLink produces a LINK for this record *****************
    // *******************************************************************
    public function getLINK()
    {
	    $current_site_id = wed_getSystemValue('SITE_ID');
	    $record_site_id  = $this->getValue('cnn_siteid');
	    $type            = $this->getTYPE();
	    
	    $link = ($record_site_id===$current_site_id) ? null : $this->getValue('site_url');
	    
	    if ($this->getValue('linktype')=='Article')
	    {
		    // link is /control/content_code
		    $link .= '/' . $this->getValue('cnc_code') . '/' . $this->getValue('cnt_code');
	    }
	    elseif ($this->getValue('primary')=='Y')
	    {
		    // link is /control/group
		    $link .= '/' . $this->getValue('cnc_code') . '/' . $this->getValue('cng_sysname');
	    }
	    else
	    {
		    // link is /control/group/id/title
		    $link .= '/' . $this->getValue('cnc_code') . '/' . $this->getValue('cng_sysname') . '/' . $this->getValue('cnt_id') . '/' . wed_cleanURL($this->getValue('cnt_title'));
	    }
	    
	    return $link;
    }
    
    // *******************************************************************
    // ********  getOBJECT_Link produces a LINK for this record *****************
    // *******************************************************************
    public function getOBJECT_LINK()
    {
	    $type = $this->getTYPE();
	    $link = null;
	    
	    if ($type=='video')
	    {
		    $link = $this->getDetail('VIDEO_LINK');
	    }
	    elseif ($type=='image')
	    {
		    $link = $this->getDetail('IMAGE_LINK');
	    }
	    elseif ($type=='extlink')
	    {
		    $link = $this->getDetail('EXT_LINK');
	    }
	    else
	    {
		    $link = $this->getLINK();
	    }
	    
	    return $link;
    }
    
    // *******************************************************************
    // ********  getITEM_DATE produces a DATE for this record **********
    // *******************************************************************
    public function getITEM_DATE()
    {
	    return $this->getValue('cnt_published');
    }
    
    // *******************************************************************
    // ********  getITEM_CATEGORY produces a LINK for this record **********
    // *******************************************************************
    public function getITEM_CATEGORY()
    {
	    $catg = $this->getValue('cng_sysname');
	    $this->options['CATEGORY_LIST'][$catg] = $this->getValue('cng_title');
	    return $catg;
    }
    
    public function getITEM_CATEGORIES_LIST()
    {
	    $html = null;
	    $tmpl = '<li><a href="#" data-filter=".%s">%s</a></li>';
	    
	    if (!empty($this->options['CATEGORY_LIST']))
	    {
		    foreach ($this->options['CATEGORY_LIST'] as $sysname=>$title)
		    {
			    $html .= sprintf($tmpl,$sysname,$title);
		    }
	    }
	    
	    return $html;
    }
    
    public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('cnt_details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
    
    // *******************************************************************
    // ********  getTags produces a set of tags for this record **********
    // *******************************************************************
    public function getTAGS()
    {
	    $keywords = $this->getValue('cnt_keywords');
	    return explode(',', $keywords); // return an array
    }
}
?>
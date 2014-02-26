<?php
/*
 * db_events_connect
 *
 * Database object for the online database events_connect
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_events_connect extends db_common
{
	public $options;
	public $db;
	public $sql;
	
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
		$this->options['TABLE_NAME']     = 'events_connect';
		$this->options['TABLE_ID_FIELD'] = 'evct_id';
		$this->options['JOIN_TABLES']    = array('evnt_' => 'events','actv_' => 'events_activity','cng_' => 'content_groups','rslt_' => 'events_results');
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
		
		$this->sql['ACTIVITY_JOIN'] = 'SELECT * FROM events_connect a JOIN events b ON a.evct_event_id = b.evnt_id JOIN events_activity c ON a.evct_event_activity_id = c.actv_id JOIN content_groups d ON a.evct_group_id = d.cng_id';
		
		$this->sql['RESULTS_JOIN'] = 'SELECT * FROM events_connect a JOIN events b ON a.evct_event_id = b.evnt_id JOIN events_activity c ON a.evct_event_activity_id = c.actv_id JOIN content_groups d ON a.evct_group_id = d.cng_id JOIN events_results e ON a.evct_event_activity_id=e.rslt_event_activity_id';
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
			'DB_FIELD'  => 'evct_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'TITLE'     => 'Modification',
			'DB_FIELD'  => 'evct_modification',
			'NO_UPDATE' => 1
			);
		
		$fields['eventid'] = array(
			'TITLE'    => 'Event ID',
			'DB_FIELD' => 'evct_event_id'
			);
			
		$fields['activityid'] = array(
			'TITLE'    => 'Activity ID',
			'DB_FIELD' => 'evct_event_activity_id'
			);
		
		$fields['groupid'] = array(
			'TITLE'    => 'Group ID',
			'DB_FIELD' => 'evct_group_id'
			);
			
		if ($join)
		{
			$fields = $this->joinFields($fields);
		}
			
		return $fields;
	}
	
	public function selectByGroupJoinEventsResults($options)
	{
		$group_id  = (isset($options['GROUP_ID'])) ? $options['GROUP_ID'] :  null ;
		$group     = (isset($options['GROUP'])) ? $options['GROUP'] :  null ;
		$year      = (isset($options['YEAR'])) ? $options['YEAR'] :  null ;
		
		if (is_null($group_id))
		{
			return null;
		}
		
		$where_str = ' WHERE a.evct_group_id = "'.$group_id.'"';
		
		if (!is_null($year))
		{
			$where_str .= ' AND b.evnt_year = "'.$year.'"';
		}
		
		$query = $this->sql['RESULTS_JOIN'] . $where_str . ' ORDER BY e.rslt_year DESC,e.rslt_sport,e.rslt_event,e.rslt_results';
		
		$data = $this->dbAll($query);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;	
	}
	
	public function selectByGroupJoinEvents($group_id)
	{
		$query = $this->sql['ACTIVITY_JOIN'] . ' WHERE a.evct_group_id = "'.$group_id . '" ORDER BY c.actv_title ASC';
		
		$data = $this->dbAll($query);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;	
	}

    
    public function selectByEvent($event=null)
    {
        if (is_null($content))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 
			'eventtid'  => $content
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
		
		$order_str = null;
		
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
    // ********  getDescription produces a description for this record ***
    // *******************************************************************
    public function getDESCRIPTION()
    {
	    $excerpt      = $this->getValue('cnt_excerpt');
		$fullarticle  = $this->getValue('cnt_fullarticle');
		$description  = (!empty($excerpt)) ? $excerpt : substr($fullarticle, 0, 150);
		$description  = strip_tags($description);
		return $description;
    }
    
    // *******************************************************************
    // ********  getLink produces a LINK for this record *****************
    // *******************************************************************
    public function getLINK()
    {
	    if ($this->getValue('linktype')=='Article')
	    {
		    // link is article
		    $link = '/' . $this->getValue('cnc_code') . '/' . $this->getValue('cnt_code');
	    }
	    elseif ($this->getValue('primary')=='Y')
	    {
		    // link is control/group
		    $link = '/' . $this->getValue('cnc_code') . '/' . $this->getValue('cng_sysname');
	    }
	    else
	    {
		    // link is control/group/code
		    $link = '/' . $this->getValue('cnc_code') . '/' . $this->getValue('cng_sysname') . '/' . $this->getValue('cnt_id') . '/' . wed_cleanURL($this->getValue('cnt_title'));
	    }
	    
	    return $link;
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
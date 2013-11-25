<?php
/*
 * @version		$Id: connect_list_detail.php 1.0 2009-03-03 $
 * @package		DreamWish
 * @subpackage	main
 * @copyright	Copyright (C) 2012 Medley Productions. All rights reserved.
 * 
 * DreamWish is a Disney inspired CMS system developed by Randy Cherry
 * Dedicated to the dreamer of dreams, Walt Disney
 * 
 * 'I believe in being an innovator.' - Walt Disney
 * 
 * 
 */
defined( '_GOOFY' ) or die();
/*
 * connect_list_detail.php
 * 
 */

class connect_list_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']        = __CLASS__;
		$this->options['LOCAL_PATH']        = dirname(__FILE__);
		$this->options['TABLE']             = 'content_connect'; //
		$this->options['DETAIL_ID']         = null; // this is assigned by List Manager
		$this->options['LIST_OBJECT']       = null; // The actual list db object is loaded here
		
		// Search Options
		$this->options['SEARCH']            = null;  // Search types: group
		$this->options['TITLE']             = null;  // Search by TITLE
		$this->options['GROUP']             = $this->setGroupFromURL();  // Search by GROUP
		$this->options['GROUP_ID']          = null;  // Search by GROUP_ID
		$this->options['CONTROL']           = null;  // Search by CONTROL
		$this->options['CONTROL_ID']        = null;  // Search by CONTROL_ID
		$this->options['CODE']              = null;  // Search by CODE
		$this->options['TAG']               = false; // Search by TAG
		$this->options['KEYWORD']           = false; // Search by KEYWORD
		$this->options['TYPE_ID']           = false; // Search by TYPE_ID
		
		$this->addOptions($options);
	}
	
	// Set Group from the URL. It still can be replaced by incoming options
	private function setGroupFromURL()
	{
		$call_parts = wed_getSystemValue('CALL_PARTS');		
		return (isset($call_parts[1])) ? $call_parts[1] : null;
	}
	
	private function setGroupID()
	{
		if (is_null($this->options['GROUP']))
		{
			$this->setGroupFromURL();
		}
			
		if ( (is_null($this->options['GROUP_ID'])) && (!is_null($this->options['GROUP'])) )
		{
			// Get Group ID
			$group_db = wed_getDBObject('content_groups');
			$this->options['GROUP_ID'] = $group_db->getGroupID($this->options['GROUP']);
		}
	}
	
	private function loadList_GROUP()
	{
		$status     = false;
		$this->setGroupID();

		$connect_db = wed_getDBObject('content_connect');
		
		// Run JOIN here that joins connect,content and groups
		if ( (!is_null($this->options['GROUP_ID'])) && ($connect_db->selectByGroupJoinContent($this->options['GROUP_ID'])) )
		{
			$this->options['LIST_OBJECT'] = $connect_db;
			$status = true;
		}
		
		return $status;
	}
	
	public function setHTML($options=array())
	{
		// Here we return a db object that is loaded with the list
		// Even though we use the call getHTML to get it because that
		// is the norm across all detail objects
		$list_object = null;
		
		if (!is_null($this->options['SEARCH']))
		{
			$method = 'loadList_'.strtoupper($this->options['SEARCH']);
			
			if ( (method_exists($this,$method)) && call_user_func_array(array($this,$method), array()) )
			{
				$list_object = $this->options['LIST_OBJECT'];
			}
		}
		
		return $list_object;
	}
}

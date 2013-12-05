<?php
/*
 * @version		$Id: content_list_detail.php 1.0 2009-03-03 $
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
 * content_list_detail.php
 * 
 */

class content_list_detail extends details
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
		$this->options['TABLE']             = 'content_main'; //
		$this->options['DETAIL_ID']         = null; // this is assigned by List Manager
		$this->options['LIST_OBJECT']       = null; // The actual list db object is loaded here
		$this->options['ORDER']             = 'title';
		
		// Search Options
		$this->options['SEARCH']            = null;  // Search types: tagid
		$this->options['TITLE']             = null;  // Search by TITLE
		$this->options['ID']                = null;  // Search by ID
		$this->options['CODE']              = null;  // Search by CODE
		$this->options['TAG']               = false; // Search by TAG
		$this->options['KEYWORD']           = false; // Search by KEYWORD
		$this->options['KEYS']              = false; // Search by SYSTEM KEYS
		$this->options['TYPE_ID']           = false; // Search by TYPE_ID
		
		$this->addOptions($options);
	}
	
	public function loadList_TAGID($args=array())
	{
		$status     = false;
		$content_db = wed_getDBObject('content_main');
		
		$options['TAG'] = $this->options['TAG'];
		$options['ID']  = $this->options['TYPE_ID'];
		
		if ($content_db->selectByTagID($options))
		{
			$this->options['LIST_OBJECT'] = $content_db;
			$status = true;
		}
		
		return $status;
	}
	
	public function loadList_CODE_PREFIX($args=array())
	{
		$status     = false;
		$content_db = wed_getDBObject('content_main');
		
		$options['CODE']  = $this->options['CODE'];
		$options['ORDER'] = $this->options['ORDER'];
		
		if ($content_db->selectByCodePrefix($options))
		{
			$this->options['LIST_OBJECT'] = $content_db;
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

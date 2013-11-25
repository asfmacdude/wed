<?php
/*
 * @version		$Id: events_results_list_detail.php 1.0 2009-03-03 $
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
 * events_results_list_detail.php
 * 
 */

class events_results_list_detail extends details
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
		$this->options['TABLE']             = 'events_results'; //
		$this->options['DETAIL_ID']         = null; // this is assigned by List Manager
		$this->options['LIST_OBJECT']       = null; // The actual list db object is loaded here
		
		// Search Options
		$this->options['SEARCH']            = null;  // Search types: sport
		$this->options['SPORT']             = null;  // Search by SPORT
		$this->options['SPORT_ID']          = null;  // Search by SPORT ID
		$this->options['YEAR']              = null;  // Search by YEAR
		$this->options['EVENT']             = null;  // Search by EVENT
		
		$this->addOptions($options);
	}
	
	public function loadList_SPORT($args=array())
	{
		$status     = false;
		$events_results_db = wed_getDBObject('events_results');
		
		$options['SPORT_ID'] = $this->options['SPORT_ID'];
		$options['YEAR']     = $this->options['YEAR'];
		
		if ($events_results_db->selectByGroupJoinEventsResults($group_id,$year=null))
		{
			$this->options['LIST_OBJECT'] = $events_results_db;
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

<?php
/*
 * @version		$Id: results_list_detail.php 1.0 2009-03-03 $
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
 * results_list_detail.php
 * 
 */

class results_list_detail extends details
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
		$this->options['TABLE']             = 'events_connect'; //
		$this->options['DETAIL_ID']         = null; // this is assigned by List Manager
		$this->options['LIST_OBJECT']       = null; // The actual list db object is loaded here
		
		// Search Options
		$this->options['SEARCH']            = null;  // Search types: sport
		$this->options['GROUP']             = null;  // Search by GROUP
		$this->options['GROUP_ID']          = null;  // Search by GROUP ID
		$this->options['YEAR']              = null;  // Search by YEAR
		$this->options['EVENT']             = null;  // Search by EVENT
		
		$this->addOptions($options);
	}
	
	public function loadList()
	{
		$status     = false;
		$results_db = wed_getDBObject($this->options['TABLE']);
		
		$options['GROUP']    = $this->options['GROUP'];
		$options['GROUP_ID'] = $this->options['GROUP_ID'];
		$options['YEAR']     = $this->options['YEAR'];
		
		if ($results_db->selectByGroupJoinEventsResults($options))
		{
			$this->options['LIST_OBJECT'] = $results_db;
			$status = true;
		}
		
		return $status;
	}
	
	public function setHTML($options=array())
	{
		return ($this->loadList()) ? $this->options['LIST_OBJECT'] : null;
	}
}

<?php
/*
 * @version		$Id: schedule_detail.php 1.0 2009-03-03 $
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
 * schedule_detail.php
 * 
 */

include_once('time_tools.php');

class schedule_detail extends time_tools
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
		$this->options['NAME']              = null; // name of the event
		$this->options['CODE']              = null; // code of the event
		$this->options['SCHEDULE_DB']       = null; // holds the actual db object
		$this->options['SCHEDULE_ID']       = null; // holds the ID of the db record
		$this->options['SCHEDULE_ACTIVE']   = null; // Y or N for whether the schedule is active
		$this->options['SCHEDULE_START']    = null; // Start date
		$this->options['SCHEDULE_END']      = null; // End date
		$this->options['SCHEDULE_NOW']      = new DateTime('now'); // Now, right now
		
		$this->addOptions($options);
	}
	
	private function setupSchedule()
	{
		if ( (!$this->loadSchedule()) || ($this->options['SCHEDULE_ACTIVE']!='Y') )
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	/*
	 * loadSchedule
	 *
	 * This function loads the initial setup for the schedule and transfers
	 * the needed values into $this->options. Obviously, if this doesn't happen,
	 * nothing else will proceed.
	 *
	 */
	private function loadSchedule()
	{
		$status   = (!is_null($this->options['SCHEDULE_DB']));
		$setup_db = wed_getDBObject('wed_schedules');
		
		if ( (is_null($this->options['SCHEDULE_DB'])) && ($setup_db->getRecordByName($this->options['NAME'])) )
		{		
			$status = true;
		}
		elseif ( (is_null($this->options['SCHEDULE_DB'])) && ($setup_db->getRecordByCode($this->options['CODE'])) )
		{
			$status = true;
		}
		
		if ($status)
		{
			$this->options['SCHEDULE_DB']         = $setup_db;
			$this->options['SCHEDULE_ID']         = $setup_db->getValue('id');
			$this->options['SCHEDULE_ACTIVE']     = $setup_db->getValue('active');
			$this->options['SCHEDULE_START']      = $setup_db->getValue('start');
			$this->options['SCHEDULE_END']        = $setup_db->getValue('end');
		}
		
		return $status;
	}
	
	public function runSchedule()
	{
		$run = false;
		
		if ($this->setupSchedule())
		{
			$start = new DateTime($this->options['SCHEDULE_START']);
			$end   = new DateTime($this->options['SCHEDULE_END']);
			$now   = $this->options['SCHEDULE_NOW'];
		
			$run = ( ( ($now>$start) || ($now==$start) ) && ( ($now==$end) || ($now<$end)) );
		}
		
		return $run;	
	}
	
	public function printSchedule($prefix=null)
	{
		$html = '';
		
		if ($this->setupSchedule())
		{
			// An example of the prefix might be 'This schedule runs '
			$start = new DateTime($this->options['SCHEDULE_START']);
			$end   = new DateTime($this->options['SCHEDULE_END']);
			$html  = $prefix . $this->getDayDateTime($start) . ' through ' . $this->getDayDateTime($end);
		}
		
		return $html;
	}
	
	public function printStart($prefix=null)
	{
		$html = '';
		
		if ($this->setupSchedule())
		{
			// An example of the prefix might be 'Registratin begins '
			$start = new DateTime($this->options['SCHEDULE_START']);
			$html  = $prefix . $this->getDayDateTime($start);
		}
		
		return $html;
	}
	
	public function printDeadline($prefix=null)
	{
		$html = '';
		
		if ($this->setupSchedule())
		{
			// An example of the prefix might be 'The deadline is '
			$end   = new DateTime($this->options['SCHEDULE_END']);
			$html  = $prefix . $this->getDayDateTime($end);
		}
		
		return $html;
	}
	
	public function printTimeLeft($prefix=null)
	{
		$html = '';
		
		if ($this->setupSchedule())
		{
			// An example of the prefix might be 'Registration ends in '
			$start = $this->options['SCHEDULE_NOW'];
			$end   = new DateTime($this->options['SCHEDULE_END']);
			$html  = $prefix . $this->getDateDifference($start,$end);
		}
		
		return $html;
	}
	
	public function printToday($prefix=null)
	{
		$html = '';
		
		if ($this->setupSchedule())
		{
			// An example of the prefix might be 'Today is '
			$end   = $this->options['SCHEDULE_NOW'];
			$html  = $prefix . $this->getDayDate($end);
		}
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		return null;
	}
}
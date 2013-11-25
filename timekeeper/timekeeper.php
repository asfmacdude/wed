<?php

/*
 * @version		$Id: TimeKeeper.php 1.0 2009-03-03 $
 * @package		DreamWish
 * @subpackage	main
 * @copyright	Copyright (C) 2009 Medley Productions. All rights reserved.
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
 * TimeKeeper.php
 * 
 * Time Keeper does just what his name describes, keeps up with the time and date
 * and so forth. Just think of the person who keeps the big clock on Cinderella's castle
 * up to date!
 * 
 * Upon initialization, several default TIME formats are loaded into options
 * NOW -> time stamp of the moment
 * TODAY -> simple date statement 1/2/2009  (Strips zeros from months and day)
 * MYSQL -> returns NOW in mysql format, ready to be stored
 * FULL_DATE -> returns Wednesday, July 4, 2009
 * FULL_TIME -> returns 2:30 pm
 * All -> returns Wednesday, July 4, 2009 2:30 pm
 * 
 * 
 * getAlltheTime(<pass along your timestamp)
 * and you get all the above based on the timestamp you send over
 * 
 */


class TimeKeeper extends imagineer
{
	public $options = array();
	public $connect;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new TimeKeeper();
        }

        return $instance;
    }
	
	private function __construct()
	{
			
	}
	
	public function init()
	{
		$this->setOptions();
	}
	
	protected function setOptions()
	{
		date_default_timezone_set('America/Chicago');
		defined('_START') or define('_START', microtime(true));
		$this->options['CLASS_NAME'] = __CLASS__;
		$this->options['LOCAL_PATH'] = dirname(__FILE__);
		$this->options['NOW']        = time();
		$this->options['TODAY']      = $this->simpleDate();
		$this->options['YEAR']       = $this->currentYear();
		$this->options['MYSQL']      = $this->time2MYSQL();
		$this->options['FULL_DATE']  = $this->fullDate();
		$this->options['FULL_TIME']  = $this->fullTime();
		$this->options['ALL']        = $this->fullStatement();
		// $this->timeSpeed(_START);
		$this->options['SPEED']      = array(
			'PAGE'  => array(
            	'START' => _START
            	)
		);
	}
	
	public function getAlltheTime($details)
	{
		$time['TIME'] = $details->getVar('TIME', time());
        
        return array(
            'TODAY'     => $this->simpleDate($time),
            'YEAR'      => $this->currentYear($time),
            'MYSQL'     => $this->time2MYSQL($time),
            'FULL_DATE' => $this->fullDate($time),
            'FULL_TIME' => $this->fullTime($time),
            'ALL'       => $this->fullStatement($time)
		);
	}
	
	public function calulateRunDate($start=false,$end=false)
	{	
		// this function will return true is today's date either falls
		// between the start and end date or before the end date
		if (!$start && !$end)
		{
			return true;
		}
		else
		{
			$start_date = (!$start) ? new DateTime('now') : new DateTime($start);
			$end_date   = new DateTime($end);
			$moment     = new DateTime('now');
	
			return ( ( ($moment>$start_date) || ($moment==$start_date) ) && ($moment<$end_date));
		}
	}
	
	public function simpleDate($time=array(), $sentence="Today is ")
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return $this->strip_zeros_from_date(strftime($sentence."*%m/*%d/%y", $time));
	}
	
	public function fullDate($time=array())
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return $this->strip_zeros_from_date(strftime("%A, %B *%d, %G", $time));
	}
	
	public function currentYear($time=array())
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return strftime("%Y", $time);
	}
	
	public function currentMonth($time=array())
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return strftime("%b", $time);
	}
	
	public function currentMonthNumber($time=array())
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return strftime("%m", $time);
	}
	
	public function currentDay($time=array())
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return strftime("%e", $time);
	}
	
	public function fullTime($time=array())
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return $this->strip_zeros_from_date(strftime("*%I:%M %p", $time));
	}
	
	public function fullStatement($time=array())
	{
        $time = (isset($time['TIME'])) ? $time['TIME'] : time();

        return $this->fullDate($time).' '.$this->fullTime($time);
	}
	
	public function time2MYSQL($time=array())
	{
		$time = (isset($time['TIME'])) ? $time['TIME'] : time();
		
		return strftime("%Y-%m-%d %H:%M:%S", $time);
	}
	
	public function convertMYSQL($details)
	{
		$time_string = $details->getVar('TIME');
        
        if ($time_string != NULL)
		{
			$time = strtotime($time_string); // converts string to time stamp
			
			$times = array(
				'TIMESTAMP'     => $time,
				'DATE_ONLY'     => $this->simpleDate($time, ''),
				'DATE_SENTENCE' => $this->simpleDate($time),
				'FULL_DATE'     => $this->fullDate($time),
				'FULL_TIME'     => $this->fullTime($time),
				'ALL'           => $this->fullStatement($time)
			);
			
			return $times;
		}
		else
		{
			return false;
		}
	}
	
	private function strip_zeros_from_date( $marked_string="")
	{
		// return "Problem-".$marked_string;
		// remove marked zeros
		$no_zeros = str_replace('*0' , '' , $marked_string);
		// remove remaining marks
		$clean_string = str_replace('*' , '' , $no_zeros);
		return $clean_string;
	}
	
	public function timeSpeed($entry=array())
	{
		/*
		 * This function either marks the beginning of an event or the end. If the event already exists
		 * in the options array, then it assumes you are marking the end of said event. Otherwise, it creates a new event
		 * and marks the time.
		 * 
		 * At the end it returns an array of times in text from thanks to the timeSpeedText function. This is totally optional
		 * for the calling program to use.
		 */
		if ( (!isset($entry['NAME'])) || (!isset($entry['NAMES'])) )
		{
			return false;
		}

        $staff  = (isset($entry['STAFF_PERSON']))  ?  $entry['STAFF_PERSON'] : 'UNKNOWN';
        $names  = (issset($ENTRY['NAMES']))        ?  $entry['NAMES']        : array($entry['NAME']);

		foreach ($names as $key)
		{
			if (isset($this->options['SPEED'][$key]))
			{
				// time has already been set asking for end time
				$this->options['SPEED'][$key]['END']  = microtime(true);
				$this->options['SPEED'][$key]['TIME'] = $this->options['SPEED'][$key]['END'] - $this->options['SPEED'][$key]['START'];
				$this->options['SPEED'][$key]['TEXT'] = round($this->options['SPEED'][$key]['TIME'], 3).' seconds';
                $this->writeStaffLog('['.$staff.']['.__METHOD__.']['.__LINE__.'] Finalized time for: '.$key);
			}
			else
			{
				$this->options['SPEED'][$key]['START'] = microtime(true);
				$this->options['SPEED'][$key]['END']   = null;
				$this->options['SPEED'][$key]['TIME']  = null;
				$this->options['SPEED'][$key]['TEXT']  = null;
                $this->writeStaffLog('['.$staff.']['.__METHOD__.']['.__LINE__.'] New Time Checkin by: '.$key);
			}
		}
		
		return $this->timeSpeedText($name);
	}
	
	public function timeSpeedText($name=null)
	{
		/*
		 * This function sweeps the the array given ($name) and collects an array
		 * of times in a readable text format ('0.001 seconds') and returns that array to the calling program
		 * 
		 */
		if ( (is_null($name)) || (!is_array($name)) )
		{
			return $name;
		}
		
		$times = array();
		
		foreach ($name as $key)
		{
			if (isset($this->options['SPEED'][$key]['TEXT']))
			{
				$times[$key] = $this->options['SPEED'][$key]['TEXT'];
			}
			elseif (isset($this->options['SPEED'][$key]))
			{
				$this->timeSpeed(array($key));
				$times[$key] = $this->options['SPEED'][$key]['TEXT'];
			}
		}
		
		return $times;
	}
	
	public function getTimeSpeed($name)
	{
		if (isset($this->options['SPEED'][$name]["TEXT"]))
		{
			return $this->options['SPEED'][$name]["TEXT"];
		}
	}
	
	public function timeImagineers($list)
	{
		foreach ($list as $key=>$value)
		{
			$this->options['SPEED'][$key]['START'] = $value;
			$this->options['SPEED'][$key]['END']   = null;
			$this->options['SPEED'][$key]['TIME']  = null;
			$this->options['SPEED'][$key]['TEXT']  = null;
            $this->writeStaffLog('['.$staff.']['.__METHOD__.']['.__LINE__.'] New Time Imagineer Checkin by: '.$key);
		}
	}
	
	public function getAge($birthdate,$calc_date='0000-00-00') 
	{
		//
		//	Calculates the age based on TODAY or
		//	on another optional date if given
		//
		//	assumes $birthdate is in YYYY-MM-DD format
		list($dob_year, $dob_month, $dob_day) = explode('-', $birthdate);
		//
		//	determine current year, month, and day
		if ($calc_date==0)
		{	// Calculate on TODAY
			$cur_year  = date('Y');
			$cur_month = date('m');
			$cur_day   = date('d');
		}
		else
		{	// Calculate on the optional date
			list($cur_year, $cur_month, $cur_day) = explode('-', $calc_date);
		}
		//
		//	either past or on the birthday
		if($cur_month >= $dob_month && $cur_day >= $dob_day) {
			$age = $cur_year - $dob_year;
		}
		//	before the birthday
		else
		{
			$age = $cur_year - $dob_year - 1;
		}
		//	and your done
		return $age;
	}
}
	
?>

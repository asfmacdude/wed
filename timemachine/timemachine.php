<?php

/*
 * @version		$Id: timemachine.php 1.0 2009-03-03 $
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
 * timemachine.php
 *
 * Basically timemachine handles scheduled events such as holidays and other deadlines.
 * I created it because I wanted a centralized way to enter dates and times for specific
 * events once and not have to enter it on numerous pages and such. This way, a script can call
 * an event by name such as "Christmas" and it would decide whether to run holiday images or videos
 * on certain pages. It can control any number of events such as registration deadlines for specific events
 * and the dates would only have to be entered in one location.
 
 * I also want timemachine to handle queued events that will be fired through a cron job from the server.
 * These events could range from sending out emails to deleting old records in a database.
 * 
 */

class timemachine extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new timemachine();
        }

        return $instance;
    }
	
	private function __construct()
	{
	
	}
	
	public function init()
	{
		$this->setOptions();
		$this->loadSupportFiles();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']         = __CLASS__;
		$this->options['LOCAL_PATH']         = dirname(__FILE__);
		$this->options['DETAILS']            = array();
		$this->options['CURRENT_ID']         = 0;
	}
	
	public function newSchedule($options=array())
	{
		$id_return = false;
		
		// Here we standardize all data pair arrays where the KEYS are always in ALL CAPS
		$options   = wed_standardKeys($options);
		
		if (isset($options['TYPE']))
		{
			$obj_class     = $options['TYPE'] . '_detail';
			$id            = $this->getNextID($options['TYPE']);
			
			if (class_exists($obj_class))
			{
				$object    = new $obj_class($options);
				
				if (!isset($this->options['DETAILS'][$id]))
				{
					$this->options['DETAILS'][$id] = $object;
				}
			}
			
			$id_return = $id;
		}
		
		return $id_return;
	}
	
	private function getNextID($type=null)
	{
		$this->options['CURRENT_ID']++;
		return $type . $this->options['CURRENT_ID'];
	}
	
	public function setHTML($options=array())
	{
		if ( (isset($options['ID'])) && (isset($this->options['DETAILS'][$options['ID']])) )
		{
			// We are calling a method in the class?_detail object
			return $this->options['DETAILS'][$options['ID']]->getHTML($options);
		} 
	}
}
?>
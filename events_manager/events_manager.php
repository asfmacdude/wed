<?php

/*
 * @version		$Id: events_manager.php 1.0 2009-03-03 $
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
 * events_manager.php
 * 
 * events_manager handles all the events such as calender, schedules, results, etc.
 *
 *
 */

class events_manager extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new events_manager();
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
		$this->options['LISTS']              = array();
		$this->options['CURRENT_ID']         = 0;
	}
	
	public function newEvent($options=array())
	{
		$id_return = false;
		
		if (isset($options['TYPE']))
		{
			$obj_class          = $options['TYPE'] . '_detail';
			$id                 = $this->getNextID($options['TYPE']);
			$options['DETAIL_ID'] = $id; // make sure the detail object has the assigned id
			
			if (class_exists($obj_class))
			{
				$object    = new $obj_class($options);
				
				if (!isset($this->options['LISTS'][$id]))
				{
					$this->options['LISTS'][$id] = $object;
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
		if ( (isset($options['ID'])) && (isset($this->options['LISTS'][$options['ID']])) )
		{
			// We are calling a method in the class?_detail object
			return $this->options['LISTS'][$options['ID']]->getHTML($options);
		} 
	}
}
?>
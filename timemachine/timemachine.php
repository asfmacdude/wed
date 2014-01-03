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
		$this->options['DETAILS']      = array();
		$this->options['CURRENT_ID']         = 0;
	}
	
	public function newPresentation($options=array())
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
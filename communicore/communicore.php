<?php
/*
 * @version		$Id: communicore.php 1.0 2009-03-03 $
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
 * communicore.php
 * 
 * Description goes here
 * 
 */

class communicore extends imagineer
{
	public $options = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new communicore();
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
		$this->options['CLASS_NAME'] = __CLASS__;
		$this->options['LOCAL_PATH'] = dirname(__FILE__);
		$this->options['LOG']        = array();
		$this->options['COUNT']      = 0;
	}
	
	public function loadDBObject($name,$class='UNKNOWN')
	{
		$object_name = 'db_'.$name;
		
		if (class_exists($object_name))
		{
			$this->logDBObject($name,$class);
			return new $object_name();
		}
		
		return false;
	}
	
	private function logDBObject($name,$class)
	{
		$name = $name . '_' . $this->options['COUNT']++;
		
		if (isset($this->options['LOG'][$class]))
		{
			$this->options['LOG'][$class] = $this->options['LOG'][$class] . ',' . $name;
		}
		else
		{
			$this->options['LOG'][$class] = $name;
		}
	}
}

?>
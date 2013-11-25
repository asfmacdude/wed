<?php

/*
 * @version		$Id: linkdirector.php 1.0 2009-03-03 $
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
 * linkdirector.php
 * 
 */

class linkdirector extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new linkdirector();
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
		$this->options['LINKS']              = array();
		$this->options['DETAIL_CLASS']       = 'link_detail';
		$this->options['CURRENT_ID']         = 0;
	}
	
	public function newLink($options=array())
	{
		$id_return     = false;
		$obj_class     = $this->options['DETAIL_CLASS'];
			
		if (class_exists($obj_class))
		{
			$id            = $this->getNextID('link');
			$options['ID'] = $id; // make sure the detail object has the assigned id
			$object        = new $obj_class($options);
			
			if (!isset($this->options['LINKS'][$id]))
			{
				$this->options['LINKS'][$id] = $object;
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
		if ( (isset($options['ID'])) && (isset($this->options['LINKS'][$options['ID']])) )
		{
			// We are calling a method in the class?_detail object
			return $this->options['LINKS'][$options['ID']]->getHTML($options);
		} 
	}
}
?>
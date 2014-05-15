<?php

/*
 * @version		$Id: communications.php 1.0 2009-03-03 $
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
 
/*
 * communications imagineer
 *
 * Communcations handles all forms of communicating with the user, staff and admin.
 * The communications come in different types here from email to screen to sms.
 * These types will grow as we go along.
 *
 * Important Note Here: This class will function with the new detail object class, but
 * it will also function with the standard options array as well.
 *
 */
 
defined( '_GOOFY' ) or die();

class communications extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new communications();
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
		$this->options['COMMUNICATIONS']     = array();
		$this->options['CURRENT_ID']         = 0;
	}
	
	public function newCommunication($options=array())
	{
		// Force change to new detail object
		$detail = $this->changeDetailObject($options);
		
		$id_return = false;
		$type      = $detail->TYPE;
		
		if (!is_null($type))
		{
			$obj_class     = $type . '_detail';
			$id            = $this->getNextID($type);
			
			if (class_exists($obj_class))
			{
				// IMPORTANT: We are passing over the new detail_object
				// Be sure each detail class here in Communcations uses
				// the new detail_object
				$object    = new $obj_class($detail);
				
				if (!isset($this->options['COMMUNICATIONS'][$id]))
				{
					$this->options['COMMUNICATIONS'][$id] = $object;
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
		if ( (isset($options['ID'])) && (isset($this->options['COMMUNICATIONS'][$options['ID']])) )
		{
			// We are calling a method in the class?_detail object
			return $this->options['COMMUNICATIONS'][$options['ID']]->getHTML($options);
		} 
	}
}
<?php

/*
 * @version		$Id: config_detail.php 1.0 2009-03-03 $
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
 * config_detail.php
 * 
 */

class config_detail extends details
{
	public $options  = array();
	public $settings = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
		$this->loadSettings();
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME'] = __CLASS__;
		$this->options['LOCAL_PATH'] = dirname(__FILE__);
		$this->options['SITE']       = SITE_DOMAIN;
		$this->options['SITE_ID']    = null;
		
		$this->addOptions($options);
	}
	
	private function loadSettings()
	{
		$config = wed_getDBObject('system_config');
		$this->settings = $config->getSettings($this->options['SITE_ID']);
	}
	
	public function __get($name)
	{
		return (isset($this->settings[$name])) ? $this->settings[$name] : false ;
	}
	
	public function getValue($name,$default=null)
	{
		return (isset($this->settings[$name])) ? $this->settings[$name] : $default ;
	}
}
<?php

/*
 * @version		$Id: wizard.php 1.0 2009-03-03 $
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
 * wizard imagineer
 *
 * The wizard does his magic with ajax calls. He handles all ajax based calls
 * by gathering the file to be called and returning the results. Generally, the
 * wizard will call files located in the 'wizard' theme which is not really a theme
 * just a directory inside themes. It is possible to still call files inside other
 * themes as well.
 *
 */
 
defined( '_GOOFY' ) or die();

class wizard extends imagineer
{	
	public $db;
	public $clock;
	public $theme;
	public $options;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new wizard();
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
		$this->options['CLASS_NAME']    = __CLASS__;
		$this->options['LOCAL_PATH']    = dirname(__FILE__);
		$this->options['SHOW_OBJECTS']  = array();
		$this->options['DEFAULT_THEME'] = 'Wizard_';
		$this->options['DEFAULT_FILE']  = 'wizard_apprentice';
	}
	
	private function buildPresentation()
	{
		$html       = '';
		
		$call_parts = wed_getSystemValue('CALL_PARTS'); // returns an array
		$query_vars = wed_getSystemValue('QUERY_VARS'); // returns an array
		
		$control    = (!empty($call_parts[0])) ? $call_parts[0] : null;
		$dir        = ((isset($query_vars['dir'])) && (!empty($query_vars['dir']))) ? $query_vars['dir'] : $this->options['DEFAULT_THEME'];
		$file       = (isset($call_parts[1]))  ? $call_parts[1] : $this->options['DEFAULT_FILE'];
				
		$dir  = str_replace('_', '/', $dir);
		$path = THEME_BASE . $dir . $file . '.php';
			
		if (file_exists($path))
		{
			include_once $path;
			
			if (class_exists($file))
			{
				$wiz_obj = new $file();
				$html    = $wiz_obj->getHTML();
			}
		}
		else
		{
			wed_changeSystemErrorCode('NO AJAX CALL FILE FOUND');
		}
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		return $this->buildPresentation();
	}
}
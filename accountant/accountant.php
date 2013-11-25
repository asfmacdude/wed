<?php
/*
 * @version		$Id: accountant.php 1.0 2009-03-03 $
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
 * accountant.php
 * 
 * The Accountant class handles all tables and their related forms. It has several helper
 * classes including accountant_table_detail and accountant_form-detail each of which handle
 * details pertaining to their related tasks.
 * 
 */

class accountant extends imagineer
{
	public $options   = array();
	public $masterObj = null;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new accountant();
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
		$this->options['DEFAULT_HTML']  = 'Requested Information Not Available';
	}
	
	protected function loadAccountDetail($options)
	{
		$html = null;
		$key = (isset($options['KEY'])) ? $options['KEY'] : null;
		
		if (!is_null($key))
		{
			$ex_key = explode('_', $key);
			$table_or_form = strtolower($ex_key[0]);
			$obj_class = 'accountant_' . $table_or_form . '_detail';

			if (class_exists($obj_class))
			{				
				$this->masterObj = new $obj_class($options);
				$html = $this->masterObj->getHTML();
			}
		}

		return $html;		
	}
		
	public function setHTML($options=array())
	{
		$options = $this->parseUrl2Options($options);  // get url varibles
		return $this->loadAccountDetail($options);
	}
	
	public function getAjaxHTML($options=array())
	{
		$html    = null;
		$options = $this->parseUrl2Options($options);  // get url varibles
		
		global $walt;
		
		if ($walt->SYS_MODE==='AJAX')
		{
			$keys   = $walt->getImagineer('keys');
			
			$key = (isset($options['KEY'])) ? $options['KEY'] : null;
			
			if (!is_null($key))
			{
				$key_obj = new key_detail(strtoupper($key));
				$key_obj->IMAGINEER = 'accountant';
				$key_obj->METHOD    = 'getHTML';
				$key_obj->ARGUMENTS = $options;
				$keys->addKeyObject($key_obj);
				
				$html = '{{'.strtoupper($key).'}}';
			}
		}
		
		return (is_null($html)) ? $this->options['DEFAULT_HTML'] : $html;
	}
}
?>
<?php
/*
 * @version		$Id: cssdirector.php 1.0 2009-03-03 $
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
 * cssdirector.php
 * 
 * Description goes here
 * 
 */

class cssdirector extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new cssdirector();
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
		$this->options['CSS']           = array();
		$this->options['CSS_KEY']       = '[-CSS-]';
		$this->options['DETAILS']       = array();
		$this->options['CURRENT_ID']    = 0;
	}
	
	public function newDetail($options=array())
	{
		$id_return = false;
		
		// Here we standardize all data pair arrays where the KEYS are always in ALL CAPS
		$options   = wed_standardKeys($options);
		
		$obj_class = 'css_detail';	
		$id        = (isset($options['ID'])) ? $options['ID'] : $this->getNextID('NA');
		
		if (class_exists($obj_class))
		{	
			if (!isset($this->options['DETAILS'][$id]))
			{
				$this->options['DETAILS'][$id] = new $obj_class($options);
				$id_return = true;
			}
		}

		return $id_return;
	}
	
	private function getNextID($type=null)
	{
		$this->options['CURRENT_ID']++;
		return $type . $this->options['CURRENT_ID'];
	}
	
	/*
	 * addCSSAsset
	 *
	 * Here we add one ASSET array to the DETAILS array.
	 *
	 */
	public function addCSSAsset($asset)
	{
		if (is_array($asset))
		{
			$this->newDetail($asset);
		}
	}
	
	/*
	 * loadCSSAssets
	 *
	 * Here we look through the list of IDs passed and check to see if that CSS ASSET
	 * exists already and if so, reset LOAD to true
	 *
	 */
	public function loadCSSAssets($list=array())
	{
		foreach ($list as $asset_id)
		{
			if (isset($this->options['DETAILS'][$asset_id]))
			{
				$this->options['DETAILS'][$asset_id]->LOAD = true;
			}
		}
	}
	
	/*
	 * getCSSObjectHTML
	 *
	 * Here we flip through the CSS array and attempt to get the html from
	 * each object. 
	 *
	 */
	private function getCSSObjectHTML()
	{
		$html = '';
		
		foreach ($this->options['DETAILS'] as $key=>$object)
		{
			$html .= $object->getHTML();
		}
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		$html = (isset($options['HTML'])) ? $options['HTML'] : null;	
		return str_replace($this->CSS_KEY, $this->getCSSObjectHTML(), $html);
	}
}

?>
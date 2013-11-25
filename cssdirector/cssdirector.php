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
		$this->options['CSS_ASSETS']    = array();
	}
	
	public function loadCSSAssets($list=array())
	{
		foreach ($list as $asset)
		{
			foreach ($this->options['CSS_ASSETS'] as $key=>$value)
			{
				if ( (isset($value['ID'])) && ($value['ID']===$asset) )
				{
					$this->options['CSS_ASSETS'][$key]['LOAD'] = true;
				}
			}
		}
	}
	
	public function addCSSAsset($asset)
	{
		if (is_array($asset))
		{
			array_push($this->options['CSS_ASSETS'], $asset);
		}
	}
	
	private function getCSSObjectHTML()
	{
		$html = '';
		
		foreach ($this->options['CSS'] as $key=>$object)
		{
			$html .= $object->getHTML();
		}
		
		return $html;
	}
	
	public function addCSSObject($object)
	{
		if ($object instanceof css_detail)
		{
			array_push($this->options['CSS'], $object);
		}
	}
	
	private function loadDetailObjects()
	{
		foreach ($this->CSS_ASSETS as $key=>$value)
		{
			if ( (isset($value['LOAD'])) && ($value['LOAD']) )
			{
				$this->options['CSS'][] = new css_detail($value);
			}
		}
	}
	
	public function setHTML($options=null)
	{
		$html = (isset($options['HTML'])) ? $options['HTML'] : null;
		$this->loadDetailObjects();	
		return str_replace($this->CSS_KEY, $this->getCSSObjectHTML(), $html);
	}
}

?>
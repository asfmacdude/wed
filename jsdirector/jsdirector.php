<?php
/*
 * @version		$Id: jsdirector.php 1.0 2009-03-03 $
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
 * jsdirector.php
 * 
 * 5/1/2013 - Redesign concept is put in place where HTML is passed to jsdirector
 * for him to put in the javascript calls at the very end so that KEYS and SHORTCODES
 * can add css and js as needed along the path. By adding css and js at the end
 * we get a very efficient and lean html with only the js and css needed and not
 * a lot of files and calls that aren't needed.
 * 
 */

class jsdirector extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new jsdirector();
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
		$this->options['JS']            = array(); // Main holder of JS Objects
		$this->options['JS_READY_CODE'] = array(); // javascript code for the document ready section
		$this->options['JQ_VERSION']    = '1.10.1'; // '1.8.2'; // latest version of JQuery to be used
		$this->options['JS_KEYS']       = array('JQUERY','JQUERY-UI','JQUERY-UI-SMOOTHNESS','GOOGLE-API','JS_GOOGLE_ANALYTIC','JS_FILES','JS_FILES_TOP','JS_READY_CODE');
		$this->options['END_TAGS']      = array('[-','-]');
		$this->options['JS_ASSETS']     = array();
	}
	
	public function addJSObject($object)
	{
		if ($object instanceof js_detail)
		{
			array_push($this->options['JS'], $object);
		}
	}
	
	public function loadJSAssets($list=array())
	{
		foreach ($list as $asset)
		{
			foreach ($this->options['JS_ASSETS'] as $key=>$value)
			{
				if ( (isset($value['ID'])) && ($value['ID']===$asset) )
				{
					$this->options['JS_ASSETS'][$key]['LOAD'] = true;
				}
			}
		}
	}
	
	public function addJSAsset($asset)
	{ 
		if (is_array($asset))
		{
			array_push($this->options['JS_ASSETS'], $asset);
		}
	}
	
	private function loadDetailObjects()
	{
		foreach ($this->JS_ASSETS as $key=>$value)
		{
			// Here we check the LOAD value to make sure it is TRUE
			// And we check to see if the same asset with the same ID has already been loaded
			if ( (isset($value['LOAD'])) && ($value['LOAD']) && (!isset($this->options['JS'][$value['ID']])) )
			{
				$this->options['JS'][$value['ID']] = new js_detail($value);
			}
		}
	}
	
	public function getJSObjectHTML($search_key=null)
	{	
		$html    = '';
		$js_list = $this->setJSCalls();
		
		foreach ($this->options['JS'] as $key=>$object)
		{	
			if ($object->KEY === $search_key)
			{
				$object->JS_LIST = $js_list; // Pass over the List of Standard Javascript Calls
				$html .= $object->getHTML();
			}
		}
		
		return ($search_key === 'READY_CODE') ? $this->getJS_Ready() : $html;
	}
	
	private function setJSCalls()
	{	
		// NOTE: Since different themes require different versions of JQuery, I added the ability to add the version dynamically
		$js = array(
			'JQUERY'               => '<script type="text/javascript" src="http://code.jquery.com/jquery-%s.min.js"></script>'.LINE1,
			'JQUERY-MIGRATE'       => '<script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>'.LINE1,
			'JQUERY-MOBILE'        => '<script src="http://code.jquery.com/mobile/1.3.2/jquery.mobile-1.3.2.min.js"></script>'.LINE1,
			'JQUERY-GOOGLE'        => '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/%s/jquery.min.js"></script>'.LINE1,
			'JQUERY-UI'            => '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.22/jquery-ui.min.js"></script>'.LINE1,
			'JQUERY-UI-SMOOTHNESS' => '<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.1/themes/smoothness/jquery-ui.css" />'.LINE1,
			'GOOGLE-API'           => '<script src="http://www.google.com/jsapi"></script>'.LINE1,
			'GOOGLE-ANALYTIC'      => '<script>var _gaq=[["_setAccount","UA-18780586-1"],["_trackPageview"]];(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.async=1;g.src=("https:"==location.protocol?"//ssl":"//www")+".google-analytics.com/ga.js";s.parentNode.insertBefore(g,s)}(document,"script"));</script>'.LINE1
		);
		
		return $js;
	}
	
	public function getJS_Ready()
	{
		$html = '';
		$html .= '<script type="text/javascript">jQuery(function(){'.LINE1;
		
		foreach ($this->options['JS_READY_CODE'] as $js_code)
		{	
			$html .= $js_code.LINE1;
		}
		
		$html .= '});</script>'.LINE2;
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		$html = (isset($options['HTML'])) ? $options['HTML'] : null;
		$this->loadDetailObjects();
		
		foreach ($this->JS_KEYS as $key)
		{
			$search_key = $this->END_TAGS[0] . $key . $this->END_TAGS[1];
			$html       = str_replace($search_key, $this->getJSObjectHTML($key), $html);
		}
		
		return $html;		
	}
}

?>
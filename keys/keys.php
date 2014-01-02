<?php
/*
 * @version		$Id: keys.php 1.0 2009-03-03 $
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
 * keys.php
 * 
 * Description goes here
 * 
 */

class keys extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new keys();
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
		$this->options['KEYS']          = array();
		$this->options['ENDS']          = array('{{', '}}');
	}
	
	private function loadEachKey($options=null)
	{
		$html_array = array();
		$html       = $options['HTML'];

		foreach ($this->options['KEYS'] as $key=>$object)
		{
			$new_key = $this->options['ENDS'][0] . $object->KEY . $this->options['ENDS'][1];			
			$html_array[$new_key] = $object->getHTML();	
		}
		
		$search  = array_keys($html_array);
		$replace = array_values($html_array);
		
		return str_replace($search, $replace, $html);
	}
	
	//
	// These functions load the File Call keys {[FILE_PATH]}
	//
	public function loadFileKeys($content)
	{
	    /*
	     * By making this recursive, it will continue to load in modules
	     * until they are all done, even if they are modules within modules
	     *
	     * Example: {[FILE_NAME]} this will only load files
	     */
	    
	    $reg_expression = '#\{\[(.*)\]\}#iU';
	    // [0] => {{FIRST_LINE}}
	    // [1] => FIRST_LINE
	    
		if (is_array($content))
	    {   
	        $content = $this->getFileCall($content[1]);
	    }

	    return preg_replace_callback($reg_expression,array($this,'loadFileKeys'),$content);
	}
	
	private function getFileCall($file)
	{
		global $walt;
		$theme   = wed_getSystemValue('THEME');
		$html    = null;
		$options = array();
		
		// Allow for sending varibles to part files
		$file_parts = explode(':', $file);
		
		if (isset($file_parts[1]))
		{
			parse_str($file_parts[1],$options);
		}
		
		// Allow for Alternate paths for mobile and other devices
		$dir_parts = explode('/', $file_parts[0]);
		
		// Now we break apart the file path and test to see if an alternate
		// path exists for another device such as a mobile device. For example:
		// for the parts directory, we may have a parts_mobile directory to serve
		// a different file for mobile devices. If for some reason the file does not
		// exists in the alternate location, it will fall back and look in the standard
		// directory.
		if (isset($dir_parts[1]))
		{
			$options['DIR_PATH'] = THEME_BASE . $theme . DS;
			$options['DIR_NAME'] = $dir_parts[0];
		
			$dir_path = wed_getAlternateDirectory($options);
			$path     = str_replace($dir_parts[0], $dir_path, $file);
			
			if ( file_exists($path) )
			{
				ob_start();
				@include $path;
				$html = ob_get_contents();
				ob_end_clean();
			}
		}
		
		if (is_null($html))
		{
			$path  = THEME_BASE . $theme . DS . $file_parts[0];
			
			if ( file_exists($path) )
			{
				ob_start();
				@include $path;
				$html = ob_get_contents();
				ob_end_clean();
			}
		}
		
		return $html;
	}

	public function loadMergeKeys($content)
	{
	    /*
	     * By making this recursive, it will continue to load in modules
	     * until they are all done, even if they are modules within modules
	     *
	     * Example: {{MERGE_KEY}}
	     */
	    $reg_expression = '#\{\{(.*)\}\}#iU';
	    // [0] => {{FIRST_LINE}}
	    // [1] => FIRST_LINE
	    
		if (is_array($content))
	    {   
	        $content = wed_getSystemValue($content[1]);
	    }

	    return preg_replace_callback($reg_expression,array($this,'loadMergeKeys'),$content);
	}
	
	public function setHTML($options=null)
	{	
		$html      = (isset($options['HTML'])) ? $options['HTML'] : null;
		$run_merge = (isset($options['MERGE'])) ? $options['MERGE'] : false;
		
		if ($run_merge)
		{
			return $this->loadMergeKeys($html);
		}
		else
		{
			return $this->loadFileKeys($html);
		}
	}
}

?>
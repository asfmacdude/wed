<?php
/*
 * @version		$Id: ajax_detail.php 1.0 2009-03-03 $
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
 * ajax.php
 *
 * This file serves has a stub for all ajax calls. The url to reach
 * this file should be /wizard/name_of_file_to_run.php?dir=_themes_theme_dir_
 *
 * The dir will be turned into a directory path by replacing the _ with /
 *
 */

class ajax_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options=array())
	{
		$this->options['CLASS_NAME']        = __CLASS__;
		$this->options['LOCAL_PATH']        = dirname(__FILE__);
		$this->options['ID']                = 'ajax1'; // this is assigned by Presentations
		
		$this->addOptions($options);
	}
	
	private function buildPresentation()
	{
		$html       = '';
		
		$call_parts = wed_getSystemValue('CALL_PARTS'); // returns an array
		$query_vars = wed_getSystemValue('QUERY_VARS'); // returns an array
		
		$control    = (!empty($call_parts[0])) ? $call_parts[0] : null;
		$file       = (isset($call_parts[1]))  ? $call_parts[1] : null;
		$dir        = (isset($query_vars['dir'])) ? $query_vars['dir'] : null;
		
		if ( ($control=='wizard') && (!is_null($file)) && (!is_null($dir)) )
		{
			$dir  = str_replace('_', '/', $dir);
			$path = THEME_BASE . $dir . $file;
			
			if (file_exists($path))
			{
				ob_start();
				@include $path;
				$html = ob_get_contents();
				ob_end_clean();
			}
			else
			{
				wed_changeSystemErrorCode('NO AJAX CALL FILE FOUND');
			}
		}
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		return $this->buildPresentation();
	}
}
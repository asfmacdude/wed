<?php
/*
 * @version		$Id: page_detail.php 1.0 2009-03-03 $
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
 * page_detail.php
 * 
 * This is the detail object for presentations
 * 
 */

class page_detail extends details
{
	public $options  = array();
	public $DetailObj = false;
	public $componentName;
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']      = __CLASS__;
		$this->options['LOCAL_PATH']      = dirname(__FILE__);
		$this->options['ERROR_CODE']      = 10;
		$this->options['ID']              = 'page1'; // this is assigned by Presentations
		$this->options['PAGE']            = null;
		$this->options['CONTROL']         = null;
		$this->options['THEME_PAGE']      = null;
		$this->options['ASSETS']          = null;
		$this->addOptions($options);
	}
	
	private function loadControl()
	{
		$control = wed_getDBObject('content_control',$this->options['CLASS_NAME']);
		
		if ($control->loadPageID(wed_verifyControlCode($this->options['PAGE'])))
		{			
			$settings                 = $control->buildSettingPairs();
			$settings['HEADER_1']     = $control->getValue('title');
			$settings['CONTROL_ID']   = $control->getValue('id');
			$details                  = $control->getDetails();	
			$settings                 = array_merge($settings, $details);
			
			$this->options['CONTROL'] = $control;
			/*
			 * Theme Update
			 *
			 * Here we check to see if the Control Code has a theme. The global theme
			 * has already been set, but the control code is allowed to override the theme
			 * here if desired.
			 */
			 
			if (strtoupper($settings['THEMEID'])!='NONE')
			{
				wed_setTheme($settings['THEMEID']);
			}
			
			wed_addSystemValueArray($settings);
		}
		
		return (is_null($this->options['CONTROL'])) ? false : true;
	}
	
	private function checkURLGroup()
	{
		// We need to go ahead and check the 'group' part of the URL and make
		// sure it is legit. Let it go if the current page is the ERROR_404
		$call_parts = wed_getSystemValue('CALL_PARTS');
		$group_db   = wed_getDBObject('content_groups',$this->options['CLASS_NAME']);
		
		if ((isset($call_parts[1])) && ($group_db->checkGroupSysName($call_parts[1])))
		{
			$sys_page_title = wed_getSystemValue('PAGE_TITLE');
			$page_title     = $group_db->getValue('pagetitle');
			$group_title    = $group_db->getValue('title');
			
			if (!empty($page_title))
			{
				$page_title = $sys_page_title.' - '.$page_title;
				// wed_addSystemValue('PAGE_TITLE',$page_title);
			}
			
			wed_addSystemValue('GROUP_TITLE',$group_title);
		}
		
		return true;
	}
	
	private function loadTemplates($html)
	{
		$shortcodes = getImagineer('shortcodes');
		// Convert all HTML entities to their applicable characters
		$html       = html_entity_decode($html);
		return $shortcodes->getHTML(array('HTML'=>$html,'PRE'=>true));
	}
	
	private function loadThemePage()
	{
		$file     = wed_getSystemValue('THEMEPAGE');
		
		$theme    = wed_getSystemValue('THEME');
		$dir_path = THEME_BASE . $theme . DS;
		
		$options['DIR_PATH']  = $dir_path;
		$options['FILE_NAME'] = $file;
		
		$path = wed_getAlternatePath($options);
			
		if (file_exists($path))
		{
			ob_start();
			@include $path;
			$this->options['THEME_PAGE'] = ob_get_contents();
			ob_end_clean();
		}
		else
		{
			wed_changeSystemErrorCode('NO THEME PAGE FOUND');
		}
		
		return (is_null($this->options['THEME_PAGE'])) ? false : true;
	}
	
	private function finalizeHTML($html)
	{
		// Convert all HTML entities to their applicable characters
		$html       = html_entity_decode($html);
		$keys       = getImagineer('keys');
		$shortcodes = getImagineer('shortcodes');
		$css        = getImagineer('cssdirector');
		$javascript = getImagineer('jsdirector');

		// Order below is very important because keys and shortcodes
		// can add css and javascript along the way so those must be
		// added last.
		$html = $keys->getHTML(array('HTML'=>$html));
		
		// You cannot run shortcodes on the Admin Show
		// because the shortcodes in the forms will be processed
		// and you will not be able to 'see' them
		if (!ADMIN)
		{
			$html = $shortcodes->getHTML(array('HTML'=>$html));
			
		}
		
		$html = $css->getHTML(array('HTML'=>$html));
		$html = $javascript->getHTML(array('HTML'=>$html));
		
		// We run Keys again to do the merge keys
		$html = $keys->getHTML(array('HTML'=>$html,'MERGE'=>true));
		$html = wed_cleanItUp($html,'FINAL_HTML');
		$this->getDebugMessages();
		$html = $this->devShowErrorDiv($html);
		$html = $this->devShowMessageDiv($html);

		return $html;
	}
	
	private function getDebugMessages()
	{
		$html = null;
		$mode = wed_getSystemValue('DEVELOPER');
		
		if ($mode)
		{
			//dbug($_SESSION);
			//dbug($_SERVER);
			//dbug($_REQUEST);
			
			if (function_exists('dbug'))
			{
				$html = dbug('print');
			}
			
			if (!is_null($html))
			{
				messages::addMessage($html,'Debug Department');
			}
		}
	}
	
	public function setHTML($options=null)
	{
		$html = null;
		
		// Load content_control object and check URL
		if (($this->checkURLGroup()) && ($this->loadControl()))
		{	
			// Load assets.php
			$this->options['ASSETS'] = wed_getAssets(); // $this->loadAssets();
			
			if (!is_null($this->options['ASSETS']))
			{
				// Call pushOptions method to push important settings out
				// to other directors.
				$this->options['ASSETS']->pushOptions();
			}
			
			// Load & Work Control Content
			$html = wed_getSystemValue('STRUCTURE');
				
			// Run Shortcodes on structure html
			$html = $this->loadTemplates($html);
			
			// Load theme page
			if ($this->loadThemePage())
			{	
				// If we have a theme page, the CONTROL_CONTENT is inserted into the template html
				$html = str_replace('[-BODY_CONTENT-]', $html, $this->options['THEME_PAGE']);
			}
			
			// Finalize HTML
			$html = $this->finalizeHTML($html);
		}
		else
		{
			wed_changeSystemErrorCode('NO CONTROL CODE FOUND');
		}
		
		return $html;
	}
}
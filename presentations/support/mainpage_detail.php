<?php
/*
 * @version		$Id: mainpage_detail.php 1.0 2009-03-03 $
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
 * mainpage_detail.php
 * 
 * This is the main page detail object for displaying the main page
 * 
 */

class mainpage_detail extends details
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
		$this->options['CLASS_NAME']         = __CLASS__;
		$this->options['LOCAL_PATH']         = dirname(__FILE__);
		$this->options['ERROR_CODE']         = 10;
		$this->options['ID']                 = 'page1'; // this is assigned by Presentations
		$this->options['PAGE_TEMPLATE']      = null; // the default template
		$this->options['PAGE_TEMPLATE_CODE'] = wed_getSystemValue('INDEX','index');
		$this->options['ERROR_PAGE_CODE']    = wed_getSystemValue('ERROR_404','error_404');
		
		$call_parts                          = wed_getSystemValue('CALL_PARTS');
		$this->options['PAGE_TEMPLATE_CODE'] = (!empty($call_parts[0])) ? $call_parts[0] : $this->options['PAGE_TEMPLATE_CODE'] ;
		
		$this->options['CONTROL']            = null;
		$this->options['THEME_PAGE']         = null;
		$this->options['ASSETS']             = null;
		$this->addOptions($options);
	}
	
	private function setPageTemplate()
	{
		if ($this->options['PAGE_TEMPLATE_CODE']==='index')
		{
			// Let it default to index when the code = index and that eliminates the need
			// for having and index record in Page Control
			$this->options['PAGE_TEMPLATE'] = 'index';
		}
		
		$control = wed_getDBObject('content_control',$this->options['CLASS_NAME']);
		
		if ($control->selectByCodeSite($this->options['PAGE_TEMPLATE_CODE']))
		{
			// Here the $control object returns the name of the page template
			// It will either be specified in the theme_page field or derived from
			// the theme_page_control.
			
			$this->options['PAGE_TEMPLATE'] = $control->getPageTemplate();
			$settings                       = $control->buildSettingPairs();
			$settings['HEADER_1']           = $control->getValue('title');
			$settings['CONTROL_ID']         = $control->getValue('id');
			$details                        = $control->getDetails();	
			$settings                       = array_merge($settings, $details);
			
			// CHange theme here if desired.
			if (strtoupper($settings['THEMEID'])!='NONE')
			{
				wed_setTheme($settings['THEMEID']);
			}
			
			wed_addSystemValueArray($settings);	
		}
	}
	
	private function loadThemePage($file=null)
	{		
		if (is_null($file))
		{
			$file = $this->options['PAGE_TEMPLATE'];
		}
		
		$html     = null;
		$theme    = wed_getSystemValue('THEME');
		$dir_path = THEME_BASE . $theme . DS;
		
		$options['DIR_PATH']  = $dir_path;
		$options['FILE_NAME'] = $file;
		
		$path = wed_getAlternatePath($options);
			
		if (file_exists($path))
		{
			ob_start();
			@include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}
		
		return $html;
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
		
		/*
		 * Logic for this Presentation
		 *
		 * 1 - Get the page template name that will either be also specified in the URL
		 * or default to index.php. The template page will decide what content to load
		 * so that will not be decided here.
		 *
		 * 2 - Load the page template html, then process.
		 *
		 */
		 
		$this->setPageTemplate();
		$html = $this->loadThemePage();
		
		if (!is_null($html))
		{
			// Load assets.php
			$this->options['ASSETS'] = wed_getAssets(); // $this->loadAssets();
			
			if (!is_null($this->options['ASSETS']))
			{
				// Call pushOptions method to push important settings out
				// to other directors.
				$this->options['ASSETS']->pushOptions();
			}
			
			// Finalize HTML
			$html = $this->finalizeHTML($html);
			wed_changeSystemErrorCode(null);
			
		}
		else
		{
			wed_changeSystemErrorCode('NO HTML LOADED');
			$html = false;
		}
		
		return $html;
	}
}
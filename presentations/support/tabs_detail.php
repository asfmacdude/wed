<?php
/*
 * @version		$Id: tabs_detail.php 1.0 2009-03-03 $
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
 * tabs_detail.php
 * 
 * This is the detail object for presentations that displays tabbed interfaces
 *
 * All tabbed interfaces have two basic elements
 * - Tab Headings (labels on the tabs)
 * - Tab Content (content that is revealed when a tab is clicked)
 *
 * Content will mainly be extraced from the content_main table; however, in future
 * devs, there may be other ways of getting content
 * 
 * The TAB_STYLE file that will be included will contain an array of 'WRAPPERS' to be used
 * around the two elements of the tabbed interface. The following are required:
 * - MAIN_OUTER_WRAP some tabbed interfaces will have an outer wrap, leave blank if not the case
 * - TAB_WRAP this is the wrap that goes around both the tab headers and the content.
 * - TAB_HEADERS_WRAP this wraps the tab headers, most of the time it will be a <ul> with a class
 * - TAB_HEAD_WRAP this wraps each individual tab head, usually a <li> with class or classes
 * - TAB_HEAD_ICON optional if the style allows for icons as part of the head
 * - TAB_CONTENT_WRAP this is the main wrap around the entire content pane, usually a <div>
 * - TAB_CONTENT_PANE_WRAP this is the wrap around eac individual content pane
 *
 *
 * CONTENT
 * The content array will be similar across all presentation and look something like this:
 * - 'header'  => array( 'CONTENT_CODE'=>content_code,'ICON_CLASS'=> icon_code )
 * This will allow for as many options as you want for each section of the presentaion
 */

class tabs_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']        = __CLASS__;
		$this->options['LOCAL_PATH']        = dirname(__FILE__);
		$this->options['ID']                = 'tab1'; // this is assigned by Presentations
		$this->options['CONTENT']           = array(); // array of header => content codes
		$this->options['STYLE']             = null; // style of the tabs, refers to a view file to be included
		$this->options['VIEW_PATH']         = 'views' . DS; // path to the views folder
		$this->options['WRAPS']             = array(); // holds the wrap html for the different segments of the presentation
		$this->options['ACTIVE_CLASS']      = 'active';
		
		$this->addOptions($options);
	}
	
	private function loadStyleFile()
	{
		$status = false;
		
		if (!is_null($this->STYLE))
		{
			$path      = $this->options['LOCAL_PATH'] . DS . $this->VIEW_PATH . $this->STYLE . '.php';
			$style = array(); // the view file will fill this array

			if (file_exists($path))
			{
				@include $path;
				$this->options['WRAPS'] = $style; // load styles into public array
				$status = true;
			}
		}
		
		return $status;
	}
	
	private function buildPresentation()
	{
		$header_html  = '';
		$h_html       = '';
		$content_html = '';
		$c_html       = '';
		$html         = '';
		$styles       = $this->WRAPS;
		$active_class = $this->ACTIVE_CLASS;
		$base_id      = 'tab_pane';
		$tab_count    = 0;
		$tab_id       = '';
		
		foreach ($this->options['CONTENT'] as $header=>$content_array)
		{
			$tab_count++;
			$tab_id       = $base_id . $tab_count;
			$icon_class   = (isset($content_array['ICON_CLASS'])) ? $content_array['ICON_CLASS'] : null;
			$icon_html    = (!is_null($icon_class)) ? str_replace('%ICON_CLASS%', $icon_class, $styles['TAB_HEAD_ICON']) : null;
			$content_code = (isset($content_array['CONTENT_CODE'])) ? $content_array['CONTENT_CODE'] : null;
			
			// format the header
			$header_html = str_replace('%TAB_ID%', $tab_id, $styles['TAB_HEAD_WRAP']);
			$header_html = str_replace('%CONTENT%', $header, $header_html);
			$header_html = str_replace('%ICON%', $icon_html, $header_html);
			$header_html = str_replace('%ACTIVE%', $active_class, $header_html);
			
			$h_html .= $header_html;
			
			// format the content
			$content_html = str_replace('%TAB_ID%', $tab_id, $styles['TAB_CONTENT_PANE_WRAP']);
			$content_html = str_replace('%CONTENT%', $this->getContent($content_code), $content_html);
			$content_html = str_replace('%ACTIVE%', $active_class, $content_html);
			
			$c_html .= $content_html;
			
			// after the first time, erase the active class
			$active_class = '';
		}
		
		// Now wrap the entire header
		$h_html = str_replace('%CONTENT%', $h_html, $styles['TAB_HEADERS_WRAP']);
		
		// Now wrap the content panes
		$c_html = str_replace('%CONTENT%', $c_html, $styles['TAB_CONTENT_WRAP']);
		
		// Now wrap the combined header and content
		$html = str_replace('%CONTENT%', $h_html . $c_html, $styles['TAB_WRAP']);
		
		// Finally the main outer wrap which is optional
		$html = str_replace('%CONTENT%', $html, $styles['MAIN_OUTER_WRAP']);
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		$html = null;

		if ($this->loadStyleFile())
		{
			$html = $this->buildPresentation();
		}

		return $html;
	}
}
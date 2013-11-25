<?php
/*
 * @version		$Id: accordian_detail.php 1.0 2009-03-03 $
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
 * accordian_detail.php
 * 
 * This is the detail object for presentations that displays accordian interfaces
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

class accordion_detail extends details
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
		$this->options['ERROR_CODE']        = 20; // Unique error code assigned to this presentation
		$this->options['ID']                = 'tab1'; // this is assigned by Presentations
		$this->options['NAME']              = null;   // NOT SURE what name does??
		$this->options['SETUP_DB']          = false; // presentation_setups db object
		// $this->options['SCHEDULE_DB']       = false; // banner_schedule db object
		$this->options['LIST_OBJECT']       = false; // list db object
		$this->options['SETUP_ID']          = null;  // actual record id on the presentation_setups db
		$this->options['SETUP_CODE']        = null;  // code of the presentation_setup (accordion_one)
		$this->options['SETUP_TAG']         = null;  // defaults to general
		$this->options['TYPE_ID']           = null;  // type id of the content
		$this->options['HEADING']           = 'General Heading'; 
		$this->options['SETUP_ACTIVE']      = 'N';   // Is the presentation object active?
		$this->options['SETUP_CSS']         = null;  // Optional CSS for the presentation
		$this->options['SETUP_JS']          = null;  // Optional javascript for the presentation
		$this->options['SETUP_JS_ASSETS']   = null;  // Optional load certain javascript assets
		$this->options['SCHEDULE_KEYS']     = null;
		$this->options['SCHEDULE_RESULT']   = null;

		// Default Accordion Styles
		$this->options['ACC_WRAP']              = '%CONTENT%';
		$this->options['ACC_HEAD']              = '%CONTENT%';
		$this->options['ACC_CONTENT_WRAP']      = '';
		$this->options['STYLE']                 = 'style3';
		
		$this->addOptions($options);
	}
	
	private function buildPresentation()
	{
		if ((!$this->loadPresentationSetup()) || ($this->options['SETUP_ACTIVE']!='Y'))
		{
			wed_changeSystemErrorCode($this->options['ERROR_CODE']);
			return null;
		}
		
		$connect_db   = $this->options['LIST_OBJECT'];
		$header_html  = '';
		$content_html = '';
		$html         = '<h3>'.$this->options['HEADING'].'</h3>';
		$acc_count    = 0;
		
		global $walt;
		$shortcodes = $walt->getImagineer('shortcodes');
		
		$rec = 0;

		while ($connect_db->moveRecordList($rec))
		{
			// First get the article code and let shortcodes evaluate it
			// If it evaluates to null then we don't run this tab. This
			// way you can do timed schedules on certain content and instead
			// of showing an empty tab, none shows at all and it is skipped.
			$content = $connect_db->getFormattedValue('FULLARTICLE');
			$content = $shortcodes->getHTML(array('HTML'=>$content));
			
			if (!empty($content))
			{
				// Set the id for this acc
				$acc_count++;
				
				// Set the heading
				$header_html  = str_replace(array('%NUMBER%','%CONTENT%'), array($acc_count,$connect_db->getFormattedValue('TITLE')), $this->options['ACC_HEAD']);
				
				// Set the content
				$content_html = str_replace(array('%NUMBER%','%CONTENT%'), array($acc_count,$content), $this->options['ACC_CONTENT_WRAP']);
				
				// Wrap it up
				$html .= str_replace(array('%STYLE%','%CONTENT%'), array($this->options['STYLE'],$header_html.$content_html), $this->options['ACC_WRAP']);
			}
			
			$rec++;
		}
		
		// Add the CSS Style Section before the Presentaion
		$html = $this->options['SETUP_CSS'] . $html;
			
		// Add any necessary javscript code
		$this->loadJavascript();
		
		return $html;
	}
	
	/*
	 * loadPresentationSetup
	 *
	 * This function loads the initial setup for the presentation and transfers
	 * the needed values into $this->options. Obviously, if this doesn't happen,
	 * nothing else will proceed.
	 *
	 */
	private function loadPresentationSetup()
	{
		$status = false;
		$setup_db = wed_getDBObject('presentation_setups');
		
		if ( ($setup_db->loadSetupCode($this->options['SETUP_CODE'])) || ($setup_db->loadSetupID($this->options['SETUP_ID'])) )
		{	
			$this->options['SETUP_DB']          = $setup_db;
			$this->options['SETUP_ID']          = $setup_db->getValue('id');
			$this->options['SETUP_MAX']         = $setup_db->getValue('max');
			$this->options['SETUP_ACTIVE']      = $setup_db->getValue('active');
			$this->options['SETUP_CSS']         = $setup_db->getValue('css');
			$this->options['SETUP_JS']          = $setup_db->getValue('js');
			$this->options['SETUP_JS_ASSETS']   = $setup_db->getDetail('JS_ASSETS');
			$this->options['ACC_WRAP']          = $setup_db->getDetail('ACC_WRAP',$this->options['ACC_WRAP']);
			$this->options['ACC_HEAD']          = $setup_db->getDetail('ACC_HEAD',$this->options['ACC_HEAD']);
			$this->options['ACC_CONTENT_WRAP']  = $setup_db->getDetail('ACC_CONTENT_WRAP',$this->options['ACC_CONTENT_WRAP']);
			$status = true;
		}
		
		return $status;
	}
	
	private function loadJavascript()
	{
		if (!is_null($this->options['SETUP_JS']))
		{
			// Send JS over to jsdirector
			$js_array = array(
				'ID'     => 'PRES_'.$this->options['SETUP_ID'],
				'LOAD'   => true,
				'KEY'    => 'JS_READY_CODE',
				'TYPE'   => 'SCRIPT',
				'SCRIPT' => $this->options['SETUP_JS']
				);
			
			wed_addNewJavascriptAsset($js_array);
		}
		
		$this->loadJavascriptAssets();
	}
	
	private function loadJavascriptAssets()
	{
		if (!is_null($this->options['SETUP_JS_ASSETS']))
		{
			$js_array = explode(',', $this->options['SETUP_JS_ASSETS']);
			wed_loadJavascriptAssets($js_array);
		}
	}
	
	public function setHTML($options=null)
	{
		return $this->buildPresentation();
	}
}
<?php
/*
 * @version		$Id: tab_detail.php 1.0 2009-03-03 $
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
 * tab_detail.php
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

class tab_detail extends details
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
		$this->options['ACTUAL_CONTENT']    = null;
		$this->options['COMPONENT']         = null;  // Components are system wide modules found in the components directory
		$this->options['NAME']              = null;   // NOT SURE what name does??
		$this->options['SETUP']             = null;  // code for the presentation_setups db
		$this->options['SETUP_DB']          = false; // presentation_setups db object
		// $this->options['SCHEDULE_DB']       = false; // banner_schedule db object
		$this->options['LIST_OBJECT']       = false; // list db object
		$this->options['CONTENT_OBJ']       = null; // connect db object
		$this->options['CODE_LIST']         = null;
		
		// $this->options['SETUP_MAX']         = 0;     // Max number of items in rotating banner
		$this->options['SETUP_ACTIVE']      = 'N';   // Is the presentation object active?
		$this->options['SETUP_CSS']         = null;  // Optional CSS for the presentation
		$this->options['SETUP_JS']          = null;  // Optional javascript for the presentation
		$this->options['SETUP_JS_ASSETS']   = null;  // Optional load certain javascript assets
		$this->options['SCHEDULE_KEYS']     = null;
		$this->options['SCHEDULE_RESULT']   = null;

		// Default Tab Styles
		$this->options['STYLE']                 = null;
		$this->options['MAIN_OUTER_WRAP']       = '%CONTENT%';
		$this->options['TAB_WRAP']              = '%CONTENT%';
		$this->options['TAB_HEADERS_WRAP']      = '';
		$this->options['TAB_HEAD_WRAP']         = '';
		$this->options['TAB_SUBHEAD']           = '';	
		$this->options['TAB_HEAD_ICON']         = '';
		$this->options['TAB_CONTENT_WRAP']      = '';
		$this->options['TAB_CONTENT_PANE_WRAP'] = '';
		$this->options['ACTIVE_CLASS']      = 'active';
		
		$this->addOptions($options);
		
		// This allows for the user to put a string of codes in the content area between the opening and closing
		// tags. It would need to be comma delimited. Example: code1,code2,code3
		if (!is_null($this->options['ACTUAL_CONTENT']))
		{
			$this->options['ACTUAL_CONTENT'] = str_replace('<p>', '', $this->options['ACTUAL_CONTENT']);
			$this->options['ACTUAL_CONTENT'] = str_replace('</p>', '', $this->options['ACTUAL_CONTENT']);
			$this->options['CODE_LIST']      = explode(',', $this->options['ACTUAL_CONTENT']);
		}
		
	}
	
	private function buildPresentation()
	{
		if ((!$this->loadPresentationSetup()) || ($this->options['SETUP_ACTIVE']!='Y') || (!$this->loadContent()))
		{
			wed_changeSystemErrorCode($this->options['ERROR_CODE']);
			return null;
		}
		
		// This will load a component file if that is what we are using to create this presentation
		$this->loadComponent();
		
		// This is a database object that holds the records for whatever content we are displaying
		$connect_db   = $this->options['CONTENT_OBJ'];
		
		// The html here has to be built in two sections, the header, and the actual content.
		// The header is used by the tab interface to form the actual tabs.
		$header_html  = '';
		$h_html       = '';
		$content_html = '';
		$c_html       = '';
		$html         = '';
		$active_class = $this->ACTIVE_CLASS;
		$base_id      = 'tab_pane';
		$tab_count    = 0;
		$tab_id       = '';
		
		global $walt;
		$shortcodes = $walt->getImagineer('shortcodes');
		
		$rec = 0;

		while ($connect_db->moveRecordList($rec))
		{
			// First get the article code and let shortcodes evaluate it
			// If it evaluates to null then we don't run this tab. This
			// way you can do timed schedules on certain content and instead
			// of showing an empty tab, none shows at all and it is skipped.
			// $code    = $connect_db->getValue('cnt_code');
			$content = $connect_db->getFormattedValue('FULLARTICLE');	
			$content = wed_renderContent($content); // $shortcodes->getHTML(array('HTML'=>$content));
			
			if (!empty($content))
			{
				$present = getImagineer('presentations');
				$id      = $present->newPresentation(array('type' => 'content', 'code' => $connect_db->getValue('cnt_code'), 'format' => 'TAB'));
				$content = (!$id) ? null : $present->getHTML(array('ID'=>$id));
				
				// Set the id for this tab
				$tab_count++;
				$tab_id       = $base_id . $tab_count;
			
				// insert the icon for the tab here (optional)
				$icon_class   = $connect_db->getDetail('ICON_CLASS');
				$icon_html    = (!is_null($icon_class)) ? str_replace('%ICON_CLASS%', $icon_class, $this->options['TAB_HEAD_ICON']) : null;
			
				// format the header and put it in a separate html var
				$header_html = str_replace('%TAB_ID%', $tab_id, $this->options['TAB_HEAD_WRAP']);
				$header_html = str_replace('%CONTENT%', $connect_db->getFormattedValue('TAB_HEADER'), $header_html);
				
				$sub_head    = $connect_db->getFormattedValue('TAB_SUBHEAD');
				
				if (!is_null($sub_head))
				{
					$sub_head = str_replace('%CONTENT%', $sub_head, $this->options['TAB_SUBHEAD']);
				}
				
				$header_html = str_replace('%SUBHEAD%', $sub_head, $header_html);	
				$header_html = str_replace('%ICON%', $icon_html, $header_html);
				$header_html = str_replace('%ACTIVE%', $active_class, $header_html);
				$h_html     .= $header_html;
			
				// format the content
				$content_html = str_replace('%TAB_ID%', $tab_id, $this->options['TAB_CONTENT_PANE_WRAP']);
				
				// add the TITLE to the article
				// $content  = '<h2>'.$connect_db->getFormattedValue('TITLE').'</h2>' . $content;
			
				$content_html = str_replace('%CONTENT%', $content, $content_html);
				$content_html = str_replace('%ACTIVE%', $active_class, $content_html);
			
				$c_html .= $content_html;
			
				// after the first time an actual tab is formatted, erase the active class
				$active_class = '';
			}
			
			$rec++;
		}
		
		// Now wrap the entire header
		$h_html = str_replace('%CONTENT%', $h_html, $this->options['TAB_HEADERS_WRAP']);
		
		// Now wrap the content panes
		$c_html = str_replace('%CONTENT%', $c_html, $this->options['TAB_CONTENT_WRAP']);
		
		// Now wrap the combined header and content
		$html = str_replace('%CONTENT%', $h_html . $c_html, $this->options['TAB_WRAP']);
		
		// Finally the main outer wrap which is optional
		$html = str_replace('%CONTENT%', $html, $this->options['MAIN_OUTER_WRAP']);
		
		$data_options = $this->getDataOptions();
		
		$html = str_replace('%DATA_OPTIONS%', $data_options, $html);
		
		// Adding the Data Options for the Zozo Tabs
		// {"theme": "silver", "orientation": "horizontal", "animation": {"duration": 800, "effects": "slideH"}}
		
		// Add the CSS Style Section before the Presentaion
		$html = $this->options['SETUP_CSS'] . $html;
		
		$this->loadCSSAssets();
			
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
		
		if ($setup_db->loadSetupCode($this->options['SETUP']))
		{	
			$this->options['SETUP_DB']         = $setup_db;
			$this->options['COMPONENT']        = $setup_db->getDetail('COMPONENT');
			$this->options['SETUP_ID']         = $setup_db->getValue('id');
			$this->options['SETUP_MAX']        = $setup_db->getValue('max');
			$this->options['SETUP_ACTIVE']     = $setup_db->getValue('active');
			$this->options['SETUP_CSS']        = $setup_db->getValue('css');
			$this->options['SETUP_JS']         = $setup_db->getValue('js');
			$this->options['SETUP_JS_ASSETS']  = $setup_db->getDetail('JS_ASSETS');
			$this->options['SETUP_CSS_ASSETS'] = $setup_db->getDetail('CSS_ASSETS');
			$this->options['MAIN_OUTER_WRAP']       = $setup_db->getDetail('MAIN_OUTER_WRAP',$this->options['MAIN_OUTER_WRAP']);
			$this->options['TAB_WRAP']              = $setup_db->getDetail('TAB_WRAP',$this->options['TAB_WRAP']);
			$this->options['TAB_HEADERS_WRAP']      = $setup_db->getDetail('TAB_HEADERS_WRAP',$this->options['TAB_HEADERS_WRAP']);
			$this->options['TAB_HEAD_WRAP']         = $setup_db->getDetail('TAB_HEAD_WRAP',$this->options['TAB_HEAD_WRAP']);
			$this->options['TAB_HEAD_ICON']         = $setup_db->getDetail('TAB_HEAD_ICON',$this->options['TAB_HEAD_ICON']);
			$this->options['TAB_CONTENT_WRAP']      = $setup_db->getDetail('TAB_CONTENT_WRAP',$this->options['TAB_CONTENT_WRAP']);
			$this->options['TAB_CONTENT_PANE_WRAP'] = $setup_db->getDetail('TAB_CONTENT_PANE_WRAP',$this->options['TAB_CONTENT_PANE_WRAP']);
			$status = true;
		}
		
		return $status;
	}
	
	private function loadContent()
	{
		$status = true;
		
		if (is_null($this->options['CONTENT_OBJ']))
		{
			$content = wed_getDBObject('content_connect',$this->options['CLASS_NAME']);
			
			if ($content->getContent($this->options))
			{
				$this->options['CONTENT_OBJ'] = $content;
			}
			else
			{
				$status = false;
			}
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
		if (!is_null($this->component_object))
		{
			$js_array = $this->component_object->loadJSAssets();
			
			foreach ($js_array as $asset)
			{
				wed_addNewJavascriptAsset($asset);
			}
		}
		elseif (!is_null($this->options['SETUP_JS_ASSETS']))
		{
			$js_array = explode(',', $this->options['SETUP_JS_ASSETS']);
			wed_loadJavascriptAssets($js_array);
		}
	}
	
	private function loadCSSAssets()
	{
		if (!is_null($this->component_object))
		{
			$css_array = $this->component_object->loadCSSAssets();
			
			foreach ($css_array as $asset)
			{
				wed_addCSSAsset($asset);
			}
		}
		elseif (!is_null($this->options['SETUP_CSS_ASSETS']))
		{
			$css_array = explode(',', $this->options['SETUP_CSS_ASSETS']);
			wed_loadCSSAssets($css_array);
		}
	}
	
	private function getDataOptions()
	{
		$options = null;
		
		if ( (!is_null($this->component_object)) && method_exists($this->component_object,'getDataOptions') )
		{
			$options = $this->component_object->getDataOptions();
		}
		
		return $options;
	}
	
	public function setHTML($options=null)
	{
		return $this->buildPresentation();
	}
}
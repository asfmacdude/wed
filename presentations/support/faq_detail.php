<?php
/*
 * @version		$Id: faq_detail.php 1.0 2009-03-03 $
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
 * faq_detail.php
 * 
 * This is the detail object for presentations that displays faqs in an accordian interface
 * using zozo_accordion
 *
 */

class faq_detail extends details
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
		$this->options['ERROR_CODE']        = 27; // Unique error code assigned to this presentation
		$this->options['COMPONENT']         = 'zozo_accordion';  // Components are system wide modules found in the components directory
		$this->options['ID']                = 'tab1'; // this is assigned by Presentations
		$this->options['KEYWORDS']          = null;   // faqs are searched by keywords, this should be an array
		$this->options['SETUP_DB']          = false; // presentation_setups db object
		// $this->options['SCHEDULE_DB']       = false; // banner_schedule db object
		$this->options['LIST_OBJECT']       = false; // list db object
		$this->options['SETUP_ID']          = null;  // actual record id on the presentation_setups db
		$this->options['SETUP_CODE']        = 'zozo_accordion';  // code of the presentation_setup (zozo_accordion)
		$this->options['SETUP_TAG']         = null;  // defaults to general
		$this->options['TYPE_ID']           = null;  // type id of the content
		$this->options['HEADING']           = 'General Heading'; 
		$this->options['SETUP_ACTIVE']      = 'N';   // Is the presentation object active?
		$this->options['SETUP_CSS']         = null;  // Optional CSS for the presentation
		$this->options['SETUP_CSS_ASSETS']  = null;  // Optional CSS for the presentation
		$this->options['SETUP_JS']          = null;  // Optional javascript for the presentation
		$this->options['SETUP_JS_ASSETS']   = null;  // Optional load certain javascript assets
		$this->options['SCHEDULE_KEYS']     = null;
		$this->options['SCHEDULE_RESULT']   = null;

		// Default Accordion Styles
		$this->options['MAIN_OUTER_WRAP']       = '%CONTENT%';
		$this->options['ACC_WRAP']              = '%CONTENT%';
		$this->options['ACC_HEAD_WRAP']         = '%CONTENT%';
		$this->options['ACC_CONTENT_WRAP']      = '%CONTENT%';
		
		$this->addOptions($options);
	}
	
	private function buildPresentation()
	{
		if ((!$this->loadPresentationSetup()) || ($this->options['SETUP_ACTIVE']!='Y') || (!$this->loadFaqList()))
		{
			wed_changeSystemErrorCode($this->options['ERROR_CODE']);
			return null;
		}
		
		// This will load a component file if that is what we are using to create this presentation
		$this->loadComponent();
		
		$faq_db         = $this->options['LIST_OBJECT'];
		$question_html  = '';
		$answer_html    = '';
		$html           = '';
		
		global $walt;
		$shortcodes = $walt->getImagineer('shortcodes');
		
		$rec = 0;

		while ($faq_db->moveRecordList($rec))
		{
			/*
			 * Load the QUESTION and the ANSWER form the faq table
			 * Then run shortcodes on the answer to evaluate the content to make
			 * sure it doesn't evaluate to null. If it is null, then we skip this one.
			 *
			 */
			$question = $faq_db->getFormattedValue('QUESTION');
			$answer   = $faq_db->getFormattedValue('ANSWER');
			$answer   = $shortcodes->getHTML(array('HTML'=>$answer));
			
			if (!empty($answer))
			{
				// Set the question
				$question_html = str_replace(array('%CONTENT%'), array($question), $this->options['ACC_HEAD_WRAP']);
				
				// Set the answer
				$answer_html   = str_replace(array('%CONTENT%'), array($answer), $this->options['ACC_CONTENT_WRAP']);
				
				// Wrap it up
				$html .= str_replace(array('%CONTENT%'), array($question_html.$answer_html), $this->options['ACC_WRAP']);
			}
			
			// Move to the next record
			$rec++;
		}
		
		// Finally the main outer wrap which is optional
		$html = str_replace('%CONTENT%', $html, $this->options['MAIN_OUTER_WRAP']);
		$data_options = $this->getDataOptions();
		$html = str_replace('%DATA_OPTIONS%', $data_options, $html);
		// Adding the Data Options for the Zozo Accordion
		// {"theme": "silver", "orientation": "horizontal", "animation": {"duration": 800, "effects": "slideH"}}
		
		// IMPORTANT!!: Add HEADING after the content has been wrapped
		$html = '<h3>'.$this->options['HEADING'].'</h3>' . $html;
		
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
		
		if ( ($setup_db->loadSetupCode($this->options['SETUP_CODE'])) || ($setup_db->loadSetupID($this->options['SETUP_ID'])) )
		{	
			$this->options['SETUP_DB']          = $setup_db;
			// $this->options['COMPONENT']         = $setup_db->getDetail('COMPONENT');
			$this->options['SETUP_ID']          = $setup_db->getValue('id');
			$this->options['SETUP_MAX']         = $setup_db->getValue('max');
			$this->options['SETUP_ACTIVE']      = $setup_db->getValue('active');
			$this->options['SETUP_CSS']         = $setup_db->getValue('css');
			$this->options['SETUP_JS']          = $setup_db->getValue('js');
			$this->options['SETUP_JS_ASSETS']   = $setup_db->getDetail('JS_ASSETS');
			$this->options['ACC_WRAP']          = $setup_db->getDetail('ACC_WRAP',$this->options['ACC_WRAP']);
			$this->options['ACC_HEAD_WRAP']     = $setup_db->getDetail('ACC_HEAD_WRAP',$this->options['ACC_HEAD_WRAP']);
			$this->options['ACC_CONTENT_WRAP']  = $setup_db->getDetail('ACC_CONTENT_WRAP',$this->options['ACC_CONTENT_WRAP']);
			$status = true;
		}
		
		return $status;
	}
	
	private function loadFaqList()
	{	
		$status = false;
		$faq_db = wed_getDBObject('faq_sites_connect');
		$keywords = $this->options['KEYWORDS'];
		
		if (!is_array($keywords))
		{
			$keywords = explode(',', $keywords);
		}
		
		if ($faq_db->searchFaqKeywords($keywords))
		{
			$this->options['LIST_OBJECT'] = $faq_db;
			$status = true;	
		}
		
		return $status;
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
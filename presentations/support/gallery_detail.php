<?php
/*
 * @version		$Id:gallery_detail.php 1.0 2009-03-03 $
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
 * gallery_detail.php
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

class gallery_detail extends details
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
		$this->options['SETUP_ID']          = null;  // actual record id on the presentation_setups db
		$this->options['SETUP_CODE']        = null;  // code of the presentation_setup
		// $this->options['SETUP_MAX']         = 0;     // Max number of items in rotating banner
		$this->options['SETUP_ACTIVE']      = 'N';   // Is the presentation object active?
		$this->options['SETUP_CSS']         = null;  // Optional CSS for the presentation
		$this->options['SETUP_JS']          = null;  // Optional javascript for the presentation
		$this->options['SETUP_JS_ASSETS']   = null;  // Optional load certain javascript assets
		$this->options['SCHEDULE_KEYS']     = null;
		$this->options['SCHEDULE_RESULT']   = null;
		
		$this->options['IMAGE_SIZE']        = null;  // Usually shown as 250_100
		$this->options['IMAGE_CROP_SIZE']   = null;  // Also 100_100
		$this->options['IMAGE_CROP_CODE']   = null; 
		$this->options['CATEGORY']          = null;  // Can be accessed from the URL
		$this->options['HEADING']           = null;
		$this->options['IMAGE_MAX']         = 0;
		$this->options['RANDOMIZE']         = false;
		$this->options['SHOW_MORE']         = false; // Shows link to show more of the gallery
		
		// Default Gallery Styles
		$this->options['HEADING_WRAP']      = '<h3>%CONTENT%</h3>';
		$this->options['UL_WRAP']           = '%CONTENT%';
		$this->options['LI_WRAP']           = '%CONTENT%';

		$this->options['ACTIVE_CLASS']      = 'active';
		
		$this->addOptions($options);
	}
	
	private function buildPresentation()
	{
		$html = null;
		
		if ((!$this->loadPresentationSetup()) || ($this->options['SETUP_ACTIVE']!='Y'))
		{
			wed_changeSystemErrorCode($this->options['ERROR_CODE']);
			return null;
		}
		
		$options['CATEGORY'] = $this->getImageCategory();
		$options['SIZE']     = $this->options['IMAGE_SIZE'];
		$img_obj = wed_getImageObject($options);
		
		if ($img_obj->loadImageDirectory()) // Loads the directory of images
		{
			$html = '';
			$item = 0;
		
			while ($img_obj->moveFileListPointer($item))
			{
				$image_path = $img_obj->getCurrentFilePath();
				
				$thumb_options = array(); // reset the array each time thru
				$thumb_options['SOURCE'] = $image_path;
		
				if (!is_null($this->options['IMAGE_CROP_SIZE']))
				{
					$sizes = explode('_', $this->options['IMAGE_CROP_SIZE']);
					$thumb_options['WIDTH']  = (isset($sizes[0])) ? $sizes[0] : 0;
					$thumb_options['HEIGHT'] = (isset($sizes[1])) ? $sizes[1] : 0;
				}
		
				$thumb_options['ZOOM_CROP'] = (!is_null($this->options['IMAGE_CROP_CODE'])) ? $this->options['IMAGE_CROP_CODE'] : 0;

				$thumb_path = $img_obj->getFileThumbPath($thumb_options);
				$html .= str_replace(array('%IMAGE_PATH%','%THUMB_PATH%'), array($image_path,$thumb_path), $this->options['LI_WRAP']);
				$item++;
			}
			
			$html = str_replace('%CONTENT%', $html, $this->options['UL_WRAP']);
			$html = (!is_null($this->options['HEADING'])) ? str_replace('%CONTENT%', $this->options['HEADING'], $this->options['HEADING_WRAP']) . $html : $html;
			$html = $html . $this->seeMorePhotosLink();
			
			// Add the CSS Style Section before the Presentaion
			$html = $this->options['SETUP_CSS'] . $html;
			
			// Add any necessary javscript code
			$this->loadJavascript();
		}
		
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
			$this->options['SETUP_DB']        = $setup_db;
			$this->options['SETUP_ID']        = $setup_db->getValue('id');
			$this->options['SETUP_MAX']       = $setup_db->getValue('max');
			$this->options['SETUP_ACTIVE']    = $setup_db->getValue('active');
			$this->options['SETUP_CSS']       = $setup_db->getValue('css');
			$this->options['SETUP_JS']        = $setup_db->getValue('js');
			$this->options['SETUP_JS_ASSETS'] = $setup_db->getDetail('JS_ASSETS');
			$this->options['UL_WRAP']         = $setup_db->getDetail('UL_WRAP',$this->options['UL_WRAP']);
			$this->options['LI_WRAP']         = $setup_db->getDetail('LI_WRAP',$this->options['LI_WRAP']);
			$status = true;
		}
		
		return $status;
	}
	
	private function getImageCategory()
	{
		if (is_null($this->options['CATEGORY']))
		{
			$call_parts = wed_getSystemValue('CALL_PARTS');
			return (isset($call_parts[1])) ? $call_parts[1] : 'general';
		}
		else
		{
			return $this->options['CATEGORY'];
		}
	}
	
	private function seeMorePhotosLink()
	{
		$html ='';
		$call_parts = wed_getSystemValue('CALL_PARTS');

		if ( (isset($call_parts[1])) && ($this->options['SHOW_MORE']) )
		{
			$html = '<p><a href="/photo/'.$call_parts[1].'">See More {{GROUP_TITLE}} Photos></a></p>';
		}
		elseif (!is_null($this->options['CATEGORY'])) // A category was specified
		{
			$html = '<p><a href="/photo/'.$this->options['CATEGORY'].'">See More {{GROUP_TITLE}} Photos></a></p>';
		}
		
		return $html;
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
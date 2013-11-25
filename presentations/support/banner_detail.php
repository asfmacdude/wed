<?php
/*
 * @version		$Id: banner_detail.php 1.0 2009-03-03 $
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
 * banner_detail.php
 * 
 * This is the detail object for presentations that displays banners. It is in development
 * at this time.
 *
 * It works through 3 tables:
 * banners - holds info about each banner which can be image based or html.
 * presentation_setups - holds the information about each banner presentation such as size and max number of banners
 * banner_schedule - connects the banners to the banner setup through schedule, start date, end date, etc.
 *
 */

class banner_detail extends details
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
		$this->options['ID']                = 'banner1'; // this is assigned by Presentations
		$this->options['NAME']              = null;
		$this->options['DETAILS']           = null; // Allows for an alternate set of menu details
		$this->options['SETUP_DB']          = false; // presentation_setups db object
		$this->options['SCHEDULE_DB']       = false; // banner_schedule db object
		$this->options['BANNERS_DB']        = false; // banners db object
		$this->options['SETUP_ID']          = null;  // actual record id on the presentation_setups db
		$this->options['SETUP_TITLE']       = null;  // title of the banner_setup
		$this->options['SETUP_MAX']         = 0;     // Max number of items in rotating banner
		$this->options['SETUP_ACTIVE']      = 'N';   // Is the banner object active?
		$this->options['SETUP_CSS']         = null;  // Optional CSS for the banner
		$this->options['SETUP_JS']          = null;  // Optional javascript for the banner
		$this->options['SETUP_JS_ASSETS']   = null;  // Optional load certain javascript assets
		$this->options['SCHEDULE_KEYS']     = null;
		$this->options['SCHEDULE_RESULT']   = null;
		$this->options['UL_ID']             = null;
		$this->options['UL_CLASS']          = null;
		$this->options['UL_FORMAT']         = '<ul id="%UL_ID%" class="%UL_CLASS%">%s</ul>';
		$this->options['LI_FORMAT']         = '<li class="%LI_CLASS%"><a href="%LINK%"><img src="%PATH%" /></a></li>';
		$this->options['LI_CLASS']          = null;
		$this->options['CSS']               = null;
		$this->options['JS']                = null;
		$this->options['SHUFFLE']           = 'Y';  // Defaults to Yes
		$this->addOptions($options);
	}
	
	/*
	 * buildBanner
	 *
	 * This function runs all the necessary functions to load and build the html for
	 * displaying the banner on the page.
	 *
	 * First - loadBannerSetup
	 * This loads the setup object and loads values into options
	 *
	 * Second - loadBannerSchedule
	 * This loads the schedules for the banners, randomizes the list and puts the results
	 * into options
	 *
	 * Third - loadBanners
	 * This takes the schedule results and loads each individual banner. That final_list
	 * is sent to buildBannerHTML to format it into the proper HTML.
	 *
	 */
	private function buildBanner()
	{
		// Load the banner setup object and values
		$this->loadBannerSetup();
		$final_list = null;
		
		// If the proper values are loaded, proceed and load schedules	
		if ( ($this->options['SETUP_ACTIVE']==='Y') && ($this->options['SETUP_MAX']>0) )
		{
			$this->loadBannerSchedule();
		}

		// If there are any schedules loaded, we now load each individual banner
		if (!is_null($this->options['SCHEDULE_RESULT']))
		{
			$final_list = $this->loadBanners(); 
		}
		
		return $this->buildBannerHTML($final_list);
	}
	
	/*
	 * loadBannerSetup
	 *
	 * This function loads the initial setup for the banner and transfers
	 * the needed values into $this->options. Obviously, if this doesn't happen,
	 * nothing else will proceed.
	 *
	 */
	private function loadBannerSetup()
	{
		$setup_db = wed_getDBObject('presentation_setups');
		
		if ( ($setup_db->loadSetupTitle($this->options['SETUP_TITLE'])) || ($setup_db->loadSetupID($this->options['SETUP_ID'])) )
		{	
			$this->options['SETUP_DB']        = $setup_db;
			$this->options['SETUP_ID']        = $setup_db->getValue('id');
			$this->options['SETUP_MAX']       = $setup_db->getValue('max');
			$this->options['SETUP_ACTIVE']    = $setup_db->getValue('active');
			$this->options['SETUP_CSS']       = $setup_db->getValue('css');
			$this->options['SETUP_JS']        = $setup_db->getValue('js');
			$this->options['SETUP_JS_ASSETS'] = $setup_db->getDetail('JS_ASSETS');
			$this->options['UL_FORMAT']       = $setup_db->getDetail('UL_FORMAT',$this->options['UL_FORMAT']);
			$this->options['LI_FORMAT']       = $setup_db->getDetail('LI_FORMAT',$this->options['LI_FORMAT']);
			$this->options['SHUFFLE']         = $setup_db->getDetail('SHUFFLE',$this->options['SHUFFLE']);
		}
	}
	
	/*
	 * loadBannerSchedule
	 *
	 * This function loads the schedules for the particular banner and then
	 * if the resulting array is larger than the MAX called for, it will randomize
	 * the keys to the array. After that, the final results are plugged into SCHEDULE_RESULT.
	 *
	 */
	private function loadBannerSchedule()
	{
		$schedule_db = wed_getDBObject('banner_schedule');
		$result      = $schedule_db->selectByIDandDate($this->options['SETUP_ID']);
		
		if ($result)
		{
			$this->options['SCHEDULE_DB']   = $schedule_db;
			$this->options['SCHEDULE_KEYS'] = $this->randomizeBanners($result);
			
			$final_results = array();
			
			foreach ($this->options['SCHEDULE_KEYS'] as $key=>$value)
			{
				$final_results[$key] = $result[$value];
			}
			
			$this->options['SCHEDULE_RESULT'] = $final_results;
		}
	}
	
	/*
	 * loadBanners
	 *
	 * This function takes the SCHEDULE_RESULT array and loads each banner that is called
	 * for in the schedule as long as it is marked ACTIVE. The results are plugged into a final_list array
	 * and returned. This array will be used to format the banner into HTML.
	 *
	 */
	private function loadBanners()
	{
		$image_db   = wed_getDBObject('banners');
		$final_list = null;
		
		if (!is_null($this->options['SCHEDULE_RESULT']))
		{
			foreach ($this->options['SCHEDULE_RESULT'] as $key=>$value)
			{
				$image_id = $value['bsch_banner_id'];
				
				if ( ($image_db->loadImageID($image_id)) && ($image_db->getValue('active')==='Y') )
				{	
					$code           = $image_db->getDetail('CODE');
					$link           = $image_db->getDetail('LINK');
					$formatted_link = wed_formatLink(array('CODE' => $code, 'LINK'=>$link));
					
					$final_list[] = array(
						'TITLE'     => $image_db->getValue('title'),
						'WIDTH'     => $image_db->getValue('width'),
						'HEIGHT'    => $image_db->getValue('height'),
						'HTML'      => $image_db->getValue('html'),
						'PATH'      => $image_db->getDetail('PATH'),
						'LINK'      => $formatted_link
					);
				}
			}
		}
		
		return $final_list;
	}
	
	private function randomizeBanners($result=array())
	{
		$rand_array = (count($result)>$this->options['SETUP_MAX']) ? array_rand($result, $this->options['SETUP_MAX']) : array_keys($result);
		
		if ($this->options['SHUFFLE']==='Y')
		{
			shuffle($rand_array);
		}
		
		return $rand_array;
	}
	
	private function loadJavascript()
	{
		if (!is_null($this->options['SETUP_JS']))
		{
			// Send JS over to jsdirector
			$js_array = array(
				'ID'     => 'BANNER_'.$this->options['SETUP_ID'],
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
	
	private function buildBannerHTML($final_list)
	{
		// The final_list is an array which has to be turned into a UL element
		$html = null;

		if (is_array($final_list))
		{
			$ul_format = $this->options['UL_FORMAT'];
			$ul_format = str_replace('%UL_ID%', $this->options['UL_ID'], $ul_format);
			$ul_format = str_replace('%UL_CLASS%', $this->options['UL_CLASS'], $ul_format);
		
			$li_format = $this->options['LI_FORMAT'];
			$li_format = str_replace('%LI_CLASS%', $this->options['LI_CLASS'], $li_format);
			
			foreach ($final_list as $key=>$value)
			{
				$line_item = $li_format;
				
				foreach ($value as $x_key=>$y_value)
				{
					$search = '%' . $x_key . '%';
					$line_item = str_replace($search, $y_value, $line_item);
				}
				
				$html .= $line_item;
			}
			
			$html = sprintf($ul_format, $html);
			
			// Add the CSS Style Section before the Banner
			$html = $this->options['SETUP_CSS'] . $html;
			
			// Add any necessary javscript code
			$this->loadJavascript();
		}
		
		return $html;	
	}
	
	public function setHTML($options=array())
	{
		return $this->buildBanner();
	}
}
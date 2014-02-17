<?php

/*
 * @version		$Id: professor.php 1.0 2009-03-03 $
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
 * professor.php
 * 
 * The Professor is just who he says he is, a professor who knows everything, a know-it-all.
 * He keeps up with a knowledge of everything going on in this sleepy little town. And when
 * requested, he can spit out the information on a whem. 
 */

class professor extends imagineer
{
	public $options   = array();
	public $theme_obj = null;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new professor();
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
		$this->loadThemeObject();
		$this->setToken();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']            = __CLASS__;
		$this->options['LOCAL_PATH']            = dirname(__FILE__);
		$this->options['LOG']                   = array();
		$this->options['SITE_ID']               = null;
		$this->options['SITE_NAME']             = null;
		$this->options['SETTINGS']              = array();
		$this->options['ASSETS']                = null; // Becomes the assets object of the theme
		$this->options['SHOW_CODE']             = null;
		$this->options['RESTRICTED']            = array();
	}
	
	protected function loadThemeObject()
	{
		$theme_obj = null;
		$setup     = THEME_BASE . 'theme_setup.php';
		
		if (file_exists($setup))
		{
			include_once($setup);
			$this->theme_obj = new theme_setup();
		}
		
	}
	
	public function loadAllSettings()
	{	
		// Set Device Detection
		$this->options['SETTINGS']['DEVICE']  = DEVICE;
		
		// Set USER_IP which is the user ip address that this user is accessing the website
		$this->options['SETTINGS']['USER_IP'] = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : null;
		
		// These are used to check and see if alternate files and directories
		// are available to accomodate mobile layouts and designs
		$this->options['SETTINGS']['ALT_DEVICE_TAGS'] = array('ipad','mobile');
		/*
		 * Check the current Site and the system site varibles
		 *
		 * Look ay table system_config and you will find settings for all sites, plus settings
		 * that are global or system wide. First we check the sites table to make sure the
		 * two sites exists.
		 */
		$this->checkSiteDomain();
		$this->checkSiteDomain('system');
		/*
		 * Set the Global Theme for this site
		 *
		 * This can be overridden in the control codes
		 */
		$this->setThemeSetup($this->options['THEME_ID']);
		/*
		 * Setup the SYSTEM_ERROR_CODE
		 *
		 * Initially this is set to null. If anywhere in the process, this is set to anything else,
		 * the system will invoke the error page.
		 */
		$this->options['SETTINGS']['SYSTEM_ERROR_CODE'] = null;
		
		$search = (isset($_POST['search'])) ? $_POST['search'] : null ;
		$this->options['SETTINGS']['SEARCH_NORMAL'] = $search;
		$this->options['SETTINGS']['SEARCH_CLEAN']  = wed_cleanItUp($search);
		/*
		 * Load SITE settings from system_config
		 * Load SYSTEM settings from system_config
		 *
		 * Merge them into SETTINGS merging SYSTEM first then SITE so items that are duplicated
		 * will end up being the SITE version.
		 */
		$site_settings             = $this->getConfigObject($this->options['SITE_ID']);
		$system_settings           = $this->getConfigObject($this->options['SYSTEM_SITE_ID']);
		
		$this->options['SETTINGS'] = array_merge($this->options['SETTINGS'],$system_settings->settings);
		$this->options['SETTINGS'] = array_merge($this->options['SETTINGS'],$site_settings->settings);
		
		/*
		 * This next line pulls a list of 'connected' control codes that are
		 * acceptable to use with the current site. The list is in the following format:
		 * 'code' => ID
		 * This will be used to verify a control code before it is used
		 */
		$this->options['settings']['SITE_CODES'] = $this->getSiteControlList();

		/*
		 * This section gets the varibles from the URL and parses them.
		 * CALL_PARTS are the base url calls example: /sport/swimming/code
		 *
		 * QUERY_PARTS are any settings sent in a url query example: ?page=error?edit=2
		 */
		$clean_path = wed_parseURLPath();
		
		$this->options['SETTINGS']['CALL_PARTS'] = $clean_path['CALL_PARTS'];
		$this->options['SETTINGS']['QUERY_VARS'] = $clean_path['QUERY_VARS'];
		
		$pages = array('LIST','PAGE','ARTICLE');
		$page  = 'HOME';
		
		foreach ($clean_path['CALL_PARTS'] as $part=>$value)
		{
			if (!empty($value))
			{
				$page = $pages[$part];
			}
		}
		
		$this->options['SETTINGS']['PAGE_TYPE'] = $page;
	}
	
	// *******************************************************************
    // ****  addSetting - adds one value to the global setting array *****
    // *******************************************************************
	public function addSetting($key,$value,$student='Professor')
	{
		if (!in_array($key, $this->options['RESTRICTED']))
		{
			$this->options['SETTINGS'][$key] = $value;
		}
		else
		{
			$this->logException($key,$value,$student);
		}
	}
	
	// *******************************************************************
    // addSettingArray - adds multiple values to the global setting array 
    // *******************************************************************
	public function addSettingArray($options=array(),$student='Professor')
	{
		foreach ($options as $key=>$value)
		{
			$this->addSetting($key,$value,$student);
		}
	}
	
	// *******************************************************************
    // ****  changeErrorCode - add a code to an array allowing  **********
    // ****  for more than one error code to be recorded  ****************
    // *******************************************************************
	public function changeErrorCode($value)
	{
		$this->options['SETTINGS']['SYSTEM_ERROR_CODE'][] = $value;
	}
		
	protected function getConfigObject($id)
	{
		// NOTE: 10/24/2013 I changed this function from looking
		// by name to looking by ID to convert my sql tables into
		// proper data normalization
		return new config_detail(array('SITE_ID' => $id));
	}
	
	public function askProfessor($options = array())
	{
		/*
		 * askProfessor
		 *
		 * Each 'student' can ask the professor a question which is basically a request
		 * for some knowledge about a varible, setting or such for their work.
		 * $name is the name of the varible or setting and can be an array in case you wanted
		 * to check several varibles, but it will take only the first match.
		 * $student is the name of the class that is asking. This is used for logging purposes
		 * only.
		 */
		 $name    = (isset($options['NAME']))    ? $options['NAME']    : null;	 
		 $student = (isset($options['STUDENT'])) ? $options['STUDENT'] : 'professor';
		 $value   = (isset($options['DEFAULT'])) ? $options['DEFAULT'] : null ;
		 
		 if (is_array($name))
		 {
			 foreach ($name as $search)
			 {
				 if (isset($this->options['SETTINGS'][$search]))
				 {
					 $value = $this->options['SETTINGS'][$search];
					 break;
				 }
			 }
		 }
		 else
		 {
			 $name    = strtoupper($name);
			 $value   = (isset($this->options['SETTINGS'][$name])) ? $this->options['SETTINGS'][$name] : $value;
		 }

		 // $this->logResponse($name,$value,$student);
		 
		 return $value;
	}
	
	// *******************************************************************
    // ****  checkSiteDomain - loads the site record from the sites ******
    // ****  table and puuls the info into the settings array  ***********
    // ****  NOTE: the global theme id is loaded here  *******************
    // *******************************************************************
	public function checkSiteDomain($site=SITE_DOMAIN)
	{
		$sites      = wed_getDBObject('sites');
		$var_prefix = ($site===SITE_DOMAIN) ? null : strtoupper($site).'_';
		
		if ($sites->loadSite($site))
		{
			$site_id       = $sites->getValue('id');
			$site_name     = $sites->getValue('name');
			$site_theme_id = $sites->getValue('theme');
			$site_security = $sites->getDetail('SECURITY_LEVEL',array());
			
			if (!is_array($site_security))
			{
				// SECURITY_LEVEL is a string, example: 1,2,3,4,5
				$site_security = explode(',', $site_security);
			}
			
			wed_addSystemValue($var_prefix.'SITE_ID',$site_id);
			wed_addSystemValue($var_prefix.'SITE_NAME',$site_name);
			wed_addSystemValue($var_prefix.'THEME_ID',$site_theme_id);
			wed_addSystemValue($var_prefix.'SECURITY_LEVEL',$site_security);

			$this->options[$var_prefix.'SITE_ID']         = $site_id;
			$this->options[$var_prefix.'SITE_NAME']       = $site_name;
			$this->options[$var_prefix.'THEME_ID']        = $site_theme_id;
			$this->options[$var_prefix.'SECURITY_LEVEL']  = $site_security;
		}
		else
		{
			$err_message = $site . ' Site Not Found.';
			trigger_error($err_message, E_USER_ERROR);
		}
	}
	
	// *******************************************************************
    // ****  getSiteControlList - loads a list of control codes **********
    // ****  that are linked to this particular site. ONLY the codes *****
    // ****  that are linked here can be used  ***************************
    // *******************************************************************
	public function getSiteControlList()
	{
		$site_connect = wed_getDBObject('sites_connect');
		return $site_connect->buildSiteControlList();
	}
	
	// *******************************************************************
    // ****  verifyControlCode - this allows other objects to check ******
    // ****  and make sure that the current site is allowed to use *******
    // ****  a particular code. Here it is checked against the list  *****
    // ****  that was loaded in getSiteControlList  **********************
    // *******************************************************************
	public function verifyControlCode($code=null)
	{
		// This returns the ID or value of the array pair
		return ( (!is_null($code)) && (isset($this->options['settings']['SITE_CODES'][$code])) ) ? $this->options['settings']['SITE_CODES'][$code] : false ;
	}
	
	// *******************************************************************
    // ****  setThemeSetup - this loads the theme setup using the ********
    // ****  theme_object class. Here several system wide settings are ***
    // ****  changed such as the THEME_URL and this also lets us know ****
    // ****  if a theme has a mobile or iPad version  ********************
    // *******************************************************************
	public function setThemeSetup($theme_id)
	{
		$settings = array();
		
		// theme_id is found in the setup.php file in each theme
		if ( (isset($this->theme_obj->theme_setups[$theme_id])) && (isset($this->theme_obj->theme_setups[$theme_id]['NAME'])) )
		{
			$theme_name = $this->theme_obj->theme_setups[$theme_id]['NAME'];
			
			$settings['THEME']          = $theme_name;
			$settings['THEME_URL']      = THEME_BASE_WEB . $theme_name . DS;
			$settings['MOBILE_VERSION'] = (isset($this->theme_obj->theme_setups[$theme_id]['MOBILE'])) ? $this->theme_obj->theme_setups[$theme_id]['MOBILE'] : false;
			$settings['IPAD_VERSION']   = (isset($this->theme_obj->theme_setups[$theme_id]['IPAD']))   ? $this->theme_obj->theme_setups[$theme_id]['IPAD']   : false;
			
			wed_addSystemValueArray($settings);
		}
	}
	
	public function getRecentHistory($max=10)
	{
		$key      = 'ASFF_HISTORY';	
		$history  = (isset($_SESSION[$key])) ? wed_decodeJSON($_SESSION[$key]) : array() ;
		$counter  = 1;
		$new_list = array();
		$page_object = null;
		
		foreach ($history as $key=>$data)
		{
			if ($counter>$max)
			{
				break;
			}	
			elseif (isset($data['PAGE']))
			{
				$page_object = wed_getPageInfo($data['PAGE']);
				
				if ($page_object)
				{
					$new_list[$data['PAGE'].'_'.$counter]['TITLE'] = $page_object->getValue('title');
					$new_list[$data['PAGE'].'_'.$counter]['CODE']  = $data['PAGE'];
					$counter++;
				}
			}
		}
		
		return $new_list;
	}
	
	public function getRecentSearch($max=10)
	{
		$key      = 'ASFF_SEARCH';	
		$search   = (isset($_SESSION[$key])) ? wed_decodeJSON($_SESSION[$key]) : array() ;
		$counter  = 1;
		$new_list = array();
		
		foreach ($search as $key=>$data)
		{
			if ($counter>$max)
			{
				break;
			}	
			elseif (isset($data['TAG']))
			{
				$new_list[$data['TAG'].'_'.$counter]['TITLE']   = 'Search for: '. $data['TAG'];
				$new_list[$data['TAG'].'_'.$counter]['SEARCH']  = $data['TAG'];
				$counter++;
			}
		}
		
		return $new_list;
	}
		
	private function logResponse($name,$value,$student)
	{
		$item = '['.$name.':'.$value.']';
		
		if (isset($this->options['LOG'][$student]))
		{
			$this->options['LOG'][$student] = $this->options['LOG'][$student] . ',' . $item;
		}
		else
		{
			$this->options['LOG'][$student] = $item;
		}
	}
	
	private function logException($name,$value,$student)
	{
		$item = '[Exception-'.$name.':'.$value.']';
		
		if (isset($this->options['LOG'][$student]))
		{
			$this->options['LOG'][$student] = $this->options['LOG'][$student] . ',' . $item;
		}
		else
		{
			$this->options['LOG'][$student] = $item;
		}
	}
	
	private function setToken()
	{
		define('WED_FORM_TOKEN', wed_HashThis(time()));
		$_SESSION['WED_TOKEN'] = WED_FORM_TOKEN;
		wed_addSystemValue('WED_FORM_TOKEN',WED_FORM_TOKEN);
	}
}
?>
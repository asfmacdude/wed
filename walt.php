<?php


/*
 * wed_tools.php & wed_theme_tools.php
 *
 * This file is a collection of straight functions that can be called from
 * anywhere and provide an easy and clean way to access all of the Imagineers
 *
 */
include_once('support/wed_tools.php') ;
include_once('support/wed_theme_tools.php') ;
/*
 * details.php
 *
 * This is a small utility class that is extending from all other detail classes
 * and provides a singel source of several repeatable methods that are used in all
 * detail classes.
 */
include_once('support/details.php') ;
/*
 * imagineer.php
 *
 * This class is the parent class for all imagineers. They all extend
 * this one and gain sharable methods.
 *
 */
include_once('support/imagineer.php') ;



/*
 * Walt
 *
 * Walt becomes a class handling all thing Imagineers. Walt only load the Imagineers as 
 * they are needed rather than loading them all.
 *
 *
 */

class walt extends imagineer
{
	public $options;
	public $imagineer_list;
	public $wed_imagineers;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new walt();
        }

        return $instance;
    }

    private function __construct()
	{
		$this->setOptions();
	}
	
	public function init()
	{
		return null;
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME'] = __CLASS__;
		$this->options['LOCAL_PATH'] = dirname(__FILE__);
		$this->options['WED_PATH']   = dirname(__FILE__) . DS;
		$this->imagineer_list        = $this->getImagineerList();
		$this->options['SYS_MODE']   = 'NORMAL';
	}
	
	private function getImagineerList()
	{
		// Get a list of all possible existing Imagineers
		// We do not load them here, just get a list by iterating
		// thru the wed directory and checking each folder that
		// has a file inside that is named the same as the folder
		// Example: folder-communication, file-communications.php
		$iterator = new DirectoryIterator($this->options['WED_PATH']);
		$list = array();

		foreach ($iterator as $fileinfo)
		{
		    if ( ($fileinfo->isDir()) && (!$fileinfo->isDot()) )
		    {
		        $list[] = $fileinfo->getFilename();
		    }
		}

		return $list;
	}
	
	public function getImagineer($name)
	{
		// This returns the actual imagineer object. First it checks to see
		// if the Imagineer has been loaded and if so, returns the object
		// to the calling program. This simple design allows for only loading
		// the imagineers as they are needed
		return ($this->loadImagineer($name)) ? $this->wed_imagineers[$name] : false ;
	}
	
	private function loadImagineer($name)
	{	
		$file    = $name.'.php';
		$path    = $this->options['WED_PATH'] . $name. DS . $file;
		$success = (file_exists($path)) ? true : false;

		if ( ($success) && (!isset($this->wed_imagineers[$name])) )
		{
			include_once($path);	
				
			$this->wed_imagineers[$name] = $name::getInstance();
			$this->wed_imagineers[$name]->init();
		}

		return $success;
	}
	
	public function getImagineerValue($imagineer,$value,$default=null)
	{
		// This checks to see if a particular option exists and returns it's value
		// If it doesn't exists, there is an option to return a default
		$return_value = $default;
		
		if ($this->loadImagineer($imagineer))
		{
			$return_value = (!is_null($this->wed_imagineers[$imagineer]->$value)) ? $this->wed_imagineers[$imagineer]->$value : $return_value;
		}
		
		return $return_value;
	}
	
	/*
	*
	* Let's start the show
	*
	*/
	public function startShow()
	{	
		// Loads basic settings, system and site, loads clean_path
		$this->loadProfessor();
		
		// Checks for user cookies and/or sessions and loads user information
		$this->greetUser();
		
		$call_parts = wed_getSystemValue('CALL_PARTS');
		$call_page  = (!empty($call_parts[0])) ? $call_parts[0] : null ;
		
		/*
		 * Calling different Shows
		 *
		 * Here we can formulate and call different shows based on the value of the
		 * control code in the url.
		 *
		 * wizard is code for an ajax call. The group part of the url will usually be a
		 * file name that is being called. The query will usually contain a directory path
		 * within the theme directory where the file is actually located.
		 *
		 */
		if ($call_page=='wizard')
		{
			$this->callWizard();
		}
		else
		{
			// Records the url and user
			$this->trackUser();
			
			// Do different shows here
			$this->showPagePresentation($call_page); // Default show for now
		}
	}
	
	public function showPagePresentation($call_page=null)
	{
		// Check Site security levels
		$this->checkSiteSecurity();
		
		$this->loadImagineer('presentations');
		
		// Deprecate these lines when switch is made.
		$home_page  = wed_getSystemValue('HOME_PAGE');
		$error_page = wed_getSystemValue('ERROR_404');
		
		$call_page  = (!is_null($call_page)) ? $call_page : $home_page ;
	
		$pages =  $this->getPageCodes();
			
		$html = null;
		
		if ( (SITE_DOMAIN === 'seniorstategames.asffoundation') || (SITE_DOMAIN === 'alagames') )
		{
			foreach ($pages as $key)
			{
				$page_id  = $this->wed_imagineers['presentations']->newPresentation(array('TYPE' => 'mainpage', 'PAGE_TEMPLATE_CODE' => $key));
				$html     = $this->wed_imagineers['presentations']->getHTML(array('ID' => $page_id));
				
				if ($html)
				{
					break;
				}
			}
		}
		else
		{
			$call_page_id  = $this->wed_imagineers['presentations']->newPresentation(array('TYPE' => 'page', 'PAGE' => $call_page));
			$html = $this->wed_imagineers['presentations']->getHTML(array('ID' => $call_page_id));
		}
		
				
		if ( (!$html) || (is_null($html)) )
		{
			$donald = (isset($_GET['donald'])) ? $_GET['donald'] : 'Great';
			
			if ($donald!='quacked')
			{
				$err_message = wed_getSystemValue('SYSTEM_ERROR_CODE','No HTML');	
				trigger_error($err_message, E_USER_ERROR);
				exit();
			}
			else
			{
				var_dump($this);
			}	
		}
		
		echo $html;
		
		exit();
	}
	
	public function callWizard()
	{
		$this->loadImagineer('wizard');	
		$html = $this->wed_imagineers['wizard']->getHTML();

		if ((is_null($html)) || (!is_null(wed_getSystemValue('SYSTEM_ERROR_CODE'))))
		{
			$html = 'System Error: '.wed_getSystemValue('SYSTEM_ERROR_CODE','Unknown error occurred');
		}
		
		echo $html;
		
		exit();
	}
	
	public function loadProfessor()
	{
		$this->loadImagineer('professor');
		// Here the Professor loads all settings for the Site and for the System	
		$this->wed_imagineers['professor']->loadAllSettings();
	}
	
	public function greetUser()
	{
		$this->loadImagineer('guestdirector');
	}
	
	public function checkSiteSecurity()
	{
		if (!wed_checkSiteLevels())
		{
			include_once('classes/check.class.php');
			$site_security = wed_getSystemValue('SECURITY_LEVEL');
			
			if (is_null($site_security))
			{
				protect('*');
			}
			else
			{
				$protect_str = implode(',', $site_security);
				protect($protect_str);
			}
		}
	}
	
	public function getPageCodes()
	{
		$pages = array();
		$call_parts = wed_getSystemValue('CALL_PARTS');
		
		foreach ($call_parts as $key)
		{
			$pages[] = $key;
		}
		
		/*
		 * The first member of $pages will be empty if it is the
		 * home page so it must be reset to the system value of HOME_PAGE
		 *
		 */
		if (empty($pages[0]))
		{
			$pages[0] = wed_getSystemValue('HOME_PAGE');
		}
		
		$pages[] = wed_getSystemValue('ERROR_404');
		$pages[] = 'index';

		return $pages;	
	}
	
	public function trackUser()
	{
		$track_log  = wed_getDBObject('tracking_log');
		$track_log->newTrackingLog();
	}
	
	
} // Close walt

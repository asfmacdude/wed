<?php


/*
 * wed_tools.php
 *
 * This file is a collection of straight functions that can be called from
 * anywhere and provide an easy and clean way to access all of the Imagineers
 *
 */
include_once('wed_tools.php') ;
/*
 * details.php
 *
 * This is a small utility class that is extending from all other detail classes
 * and provides a singel source of several repeatable methods that are used in all
 * detail classes.
 */
include_once('details.php') ;
/*
 * imagineer.php
 *
 * This class is the parent class for all imagineers. They all extend
 * this one and gain sharable methods, even Walt himself.
 *
 */
include_once('imagineer.php') ;



/*
 * New Addition
 *
 * 1/22/2013
 *
 * Walt becomes a class handling all thing Imagineers. My vision is to have Walt
 * only load the Imagineers as they are needed rather than loading them all regardless
 * of whether they are needed or not. Hopefully this will make WED more efficient and faster.
 *
 * We can accomplish this two ways, either has a base set of Imagineers that load each time or
 * actually load each as needed. The latter is more favorable, but will take a bit more thought
 * because certain Imagineers have dependencies on other Imagineers and they cannot load or function
 * without them. Thus, we have to add a dependency list to each Imagineer and have Walt check that list
 * and make sure all are loaded.
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
			$this->loadWizard();
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
		$this->loadImagineer('presentations');
		$home_page  = wed_getSystemValue('HOME_PAGE');
		$error_page = wed_getSystemValue('ERROR_404');
		$call_page  = (!is_null($call_page)) ? $call_page : $home_page ;
		
		$html = null;
		
		$call_page_id  = $this->wed_imagineers['presentations']->newPresentation(array('TYPE' => 'page', 'PAGE' => $call_page));
		$html = $this->wed_imagineers['presentations']->getHTML(array('ID' => $call_page_id));
				
		if ((is_null($html)) || (!is_null(wed_getSystemValue('SYSTEM_ERROR_CODE'))))
		{
			header('Location: /'.$error_page);
			exit();
		}
		
		if ( (!$html) || (is_null($html)) )
		{
			$err_message = wed_getSystemValue('SYS_ERR_NO_HTML','No HTML');	
			trigger_error($err_message, E_USER_ERROR);
		}
		
		echo $html;
		
		exit();
	}
	
	public function loadWizard()
	{
		$this->loadImagineer('wizard');
		$html = $this->wed_imagineers['wizard']->getHTML();

		if ((is_null($html)) || (!is_null(wed_getSystemValue('SYSTEM_ERROR_CODE'))))
		{
			$html .= 'System Error: '.wed_getSystemValue('SYSTEM_ERROR_CODE','Unknown error occurred');
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
	
	public function trackUser()
	{
		$track_log  = wed_getDBObject('tracking_log');
		$track_log->newTrackingLog();
	}
	
	
} // Close walt

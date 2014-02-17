<?php
/*
 * @version		$Id: guestdirector.php 1.0 2009-03-03 $
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
 *
 * Notes
 * Guest Director handles all info about our guest and also info from gravatar.com
 *
 */


class guestdirector extends imagineer
{	
	public $options;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new guestdirector();
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
		$this->shareWithProfessor();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']      = __CLASS__;
		$this->options['LOCAL_PATH']      = dirname(__FILE__);
		$this->options['DEVELOPER_LEVEL'] = 33;
		
		$this->options['USER_ID']       = (isset($_SESSION['jigowatt']['user_id'])) ? $_SESSION['jigowatt']['user_id'] : null;
		$this->options['USER_NAME']     = (isset($_SESSION['jigowatt']['username'])) ? $_SESSION['jigowatt']['username'] : null;
		$this->options['USER_LEVEL']    = (isset($_SESSION['jigowatt']['user_level'])) ? $_SESSION['jigowatt']['user_level'] : array();
		$this->options['USER_EMAIL']    = (isset($_SESSION['jigowatt']['email'])) ? $_SESSION['jigowatt']['email'] : null;
		$this->options['USER_GRAVATAR'] = (isset($_SESSION['jigowatt']['gravatar'])) ? $_SESSION['jigowatt']['gravatar'] : null;
		
		// var_dump($this->options['USER_LEVEL']);
		
		$this->compareCurrentData();
		
		$this->options['DEVELOPER']     = (in_array($this->options['DEVELOPER_LEVEL'], $this->options['USER_LEVEL'])) ? true : false;
	}
	
	public function logUserOut()
	{
		if ((isset($_GET['call'])) && ($_GET['call']=='logout'))
		{
			$this->options['USER']->logUserOut();
		}
	}
	
	private function compareCurrentData()
	{
		// This function compares the user data in the SESSION to what is actually
		// in the login_users table and updates the SESSION accordingly
		$user_db = wed_getDBObject('login_users');
		
		if ($user_db->loadUserID($this->options['USER_ID']))
		{		
			$this->options['USER_NAME']  = $user_db->getValue('username');
			$this->options['USER_LEVEL'] = unserialize($user_db->getValue('level'));
			$this->options['USER_EMAIL'] = $user_db->getValue('email');
			
			$_SESSION['jigowatt']['username']   = $this->options['USER_NAME'];
			$_SESSION['jigowatt']['user_level'] = $this->options['USER_LEVEL'];
			$_SESSION['jigowatt']['email']      = $this->options['USER_EMAIL'];
		}
	}
	
	public function setDeveloperMode()
	{
		$mode = $this->options['USER']->getValue('developer',0);
		return ($mode==1) ? true : false;	
	}
	
	private function loadUserInformation()
	{
		/*
		 * loaduserInformation
		 *
		 * It is important to note here that even though checkForUser
		 * does not find a user in our db, we load the default values
		 * into the user_db object so that GuestDirector has an object
		 * to work from with default values.
		 */
		$user_db = wed_getDBObject('users');
		$user_db->checkForUser($this->options['USER_NAME']);	
		return $user_db;
	}
	
	private function shareWithProfessor()
	{
		$settings['USER_ID']       = $this->options['USER_ID'];
		$settings['USER_NAME']     = $this->options['USER_NAME'];
		$settings['USER_LEVEL']    = $this->options['USER_LEVEL'];
		$settings['USER_EMAIL']    = $this->options['USER_EMAIL'];
		$settings['USER_GRAVATAR'] = $this->options['USER_GRAVATAR'];
		$settings['DEVELOPER']     = $this->options['DEVELOPER'];
		
		wed_addSystemValueArray($settings);
	}
	
	public function canUserSeeThis($list=array())
	{
		$result  = false;
		$levels  = $this->USER_LEVEL;
		
		if (is_array($list))
		{
			foreach ($list as $value)
			{
				$result = (in_array($value, $levels)) ? true : $result;
			}
		}

		return $result;
	}
	
	public function getGravatarImage($size=200)
	{
		return $this->USER_GRAVATAR;
		
		/*
$email = $this->USER->getValue('email',null);
		$email = trim($email);
		$email = strtolower($email);
		$email_hash = md5($email);
		$size  = ($size===0) ? '%s' : $size;
		
		return 'http://gravatar.com/avatar/'.$email_hash.'?s='.$size.'&d=mm';
*/
	}
	
	public function isLoggedIn()
	{
		// return ($this->USER->getValue('loggedin')==1) ? true : false;
	}
	
	public function isApproved()
	{
		// return $this->USER->getValue('approved',false);
	}
	
	public function isOwner()
	{
		// return $this->USER->getValue('isowner',false);
	}
	
	public function isPremium()
	{
		// return $this->USER->getValue('ispremium',false);
	}
}
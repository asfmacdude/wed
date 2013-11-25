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
		$this->options['USER_NAME']   = $this->getUserName();
		$this->options['USER']        = $this->loadUserInformation();
		$this->options['USER_ROLES']  = $this->getUserRoles();
		$this->options['DEVELOPER']   = $this->setDeveloperMode();
		$this->logUserOut();
		$this->shareWithProfessor();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']  = __CLASS__;
		$this->options['LOCAL_PATH']  = dirname(__FILE__);
	}
	
	public function logUserOut()
	{
		if ((isset($_GET['call'])) && ($_GET['call']=='logout'))
		{
			$this->options['USER']->logUserOut();
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
		$settings['GUEST_USER_NAME']     = $this->options['USER_NAME'];
		$settings['GUEST_USER_ROLES']    = $this->options['USER_ROLES'];
		$settings['GUEST_USER_GRAVATAR'] = $this->getGravatarImage(0);
		$settings['DEVELOPER']           = $this->options['DEVELOPER'];
		
		wed_addSystemValueArray($settings);
	}
	
	private function getUserName()
	{
		$name = null;
		
		if (isset($_COOKIE['user']))
		{
			$name = addslashes($_COOKIE['user']);
		}
		elseif (isset($_SESSION['UserName']))
		{
			$name = $_SESSION['UserName'];
		}
		
		return $name;
	}
	
	private function getUserRoles()
	{
		$roles = array();
		$id    = $this->USER->getValue('id',null);
		
		if (!is_null($id))
		{
			$role_obj = new user_roles($id);
			$roles    = $role_obj->getUserRoles();
		}

		return $roles;
	}
	
	public function canUserSeeThis($list=array())
	{
		$result = false;
		$roles  = $this->USER_ROLES;
		
		if (is_array($list))
		{
			foreach ($list as $value)
			{
				$result = (in_array($value, $roles)) ? true : $result;
			}
		}

		return $result;
	}
	
	public function getGravatarImage($size=200)
	{
		$email = $this->USER->getValue('email',null);
		$email = trim($email);
		$email = strtolower($email);
		$email_hash = md5($email);
		$size  = ($size===0) ? '%s' : $size;
		
		return 'http://gravatar.com/avatar/'.$email_hash.'?s='.$size.'&d=mm';
	}
	
	public function isLoggedIn()
	{
		return ($this->USER->getValue('loggedin')==1) ? true : false;
	}
	
	public function isApproved()
	{
		return $this->USER->getValue('approved',false);
	}
	
	public function isOwner()
	{
		return $this->USER->getValue('isowner',false);
	}
	
	public function isPremium()
	{
		return $this->USER->getValue('ispremium',false);
	}
}
<?php
/*
 * @version		$Id: mickey_mouse.php 1.0 2009-03-03 $
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
 * mickey_mouse handles all guest relations as well as gathering all info and images from gravatar.com
 *
 */


class mickey_mouse extends imagineer
{	
	public $options;
	public $guest;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new mickey_mouse();
        }

        return $instance;
    }
		
	private function __construct()
	{

	}
	
	public function init()
	{
		$this->setOptions();
		$this->guest = $this->loadGuest();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
	}
	
	public function getGravatarImage($size=200)
	{
		$email = $this->getUserEmail();
		$email = trim($email);
		$email = strtolower($email);
		$email_hash = md5($email);
		
		return 'http://gravatar.com/avatar/'.$email_hash.'?s='.$size.'&d=mm';
	}
	
	public function loadGuest()
	{
		$user_obj = null;
		
		if (isset($_SESSION['jigowatt']['user_id']))
		{
			global $walt;
			$db   = $walt->getImagineer('communicore');
			$user = $db->loadDBObject('login_users');
			$user_status = $user->loadUserID($_SESSION['jigowatt']['user_id']);
			$user_obj = ($user_status) ? $user : null ;
		}
		
		return $user_obj;
	}
	
	public function getUserAttribute($attr='Name',$default='Unknown')
	{
		return (!is_null($this->user)) ? $this->user->getValue($attr,$default) : null;
	}
	
	public function getUserName()
	{
		return ((isset($_SESSION)) && (isset($_SESSION['jigowatt']['username']))) ? $_SESSION['jigowatt']['username'] : null ;
	}
	
	public function getUserEmail()
	{
		return ((isset($_SESSION)) && (isset($_SESSION['jigowatt']['email']))) ? $_SESSION['jigowatt']['email'] : null ;
	}
	
	public function getUserID()
	{
		return ((isset($_SESSION)) && (isset($_SESSION['jigowatt']['user_id']))) ? $_SESSION['jigowatt']['user_id'] : null ;
	}
	
	public function getUserLevel()
	{
		return ((isset($_SESSION)) && (isset($_SESSION['jigowatt']['user_level']))) ? $_SESSION['jigowatt']['user_level'] : null ;
	}


}
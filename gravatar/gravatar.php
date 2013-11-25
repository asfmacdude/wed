<?php
/*
 * @version		$Id: gravatar.php 1.0 2009-03-03 $
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
 * Gravatar handles gathering all info and images from gravatar.com
 *
 */


class gravatar extends tools_imagineers
{	
	public $options;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new gravatar();
        }

        return $instance;
    }
		
	private function __construct()
	{

	}
	
	public function init()
	{
		$this->setOptions();
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
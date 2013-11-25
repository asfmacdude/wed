<?php

/*
 * @version		$Id: session_manager.php 1.0 2009-03-03 $
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
 * session_manager.php
 * 
 */

class session_manager extends imagineer
{
	public $options  = array();
	protected $savePath;
    protected $sessionName;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new session_manager();
        }

        return $instance;
    }
	
	private function __construct()
	{
		session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
        
        // the following prevents unexpected effects when using objects as save handlers
		register_shutdown_function('session_write_close');
		session_start();
	}
	
	public function init()
	{
		$this->setOptions();
		$this->loadSupportFiles();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']         = __CLASS__;
		$this->options['LOCAL_PATH']         = dirname(__FILE__);
	}

    public function open($savePath, $sessionName) {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;
        return true;
    }

    public function close() {
        // your code if any
        return true;
    }

    public function read($session_id)
    {
        global $walt;
        $db         = $walt->getImagineer('communicore');
        $session_db = $db->loadDBObject('sessions','session_manager');
        
        if ($session_db->loadSessionID($session_id))
        {
	        $data     = $session_db->getValue('data');
	        $_SESSION = unserialize($data);
	        return session_encode();
        }
        
        return '';
    }

    public function write($session_id, $session_data)
    {
     	global $walt;
        $db         = $walt->getImagineer('communicore');
        $session_db = $db->loadDBObject('sessions','session_manager');  

		$session_data	= serialize($_SESSION);
		
		$sql = "INSERT INTO `sessions` (`session_id`, `session_data`, `session_site`, `session_last_use`)
				VALUES ('" . $session_id . "', '" . $session_data . "', '" . SITE_DOMAIN . "', UNIX_TIMESTAMP())
				ON DUPLICATE KEY UPDATE `session_data` = '" . $session_data . "', `session_last_use` = UNIX_TIMESTAMP()";
		
		return $session_db->writeSessionData($sql);
    }

    public function destroy($session_id)
    {
		global $walt;
        $db         = $walt->getImagineer('communicore');
        $session_db = $db->loadDBObject('sessions','session_manager');
		return $session_db->deleteByID($session_id);
    }

    public function gc($maxlifetime="1440")
    {
        global $walt;
        $db         = $walt->getImagineer('communicore');
        $session_db = $db->loadDBObject('sessions','session_manager');
        return $session_db->writeSessionData("DELETE FROM `sessions` WHERE UNIX_TIMESTAMP() - `session_last_use` > '" .$max_lifetime . "'");
    }
}
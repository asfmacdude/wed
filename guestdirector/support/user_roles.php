<?php
/*
 * @version		$Id: user_roles.php 1.0 2009-03-03 $
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

include_once('user_common.php');
/*
 *
 * Notes
 * User Roles handles all info about our guest and his roles in our system
 *
 */


class user_roles extends user_common
{	
	public $options;
		
	public function __construct($id)
	{
		$this->setOptions($id);
	}
	
	public function setOptions($id)
	{
		$this->options['CLASS_NAME']  = __CLASS__;
		$this->options['LOCAL_PATH']  = dirname(__FILE__);
		$this->options['USER_ID']     = $id;
		$this->options['USER_CONN']   = $this->getUserRolesConnection();
		$this->options['USER_ROLES']  = $this->getUserRoles();
	}
	
	public function getUserRolesConnection()
	{
		global $walt;
		$db = $walt->getImagineer('communicore');
		return $db->loadDBObject('users_in_roles');
	}
	
	public function getUserRoles()
	{
		$roles   = array();
		$db_conn = $this->USER_CONN;
		$id      = $this->USER_ID;
		
		if (!is_null($id))
		{	
			$roles = $db_conn->selectUserRoles($id);
		}
		
		return $roles;
	}
}
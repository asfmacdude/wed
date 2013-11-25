<?php
/*
 * @version		$Id: formdirector.php 1.0 2009-03-03 $
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
 * formdirector.php
 * 
 * The formdirector accepts all form calls and decides how to handle them. It
 * will capture whether we are in SUBMIT action mode, CANCEL action mode or DISPLAY action mode
 * and also whether we are creating, editing or deleting a record.
 * 
 * Notes 5/4/2012
 * When a form is called, you have to know the following:
 * THEME folder - that's where the actual form view and controller files will be
 * DATABASE such as content_main, content_control, etc,
 * FORM name such as admin, quick, user, etc. becuase sometimes you will have more than one form to edit data
 * for instance an admin form to edit everything, then a user form so he only sees a certain part of the data.
 *
 * Files
 * Each form will be stored inside a forms directory inside the theme. Inside that directory will be a directory for
 * each group of forms, probably similar forms that edit the same database. Then each form will have a controller
 * file and a view file minimum and then possibly a javascript file to handle specific javascript calls.
 * 
 * HIDDEN FIELDS
 * Each form needs to 'HIDE' the above information in hidden inputs on the form.
 * form_path = hide the literal path to the form controller here
 * form_mode = NEW or EDIT
 * form_record_id = id of the record you are editing
 *
 */
include_once('form_tools.php');

class formdirector extends imagineer
{
	public $options  = array();
	public static $action;
	public static $mode;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new formdirector();
        }

        return $instance;
    }
	
	private function __construct()
	{

	}
	
	public function init()
	{
		$this->setOptions();
		self::$action = $this->getAction();
		self::$mode   = $this->getMode();
		$this->loadSupportFiles();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']        = __CLASS__;
		$this->options['LOCAL_PATH']        = dirname(__FILE__);
		$this->options['CONTROLLER_SUFFIX'] = '_controller';
		$this->options['FORMS_DIR']         = 'forms';
	}
	
	protected function getAction()
	{
		return (isset($_REQUEST['action'])) ? $_REQUEST['action'] : 'display';
	}
	
	protected function getMode()
	{
		return (isset($_REQUEST['mode'])) ? $_REQUEST['mode'] : 'new';
	}
	
	public function callForForm($options=null)
	{
		if ( (is_null($options)) && (!isset($options['path'])) )
		{
			return null;
		}
		
		$full_path = THEME_DIR.$this->options['FORMS_DIR'].DS.$options['path'];
		$controller = basename($full_path,'.php');
		
		$html = '';
		
		if (file_exists($full_path))
		{
			include $full_path;
			$form_object = new $controller($options);
			$html = $form_object->getHTML();
		}
		
		return $html;
	}
	
	public function callForFormAjax()
	{
		// You must have the form path and the form control class to continue
		if (!isset($_REQUEST['form_path']))
		{
			return null;
		}
		
		$full_path  = $_REQUEST['form_path'];
		$controller = basename($full_path,'.php');
		
		$html = '';
		
		if (file_exists($full_path))
		{
			include $full_path;
			$form_object = new $controller();
			$html = $form_object->getHTML();
		}
		
		return $html;
	}

}
?>
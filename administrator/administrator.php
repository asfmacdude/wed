<?php

/*
 * @version		$Id: administrator.php 1.0 2009-03-03 $
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
 * Notes on Levels
 * Admin - 1
 * Web Manager - 2
 * Staff - 3
 * Editor - 4
 *
 */


class administrator extends imagineer
{	
	public $db;
	public $clock;
	public $options;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new administrator();
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
		$this->options['CLASS_NAME']    = __CLASS__;
		$this->options['LOCAL_PATH']    = dirname(__FILE__);
		$this->options['PAGE_BASIC']    = 'page_basic.php';
		// $this->options['PAGE_PATH']     = ADMIN_ROOT . $this->options['PAGE_BASIC'];
		$this->options['CONTENT_CODE']  = 'dashboard'; // default page & content
		$this->options['ALLOWED_CODES'] = explode(',',$this->getShowScript('ALLOWED_CODES'));
		// $this->options['SECTION_PATH']  = ADMIN_ROOT . 'sections' . DS;
	}
	
	public function getContentCode()
	{	
		$options = $this->parseUrl2Options();	
		$sc      = (isset($options['SC'])) ? $options['SC'] : $this->options['CONTENT_CODE'] ;
		return ($this->isCodeAllowed($sc)) ? $sc : $this->options['CONTENT_CODE'];
	}
	
	private function isCodeAllowed($code)
	{
		return (in_array($code, $this->options['ALLOWED_CODES']));
	}
	
	public function getMainMenuArray($item=null)
	{
		$sc_code = $this->getContentCode();
		
		$menu_array = array(
			'dashboard' => array(
				'TITLE' => 'Dashboard',
				'LINK'  => 'index.php',
				'ICON_CLASS' => 'home-{SIZE}',
				'PROTECT' => array('owner','superadmin','admin','staff')
				),
			'page_struct' => array(
				'TITLE' => 'Page Structures',
				'LINK'  => 'index.php?sc=page_struct',
				'ICON_CLASS' => 'layout3-{SIZE}',
				'PROTECT' => array('owner','superadmin','admin','staff')
				),
			'content' => array(
				'TITLE' => 'Content',
				'LINK'  => 'index.php?sc=content',
				'ICON_CLASS' => 'rows4-{SIZE}',
				'PROTECT' => array('owner','superadmin','admin','staff','editor')
				), 
			'docs' => array(
				'TITLE' => 'Files',
				'LINK'  => 'index.php?sc=docs',
				'ICON_CLASS' => 'folder-{SIZE}',
				'PROTECT' => array('owner','superadmin','admin','staff','editor')
				),
			'media' => array(
				'TITLE' => 'Media',
				'LINK'  => 'index.php?sc=media',
				'ICON_CLASS' => 'image-{SIZE}',
				'PROTECT' => array('owner','superadmin','admin','staff')
				),
			'config' => array(
				'TITLE' => 'Configuration',
				'LINK'  => 'index.php?sc=config',
				'ICON_CLASS' => 'controls-{SIZE}',
				'PROTECT' => array('owner','superadmin','admin')
				),
			'redirect' => array(
				'TITLE' => 'Page Redirects',
				'LINK'  => 'index.php?sc=redirect',
				'ICON_CLASS' => 'next-{SIZE}',
				'PROTECT' => array('owner','superadmin','admin')
				)
			/*
'help' => array(
				'TITLE' => 'Documentation',
				'LINK'  => 'javascript:void(0)',
				'ICON_CLASS' => 'note-{SIZE}',
				'BUTTON' => '<span class="button-icon"><span class="plus-10 plix-10"></span></span>',
				'NESTED-UL' => '{{MENU_DOCS_UL}}',
				'PROTECT' => '1,2,3,4'
				)
*/
		);
		
		if ((!is_null($item)) && (isset($menu_array[$sc_code][$item])))
		{
			return $menu_array[$sc_code][$item];
		}
		else
		{
			return $menu_array;
		}
	}
	
	public function getSectionFile($options)
	{
		$html = '';
		
		$file = (isset($options['KEY'])) ? $options['KEY'] : null ;
		
		if (!is_null($file))
		{
			$file = strtolower($file) . '.php';
			$path = $this->options['SECTION_PATH'] . $file;
			$device = $this->getDevice(); // Make devices available to all sections
			
			if (file_exists($path))
			{
				ob_start();
				@include $path;
				$html = ob_get_contents();
				ob_end_clean();
			}	
		}
		
		return $html;
	}
	
	private function loadPageBasic()
	{
		$html = null;
		$path = $this->options['PAGE_PATH'];
		
		if (file_exists($path))
		{
			ob_start();
			@include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		return $this->loadPageBasic();
	}
}
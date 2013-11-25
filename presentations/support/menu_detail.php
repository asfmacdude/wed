<?php
/*
 * @version		$Id: menu_detail.php 1.0 2009-03-03 $
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
 * menu_detail.php
 * 
 * This is the detail object for presentations that displays layered menus
 *
 * All tabbed interfaces have two basic elements
 * - Tab Headings (labels on the tabs)
 * - Tab Content (content that is revealed when a tab is clicked)
 *
 * Content will mainly be extraced from the content_main table; however, in future
 * devs, there may be other ways of getting content
 * 
 * The TAB_STYLE file that will be included will contain an array of 'WRAPPERS' to be used
 * around the two elements of the tabbed interface. The following are required:
 * - MAIN_OUTER_WRAP some tabbed interfaces will have an outer wrap, leave blank if not the case
 * - TAB_WRAP this is the wrap that goes around both the tab headers and the content.
 * - TAB_HEADERS_WRAP this wraps the tab headers, most of the time it will be a <ul> with a class
 * - TAB_HEAD_WRAP this wraps each individual tab head, usually a <li> with class or classes
 * - TAB_HEAD_ICON optional if the style allows for icons as part of the head
 * - TAB_CONTENT_WRAP this is the main wrap around the entire content pane, usually a <div>
 * - TAB_CONTENT_PANE_WRAP this is the wrap around eac individual content pane
 *
 *
 * CONTENT
 * The content array will be similar across all presentation and look something like this:
 * - 'header'  => array( 'CONTENT_CODE'=>content_code,'ICON_CLASS'=> icon_code )
 * This will allow for as many options as you want for each section of the presentaion
 */

class menu_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']        = __CLASS__;
		$this->options['LOCAL_PATH']        = dirname(__FILE__);
		$this->options['ID']                = 'menu1'; // this is assigned by Presentations
		$this->options['NAME']              = null;
		$this->options['MENU_FORMATS']      = array(); // DS . 'menus' . DS;
		$this->options['DETAILS']           = null; // Allows for an alternate set of menu details
		$this->addOptions($options);
	}
	
	// *******************************************************************
    // ********  getMenuInfo by Name *************************************
    // *******************************************************************
    public function getMenuInfo($name)
	{
		global $walt;
		$db = $walt->getImagineer('communicore');
		$menu_db = $db->loadDBObject('theme_menus',__CLASS__);
		
		$details = null;
		
		if ( (!is_null($name)) && ($menu_db->loadMenuName($name)) )
		{	
			$this->options['MENU_FORMATS'] = ($menu_db->getValue('top')==='Y') ? $menu_db->getFormats() : $this->options['MENU_FORMATS'];		
			$details = (!is_null($this->options['DETAILS'])) ? $this->options['DETAILS'] : $menu_db->getDetails();
		}
			
		return $details;
	}
	
	// *******************************************************************
    // ********  buildMenu starting from the TOP *************************
    // *******************************************************************
	public function buildMenu()
	{
		$html = '';
		
		// First we acquire the info about the 'TOP' menu by searching
		// the theme_menus table for the given code. The default is to send back the 
		// details field(JSON) which will be interpreted into an array. If we do indeed get an array
		// then we turn it over to renderMenu to do their task.
		$top_menu = $this->getMenuInfo($this->options['NAME']);
		
		if (is_array($top_menu))
		{
			$html = $this->renderMenu($top_menu);
		}
		
		$html =sprintf($this->options['MENU_FORMATS']['UL_WRAP_TOP'], $html);
		
		return $html;
	}
	
	// *******************************************************************
    // ********  renderMenu **********************************************
    // *******************************************************************
	public function renderMenu($array,$level=1)
	{
		$html = '';
		
		foreach ($array as $key=>$value)
		{
			// First blank these out time you come through the loop
			$item_html = '';
			$drop_html = '';
			
			// Now set the values/defaults
			$title      = (isset($value['TITLE']))      ? $value['TITLE']      : 'New Item';
			$sub_title  = (isset($value['SUB_TITLE']))  ? $value['SUB_TITLE']  : 'Nice Sub Title';
			$class      = (isset($value['CLASS']))      ? $value['CLASS']      : null;
			$code       = (isset($value['CODE']))       ? $value['CODE']       : null;
			$link       = (isset($value['LINK']))       ? $value['LINK']       : '#';
			$drop       = (isset($value['DROPDOWN']))   ? $value['DROPDOWN']   : null;
			$search     = (isset($value['SEARCH']))     ? $value['SEARCH']     : null;
			$start_date = (isset($value['START_DATE'])) ? $value['START_DATE'] : false;
			$end_date   = (isset($value['END_DATE']))   ? $value['END_DATE']   : false;
			
			if (!wed_getMomentInTime($value))
			{
				continue;
			}
			
			// Start placing the values into the format string
			$li_level  = 'LEVEL-'.$level;
			$item_html = $this->options['MENU_FORMATS']['LI_FORMAT'][$li_level];
			$item_html = str_replace('{{TITLE}}', $title, $item_html);
			$item_html = str_replace('{{SUB_TITLE}}', $sub_title, $item_html);
			$item_html = str_replace('{{CLASS}}', wed_formatClass($class), $item_html);
			
			if (!is_null($search))
			{
				$link = '/tag/'.$search;
			}
			
			$link_opts['CODE'] = $code;
			$link_opts['LINK'] = $link;
			$item_html = str_replace('{{LINK}}', wed_formatLink($link_opts), $item_html);
			
			// Now check if there is a dropdown menu
			if (!is_null($drop))
			{
				$drop_menu = $this->getSpecialMenu($drop);
				
				if (is_array($drop_menu))
				{
					$next = $level + 1;
					$drop_html = $this->renderMenu($drop_menu,$next);
					$drop_html = sprintf($this->options['MENU_FORMATS']['UL_WRAP_DROP'],$drop_html);
					$item_html = str_replace('{{DROPDOWN}}', $drop_html, $item_html);
				}
			}
			
			$html .= $item_html;
		}
		
		return $html;
	}
	
	public function getSpecialMenu($name)
	{
		$menu = null;
		
		if (substr($name, 0, 6)==='Group:')
		{
			$x = explode(':', $name); // Example Group:sport
			$group = (isset($x[1])) ? $x[1] : null;
			return $this->getGroupMenu($group);
		}
		else
		{
			$method = 'getMenu'.$name;
		}

		// First, check to see if a method exists in this class
		// Then, look for a function that may exists
		// Finally, look in the theme_menus table for answers
		if (method_exists($this,$method))
		{
			$menu = call_user_func(array($this,$method));
		}
		elseif (function_exists($method))
		{
			$menu = call_user_func($method);
		}
		else
		{
			$menu = $this->getMenuInfo($name);
		}
		
		return $menu;
	}
	
	private function getGroupMenu($group)
	{
		if (is_null($group))
		{
			return null;
		}
		
		$menu       = null;		
		$control_db = wed_getDBObject('content_control');
		$id         = $control_db->getControlID($group);
		
		if ($id)
		{
			$connect_db = wed_getDBObject('content_connect');
			
			if ($connect_db->searchControl($id))
			{
				$menu = array();
				$rec  = 0;

				while ($connect_db->moveRecordList($rec))
				{
					$title        = $connect_db->getFormattedValue('MENU_TITLE');
					$link         = $connect_db->getFormattedValue('LINK');	
					$menu[$title] = array('TITLE' => $title, 'LINK' => $link);
					$rec++;
				}
			}
		}
		
		return $menu;
	}
	
	public function setHTML($options=array())
	{
		return $this->buildMenu();
	}
}
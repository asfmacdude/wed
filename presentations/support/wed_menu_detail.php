<?php
/*
 * @version		$Id: wed_menu_detail.php 1.0 2009-03-03 $
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
 * wed_menu_detail.php
 * 
 * 2/19/2014
 * This menu program was developed in an attempt to completely redo
 * the menu app from before. This uses the data tables wed_menus, wed_menus_connect and
 * wed_menus_base. Each menu item is stored in wed_menus_base and has no connection to
 * anything else. It's basically a title and a link with other optional info as needed.
 * wed_menus is the top level table with simply one record for each menu on a site, 
 * although it is NOT connected to any sites whatsoever so it can be called from anywhere.
 * wed_menus_connect 'connects' the two tables to build the menu.
 *
 * NOTE: These tables have absolutely NO html formatting associated with them. The formats
 * are going to be sent over from the THEME each time. This is a totally new concept I am
 * trying. Before the formats was stored in a table along with the menu. 
 */

class wed_menu_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']   = __CLASS__;
		$this->options['LOCAL_PATH']   = dirname(__FILE__);
		$this->options['ID']           = 'wedmenu1'; // this is assigned by Presentations
		$this->options['NAME']         = null; // name of the menu, example: sidebar_left, top_menu, etc.
		$this->options['LIST']         = array();
		$this->options['FORMATS']      = array();
		$this->options['START_CLASS']  = null;
		$this->options['ACTIVE_CLASS'] = null;
		$this->addOptions($options);
		$this->options['FORMATS']      = $this->getFormats();
		$this->options['ACTIVE_CLASS'] = (isset($this->options['FORMATS']['ACTIVE_CLASS'])) ? $this->options['FORMATS']['ACTIVE_CLASS'] : null;
		$this->options['START_CLASS']  = (isset($this->options['FORMATS']['START_CLASS'])) ? $this->options['FORMATS']['START_CLASS'] : null;
	}
	
	private function getFormats()
	{
		$formats = array();
		$assets  = wed_getAssets();
		
		if (!is_null($assets))
		{
			$formats = $assets->getFormats($this->options['NAME']);
		}
		
		return $formats;
	}
	
	public function buildMenu()
	{
		$menu_array = $this->options['LIST'];
		$start_item = true;
		$menu_html  = '';
		
		foreach ($menu_array as $key=>$data)
		{
			$li_array = array();
			
			$li_array['%LI_CLASS%']   = $this->setLiClass($data,$start_item);
			$li_array['%TITLE%']      = ((isset($data['TITLE'])) && (!empty($data['TITLE']))) ? $data['TITLE'] : 'Title Unavailable';
			$link_query               = (strval($data['PARENT_ID'])>0) ? '?aid='.$data['PARENT_ID'] : '?aid='.$data['ID'];
			$li_array['%LINK%']       = ((isset($data['LINK'])) && (!empty($data['LINK']))) ? $data['LINK'] . $link_query : '#';
			$li_array['%ICON_CLASS%'] = (isset($data['DETAILS']['ICON_CLASS'])) ? $data['DETAILS']['ICON_CLASS'] : null;
			
			if (is_null($data['SUB_MENU']))
			{
				$menu_html .= str_replace(array_keys($li_array), array_values($li_array), $this->options['FORMATS']['TOP_NOSUB']);
			}
			else
			{
				$li_array['%SUB_MENU%'] = $this->buildSubMenu($data['SUB_MENU']);
				$menu_html .= str_replace(array_keys($li_array), array_values($li_array), $this->options['FORMATS']['TOP_SUB']);
			}
			
		
			$start_item = false;
		}
		
		return $menu_html;
	}
	
	public function buildSubMenu($list)
	{
		$sub_html = null;
		$format = '<li><a href="%LINK%">%TITLE%</a></li>';
		
		foreach ($list as $key=>$data)
		{
			$li_array = array();
			
			$li_array['%LI_CLASS%']   = $this->setLiClass($data);
			$li_array['%TITLE%']      = ((isset($data['TITLE'])) && (!empty($data['TITLE']))) ? $data['TITLE'] : 'Title Unavailable';
			$link_query               = (strval($data['PARENT_ID'])>0) ? '?aid='.$data['PARENT_ID'] : '?aid='.$data['ID'];
			$li_array['%LINK%']       = ((isset($data['LINK'])) && (!empty($data['LINK']))) ? $data['LINK'] . $link_query : '#';
			$li_array['%ICON_CLASS%'] = (isset($data['DETAILS']['ICON_CLASS'])) ? $data['DETAILS']['ICON_CLASS'] : null;
			
			$sub_html .= str_replace(array_keys($li_array), array_values($li_array), $this->options['FORMATS']['SUB']);
		}
	
		return $sub_html;
	}
	
	public function setLiClass($data=array(),$start=false)
	{
		$liclass = '';
		$liclass .= $this->setStartClass($data,$start); 
		$liclass .= $this->setActiveClass($data);
		$liclass .= (isset($data['LI_CLASS'])) ? $data['LI_CLASS'].' ' : $liclass;
		
		return $liclass;
	}
	
	public function setStartClass($data=array(),$start=false)
	{
		$class = null;
		
		if ((isset($this->options['FORMATS']['START_CLASS'])) && ($start))
		{
			$class = $this->options['FORMATS']['START_CLASS'].' ';
		}
		
		return $class;
	}
	
	public function setActiveClass($data=array())
	{
		$class = null;
		$aid   = (isset($_GET['aid'])) ? $_GET['aid'] : null;

		if ( (!is_null($aid)) && ($aid==$data['ID']) )
		{
			$class = $this->options['ACTIVE_CLASS'];
		}
		
		return $class;
	}
	
	public function setHTML($options=array())
	{
		return $this->buildMenu();
	}
}
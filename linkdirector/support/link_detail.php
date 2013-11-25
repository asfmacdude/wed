<?php
/*
 * @version		$Id: link_detail.php 1.0 2009-03-03 $
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
 * link_detail.php
 * 
 * This is the detail object for presentations that displays layered menus
 *
 */

class link_detail extends details
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
		$this->options['ID']                = 'link1'; // this is assigned by linkdirector
		$this->options['CODE']              = null;
		$this->options['LINK']              = null;
		$this->options['SITE']              = null;
		$this->options['SECURE']            = false;
		$this->options['SITE_ONLY']         = false;
		$this->options['DEFAULT_DOMAIN']    = '.net';
		$this->addOptions($options);
	}
	
	// *******************************************************************
    // ********  getCodeInfo *********************************************
    // *******************************************************************
    private function getCodeInfo()
	{
		$page = false;
		
		// Logic Description Here
		// If SITE_ONLY is true, we ONLY look for the code
		// in the same SITE as we are currently in.
		//
		// If SITE_ONLY is false, we look in all places, first in the 
		// same SITE, then look everywhere else.
	
		if ($this->options['SITE_ONLY'])
		{
			$page = wed_getPageInfo($this->options['CODE'],true);
		}
		else
		{
			$look = array(true,false);
			
			foreach ($look as $value)
			{
				$page = wed_getPageInfo($this->options['CODE'],$value);
				
				if ($page)
				{
					break;
				}
			}
		}

		if ($page)
		{
			$this->options['SITE'] = $page->getValue('site');
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// *******************************************************************
    // ********  buildLink  **********************************************
    // *******************************************************************
	public function buildLink()
	{
		$href = '/index.php';
		
		if ( (!is_null($this->options['CODE'])) && ($this->getCodeInfo()) )
		{
			$href = $this->buildLinkFromCode();	
		}
		elseif (!is_null($this->options['LINK']))
		{
			$href = $this->options['LINK'];
		}

		return $this->renderLink($href);
	}
	
	private function buildLinkFromCode()
	{
		$href = $this->options['SITE'] . $this->options['DEFAULT_DOMAIN'];
		
		if ($this->options['SECURE'])
		{
			$href = 'https://' . $href;
		}
		else
		{
			$href = 'http://' . $href;
		}
		
		return $href . '/index.php?page=' . $this->options['CODE'];
	}
	
	// *******************************************************************
    // ********  renderLink **********************************************
    // *******************************************************************
	public function renderLink($href)
	{	
		return $href;
	}
	
	public function setHTML($options=array())
	{
		return $this->buildLink();
	}
}
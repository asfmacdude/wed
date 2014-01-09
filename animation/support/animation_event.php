<?php
/*
 * @version		$Id: animation_event.php 1.0 2009-03-03 $
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
 * animation_event.php
 * 
 * This is the cell object for animation_cell
 * 
 */

class animation_event extends details
{
	public $options  = array();
	public $DetailObj = false;
	public $componentName;
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']            = __CLASS__;
		$this->options['LOCAL_PATH']            = dirname(__FILE__);
		$this->options['SLIDER_ID']             = 'layerslider'; // this can change depending on how many sliders are on a page
		$this->options['CODE']                  = null; // if a code is sent over, we load the settings in from the database
		$this->options['TYPE']                  = 'img'; // used to add a logo to each image
		$this->options['IMG_SRC']               = null;
		$this->options['CLASS']                 = null;
		$this->options['STYLE']                 = null; // version 5 of layerslider requires css style to be separate from settings
		$this->options['SETTINGS']              = null;
		$this->options['TEXT']                  = null; // used to add a logo to each image
		
		$this->addOptions($options);
	}
	
	private function formatEvent()
	{
		$this->processEventCode(); // sets SETTINGS in the case that a code is passed
		$html         = '';
		$type         = (isset($this->options['TYPE']))  ? $this->options['TYPE']  : null ;
		$src          = (isset($this->options['IMG_SRC'])) ? $this->options['IMG_SRC'] : null ;
		$class        = (isset($this->options['CLASS'])) ? $this->options['CLASS'] : null ;
		$style        = (isset($this->options['STYLE'])) ? $this->options['STYLE'] : null ;
		$settings     = (isset($this->options['SETTINGS'])) ? $this->options['SETTINGS'] : null ;
		$text         = (isset($this->options['TEXT']))  ? $this->options['TEXT']  : null ;
		$style_str    = $this->dataToString($style);
		$settings_str = $this->dataToString($settings);
			
		// Events will have a TYPE associated with it. They can be images (img tag) or different
		// level headers (h1,h2,h3,h4). Some items will have a style list, but not all of them. For example,
		// each layer begins with a background image that generally has no style listing.
		if ($type === 'img')
		{
			// process image tag here
			$html .= '<img src="'.$src.'" class="'.$class.'" style="'.$style_str.'">';
		}
		elseif ( ($type === 'h1') || ($type === 'h2') || ($type === 'h3') || ($type === 'h4') )
		{
			// process h1,h2,h3,h4 tags
			$html .= '<' .$type .' class="'.$class.'" style="'.$style_str.'" data-ls="'.$settings_str.'" >'.$text.'</'.$type.'>';
		}
		
		return $html;
	}
	
	private function dataToString($settings)
	{
		$string    = '';
		
		if ( (!is_null($settings)) && (is_array($settings)) )
		{
			foreach ($settings as $key=>$value)
			{
				$string .= $key.':'.$value.';';
			}
		}
		
		return $string;
	}
	
	private function processEventCode()
	{
		if (is_null($this->options['CODE']))
		{
			return null;
		}
		
		$settings = array();
		
		$db_event = wed_getDBObject('animation_events');
		
		if ($db_event->loadEventCode($this->options['CODE']))
		{
			$settings = $db_event->getDetails();
			$this->addOptions($settings);
		}
	}
	
	public function setHTML($options=null)
	{
		return $this->formatEvent();
	}
}
?>
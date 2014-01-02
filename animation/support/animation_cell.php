<?php
/*
 * @version		$Id: animation_cell.php 1.0 2009-03-03 $
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
 * animation_cell.php
 * 
 * This is the cell object for animation_detail
 * 
 */

class animation_cell extends details
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
		$this->options['LAYER_CLASS']           = 'ls-layer'; // class for each layer of the animation
		$this->options['LAYER_BG_CLASS']        = 'ls-bg'; // Each layer has one background image with this class
		$this->options['IMAGE_BG']              = null; // this is the main image that serves as the background of the cell
		$this->options['LAYER_STYLE']           = array('transition3d: all;','transition2d: all;'); // base style which can be replaced
		$this->options['EVENTS']                = null; // Futured Development of creative events
		$this->options['CODES']                 = null; // Futured Development of creative events
		$this->options['SETTINGS']              = null; // Futured Development of creative events
		
		$this->addOptions($options);
	}
	
	private function formatBaseImage()
	{
		return (!is_null($this->IMAGE_BG)) ? '<img src="'.$this->IMAGE_BG.'" class="'.$this->LAYER_BG_CLASS.'" />'.LINE1 : null ;
	}
	
	private function formatEvents()
	{
		$html = '';
		
		if (is_array($this->EVENTS))
		{
			foreach ($this->EVENTS as $event=>$options)
			{
				$options['SLIDER_ID'] = $this->SLIDER_ID;
				$event_object         = new animation_event($options);
				$html .= $event_object->getHTML();
			}
		}
		
		return $html;
	}
	
	private function formatCodes()
	{
		$html = '';
		
		if ((!is_null($this->CODES)) && (is_array($this->CODES)))
		{
			foreach ($this->CODES as $code)
			{
				$options['SLIDER_ID'] = $this->SLIDER_ID;
				$options['CODE']      = $code;
				$event_object         = new animation_event($options);
				$html .= $event_object->getHTML();
			}
		}
		
		return $html;
	}
	
	private function formatLink()
	{
		$html = '';
		
		if (isset($this->options['LINK']))
		{
			$html = '<a href="'.$this->options['LINK'].'" class="ls-link"></a>';
		}
		
		return $html;
	}
	
	private function wrapLayer($content)
	{
		$html  = '';
		$style = '';
		
		if ( (!is_null($this->options['SETTINGS'])) && ($this->options['SETTINGS']) )
		{
			foreach ($this->options['SETTINGS'] as $key=>$value)
			{
				$style .= $key.':'.$value.';';
			}
		}
		elseif (is_array($this->options['LAYER_STYLE']))
		{
			$style = implode('', $this->options['LAYER_STYLE']);
		}
		
		$html .= '<div class="'.$this->LAYER_CLASS.'" style="'.$style.'">'.LINE1;
		$html .= $content;
		$html .= $this->formatLink();
		$html .= '</div>'.LINE1;
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		$html = null;
		
		$html .= $this->formatBaseImage(); // Start with base image
		
		if (!is_null($this->options['EVENTS']))
		{
			$html .= $this->formatEvents();
		}
		elseif (!is_null($this->options['CODES']))
		{
			$html .= $this->formatCodes();
		}
		
		// Finish with a Wrapper
		if (!is_null($html))
		{
			$html = $this->wrapLayer($html);
		}
		
		return $html;
	}
}
?>				
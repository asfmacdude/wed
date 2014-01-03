<?php
/*
 * @version		$Id: animation.php 1.0 2009-03-03 $
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
 * animation.php
 * 
 * Animation uses the great slider, LayerSlider to produce
 * some awesome animations. This class handles the formation of the actual
 * html to build the animation.
 *
 * In order for the html to work, remember to load the proper javascript files and
 * css files associated with LayerSlider.
 *
 * Also, each theme must decide whether the slider is to be put in a container or wrapper
 * based on it's placement on the page. Animation outputs the basic html for the slider.
 * 
 * Animation works by creating 
 */

class animation extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new animation();
        }

        return $instance;
    }
	
	private function __construct()
	{
	
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
		$this->options['CARTOONS']           = array();
	}
	
	public function newAnimation($options=array())
	{
		if (isset($options['SLIDER_ID']))
		{
			$object = new animation_detail($options);
			$id     = $options['SLIDER_ID'];
			
			if (!isset($this->options['CARTOONS'][$id]))
			{
				$this->options['CARTOONS'][$id] = $object;
			}
		}
	}
	
	public function newAnimationByCode($options=array())
	{
		// Note: this function serves to accept animations that use
		// Cell Codes instead of pre-processing the cells before submittal.
		// This will probably be the wave of the future.
		
		if ( (isset($options['SLIDER_ID'])) && (isset($options['CELLS'])) )
		{
			$options['CELLS'] = $this->processCellCodes($options['CELLS']);
			
			$object = new animation_detail($options);
			$id     = $options['SLIDER_ID'];
			
			if (!isset($this->options['CARTOONS'][$id]))
			{
				$this->options['CARTOONS'][$id] = $object;
			}
		}
	}
	
	public function addAnimationCell($id,$options=array())
	{
		if (!isset($this->options['CARTOONS'][$id]))
		{
			// We are calling a method in the animation_detail object
			$this->options['CARTOONS'][$id]->addCell($options);
		}
	}
	
	private function processCellCodes($codes)
	{
		// Here we look up each code and load it's array that contains
		// all of it's events.
		$cells = array();
		
		global $walt;
		$db = $walt->getImagineer('communicore');
		$db_cell = $db->loadDBObject('animation_cells');
		
		if (is_array($codes))
		{
			foreach ($codes as $value)
			{
				If ($db_cell->loadCellCode($value))
				{
					$cells[] = $db_cell->getDetails();
				}
			}
		}
		
		return $cells;
	}
	
	public function setHTML($options=null)
	{
		if ( (isset($options['SLIDER_ID'])) && (isset($this->options['CARTOONS'][$options['SLIDER_ID']])) )
		{
			// We are calling a method in the animation_detail object
			return $this->options['CARTOONS'][$options['SLIDER_ID']]->getHTML();
		} 
	}
}
?>
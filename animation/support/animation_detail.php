<?php
/*
 * @version		$Id: animation_detail.php 1.0 2009-03-03 $
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
 * animation_detail.php
 * 
 * This is the detail object for animation
 * 
 */

class animation_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']            = __CLASS__;
		$this->options['LOCAL_PATH']            = dirname(__FILE__);
		$this->options['SLIDER_ID']             = 'layerslider'; // this can change depending on how many sliders are on a page
		$this->options['SLIDER_STYLE']          = 'width: 100%; height: %height%px; margin: 0px auto;'; // Main Slider style
		$this->options['SLIDER_HEIGHT']         = '400'; // default height of slider
		$this->options['ANIMATION_TYPE']        = 'GRAB_FOLDER'; // Default is GRAB_FOLDER which will load a folder of images or CREATIVE (To be developed)
		$this->options['MAIN_WRAPPER_CLASS']    = 'layerslider-container-fw'; // this should not change
		$this->options['IMAGE_FOLDER']          = null; // folder will usually be the group sysname like archery, basketball, openceremony, homepage, etc.
		$this->options['IMAGE_SIZE']            = null; // example width_height like 900_400 means 900 pixels by 400 pixels
		$this->options['CELLS']                 = array();
		$this->options['CELL_OBJECTS']          = array();
		$this->options['CELL_TEMPLATE']         = null; // code to pull in a template from the animation_cells table
		
		$this->addOptions($options);
	}
	
	public function addCell($options)
	{
		$options['SLIDER_ID']            = $this->SLIDER_ID;
		$this->options['CELL_OBJECTS'][] = new animation_cell($options);
	}
	
	/*
	 * addCellsFromList
	 *
	 * This function adds cells to the animation by working with a list of images
	 * from a folder. We can use a template cell to add to each image such as adding
	 * a fading logo to each slide. Otherwise, it will just be an image only.
	 */
	private function addCellsFromList($list)
	{
		if (is_array($list))
		{
			$template_cell = $this->getTemplateCell();
			
			foreach ($list as $item)
			{
				$template_cell['IMAGE_BG'] = $item;
				$this->addCell($template_cell);
			}
		}
	}
	
	private function addCellsFromCells()
	{
		if (is_array($this->options['CELLS']))
		{
			foreach ($this->options['CELLS'] as $cell=>$data)
			{
				$this->addCell($data);
			}
		}

	}
	
	/*
	 * getTemplateCell
	 *
	 * This check the CELL_TEMPLATE option and if there is a specified
	 * template code, it will load that code. If not, it simply returns
	 * a blank array.
	 */
	private function getTemplateCell()
	{
		$template = array();
		
		if (!is_null($this->options['CELL_TEMPLATE']))
		{
			$cell_db = wed_getDBObject('animation_cells');
			
			if ($cell_db->loadCellCode($this->options['CELL_TEMPLATE']))
			{
				$template = $cell_db->getDetails();
			}
		}
		
		return $template;
	}
	
	private function getImagesFromFolder()
	{
		$imagelist = array();
		$web_path  = null;
		
		$options['CATEGORY'] = $this->options['IMAGE_FOLDER'];
		$options['SIZE']     = $this->options['IMAGE_SIZE'];
		$img_obj = wed_getImageObject($options);
		
		return $img_obj->getImages();
	}
	
	private function formatHTML($content)
	{
		$html  = '';
		$style = str_replace('%height%',$this->options['SLIDER_HEIGHT'] , $this->options['SLIDER_STYLE']);
		
		// Final wrapper for the entire slider
		$html .= '<div id="'.$this->MAIN_WRAPPER_CLASS.'">'.LINE1;
		$html .= '<div id="'.$this->SLIDER_ID.'" style="'.$style.'">'.LINE1;
		$html .= $content;
		$html .= '</div>'.LINE1;
		$html .= '</div>'.LINE1;
		
		return $html;
	}
	
	public function setHTML($options=null)
	{
		$html = null;
		
		if ($this->ANIMATION_TYPE === 'GRAB_FOLDER')
		{
			$this->addCellsFromList($this->getImagesFromFolder());
		}
		elseif ($this->ANIMATION_TYPE === 'CREATIVE')
		{
			// The future has arrived!!
			$this->addCellsFromCells();
		}
		
		foreach ($this->options['CELL_OBJECTS'] as $cell_object)
		{
			$html .= $cell_object->getHTML();
		}

		return $this->formatHTML($html);
	}
}
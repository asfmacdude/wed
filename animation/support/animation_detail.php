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
		$this->options['COMPONENT']             = null;
		$this->options['STYLE']                 = null; // Used to pass styles over to the COMPONENT
		$this->options['SLIDER_ID']             = 'layerslider'; // this can change depending on how many sliders are on a page
		$this->options['ANIMATION_TYPE']        = 'GRAB_FOLDER'; // Default is GRAB_FOLDER which will load a folder of images or CREATIVE (To be developed)
		$this->options['IMAGE_FOLDER']          = null; // folder will usually be the group sysname like archery, basketball, openceremony, homepage, etc.
		$this->options['DEFAULT_IMAGE_FOLDER']  = null; // You can specify a default image folder in case the called directory is empty
		$this->options['IMAGE_SIZE']            = null; // example width_height like 900_400 means 900 pixels by 400 pixels
		$this->options['CELLS']                 = array();
		$this->options['CELL_OBJECTS']          = array();
		$this->options['CELL_TEMPLATE']         = null; // code to pull in a template from the animation_cells table
		
		// These could be loaded from a component in the future
		$this->options['SLIDER_HEIGHT']         = '400px'; // default height of slider
		$this->options['SLIDER_WIDTH']          = '100%'; // default height of slider		
		$this->options['SLIDER_STYLE']          = 'width: %WIDTH%; height: %HEIGHT%; margin: 0px auto;'; // Main Slider style
		$this->options['MAIN_OUTER_WRAP']       = '<div id="layerslider-container-fw" ><div id="%ID%" style="%STYLE%" >%CONTENT%</div></div>';
		
		$this->addOptions($options);
	}
	
	public function buildAnimation()
	{
		$html = null;
		
		// This will load a component file if that is what we are using to create this presentation
		$comp_options = array(
			'COMPONENT_ID' => $this->options['SLIDER_ID'],
			'STYLE'        => $this->options['STYLE']
			);
		$this->loadComponent($comp_options);
		
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
		
		// Don't draw empty <div> on the screen. If there is no content, don't draw anything
		if (!is_null($html))
		{
			$style = $this->options['SLIDER_STYLE'];
			$style = str_replace('%HEIGHT%',$this->options['SLIDER_HEIGHT'] , $style);
			$style = str_replace('%WIDTH%',$this->options['SLIDER_WIDTH'] , $style);
		
			$wrap  = $this->options['MAIN_OUTER_WRAP'];
			$wrap  = str_replace('%ID%', $this->options['SLIDER_ID'], $wrap);
			$wrap  = str_replace('%STYLE%', $style, $wrap);
		
			$html  = str_replace('%CONTENT%', $html, $wrap);
			
			// These only load if there is a component object, otherwise
			// nothing happens here.
			$this->loadCSSAssets();
			$this->loadJavascript();
		}
		
		return $html;
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
		$img_obj             = wed_getImageObject($options);
		$imagelist           = $img_obj->getImages();
		
		if (empty($imagelist))
		{
			$options['CATEGORY'] = $this->options['DEFAULT_IMAGE_FOLDER'];
			$options['SIZE']     = $this->options['IMAGE_SIZE'];
			$img_obj_default     = wed_getImageObject($options);
			$imagelist           = $img_obj_default->getImages();
		}
		
		return $imagelist;
	}
	
	private function loadJavascript()
	{
		if (!is_null($this->component_object))
		{
			$js_array = $this->component_object->loadJSAssets();
			
			foreach ($js_array as $asset)
			{
				wed_addNewJavascriptAsset($asset);
			}
		}
	}
	
	private function loadCSSAssets()
	{
		if (!is_null($this->component_object))
		{
			$css_array = $this->component_object->loadCSSAssets();
			
			foreach ($css_array as $asset)
			{
				wed_addCSSAsset($asset);
			}
		}
	}
	
	public function setHTML($options=null)
	{
		return $this->buildAnimation();
	}
}
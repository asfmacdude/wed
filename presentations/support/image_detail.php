<?php
/*
 * @version		$Id: image_detail.php 1.0 2009-03-03 $
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
 * image_detail.php
 * 
 * This is the detail object for presentations that renders images
 * 
 */

class image_detail extends details
{
	public $options  = array();
	public $DetailObj = false;
	public $componentName;
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
		$this->setSize();
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']      = __CLASS__;
		$this->options['LOCAL_PATH']      = dirname(__FILE__);
		$this->options['SHOW_ERROR']      = true;
		$this->options['ERROR_MSG']       = wed_getSystemValue('CONTENT_UNAVAILABLE');
		$this->options['FILE_BASE']       = FILE_BASE . 'images' . DS;
		$this->options['FILE_BASE_WEB']   = FILE_BASE_WEB . 'images' . DS;
		$this->options['PATH']            = null; // path to the file minus /files/images/
		$this->options['FILE']            = null; // actual name of the file
		$this->options['GROUP']           = null;
		$this->options['SIZE']            = null; // expressed like 100_50, 100px high, 50px wide
		$this->options['WIDTH']           = null;
		$this->options['HEIGHT']          = null;
		$this->options['RESPONSIVE']      = true; // this will cause the width to be set to 100%
		$this->options['MAKE_THUMB']      = false;
		$this->options['STYLE']           = null;
		$this->options['CLASS']           = null;
		
		if ($this->options['MAKE_THUMB']==strtoupper('YES'))
		{
			$this->options['MAKE_THUMB'] = true;
		}
		elseif ($this->options['MAKE_THUMB']==strtoupper('NO'))
		{
			$this->options['MAKE_THUMB'] = false;
		}
		
		$this->addOptions($options);
	}
	
	private function setSize()
	{
		if (!is_null($this->options['SIZE']))
	    {
		    $sz = explode('_', $this->options['SIZE']);
		    $this->options['WIDTH']  = (isset($sz[0])) ? $sz[0] : null;
		    $this->options['HEIGHT'] = (isset($sz[1])) ? $sz[1] : null;
	    }
	}
	
	public function setStyle()
	{
		if (is_null($this->options['STYLE']))
		{
			$style = null;
			
			if ($this->options['RESPONSIVE'])
			{
				$style .= 'width:100%;';
			}
			
			if (!is_null($this->options['WIDTH']))
			{
				$style .= 'max-width:'.$this->options['WIDTH'].'px;';
			}
			
			if (!is_null($this->options['HEIGHT']))
			{
				$style .= 'max-height:'.$this->options['HEIGHT'].'px;';
			}
			
			if (!is_null($style))
			{
				$style = ' style="'.$style.'"';
			}
			
			return $style;
		}
	}	
	
	public function buildPresentation()
	{
		return '<img src="' . $this->getImagePath() . '"' . $this->setStyle() .' />';
	}
	
	public function getImagePath()
    {
	    $image_path  = null;
	    $thumb_specs = array();
	    $sizes       = array();
	    
	    if (!is_null($this->options['PATH']))
	    {
		    // actual image path is specified - looking for a specific file
		    $image_path = $this->options['FILE_BASE_WEB'] . $this->options['PATH'];
		    
		    if ($this->options['MAKE_THUMB'])
		    {
			    $img_obj                  = wed_getImageObject();
			    $thumb_specs['SOURCE']    = $image_path;
				$thumb_specs['ZOOM_CROP'] = 1;
				$thumb_specs['WIDTH']     = $this->options['WIDTH'];
				$thumb_specs['HEIGHT']    = $this->options['HEIGHT'];			
				$image_path = $img_obj->getFileThumbPath($thumb_specs);
		    }   
	    }
	    else
	    {
			// grab a random image from the given directory
			$options['CATEGORY'] = $this->options['GROUP'];
			$options['SIZE']     = $this->options['SIZE'];
			$img_obj             = wed_getImageObject($options);
			$image_path          = $img_obj->getRandomFilePath();
			
			if ($this->options['MAKE_THUMB'])
		    {
			    $thumb_specs['SOURCE']    = $image_path;
				$thumb_specs['ZOOM_CROP'] = 1;
				$thumb_specs['WIDTH']     = $this->options['WIDTH'];
				$thumb_specs['HEIGHT']    = $this->options['HEIGHT'];			
				$image_path = $img_obj->getFileThumbPath($thumb_specs);
		    }
	    }
	    
		return $image_path;
    }
	
	public function setHTML($options=null)
	{
		$html  = $this->buildPresentation();
		
		if ((is_null($html)) && ($this->options['SHOW_ERROR']))
		{
			$html = $this->options['ERROR_MSG'];
		}
		
		return $html;
	}

}
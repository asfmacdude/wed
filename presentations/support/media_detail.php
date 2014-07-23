<?php
/*
 * @version		$Id: media_detail.php 1.0 2009-03-03 $
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
 * media_detail.php
 * 
 * This is the detail object for presentations that renders images
 * 
 */

class media_detail extends details
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
		$this->options['ACTUAL_CONTENT']  = null;
		$this->options['MAIN_WRAP']       = '<div class="media">%CONTENT%</div>';
		$this->options['BODY_WRAP']       = '<div class="media-body">%CONTENT%</div>';
		$this->options['HEADING_WRAP']    = '<h4 class="media-heading">%CONTENT%</h4>';
		$this->options['IMAGE_FORMAT']    = '<img class="media-object" src="%SOURCE%" >';
		$this->options['LINK_FORMAT']     = '<a class="%CLASS%" href="%LINK%" >%CONTENT%</a>';
		$this->options['IMAGE_SIDE']      = 'left';
		$this->options['SIDE_CLASS']      = array('left' => 'pull-left', 'right' => 'pull-right');
		$this->options['LIGHTBOX']        = true; 
		$this->options['LIGHTBOX_CLASS']  = 'lightbox';
		$this->options['LINK']            = null;
		$this->options['YOUTUBE']         = null;
		
		$this->addOptions($options);
	}
	
	<div class="media">
  <a class="pull-left" href="#">
    <img class="media-object" src="..." alt="...">
  </a>
  <div class="media-body">
    <h4 class="media-heading">Media heading</h4>
    ...
  </div>
</div>
	
	
	public function buildPresentation()
	{
		$html = null;	
		$html .= $this->buildLink();
		$html .= $this->buildBody();
		
		if (!is_null($html))
		{
			$html = str_replace('%CONTENT%',$html , $this->options['MAIN_WRAP']);
		}
		
		return $html;
	}
	
	
	private function buildLink()
	{
		$html        = null;
		$format      = $this->options['IMAGE_FORMAT']; // <img class="%CLASS%" src="%SOURCE%" >
		$link        = $this->options['LINK'];
		$class       = null;
		
		if (!is_null($link))
		{
			$html = $this->options['LINK_FORMAT']; // <a class="%CLASS%" href="%LINK%" >%CONTENT%</a>
			
			if (!is_null($this->options['IMAGE_SIDE']))
			{
				$class = $this->options['SIDE_CLASS'][$this->options['IMAGE_SIDE']];
			}
			
			if ($this->options['LIGHTBOX']))
			{
				$class .= ' '.$this->options['LIGHTBOX_CLASS'];
			}
			
			$html = str_replace(array('%CLASS%','%LINK%'), array($class,$link), $html);
		}
		
		
		
		return str_replace(, , );
	}
	
	public function buildBody()
	{
		
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
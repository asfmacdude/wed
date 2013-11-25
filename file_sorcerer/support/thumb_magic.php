<?php


// *****************************************************************************************
// *************************** IMAGE DETAIL CLASS ******************************************
// *****************************************************************************************
/*
 * thumb_magic
 *
 * Uses the timthumb class to create a thumbnail for an image
 *
 * src	source			url to image	Tells TimThumb which image to resize
 * w	width			the width to resize to, remove the width to scale proportionally (will then need the height)
 * h	height			the height to resize to, remove the height to scale proportionally (will then need the width)
 * q	quality			0 Ð 100	Compression quality. The higher the number the nicer the image will look. I wouldnÕt 
 *						recommend going any higher than about 95 else the image will get too large
 * a	alignment		c, t, l, r, b, tl, tr, bl, br	Crop alignment. c = center, t = top, b = bottom, r = right, l = left. 
 *						The positions can be joined to create diagonal positions
 * zc	zoom/crop		0, 1, 2, 3	Change the cropping and scaling settings
 * f	filters			Too many to mention	LetÕs you apply image filters to change the resized picture. For instance,
 *						you can change brightness/contrast or even blur the image
 * s	sharpen			Apply a sharpen filter to the image, makes scaled down images look a little crisper
 * cc	canvas colour	hexadecimal colour value (#ffffff)	Change background colour. Most used when changing the 
 *						zoom and crop settings, which in turn can add borders to the image.
 * ct	canvas transparency	true (1)	Use transparency and ignore background colour
 *
 *
 */
 
class thumb_magic extends details
{
	public $options = array();
	
	public function __construct($options)
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['SOURCE']         = null; // the actual path to the file
		$this->options['WIDTH']          = null; // width of the resized image
		$this->options['HEIGHT']         = null; // height of the resized image
		$this->options['QUALITY']        = null; // quality of the resized image 0-100
		$this->options['ALIGNMENT']      = null; // alignment crop c, t, l, r, b, tl, tr, bl, br
		$this->options['ZOOM_CROP']      = null; // zoom/crop 0, 1, 2, 3
		$this->options['FILTERS']        = null; // numerous
		$this->options['SHARPEN']        = null; // 0 or 1
		$this->options['CANVAS_COLOR']   = null; // hexadecimal colour value (#ffffff)
		$this->options['CANVAS_TRANS']   = null; // 0 or 1
		
		$this->options['TIMTHUMB_PATH']  = '/files/images/timthumb.php'; // Path to the timthumb plugin
		$this->options['QUERY']          = array(); // holds the data for the query string
		
		$this->addOptions($options);
	}
	
	private function buildQuery()
	{
		$query_list = array(
			'SOURCE'       => 'src',
			'WIDTH'        => 'w',
			'HEIGHT'       => 'h',
			'QUALITY'      => 'q',
			'ALIGNMENT'    => 'a',
			'ZOOM_CROP'    => 'zc',
			'FILTERS'      => 'f',
			'SHARPEN'      => 's',
			'CANVAS_COLOR' => 'cc',
			'CANVAS_TRANS' => 'ct'	
		);
		
		foreach ($query_list as $key=>$value)
		{
			$this->callMethod($key,$value);
		}
		
		return http_build_query($this->options['QUERY']);
	}
	
	private function callMethod($name,$url_key)
	{
		$method = 'get'.$name;
		
		if (method_exists($this, $method))
		{
			call_user_func_array(array($this,$method), array('URL_KEY' => $url_key));
		}
		else
		{
			$this->add2Query($name,$url_key);
		}
	}
	
	private function add2Query($name,$url_key)
	{
		if (!is_null($this->options[$name]))
		{		
			$this->options['QUERY'][$url_key] = $this->options[$name];
		}
	}
	
	private function getWIDTH($options)
	{
		if ( (!is_null($this->options['WIDTH'])) && (isset($options['URL_KEY'])) && ($this->options['WIDTH']>0))
		{		
			$this->options['QUERY'][$options['URL_KEY']] = $this->options['WIDTH'];
		}
	}
	
	private function getHEIGHT($options)
	{
		if ( (!is_null($this->options['HEIGHT'])) && (isset($options['URL_KEY'])) && ($this->options['HEIGHT']>0))
		{		
			$this->options['QUERY'][$options['URL_KEY']] = $this->options['HEIGHT'];
		}
	}
	
	private function getQUALITY($options)
	{
		if ( (!is_null($this->options['QUALITY'])) && (isset($options['URL_KEY'])) && ($this->options['QUALITY']>0))
		{		
			$this->options['QUERY'][$options['URL_KEY']] = $this->options['QUALITY'];
		}
	}
	
	private function getALIGNMENT($options)
	{
		$pos_values = array('c', 't', 'l', 'r', 'b', 'tl', 'tr', 'bl', 'br');
		
		if ( (!is_null($this->options['ALIGNMENT'])) && (isset($options['URL_KEY'])) && (in_array($this->options['ALIGNMENT'], $pos_values)) )
		{		
			$this->options['QUERY'][$options['URL_KEY']] = $this->options['ALIGNMENT'];
		}
	}
	
	private function getZOOM_CROP($options)
	{
		$pos_values = array(0, 1, 2, 3);
		
		if ( (!is_null($this->options['ZOOM_CROP'])) && (isset($options['URL_KEY'])) && (in_array($this->options['ZOOM_CROP'], $pos_values)) )
		{		
			$this->options['QUERY'][$options['URL_KEY']] = $this->options['ZOOM_CROP'];
		}
	}
	
	private function getSHARPEN($options)
	{
		if ( (!is_null($this->options['SHARPEN'])) && (isset($options['URL_KEY'])) && ($this->options['SHARPEN']==1))
		{		
			$this->options['QUERY'][$options['URL_KEY']] = $this->options['SHARPEN'];
		}
	}
	
	private function getCANVAS_TRANS($options)
	{
		if ( (!is_null($this->options['CANVAS_TRANS'])) && (isset($options['URL_KEY'])) && ($this->options['CANVAS_TRANS']==1))
		{		
			$this->options['QUERY'][$options['URL_KEY']] = $this->options['CANVAS_TRANS'];
		}
	}
	
	public function getThumbPath()
	{
		return $this->options['TIMTHUMB_PATH'] . '?' . $this->buildQuery();
	}
}
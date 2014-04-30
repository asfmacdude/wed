<?php
/*
 * @version		$Id: content_detail.php 1.0 2009-03-03 $
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
 * content_detail.php
 * 
 * This is the detail object for presentations that renders content
 * 
 */

class content_detail extends details
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
		$this->options['CLASS_NAME']      = __CLASS__;
		$this->options['LOCAL_PATH']      = dirname(__FILE__);
		$this->options['ERROR_CODE']      = 10;
		$this->options['SHOW_ERROR']      = true;
		$this->options['ERROR_MSG']       = wed_getSystemValue('CONTENT_UNAVAILABLE');
		$this->options['CONTENT_OBJ']     = null;
		$this->options['CODE']            = null;
		$this->options['ID']              = null;
		$this->options['ACTUAL_CONTENT']  = null;
		$this->options['FORMAT']          = null;
		$this->options['DETAIL']          = null;
		$this->options['EXPT_LENGTH']     = 150;
		$this->options['HTML_FORMATS']    = array(
			'TITLE'    => wed_getSystemValue('THEME_TITLE','<h1>%TITLE%</h1>'),
			'TITLE2'   => wed_getSystemValue('THEME_TITLE','<h2>%TITLE%</h2>'),
			'SUBTITLE' => wed_getSystemValue('THEME_SUBTITLE','<h3>%SUBTITLE%</h3>'),
			'LINK'     => wed_getSystemValue('THEME_LINK','<a href="%LINK%">%TITLE%</a>'),
			'VIDEO'    => wed_getSystemValue('THEME_VIDEO','<iframe width="%WIDTH%" height="%HEIGHT%" src="//www.youtube.com/embed/%CODE%" frameborder="0" allowfullscreen></iframe>'),
			'IMAGE'    => wed_getSystemValue('THEME_IMAGE','<img src="%IMAGE_PATH%" style="padding-bottom:20px;" />'),
			'ARTICLE'  => wed_getSystemValue('THEME_ARTICLE','%ARTICLE%'),
			'EXCERPT'  => wed_getSystemValue('THEME_EXCERPT','%EXCERPT%')
			);
		$this->options['VIDEO_SIZES']     = array(
			'SMALL'    => wed_getSystemValue('THEME_VIDEO_SMALL','560_315'),
			'MEDIUM'   => wed_getSystemValue('THEME_VIDEO_MEDIUM','640_360'),
			'LARGE'    => wed_getSystemValue('THEME_VIDEO_LARGE','853_480')
		);
		
		$this->options['IMAGE_SIZES']     = array(
			'SMALL'    => wed_getSystemValue('THEME_IMAGE_SMALL','560_315'),
			'MEDIUM'   => wed_getSystemValue('THEME_IMAGE_MEDIUM','640_360'),
			'LARGE'    => wed_getSystemValue('THEME_IMAGE_LARGE','853_480')
		);
		
		$this->addOptions($options);
		// This allows for the user to put a string of codes in the content area between the opening and closing
		// tags. It would need to be comma delimited. Example: code1,code2,code3
		$this->options['CODE_LIST'] = (!is_null($this->options['ACTUAL_CONTENT'])) ? explode(',', $this->options['ACTUAL_CONTENT']) : null;
		
		// This will capture the content code in the URL if  so desired.
		if ( ($this->options['CODE']==='URL') || ($this->options['CODE']==='URL-ID') || (substr($this->options['CODE'], 0, 4)==='URL_') )
		{
			$call_parts                  = wed_getSystemValue('CALL_PARTS');
			
			if ((isset($call_parts[1])) && (!empty($call_parts[1])))
			{
				$this->options['CODE'] = ($this->options['CODE']==='URL') ? $call_parts[1] : $this->options['CODE'];
				$this->options['ID']   = ($this->options['CODE']==='URL-ID') ? $call_parts[1] : $this->options['ID'];
				
				if (substr($this->options['CODE'], 0, 4)==='URL_')
				{
					$code_replace = $call_parts[1].'_';
					$this->options['CODE'] = str_replace('URL_', $code_replace, $this->options['CODE']);
				}
			}
			else
			{
				wed_changeSystemErrorCode('NO ARTICLE CODE');
			}
		}
	}
	
	private function buildPresentation()
	{
		$html = null;
		
		if ($this->loadContent())
		{
			$rec = 0;

			while ($this->options['CONTENT_OBJ']->moveRecordList($rec))
			{
				$html .= $this->loadFormat();
				$rec++;
			}	
		}
		
		return $html;
	}
	
	private function loadContent()
	{
		$status = true;
		
		if (is_null($this->options['CONTENT_OBJ']))
		{
			$content = wed_getDBObject('content_connect',$this->options['CLASS_NAME']);
			
			if ($content->getContent($this->options))
			{
				$this->options['CONTENT_OBJ'] = $content;
			}
			else
			{
				$status = false;
			}
		}
		
		return $status;
	}
	
	private function loadFormat()
	{
		$html   = null;
		
		$format = (is_null($this->options['FORMAT'])) ? 'page' : $this->options['FORMAT'];
		$method = 'getFormat'.strtoupper($format);  // getFormatPAGE()
		
		if (method_exists($this,$method))
		{
			$html = call_user_func(array($this,$method));
		}
		
		return $html;
	}
	
	/*
	 * getFormatPAGE()
	 *
	 * This format is for displaying the entire article as a
	 * web page. The article may have sub articles within the article, but
	 * this is the main displayed article which means the HEADER_1 will reflect
	 * the name of this article and not the sub-articles or snippets inside.
	 */
	private function getFormatPAGE()
	{
		$this->updateHeader();
		$this->updatePageTitle();
		$this->updateGroupTitle();
		$html  = str_replace('%TITLE%', $this->getTitle(), $this->options['HTML_FORMATS']['TITLE']);
		$html .= $this->getMedia('LARGE');
		$html .= str_replace('%ARTICLE%', $this->getFullArticle(), $this->options['HTML_FORMATS']['ARTICLE']);
		$html  = wed_cleanItUp($html,'SC_BRACKETS');
		return $html;
	}
	
	private function getFormatTAB()
	{
		$this->updateHeader();
		// $html  = str_replace('%TITLE%', $this->getTitle(), $this->options['HTML_FORMATS']['TITLE2']);
		$html = $this->getMedia('SMALL');
		$html .= str_replace('%ARTICLE%', $this->getFullArticle(), $this->options['HTML_FORMATS']['ARTICLE']);
		$html  = wed_cleanItUp($html,'SC_BRACKETS');
		return $html;
	}
	
	private function getFormatEXCERPT()
	{
		if ($this->options['CODE']=='weee')
		{
			$html = $this->getExcerpt();
			$html = wed_cleanItUp($html,'SC_BRACKETS');
			$html = wed_cleanItUp($html,'NO_DOUBLE_P');
		}
		else
		{
			$html = str_replace('%EXCERPT%', $this->getExcerpt(), $this->options['HTML_FORMATS']['EXCERPT']);
			$html = wed_cleanItUp($html,'SC_BRACKETS');
		}

		return $html;
	}
	
	private function getFormatSECTION()
	{
		$html  = str_replace('%SUBTITLE%', $this->getTitle(), $this->options['HTML_FORMATS']['SUBTITLE']);
		$html .= $this->getMedia('SMALL');
		$html .= str_replace('%ARTICLE%', $this->getFullArticle(), $this->options['HTML_FORMATS']['ARTICLE']);
		$html  = wed_cleanItUp($html,'SC_BRACKETS');
		return $html;
	}
	
	private function getFormatFEATURE()
	{
		// Feature format is for teasers and the title is the link to the actual content
		$html  = str_replace('%SUBTITLE%', $this->getTitle(true), $this->options['HTML_FORMATS']['SUBTITLE']);
		// $html .= $this->getMedia('SMALL');
		$html .= str_replace('%EXCERPT%', $this->getExcerpt(), $this->options['HTML_FORMATS']['EXCERPT']);
		$html .= $this->learnMoreButton();
		$html  = wed_cleanItUp($html,'SC_BRACKETS');
		return $html;
	}
	
	private function getFormatSNIPPET()
	{
		$html = $this->getFullArticle(false);
		$html = wed_cleanItUp($html,'SC_BRACKETS');
		// $html = wed_cleanItUp($html,'NO_P_TAGS');
		return $html;
	}
	
	private function getFormatTITLE()
	{
		return $this->getTitle();
	}
	
	private function getFormatDETAIL()
	{
		return (!is_null($this->options['DETAIL'])) ? $this->getDetail($this->options['DETAIL']) : null ;
	}
	
	private function updateHeader()
	{
		wed_addSystemValue('HEADER_1',$this->getTitle());
	}
	
	private function updatePageTitle()
	{
		$page_title = wed_getSystemValue('PAGE_TITLE');
		$page_title = $page_title . '-' .$this->getTitle();
		wed_addSystemValue('PAGE_TITLE',$page_title);
	}
	
	private function updateGroupTitle()
	{
		$group_title = $this->getGroupTitle();
		wed_addSystemValue('GROUP_TITLE',$group_title);
	}
	
	
	private function getMedia($size='LARGE')
	{
		$html           = null;
		$video_code     = $this->getDetail('VIDEO_CODE');
		$image_path     = $this->getDetail('IMAGE_PATH');
		$image_category = $this->getDetail('CATEGORY');
		
		if (!is_null($video_code))
		{
			$search    = array('%WIDTH%','%HEIGHT%','%CODE%');
			$sizes     = explode('_', $this->options['VIDEO_SIZES'][$size]);
			$replace[] = (isset($sizes[0])) ? $sizes[0] : '640'; // width
			$replace[] = (isset($sizes[1])) ? $sizes[1] : '360'; // height
			$replace[] = $video_code; // video code
			return str_replace($search,$replace,$this->options['HTML_FORMATS']['VIDEO']);
		}
		elseif ( (!is_null($image_path)) || (!is_null($image_category)) )
		{
			$search    = array('%IMAGE_PATH%');
			$size_code = $this->options['IMAGE_SIZES'][$size];
			$replace[] = $this->getImagePath($size_code);
			return str_replace($search,$replace,$this->options['HTML_FORMATS']['IMAGE']);
		}

		return $html;
	}
	
	
	private function getTitle($return_as_link=false)
	{
		$html = 'No Title Available';
		
		if ($this->loadContent())
		{
			$html = $this->options['CONTENT_OBJ']->getFormattedValue('TITLE');
			
			if ($return_as_link)
			{
				$html = $this->getLink($html);
			}
		}
		
		return $html;
	}
	
	private function learnMoreButton($title='Learn more')
	{
		$html = '';
		
		if ($this->loadContent())
		{
			$link = $this->options['CONTENT_OBJ']->getFormattedValue('LINK');
			$html = '<p><a class="btn btn-default" href="'.$link.'">'.$title.'</a></p>';
		}
		
		return $html;
	}
	
	private function getGroupTitle($show_not_available=true)
	{
		$html = null;
		
		if ($this->loadContent())
		{
			$html = $this->options['CONTENT_OBJ']->getFormattedValue('GROUPTITLE');
		}
		
		return $html;
	}
	
	private function getFullArticle($show_not_available=true)
	{
		$html = ($show_not_available) ? 'Article not available.' : null;
		
		if ($this->loadContent())
		{
			$html = $this->options['CONTENT_OBJ']->getFormattedValue('FULLARTICLE');
		}
		
		return $html;
	}
	
	private function getExcerpt($show_not_available=true)
	{
		$html = ($show_not_available) ? 'Excerpt not available.' : null;
		
		if ($this->loadContent())
		{
			$html = $this->options['CONTENT_OBJ']->getFormattedValue('EXCERPT');
		}
		
		return $html;
	}
	
	private function getLink($title='Click here')
	{
		$html = '#';
		
		if ($this->loadContent())
		{
			$link = $this->options['CONTENT_OBJ']->getFormattedValue('LINK');
			$html = str_replace('%LINK%',$link,$this->options['HTML_FORMATS']['LINK']);
			$html = str_replace('%TITLE%',$title,$html);
		}
		
		return $html;
	}
	
	private function getImagePath($size=null)
	{
		$html = null;
		
		if ($this->loadContent())
		{
			$formats = (is_null($size)) ? array() : array('IMAGE_SIZE' => $size);
			$html    = $this->options['CONTENT_OBJ']->getFormattedValue('IMAGE_PATH',$formats);
		}
		
		return $html; 
	}
	
	private function getDetail($name,$default=null)
	{
		return $this->options['CONTENT_OBJ']->getDetail($name,$default);
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
<?php

/*
 * @version		$Id: screenwriter.php 1.0 2009-03-03 $
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
 * screenwriter.php
 * 
 * ScreenWriter only handles content from content_main
 * 
 */

class screenwriter extends imagineer
{
	public $options  = array();
	public $db;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new screenwriter();
        }

        return $instance;
    }
	
	private function __construct()
	{
		
	}
	
	public function init()
	{
		global $walt;
		$this->db = $walt->getImagineer('communicore');
		$this->setOptions();
		$this->loadSupportFiles();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']  = __CLASS__;
		$this->options['LOCAL_PATH']  = dirname(__FILE__);
		$this->options['ARTICLES']    = array();
	}
	
	public function addArticleObject($object,$code)
	{
		if ( ($object instanceof article_detail) && (!isset($this->options['ARTICLES'][$code])) )
		{
			$this->options['ARTICLES'][$code] = $object;
		}
	}
	
	public function newArticle($code=null,$isID=false)
	{
		if ( (!is_null($code)) && (!isset($this->ARTICLES[$code])) )
		{
			$art_obj = new article_detail();
			
			If ($isID)
			{
				$art_obj->ID = $code;
			}
			else
			{
				$art_obj->CODE = $code;
			}
			
			$this->addArticleObject($art_obj,$code); 
		}
	}
	
	public function getDetail($code,$detail=null,$default=null)
	{
		if (isset($this->options['ARTICLES'][$code]))
		{
			return $this->options['ARTICLES'][$code]->getDetail($detail,$default);
		}
		else
		{
			return $default;
		}
	}

	public function getStatus($code,$default=null)
	{
		if (isset($this->options['ARTICLES'][$code]))
		{
			return $this->options['ARTICLES'][$code]->getStatus();
		}
		else
		{
			return $default;
		}
	}
	
	public function getTitle($code,$default=null)
	{	
		if (isset($this->options['ARTICLES'][$code]))
		{
			return $this->options['ARTICLES'][$code]->getTitle();
		}
		else
		{
			return $default;
		}
	}
	
	public function getKeywords($code,$default=null)
	{
		if (isset($this->options['ARTICLES'][$code]))
		{
			return $this->options['ARTICLES'][$code]->getKeywords();
		}
		else
		{
			return $default;
		}
	}
	
	public function getExcerpt($code,$default=null)
	{
		if (isset($this->options['ARTICLES'][$code]))
		{
			return $this->options['ARTICLES'][$code]->getExcerpt();
		}
		else
		{
			return $default;
		}
	}
	
	public function getFullArticle($code,$default=null)
	{
		if (isset($this->options['ARTICLES'][$code]))
		{
			return $this->options['ARTICLES'][$code]->getFullArticle();
		}
		else
		{
			return $default;
		}
	}
	
	public function getArticleObject($code)
	{
		if (isset($this->options['ARTICLES'][$code]))
		{
			return $this->options['ARTICLES'][$code];
		}
	}
}
?>
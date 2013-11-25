<?php
/*
 * @version		$Id: file_sorcerer.php 1.0 2009-03-03 $
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
 * file_sorcerer.php
 * 
 * file_sorcerer is my clean, easy going, go to guy for providing images and files. The
 * sole purpose of file_sorcerer is to make as easy as possible for the end user to select
 * images for there content and downloadable files for their pages and to not have to remember
 * long complicated path structures. Basically, we feed file_sorcerer a little bit about the
 * file or image and he returns the full scoop complete with the proper path to the file or
 * image to place in the html.
 *
 * file_sorcerer is placed as head guy over a particular directory or directories. He 'keeps' with
 * everything in those directories so that when an image or file is needed, he knows where it is
 * located.
 *
 * When there is a request for an image, file, or any number of both, he creates a detail object
 * from the type of file or image. image_detail or document_detail.
 * 
 */

class file_sorcerer extends imagineer
{
	public $options          = array();
	public $themeObj         = false;
	public $detailList       = array();
	public $detailObjects    = array();
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new file_sorcerer();
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
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['FILE_OBJECTS']   = array();
		$this->options['ALLOWED_TYPES']  = array('image','document','directory','curl');
		$this->options['CURRENT_ID']     = 0;
	}
	
	public function newFileMagic($options=array())
	{
		/*
		 * newFileMagic logic
		 *
		 * This is generally called only from wed_tools:
		 * wed_getImageObject - gets an image object
		 * wed_getDocumentObject - gets a document object
		 *
		 * The logic here is that the request sends over the directory (images or documents), the next level
		 * (archery, openceremony,triathlon, etc) and the size (image: 1200_500) or type (pdf,txt,etc)
		 * and we load that directory into an object and return the object for listing or possibly looking
		 * for a particular file.
		 *
		 */
		
		$directory_object = false;
		
		if ( (isset($options['FILE_TYPE'])) && (in_array($options['FILE_TYPE'], $this->ALLOWED_TYPES)) )
		{
			$obj_class          = $options['FILE_TYPE'] . '_magic';
			$id                 = $this->getNextID($options['FILE_TYPE']);
			$options['ID']      = $id; // make sure the detail object has the assigned id
			
			if (class_exists($obj_class))
			{
				$directory_object    = new $obj_class($options);
				
				if (!isset($this->options['FILE_OBJECTS'][$id]))
				{
					$this->options['FILE_OBJECTS'][$id] = $directory_object;
				}
			}
		}
		
		return $directory_object;
	}
	
	private function getNextID($type=null)
	{
		$this->options['CURRENT_ID']++;
		return $type . $this->options['CURRENT_ID'];
	}
}
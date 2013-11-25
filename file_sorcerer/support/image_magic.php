<?php


// *****************************************************************************************
// *************************** IMAGE DETAIL CLASS ******************************************
// *****************************************************************************************
/*
 * image_magic
 *
 * Small workhorse class that helps get every image file loaded
 *
 *
 */
include_once('sorcerer_apprentice.php');
include_once('thumb_magic.php');
 
class image_magic extends sorcerer_apprentice
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
		$this->options['FILE_BASE']      = FILE_BASE . 'images' . DS;
		$this->options['FILE_BASE_WEB']  = FILE_BASE_WEB . 'images' . DS;
		$this->options['CATEGORY_LIST']  = false;
		$this->options['SIZE_LIST']      = false;
		// These generally come from call
		$this->options['CATEGORY']       = 'gnrl';
		$this->options['SIZE']           = null;
		
		$this->options['CURRENT_PATH']   = null;
		$this->options['CURRENT_FILE']   = null;
		$this->options['GENERAL_CATEGORY']  = 'gnrl';
		$this->options['DEFAULT_FILE']   = 'default.jpg';
		$this->options['NAME']           = null;
		
		$this->addOptions($options);
	}
	
	public function loadDirectory()
	{
		$status = false;
		// Make sure specified path is good, if not use default
		$working_path = $this->getWorkingDirectory();
		
		// Make sure specified size is available, if not return null
		// FUTURE UPDATE: have several sizes to check from
		$working_path = $this->getWorkingSizeDirectory($working_path);
		
		if (!is_null($working_path))
		{
			// At this point the CURRENT_PATH does not include it's parent path
			// because we need to be able to choose (see getCurrentFilePath) which
			// file path we want.
			$this->options['CURRENT_PATH'] = $working_path;
			$status = true;
		}
		
		return $status;
	}
	
	private function getWorkingDirectory()
	{
		// For file_exists purposes, we need to use the system file path. That's why
		// we keep it separate from the specified path so that we can add the file web path
		// later to display the images
		$file_base     = $this->options['FILE_BASE'];
		
		$category_path = $this->options['CATEGORY'];
		$default_path  = $this->options['GENERAL_CATEGORY'];
		$working_path  = null;
		
		if (file_exists($file_base . $category_path))
		{
			$working_path = $category_path;
		}
		elseif (file_exists($file_base . $default_path))
		{
			$working_path = $default_path;
		}
		
		$this->options['CATEGORY_LIST'] = (!is_null($working_path)) ? $this->getDirectoryList($file_base . $working_path) : false;
		
		return $working_path;
	}
	
	private function getWorkingSizeDirectory($working_path)
	{
		$new_path  = null;
		$file_base = $this->options['FILE_BASE'];
		
		if ( (!is_null($working_path)) && (!is_null($this->options['SIZE'])) )
		{
			// Here's where you could add several default sizes to try in case
			$size_path = $working_path . DS . $this->options['SIZE'];
			
			if (file_exists($file_base . $size_path))
			{
				$this->options['SIZE_LIST'] = $this->getDirectoryList($file_base . $size_path);
				
				if ( (isset($this->options['SIZE_LIST']['FILES'])) && (count($this->options['SIZE_LIST']['FILES'])>0) )
				{
					$this->file_list = $this->options['SIZE_LIST']['FILES'];
					$new_path = $size_path;
				}
			}
		}
		
		return $new_path;
	}
	
	public function loadImageDirectory()
	{
		return $this->loadDirectory();
	}
	
	public function getCurrentFilePath($web=true)
	{
		if ($web)
		{
			return $this->options['FILE_BASE_WEB'] . $this->options['CURRENT_PATH'] . DS . $this->options['CURRENT_FILE'];
		}
		else
		{
			return $this->options['FILE_BASE'] . $this->options['CURRENT_PATH'] . DS . $this->options['CURRENT_FILE'];
		}		
	}
	
	/*
	 * getImages
	 *
	 * This is a public function that simply returns an array of the image paths
	 * for all the images loaded under the current settings
	 */
	public function getImages()
	{
		$imagelist = array();
		
		if ($this->loadImageDirectory()) // Loads the directory of images
		{
			$item = 0;
		
			while ($this->moveFileListPointer($item))
			{
				$imagelist[] = $this->getCurrentFilePath();
				$item++;
			}
		}
		
		return $imagelist;
	}
	
	/*
	 * getImageFilePath
	 *
	 * This is called when an object is looking for a specific file. It loads the directories
	 * and sets the pointer on the first file, then attempts to load the NAMED file by checking
	 * it against the loaded directories. If it is not available, it falls back to the DEFAULT IMAGE.
	 *
	 */
	public function getImageFilePath()
	{
		if ( ($this->loadImageDirectory()) && ($this->moveFileListPointer(0)) )
		{
			return $this->getNamedFilePath();
		}
		else
		{
			// Get Default image
			return $this->getDefaultImage();
		}
	}
	
	public function getNamedFilePath()
	{	
		if (in_array($this->options['NAME'], $this->options['SIZE_LIST']['FILES']))
		{
			return $this->options['FILE_BASE_WEB'] . $this->options['CURRENT_PATH'] . DS .$this->options['NAME'];
		}
		else
		{
			return $this->getCurrentFilePath();
		}
	}
	
	/*
	 * getFileThumbPath
	 *
	 * This function uses the timthumb plugin (see http://www.binarymoon.co.uk/projects/timthumb/)
	 * to generate a thumbnail or an altered image, scaled, cropped, etc. See the timthumb class
	 * for all the options and settings.
	 *
	 */
	public function getFileThumbPath($options=array())
	{
		$timthumb = new thumb_magic($options);
		return $timthumb->getThumbPath();
	}
	
	public function getRandomFilePath()
	{
		$image_path = null;
		
		if ($this->loadImageDirectory())
		{
			$count = count($this->file_list);
			
			if ($count==1)
			{
				$this->moveFileListPointer(0);
			}
			elseif ($count>1)
			{
				$rand = rand(0,$count-1);
				$this->moveFileListPointer($rand);
			}
			
			$image_path = $this->getCurrentFilePath();
		}
		
		return $image_path;
	}
}
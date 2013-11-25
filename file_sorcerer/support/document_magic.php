<?php


// *****************************************************************************************
// *************************** DOCUMENT DETAIL CLASS ***************************************
// *****************************************************************************************
/*
 * document_magic
 *
 * Small workhorse class that helps get every document file loaded
 *
 *
 */
include_once('sorcerer_apprentice.php');
 
class document_magic extends sorcerer_apprentice
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
		$this->options['FILE_BASE']      = FILE_BASE . 'documents' . DS;
		$this->options['FILE_BASE_WEB']  = FILE_BASE_WEB . 'documents' . DS;
		$this->options['CATEGORY_LIST']  = false;
		$this->options['CATEGORY']       = null;
		$this->options['NAME']           = null;
		$this->options['CURRENT_PATH']   = null;
		$this->options['CURRENT_FILE']   = null;
		
		$this->addOptions($options);
	}
	
	public function loadDirectory()
	{
		$category_path = $this->options['FILE_BASE'] . $this->options['CATEGORY'];
		// Now get a list of files and directories
		$this->options['CATEGORY_LIST'] = $this->getDirectoryList($category_path);
	}
	
	public function loadDocumentDirectory()
	{
		$status = false;
		$this->loadDirectory();
		
		// This call loads the FILES from the CATEGORY_LIST into an array
		if ( (isset($this->options['CATEGORY_LIST']['FILES'])) && (count($this->options['CATEGORY_LIST']['FILES'])>0) )
		{
			$this->file_list = $this->options['CATEGORY_LIST']['FILES'];
			$this->options['CURRENT_PATH'] = $this->options['CATEGORY'] . DS;
			$status = true;
		}
		
		return $status;
	}
	
	public function getCurrentFilePath()
	{
		return $this->options['FILE_BASE_WEB'] . $this->options['CURRENT_PATH'] . $this->options['CURRENT_FILE'];
	}
	
	/*
	 * getDocumentFilePath
	 *
	 * This is called when an object is looking for a specific file. It loads the directories
	 * and sets the pointer on the first file, then attempts to load the NAMED file by checking
	 * it against the loaded directories. If it is not available, you get false.
	 *
	 */
	public function getDocumentFilePath()
	{
		if ( ($this->loadDocumentDirectory()) && ($this->moveFileListPointer(0)) )
		{
			return $this->getNamedFilePath();
		}
		else
		{
			return false;
		}
	}
	
	public function getNamedFilePath()
	{	
		if (in_array($this->options['NAME'], $this->options['CATEGORY_LIST']['FILES']))
		{
			return $this->options['FILE_BASE_WEB'] . $this->options['CURRENT_PATH'] . $this->options['NAME'];
		}
		else
		{
			return $this->getCurrentFilePath();
		}
	}
}
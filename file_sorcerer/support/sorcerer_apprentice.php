<?php
/*
	The sorcerer_apprentice class is never called alone, it is always extended from
	each of the image_detail class or the document_detail class
	
*/

class sorcerer_apprentice extends details
{	
	public $file_list = null;
	
	public function moveFileListPointer($item=0)
	{
		$success = false;
		
		if (isset($this->file_list[$item]))
		{
			$success = true;
			$this->options['CURRENT_FILE'] = $this->file_list[$item];
		}
		
		return $success;
	}
	
	public function getDirectoryList($path)
	{
		if (!file_exists($path))
		{
			return false;
		}
		
		$iterator = new DirectoryIterator($path);
		$files = array();
		$dirs  = array();
		$info  = array();

		foreach ($iterator as $fileinfo)
		{
		    if (!$fileinfo->isDot())
		    {
		        if ($fileinfo->isFile())
		        {
			        $files[] = $fileinfo->getFilename();
		        }
		        elseif ($fileinfo->isDir())
		        {
			        $dirs[] = $fileinfo->getFilename();
		        }  
		    }
		}
		
		$info['FILES'] = $files;
		$info['DIRS']  = $dirs;
		
		return $info;
	}
	
	public function checkFilePath($path)
	{
		return file_exists($this->FILE_BASE . $path);
	}
}

?>
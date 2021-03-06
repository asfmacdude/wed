<?php
/*
	The imagineer class is never called alone, it is always extended from
	each of the Imagineers
	
*/

abstract class imagineer
{		
	abstract public function init();
	abstract protected function setOptions();
	
	public function __get($name)
	{
		return  (isset($this->options[$name])) ? $this->options[$name] : null;
	}
	
	public function __set($name,$value)
	{
		$this->options[$name] = $value;
	}
	
	public function addOptions($options)
	{
		$this->options = array_merge( $this->options, $options);
	}
	
	public function setHTML($options=null)
	{
		return null;
	}
	
	public function getHTML($options=null)
	{
		$html = null;
		$html = $this->setHTML($options);
		return ( (is_null($html)) && (isset($this->options['DEFAULT_HTML'])) ) ? $this->options['DEFAULT_HTML'] : $html;
	}
	
	public function getAjaxHTML($options=null)
	{
		return null;
	}
	
	public function getDetailObject($id=null)
	{
		// This function returns the actual detail object. In some cases
		// this is needed because certain detail objects don't really generate
		// HTML.
		if ( (!is_null($id)) && (isset($this->options['DETAILS'][$id])) )
		{
			return $this->options['DETAILS'][$id];
		} 
	}
    
    // *******************************************************************
    // ********  PROFESSOR OPTIONS ***************************************
    // *******************************************************************
    public function getSystemValue($name,$default=null)
    {
	    return wed_getSystemValue($name,$default,$this->options['CLASS_NAME']);
    }
    
    // *******************************************************************
    // ********  File Tools **********************************************
    // *******************************************************************
    public function loadSupportFiles($local_path='support')
    {
    	// This will look to see if the Imagineer has a support folder and load all support files in that directory
    	if (file_exists($this->options['LOCAL_PATH'] . DS . $local_path))
    	{
    		$support_path = $this->options['LOCAL_PATH'] . DS . $local_path;
    		
    		$iterator = new DirectoryIterator($support_path);

			foreach ($iterator as $fileinfo)
			{
			    if ( (!$fileinfo->isDir()) && (!$fileinfo->isDot()) )
			    {
			        $filename = $fileinfo->getFilename();
			        
			        if ($filename!='index.php')
			        {
			        	include_once($support_path.DS.$filename);
			        }
			    }
			}
    	}
    }
    
    public function includeLocalFile($file=null,$ext='.php')
	{		
		if ((!is_null($file)) && (isset($this->options['LOCAL_PATH'])))
		{
			$path = $this->options['LOCAL_PATH'] . DS . $file . $ext;
			
			if (file_exists($path))
			{
				include_once($path);
			}
		}
	}
    
	// *******************************************************************
    // ********  String Cleaning Tools ***********************************
    // *******************************************************************
    public function infoString()
    {
	    return 'Object Name: ' . $this->options['CLASS_NAME'];
    }
    
    // *******************************************************************
    // ********  Logging Tools *******************************************
    // *******************************************************************
    public function logMessage($message=null)
    {
	    wed_logMessage($message,$this->options['CLASS_NAME']);
    }
    
    public function logOptions()
    {
	    wed_logMessageValue($this->options);
    }
    
    // *******************************************************************
    // ********  Error and Debug Tools ***********************************
    // *******************************************************************
    public function setErrorStatus($code=0)
	{
		$this->options['ERROR_STATUS'] = $code;
		
		if ($code>0)
		{
			$this->logMessageValue('Error Status: ' . $code);
		}
	}
	
	public function getErrorStatus()
	{
		// ERROR_STATUS will always be a number. No error=0, that way different objects
		// can have different error codes
		return (isset($this->options['ERROR_STATUS'])) ? $this->options['ERROR_STATUS'] : 0;
	}
	
	// *******************************************************************
    // ********  changeDetailObject **************************************
    // *******************************************************************
	public function changeDetailObject($object)
	{
		if ($object instanceof detail_object)
		{
			return $object;
		}
		else
		{
			// $object is array so convert to detail_object
			if (!is_array($object))
			{
				$object = array();
			}
			return new detail_object($object,$this->options['CLASS_NAME']);
		}
	}
}

?>
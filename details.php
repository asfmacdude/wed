<?php
/*
	The details class is never called alone, it is always extended from
	each of the detail objects
	
*/

abstract class details
{		
	public function __get($name)
	{
		return  (isset($this->options[$name])) ? $this->options[$name] : null;
	}
	
	public function __set($name,$value)
	{
		$this->options[$name] = $value;
	}
	
	public function __call($method,$arguments=null)
	{
		if (method_exists($this,$method))
		{
			return call_user_func(array($this, $action), $arguments);
		}
		else
		{
			return false;
		}
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
		
		$error = wed_getSystemValue('SYSTEM_ERROR');
		
		return (is_null($error)) ? $html : null;
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
    
    public function devShowErrorDiv($html)
	{
		return str_replace('<error-div>', Error::getMessage(), $html);
	}
	
	public function devShowMessageDiv($html)
	{
		return str_replace('<message-div>', messages::getMessage(), $html);
	}
}

?>
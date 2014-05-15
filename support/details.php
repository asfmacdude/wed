<?php
/*
	The details class is never called alone, it is always extended from
	each of the detail objects
	
*/

abstract class details
{		
	public $component_object = null;
	
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
		if (is_array($options))
		{
			$this->options = array_merge( $this->options, $options);
		}
		else
		{
			dbug($options);
		}
		
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
    // ********  COMPONENTS OPTIONS **************************************
    // *******************************************************************
    public function loadComponent($options=array())
    {
	    if (!is_null($this->options['COMPONENT']))
	    {
		    $index_file = COMPONENT_BASE . $this->options['COMPONENT'] . DS . 'index.php';
		    
		    if (file_exists($index_file))
		    {
			    include_once($index_file);
			    $this->component_object = new $this->options['COMPONENT']($options);
			    
			    if (method_exists($this->component_object,'uploadOptions'))
			    {
				    $this->addOptions($this->component_object->uploadOptions());
			    }
		    }
	    }
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

/*
 * class detail_object
 *
 * I want to use this class to have a standard object for passing varibles
 * around from function to function.
 *
 * 
 */
class detail_object
{
	public $options;
	
	public function __construct($options=array(),$source=null)
	{	
		$this->setOptions($options,$source);
	}
	
	public function setOptions($options,$source)
	{
		$this->options['SOURCE'] = $source;
		$this->addOptions($options);
		$this->registerObject($source);
	}
	
	public function addOptions($options)
	{
		if (is_array($options))
		{
			$options       = wed_standardKeys($options);
			$this->options = array_merge( $this->options, $options);
		}
		else
		{
			dbug($options);
		}		
	}
	
	public function __get($name)
	{
		$name = strtoupper($name);
		return  (isset($this->options[$name])) ? $this->options[$name] : null;
	}
	
	public function __set($name,$value)
	{
		$this->options[strtoupper($name)] = $value;
	}
	
	public function registerObject($source=null)
	{
		wed_registerObject($this,$source);
	}
}

?>
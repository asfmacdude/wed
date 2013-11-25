<?php


// *****************************************************************************************
// *************************** CURL MAGIC CLASS ********************************************
// *****************************************************************************************
/*
 * curl_magic
 *
 * Small workhorse class that helps get every image file loaded
 *
 *
 */
include_once('sorcerer_apprentice.php');
 
class curl_magic extends sorcerer_apprentice
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
		$this->options['URL']            = null;
		$this->options['REFERER']        = null;
		$this->options['USERAGENT']      = 'MozillaXYZ/1.0';
		$this->options['HEADER']         = 0;
		$this->options['RETURNTRANSFER'] = true;
		$this->options['TIMEOUT']        = 10;
		
		$this->addOptions($options);
	}
	
	private function curlDownload()
	{
		$output = null;
		
	    // is cURL installed yet?
	    if (function_exists('curl_init'))
	    {
		    // OK cool - then let's create a new cURL resource handle
		    $ch = curl_init();
		 
		    // Now set some options (most are optional)
		 
		    // Set URL to download
		    curl_setopt($ch, CURLOPT_URL, $this->options['URL']);
		 
		    // Set a referer
		    curl_setopt($ch, CURLOPT_REFERER, $this->options['REFERER']);
		 
		    // User agent
		    curl_setopt($ch, CURLOPT_USERAGENT, $this->options['USERAGENT']);
		 
		    // Include header in result? (0 = yes, 1 = no)
		    curl_setopt($ch, CURLOPT_HEADER, $this->options['HEADER']);
		 
		    // Should cURL return or print out the data? (true = return, false = print)
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, $this->options['RETURNTRANSFER']);
		 
		    // Timeout in seconds
		    curl_setopt($ch, CURLOPT_TIMEOUT, $this->options['TIMEOUT']);
		 
		    // Download the given URL, and return output
		    $output = curl_exec($ch);
		 
		    // Close the cURL resource, and free system resources
		    curl_close($ch);
	    }
	 
	    return $output;
	}
	
	public function getOutput()
	{
		return $this->curlDownload();
	}
}
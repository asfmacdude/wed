<?php


// *****************************************************************************************
// *************************** JS DETAIL CLASS ****************************************
// *****************************************************************************************
/*
 * js_detail
 *
 * Small workhorse class that helps get every js file loaded
 *
 * IMPORTANT NOTE: You have to go ahead and include_once the tools file even though
 * loadSupportFiles() will include it because if it's not included before this detail
 * file is included, you will get an error
 */

class js_detail extends details
{
	public $options = array();
	
	public function __construct($options)
	{
		$this->setOptions();
		$this->addOptions($options);
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME'] = __CLASS__;
		$this->options['LOCAL_PATH'] = dirname(__FILE__);
		$this->options['BASE_PATH']  = THEME_BASE_WEB;
		$this->options['ID']         = '';
		$this->options['LOAD']       = false;
		$this->options['KEY']        = ''; // Key is where will this javascript end up on the page
		$this->options['TYPE']       = ''; // LIST, FILE, CDN or SCRIPT
		$this->options['PATH']       = ''; // Path to the Javascript file	
		$this->options['TAG']        = ''; // Used to select from List
		$this->options['SCRIPT']     = ''; // load actual javascript code here	 
		$this->options['SCRIPT_FILE_TEXT']  = '<script type="text/javascript" src="%s"></script>'.LINE1;
		$this->options['WRAP']       = '%s'; // wraps the script	
		$this->options['JS_LIST']    = array();
		$this->options['VERSION']    = null;
	}
	
	private function formatHTML($href)
	{
		$html = sprintf($this->options['SCRIPT_FILE_TEXT'], $href );
		return sprintf($this->options['WRAP'], $html );
	}
	
	public function setHTML($options=null)
	{
		$html               = '';		
		$paths              = array();
		$paths['FILE']      = $this->options['BASE_PATH'] . $this->options['PATH'];
		$paths['CDN']       = $this->options['PATH'];
		$paths['LIBRARY']   = LIBRARY_BASE_WEB . $this->options['PATH'];
		$paths['COMPONENT'] = COMPONENT_BASE_WEB . $this->options['PATH'];
		
		switch ($this->options['TYPE'])
		{
			case 'LIST':
				if (isset($this->options['JS_LIST'][$this->options['TAG']]))
				{
					// This adds the version on items that version may vary from theme to theme
					$html = sprintf($this->options['JS_LIST'][$this->options['TAG']], $this->options['VERSION']);
				}
				break;
				
			case 'SCRIPT':
				$html = '<script type="text/javascript">' . $this->options['SCRIPT'] . '</script>';
				break;
			
			default;
				$html = $this->formatHTML($paths[$this->options['TYPE']]);	
		}
		
		return $html;
	}
}

?>
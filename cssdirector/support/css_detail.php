<?php


// *****************************************************************************************
// *************************** CSS DETAIL CLASS ****************************************
// *****************************************************************************************
/*
 * css_detail
 *
 * Small workhorse class that helps get every css file loaded
 *
 *
 */
 
class css_detail extends details
{
	public $options = array();
	
	public function __construct($options)
	{
		$this->setOptions();
		$this->addOptions($options);
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']   = __CLASS__;
		$this->options['LOCAL_PATH']   = dirname(__FILE__);
		$this->options['BASE_PATH']    = THEME_BASE_WEB;
		$this->options['CSS_DIR']      = DS . 'css' .DS;
		$this->options['LOAD']         = false;	
		$this->options['FILE']         = 'styles.css';
		$this->options['MEDIA']        = 'screen';
		$this->options['PATH']         = '';
		$this->options['TYPE']         = 'NORMAL'; // or STYLE_STRING
		$this->options['STYLE_STRING'] = null;
		$this->options['LINK_TEXT']    = '<link href="%s" media="%s" rel="stylesheet" />'.LINE1;
	}
	
	private function formatHTML($html)
	{
		$media = $this->options['MEDIA'];
		return sprintf($this->options['LINK_TEXT'], $html , $media);
	}
	
	public function setHTML($options=null)
	{
		$html = '';
		
		if ($this->options['LOAD'])
		{	
			$paths              = array();
			$paths['NORMAL']    = $this->options['BASE_PATH'] . $this->options['PATH'];
			$paths['CDN']       = $this->options['PATH'];
			$paths['LIBRARY']   = LIBRARY_BASE_WEB . $this->options['PATH'];
			$paths['COMPONENT'] = COMPONENT_BASE_WEB . $this->options['PATH'];
			
			if ($this->options['TYPE'] === 'STYLE_STRING')
			{
				$html  = $this->options['STYLE_STRING'];
			}
			else
			{
				$html = $this->formatHTML( $paths[$this->options['TYPE']] );
			}
		}
		
		return $html;
	}
}

?>
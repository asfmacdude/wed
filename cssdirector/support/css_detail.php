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
		$this->options['FILE']         = 'styles.css';
		$this->options['MEDIA']        = 'screen';
		$this->options['PATH']         = '';
		$this->options['TYPE']         = 'NORMAL'; // or STYLE_STRING
		$this->options['STYLE_STRING'] = null;
		$this->options['LINK_TEXT']    = '<link href="%s" media="%s" rel="stylesheet" />'.LINE1;
	}
	
	public function setHTML($options=null)
	{
		$html = '';
	
		if ($this->options['TYPE'] === 'NORMAL')
		{
			$href  = $this->options['BASE_PATH'] . $this->options['PATH'];
			$media = $this->options['MEDIA'];
			$html  = sprintf($this->options['LINK_TEXT'], $href , $media);
		}
		elseif ($this->options['TYPE'] === 'CDN')
		{
			$href  = $this->options['PATH'];
			$media = $this->options['MEDIA'];
			$html  = sprintf($this->options['LINK_TEXT'], $href , $media);
		}
		elseif ($this->options['TYPE'] === 'STYLE_STRING')
		{
			$html  = $this->options['STYLE_STRING'];
		}
		else
		{
			$html  = $this->options['LINK_TEXT'];
		}
		
		return $html;
	}
}

?>
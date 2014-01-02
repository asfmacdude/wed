<?php
/*
 * @version		$Id: search_detail.php 1.0 2009-03-03 $
 * @package		DreamWish
 * @subpackage	main
 * @copyright	Copyright (C) 2012 Medley Productions. All rights reserved.
 * 
 * DreamWish is a Disney inspired CMS system developed by Randy Cherry
 * Dedicated to the dreamer of dreams, Walt Disney
 * 
 * 'I believe in being an innovator.' - Walt Disney
 * 
 * 
 */
defined( '_GOOFY' ) or die();
/*
 * search_detail.php
 * 
 */
include_once('search_functions.php');

class search_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']        = __CLASS__;
		$this->options['LOCAL_PATH']        = dirname(__FILE__);
		$this->options['ID']                = 'search1'; // this is assigned by Presentations
		$this->options['CODE']              = wed_getSystemValue('THEME','System').'-search';
		$this->options['CALL']              = null;
		$this->options['HEADING']           = null;
		$this->options['SEARCH_FORMATS']    = array();
		$this->options['SEARCH_TYPE']       = null;
		$this->options['SEARCH_OPTIONS']    = array();
		$this->options['SETUP_ID']          = null;
		$this->options['SETUP_CSS']         = null;
		$this->options['SETUP_JS']          = null;
		$this->options['SETUP_JS_ASSETS']   = null;
		$this->addOptions($options);
		
		if (!is_null($this->options['HEADING']))
		{
			wed_addSystemValue('HEADER_1',$this->options['HEADING']);
		}
	}
	
	// *******************************************************************
    // ********  buildSearch *********************************************
    // *******************************************************************
	public function buildSearch()
	{
		$html = null;
		if ( ($this->getSearchInfo()) && ($this->getSearch()) )
		{
			$html = $this->buildPresentation();
		}
		else
		{
			$html = '<p>'.wed_getSystemValue('SEARCH_EMPTY').'</p>'.LINE1;
		}
		
		return $html;
	}
	
	// *******************************************************************
    // ********  getSearchInfo by Code ***********************************
    // *******************************************************************
    public function getSearchInfo()
	{
		$status           = false;
		$setup_db         = wed_getDBObject('presentation_setups');
		
		if ($setup_db->loadSetupCode($this->options['CODE']))
		{	
			$this->options['SETUP_ID']        = $setup_db->getValue('id');
			$this->options['SETUP_CSS']       = $setup_db->getValue('css');
			$this->options['SETUP_JS']        = $setup_db->getValue('js');
			$this->options['SETUP_JS_ASSETS'] = $setup_db->getDetail('JS_ASSETS');
			$this->options['SEARCH_FORMATS']  = $setup_db->getIncludeFile();
			
			if (is_null($this->options['SEARCH_FORMATS']))
			{
				$this->options['SEARCH_FORMATS'] = $setup_db->getFormats();
			}
			
			$status = (is_null($this->options['SEARCH_FORMATS'])) ? $status : true;
		}
		
		return $status;
	}
	
	// *******************************************************************
    // ********  buildPresentation ***************************************
    // *******************************************************************
	private function buildPresentation()
	{
		$html             = '';
		$search_db        = $this->options['DB_OBJECT'];
		$formats          = $this->options['SEARCH_FORMATS'];
		$field_values     = (isset($formats['FIELD_VALUES'])) ? $formats['FIELD_VALUES'] : null;
		$container_values = (isset($formats['CONTAINER_VALUES'])) ? $formats['CONTAINER_VALUES'] : null;
		
		if (is_null($field_values))
		{
			return null;
		}
		
		foreach ($field_values as $key=>$value)
		{
			$search[] = '%'.$value.'%';
		}
		
		$rec = 0;

		while ($search_db->moveRecordList($rec))
		{
			$item_html  = $formats['ITEM'];
			$tag_html   = '';
			$image_html = $formats['IMAGE'];
			
			$replace    = $search_db->getFormattedValue($field_values,$formats); // returns an array
			
			dbug($replace);
			
			if (!empty($replace['IMAGE_PATH']))
			{
				$item_html = str_replace('%IMAGE%', $image_html, $item_html);
			}
			
			$item_html = str_replace($search, $replace, $item_html);
			
			if (isset($formats['TAGS']))
			{		
				$tag_array = $search_db->getFormattedValue('TAGS'); // returns an array
				
				if (is_array($tag_array))
				{
					foreach ($tag_array as $tag_name)
					{
						$tag_html .= str_replace( array('%TAG_NAME%','%TAG_LINK%'), array($tag_name, '/tag/'.wed_cleanURL($tag_name)) , $formats['TAGS']['ITEM'] );
					}
					
					$tag_html  = str_replace('%CONTENT%', $tag_html, $formats['TAGS']['WRAP']);
				}

				$item_html = str_replace('%TAGS%', $tag_html, $item_html);	
			}
			
			$html .= $item_html;
			$rec++;
		}
		
		// Put the LIST Wrap around it
		if (isset($formats['LIST']))
		{
			$html = str_replace('%CONTENT%', $html, $formats['LIST']);
		}
		
		// Put the CONTAINER Wrap around it
		if (isset($formats['CONTAINER']))
		{
			if (!is_null($container_values))
			{
				foreach ($container_values as $key=>$value)
				{
					$search_arr[] = '%'.$value.'%';
				}
				
				$replace_arr = $search_db->getFormattedValue($container_values,$formats); // returns an array
			}
			
			$search_arr[]  = '%CONTENT%';
			$replace_arr[] = $html;
			
			$html = str_replace($search_arr, $replace_arr, $formats['CONTAINER']);
		}
		
		// Add the CSS Style Section before the Presentaion
		$html = $this->options['SETUP_CSS'] . $html;
		
		// Add any necessary javscript code
		$this->loadJavascript();

		return $html;
	}
	
	// *******************************************************************
    // ********  getSearch ***********************************************
    // *******************************************************************
	private function getSearch()
	{
		$status = false;
		
		if (!is_null($this->options['SEARCH_TYPE']))
		{
			$function = 'getSearch'.$this->options['SEARCH_TYPE'];
		
			if (function_exists($function))
			{
				$result = call_user_func($function, $this->options);
				
				if ($result)
				{
					$this->options['DB_OBJECT'] = $result;
					$status = true;
				}
			}
		}
		
		return $status;
	}
	
	private function loadJavascript()
	{
		if (!is_null($this->options['SETUP_JS']))
		{
			// Send JS over to jsdirector
			$js_array = array(
				'ID'     => 'PRES_'.$this->options['SETUP_ID'],
				'LOAD'   => true,
				'KEY'    => 'JS_READY_CODE',
				'TYPE'   => 'SCRIPT',
				'SCRIPT' => $this->options['SETUP_JS']
				);
			
			wed_addNewJavascriptAsset($js_array);
		}
		
		$this->loadJavascriptAssets();
	}
	
	private function loadJavascriptAssets()
	{
		if (!is_null($this->options['SETUP_JS_ASSETS']))
		{
			$js_array = explode(',', $this->options['SETUP_JS_ASSETS']);
			wed_loadJavascriptAssets($js_array);
		}
	}
    
	public function setHTML($options=array())
	{
		return $this->buildSearch();
	}
}

<?php


// *****************************************************************************************
// *************************** ARTICLE DETAIL CLASS ****************************************
// *****************************************************************************************
/*
 * article_detail
 *
 * Small workhorse class that helps get every article file loaded
 *
 * NOTE: You must create a content_code "error_not_found" in case of errors.
 */
include_once('article_tools.php');
 
class article_detail extends article_tools
{
	public $options = array();
	public $db;
	
	public function __construct()
	{
		global $walt;
		$this->db = $walt->getImagineer('communicore');
		$this->setOptions();
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']  = __CLASS__;
		$this->options['LOCAL_PATH']  = dirname(__FILE__);
		$this->options['CODE']        = null;
		$this->options['ID']          = null;
		$this->options['ARTICLE']     = null;
		$this->options['EXPT_LENGTH'] = 150;
	}
	
}
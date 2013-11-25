<?php
/*
 * fm_content_main_std
 *
 * form object for the online database content_main
 *
 *
 */

defined( '_GOOFY' ) or die();

include_once('fm_common.php');

class fm_content_main_std extends fm_common
{
	public $options;
	public $db;
	public $data;
	public $table_db;
	
	public function __construct($options=array())
	{
		global $walt;
		$this->db = $walt->getImagineer('communicore');
		$this->setOptions($options);		
	}
	
	public function setOptions($options)
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['FORM_VIEW']      = 'vw_content_main_std.php';

		$this->addOptions($options);
	}
	
	public function setHTML($options)
	{
		$html = '<p>Sorry, unavailable at this time.</p>';
		$db_content_main = $this->db->loadDBObject('content_main');
		$id      = (isset($options['id'])) ? $options['id'] : null ;
		$record  = $db_content_main->loadArticleID($id);
		$fields  = $db_content_main->options['FIELDS'];
		
		if (!$record)
		{
			return $html;
		}
		
		$file = (isset($this->options['FORM_VIEW'])) ? $this->options['FORM_VIEW'] : null ;
		$path = (!is_null($file)) ? $this->options['LOCAL_PATH'] . '/form_views/' . $file : null ;
		
		if (file_exists($path))
		{
			ob_start();
			@include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}
		
		return $this->loadFormValues($fields,$html);
	}
	
	public function getHTML($options)
	{
		return $this->setHTML($options);
	}

}
<?php


// *****************************************************************************************
// ************************* ACCOUNTANT FORM DETAIL CLASS **********************************
// *****************************************************************************************
/*
 * accountant_form_detail
 *
 * The main job of this class is to load the form view file and implement it.
 * The view file itself will format and return html for display. The html is then
 * returned back to the accountant imagineer.
 *
 *
 */
include_once('accountant_form_tools.php');
 
class accountant_form_detail extends accountant_form_tools
{
	public $options = array();
	public $db;
	public $db_connection = null;
	
	public function __construct($options)
	{
		global $walt;
		$this->db = $walt->getImagineer('communicore');
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME'] = __CLASS__;
		$this->options['LOCAL_PATH'] = dirname(__FILE__);
		$this->options['VIEW_PATH']  = $this->options['LOCAL_PATH'] . DS . 'form_views' . DS;
		$this->options['FORM_TABLE'] = '';
		$this->options['DB_TABLE']   = '';
		$this->options['VIEW']       = null;
		$this->options['KEY']        = null;
		$this->options['ID']         = null;
		$this->options['MODE']       = 'edit';
		$this->options['URL_FORMAT'] = '/admin/ajax.php?img_code=acco&key=%s&mode=%s&id=%s';
		$this->options['RETURN_URL'] = 'index.php';
		$this->options['RETURN_VAL'] = null;
		$this->options['RETURN']     = false;
	
		$this->addOptions($options);
		$this->setFormName();
	}
	
	private function setFormName()
	{
		$this->options['FORM_TABLE'] = str_replace('form_', '', $this->options['KEY']);
	}
	
	protected function setDBConnection()
	{
		if (is_null($this->db_connection))
		{
			$this->db_connection  = $this->db->loadDBObject($this->DB_TABLE);
		}
	}
	
	private function loadFormView($options)
	{
		$device    = $this->getDevice();
		$html      = null;
		$view_file = (is_null($this->options['VIEW'])) ? $this->options['FORM_TABLE'] : $this->options['VIEW'] ;
		$view_file = $view_file . '.php';
		$path      = $this->options['VIEW_PATH'] . $view_file;
		$db_conn   = $this->db;
		$detail    = $this;
		$options   = $this->options;

		if (file_exists($path))
		{
			ob_start();
			@include $path;
			$html = ob_get_contents();
			ob_end_clean();
		}

		return $this->loadFormValues($html);
	}
	
	public function setHTML($options=array())
	{
		return $this->loadFormView($options);
	}
}
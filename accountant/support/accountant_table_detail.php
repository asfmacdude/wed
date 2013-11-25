<?php


// *****************************************************************************************
// ************************ ACCOUNTANT TABLE DETAIL CLASS **********************************
// *****************************************************************************************
/*
 * accountant_table_detail
 *
 * The main job of this class is to load the table view file and implement it.
 * The view file itself will format and return html for display. The html is then
 * returned back to the accountant imagineer.
 *
 */
include_once('accountant_table_tools.php');
 
class accountant_table_detail extends accountant_table_tools
{
	public $options = array();
	public $db;
	
	public function __construct($options)
	{
		global $walt;
		$this->db = $walt->getImagineer('communicore');
		$this->setOptions($options);
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']  = __CLASS__;
		$this->options['LOCAL_PATH']  = dirname(__FILE__);
		$this->options['VIEW_PATH']   = $this->options['LOCAL_PATH'] . DS . 'table_views' . DS;
		$this->options['TABLE']       = '';
		$this->options['TABLE_ID']    = '';
		$this->options['TABLE_TAG']   = '<table id="%s" width="%s">';
		$this->options['TABLE_WIDTH'] = '100%';
		$this->options['DB_TABLE']    = '';
		$this->options['COLUMNS']     = array();
		$this->options['ORDER']       = array();
		$this->options['VIEW']        = null;
		$this->options['KEY']         = null;
		$this->options['ID']          = null;
		$this->options['JSON']        = 0;
		$this->options['RETURN_URL']  = 'index.php';
		$this->options['URL_FORMAT']  = '/admin/ajax.php?img_code=acco&key=%s&mode=%s&id=%s';
		$this->options['LINE_FORMAT'] = '<th width="%s" scope="col"><div style="font-size:1.25em;color:#969696;">%s</div></th>';
		$this->options['ADD_FORMAT']  = '<th width="%s" scope="col"><div class="table_td_div_add"><a href="%s" class="button-text-icon open-add-image">Add <span class="plus-10 plix-10"></span></a></div></th>';
		$this->options['ADD_WIDTH']   = '8%';
		$this->options['ADD_LINK']    = '';
		
		$this->addOptions($options);
		$this->setTableName();
	}
	
	private function setTableName()
	{
		$this->options['TABLE'] = str_replace('table_', '', $this->options['KEY']);
	}
	
	protected function setDBConnection()
	{
		if (is_null($this->db_connection))
		{
			$this->db_connection  = $this->db->loadDBObject($this->DB_TABLE);
		}
	}
	
	private function loadTableView($options)
	{
		$device    = $this->getDevice();
		$html      = null;
		$view_file = (is_null($this->options['VIEW'])) ? $this->options['TABLE'] : $this->options['VIEW'] ;
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

		return $html;
	}
	
	public function setHTML($options=array())
	{
		return $this->loadTableView($options);
	}
}
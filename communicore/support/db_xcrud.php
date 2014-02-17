<?php
/*
 * db_xcrud.php
 *
 * This class will be extended by each of the db objects and
 * provide a common set of tools for each object.
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_tools.php');
include_once(COMPONENT_BASE . 'xcrud/xcrud.php');

abstract class db_xcrud extends db_tools
{	
	public $xcrud = null;
	
	public function initXCrud()
	{	
		global $db_settings;
		$this->xcrud = Xcrud::get_instance();
		Xcrud_config::$editor_url = 'http://admin.asffoundation.net/components/editors/ckeditor/ckeditor.js';
		$this->xcrud->connection($db_settings['USERNAME'],$db_settings['PASSWORD'],$db_settings['DB_NAME'],'localhost');		
		$this->xcrud->table($this->options['TABLE_NAME']);
	}
	
	public function renderXCrud()
	{
		$html = null;
		
		if (!is_null($this->xcrud))
		{
			$html = $this->xcrud->render();
		}
		
		return $html;	
	}
	
	public function setupXCrud()
    {
	 	$this->initXCrud();  
    }	
}
?>


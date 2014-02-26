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
	public $xcrud    = null;
	public $patterns = array('email', 'alpha', 'alpha_numeric', 'alpha_dash', 'numeric', 'integer', 'decimal', 'natural');
	
	public function initXCrud()
	{	
		global $db_settings;
		$this->xcrud = Xcrud::get_instance();
		Xcrud_config::$editor_url = 'http://admin.asffoundation.net/components/editors/ckeditor/ckeditor.js';
		$this->xcrud->connection($db_settings['USERNAME'],$db_settings['PASSWORD'],$db_settings['DB_NAME'],'localhost');		
		$this->xcrud->table($this->options['TABLE_NAME']);
		$this->configFields();
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
	
	public function setupXCrud($code=null)
    {
	 	$this->initXCrud();  
    }
    
    public function configFields()
    {
	    $columns  = null;
	    $fields   = null;
	    $disabled = null;
	    
	    $db_fields = $this->setFields(false);
	    
	    foreach ($db_fields as $field=>$data)
	    {
		    $this->checkValidation($data);
		    
		    if (isset($data['LABEL']))
		    {
			    $this->xcrud->label($data['DB_FIELD'],$data['LABEL']);
		    }
			    
		    if ((isset($data['SHOW_COLUMN'])) && ($data['SHOW_COLUMN']))
		    {
			    $columns[] = $data['DB_FIELD'];
		    }
		    
		    if ((isset($data['SHOW_FIELD'])) && ($data['SHOW_FIELD']))
		    {
			    $fields[] = $data['DB_FIELD'];
		    }
		    
		    if ((isset($data['NO_UPDATE'])) && ($data['NO_UPDATE']))
		    {
			    $disabled[] = $data['DB_FIELD'];
		    }
		    
		    if (isset($data['NO_EDITOR']))
		    {
			    $this->xcrud->no_editor($data['DB_FIELD']);
		    }
		    
		    if ( (isset($data['LIST_SELECT'])) && (is_array($data['LIST_SELECT'])) )
		    {
			    $list    = implode(',', $data['LIST_SELECT']);
			    $default = (isset($data['DEFAULT'])) ? $data['DEFAULT'] : $data['LIST_SELECT'][0] ;
			    $this->xcrud->change_type($data['DB_FIELD'],'select',$default,$list);
		    }
	    }
	    
	    if (is_array($columns))
	    {
		    $string = implode(',', $columns);
		    $this->xcrud->columns($string);
	    }
	    
	    if (is_array($fields))
	    {
		    $string = implode(',', $fields);
		    $this->xcrud->fields($string);
	    }
	    
	    if (is_array($disabled))
	    {
		    $string = implode(',', $disabled);
		    $this->xcrud->disabled($string);
	    }
    }
    
    public function checkValidation($data=array())
    {
	    if (isset($data['VALIDATE']))
	    {
		    if ($data['VALIDATE']=='Required')
		    {
			    $this->xcrud->validation_required($data['DB_FIELD']);
		    }
		    elseif (substr($data['VALIDATE'],0,11)=='Characters,')
		    {
			    $x = explode(',', $data['VALIDATE']);
			    
			    if ( (isset($x[1])) && is_int($x[1]) )
			    {
				    $this->xcrud->validation_required($data['DB_FIELD'],$x[1]);
			    }
		    }
	    }
	    
	    if ( (isset($data['PATTERN'])) && (in_array($data['PATTERN'], $this->patterns)) )
	    {
		    $this->xcrud->validation_pattern($data['DB_FIELD'],$data['PATTERN']);
	    }
    }	
}
?>


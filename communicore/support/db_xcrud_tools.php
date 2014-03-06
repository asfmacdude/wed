<?php
/*
 * db_xcrud_tools.php
 *
 * This class is a standalone version of d_xcrud to provide
 * a more flexible experience and alow for using nested tables.
 *
 */
defined( '_GOOFY' ) or die();

include_once(COMPONENT_BASE . 'xcrud/xcrud.php');

class db_xcrud_tools
{	
	public $xcrud          = null;
	public $nested_objects = array();
	public $patterns       = array('email', 'alpha', 'alpha_numeric', 'alpha_dash', 'numeric', 'integer', 'decimal', 'natural');
	
	public function initXCrud()
	{	
		$this->xcrud = Xcrud::get_instance();
		// Xcrud_config::$editor_url = 'http://admin.asffoundation.net/components/editors/ckeditor/ckeditor.js';
	}
	
	public function setTable($table)
	{
		$this->xcrud->table($table);
	}
	
	public function createNestedTable($options=array())
	{
		// Syntax: OBJECT_NAME = PARENT_OBJECT->nested_table(NAME OF CONNECTION, RELATE FIELD-CURRENT TABLE, TABLE TO RELATE TO, RELATE FIELD-RELATE TABLE);
		// OBJECT_NAME could be a NEW Nested Object or an existing one
		// PARENT_OBJECT could be the main $this->xcrud object or it could be another nested object.
		$object_name     = (isset($options['OBJECT_NAME'])) ? $options['OBJECT_NAME'] : null;
		$parent_name     = (isset($options['PARENT_NAME'])) ? $options['PARENT_NAME'] : null;
		$connection_name = (isset($options['CONNECTION_NAME'])) ? $options['CONNECTION_NAME'] : null;
		$relate_from     = (isset($options['RELATE_FROM'])) ? $options['RELATE_FROM'] : null;
		$relate_table    = (isset($options['RELATE_TABLE'])) ? $options['RELATE_TABLE'] : null;
		$relate_to       = (isset($options['RELATE_TO'])) ? $options['RELATE_TO'] : null;
		
		if (is_null($object_name))
		{
			// Error, must have an object name
			echo 'Error: no object name';
			return false;
		}
		
		if ( (is_null($connection_name)) || (is_null($relate_from)) || (is_null($relate_table)) || (is_null($relate_to)) )
		{
			echo 'Error: not enough information';
			return false;
		}
		
		if (is_null($parent_name))
		{
			// Means we are creating a new NESTED OBJECT from the base xcrud object
			$this->nested_objects[$object_name] = $this->xcrud->nested_table($connection_name, $relate_from, $relate_table, $relate_to);
		}
		elseif (isset($this->nested_objects[$parent_name]))
		{
			// Means we are creating a new NESTED OBJECT based on an existin NESTED OBJECT
			$this->nested_objects[$object_name] = $this->nested_objects[$parent_name]->nested_table($connection_name, $relate_from, $relate_table, $relate_to);
		}
		else
		{
			echo 'Error: nesting error occurred';
			return false;
		}

		return true;		
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
    
    public function configFields($field_list,$name=null)
    {
	    $status   = true;
	    $columns  = null;
	    $fields   = null;
	    $disabled = null;
	    $x_object = null;
	    
	    if (!is_null($name))
	    {
		    if (isset($this->nested_objects[$name]))
		    {
			    $x_object = $this->nested_objects[$name];
		    }
		    else
		    {
			    return false;
		    }		    
	    }
	    else
	    {
		    $x_object = $this->xcrud;
	    } 
	    
	    foreach ($field_list as $field=>$data)
	    {
		    $x_object = $this->checkValidation($data,$x_object);
		    
		    if (isset($data['LABEL']))
		    {
			    $x_object->label($data['DB_FIELD'],$data['LABEL']);
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
			    $x_object->no_editor($data['DB_FIELD']);
		    }
		    
		    if ( (isset($data['LIST_SELECT'])) && (is_array($data['LIST_SELECT'])) )
		    {
			    $list    = implode(',', $data['LIST_SELECT']);
			    $default = (isset($data['DEFAULT'])) ? $data['DEFAULT'] : $data['LIST_SELECT'][0] ;
			    $x_object->change_type($data['DB_FIELD'],'select',$default,$list);
		    }
	    }
	    
	    if (is_array($columns))
	    {
		    $string = implode(',', $columns);
		    $x_object->columns($string);
	    }
	    
	    if (is_array($fields))
	    {
		    $string = implode(',', $fields);
		    $x_object->fields($string);
	    }
	    
	    if (is_array($disabled))
	    {
		    $string = implode(',', $disabled);
		    $x_object->disabled($string);
	    }
	    
	    if (isset($this->nested_objects[$name]))
	    {
		    $this->nested_objects[$name] = $x_object;
	    }
	    else
	    {
		    $this->xcrud = $x_object;
	    }
	    
	    return $status;
    }
    
    private function checkValidation($data=array(),$x_object)
    {
	    if (isset($data['VALIDATE']))
	    {
		    if ($data['VALIDATE']=='Required')
		    {
			    $x_object->validation_required($data['DB_FIELD']);
		    }
		    elseif (substr($data['VALIDATE'],0,11)=='Characters,')
		    {
			    $x = explode(',', $data['VALIDATE']);
			    
			    if ( (isset($x[1])) && is_int($x[1]) )
			    {
				    $x_object->validation_required($data['DB_FIELD'],$x[1]);
			    }
		    }
	    }
	    
	    if ( (isset($data['PATTERN'])) && (in_array($data['PATTERN'], $this->patterns)) )
	    {
		    $x_object->validation_pattern($data['DB_FIELD'],$data['PATTERN']);
	    }
	    
	    return $x_object;
    }
    
    public function setRelation($options=array())
    {
	    // $this->xcrud->relation(RELATE_FROM, RELATE_TABLE, RELATE_TO, DISPLAY_FIELD);
	    $object_name     = (isset($options['OBJECT_NAME'])) ? $options['OBJECT_NAME'] : null;
		
		$relate_from     = (isset($options['RELATE_FROM'])) ? $options['RELATE_FROM'] : null;
		$relate_table    = (isset($options['RELATE_TABLE'])) ? $options['RELATE_TABLE'] : null;
		$relate_to       = (isset($options['RELATE_TO'])) ? $options['RELATE_TO'] : null;
		$display_field   = (isset($options['DISPLAY_FIELD'])) ? $options['DISPLAY_FIELD'] : null;
		
		if (is_null($object_name))
		{
			// Apply to main xcrud object
			$this->xcrud->relation($relate_from, $relate_table, $relate_to, $display_field);
		}
		elseif (isset($this->nested_objects[$object_name])) 
		{
			// Apply to a nested object
			$this->nested_objects[$object_name]->relation($relate_from, $relate_table, $relate_to, $display_field);
		}
    }	
}
?>


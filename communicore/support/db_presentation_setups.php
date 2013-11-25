<?php
/*
 * presentation_setups
 *
 * Database object for the online database presentation_setups
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_presentation_setups extends db_common
{
	public $options;
	public $db;
	
	public function __construct($options=array())
	{
		global $walt;
		$this->db     = $walt->getImagineer('communicore');
		$this->setOptions($options);
	}
	
	public function setOptions($options)
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['TABLE_NAME']     = 'presentation_setups';
		$this->options['TABLE_ID_FIELD'] = 'pset_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
	}
	
	private function setFields()
	{
		/*
		 * The 'FIELDS' array setup
		 *
		 * each field/element has a simple name for a key. This helps when placing them on a form because
		 * because you don't have to remember the actual name of the field in the database. Plus, sometimes
		 * you have elements that are not actual fields in the database, but collectors of information that
		 * you can later process and put in whatever field you want. 
		 *
		 * The subkeys are:
		 * TITLE - this is usually the label on the form
		 * VALIDATE - this is the standard validation for the field. It can be overridden if needed, but if no
		 * other validation is specified, it will always run. It can also be an array so that it runs more than one
		 * validation.
		 * MESSAGE - this is the message that will appear if the validation fails
		 * DB_FIELD - this is the ACTUAL name of the field in the database. If this is an ELEMENT (See above note)
		 * then DB_FIELD will have no value.
         * DEFAULT - will contain a default value when a new record is created.		 
         * ERROR - will be added when the list is validated. ERROR can either be 1 (has an error) or 0 (no error)
		 * BE AWARE that VALIDATE can be an array of validations, so ERROR would be returned as an array of error values.
		 * 
		 * Remember to never include to auto-increment id field or the modified timestamp field. These fields will
		 * never be edited or updated from sql statements. They will always be updated or created automaticlly by mysql.
		 *
		 * Whenever addValues is run, a VALUE key is inserted and the current value from the $_REQUEST array is inserted.
		 *
		 * NOTE: Other values can be added as needed.
		 */
		$fields = array();
		
		$fields['id'] = array(
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'pset_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'TITLE'     => 'Creation',
			'DB_FIELD'  => 'pset_modified',
			'NO_UPDATE' => 1
			);
			
		$fields['code'] = array(
			'TITLE'    => 'Setup Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'pset_code'
			);
		
		$fields['title'] = array(
			'TITLE'    => 'Setup Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'pset_title'
			);
			
		$fields['description'] = array(
			'TITLE'    => 'Description',
			'DB_FIELD' => 'pset_description'
			);
			
		$fields['max'] = array(
			'TITLE'    => 'Max Items',
			'DB_FIELD' => 'pset_max_items'
			);
			
		$fields['active'] = array(
			'TITLE'    => 'Active',
			'DB_FIELD' => 'pset_active',
			'DEFAULT'  => 'Y'
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'pset_details',
			'INSTRUCT' => 'Details are various options for this schedule. Example:  LINK| apple.com;'
			);
		
		$fields['css'] = array(
			'LABEL'    => 'CSS',
			'DB_FIELD' => 'pset_css',
			'INSTRUCT' => 'CSS for the styling this banner'
			);
			
		$fields['js'] = array(
			'LABEL'    => 'Javascript',
			'DB_FIELD' => 'pset_js',
			'INSTRUCT' => 'Javascript for this banner'
			);
			
		$fields['formats'] = array(
			'LABEL'    => 'Formats ',
			'DB_FIELD' => 'pset_formats_json',
			'INSTRUCT' => 'Formatting is JSON format.'
			);
			
		$fields['data'] = array(
			'LABEL'    => 'Formats ',
			'DB_FIELD' => 'pset_data_json',
			'INSTRUCT' => 'Data field for arrays in JSON format.'
			);
			
		$fields['file'] = array(
			'LABEL'    => 'Include File ',
			'DB_FIELD' => 'pset_include_file',
			'INSTRUCT' => 'Name of include file if used.'
			);
					
		return $fields;
	}
	
	public function loadAllForList()
	{
		$fields = 'pset_id,pset_title';
		$order  = 'pset_title';
		$data   = $this->selectAllForList($fields,$order);	
		return (!$data) ? false : $data ;
	}
	
	public function loadSetupID($id=null)
	{
		if (is_null($id))
        {
            return false;
        }
        
		$data = $this->selectByID($id);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ;
	}
	
	public function loadSetupTitle($title=null)
    {   
        if (is_null($title))
        {
            return false;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 'title' => $title );	
		$order_str = $this->options['FIELDS']['title']['DB_FIELD'];	
		$data      = $this->selectByPairs($pairs, $order_str,false);
		
		$this->addValues_Data($data);
		return (!$data) ? false : true ;
    }
    
    public function loadSetupCode($code=null)
    {   
        if (is_null($code))
        {
            return false;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 'code' => $code );	
		$order_str = $this->options['FIELDS']['title']['DB_FIELD'];	
		$data      = $this->selectByPairs($pairs, $order_str,false);
		
		$this->addValues_Data($data);
		return (!$data) ? false : true ;
    }
    
    public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
    
    public function getFormats()
    {
	    // Note: this table formats field uses JSON formatting
	    $format_field = $this->getValue('formats');
	    return wed_decodeJSON($format_field,true);
    }
    
    public function getIncludeFile()
    {
	    $setup = null;
	    $file  = $this->getValue('file');
	    
	    if (!empty($file))
	    {
		    $theme = wed_getSystemValue('THEME');
	
			$options['DIR_PATH'] = THEME_BASE . $theme . DS;
			$options['DIR_NAME'] = 'templates';
				
			$dir_path = wed_getAlternateDirectory($options);
			$path     = $dir_path . DS . $file;
			
			if (file_exists($path))
			{
				$setup = include $path;
			}
	    }
	    
	    return $setup;
    }
}
?>
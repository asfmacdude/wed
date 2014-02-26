<?php
/*
 * db_themes
 *
 * Database object for the themes menus atabase
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_theme_menus extends db_common
{
	public $options;
	public $db;
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
	}
	
	public function setOptions($options)
	{
		$this->options['CLASS_NAME']     = __CLASS__;
		$this->options['LOCAL_PATH']     = dirname(__FILE__);
		$this->options['TABLE_NAME']     = 'theme_menus';
		$this->options['TABLE_ID_FIELD'] = 'tmu_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->addOptions($options);
	}
	
	public function setFields($join=true)
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
			'DB_FIELD'  => 'tmu_id',
			'NO_UPDATE' => 1
			);
		
		$fields['themeid'] = array(
			'TITLE'    => 'Theme ID',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The theme id is a required field',
			'DB_FIELD' => 'tmu_theme_id'
			);
			
		$fields['theme'] = array(
			'TITLE'    => 'Theme Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The theme name is a required field',
			'DB_FIELD' => 'tmu_theme_name'
			);
			
		$fields['top'] = array(
			'TITLE'    => 'Top Level Menu',
			'DB_FIELD' => 'tmu_top_level',
			'INSTRUCT' => 'Is this a Top Level Menu?'
			);
			
		$fields['format'] = array(
			'LABEL'    => 'Formats',
			'DB_FIELD' => 'tmu_menu_formats',
			'INSTRUCT' => 'Formats are various formatting options for this theme in JSON format.'
			);
		
		$fields['code'] = array(
			'TITLE'    => 'Theme Menu Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The theme menu code is a required field',
			'DB_FIELD' => 'tmu_menu_code'
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'tmu_details',
			'INSTRUCT' => 'Details are various options for this theme in JSON format.'
			);	
						
		return $fields;
	}
    
    public function loadAllForList()
	{
		$fields = 'tmu_id,tmu_menu_code';
		$order  = 'theme_name';
		$data   = $this->selectAllForList($fields,$order);	
		return (!$data) ? false : $data ;
	}
	
	public function loadMenuCode($code=null)
	{
		$data = $this->selectByCode($code);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let caller know
	}
	
	public function loadMenuName($name=null)
	{
		// This loads a menu by name as well as by theme
		$theme = wed_getSystemValue('THEME');
		$pairs = array('theme' => $theme, 'code' => $name);
		$data  = $this->selectByPairs($pairs,null,false);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let caller know
	}
	
	public function selectByCode($code=null)
    {
        if (is_null($code))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['code']['DB_FIELD'].'="'.$code.'"';

        $sql = 'SELECT * FROM '.$table.$where_str;
        
        return $this->dbRow($sql);
    }
    
    public function getDetails()
    {
	    // Note: this table details field uses JSON formatting
	    $detail_field = $this->getValue('details');
	    return wed_decodeJSON($detail_field,true);
    }
    
    public function getFormats()
    {
	    // Note: this table formats field uses JSON formatting
	    $format_field = $this->getValue('format');
	    return wed_decodeJSON($format_field,true);
    }
	
}
?>
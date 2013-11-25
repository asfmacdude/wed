<?php
/*
 * search_setups
 *
 * Database object for the online database search_setups
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_search_setups extends db_common
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
		$this->options['TABLE_NAME']     = 'search_setups';
		$this->options['TABLE_ID_FIELD'] = 'sset_id';
		
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
			'DB_FIELD'  => 'bsset_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'TITLE'     => 'Creation',
			'DB_FIELD'  => 'sset_modified',
			'NO_UPDATE' => 1
			);
		
		$fields['theme'] = array(
			'TITLE'    => 'Theme',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The theme is a required field',
			'DB_FIELD' => 'sset_theme',
			'DEFAULT'  => 'System'
			);
			
		$fields['code'] = array(
			'TITLE'    => 'Code',
			'DB_FIELD' => 'sset_code',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'sset_code'
			);
		
		$fields['format'] = array(
			'LABEL'    => 'Formats',
			'DB_FIELD' => 'sset_formats',
			'INSTRUCT' => 'Formats are various formatting options for this theme in JSON format.'
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'sset_details',
			'INSTRUCT' => 'Details are various options for this schedule. Example:  LINK| apple.com;'
			);
		
		$fields['html'] = array(
			'LABEL'    => 'HTML',
			'DB_FIELD' => 'sset_html',
			'INSTRUCT' => 'Possible HTML for the styling of the search results.'
			);
					
		return $fields;
	}
	
	public function loadSearchSetup($options=null)
    {   
        if (is_null($options))
        {
            return false;
        }
        
        $theme   = (isset($options['THEME'])) ? $options['THEME'] : 'System' ;
		$code    = (isset($options['CODE'])) ? $options['CODE']   : 'default' ;	
		$pairs   = array( 'theme' => $theme, 'code' => $code );	
		$data    = $this->selectByPairs($pairs, null, false);
		
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
	    $format_field = $this->getValue('format');
	    return wed_decodeJSON($format_field,true);
    }
}
?>
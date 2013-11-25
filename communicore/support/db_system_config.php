<?php
/*
 * db_system_config
 *
 * Database object for the online database system_config
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_system_config extends db_common
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
		$this->options['TABLE_NAME']     = 'system_config';
		$this->options['TABLE_ID_FIELD'] = 'sys_cfg_id';
		
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
			'LABEL'     => 'ID',
			'DB_FIELD'  => 'sys_cfg_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'sys_cfg_modify',
			'NO_UPDATE' => 1
			);
		
		$fields['siteid'] = array(
			'LABEL'    => 'Setting Site',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The site is a required field',
			'INSTRUCT' => 'Each setting is site specific so choose which site this will affect.',
			'DB_FIELD' => 'sys_cfg_site_id',
			'DEFAULT'  => 4
			);
			
		$fields['name'] = array(
			'LABEL'    => 'Setting Name',
			'INSTRUCT' => 'This is the name or key of the setting. Be sure it is in ALL CAPS!',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The setting name is a required field',
			'DB_FIELD' => 'sys_cfg_name',
			'DEFAULT'  => 'ENTER_NAME'
			);
			
		$fields['value'] = array(
			'LABEL'    => 'Setting Value',
			'INSTRUCT' => 'Insert the actual value of the setting here.',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The setting value is a required field',
			'DB_FIELD' => 'sys_cfg_value',
			'DEFAULT'  => 'none'
			);
			
		$fields['convert'] = array(
			'LABEL'    => 'Value Type',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The value type is a required field',
			'DB_FIELD' => 'sys_cfg_convert',
			'DEFAULT'  => 'string',
			'LIST-SELECT' => array('string'=>'string','bool'=>'bool','array'=>'array','encrypt'=>'encrypt')
			);
			
		$fields['description'] = array(
			'LABEL'    => 'Description',
			'INSTRUCT' => 'Insert a description of this setting for others to understand.',
			'DB_FIELD' => 'sys_cfg_description'
			);
			
		$fields['message'] = array(
			'LABEL'    => 'Error Message',
			'DB_FIELD' => 'sys_cfg_message'
			);
			
		$fields['validate'] = array(
			'LABEL'    => 'Setting Validation',
			'DB_FIELD' => 'sys_cfg_validate'
			);
			
		$fields['access'] = array(
			'LABEL'    => 'Setting Access',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The setting access is a required field',
			'DB_FIELD' => 'sys_cfg_access',
			'DEFAULT'  => '50:50'
			);
			
		$fields['edit'] = array(
			'LABEL'    => 'Edit Options',
			'DB_FIELD' => 'sys_cfg_edit'
			);
			
		$fields['replace'] = array(
			'LABEL'    => 'Setting Replace',
			'DB_FIELD' => 'sys_cfg_replaceable',
			'DEFAULT'  => 'Y'
			);
		
		return $fields;
	}
	
	public function loadAllForTable($order=null)
	{
		$data = $this->selectAllTable($order);	
		return (!$data) ? false : $data ;
	}
	
	public function loadConfigID($id=null)
	{
		$data = $this->selectByID($id);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let showdirector know
	}
	
	public function selectByName($name=null)
    {
        if (is_null($name))
        {
            return null;
        }
        
        $table      = $this->options['TABLE_NAME'];
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['name']['DB_FIELD'].'="'.$name.'"';
        $sql        = 'SELECT * FROM '.$table.$where_str;
        
        return $this->dbRow($sql);
    }
    
    public function getSettings($id=4)
    {
        if (is_null($id))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array('siteid' => $id);
		$order_str = $this->options['FIELDS']['name']['DB_FIELD'];
		$data      = $this->selectByPairs($pairs, $order_str);
		$data      = $this->stripslashes_deep($data);
		
		$settings = array();
				
		foreach ($data as $key=>$value)
		{
			$settings[$value['sys_cfg_name']] = $this->convertValue($value['sys_cfg_convert'], $value['sys_cfg_value']);
		}
		
		return $settings;
    }
}
?>
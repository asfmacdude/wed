<?php
/*
 * db_content_groups
 *
 * Database object for the online database content_groups
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_content_groups extends db_common
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
		$this->options['TABLE_NAME']     = 'content_groups';
		$this->options['TABLE_ID_FIELD'] = 'cng_id';
		
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
			'DB_FIELD'  => 'cng_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modification'] = array(
			'TITLE'     => 'Modification',
			'DB_FIELD'  => 'cng_modification',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
		
		$fields['code'] = array(
			'TITLE'    => 'Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The 4 character code is a required field',
			'DB_FIELD' => 'cng_code',
			'DEFAULT'  => 'xxxx'
			);
			
		$fields['sysname'] = array(
			'TITLE'    => 'System Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The system name is a required field',
			'DB_FIELD' => 'cng_sys_name'
			);
			
		$fields['title'] = array(
			'TITLE'    => 'Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'cng_group_title'
			);
			
		$fields['pagetitle'] = array(
			'TITLE'    => 'Page Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'cng_page_title'
			);
			
		$fields['details'] = array(
			'TITLE'    => 'Details',
			'DB_FIELD' => 'cng_details'
			);
		
		$fields['active'] = array(
			'TITLE'    => 'Active Y/N?',
			'DB_FIELD' => 'cng_active',
			'DEFAULT'  => 'Y'
			);
			
		return $fields;
	}
	
	public function getGroupID($name)
	{
		$data = $this->selectBySysName($name);
		
		if ($data)
		{
			$this->addValues_Data($data);
			return $this->getValue('id');
		}
		else
		{
			return false;
		}
		
	}
	
	public function getGroupTitle($name)
	{
		$data = $this->selectBySysName($name);
		
		if ($data)
		{
			$this->addValues_Data($data);
			return $this->getValue('title');
		}
		else
		{
			return false;
		}
		
	}
	
	public function checkGroupSysName($name)
	{
		$data = $this->selectBySysName($name);
		$this->addValues_Data($data);
		return (!$data) ? false : true;
	}
	
	public function loadGroupList($format_list=true)
	{
		$fields = 'cng_code,cng_sys_name,cng_title,cng_page_title';
		$order  = 'cng_sys_name';
		$data   = $this->selectAllForList($fields,$order);
		
		if (!$data)
		{
			// query failed to find anything
			return false;
		}	
		elseif ($format_list)
		{
			// Return a associative array based on sys_name
			return $this->formatGroupList($data);
		}
		else
		{
			// Return raw rows data from query
			return $data;
		}
	}
	
	private function formatGroupList($data)
	{
		// This formats the db rows into a nice associative array based of the sys_name
		// as the key
		$list = array();
		
		foreach ($data as $row=>$fields)
		{
			$list[$fields['cng_sys_name']] = $fields;
		}
		
		return $list;
	}
	
	public function selectBySysName($name=null)
    {
        if (is_null($name))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 'sysname'=> $name );
		
		return $this->selectByPairs($pairs, null, false); 
    }
    
    public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
}
?>
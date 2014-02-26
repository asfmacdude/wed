<?php
/*
 * db_games_sports
 *
 * Database object for the online database games_sports
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_games_sports extends db_common
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
		$this->options['TABLE_NAME']     = 'games_sports';
		$this->options['TABLE_ID_FIELD'] = 'sport_id';
			
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
			'LABEL'     => 'ID',
			'DB_FIELD'  => 'sport_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'sport_modified',
			'NO_UPDATE' => 1
			);
		
		$fields['title'] = array(
			'LABEL'    => 'Sport Title',
			'INSTRUCT' => 'Unique name or title for this sport.',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'sport_title',
			'DEFAULT'  => ''
			);
			
		$fields['active'] = array(
			'LABEL'    => 'Active',
			'VALIDATE' => 'isRequired',
			'DB_FIELD' => 'sport_active',
			'DEFAULT'  => 'Y',
			'LIST-SELECT' => array('Y'=>'Y','N'=>'N')
			);
			
		$fields['code'] = array(
			'LABEL'    => 'Sport Code',
			'INSTRUCT' => 'Unique code for calling this sport. (Must be UNIQUE!)',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'sport_code',
			'DEFAULT'  => 'xxxx'
			);
		
		$fields['sysname'] = array(
			'LABEL'    => 'Sport System Name',
			'INSTRUCT' => 'Unique name for calling this sport. (Must be UNIQUE!)',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'sport_sysname'
			);
		
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'sport_details',
			'INSTRUCT' => 'Details are various options for this sport. Example:  VENUE: Homerun Field;'
			);
			
		$fields['startdate'] = array(
			'LABEL'    => 'Start Date',
			'DB_FIELD' => 'sport_start_date',
			'INSTRUCT' => ''
			);
			
		$fields['register'] = array(
			'LABEL'    => 'Registration Open',
			'DB_FIELD' => 'sport_register',
			'DEFAULT'  => 'N',
			'LIST-SELECT' => array('Y'=>'Y','N'=>'N')
			);
		
		$fields['overview'] = array(
			'LABEL'    => 'Overview',
			'DB_FIELD' => 'sport_overview',
			'INSTRUCT' => 'The overview should only be about 150 characters or less and should give a clear introduction of the sport.'
			);
			
		$fields['fullarticle'] = array(
			'LABEL'    => 'Full Content',
			'DB_FIELD' => 'sport_content',
			'NO_TABLE' => true,
			'INSTRUCT' => 'Use the WYSIWYG Editor to format and style the content to your taste.'
			);
		
		$fields['staff'] = array(
			'LABEL'    => 'Staff Person',
			'INSTRUCT' => 'Staff person in charge of this sport.',
			'DB_FIELD' => 'sport_staff',
			'DEFAULT'  => ''
			);
			
		$fields['email'] = array(
			'TITLE'    => 'Staff Email Address',
			'VALIDATE' => array('isEmail','isRequired'),
			'MESSAGE'  => array('The email address is not valid','The email address is a required field'),
			'DB_FIELD' => 'sport_staff_email'
			);
		
		return $fields;
	}
	
	public function loadSport($sport=null)
	{
		$data = $this->selectByCode($sport);
			
		if ($data)
		{
			$this->addValues_Data($data);
		}
		
		return (!$data) ? false : true ;
	}
	
	public function loadActiveSports()
	{
		$pairs = array('active' => 'Y');
		$order = 'sport_title';	
		$data  = $this->selectByPairs($pairs,$order, true);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;
	}
	
	public function loadAllForTable($order=null)
	{
		$data = $this->selectAllTable($order);	
		return (!$data) ? false : $data ;
	}
	
	public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }	
}
?>
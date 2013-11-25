<?php
/*
 * db_system_redirect
 *
 * Database object for the online database system_redirect
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_system_redirect extends db_common
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
		$this->options['TABLE_NAME']     = 'system_redirect';
		$this->options['TABLE_ID_FIELD'] = 'rdr_id';
		
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
			'DB_FIELD'  => 'rdr_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'rdr_modified',
			'NO_UPDATE' => 1
			);
		
		$fields['site'] = array(
			'LABEL'    => 'Site',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The site is a required field',
			'DB_FIELD' => 'rdr_site',
			'DEFAULT'  => 'com_alagames',
			'LIST-SELECT' => array('admin'=>'admin','system'=>'system','com_alagames'=>'com_alagames')
			);
			
		$fields['subject'] = array(
			'LABEL'    => 'Subject',
			'INSTRUCT' => 'The subject will appear immediately after the domain name. Example: /subject',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The subject is a required field',
			'DB_FIELD' => 'rdr_subject'
			);
			
		$fields['action'] = array(
			'LABEL'    => 'Action',
			'INSTRUCT' => 'The action is optional and adds an extra category to the redirect. Example: /subject/action',
			'DB_FIELD' => 'rdr_action'
			);
			
		$fields['code'] = array(
			'LABEL'    => 'Page Code',
			'INSTRUCT' => 'The code is the page code for the page that you are redirecting to',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The page code is a required field',
			'DB_FIELD' => 'rdr_code',
			'DEFAULT'  => 'home_page'
			);
			
		$fields['keywords'] = array(
			'LABEL'    => 'Keywords',
			'INSTRUCT' => 'Keywords will be used in search functions to allow the CMS to redirect by interests.',
			'DB_FIELD' => 'rdr_keywords'
			);
		
		return $fields;
	}
	
	public function loadAllForTable($order=null)
	{
		$data = $this->selectAllTable($order);	
		return (!$data) ? false : $data ;
	}
	
	public function loadRedirectID($id=null)
	{
		$data = $this->selectByID($id);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let showdirector know
	}
		
	public function selectBySubject($subject=null)
    {
        if (is_null($subject))
        {
            return null;
        }
        
        $table = $this->options['TABLE_NAME'];
		
		$pairs = array(
			'site'    => SITE_DOMAIN, 
			'subject' => $subject
		);
		
		$order_str = $this->options['FIELDS']['subject']['DB_FIELD'].','.$this->options['FIELDS']['action']['DB_FIELD'];
		
		return $this->selectByPairs($pairs, $order_str); 
    }
	
}
?>
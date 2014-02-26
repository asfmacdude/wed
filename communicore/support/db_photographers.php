<?php
/*
 * db_photographers
 *
 * Database object for the online database photographers
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_photographers extends db_common
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
		$this->options['TABLE_NAME']     = 'photographers';
		$this->options['TABLE_ID_FIELD'] = 'pho_id';
		
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
			'DB_FIELD'  => 'pho_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'TITLE'     => 'Modified',
			'DB_FIELD'  => 'pho_modified',
			'NO_UPDATE' => 1
			);
		
		$fields['photo_id'] = array(
			'TITLE'    => 'Photographer ID',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The photographer id is a required field',
			'DB_FIELD' => 'pho_photo_id'
			);
			
		$fields['name'] = array(
			'TITLE'    => 'Photographer Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The photographer name is a required field',
			'DB_FIELD' => 'pho_full_name'
			);
			
		$fields['club'] = array(
			'TITLE'    => 'Photography Club',
			'DB_FIELD' => 'pho_club'
			);
			
		$fields['bio'] = array(
			'TITLE'    => 'Photographer Bio',
			'DB_FIELD' => 'pho_bio'
			);
			
		$fields['email'] = array(
			'TITLE'    => 'Photographer Email',
			'DB_FIELD' => 'pho_email'
			);
			
		$fields['twitter'] = array(
			'TITLE'    => 'Photographer Twitter',
			'DB_FIELD' => 'pho_twitter'
			);
			
		$fields['facebook'] = array(
			'TITLE'    => 'Photographer Facebook',
			'DB_FIELD' => 'pho_facebook'
			);
			
		$fields['contact_id'] = array(
			'TITLE'    => 'Contact ID',
			'DB_FIELD' => 'pho_contact_id'
			);
						
		return $fields;
	}
	
	public function selectByPhotoID($id=null)
    {
        if (is_null($id))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
		
		$pairs = array(
			'photo_id'=> $id
		);
		
		return $this->selectByPairs($pairs); 
    }
    
    public function selectByClub($club=null)
    {
        if (is_null($club))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
		
		$pairs = array(
			'club'=> $club
		);
		
		$order_str = $this->options['FIELDS']['club']['DB_FIELD'].','.$this->options['FIELDS']['name']['DB_FIELD'];
		
		return $this->selectByPairs($pairs, $order_str); 
    }
	
}
?>
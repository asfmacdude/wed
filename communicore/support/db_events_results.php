<?php
/*
 * db_events_results
 *
 * Database object for the online results from the games
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_events_results extends db_common
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
		$this->options['TABLE_NAME']     = 'events_results';
		$this->options['TABLE_ID_FIELD'] = 'rslt_id';
		
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
			'DB_FIELD'  => 'rslt_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'TITLE'     => 'Modified',
			'DB_FIELD'  => 'rslt_modification',
			'NO_UPDATE' => 1
			);
		
		$fields['eventid'] = array(
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'rslt_event_id'
			);
		
		$fields['sport'] = array(
			'TITLE'    => 'Sport',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The sport name is a required field',
			'DB_FIELD' => 'rslt_sport'
			);
		
		$fields['sportid'] = array(
			'TITLE'     => 'ID',
			'DB_FIELD'  => 'rslt_event_activity_id',
			'NO_UPDATE' => 1
			);
			
		$fields['event'] = array(
			'TITLE'    => 'Event',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The event name is a required field',
			'DB_FIELD' => 'rslt_event'
			);
			
		$fields['event_code'] = array(
			'TITLE'    => 'Event Code',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The event code is a required field',
			'DB_FIELD' => 'rslt_event_code'
			);
			
		$fields['title'] = array(
			'TITLE'    => 'Title',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'rslt_title'
			);
			
		$fields['score'] = array(
			'TITLE'    => 'Score',
			'DB_FIELD' => 'rslt_score'
			);
			
		$fields['results'] = array(
			'TITLE'    => 'Results',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The results is a required field',
			'DB_FIELD' => 'rslt_results'
			);
			
		$fields['firstname'] = array(
			'TITLE'    => 'First Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The first name is a required field',
			'DB_FIELD' => 'rslt_firstname'
			);
			
		$fields['lastname'] = array(
			'TITLE'    => 'Last Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The last name is a required field',
			'DB_FIELD' => 'rslt_lastname'
			);
			
		$fields['address1'] = array(
			'TITLE'    => 'Address 1',
			'DB_FIELD' => 'rslt_address1'
			);
			
		$fields['address2'] = array(
			'TITLE'    => 'Address 2',
			'DB_FIELD' => 'rslt_address2'
			);
			
		$fields['city'] = array(
			'TITLE'    => 'City',
			'DB_FIELD' => 'rslt_city'
			);
			
		$fields['state'] = array(
			'TITLE'    => 'State',
			'DB_FIELD' => 'rslt_state',
			'DEFAULT'  => 'AL'
			);
			
		$fields['zipcode'] = array(
			'TITLE'    => 'Zipcode',
			'DB_FIELD' => 'rslt_zipcode'
			);
			
		$fields['county'] = array(
			'TITLE'    => 'County',
			'DB_FIELD' => 'rslt_county'
			);
			
		$fields['region'] = array(
			'TITLE'    => 'Region',
			'DB_FIELD' => 'rslt_region'
			);
		
		$fields['year'] = array(
			'TITLE'    => 'Year',
			'DB_FIELD' => 'rslt_year'
			);
			
		$fields['system_id'] = array(
			'TITLE'    => 'System ID',
			'DB_FIELD' => 'rslt_system_id'
			);			
						
		return $fields;
	}
	
	public function selectBySportID($options=array())
    {
        $sportid = (isset($options['SPORT_ID'])) ? $options['SPORT_ID'] : null;
        $year    = (isset($options['YEAR'])) ? $options['YEAR'] : null;
        
        if (is_null($sportid))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $pairs['sportid'] = $sportid;
        
        if (!is_null($year))
        {
	        $pairs['year'] = $year;
        }
		
		$order_str = $this->getActualFieldName('year').' DESC,'.$this->getActualFieldName('sport').','.$this->getActualFieldName('event').','.$this->getActualFieldName('results');
		
		$data = $this->selectByPairs($pairs, $order_str);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ; 
    }
    
    public function selectByYear($year=null)
    {
        if (is_null($year))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
		
		$pairs = array(
			'year'=> $year
		);
		
		$order_str = $this->getActualFieldName('year').' DESC,'.$this->getActualFieldName('sport').','.$this->getActualFieldName('event').','.$this->getActualFieldName('results');
		
		$data = $this->selectByPairs($pairs, $order_str);
		
		if ($data)
		{
			$this->record_list = $data;
		}
		
		return (!$data) ? false : $data ;
    }
	
}
?>
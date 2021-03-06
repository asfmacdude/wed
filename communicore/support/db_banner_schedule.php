<?php
/*
 * db_banner_schedule
 *
 * Database object for the online database banner_schedule
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_banner_schedule extends db_common
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
		$this->options['TABLE_NAME']     = 'banner_schedule';
		$this->options['TABLE_ID_FIELD'] = 'bsch_id';
		
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
		$fields     = array();
		$today_date = wed_getDateToday();
		
		$fields['id'] = array(
			'LABEL'     => 'ID',
			'DB_FIELD'  => 'bsch_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'bsch_modified',
			'NO_UPDATE' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['bannerid'] = array(
			'LABEL'     => 'Banner ID',
			'DB_FIELD'  => 'bsch_banner_id',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['categoryid'] = array(
			'LABEL'     => 'Category ID',
			'DB_FIELD'  => 'bsch_category_id',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['active'] = array(
			'LABEL'    => 'Active',
			'DB_FIELD' => 'bsch_active',
			'DEFAULT'  => 'Y',
			'LIST_SELECT' => array('Y','N'),
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['start'] = array(
			'LABEL'    => 'Start Date',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The start date is a required field',
			'DB_FIELD' => 'bsch_start_date',
			'DEFAULT'  => $today_date,
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['end'] = array(
			'LABEL'    => 'End Date',
			'DB_FIELD' => 'bsch_end_date',
			'DEFAULT'  => null,
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'bsch_details',
			'INSTRUCT' => 'Details are various options for this schedule. Example:  LINK| apple.com;',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
			
		return $fields;
	}
	
	public function selectByIDandDate($id,$date=null)
	{
		$date = (is_null($date)) ? wed_getDateTodaySQL() : $date;
		
		$sql  = 'SELECT * FROM ' . $this->options['TABLE_NAME'] . ' WHERE ' . $this->options['FIELDS']['categoryid']['DB_FIELD'] . '=' . $id . ' AND ' .
			$this->options['FIELDS']['active']['DB_FIELD'] . ' = "Y" AND ' .
			$this->options['FIELDS']['start']['DB_FIELD'] . ' <= "' . $date . '" AND ' . $this->options['FIELDS']['end']['DB_FIELD'] . ' > "' . $date . '"';
			
		$data = $this->dbAll($sql);
		return (!$data) ? false : $data ;
	}
	
	public function selectByCategory($code=null)
    {
        if (is_null($code))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];	
		$pairs     = array( 'category'=> $code );	
		$order_str = $this->options['FIELDS']['category']['DB_FIELD'].','.$this->options['FIELDS']['title']['DB_FIELD'];
		
		return $this->selectByPairs($pairs, $order_str); 
    }
    
    public function selectByGallery($code=null)
    {
        if (is_null($code))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
		$pairs     = array( 'gallery'=> $code );
		$order_str = $this->options['FIELDS']['gallery']['DB_FIELD'].','.$this->options['FIELDS']['title']['DB_FIELD'];
		
		return $this->selectByPairs($pairs, $order_str); 
    }
    
    // *******************************************************************
    // ********  XCRUD Section *******************************************
    // *******************************************************************
    
    // *******************************************************************
    // ********  setupXCrud initial setup of XCrud Object ****************
    // *******************************************************************
    public function setupXCrud($code=null)
    {
	    // Based on the code, we can present different views of the content_main
	    // table with different settings.
	    if ($code=='banner_schedules')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME'],$this->options['TABLE_DISPLAY']);
		    $xcrud->configFields($this->setFields(false));
 
		    $local_relations = $this->getXCrudRelations();
		    
		    foreach ($local_relations as $key=>$data)
		    {
			    $xcrud->setRelation($data);
		    }
		    
		    return $xcrud->renderXCrud();
	    }
    }
    
    public function getXCrudRelations()
    {
	    $relations[] = array(
	    	'RELATE_FROM'   => 'bsch_category_id', 
	    	'RELATE_TABLE'  => 'presentation_setups', 
	    	'RELATE_TO'     => 'pset_id', 
	    	'DISPLAY_FIELD' => 'pset_title');
	    return $relations;
    }
	
}
?>
<?php
/*
 * db_web_stats
 *
 * Database object for the online database web_stats
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_web_stats extends db_common
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
		$this->options['TABLE_NAME']     = 'web_stats';
		$this->options['TABLE_ID_FIELD'] = 'day';
		
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
		
		$fields['month'] = array(
			'TITLE'     => 'Month',
			'DB_FIELD'  => 'month'
			);
		
		$fields['year'] = array(
			'TITLE'     => 'Year',
			'DB_FIELD'  => 'year'
			);
		
		$fields['day'] = array(
			'TITLE'    => 'Day',
			'DB_FIELD' => 'day'
			);
			
		$fields['hits'] = array(
			'TITLE'    => 'Hits',
			'DB_FIELD' => 'hits'
			);
			
		$fields['files'] = array(
			'TITLE'    => 'Files',
			'DB_FIELD' => 'files'
			);
			
		$fields['pages'] = array(
			'TITLE'    => 'Pages',
			'DB_FIELD' => 'pages'
			);
			
		$fields['visits'] = array(
			'TITLE'    => 'Visits',
			'DB_FIELD' => 'visits'
			);
			
		$fields['sites'] = array(
			'TITLE'    => 'Sites',
			'DB_FIELD' => 'sites'
			);
			
		$fields['kbytes'] = array(
			'TITLE'    => 'Kbytes',
			'DB_FIELD' => 'kbytes'
			);
		
		return $fields;
	}
	
	public function loadAllForTable($order=null)
	{
		$data = $this->selectAllTable($order);	
		return (!$data) ? false : $data ;
	}
	
	public function selectByYear($year=null)
    {
        if (is_null($year))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['year']['DB_FIELD'].'="'.$year.'"';
        $order_str  = ' ORDER BY year,month,day';

        $sql = 'SELECT * FROM '.$table.$where_str.$order_str;
        
        return $this->dbAll($sql);
    }
    
    public function selectByYearMonth($year=null,$month=null)
    {
        if (is_null($year))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['year']['DB_FIELD'].'='.$year;
        $where_str .= ' AND '.$this->options['FIELDS']['month']['DB_FIELD'].'='.$month;
        $order_str  = ' ORDER BY year,month,day';

        $sql = 'SELECT * FROM '.$table.$where_str.$order_str;
        
        return $this->dbAll($sql);
    }
    
    public function selectByYearMonthDay($year=null,$month=null,$day=null)
    {
        if (is_null($year))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
        
        $where_str  = ' WHERE ';
        $where_str .= $this->options['FIELDS']['year']['DB_FIELD'].'="'.$year.'"';
        $where_str .= ' AND '.$this->options['FIELDS']['month']['DB_FIELD'].'="'.$month.'"';
        $where_str .= ' AND '.$this->options['FIELDS']['day']['DB_FIELD'].'="'.$day.'"';
        $order_str  = ' ORDER BY year,month,day';

        $sql = 'SELECT * FROM '.$table.$where_str.$order_str;
        
        return $this->dbAll($sql);
    }
	
}
?>
<?php
/*
 * db_content_main
 *
 * Database object for the online database content_main
 *
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_content_main extends db_common
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
		$this->options['TABLE_NAME']     = 'content_main';
		$this->options['TABLE_ID_FIELD'] = 'cnt_id';
		
		$this->options['FIELDS']         = $this->setFields();
		$this->options['TYPE_LIST']      = $this->loadTypeList();
		$this->addOptions($options);
	}
	
	private function loadTypeList()
	{
		$list_db = wed_getDBObject('content_types');
		return $list_db->options['TYPE_LIST'];
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
			'DB_FIELD'  => 'cnt_id',
			'NO_UPDATE' => 1
			);
		
		$fields['modified'] = array(
			'LABEL'     => 'Modified',
			'DB_FIELD'  => 'cnt_modified',
			'NO_UPDATE' => 1,
			'SHOW_FIELD' => 1
			);
		
		$fields['title'] = array(
			'LABEL'    => 'Title',
			'VALIDATE' => 'Required',
			'MESSAGE'  => 'The title is a required field',
			'DB_FIELD' => 'cnt_title',
			'INSTRUCT' => 'This is the unique name to identify this article.',
			'DEFAULT'  => 'Enter a title',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['typeid'] = array(
			'LABEL'    => 'Type',
			'DB_FIELD' => 'cnt_type_id',
			'INSTRUCT' => 'Select the type of content.',
			'DEFAULT'  => 1,
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['code'] = array(
			'LABEL'    => 'Content Code',
			'VALIDATE' => 'Required',
			'MESSAGE'  => 'The code is a required field',
			'DB_FIELD' => 'cnt_code',
			'INSTRUCT' => 'This is the unique code that the system uses to index this article.',
			'DEFAULT'  => 'Enter a code',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
		
		$fields['order'] = array(
			'LABEL'    => 'Content Order',
			'DB_FIELD' => 'cnt_content_order',
			'INSTRUCT' => 'This is used to sort certain grouped content records.',
			'DEFAULT'  => 'zzzz',
			'SHOW_FIELD'  => 1
			);
			
		$fields['status'] = array(
			'LABEL'    => 'Status',
			'DB_FIELD' => 'cnt_status',
			'INSTRUCT' => 'Publish means your content can be viewed online. Hold or Draft will not be shown online.',
			'DEFAULT'  => 'Publish',
			'LIST_SELECT' => array('Publish','Hold','Draft'),
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['details'] = array(
			'LABEL'    => 'Details',
			'DB_FIELD' => 'cnt_details',
			'INSTRUCT' => 'Details are various options for this article. Example:  AUTHOR| William Shakespeare;',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
			
		$fields['excerpt'] = array(
			'LABEL'    => 'Excerpt',
			'DB_FIELD' => 'cnt_excerpt',
			'INSTRUCT' => 'The excerpt should only be about 150 characters of less and should give a clear introduction of the actual content.',
			'SHOW_COLUMN' => 1,
			'SHOW_FIELD'  => 1
			);
			
		$fields['fullarticle'] = array(
			'LABEL'    => 'Full Article',
			'DB_FIELD' => 'cnt_content',
			'NO_TABLE' => true,
			'INSTRUCT' => 'Use the WYSIWYG Editor to format and style the article to your taste.',
			'SHOW_FIELD'  => 1
			);
			
		$fields['published'] = array(
			'LABEL'    => 'Publish Date',
			'DB_FIELD' => 'cnt_published_timestamp',
			'INSTRUCT' => 'The timestamp when the article was published.',
			'DEFAULT'  => $this->getDateToday(),
			'SHOW_FIELD'  => 1
			);
			
		$fields['author'] = array(
			'LABEL'    => 'Author',
			'DB_FIELD' => 'cnt_author',
			'INSTRUCT' => 'The author of the article.',
			'DEFAULT'  => $this->getDateToday(),
			'SHOW_FIELD'  => 1
			);
			
		$fields['tag'] = array(
			'LABEL'    => 'Related Tags',
			'DB_FIELD' => 'cnt_related_tags',
			'INSTRUCT' => 'Select one or more related tags for this article.(optional)',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
			
		$fields['keywords'] = array(
			'LABEL'    => 'Keywords',
			'DB_FIELD' => 'cnt_search_keywords',
			'INSTRUCT' => 'Use keywords separated with commas to be used in searches.',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
			
		$fields['syskeywords'] = array(
			'LABEL'    => 'System Keywords',
			'DB_FIELD' => 'cnt_system_keywords',
			'INSTRUCT' => 'Use keywords separated with commas to be used by the system.',
			'SHOW_FIELD'  => 1,
			'NO_EDITOR'   => 1
			);
		
		return $fields;
	}
	
	public function loadArticle($code=null)
	{
		$data = $this->selectByCode($code);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let showdirector know
	}
	
	public function loadArticleID($id=null)
	{
		$data = $this->selectByID($id);
		$this->addValues_Data($data);	
		return (!$data) ? false : true ; // let showdirector know
	}
	
	public function loadAllForTable($order=null)
	{
		$data = $this->selectAllTable($order);	
		return (!$data) ? false : $data ;
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
    
    public function selectByTagID($options)
    {
		$tag = (isset($options['TAG'])) ? $options['TAG'] : null;
		$id  = (isset($options['ID'])) ? $options['ID'] : null;
		
		$table     = $this->options['TABLE_NAME'];
		$where_str = ' WHERE ';
		
		if (!is_null($id))
		{
			$where_str .= $this->options['FIELDS']['typeid']['DB_FIELD'].' = "'.$id.'" AND ';
		}
		
		$where_str .= $this->options['FIELDS']['syskeywords']['DB_FIELD'].' LIKE "%'.$tag.'%"';
		
		$order_str = ' ORDER BY '.$this->options['FIELDS']['title']['DB_FIELD'];

		$sql = 'SELECT * FROM '.$table.$where_str.$order_str;
			
		$data = $this->dbAll($sql);
		
		if ($data)
		{
			$this->record_list = $data;
		}
			
		return (!$data) ? false : $data ;
    }
    
    public function selectByCodePrefix($options)
    {
		$code  = (isset($options['CODE'])) ? $options['CODE'] : null;
		$id    = (isset($options['ID'])) ? $options['ID'] : null;
		$keys  = (isset($options['KEYS'])) ? $options['KEYS'] : null;
		$order = (isset($options['ORDER'])) ? $options['ORDER'] : 'title';
		
		$table     = $this->options['TABLE_NAME'];
		$where_str = ' WHERE ';
		$data      = array();
		
		if (!is_null($id))
		{
			$where_str .= $this->options['FIELDS']['typeid']['DB_FIELD'].' = ":id" AND ';
			$data['typeid'] = $id;
		}
		
		if (!is_null($keys))
		{
			// $keys should be an array
			if (is_array($keys))
			{
				foreach ($key as $keyword)
				{
					$where_str .= $this->options['FIELDS']['syskeywords']['DB_FIELD'].' LIKE "%'.$keyword.'%" AND ';
				}
			}
		}
		
		$where_str .= $this->options['FIELDS']['code']['DB_FIELD'].' LIKE "'.$code.'%"';
		// $data['code'] = $code.'%';
		
		$order_str = ' ORDER BY '.$this->options['FIELDS'][$order]['DB_FIELD'];

		$sql = 'SELECT * FROM '.$table.$where_str.$order_str;
		
		$data  = $this->dbExecute($sql,$data,$all=true);
		
		if ($data)
		{
			$this->record_list = $data;
		}
			
		return (!$data) ? false : $data ;
    }
    
    public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
    
    public function searchArticlesByKeywords($content=null)
    {
	    if (!is_null($content))
	    {
		    $content = trim($content);
		    
		    // split $content by spaces
		    $search_words  = explode(' ', $content);
		    $searches = array();
		    
		    foreach ($search_words as $item)
		    {
			    $item = wed_cleanItUp($item,'SEARCH');
			    $searches[] = $this->options['FIELDS']['keywords']['DB_FIELD'].' LIKE "%'.$item.'%"';
		    }
		    
		    $table     = $this->options['TABLE_NAME'];
			$where_str = ' WHERE ';
			$where_str .= implode(' OR ', $searches);
			$order_str = ' ORDER BY '.$this->options['FIELDS']['title']['DB_FIELD'];

			$sql = 'SELECT * FROM '.$table.$where_str.$order_str;
			
			$data = $this->dbAll($sql);
			
			if ($data)
			{
				$this->record_list = $data;
			}
			
			return (!$data) ? false : $data ; 
	    }
    }
    
    public function searchArticlesByContentType($type=null)
    {
	    if (!is_null($type))
	    {
		    $types_db        = wed_getDBObject('content_types');
		    $type_id         = $types_db->getTypeID($type);
			$pairs['typeid'] = $type_id;
			
			if ( (strtoupper($type)==='NEWS') || (strtoupper($type)==='POST') )
			{
				$order_str = $this->options['FIELDS']['published']['DB_FIELD'] . ' DESC';
			}
			else
			{
				$order_str = $this->options['FIELDS']['title']['DB_FIELD'];
			}
		
			$data = $this->selectByPairs($pairs, $order_str);
			
			if ($data)
			{
				$this->record_list = $data;
			}
			
			return (!$data) ? false : $data ; 
	    }
    }
    
    // See wed-retired functions for the runSearch function that was here
    
    // *******************************************************************
    // *****  getTITLE produces the title for this record ***
    // *******************************************************************
    public function getTITLE()
    {
		return $this->getValue('title');
    }
    
    // *******************************************************************
    // *****  getTAB_HEADER produces the tab header for this record ***
    // *******************************************************************
    public function getTAB_HEADER()
    {
		return $this->getDetail('TAB_HEADER',$this->getValue('title'));
    }
    
    // *******************************************************************
    // *****  getFULLARTICLE produces the full content for this record ***
    // *******************************************************************
    public function getFULLARTICLE()
    {
		return $this->getValue('fullarticle');
    }
    
    // *******************************************************************
    // ********  getDescription produces a description for this record ***
    // *******************************************************************
    public function getDESCRIPTION()
    {
	    $excerpt      = $this->getValue('excerpt');
		// $fullarticle  = $this->getValue('fullarticle');
		// $description  = (!empty($excerpt)) ? $excerpt : substr($fullarticle, 0, 150);
		// $description  = strip_tags($description);
		return $excerpt; // $description;
    }
    
	// See wed-retired functions for the getLINK method that was originally here
        
    // *******************************************************************
    // ********  getTags produces a set of tags for this record **********
    // *******************************************************************
    public function getTAGS()
    {
	    $keywords = $this->getValue('keywords');
	    return explode(',', $keywords); // return an array
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
	    if ($code=='content')
	    {
		    $xcrud = new db_xcrud_tools();
		    $xcrud->initXCrud();
		    $xcrud->setTable($this->options['TABLE_NAME']);
		    $xcrud->configFields($this->setFields(false));
		    
		    $local_relations = $this->getXCrudRelations();
		    
		    foreach ($local_relations as $key=>$data)
		    {
			    $xcrud->setRelation($data);
		    }
		    
		    // Try Nested Table
		    $nest_name1 = 'content_connect';
		    $db_object  = wed_getDBObject($nest_name1);
		    
		    $nest1_options = array(
		    	'OBJECT_NAME'     => $nest_name1,
		    	'CONNECTION_NAME' => 'Content Group Connections',
		    	'RELATE_FROM'     => 'cnt_id',
		    	'RELATE_TABLE'    => $nest_name1,
		    	'RELATE_TO'       => 'cnn_content_id'
		    );
			
			$xcrud->createNestedTable($nest1_options);
			$xcrud->configFields($db_object->setFields(false),$nest_name1);
			
			$nest1_relations = $db_object->getXCrudRelations();
			
			foreach ($nest1_relations as $key=>$data)
			{
				$data['OBJECT_NAME'] = $nest_name1;
				$xcrud->setRelation($data);
			}
		    
		    return $xcrud->renderXCrud();

	    }
    }
    
    public function getXCrudRelations()
    {
	    $relations[] = array(
	    	'RELATE_FROM'   => 'cnt_type_id', 
	    	'RELATE_TABLE'  => 'content_types', 
	    	'RELATE_TO'     => 'ctp_id', 
	    	'DISPLAY_FIELD' => 'ctp_title');
	    return $relations;
    }
}
?>
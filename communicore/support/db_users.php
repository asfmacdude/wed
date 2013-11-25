<?php
/*
 * db_users
 *
 * Database object for the online database users as part of the 
 * new login/membership software
 *
 */
defined( '_GOOFY' ) or die();

include_once('db_common.php');

class db_users extends db_common
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
		$this->options['TABLE_NAME']     = 'users';
		$this->options['TABLE_ID_FIELD'] = 'UserName';
		
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
			'DB_FIELD'  => 'UserId',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
		
		$fields['lastactivity'] = array(
			'TITLE'     => 'Last Activity',
			'DB_FIELD'  => 'LastActivityDate',
			'NO_UPDATE' => 1
			);
			
		$fields['created'] = array(
			'TITLE'     => 'Created',
			'DB_FIELD'  => 'CreateDate',
			'NO_UPDATE' => 1
			);
		
		$fields['name'] = array(
			'TITLE'    => 'User Name',
			'VALIDATE' => 'isRequired',
			'MESSAGE'  => 'The user name is a required field',
			'DB_FIELD' => 'UserName',
			'DEFAULT'  => 'Guest'
			);
			
		$fields['password'] = array(
			'TITLE'    => 'User Password',
			'DB_FIELD' => 'Password',
			'NO_UPDATE' => 1
			);		
			
		$fields['approved'] = array(
			'TITLE'    => 'Approved',
			'DB_FIELD' => 'IsApproved',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
			
		$fields['lockedout'] = array(
			'TITLE'    => 'Locked Out',
			'DB_FIELD' => 'IsLockedOut',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
			
		$fields['loggedin'] = array(
			'TITLE'    => 'Logged In',
			'DB_FIELD' => 'IsLoggedIn',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
		
		$fields['developer'] = array(
			'TITLE'    => 'Developer',
			'DB_FIELD' => 'IsDeveloper',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
		
		$fields['autologin'] = array(
			'TITLE'    => 'Auto Login',
			'DB_FIELD' => 'AutoLogin',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
			
		$fields['sessionid'] = array(
			'TITLE'    => 'Session ID',
			'DB_FIELD' => 'SessionId',
			'NO_UPDATE' => 1
			);
			
		$fields['lastlogindate'] = array(
			'TITLE'    => 'Last Login Date',
			'DB_FIELD' => 'LastLoginDate',
			'NO_UPDATE' => 1
			);
			
		$fields['lastloginip'] = array(
			'TITLE'    => 'Last Login IP',
			'DB_FIELD' => 'LastLoginIP',
			'NO_UPDATE' => 1
			);
			
		$fields['isowner'] = array(
			'TITLE'    => 'Is Owner',
			'DB_FIELD' => 'IsOwner',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
			
		$fields['ispremium'] = array(
			'TITLE'    => 'Is Premium',
			'DB_FIELD' => 'IsPremium',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
			
		$fields['premiumlevel'] = array(
			'TITLE'    => 'Premium Level',
			'DB_FIELD' => 'PremiumLevel',
			'NO_UPDATE' => 1,
			'DEFAULT'   => 0
			);
			
		$fields['email'] = array(
			'TITLE'    => 'Email Address',
			'VALIDATE' => array('isEmail','isRequired'),
			'MESSAGE'  => array('The email address is not valid','The email address is a required field'),
			'DB_FIELD' => 'Email'
			);			
						
		return $fields;
	}
    
    public function checkForUser($name=null)
    {
	    // Add the default values here first so that Guestdirector
	    // will have a user object with the default values
	    $this->addValues_Default();
	    
	    if (is_null($name))
        {
            return false;
        }
        
        $data = $this->selectByUsername($name);
        
        if ($data)
        {
	        $this->addValues_Data($data);
        }

        return (!$data) ? false : true ;
    }
    
    
    public function selectByUserName($name=null)
    {
        if (is_null($name))
        {
            return null;
        }
        
        $table     = $this->options['TABLE_NAME'];
		
		$pairs = array(
			'name'=> $name
		);
		
		return $this->selectByPairs($pairs,null,false); 
    }
    
    public function setUserLoggedIn()
    {
	    $_SESSION['userid']   = $this->getValue('id');
		$_SESSION['username'] = $this->getValue('name');
		$_SESSION['password'] = $this->getValue('password');
			
		setcookie("id", $this->getValue('id'), strtotime( '+30 days' ), "/", "", "", TRUE);
		setcookie("user", $this->getValue('name'), strtotime( '+30 days' ), "/", "", "", TRUE);
		setcookie("pass", $this->getValue('password'), strtotime( '+30 days' ), "/", "", "", TRUE);
    }
    
    public function logUserOut()
    {
	    if (isset($_SESSION['username']))
	    {
		    // Load user into object
		    $this->checkForUser($_SESSION['username']);
		    $id     = $this->getValue('id');
			$sql    = 'UPDATE users SET IsLoggedin=0 WHERE Userid='.$id;
			$result = $this->dbQuery($sql);
			
			if ($this->getValue('autologin')==0)
		    {
			    setcookie('id', null, time() - 3600);
				setcookie('user', null, time() - 3600);
				setcookie('pass', null, time() - 3600);
		    }
	    }
	    
		// delete sessions
		session_destroy();
		header('Location: /');	
    }
    
    public function updateUserIP()
    {
	    $id     = $this->getValue('id');
	    $sql    = 'UPDATE users SET IsLoggedin=1,LastLoginIP="'.$_SERVER['REMOTE_ADDR'].'",LastLoginDate="'.$this->getDateToday().'" WHERE Userid='.$id;
	    dbug($sql);
		$result = $this->dbQuery($sql);
		dbug($result);
    }
    
    public function save_action_encrypt_password($value)
    {
    	return md5($value);
    }
	
}
?>
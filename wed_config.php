<?php

// Start Output buffering
ob_start();

/*
 * Load Necessary Config Files
 */
$config_files = array(
	'wed_error.php',
	'wed_debug.php',
	'wed_messages.php',
	'wed_mobileDetect.php'
);

foreach ($config_files as $file)
{
	if (wed_checkFileExist(WED_BASE . $file,'Configuration files are missing.'))
	{
		include_once($file);
	}
}

// This makes sure your web site reflects our time zone
date_default_timezone_set('America/Chicago');

// Define some simple formatting constants
!defined('TAB1')  ? define("TAB1","\t") : null;
!defined('TAB2')  ? define("TAB2","\t\t") : null;
!defined('TAB3')  ? define("TAB3","\t\t\t") : null;
!defined('TAB4')  ? define("TAB4","\t\t\t\t") : null;
!defined('TAB5')  ? define("TAB5","\t\t\t\t\t") : null;
!defined('TAB6')  ? define("TAB6","\t\t\t\t\t\t") : null;
!defined('TAB7')  ? define("TAB7","\t\t\t\t\t\t\t") : null;
!defined('TAB8')  ? define("TAB8","\t\t\t\t\t\t\t\t") : null;
!defined('LINE1') ? define("LINE1","\n") : null;
!defined('LINE2') ? define("LINE2","\n\n") : null;
!defined('LINE3') ? define("LINE3","\n\n\n") : null;

// Define Mobile and iPad
$MobileDetect = new MobileDetect();
$devices      = array('Mobile','Ipad');
$the_device   = null;

foreach ($devices as $gadget)
{
	$method = 'Is'.$gadget;
	$the_device = (call_user_func(array($MobileDetect,$method))) ? strtolower($gadget) : $the_device;
}

!defined('DEVICE')      ? define('DEVICE', $the_device) : false;

// Define numerous paths for important directories such as /sites, /themes, etc.
!defined('SITE_BASE')   ? define('SITE_BASE', SITE_ROOT . 'sites' .DS )   : null;
!defined('SITE_DIR')    ? define('SITE_DIR', SITE_BASE . SITE_DOMAIN .DS ) : null;
!defined('SITE_WEB')    ? define('SITE_WEB', DS . 'sites' .DS . SITE_DOMAIN . DS ) : null;

!defined('WED_WALT')    ? define('WED_WALT', WED_BASE . 'walt.php')       : null;
!defined('SETUP_FILE')  ? define('SETUP_FILE', SITE_BASE . '.private'.DS.'system_setup.php') : null;
!defined('CONFIG_FILE') ? define('CONFIG_FILE', SITE_BASE . '.private'.DS.'config.php') : null;

!defined('WEB_BASE')    ? define('WEB_BASE', DS ) : null ;
!defined('THEME_BASE')  ? define('THEME_BASE', SITE_ROOT . 'themes' . DS ) : null ;
!defined('THEME_BASE_WEB') ? define('THEME_BASE_WEB', WEB_BASE . 'themes' . DS ) : null ;
!defined('COMPONENT_BASE')  ? define('COMPONENT_BASE', SITE_ROOT . 'components' . DS ) : null ;
!defined('COMPONENT_BASE_WEB') ? define('COMPONENT_BASE_WEB', WEB_BASE . 'components' . DS ) : null ;
!defined('LIBRARY_BASE')  ? define('LIBRARY_BASE', SITE_ROOT . 'library' . DS ) : null ;
!defined('LIBRARY_BASE_WEB') ? define('LIBRARY_BASE_WEB', WEB_BASE . 'library' . DS ) : null ;
!defined('FILE_BASE')  ? define('FILE_BASE', SITE_ROOT . 'files' . DS ) : null ;
!defined('FILE_BASE_WEB') ? define('FILE_BASE_WEB', WEB_BASE . 'files' . DS ) : null ;

// Capture database settings
$db_settings = include_once(SETUP_FILE);

/*
 * Site Configuration
 *
 * This next section loads the site information from the sites table so that we can
 * set any configuration before call a SESSION_START.
 *
 */
$site_key = new site_info($db_settings,SITE_DOMAIN);
$cookie_domain = $site_key->getDetail('COOKIE_DOMAIN','.'.SITE_DOMAIN);
ini_set('session.cookie_domain', $cookie_domain);

// Start the SESSION
session_start();

/*
 * Walt
 *
 * Here's where we require the walt.php file which is the base class
 * for this entire application.
 *
 */
if (wed_checkFileExist(WED_WALT,'System Error: no dream file|'))
{
	require_once (WED_WALT); // Load Walt
}

/*
 * wed_checkFileExists()
 *
 * Simple function to check that a file exists and issue an error if it does
 * not exist.
 *
 */
function wed_checkFileExist($file,$err_message,$err_type=E_USER_ERROR)
{
	$status = (file_exists($file));
	
	if (!$status)
	{
		$err_message = $err_message;
		trigger_error($err_message, $err_type);
	}
	
	return $status;
}

/*
 * site_info class
 *
 * This class allows access to the sites database before we get into the main core of
 * the WED application. Here we can check the site for additional setups and configurations
 * before we instantiate Walt and get the main app running.
 */
class site_info
{
	protected $db;
	private $settings;
	public $site;
	private $connect;
	
	public function __construct($db,$site)
	{
		$this->site = $site;
		$this->db   = $db;
		$this->settings = array();
		$this->getSiteKey();
	}
	
	private function getSiteKey()
	{	
		$query = 'SELECT * FROM sites WHERE site_name="'.$this->site.'"';
		$row   = $this->dbRow($query);
		$row   = $this->stripslashes_deep($row);
		
		$this->settings = $row;
	}
	
	public function __get($name)
	{
		return (isset($this->settings[$name])) ? $this->settings[$name] : false ;
	}
	
	public function getValue($name,$default=null)
	{
		return (isset($this->settings[$name])) ? $this->settings[$name] : $default ;
	}
	
	public function getDetail($detail,$default=null)
    {
	    $detail_field = $this->getValue('details');
	    $detail_array = $this->wed_getOptionsFromString($detail_field);
	    
	    return (isset($detail_array[$detail])) ? $detail_array[$detail] : $default;
    }
    
    private function wed_getOptionsFromString($string,$line_sep=';',$option_sep='|')
	{
		// Example String
		// PAGE_TITLE: The Official Web Site of the ASF Foundation;
		// COLOR: crimson_red;
		$string  = trim($string);
		$string  = (substr($string, -1)===$line_sep) ? substr($string, 0, -1) : $string ;
		
		$options = array();
		$lines   = explode($line_sep, $string);
		
		foreach ($lines as $value)
		{
			$opt_break = explode($option_sep, $value);
			$key       = (isset($opt_break [0])) ? trim($opt_break [0]) : 'SILLY' ;
			$val       = (isset($opt_break [1])) ? trim($opt_break [1]) : 'ERROR' ;
			$options[$key] = $val;
		}
		
		return $options;
	}
	
	private function dbInit()
	{
		if (!$this->connect)
		{
			$this->connect = new PDO('mysql:host='.$this->db['HOSTNAME'].';dbname='.$this->db['DB_NAME'],$this->db['USERNAME'],$this->db['PASSWORD']);
			$this->connect->query('SET NAMES utf8');
			$this->connect->num_queries=0;
		}
	}
	
	private function dbQuery($query)
	{
		$this->dbInit();	
		$q = $this->connect->query($query);
		$this->connect->num_queries++;
		return $q;
	}
	
	private function dbRow($query) 
	{
		$q = $this->dbQuery($query);
		return $q->fetch(PDO::FETCH_ASSOC);
	}
	
	private function stripslashes_deep($value)
	{
	   $value = is_array($value) ? array_map(array($this,'stripslashes_deep'), $value) : stripslashes($value);
	   return $value;
	}

}
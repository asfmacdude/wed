<?php
/*
	The wed_tools file is a global set of functions that can be called from anywhere
	in WED.
	
*/
// *******************************************************************
// ********  PROFESSOR OPTIONS ***************************************
// *******************************************************************
function wed_getSystemValue($name,$default=null)
{
    $options = array();
    $options['NAME']    = $name;
    $options['DEFAULT'] = $default;

    global $walt;
	$prof = $walt->getImagineer('professor');
	return  $prof->askProfessor($options);
}

function wed_addSystemValue($key,$value)
{
    global $walt;
	$prof = $walt->getImagineer('professor');
	$prof->addSetting($key,$value);
}

function wed_changeSystemErrorCode($value)
{
    global $walt;
	$prof = $walt->getImagineer('professor');
	$prof->changeErrorCode($value);
}

function wed_addSystemValueArray($array)
{
    global $walt;
	$prof = $walt->getImagineer('professor');
	$prof->addSettingArray($array);
}

function wed_setTheme($theme_id)
{
    global $walt;
	$prof = $walt->getImagineer('professor');
	$prof->setThemeSetup($theme_id);
}

function wed_getRecentHistory($max=10)
{
	global $walt;
	$prof = $walt->getImagineer('professor');
	return  $prof->getRecentHistory($max);
}

function wed_getRecentSearch($max=10)
{
	global $walt;
	$prof = $walt->getImagineer('professor');
	return  $prof->getRecentSearch($max);
}

// *******************************************************************
// ********  GUEST DIRECTOR OPTIONS **********************************
// *******************************************************************
function wed_loggedIn()
{
	global $walt;
	$guest = $walt->getImagineer('guestdirector');
	return $guest->isLoggedIn();
}
// *******************************************************************
// ********  PARSE URL FUNCTIONS *************************************
// *******************************************************************
function wed_cleanURL($string)
{
    $url = str_replace("'", '', $string);
    $url = str_replace('%20', ' ', $url);
    $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url); // substitutes anything but letters, numbers and '_' with separator
    $url = trim($url, "-");
    $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);  // you may opt for your own custom character map for encoding.
    $url = strtolower($url);
    $url = preg_replace('~[^-a-z0-9_]+~', '', $url); // keep only letters, numbers, '_' and separator
    return $url;
}

function wed_parseURLPath()
{
	$path = array();
	
	if (isset($_SERVER['REQUEST_URI']))
	{
    	$request_path      = explode('?', $_SERVER['REQUEST_URI']);

		$path['BASE']      = rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/');
		$path['CALL_UTF8'] = substr(urldecode($request_path[0]), strlen($path['BASE']) + 1);
		$path['CALL']      = utf8_decode($path['CALL_UTF8']);
    
		if ($path['CALL'] == basename($_SERVER['PHP_SELF']))
		{
			$path['CALL'] = '';
		}
		
		$path['CALL_PARTS'] = explode('/', $path['CALL']);
		
		if (isset($request_path[1]))
		{
			$path['QUERY_UTF8'] = urldecode($request_path[1]);
			$path['QUERY']      = utf8_decode(urldecode($request_path[1]));
			$vars               = explode('&', $path['QUERY']);
		
			foreach ($vars as $var)
			{
				$t = explode('=', $var);
				$path['QUERY_VARS'][$t[0]] = $t[1];
				}
		}
		else
		{
			$path['QUERY_VARS'] = null;
		}
		
	}
	
	return $path;
}

// *******************************************************************
// ********  COMMUNICORE OPERATIONS **********************************
// *******************************************************************
function wed_getDBObject($db_name)
{
	global $walt;
	$db    = $walt->getImagineer('communicore');
	return $db->loadDBObject($db_name,'WED_TOOLS');
}

function wed_verifyControlCode($code)
{
	global $walt;
	$prof    = $walt->getImagineer('professor');
	return $prof->verifyControlCode($code);
}

function wed_getPageInfo($code,$bysite=true)
{
	// This function returns an object from Communicore that the calling
	// program can use to access information about the content_control
	// page structure that was passed.
	//
	// $bysite simply causes the search to be limited to the current site,
	// in other words , only find page codes within the current site.
	global $walt;
	$db    = $walt->getImagineer('communicore');
	$pages = $db->loadDBObject('content_control','WED_TOOLS');
	
	if ($pages->loadPage($code,$bysite))
	{
		return $pages;
	}
	else
	{
		return false;
	}
}

function wed_getContentInfo($code)
{
	// This function returns an object from Communicore that the calling
	// program can use to access information about the content_main
	// article that was passed.
	global $walt;
	$db      = $walt->getImagineer('communicore');
	$content = $db->loadDBObject('content_main','WED_TOOLS');
	
	if ($content->loadArticle($code))
	{
		return $content;
	}
	else
	{
		return false;
	}
}

// *******************************************************************
// ********  FILE_SORCERER OPTIONS ***********************************
// *******************************************************************
function wed_getImageObject($options=array())
{
	global $walt;
	$file   = $walt->getImagineer('file_sorcerer');
	$options['FILE_TYPE'] = 'image';
	return $file->newFileMagic($options);
}

function wed_getDocumentObject($options=array())
{
	global $walt;
	$file   = $walt->getImagineer('file_sorcerer');
	$options['FILE_TYPE'] = 'document';
	return $file->newFileMagic($options);
}

function wed_getCurlObject($options=array())
{
	global $walt;
	$file   = $walt->getImagineer('file_sorcerer');
	$options['FILE_TYPE'] = 'curl';
	return $file->newFileMagic($options);
}

// *******************************************************************
// ********  get Alternate Paths/Directory ***************************
// *******************************************************************
function wed_getAlternatePath($options=array())
{
	/*
	 * getAlternatePath()
	 *
	 * This function is very important to switching to MOBILE, IPAD or other device
	 * themes. It first gets the DEVICE from the main settings and then checks to see
	 * if the theme has a DEVICE_VERSION for the particular DEVICE. If not it returns
	 * the default path. otherwise, it checks to see if the theme has a path with the
	 * DEVICE name as a suffix. Example: assets_mobile,themes_ipad, etc.
	 *
	 */
	$device      = wed_getSystemValue('DEVICE');
	$check_theme = strtoupper($device . '_VERSION');
	
	// Required options
	$dir_path    = (isset($options['DIR_PATH'])) ? $options['DIR_PATH'] : null;
	$file_name   = (isset($options['FILE_NAME'])) ? $options['FILE_NAME'] : null;
	
	// Optional options
	$file_ext   = (isset($options['FILE_EXT'])) ? $options['FILE_EXT'] : '.php';
	
	$path       = $dir_path . $file_name . $file_ext;
	
	if (wed_getSystemValue($check_theme,false))
	{
		$file = $file_name . '_' . $device . $file_ext; // Example assets_mobile.php
		
		if (file_exists($dir_path . $file))
		{
			$path = $dir_path . $file;
		}
	}
	
	return $path;
}

function wed_getAlternateDirectory($options=array())
{
	/*
	 * getAlternateDirectory()
	 *
	 * This function is very important to switching to MOBILE, IPAD or other device
	 * themes. It first gets the DEVICE from the main settings and then checks to see
	 * if the theme has a DEVICE_VERSION for the particular DEVICE. If not it returns
	 * the default path. otherwise, it checks to see if the theme has a path with the
	 * DEVICE name as a suffix. Example: assets_mobile,themes_ipad, etc.
	 *
	 */
	$device      = wed_getSystemValue('DEVICE');
	$check_theme = strtoupper($device . '_VERSION');
	
	// Required options
	$dir_path  = (isset($options['DIR_PATH'])) ? $options['DIR_PATH'] : null;
	$dir_name  = (isset($options['DIR_NAME'])) ? $options['DIR_NAME'] : null;
	$path      = $dir_path . $dir_name;

	if (wed_getSystemValue($check_theme,false))
	{
		$dir = $dir_name . '_' . $device; // Example templates_mobile
		
		if (is_dir($dir_path . $dir))
		{
			$path = $dir_path . $dir;
		}
	}
	
	return $path;
}
// *******************************************************************
// ********  LIST MANAGER OPTIONS ************************************
// *******************************************************************
function wed_getList($data)
{
	// if $data comes as a string, it will need to be parsed
	if (!is_array($data))
	{
		// do a parse_str and standardize keys
		parse_str($data,$output);
		$data = wed_standardKeys($output);
	}
	
	global $walt;
	$list_dude  = $walt->getImagineer('list_manager');
	$id   	    = $list_dude->newList($data);
	
	// Here we return a list object which is a 'loaded' db object
	return $list_dude->getHTML(array('ID'=>$id));
}
// *******************************************************************
// ********  CSS DIRECTOR OPTIONS ************************************
// *******************************************************************
function wed_getCSS($css_array=array())
{
	global $walt;
	$css = $walt->getImagineer('cssdirector');
	// $css->CSS_ASSETS = $css_array;
	foreach ($css_array as $key=>$css_data)
	{
		$css->addCSSAsset($css_data);
	}
}

function wed_addCSSAsset($css_array=array())
{
	global $walt;
	$css = $walt->getImagineer('cssdirector');
	$css->newDetail($css_array);
}

function wed_loadCSSAssets($css_array=array())
{
	global $walt;
	$css_director  = $walt->getImagineer('cssdirector');
	$css_director->loadCSSAssets($css_array);
}

// *******************************************************************
// ********  JS DIRECTOR OPTIONS *************************************
// *******************************************************************
function wed_getJavascript($js_array=array())
{
	global $walt;
	$js = $walt->getImagineer('jsdirector');
	$js->JS_ASSETS = $js_array;
}
/*
 * wed_loadJavascriptAssets
 *
 * In each Theme Assets file, we define ALL the different javascript asset files
 * we may need for the theme. Not all of them load every time since they may not be needed.
 * To load a certian javascript asset, you name the KEY to the asset in an array and send it here.
 */
function wed_loadJavascriptAssets($js_array=array())
{
	global $walt;
	$js_director  = $walt->getImagineer('jsdirector');
	$js_director->loadJSAssets($js_array);
}

/*
 * wed_addNewJavascriptAsset
 *
 * This function allows you to add a SINGLE javascript asset that is not in the original
 * assets file. This works well for ready code that has to be customized by a particular
 * object such as a banner.
 */
function wed_addNewJavascriptAsset($js_array=array())
{
	global $walt;
	$js_director  = $walt->getImagineer('jsdirector');
	$js_director->addJSAsset($js_array);
}

// *******************************************************************
// ********  PRESENTATION OPTIONS ************************************
// *******************************************************************
function wed_getPresentation($specs)
{
	// These are the basic required items
	$type  = (isset($specs['TYPE'])) ? $specs['TYPE'] : null;
	$name  = (isset($specs['NAME'])) ? $specs['NAME'] : null;
	
	global $walt;
	$pres = $walt->getImagineer('presentations');
	$id   = $pres->newPresentation($specs);
	return $pres->getHTML(array('ID' => $id));
}

// *******************************************************************
// ********  KEYS OPTIONS ********************************************
// *******************************************************************
function wed_getKeysMerge($html)
{
	global $walt;
	$keys = $walt->getImagineer('keys');
	return $keys->getHTML(array('HTML'=>$html,'MERGE'=>true));
}

// *******************************************************************
// ********  SHORTCODE OPTIONS ***************************************
// *******************************************************************
function wed_renderContent($content=null,$pre=false)
{
	global $walt;
	$sc              = $walt->getImagineer('shortcodes');	
	$content         = trim($content);
	$options['HTML'] = $content;
	$options['PRE']  = $pre;
	return $sc->getHTML($options);
}

// *******************************************************************
// ********  TRAFFIC_MANAGER OPTIONS *********************************
// *******************************************************************
function wed_trafficReports()
{
	global $walt;
	$traffic = $walt->getImagineer('traffic_manager');
	$traffic->loadTrafficReport();
}

// *******************************************************************
// ********  Parse URL String and options ****************************
// *******************************************************************

function wed_parseUrl2Options($options=array())
{
	$query_str = $_SERVER['QUERY_STRING'];
	parse_str($query_str, $query_options);

	if (is_array($query_options))
	{
		foreach ($query_options as $key=>$value)
		{
			$query_options[$key] = wed_cleanItUp($value,'URL');
		}
	}
	
	if (is_array($options))
	{
		$query_options = array_merge($query_options, $options);
	}
	
	return wed_standardKeys($query_options);
}

function wed_standardKeys($options=array())
{
	$new_options = array();
	
	if ( (is_array($options)) && (count($options)>0) )
	{
		foreach ($options as $key=>$value)
		{
			$new_options[strtoupper($key)] = $value;
		}
	}
	
	return $new_options;
}

// *******************************************************************
// ********  Parse a String into options array ***********************
// *******************************************************************
function wed_getOptionsFromString($string,$line_sep=';',$option_sep='|')
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
	
// *******************************************************************
// ********  File Tools **********************************************
// *******************************************************************
function wed_includeFile($path)
{		
	if (file_exists($path))
	{
		include_once($path);
		return true;
	}
	else
	{
		return false;
	}
}

function wed_loadFolderOfFiles($path)
{
	$filelist = array();
	
	// Loads a folder of files
	if (file_exists($path))
	{	
		$iterator = new DirectoryIterator($path);

		foreach ($iterator as $fileinfo)
		{
		    if ( (!$fileinfo->isDir()) && (!$fileinfo->isDot()) )
		    {
		        $filelist[] = $fileinfo->getFilename();
		    }
		}
	}
	
	return $filelist;
}

// *******************************************************************
// ********  Decode JSON *********************************************
// ******************************************************************* 
function wed_decodeJSON($string,$ret_array=true)
{
    if (is_null($string))
    {
	    return null;
    }
    
    $value = json_decode($string,$ret_array);
    $error = null;
    
    switch (json_last_error()) 
    {
		case JSON_ERROR_DEPTH:
        	$error = ' - Maximum stack depth exceeded';
			break;
		case JSON_ERROR_STATE_MISMATCH:
        	$error = ' - Underflow or the modes mismatch';
			break;
		case JSON_ERROR_CTRL_CHAR:
        	$error = ' - Unexpected control character found';
			break;
		case JSON_ERROR_SYNTAX:
        	$error = ' - Syntax error, malformed JSON';
			break;
		case JSON_ERROR_UTF8:
        	$error = ' - Malformed UTF-8 characters, possibly incorrectly encoded';
			break;
	}
	
	if (!is_null($error))
	{
		trigger_error($error, E_USER_NOTICE);
	}
	
	return $value;
}

// *******************************************************************
// ********  String Cleaning Tools ***********************************
// *******************************************************************
function wed_cleanItUp($string=null,$soap=null)
{
    global $walt;
    $clean = $walt->getImagineer('goofy_clean');
    
    return (is_null($soap)) ? $clean->CleanItUp($string) : $clean->CleanItUp($string,$soap);
}

// *******************************************************************
// ********  TimeKeeper Tools ****************************************
// *******************************************************************
function wed_getMomentInTime($options=array())
{
	$start = (isset($options['start_date'])) ? $options['start_date'] : false ;
	$end   = (isset($options['end_date'])) ? $options['end_date'] : false ;
	
	if (!$start && !$end)
	{
		return true;
	}
	else
	{
		$start_date = (!$start) ? new DateTime('now') : new DateTime($start);
		$end_date   = new DateTime($end);
		$moment     = new DateTime('now');
		
		return ( ( ($moment>$start_date) || ($moment==$start_date) ) && ( ($moment==$end_date) || ($moment<$end_date)) );
	}
}

function wed_getDateToday()
{
	$today = new DateTime('NOW');
	return $today->format("m-d-Y");
}

function wed_getDateTodaySQL()
{
	$today = new DateTime('NOW');
	return $today->format("Y-m-d");
}

function wed_getTimeStampToday()
{
	$today = new DateTime('NOW');
	return $today->format("Y-m-d H:i:s");
}

// *******************************************************************
// ********  Logging Tools *******************************************
// *******************************************************************
function wed_logMessage($message=null,$from='UNKNOWN')
{
    if (!is_null($message))
    {
	    messages::addMessage($message,$from);
    }
}

// *******************************************************************
// ********  Formatting Tools ****************************************
// *******************************************************************
function wed_formatClass($class)
{
	if (!is_null($class))
	{
		return 'class="'.$class.'"';
	}
}
	
function wed_formatLink($options=array())
{
	$options['CODE']      = (isset($options['CODE']))      ? $options['CODE']      : null;
	$options['LINK']      = (isset($options['LINK']))      ? $options['LINK']      : null;
	$options['SECURE']    = (isset($options['SECURE']))    ? $options['SECURE']    : false;
	$options['SITE_ONLY'] = (isset($options['SITE_ONLY'])) ? $options['SITE_ONLY'] : false;
	
	global $walt;
	$link_dir = $walt->getImagineer('linkdirector');
	$id       = $link_dir->newLink($options);
	
	return  $link_dir->getHTML(array( 'ID' => $id));
}

function wed_number2Text($number=0)
{
	$numtext = array(
		0   =>  'zero',
		1   =>  'one',
		2   =>  'two',
		3   =>  'three',
		4   =>  'four',
		5   =>  'five',
		6   =>  'six',
		7   =>  'seven',
		8   =>  'eight',
		9   =>  'nine',
		10  =>  'ten',
		11  =>  'eleven',
		12  =>  'twelve',
		13  =>  'thirteen',
		14  =>  'fourteen',
		15  =>  'fifteen',
		16  =>  'sixteen',
		17  =>  'seventeen',
		18  =>  'eighteen',
		19  =>  'nineteen',
		20  =>  'twenty'
		);
	
	return $numtext[$number];
}

// *******************************************************************
// ********  Security ************************************************
// *******************************************************************
function wed_HashThis($hash_this)
{
	$salt = 'h@u!n#z$o%n^i!z*n';
	$result = sha1(sha1($hash_this.$salt));
	return $result;
}

function wed_checkSiteLevels()
{
	$status        = true;
	$site_security = wed_getSystemValue('SECURITY_LEVEL',array());
	
	if (!empty($site_security))
	{
		$status        = false;
		$user_level    = wed_getSystemValue('USER_LEVEL',array());
	
		foreach ($user_level as $key)
		{
			$status = (in_array($key, $site_security)) ? true : $status;
		}
	}
	
	return $status;
}

?>
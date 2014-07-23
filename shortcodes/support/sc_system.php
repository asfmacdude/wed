<?php

/*
 * sc_system.php
 *
 * These are shortcode functions designed for the entire system no matter
 * what theme you are using. All functions have the prefix sys_ to cut down of conflicts
 * between themes. Themes could possibly have the same shortcode and since the theme
 * loads after the system, the them shortcode will 'override' the system shortcode.
 *
 * Precodes
 * Precodes only fire on the content extracted from the control code table. That
 * way you can limit what shortcodes fire at that point.
 *
 * Postcodes
 * Postcodes are shortcode that fire on the remaining content that is generated from
 * the control codes.
 *
 *
 */

$precodes = array(
	'body'         => 'sys_showBodyParts',
	'template'     => 'sys_getTemplate',
	'hide'         => 'sys_hideContent',
	'runtime'      => 'sys_runTime',
	'url_path'     => 'sys_urlPath',
	'system_error' => 'sys_systemError'
);

wed_registerShortcodes($precodes,true);

// *******************************************************************
// *****  Post Code List - Shortcodes that are run last **************
// *******************************************************************
$postcodes = array(
	'template'     => 'sys_getTemplate',
	'presentation' => 'sys_Presentations',
	'article'      => 'sys_getArticle',
	'hide'         => 'sys_hideContent',
	'clear'        => 'sys_divClear',
	'data_table'   => 'sys_getData_Table',
	'runtime'      => 'sys_runTime',
	'tabs_group'   => 'sys_tabGroupPresentation',
	'accordion'    => 'sys_accordionPresentation',
	'accordion_faq' => 'sys_accordionFaqPresentation',
	'gallery'      => 'sys_galleryPresentation',
	'download'     => 'sys_fileDownload',
	'search'       => 'sys_searchManager',
	'system_error' => 'sys_systemError',
	'event_results' => 'sys_eventResults',
	'schedule'      => 'sys_scheduleEvent'
);

$postcodes['banner']  = 'sys_BannerPresentation';
$postcodes['content'] = 'sys_ContentPresentation';
$postcodes['image']   = 'sys_ImagePresentation';
$postcodes['media']   = 'sys_MediaPresentation';


wed_registerShortcodes($postcodes);


// *******************************************************************
// *****  Presentation Shortcodes ************************************
// *******************************************************************
function sys_Presentations($options=array(), $content='')
{
	if (!wed_getMomentInTime($options))
	{
		return null;
	}
	
	// wed_renderContent runs the shortcodes found in the $content
	$options['ACTUAL_CONTENT'] = wed_renderContent($content);

	$present = getImagineer('presentations');
	$id      = $present->newPresentation($options);
	return (!$id) ? null : $present->getHTML(array('ID'=>$id));
}

function sys_BannerPresentation($options=array(), $content='')
{
	$options['type'] = 'banner';
	return sys_Presentations($options, $content);

}

function sys_ContentPresentation($options=array(), $content='')
{
	$options['type'] = 'content';
	return sys_Presentations($options, $content);

}

function sys_ImagePresentation($options=array(), $content='')
{
	$options['type'] = 'image';
	return sys_Presentations($options, $content);

}

function sys_MediaPresentation($options=array(), $content='')
{
	$options['type'] = 'media';
	return sys_Presentations($options, $content);

}

function sys_showBodyParts($options=array(), $content='')
{
	global $walt;
	$prof = $walt->getImagineer('professor');
	$sc   = $walt->getImagineer('shortcodes');
	
	$content = trim($content);
	
	$prof->addSetting('BODY_STRUCTURE',$content);
	$actual_content = $sc->getHTML(array('HTML'=>$content));
	$prof->addSetting('BODY_HTML',$actual_content);
}

function sys_urlPath($options=array(), $content='')
{
	$part = (isset($options['part'])) ? $options['part'] : 'LIST' ;
	
	if ($part===wed_getSystemValue('PAGE_TYPE'))
	{
		return $content;
	}
	else
	{
		return null;
	}
}

function sys_getTemplate($options=array(), $content='')
{
	$name      = (isset($options['name'])) ? $options['name'] : null ;
	$mobile    = (isset($options['mobile'])) ? $options['mobile'] : false ;
	$ipad      = (isset($options['ipad'])) ? $options['ipad'] : false ;
	
	$type      = (isset($options['type'])) ? $options['type'] : null ;
	$startDate = (isset($options['start_date'])) ? $options['start_date'] : false ;
	$endDate   = (isset($options['end_date'])) ? $options['end_date'] : false ;
	$page_type = wed_getSystemValue('PAGE_TYPE');
	
	if (!wed_getMomentInTime($options))
	{
		return null;
	}
	
	if ((!is_null($type)) && $type!=$page_type)
	{
		return null;
	}
	
	$html = '';
	
	$show_ipad   = wed_getSystemValue('IPAD',false);
	$show_mobile = wed_getSystemValue('MOBILE',false);
	
	if ( ($show_ipad) && ($ipad) )
	{
		$name = 'tmpl_' . $ipad;
	}
	elseif ( ($show_mobile) && ($mobile) )
	{
		$name = 'tmpl_' . $mobile;
	}
	else
	{
		$name = 'tmpl_' . $name;
	}
	
	$theme = wed_getSystemValue('THEME');
	
	$options['DIR_PATH'] = THEME_BASE . $theme . DS;
	$options['DIR_NAME'] = 'templates';
		
	$dir_path = wed_getAlternateDirectory($options);
	$path     = $dir_path . DS . $name . '.php';
	
	if (file_exists($path))
	{
		ob_start();
		@include $path;
		$html = ob_get_contents();
		ob_end_clean();
	}
	
	$html = str_replace('%CONTENT%', $content, $html);
	
	return $html;
}

function sys_showElementsContent($options=array(), $content='')
{
	global $walt;
	$prof = $walt->getImagineer('professor');
	$sc   = $walt->getImagineer('shortcodes');
	
	$content = trim($content);
	
	return $content;
	
	$prof->addSetting('CONTENT_STRUCTURE',$content);
	$actual_content = $sc->getHTML(array('HTML'=>$content));
	$prof->addSetting('CONTENT_HTML',$actual_content);
}

function sys_runTime($options=array(), $content='')
{
	if (!wed_getMomentInTime($options))
	{
		return null;
	}
	else
	{
		return $content;
	}
}

	
function sys_getArticle($options=array(), $content='')
{
	$options['type'] = 'content';
	return sys_Presentations($options, $content);
	
	// Deprecate this function soon!
}
		
function sys_getData_Table($options=array(), $content='')
{
	global $walt;
	$js = $walt->getImagineer('jsdirector');
	
	$js_array[] = array(
			'KEY'  => 'JS_FILES',
			'TYPE' => 'FILE',
			'PATH' => 'jquery.dataTables.min.js');
			
	$js_obj = new js_detail($js_array);
	$js->addJSObject($js_obj);
		
	$table = (isset($options['table'])) ? $options['table'] : false ;
	
	if (!isset($options['table']))
	{
		return null;
	}
	
	$acct = $walt->getImagineer('accountant');
	return $acct->getHTML($options);
}

function sys_hideContent($options=array(), $content='')
{
	return '';
}

function sys_divClear($options=array(), $content='')
{
	return '<div style="clear:both;"></div>';
}


// *******************************************************************
// *****  sys_tabGroupPresentation *************************************
// *******************************************************************
function sys_tabGroupPresentation($options=array(), $content='')
{
	// Common Shortcode: [tabs_group setup="tabs_vertical" group="archery_" /]
	$html      = '';
	$options['TYPE']       = (isset($options['type'])) ? $options['type'] : 'tabs_list' ;
	$options['SETUP_CODE'] = (isset($options['setup'])) ? $options['setup'] : 'tabs_vertical' ;
	$options['GROUP']      = (isset($options['group'])) ? $options['group'] : null ;
	
	if (is_null($options['GROUP']))
	{
		$call_parts       = wed_getSystemValue('CALL_PARTS');
		$options['GROUP'] = (isset($call_parts[1])) ? $call_parts[1] . '_' : null;
	}
	
	$data['ORDER']  = (isset($options['order'])) ? $options['order'] : 'order' ;
	$data['TYPE']   = 'content_list';
	$data['SEARCH'] = 'code_prefix';
	$data['CODE']   = $options['GROUP'];

	$options['LIST_OBJECT'] = wed_getList($data);
	
	if (!is_null($options['LIST_OBJECT']))
	{
		global $walt;
		$tab  = $walt->getImagineer('presentations');
		$id   = $tab->newPresentation($options);
		$html = $tab->getHTML(array('ID'=>$id));
	}
	else
	{
		$html = SYS_ERR_NO_INFO;
	}

	return $html;
}

// *******************************************************************
// *****  sys_accordionPresentation ***********************************
// *******************************************************************
function sys_accordionPresentation($options=array(), $content='')
{
	$html      = '';
	$type      = (isset($options['type'])) ? $options['type'] : 'accordion' ;
	$setup     = (isset($options['setup'])) ? $options['setup'] : 'accordion_one' ;
	$heading   = (isset($options['heading'])) ? $options['heading'] : 'General Heading' ;
	$list      = (isset($options['list'])) ? $options['list'] : null ;
	
	$list_object = wed_getList($list);
	
	if (!is_null($list_object))
	{
		$specs['TYPE']        = $type;
		$specs['SETUP_CODE']  = $setup;
		$specs['HEADING']     = $heading;
		$specs['LIST_OBJECT'] = $list_object;

		global $walt;
		$tab  = $walt->getImagineer('presentations');
		$id   = $tab->newPresentation($specs);
		$html = $tab->getHTML(array('ID'=>$id));
	}
	else
	{
		$html = SYS_ERR_NO_INFO;
	}

	return $html;
}


function sys_galleryPresentation($options=array(), $content='')
{
	$html      = '';
	$type      = (isset($options['type'])) ? $options['type'] : 'gallery' ;
	$setup     = (isset($options['setup'])) ? $options['setup'] : 'gallery_simple' ;
	$heading   = (isset($options['heading'])) ? $options['heading'] : 'General Heading' ;
	$category  = (isset($options['category'])) ? $options['category'] : null ;
	$size      = (isset($options['size'])) ? $options['size'] : '1200_500' ;
	$crop_size = (isset($options['crop_size'])) ? $options['crop_size'] : '100_100' ;
	$crop_code = (isset($options['crop_code'])) ? $options['crop_code'] : 0 ;
	$max       = (isset($options['max'])) ? $options['max'] : 0 ;
	$random    = (isset($options['random'])) ? $options['random'] : false ;
	$show_more = (isset($options['show_more'])) ? $options['show_more'] : false ;
	
	$specs['TYPE']             = $type;
	$specs['SETUP_CODE']       = $setup;
	$specs['HEADING']          = $heading;
	$specs['CATEGORY']         = $category;
	$specs['IMAGE_SIZE']       = $size;
	$specs['IMAGE_CROP_SIZE']  = $crop_size;
	$specs['IMAGE_CROP_CODE']  = $crop_code;
	$specs['IMAGE_MAX']        = $max;
	$specs['RANDOMIZE']        = $random;
	$specs['SHOW_MORE']        = $show_more;

	global $walt;
	$tab  = $walt->getImagineer('presentations');
	$id   = $tab->newPresentation($specs);
	$html = $tab->getHTML(array('ID'=>$id));

	return $html;
}

function sys_fileDownload($options=array(), $content='')
{
	$html                = '';
	$options['TITLE']    = (isset($options['title'])) ? $options['title'] : 'Download' ;
	$options['FILE']     = (isset($options['file'])) ? $options['file'] : null ;
	$options['CATEGORY'] = (isset($options['category'])) ? $options['category'] : null ;
	
	if (is_null($options['FILE']))
	{
		return null;
	}
	
	/*
	 * SPECIAL NOTE
	 * Remember to specify the category when the file is located in another directory other than
	 * the category specified in the URL. For example: on the soocer register page, I wanted to download
	 * rules a and rules b which are both found in the general folder 'forms'. Because I did not specify
	 * the category="forms", it looked in the default folder 'soccer' which is added below when there is not a
	 * specified folder. Thus, it found the rules files, not the actual files that I was looking for.
	 */
	
	if (is_null($options['CATEGORY']))
	{
		$call_parts = wed_getSystemValue('CALL_PARTS');
		$options['CATEGORY']   = (isset($call_parts[1])) ? $call_parts[1] : null;
	}
	
	$options['NAME'] = $options['FILE'];
	
	$doc_obj = wed_getDocumentObject($options);
	
	$file_path = $doc_obj->getDocumentFilePath();
	
	// the styling class here is specific to the Kallyas theme. You will need
	// to create a way to add different classes based on the theme or standardize
	// the class names across themes
	return '<p class="register" ><a href="'.$file_path.'"><button class="btn btn-success" type="button">Download</button></a> '.$options['TITLE'].'</p>'.LINE1;
}

// **********************************************************************
// ************** SEARCH FUNCTIONS **************************************
// **********************************************************************
function sys_searchResults($options=array(), $content='')
{
	$html      = '';
	$type      = (isset($options['type'])) ? $options['type'] : 'content' ;
	$search    = (isset($options['search'])) ? $options['search'] : 'KEYWORD' ;
	$style     = (isset($options['style'])) ? $options['style'] : 'standard' ;
	$content   = (isset($_POST['search'])) ? $_POST['search'] : null ;
	$tag       = (isset($_GET['tag'])) ? $_GET['tag'] : null ;

	$display   = (is_null($content)) ? $tag : $content;
	
	$options['TYPE']    = $type;
	$options['SEARCH']  = strtoupper($search);
	$options['STYLE']   = $style;
	$options['CONTENT'] = $display;
	$options['TAG']     = $tag;
	
	global $walt;
	$search  = $walt->getImagineer('search');
	
	$display  = wed_cleanItUp($display,'SEARCH');
	$html    .= '<h3>Searching Results for '.$display.'</h3>'.LINE1;
	
	$id      = $search->newSearch($options);
	$html    .= $search->getHTML(array('ID'=>$id));
	
	return $html;
}

function sys_searchManager($options=array(), $content='')
{
	$theme                   = wed_getSystemValue('THEME','System');
	$specs['THEME']          = (isset($options['theme'])) ? $options['theme'] : $theme ;
	$specs['CODE']           = (isset($options['code'])) ? $options['code'] : $theme.'-search' ;
	$specs['TYPE']           = (isset($options['type'])) ? $options['type'] : 'search' ;
	$specs['CALL']           = (isset($options['call'])) ? $options['call'] : null ;
	$specs['SEARCH_TYPE']    = (isset($options['search_type'])) ? $options['search_type'] : null ;
	$specs['SEARCH_OPTIONS'] = (isset($options['options'])) ? $options['options'] : array() ;
	$specs['HEADING']        = (isset($options['heading'])) ? $options['heading'] : 'Search Results' ;
	
	$search  = getImagineer('search');	
	$id      = $search->newSearch($specs);
		
	return $search->getHTML(array('ID'=>$id));
}

function sys_systemError($options=array(), $content='')
{
	$code = (isset($options['code'])) ? $options['code'] : 'GENERAL ERROR' ;
	wed_changeSystemErrorCode($code);
}

function sys_scheduleEvent($options=array(), $content='')
{
	$options = wed_standardKeys($options);
	
	// Detail Object for running scheduled events
	$options['TYPE'] = 'schedule';
	
	$options['PREFIX'] = (isset($options['PREFIX'])) ? $options['PREFIX'] : null ;
	$options['SUFFIX'] = (isset($options['SUFFIX'])) ? $options['SUFFIX'] : null ;
	$options['PRINT']  = (isset($options['PRINT'])) ? $options['PRINT'] : null ;

	$time   = getImagineer('timemachine');	
	$id     = $time->newSchedule($options);
	$detail = $time->getDetailObject($id);
	
	if ($options['PRINT'] === 'Deadline')
	{
		$content = $detail->printDeadline($options['PREFIX']);
	}
	elseif ($options['PRINT'] === 'Start')
	{
		$content = $detail->printStart($options['PREFIX']);
	}
	elseif ($options['PRINT'] === 'Schedule')
	{
		$content = $detail->printSchedule($options['PREFIX']);
	}
	elseif ($options['PRINT'] === 'Today')
	{
		$content = $detail->printToday($options['PREFIX']);
	}
	elseif ($options['PRINT'] === 'TimeLeft')
	{
		$content = $detail->printTimeLeft($options['PREFIX']);
	}
	
	if ($detail->runSchedule())
	{
		return $content . $options['SUFFIX'];
	}
}

// **********************************************************************
// ************** EVENT MANAGER FUNCTIONS *******************************
// **********************************************************************
function sys_eventResults($options=array(), $content='')
{
	$options = wed_standardKeys($options);
	
	// Detail Object for getting Games Results
	$options['TYPE'] = 'results';
	
	global $walt;
	$event   = $walt->getImagineer('events_manager');	
	$id      = $event->newEvent($options);
	
	$html = $event->getHTML(array('ID'=>$id));
	
	if (!is_null($html))
	{
		$html = $content . $html;
	}
	
	return $html;
}

?>
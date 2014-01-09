<?php

/*
 * sc_system.php
 *
 * These are shortcode functions designed for the entire system no matter
 * what theme you are using.
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
	'body'         => 'sc_showBodyParts',
	'template'     => 'sc_getTemplate',
	'hide'         => 'sc_hideContent',
	'runtime'      => 'sc_runTime',
	'url_path'     => 'sc_urlPath',
	'system_error' => 'sc_systemError'
);

global $walt;
$short = $walt->getImagineer('shortcodes');
$short->add_shortcodes_array($precodes,true);

// *******************************************************************
// *****  Post Code List - Shortcodes that are run last **************
// *******************************************************************
$postcodes = array(
	'template'     => 'sc_getTemplate',
	'presentation' => 'sc_Presentations',
	'article'      => 'sc_getArticle',
	'hide'         => 'sc_hideContent',
	'clear'        => 'sc_divClear',
	'data_table'   => 'sc_getData_Table',
	'runtime'      => 'sc_runTime',
	'tabs_group'   => 'sc_tabGroupPresentation',
	'accordion'    => 'sc_accordionPresentation',
	'accordion_faq' => 'sc_accordionFaqPresentation',
	'gallery'      => 'sc_galleryPresentation',
	'download'     => 'sc_fileDownload',
	'search'       => 'sc_searchManager',
	'template'     => 'sc_getTemplate',
	'system_error' => 'sc_systemError',
	'event_results' => 'sc_eventResults',
	'schedule'      => 'sc_scheduleEvent'
);

global $walt;
$short = $walt->getImagineer('shortcodes');
$short->add_shortcodes_array($postcodes);

function sc_showBodyParts($options=array(), $content='')
{
	global $walt;
	$prof = $walt->getImagineer('professor');
	$sc   = $walt->getImagineer('shortcodes');
	
	$content = trim($content);
	
	$prof->addSetting('BODY_STRUCTURE',$content);
	$actual_content = $sc->getHTML(array('HTML'=>$content));
	$prof->addSetting('BODY_HTML',$actual_content);
}

function sc_urlPath($options=array(), $content='')
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

function sc_getTemplate($options=array(), $content='')
{
	$name      = (isset($options['name'])) ? $options['name'] : null ;
	$mobile    = (isset($options['mobile'])) ? $options['mobile'] : false ;
	$ipad      = (isset($options['ipad'])) ? $options['ipad'] : false ;
	
	$type      = (isset($options['type'])) ? $options['type'] : null ;
	$startDate = (isset($options['start_date'])) ? $options['start_date'] : false ;
	$endDate   = (isset($options['end_date'])) ? $options['end_date'] : false ;
	$page_type = wed_getSystemValue('PAGE_TYPE');
	
	if ( (!$name) || (!calulateRunDate($startDate, $endDate)) )
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

function sc_showElementsContent($options=array(), $content='')
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

function sc_runTime($options=array(), $content='')
{
	$startDate = (isset($options['start_date'])) ? $options['start_date'] : false ;
	$endDate   = (isset($options['end_date'])) ? $options['end_date'] : false ;
	
	if (!calulateRunDate($startDate, $endDate))
	{
		return null;
	}
	else
	{
		return $content;
	}
}

	
function sc_getArticle($options=array(), $content='')
{
	if (!wed_getMomentInTime($options))
	{
		return null;
	}
	
	$specs['ARTICLE_CODE'] = (isset($options['code'])) ? $options['code'] : null ;
	$specs['ARTICLE_ID']   = (isset($options['id'])) ? $options['id'] : null ;
	$specs['FORMAT']       = (isset($options['format'])) ? $options['format'] : null ;
	$specs['DETAIL']       = (isset($options['detail'])) ? $options['detail'] : null ;
	$specs['TYPE']         = 'article';
	
	if ((is_null($specs['ARTICLE_CODE'])) && (is_null($specs['ARTICLE_ID'])))
	{
		return null;
	}
	
	if ( ($specs['ARTICLE_CODE']==='URL') || ($specs['ARTICLE_CODE']==='URL-ID') )
	{
		$call_parts = wed_getSystemValue('CALL_PARTS');
		
		if ((isset($call_parts[1])) && (!empty($call_parts[1])))
		{
			$specs['ARTICLE_CODE'] = ($specs['ARTICLE_CODE']==='URL') ? $call_parts[1] : null;
			$specs['ARTICLE_ID']   = ($specs['ARTICLE_CODE']==='URL-ID') ? $call_parts[1] : null;
			$update_header = true;
		}
		else
		{
			wed_changeSystemErrorCode('NO ARTICLE CODE');
		}
	}
	
	global $walt;
	$article  = $walt->getImagineer('presentations');
	$id       = $article->newPresentation($specs);
	return $article->getHTML(array('ID'=>$id));
}
		
function sc_getData_Table($options=array(), $content='')
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

function sc_hideContent($options=array(), $content='')
{
	return '';
}

function sc_divClear($options=array(), $content='')
{
	return '<div style="clear:both;"></div>';
}

// *******************************************************************
// *****  sc_Presentations *******************************************
// *******************************************************************
function sc_Presentations($options=array(), $content='')
{
	if (!wed_getMomentInTime($options))
	{
		return null;
	}
	
	// wed_renderContent runs the shortcodes found in the $content
	$options['ACTUAL_CONTENT'] = wed_renderContent($content);
	
	global $walt;
	$present = $walt->getImagineer('presentations');
	$id      = $present->newPresentation($options);
	return (!$id) ? null : $present->getHTML(array('ID'=>$id));
}


// *******************************************************************
// *****  sc_tabGroupPresentation *************************************
// *******************************************************************
function sc_tabGroupPresentation($options=array(), $content='')
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
// *****  sc_accordionPresentation ***********************************
// *******************************************************************
function sc_accordionPresentation($options=array(), $content='')
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


function sc_galleryPresentation($options=array(), $content='')
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

function sc_fileDownload($options=array(), $content='')
{
	$html     = '';
	$title    = (isset($options['title'])) ? $options['title'] : null ;
	$file     = (isset($options['file'])) ? $options['file'] : null ;
	$category = (isset($options['category'])) ? $options['category'] : null ;
	
	if (is_null($file))
	{
		return null;
	}
	
	if (is_null($category))
	{
		$call_parts = wed_getSystemValue('CALL_PARTS');
		$category   = (isset($call_parts[1])) ? $call_parts[1] : null;
	}
	
	$doc_options['NAME']     = $file;
	$doc_options['CATEGORY'] = $category;
	
	$doc_obj = wed_getDocumentObject($options=array());
	
	$file_path = $doc_obj->getDocumentFilePath();
	
	// the styling class here is specific to the Kallyas theme. You will need
	// to create a way to add different classes based on the theme or standardize
	// the class names across themes
	return '<p class="register" ><a href="'.$file_path.'"><button class="btn btn-success" type="button">Download</button></a> '.$title.'</p>'.LINE1;
}

// **********************************************************************
// ************** SEARCH FUNCTIONS **************************************
// **********************************************************************
function sc_searchResults($options=array(), $content='')
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

function sc_searchManager($options=array(), $content='')
{
	$theme                   = wed_getSystemValue('THEME','System');
	$specs['THEME']          = (isset($options['theme'])) ? $options['theme'] : $theme ;
	$specs['CODE']           = (isset($options['code'])) ? $options['code'] : $theme.'-search' ;
	$specs['TYPE']           = (isset($options['type'])) ? $options['type'] : 'search' ;
	$specs['CALL']           = (isset($options['call'])) ? $options['call'] : null ;
	$specs['SEARCH_TYPE']    = (isset($options['search_type'])) ? $options['search_type'] : null ;
	$specs['SEARCH_OPTIONS'] = (isset($options['options'])) ? $options['options'] : array() ;
	$specs['HEADING']        = (isset($options['heading'])) ? $options['heading'] : 'Search Results' ;
	
	global $walt;
	$search  = $walt->getImagineer('search');	
	$id      = $search->newSearch($specs);
		
	return $search->getHTML(array('ID'=>$id));
}

function sc_systemError($options=array(), $content='')
{
	$code = (isset($options['code'])) ? $options['code'] : 'GENERAL ERROR' ;
	wed_changeSystemErrorCode($code);
}

function sc_scheduleEvent($options=array(), $content='')
{
	$options = wed_standardKeys($options);
	
	// Detail Object for running scheduled events
	$options['TYPE'] = 'schedule';
	
	$options['PREFIX'] = (isset($options['PREFIX'])) ? $options['PREFIX'] : null ;
	$options['SUFFIX'] = (isset($options['SUFFIX'])) ? $options['SUFFIX'] : null ;
	$options['PRINT']  = (isset($options['PRINT'])) ? $options['PRINT'] : null ;
	
	global $walt;
	$time   = $walt->getImagineer('timemachine');	
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
function sc_eventResults($options=array(), $content='')
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

// **********************************************************************
// ************** COMMON FUNCTIONS **************************************
// **********************************************************************
function calulateRunDate($start=false,$end=false)
{
	date_default_timezone_set('America/Chicago');
	
	// this function will return true is today's date either falls
	// between the start and end date or before the end date
	if (!$start && !$end)
	{
		return true;
	}
	else
	{
		$start_date = (!$start) ? new DateTime('now') : new DateTime($start);
		$end_date   = new DateTime($end);
		$moment     = new DateTime('now');
		
		return ( ( ($moment>$start_date) || ($moment==$start_date) ) && (($moment==$end_date) || ($moment<$end_date)) );
	}
}
?>
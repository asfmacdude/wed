<?php

// *******************************************************************
// ********  getSearchContentKeywords() ******************************
// *******************************************************************
function getSearchContentKeywords()
{
	$status          = false;
	$search_string   = wed_getSystemValue('SEARCH_CLEAN');
	
	if (is_null($search_string))
	{
		return false;
	}

	$connect_db    = wed_getDBObject('content_connect');
	$status        = $connect_db->searchContentKeywords($search_string);
	
	return ($status) ? $connect_db : false ;
}

// *******************************************************************
// ******** getSearchContentTags() ***********************************
// *******************************************************************
function getSearchContentTags()
{
	$status          = false;
	$call_parts      = wed_getSystemValue('CALL_PARTS');
	$search_tag      = (isset($call_parts[1])) ? $call_parts[1] : null;
	
	if (is_null($search_tag))
	{
		wed_changeSystemErrorCode('NO SEARCH TAG');
		return $status;
	}
	
	$search_tag    = str_replace('-', ' ', $search_tag);
	$connect_db    = wed_getDBObject('content_connect');
	$status        = $connect_db->searchContentKeywords($search_tag);
	
	return ($status) ? $connect_db : false ;
}

// *******************************************************************
// ********  getSearchControl() **************************************
// *******************************************************************
function getSearchControl()
{
	$status          = false;
	$control_id      = wed_getSystemValue('CONTROL_ID'); // id of sport,video,photo,etc.
	$connect_db      = wed_getDBObject('content_connect');
	$status          = $connect_db->searchControl($control_id);
	
	return ($status) ? $connect_db : false ;
}

// *******************************************************************
// ********  getFeatureList() **************************************
// *******************************************************************
function getSearchFeatureList($options=array())
{
	$status          = false;
	$connect_db      = wed_getDBObject('content_connect');
	$feature         = (isset($options['CALL'])) ? $options['CALL'] : 'feature';
	$status          = $connect_db->searchFeature($feature);
	
	return ($status) ? $connect_db : false ;
}
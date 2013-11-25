<?php
defined( '_GOOFY' ) or die();
/*
 * The TAB_STYLE file that will be included will contain an array of 'WRAPPERS' to be used
 * around the two elements of the tabbed interface. The following are required:
 * - MAIN_OUTER_WRAP some tabbed interfaces will have an outer wrap, leave blank if not the case
 * - TAB_WRAP this is the wrap that goes around both the tab headers and the content.
 * - TAB_HEADERS_WRAP this wraps the tab headers, most of the time it will be a <ul> with a class
 * - TAB_HEAD_WRAP this wraps each individual tab head, usually a <li> with class or classes
 * - TAB_HEAD_ICON optional if the style allows for icons as part of the head
 * - TAB_CONTENT_WRAP this is the main wrap around the entire content pane, usually a <div>
 * - TAB_CONTENT_PANE_WRAP this is the wrap around eac individual content pane
 */
 $style = array();
 
 global $walt;
 $prof = $walt->getImagineer('professor');
 $theme  = $prof->getSystemValue('THEME');

 
 // Default Tab Styles
 $style['MAIN_OUTER_WRAP']       = '%CONTENT%';
 $style['TAB_WRAP']              = '%CONTENT%';
 $style['TAB_HEADERS_WRAP']      = '';
 $style['TAB_HEAD_WRAP']         = '';
 $style['TAB_HEAD_ICON']         = '';
 $style['TAB_CONTENT_WRAP']      = '';
 $style['TAB_CONTENT_PANE_WRAP'] = '';
 
 if ($theme==='Kallyas')
 {
	$style['MAIN_OUTER_WRAP']       = '<div class="vertical_tabs">%CONTENT%</div>';
	$style['TAB_WRAP']              = '<div class="tabbable">%CONTENT%</div>';
	$style['TAB_HEADERS_WRAP']      = '<ul class="nav fixclear">%CONTENT%</ul>';
	$style['TAB_HEAD_WRAP']         = '<li class="%ACTIVE%"><a href="#%TAB_ID%" data-toggle="tab">%ICON%%CONTENT%</a></li>';
	$style['TAB_HEAD_ICON']         = '<span><span class="%ICON_CLASS%"></span></span>';
	$style['TAB_CONTENT_WRAP']      = '<div class="tab-content">%CONTENT%</div>';
	$style['TAB_CONTENT_PANE_WRAP'] = '<div class="tab-pane %ACTIVE%" id="%TAB_ID%">%CONTENT%</div>';
 }
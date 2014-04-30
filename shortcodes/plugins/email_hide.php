<?php

/*
 * email_hide.php
 *
 *
 *
 */

$email_codes['email_hide'] = 'email_hideEmailAddress';

wed_registerShortcodes($email_codes);


function email_hideEmailAddress($options=array(), $content='')
{
	if (!wed_getMomentInTime($options))
	{
		return null;
	}
	
	$email          = (isset($options['email'])) ? $options['email'] : null;
	$class          = (isset($options['class'])) ? $options['class'] : 'cloaked';
	
	if (is_null($email))
	{
		return null;
	}
	
	$js_array[] = array(
		'ID'   => 'JQUERY',
		'LOAD' => true,
		'KEY'  => 'JS_FILES_TOP',
		'TYPE' => 'LIBRARY',
		'PATH' => 'jquery/jquery.js');
	$js_array[] = array(
		'ID'   => 'OBFUSCATE',
		'LOAD' => true,
		'KEY'  => 'JS_FILES',
		'TYPE' => 'LIBRARY',
		'PATH' => 'jquery/jquery.obfuscate.min.js');
	$js_array[] = array(
		'ID'   => 'OBFUSCATE-READY',
		'LOAD' => true,
		'KEY'  => 'JS_READY_CODE',
		'TYPE' => 'SCRIPT',
		'SCRIPT' => '$(".cloaked").obfuscate({cssClass: "email"});');
		
	wed_registerJavascript($js_array);
	
	$email_parts = explode('@', $email); // splits to john.doe  and aol.com
	
	if (count($email_parts)!=2)
	{
		return null;  // bad email address
	}
	
	$domain     = explode('.', $email_parts[1]); // Domain may have several '.', you have to make sure you get the last one
	$domain_ext = '';
	
	foreach ($domain as $value)
	{
		// It will end up being the last element of the $domain array
		$domain_ext = $value;
	}
	
	$domain_str = str_replace('.' . $domain_ext, '', $email_parts[1]);
	
	return '<span class="'.$class.'" title="'.$domain_ext.'|'.$domain_str.'|'.$email_parts[0].'::true"></span>';	
}
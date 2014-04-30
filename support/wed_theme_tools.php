<?php
/*
 * wed_theme_tools.php
 *
 * This file should be extended by all assets files in each theme. This stubs out all common
 * method calls across the board so that no errors occur.
 *
 */

class wed_theme_tools
{
	public function pushOptions()
	{
		wed_getCSS($this->getFilesCSS());
		wed_getJavascript($this->getFilesJavascript());
	}
	
	public function getFilesJavascript()
	{
		return array();
	}
	
	public function getFilesCSS()
	{	
		return array();
	}
	
	public function getFormats($name=null)
	{
		return array();
	}
}
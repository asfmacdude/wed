<?php

/*
 * layerslider.php
 *
 *
 *
 */

$slider_codes['layerslider'] = 'layer_showSlider';

wed_registerShortcodes($slider_codes);


function layer_showSlider($options=array(), $content='')
{
	if (!wed_getMomentInTime($options))
	{
		return null;
	}
	
	$style          = (isset($options['style'])) ? $options['style'] : 'clean_slider';
	$size           = (isset($options['size'])) ? $options['size'] : '1200_500';
	$folder         = (isset($options['folder'])) ? $options['folder'] : null;
	$default_folder = (isset($options['backup_folder'])) ? $options['backup_folder'] : wed_getSystemValue('DEFAULT_IMAGE_FOLDER','homepage');
	$height         = (isset($options['height'])) ? $options['height'] : null;
	$template       = (isset($options['template'])) ? $options['template'] : null;
	
	if (is_null($height))
	{
		$split_size = explode('_', $size);
		$height     = (isset($split_size[1])) ? $split_size[1] . 'px' : '0px';
	}

	$slider = array(
		'SLIDER_ID'            => 'layerslider',
		'COMPONENT'            => 'layerslider',
		'STYLE'                => $style,
		'IMAGE_SIZE'           => $size,
		'IMAGE_FOLDER'         => $folder,
		'DEFAULT_IMAGE_FOLDER' => $default_folder,
		'SLIDER_HEIGHT'        => $height,
		'CELL_TEMPLATE'        => $template
		);
		
	$animator  = getImagineer('animation');
	$animator->newAnimation($slider);
	return $animator->getHTML(array('SLIDER_ID' => 'layerslider'));	
}
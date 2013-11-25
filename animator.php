<?php

/*
You can add this properties to layers if you are using NEW 2D or 3D transitions (all the other properties listed above will be skipped):
slidedelay
transition2d
transition3d
timeshift
*/

/*
$type can be img,h1,h2,h3,h4, or future div
$src is the path to the image if this is TYPE = img

$class example: ls-s2, ls-s3, ls-s4
	There are a special sublayer, it is called background and it has a class named ls-bg. The parallax effect does not apply to background sublayers - so they are always staying in the background of the parent layer. (Otherwise you can add background-images to layers too.)
	
	Other sublayers have a class named ls-snumber. This number is important because of the parallax effect: sublayers with higher number will animate faster and therefore it seems that you see the whole animation in 3D. So the class name of a sublayer is one of the most important setting of the parallax effect. Sublayers with all the same properties BUT different number in their class names will animate with different speeds. For example a sublayer with class="ls-s4" will animate faster than other sublayer with class="ls-s2". From version 4.0 you can use the value -1. Sublayers with class="ls-s-1" will be positioned exactly outside of the layer. We recommend to use this setting for your sublayers if you want to slide them in from various directions.

$settings
	You can add this properties to sublayers:
	left
	top
	right
	bottom
	slidedirection *
	slideoutdirection **
	durationin
	durationout
	easingin
	easingout
	delayin
	delayout
	showuntil
	rotatein ***
	rotateout ***
	scalein ***
	scaleout ***
	
$text any text that will show in the h1-h4
*/


$event_array = array();

// TYPE can be img, h1, h2, h3, h4 or div(future)
$event_array['TYPE']  = 'img';
$event_array['SRC']   = '';  // Path to the image if the TYPE is img

// See docs above for sub layer classes
$event_array['CLASS'] = 'ls-s2';

// Leave settings null if not used
$settings['top']               = null; // can be in pixels or percentages
$settings['left']              = null; // can be in pixels or percentages
$settings['right']             = null; // can be in pixels or percentages
$settings['bottom']            = null; // can be in pixels or percentages
$settings['slidedirection']    = null; // can be  'left', 'right', 'top', 'bottom' or 'fade' 
$settings['slideoutdirection'] = null; // can be  'left', 'right', 'top', 'bottom' or 'fade'
$settings['durationin']        = null; // number (millisecs) Duration of the slide-in animation.
$settings['durationout']       = null; // number (millisecs) Duration of the slide-out animation.
$settings['easingin']          = null; // (all easing types of jQuery UI Effects Core can be used) Easing (type of transition) of the slide-in animation.
$settings['easingout']         = null; // (all easing types of jQuery UI Effects Core can be used) Easing (type of transition) of the slide-in animation.
$settings['delayin']           = null; // number (millisecs) Delay time of the slide-in animation.
$settings['delayout']          = null; // number (millisecs) Delay time of the slide-out animation.
$settings['showuntil']         = null; // number (millisecs)
$settings['rotatein']          = null; // in degrees 180, 90, -90, etc.
$settings['rotateout']         = null; // in degrees 180, 90, -90, etc.
$settings['scalein']           = null; // numeric 0.5, 1, 2, etc.
$settings['scaleout']          = null; // numeric 0.5, 1, 2, et

$event_array['SETTINGS'] = filterNull($settings);

echo encodeJSON($event_array);


// filter out null settings
function filterNull($settings)
{
	$filtered = array();
	
	foreach ($settings as $key=>$value)
	{
		if (!is_null($value))
		{
			$filtered[$key] = $value;
		}
	}
	
	return $filtered;
}

function encodeJSON($arr)
{
	return json_encode($arr);
}
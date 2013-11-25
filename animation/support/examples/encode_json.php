<?php

	
$arr['UL_WRAP_TOP']       = '<ul class="menu">%s</ul>';
$arr['LI_FORMAT']         = array(
			'LEVEL-1'   => '<li><a href="{{LINK}}">{{TITLE}}</a>{{DROPDOWN}}</li>',
			'LEVEL-2'   => '<li><a href="{{LINK}}">{{TITLE}}</a>{{DROPDOWN}}</li>'
			);
$arr['UL_WRAP_DROP']      = '<ul>%s</ul>';


$arr = array(
    'TYPE'    => 'img',
    'IMG_SRC' => 'files/images/gnrl/logos/asg_logo.png',
    'CLASS'   => 'ls-s2',
    'SETTINGS' => array(
        'top'               => '280px', 
		'left'              => '90%',
		'slidedirection'    => 'fade', 
		'slideoutdirection' => 'fade', 
		'durationin'        => 2000, 
		'durationout'       => 1500, 
		'easingin'          => 'easeInQuint'
     )

);
echo json_encode($arr);


$json = '{"TYPE":"img","IMG_SRC":"\/themes\/Yalin\/images\/slider\/ASFF_Logo_med.png","CLASS":"ls-s2","SETTINGS":{"top":"40px","left":"80%","slidedirection":"fade","slideoutdirection":"fade","durationin":2000,"durationout":1500,"easingin":"easeInQuint"}}';

$array = json_decode($json);

// print_r($array);
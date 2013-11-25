<?php


// ASF Logo fades in on the right of the cell
$event = array(
    'TYPE'    => 'img',
    'IMG_SRC' => '/themes/Yalin/images/slider/ASFF_Logo_med.png',
    'CLASS'   => 'ls-s2',
    'SETTINGS' => array(
        'top'               => '40px',
        'left'              => '80%',
        'slidedirection'    => 'fade',
        'slideoutdirection' => 'fade',
        'durationin'        => 2000
        'durationout'       => 1500
        'easingin'          => 'easeInQuint'
     )

);

$event = array(
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



// White box fades in on right with the TEXT
$event = array(
    'TYPE'  => 'h4',
    'CLASS' => 'ls-s1 whitebox w240 buttonbar',
    'SETTINGS' => array(
        'top'   => '290px',
        'left'  => '80%',
        ),
    'TEXT' => 'Donate Online<button class="buttonbar" type="button">Give</button>'
);


// Box slides in from bottom of cell with the words 'We Mentor'
$event = array(
	'TYPE'  => 'h3',
    'CLASS' => 'ls-s2 redbox whiteletters w440',
    'SETTINGS' => array(
        'position' => 'absolute',
            'top'               => '110px',
            'left'              => '55px',
            'slidedirection'    => 'bottom',
            'slideoutdirection' => 'left',
            'durationin'        => '1000',
            'durationout'       => '750',
            'easingin'          => 'easeInOutQuint',
            'easingout'         => 'aseInBack',
            'delayin'           => 2800
        ),
    'TEXT' => 'We Mentor'
);
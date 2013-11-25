<?php

// This cell starts with an image of a blue sky, then builds each of the events on top
$cell = array(
    'IMAGE_BG' => '/themes/Yalin/images/slider/bkgrd_sky.jpg',
    'SETTINGS' => array(
        'slidedelay'   => 9000,
        'transition3d' => 'all',
        'transition2d' => 'all'
        ),
    'CODES' => array(
        'asflogo_right',
        'donatebox_right',
        'mission_topleft',
        'mentor_topleft',
        'impact_topleft',
        'lead_topleft',
        'reaching_left'
        )
);


// Example template cell
// Working from a list of images/image folder, you would use this setup for
// each image by simply plugging in the path to the IMAGE_BK value.
$cell = array(
    'IMAGE_BG' => null,
    'SETTINGS' => array(
        'transition3d' => 'all',
        'transition2d' => 'all'
        ),
    'CODES' => array(
        'asflogo_right'
        )
);
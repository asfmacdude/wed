<?php

$today = new DateTime('NOW');

// var_dump($today);

echo $today->format("Y/m/d H:i:s");
echo '<br>';
echo $today->format("l, F j, Y, g:ia");




/*
$date=date_create("2013-03-15");
echo date_format($date,"Y/m/d H:i:s");
*/
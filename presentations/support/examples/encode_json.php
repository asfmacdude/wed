<?php

// Menu Setups

$style['UL_WRAP_TOP'] = '<div id="menu"><ul>%s</ul></div>';

// <li><a href="index.html" class="contentLink" rel="external"><span><img src="images/icon-home.png" alt="" border="0"/></span>Home </a></li>

$style['LI_FORMAT'] = array(
	'LEVEL-1' => '<li><a href="{{LINK}}" class="contentLink" rel="external"><span><img src="{{THEME_URL}}images/icon-home.png" alt="" border="0"/></span>{{TITLE}}</a>/li>'
);
$style['UL_WRAP_DROP'] = '<ul>%s</ul>';

echo json_encode($style);

?>
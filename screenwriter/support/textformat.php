<?php

//returns $length characters from the left of $string
function left($string, $length) {
   return substr($string, 0, $length);
}

//returns $length characters from the right of $string
function right($string, $length) {
   return substr($string, -$length, $length);
}

function smartProper($name)
{
	$name = strtolower($name);
	$name = implode("'", array_map('ucwords', explode("'", $name)));
	$name = implode("-", array_map('ucwords', explode("-", $name)));
	$name = implode("Mac", array_map('ucwords', explode("Mac", $name)));
	$name = implode("Mc", array_map('ucwords', explode("Mc", $name)));
	return $name;
}
	
function getWords($string='', $which_word=0)
{
    if (!empty($string))
    {
        $words = explode(' ',$string);
        
        if ($which_word>0)
        {
            return $words[$which_word-1];
        }
        else
        {
        	return $words[$which_word];
        }
    }
}

function eclipseLongStrings($string, $length)
{
	if (strlen($string) > $length)
	{
		$string = left($string, $length).'...';
	}
	
	return $string;
}
?>
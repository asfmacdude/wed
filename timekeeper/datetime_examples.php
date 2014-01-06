<?php

// $today = new DateTime('NOW');

// var_dump($today);

/*
echo $today->format("Y/m/d H:i:s");
echo '<br>';
echo $today->format("l, F j, Y, g:ia");
*/

$start = null;
$end   = '2014/06/20 19:00:00';

echo formatDateDiff($start,$end);


/*
$date=date_create("2013-03-15");
echo date_format($date,"Y/m/d H:i:s");
*/

function formatDateDiff($start=null, $end=null)
{ 
    if (is_null($start))
    { 
        $start = new DateTime("now"); 
    }
	else
	{
		$start = new DateTime($start);
	}
    
    if (is_null($end))
    { 
        $end = new DateTime("now"); 
    }
	else
	{
		$end = new DateTime($end);
	}

    $interval = $end->diff($start);
    
    var_dump($interval);
    
    $doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals
    
    $format = array(); 
    if ($interval->y !== 0)
    { 
        $format[] = "%y ".$doPlural($interval->y, "year"); 
    }
    
    if ($interval->m !== 0)
    { 
        $format[] = "%m ".$doPlural($interval->m, "month"); 
    }
    
    // calculate total days
    if ($interval->a !== 0)
    { 
        $format[] = "%a ".$doPlural($interval->a, "day"); 
    }
    
    if($interval->d !== 0)
    { 
        $format[] = "%d ".$doPlural($interval->d, "day"); 
    } 
    if ($interval->h !== 0)
    { 
        $format[] = "%h ".$doPlural($interval->h, "hour"); 
    }
    
    if($interval->i !== 0)
    { 
        $format[] = "%i ".$doPlural($interval->i, "minute"); 
    }
    
    if($interval->s !== 0)
    { 
        if (!count($format))
        { 
            return "less than a minute ago"; 
        }
        else
        { 
            $format[] = "%s ".$doPlural($interval->s, "second"); 
        } 
    }
    
    var_dump($format);
    
    // We use the two biggest parts 
    if (count($format) > 1)
    { 
        $format = array_shift($format)." and ".array_shift($format); 
    }
    else
    { 
        $format = array_pop($format); 
    }
    
    // Prepend 'since ' or whatever you like 
    return $interval->format($format); 
}
?>
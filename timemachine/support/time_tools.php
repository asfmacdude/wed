<?php
/*
	The time_tools class is never called alone, it is always extended from
	one of the timemachine detail classes
	
	All of these functions take DateTime objects as arguments so remember to convert
	string dates/times to DateTime objects before passing them over.
	
	Function List
	getSQLTime - returns proper format for sql date fields
	getTime - returns time as 9:30 pm
	getWeekday - returns weekday Monday, Tuesday, etc.
	getFullDate - returns full date as January 15, 2014
	getMonth - returns month January, February, etc.
	getMonthNumber - returns month number either padded or not 1,2,3,4 or 01,02,03,04
	getDayNumber - returns the day date 1-31
	getDaySuffix - returns day date with the proper suffix 1st, 2nd, 3rd, 4th
	getDayDate - returns Monday, January 15, 2014
	getDayDateTime - returns Monday, January 15, 2014, 5:45 pm
	getDateDifference - returns difference between two dates in either
		years and months like 5 months and 4 days or in total days like 165 days and 4 hours
	
*/

class time_tools extends details
{	
	public function getSQLTime($dtime_obj)
	{
		// returns proper format for sql date fields
		return $dtime_obj->format("Y/m/d H:i:s");
	}
	
	public function getTime($dtime_obj)
	{
		// returns time as 9:30 pm
		return $dtime_obj->format("g:i a");
	}
	
	public function getWeekday($dtime_obj)
	{
		// returns weekday Monday, Tuesday, etc.
		return $dtime_obj->format("l");
	}
	
	public function getFullDate($dtime_obj)
	{
		// returns January 15, 2014
		return $dtime_obj->format("F j, Y");
	}
	
	public function getShortDate($dtime_obj)
	{
		// returns 1/15/2014
		return $dtime_obj->format("m/d/Y");
	}
	
	public function getMonth($dtime_obj)
	{
		// returns January
		return $dtime_obj->format("F");
	}
	
	public function getMonthNumber($dtime_obj,$leading_zero=false)
	{
		if (!$leading_zero)
		{
			// returns 1,2,3,4,5
			return $dtime_obj->format("n");
		}
		else
		{
			// returns 01,02,03,04,05
			return $dtime_obj->format("m");
		}
	}
	
	public function getDayNumber($dtime_obj,$leading_zero=false)
	{
		if (!$leading_zero)
		{
			// returns 1,2,3,4,5
			return $dtime_obj->format("j");
		}
		else
		{
			// returns 01,02,03,04,05
			return $dtime_obj->format("d");
		}
	}
	
	public function getDaySuffix($dtime_obj,$leading_zero=false)
	{
		// returns 2nd, 3rd, 4th
		return $dtime_obj->format("jS");
	}
	
	public function getDayDate($dtime_obj)
	{
		// returns Monday, January 15, 2014
		$str  = $this->getWeekday($dtime_obj) . ', ';
		$str .= $this->getFullDate($dtime_obj);	
		return $str;
	}
	
	public function getDayDateTime($dtime_obj)
	{
		// returns Monday, January 15, 2014, 5:45 pm
		$str  = $this->getWeekday($dtime_obj) . ', ';
		$str .= $this->getFullDate($dtime_obj) . ', ';
		$str .= $this->getTime($dtime_obj);		
		return $str;
	}
	
	public function getDateDifference($start_dtime_obj, $end_dtime_obj, $count_months=false)
	{ 	
	    $interval = $end_dtime_obj->diff($start_dtime_obj);
	     
	    $doPlural = function($nb,$str){return $nb>1?$str.'s':$str;}; // adds plurals
	    
	    $format = array();
	    
	    if ($count_months)
	    {
		    // Turn on $count_months if you want it to display
		    // 5 months and 14 days
		    // calculate years
		    if ($interval->y !== 0)
		    { 
		        $format[] = "%y ".$doPlural($interval->y, "year"); 
		    }
		    
		    // calculate months
		    if ($interval->m !== 0)
		    { 
		        $format[] = "%m ".$doPlural($interval->m, "month"); 
		    }
		    
		    // calculate days
		    if ($interval->d !== 0)
		    { 
		        $format[] = "%d ".$doPlural($interval->d, "day"); 
		    }
	    }
	    else
	    {
		    // Default: $count_months off will display 165 days and 5 hours
		    // calculate total days
		    if ($interval->days !== 0)
		    { 
		        $format[] = "%a ".$doPlural($interval->days, "day"); 
		    }
	    }
	    
	    // calculate hours
	    if ($interval->h !== 0)
	    { 
	        $format[] = "%h ".$doPlural($interval->h, "hour"); 
	    }
	    
	    // calculate minutes
	    if ($interval->i !== 0)
	    { 
	        $format[] = "%i ".$doPlural($interval->i, "minute"); 
	    }
	    
	    // calculate seconds
	    if ($interval->s !== 0)
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
}

?>
<?php
/*
	The time_tools class is never called alone, it is always extended from
	one of the timemachine detail classes
	
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
}

?>
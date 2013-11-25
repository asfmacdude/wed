<?php

/*
 * validate.php
 *
 *
 * Possible Rules
 * isRequired
 * isRequiredNone
 * isString
 * isNumber
 * isInteger
 * isAlpha
 * isEmail (NEWER!!)
 * isWithinRange
 * isEmailAddress
 *
 * compareValues
 * containsCharacter - need to re-write this one
 *
 * 4/15/11 - Make a change here to pass entire $data array over to validate functions
 *           this will allow greater flexibility to write validate function that COMPARE values
 *           or work in MIN and MAX ranges
 *
 * 4/12/12 - Changed the way errors are returned. Instead or worrying with error messages here
 *           we simply add an ERROR key to the array and enter the value of 1 (error) or 0 (no error)
 *           Now the db object will handle whether to display the message
 */
defined( '_GOOFY' ) or die();


class validate
{
	public $field_list;
	public $Local_Config;
	public $error_messages;

	public function __construct($field_list)
	{
		$this->field_list = $field_list;
	}

	public function setLocalConfig()
	{
		$this->Local_Config['CLASS_NAME'] = __CLASS__;
		$this->Local_Config['LOCAL_PATH'] = dirname(__FILE__);
	}

    public function getErrors()
    {
        
        foreach ($this->field_list as $name=>$data)
        {   
            if (isset($data['VALIDATE']))
            {               
                if (is_array($data['VALIDATE']))
                {
                    $error_array = array();
                    
                    foreach ($data['VALIDATE'] as $key=>$check)
                    {         
                        $error_array[$key] = ((method_exists($this, $check)) && !call_user_func(array($this, $check), $data)) ? 'Y' : 'N' ;
                    }
                    
                    $this->field_list[$name]['ERROR'] = $error_array;
                }
                else
                {                
                    if ( (method_exists($this, $data['VALIDATE'])) && !call_user_func(array($this, $data['VALIDATE']), $data) )
                    {
                    	$this->field_list[$name]['ERROR'] = 'Y';
                    }
                    else
                    {
                    	$this->field_list[$name]['ERROR'] = 'N';
                    }
                }
            }
        }

        return $this->field_list;
    }

    // check whether input is empty
    private function isRequired($data)
	{	
		$value = (isset($data['VALUE'])) ? $data['VALUE'] : '';
		
		if ( (isset($data['DEFAULT'])) && $value === $data['DEFAULT'])
		{
			return false;
		}

        return (empty($value) || trim($value) == '') ? false : true;
    }

	// check whether input is 'none' like a selected value
    private function isRequiredNone($data)
	{
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : 'none';
        return (empty($value) || trim($value) == 'none') ? false : true;
    }

    // check whether input is a string
    private function isString($data)
	{
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : '';
        return is_string($value);
    }

    // check whether input is a number
    private function isNumber($data)
	{
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : 0;
        return is_numeric($value);
    }

    // check whether input is an integer
    private function isInteger($data)
	{
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : 0;
        return (intval($value) == $value) ? true : false;
    }

    // check whether input is alphabetic
    private function isAlpha($data)
	{
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : '';
        return preg_match('/^[a-zA-Z]+$/', $value);
    }

    private function isEmail($data)
    {
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : 0;
        return(preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i",$value));
    }
 
    // check whether input is within a numeric range
    private function isWithinRange($data)
	{
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : 0;
        $min   = (isset($data['MIN']))   ? $data['MIN']   : 0;
        $max   = (isset($data['MAX']))   ? $data['MAX']   : 100;
        return (is_numeric($value) && $value >= $min && $value['VALUE'] <= $max) ? true : false;
    }

    // check whether input is a valid email address
    private function isEmailAddress($data)
	{
        $value = (isset($data['VALUE'])) ? $data['VALUE'] : '';
        return eregi('^([a-z0-9])+([\.a-z0-9_-])*@([a-z0-9_-])+(\.[a-z0-9_-]+)*\.([a-z]{2,6})$', $value);
    }

	//
	private function compareValues($data)
	{
        $value   = (isset($data['VALUE']))   ? $data['VALUE']   : '';
        $compare = (isset($data['COMPARE'])) ? $data['COMPARE'] : '';

		return ($value === $compare ) ? true : false;
	}

    // check if a value exists in an array
    private function isAllowed($data)
	{
        $value   = (isset($data['VALUE'])) ? $data['VALUE'] : '';
        $allowed = ((isset($data['ALLOWED'])) && (is_array($data['ALLOWED']))) ? $data['ALLOWED'] : array();
        return in_array($value, $allowed);
    }
    
    // Specific Validation for Heroes Forms
    // If the value of 'not_attend' is anything other than Yes, theses fields are required
    private function not_attend_Required($data)
    {
    	if ($this->field_list['not_attend']['VALUE'] != 'Yes')
    	{
    		$value = (isset($data['VALUE'])) ? $data['VALUE'] : '';
        	return (empty($value) || trim($value) == '') ? false : true;
    	}
    	else
    	{
    		return true;
    	}
    }
    
    // Specific Validation for Heroes Forms
    // If the value of 'not_attend' is anything other than Yes, theses fields are required
    private function not_attend_Email($data)
    {
    	if ($this->field_list['not_attend']['VALUE'] != 'Yes')
    	{
    		$value = (isset($data['VALUE'])) ? $data['VALUE'] : '';
        	return eregi('^([a-z0-9])+([\.a-z0-9_-])*@([a-z0-9_-])+(\.[a-z0-9_-]+)*\.([a-z]{2,6})$', $value);
    	}
    	else
    	{
    		return true;
    	}
    } 
}
?>
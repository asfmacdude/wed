<?php
/*
 * @version		$Id: goofy_clean.php 1.0 2009-03-03 $
 * @package		DreamWish
 * @subpackage	main
 * @copyright	Copyright (C) 2012 Medley Productions. All rights reserved.
 * 
 * DreamWish is a Disney inspired CMS system developed by Randy Cherry
 * Dedicated to the dreamer of dreams, Walt Disney
 * 
 * 'I believe in being an innovator.' - Walt Disney
 * 
 * 
 */
defined( '_GOOFY' ) or die();
/*
 * goofy_clean.php
 * 
 * Description goes here
 * 
 */

class goofy_clean extends imagineer
{
    public $soaps;
    public $soap_regex;
    public $cleanLog = array();
    
    public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new goofy_clean();
        }

        return $instance;
    }

    private function __construct()
    {
        $this->soaps = array(
            'ALL'          => 'extraStrong',
            'NONE'         => 'leaveDirty',
            'EXTRA_STRONG' => 'extraStrong',
            'SEARCH'       => 'cleanSearch',
            'NAME'         => 'cleanName',
            'ADDRESS'      => 'cleanAddress',
            'ZIPCODE'      => 'cleanZipcode',
            'EMAIL'        => 'cleanEmail',
            'PHONE'        => 'cleanPhone',
            'PASSWORD'     => 'cleanPassword',
            'SYS_PAGE'     => 'cleanSysPage',
            'SYS_ACT'      => 'cleanSysAct',
            'NUMBER'       => 'cleanAlpha',
            'ALPHA'        => 'cleanNumbers',
            'URL'          => 'cleanURL',
            'FINAL_HTML'   => 'cleanHTML',
            'SC_BRACKETS'  => 'cleanPreShortcodes'
        );

        $this->soap_regex = array(
            'NO_ALPHA'              => '([a-zA-Z])',
            'NO_LOWER_ALPHA'        => '([a-z])',
            'NO_UPPER_ALPHA'        => '([A-Z])',
            'NO_NUMBERS'            => '([0-9])',
            'NO_SPACES'             => '(\s)',
            'NO_CHARACTERS'         => '([~!@#$%&*()^<>=:;.,?_+-])',
            'NO_CHARACTERS_NAME'    => '([~!@#$%&*()^<>=:;?_+-])',
            'NO_CHARACTERS_ADDRESS' => '([~!@#$%&*()^<>=:;?_+-])',
            'NO_CHARACTERS_EMAIL'   => '([!#$%&*()^<>=:;?+-])',
            'NO_CHARACTERS_PAGE'    => '([!#$%&*()^<>=:;?+-])',
            'NO_CHARACTERS_URL'     => '([!#$%&*()^<>=:;?+-])',
            'NO_CHARACTERS_ACT'     => '([!#$%&*()^<>=:;?+])',
            'NO_MATH_FUNCTIONS'     => '([<>=+-])',
            'NO_QUOTES'             => '([\"\'])'
        );
    }
    
    public function setOptions() {}
    public function init() {}
    
    public function padString($string=null,$char=0,$fill=' ',$side='LEFT')
    {
	    return ($side==='LEFT') ? str_pad($string, $char, $fill, STR_PAD_LEFT) : str_pad($string, $char, $fill, STR_PAD_RIGHT);
    }

    public function CleanItUp($string=null, $soap='EXTRA_STRONG')
    {
        if (is_null($string))
        {
            return $string;
        }

        $string = trim($string);

        $soap_method = (isset($this->soaps[$soap])) ? $this->soaps[$soap] : $this->soaps['ALL'];
        $all_clean   = call_user_func(array($this,$soap_method), $string);

        $message = 'Dirty value: '.$string.' cleaned with: '.$soap.'['.$soap_method.'] Clean value: '.$all_clean;
        $this->cleaningLog($message);

        return $all_clean;
    }

    private function extraStrong($string)
    {
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS');

        return $string;
    }
    
    private function cleanForwardSlash($string)
    {
	    return str_replace('/', '', $string);
    }
    
    private function cleanBackSlash($string)
    {
	    return str_replace('\\', '', $string);
    }
    
    private function cleanURL($string)
    {
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS_URL');

        return $string;
    }

    private function cleanSearch($string)
    {
        /*
         * How to clean a search:
         * - Remove all punctuation marks and such
         * - Remove quotes for serializing the data
         * - strip slashes
         */
        $transform = 'cleanSearch-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS_NAME');
        $transform .= '['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_QUOTES');
        $transform .= '['.$string.']';
        $string = stripslashes($string);
        $string = $this->cleanForwardSlash($string);
        $string = $this->cleanBackSlash($string);
        $transform .= '['.$string.']';

        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanName($string)
    {
        /*
         * How to clean a name:
         * - Remove all punctuation marks and such
         * - Properly capitalize First and Last Name
         * - Remove multiple spaces
         */
        $transform = 'cleanName-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS_NAME');
        $transform .= '['.$string.']';
        $string = $this->removeExtraSpaces($string);
        $transform .= '['.$string.']';
        $string = $this->smartProper($string);
        $transform .= '['.$string.']';

        $this->cleaningLog($transform);
        
        return $string;
    }

    private function cleanAddress($string)
    {
        /*
         * How to clean a address:
         * - Remove all punctuation marks and such
         * - Properly capitalize each word
         * - Remove multiple spaces
         */
        $transform = 'cleanAddress-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS_ADDRESS');
        $transform .= '['.$string.']';
        $string = $this->smartProper($string);
        $transform .= '['.$string.']';
        $string = $this->removeExtraSpaces($string);
        $transform .= '['.$string.']';

        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanZipcode($string)
    {
        /*
         * How to clean a zipcode:
         * - Remove all punctuation marks and such
         * - Remove all alpha characters
         * - Remove spaces
         * - Check length either 5 numbers (36067) or 10 characters (36067-8000)
         */
        $transform = 'cleanZipcode-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS');
        $transform .= '['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_ALPHA');
        $transform .= '['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_SPACES');
        $transform .= '['.$string.']';

        if (strlen($string)>=9)
        {
            // formatted 36066-8000
            $string = substr($string, 0, 5).'-'.substr($string, 5, 4);
        }
        else
        {
            $string = substr($string, 0, 5);
        }

        $transform .= '['.$string.']';
        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanPhone($string)
    {
        /*
         * How to clean a phone number:
         * - Remove all punctuation marks and such
         * - Remove all alpha characters
         * - Remove spaces
         * - Check length either  numbers (2721888) or 10 characters (3342721888)
         */
        $transform = 'cleanPhone-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS');
        $transform .= '['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_ALPHA');
        $transform .= '['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_SPACES');
        $transform .= '['.$string.']';

        if (strlen($string)>=10)
        {
            // formatted 334.272.1888
            $string = substr($string, 0, 3).'.'.substr($string, 3, 3).'.'.substr($string, 6, 4);
        }
        else
        {
            $string = substr($string, 0, 3).'.'.substr($string, 3, 4);
        }

        $transform .= '['.$string.']';
        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanPassword($string)
    {
        /*
         * How to clean up a password
         * - remove spaces
         * - everything else is pretty much allowed
         */
        $transform = 'cleanPassword-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_SPACES');

        $transform .= '['.$string.']';
        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanEmail($string)
    {
        /*
         * How to clean a Email:
         * - Remove all punctuation marks and such
         * - Remove all spaces
         */
        $transform = 'cleanEmail-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS_EMAIL');
        $transform .= '['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_SPACES');
        $transform .= '['.$string.']';

        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanSysPage($string)
    {
        $transform = 'cleanSysPage-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS_PAGE');

        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanSysAct($string)
    {
        $transform = 'cleanSysAct-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_CHARACTERS_ACT');

        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanAlpha($string)
    {
        $transform = 'cleanAlpha-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_ALPHA');
        $transform .= '['.$string.']';
        $this->cleaningLog($transform);

        return $string;
    }

    private function cleanNumbers($string)
    {
        $transform = 'cleanAlpha-['.$string.']';
        $string = $this->digitalCleaners($string, 'NO_NUMBERS');
        $transform .= '['.$string.']';
        $this->cleaningLog($transform);

        return $string;
    }
    
    private function cleanPreShortcodes($string)
    {
	    /*
	     * The WYSIWYG Editors all put paragraph tags around everything including
	     * your shortcodes so it ends up looking like <p>[shortcode /]</p>. This
	     * function will attempt to clean that up.
	     *
	     */
	     $string = str_replace('<p>[', '[', $string);
	     $string = str_replace(']</p>', ']', $string);
	     
	     return $string;
    }
    
    private function cleanHTML($string)
    {
	    /*
	     * This cleans up after all the other imagineers, shortcodes are
	     * going to leave '<p></p>' all over the place and there will be
	     * others that do similar.
	     *
	     */
	     $dirt = array('<p></p>');
	     $string = str_replace($dirt, '', $string);
	     
	     return $string;
    }

    private function smartProper($string)
    {
        $string = $this->removeExtraSpaces($string);
        
        $start = explode(' ',$string);
        $end   = array();

        foreach ($start as $word)
        {
            $word = strtolower($word);

            // Fix McDonald, McSmith, etc.
            if (substr($word, 0,2) === 'mc')
            {
                $word = substr($word, 2);
                $word = 'Mc'.ucfirst($word);
            }
            else
            {
                $word = ucfirst($word);
            }

            $end[] = $word;
        }


        return implode(' ', $end);
    }

    private function removeExtraSpaces($string)
    {
        // Removes multiple spaces and replaces them with a single space
        return preg_replace('(\s+)', ' ', $string);
    }

    private function leaveDirty($string)
    {
        return $string;
    }

    private function washingMachine($string, $soap)
    { 
        return str_replace(array_keys($soap), array_values($soap), $string);
    }

    private function digitalCleaners($string=null, $exp_key=null, $replace='')
    {
        $clean_string = $string;

        if (isset($this->soap_regex[$exp_key]))
        {
            $clean_string = preg_replace($this->soap_regex[$exp_key], $replace, $string);
        }
        return $clean_string;
    }

    private function cleaningLog($message)
    {
        $this->cleanLog[] = 'CleanString:'.$message;
    }

    public function getCleaningLog()
    {
        return $this->cleanLog;
    }
}

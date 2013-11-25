<?php
/*
 * @version		$Id: actions.php 1.0 2009-03-03 $
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
 * actions.php
 * 
 * Actions Array
 * [ACTIONS]
 *		[CONTENT]
 *			[0]
 *				[ARRAY HOOK=BEFORE (Meaning add it BEFORE the content), FUNCTION=getDate, OPTIONS=array of options, PRIORITY=10 (Default)
 */

class actions extends imagineer
{
	public $options  = array();
	
	public static function getInstance()
    {
        static $instance = NULL; // this only initializes the $instance varible not set it

        if ($instance == null)
        {
            $instance = new actions();
        }

        return $instance;
    }
	
	private function __construct()
	{

	}
	
	public function init()
	{
		$this->setOptions();
	}
	
	protected function setOptions()
	{
		$this->options['ACTIONS'] = array();
	}
	
	public function add_action($config=array())
	{
		$setup[] = $config;
		$this->add_actions_array($setup);
	}
	
	public function add_actions_array($setup=array())
	{
		foreach ($setup as $key=>$options)
		{
			$hook     = (isset($options['HOOK'])) ? $options['HOOK'] : false ;
			$func     = (isset($options['FUNCTION'])) ? $options['FUNCTION'] : false ;
			$options  = (isset($options['OPTIONS'])) ? $options['OPTIONS'] : array() ;
			$priority = (isset($options['PRIORITY'])) ? $options['PRIORITY'] : 10 ;
			
			if ( $hook && $func )
			{
				$this->options['ACTIONS'][$hook][$priority][] = array( 'FUNCTION' => $func, 'OPTIONS'=> $options );
			}
		}
	}
	
	public function remove_action($hook)
	{
		$this->options['ACTIONS'][$hook];
	}
	
	public function remove_all_actions()
	{
		$this->options['ACTIONS'] = array();
	}
	
	public function do_action($options)
	{	
		$result = '';
		
		$hook = ((is_array($options)) && (isset($options['HOOK']))) ? $options['HOOK'] : $options ;
		
		if (isset($this->options['ACTIONS'][$hook]))
		{
			$actions = $this->options['ACTIONS'][$hook];
			ksort($actions);
			
			foreach ($actions as $priority=>$list)
			{
				foreach ( $list as $key=>$value )
				{
					$result .= call_user_func( $value['FUNCTION'], $value['OPTIONS']);
				}
			}
		}
		
		return $result;
	}
}

?>
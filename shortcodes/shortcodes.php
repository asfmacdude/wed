<?php
/*
 * @version		$Id: shortstuff.php 1.0 2009-03-03 $
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
 * shortstuff.php
 * 
 * Description goes here
 * 
 */
 /**
 * WordPress API for creating bbcode like tags or what WordPress calls
 * "shortcodes." The tag and attribute parsing or regular expression code is
 * based on the Textpattern tag parser.
 *
 * A few examples are below:
 *
 * [shortcode /]
 * [shortcode foo="bar" baz="bing" /]
 * [shortcode foo="bar"]content[/shortcode]
 *
 * Shortcode tags support attributes and enclosed content, but does not entirely
 * support inline shortcodes in other shortcodes. You will have to call the
 * shortcode parser in your function to account for that.
 *
 * {@internal
 * Please be aware that the above note was made during the beta of WordPress 2.6
 * and in the future may not be accurate. Please update the note when it is no
 * longer the case.}}
 *
 * To apply shortcode tags to content:
 *
 * <code>
 * $out = do_shortcode($content);
 * </code>
 *
 * @link http://codex.wordpress.org/Shortcode_API
 *
 * @package WordPress
 * @subpackage Shortcodes
 * @since 2.5
 */

class shortcodes extends imagineer
{
	public $options  = array();
	public $themeObj = false;
	public $pattern;
	
	public static function getInstance()
    {
        // this only initializes the $instance varible not set it
        static $instance = NULL; 

        if ($instance == null)
        {
            $instance = new shortcodes();
        }

        return $instance;
    }
	
	private function __construct()
	{
		
	}
	
	public function init()
	{
		$this->setOptions();
		$this->loadSupportFiles();
		$this->loadSupportFiles('plugins');
	}
	
	protected function setOptions()
	{
		$this->options['CLASS_NAME']       = __CLASS__;
		$this->options['LOCAL_PATH']       = dirname(__FILE__);
		$this->options['SHORT_CODES']      = array();
		$this->options['POST_SHORT_CODES'] = array();
		$this->options['PRE_SHORT_CODES']  = array();
	}
		
	public function add_shortcode($tag, $func)
	{
		// Check things at the door here
		// If the function does not exist, then don't add it to list
		if ( function_exists($func) )
		{
			$this->options['POST_SHORT_CODES'][$tag] = $func;
		}
	}
	
	public function add_pre_shortcode($tag, $func)
	{
		// Check things at the door here
		// If the function does not exist, then don't add it to list
		if ( function_exists($func) )
		{
			$this->options['PRE_SHORT_CODES'][$tag] = $func;
		}
	}
	
	public function add_shortcodes_array($shortcodes,$pre=false)
	{
		foreach ($shortcodes as $tag=>$func)
		{
			if ($pre)
			{
				$this->add_pre_shortcode($tag,$func);
			}
			else
			{
				$this->add_shortcode($tag,$func);
			}
		}
	}
	
	public function remove_shortcode($tag)
	{
		$this->options['SHORT_CODES'][$tag];
	}
	
	public function remove_all_shortcodes()
	{
		$this->options['SHORT_CODES'] = array();
	}
	
	public function getShortcodeFunction($shortcode)
	{
		$function = null;
		
		if (isset($this->options['POST_SHORT_CODES'][$shortcode]))
		{
			$function = $this->options['POST_SHORT_CODES'][$shortcode];
		}
		elseif (isset($this->options['PRE_SHORT_CODES'][$shortcode]))
		{
			$function = $this->options['PRE_SHORT_CODES'][$shortcode];
		}
		
		return $function;
	}
	
	private function do_all_shortcodes($content)
	{
		// If content is an array, there are matches
		if (is_array($content))
		{
			$content = call_user_func(array($this,'do_shortcode_tag'), $content);
		}
		
		$pattern = $this->get_shortcode_regex();		
		return preg_replace_callback( "/$pattern/s", array($this,'do_all_shortcodes'), $content );
	}
	
	public function do_shortcode($content)
	{	
		if (empty($this->options['SHORT_CODES']))
		{
			return $content;
		}

		$pattern = $this->get_shortcode_regex();		
		return preg_replace_callback( "/$pattern/s", array($this,'do_shortcode_tag'), $content );
	}
	
	/**
	 * Retrieve the shortcode regular expression for searching.
	 *
	 * The regular expression combines the shortcode tags in the regular expression
	 * in a regex class.
	 *
	 * The regular expression contains 6 different sub matches to help with parsing.
	 *
	 * 1 - An extra [ to allow for escaping shortcodes with double [[]]
	 * 2 - The shortcode name
	 * 3 - The shortcode argument list
	 * 4 - The self closing /
	 * 5 - The content of a shortcode when it wraps some content.
	 * 6 - An extra ] to allow for escaping shortcodes with double [[]]
	 *
	 * @since 2.5
	 * @uses $shortcode_tags
	 *
	 * @return string The shortcode search regular expression
	 */
	
	public function get_shortcode_regex()
	{
		$tagnames  = array_keys($this->options['SHORT_CODES']);
		$tagregexp = join( '|', array_map('preg_quote', $tagnames) );
	
		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		return
			  '\\['                              // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)"                     // 2: Shortcode name
			. '\\b'                              // Word boundary
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			.     '(?:'
			.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
			.     ')*?'
			. ')'
			. '(?:'
			.     '(\\/)'                        // 4: Self closing tag ...
			.     '\\]'                          // ... and closing bracket
			. '|'
			.     '\\]'                          // Closing bracket
			.     '(?:'
			.         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			.             '[^\\[]*+'             // Not an opening bracket
			.             '(?:'
			.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			.                 '[^\\[]*+'         // Not an opening bracket
			.             ')*+'
			.         ')'
			.         '\\[\\/\\2\\]'             // Closing shortcode tag
			.     ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}
	
	/**
	 * Regular Expression callable for do_shortcode() for calling shortcode hook.
	 * @see get_shortcode_regex for details of the match array contents.
	 *
	 * @since 2.5
	 * @access private
	 * @uses $shortcode_tags
	 *
	 * @param array $m Regular expression match array
	 * @return mixed False on failure.
	 */
	public function do_shortcode_tag( $m )
	{	
		$clean_entity = function($value)
		{
			return html_entity_decode($value);
		};
		
		$m = array_map($clean_entity, $m);
		
		// allow [[foo]] syntax for escaping a tag
		// strips the extra [ ] off the ends
		if ( $m[1] == '[' && $m[6] == ']' )
		{
			return substr($m[0], 1, -1);
		}
	
		$tag = $m[2];
		$attr = $this->shortcode_parse_atts( $m[3] );
	
		if ( isset( $m[5] ) )
		{
			// enclosing tag - extra parameter
			return $m[1] . call_user_func( $this->options['SHORT_CODES'][$tag] , $attr, $m[5], $tag ) . $m[6];
		}
		else
		{
			// self-closing tag
			return $m[1] . call_user_func( $this->options['SHORT_CODES'][$tag] , $attr, NULL,  $tag ) . $m[6];
		}
	}
	
	/**
	 * Retrieve all attributes from the shortcodes tag.
	 *
	 * The attributes list has the attribute name as the key and the value of the
	 * attribute as the value in the key/value pair. This allows for easier
	 * retrieval of the attributes, since all attributes have to be known.
	 *
	 * @since 2.5
	 *
	 * @param string $text
	 * @return array List of attributes and their value.
	 */
	public function shortcode_parse_atts($text)
	{
		$atts    = array();
		$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text    = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
		
		if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) )
		{
			foreach ($match as $m)
			{
				if (!empty($m[1]))
					$atts[strtolower($m[1])] = stripcslashes($m[2]);
				elseif (!empty($m[3]))
					$atts[strtolower($m[3])] = stripcslashes($m[4]);
				elseif (!empty($m[5]))
					$atts[strtolower($m[5])] = stripcslashes($m[6]);
				elseif (isset($m[7]) and strlen($m[7]))
					$atts[] = stripcslashes($m[7]);
				elseif (isset($m[8]))
					$atts[] = stripcslashes($m[8]);
			}
		}
		else
		{
			$atts = ltrim($text);
		}
		return $atts;
	}
	
	/**
	 * Combine user attributes with known attributes and fill in defaults when needed.
	 *
	 * The pairs should be considered to be all of the attributes which are
	 * supported by the caller and given as a list. The returned attributes will
	 * only contain the attributes in the $pairs list.
	 *
	 * If the $atts list has unsupported attributes, then they will be ignored and
	 * removed from the final returned list.
	 *
	 * @since 2.5
	 *
	 * @param array $pairs Entire list of supported attributes and their defaults.
	 * @param array $atts User defined attributes in shortcode tag.
	 * @return array Combined and filtered attribute list.
	 */
	public function shortcode_atts($pairs, $atts)
	{
		$atts = (array)$atts;
		$out  = array();
		
		foreach ($pairs as $name => $default)
		{
			if ( array_key_exists($name, $atts) )
			{
				$out[$name] = $atts[$name];
			}	
			else
			{
				$out[$name] = $default;
			}	
		}
		return $out;
	}
	
	/**
	 * Remove all shortcode tags from the given content.
	 *
	 * @since 2.5
	 * @uses $shortcode_tags
	 *
	 * @param string $content Content to remove shortcode tags.
	 * @return string Content without shortcode tags.
	 */
	public function strip_shortcodes( $content )
	{
		if (empty($this->options['SHORT_CODES']) || !is_array($this->options['SHORT_CODES']))
		{
			return $content;
		}	
	
		$pattern = $this->get_shortcode_regex();
	
		return preg_replace_callback( "/$pattern/s", array( $this, 'strip_shortcode_tag'), $content );
	}
	
	public function strip_shortcode_tag( $m )
	{
		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' )
		{
			return substr($m[0], 1, -1);
		}
	
		return $m[1] . $m[6];
	}
	
	private function setShortCodes($pre)
	{
		if ($pre)
		{
			$this->options['SHORT_CODES'] = $this->options['PRE_SHORT_CODES'];
		}
		else
		{
			$this->options['SHORT_CODES'] = $this->options['POST_SHORT_CODES'];
		}
	}
	
	public function setHTML($options=null)
	{		
		$html = (isset($options['HTML'])) ? $options['HTML'] : null;
		$pre  = (isset($options['PRE'])) ? $options['PRE'] : false;
		$this->setShortCodes($pre);
		
		return $this->do_all_shortcodes($html,$pre);
		
		// In case there are no Short Codes
		/*
if (empty($this->options['SHORT_CODES']))
		{
			return $html;
		}
		else
		{
			// $this->pattern = $this->get_shortcode_regex();
			return $this->do_all_shortcodes($html);
		}
*/
	}

}

?>

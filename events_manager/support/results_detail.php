<?php
/*
 * @version		$Id: results_detail.php 1.0 2009-03-03 $
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
 * results_detail.php
 * 
 */

class results_detail extends details
{
	public $options  = array();
	
	public function __construct($options=array())
	{
		$this->setOptions($options);
		$this->getSettingsFromURL();
	}
	
	private function setOptions($options)
	{
		$this->options['CLASS_NAME']        = __CLASS__;
		$this->options['LOCAL_PATH']        = dirname(__FILE__);
		$this->options['DETAIL_ID']         = null; // this is assigned by Events Manager
		$this->options['SEARCH']            = 'sport'; // search for results by 
		$this->options['LIST_OBJECT']       = null; // The actual list db object is loaded here
		
		// URL Search Options
		$this->options['CONTROL']           = null;  // Gontrol from URL
		$this->options['GROUP']             = null;  // Group from URL
		$this->options['ARTICLE']           = null;  // Article from URL
		$this->options['GROUP_ID']          = null;  // Group ID
		$this->options['GROUP_TITLE']       = null;  // Group Title
		
		$this->options['YEAR']              = date("Y");  // Search by YEAR
		$this->options['EVENT']             = null;  // Search by GROUP
		$this->options['ACTIVITY_ID']       = null;  // Search by GROUP_ID
		
		// Formats
		$this->options['YEAR_HEADING']      = '<h3>%CONTENT% Official Results</h3>'.LINE1;
		$this->options['SPORT_HEADING']     = '<h4>%CONTENT% Results</h4>'.LINE1;
		$this->options['EVENT_HEADING']     = '<h5>%CONTENT%</h5>'.LINE1;
		$this->options['LI_FORMAT']         = '<li>%CONTENT%</li>'.LINE1;
		
		$this->addOptions($options);
	}
	
	private function buildPresentation()
	{
		$html = null;
		$this->loadList();

		if ($this->options['LIST_OBJECT'])
		{
			$list_db      = $this->options['LIST_OBJECT'];
			$item         = 0;
			$currentYear  = '';
			$currentSport = '';
			$currentEvent = '';
			$first_year   = true;
			$first_sport  = true;
			$first_event  = true;
			
			$fields = array('rslt_year','rslt_sport','rslt_event','rslt_results','rslt_title','rslt_score','rslt_city');
			
			$html .= '<div id="results">'.LINE1;
			
			while ($list_db->moveRecordList($item))
			{	
				$row_values = $list_db->getFormattedValue($fields);
				$year       = $row_values['rslt_year'];
				$sport      = $row_values['rslt_sport'];
				$event      = $row_values['rslt_event'];
				$results    = $row_values['rslt_results'];
				$title      = $row_values['rslt_title'];
				$score      = $row_values['rslt_score'];
				$city       = $row_values['rslt_city'];	
				
				if ( $year != $currentYear)
		        {
		            /*
		             * Print the year
		             */
		            if ($first_year)
		            {
		                $html .= str_replace('%CONTENT%', $year, $this->options['YEAR_HEADING']);
		            }
		            else
		            {
		                $html .= '</ul>'.LINE1;
		                $html .= str_replace('%CONTENT%', $year, $this->options['YEAR_HEADING']);
		            }
		
		            $currentYear = $year;
		            $first_year  = false;
		            $first_sport = true;
		    		$first_event = true;
		        }
		        
		        if ($sport != $currentSport)
		        {
		            /*
		             * Print the Sport
		             */
		            if ($first_sport)
		            {
		                $html .= str_replace('%CONTENT%', $sport, $this->options['SPORT_HEADING']);
		                $first_sport = false;
		            }
		            else
		            {
		                $html .= '</ul>'.LINE1;
		                $html .= str_replace('%CONTENT%', $sport, $this->options['SPORT_HEADING']);
		            }
		           
		            $currentSport = $sport;
		        }
		        
		        if ($event != $currentEvent)
		        {
		            /*
		             * Print the Event
		             */
		            if ($first_event)
		            {
		               $html .= '<ul>'.LINE1;
		               $html .= str_replace('%CONTENT%', $event, $this->options['EVENT_HEADING']);
		               $first_event = false;
		            }
		            else
		            {
		                $html .= '</ul><ul>'.LINE1;
		                $html .= str_replace('%CONTENT%', $event, $this->options['EVENT_HEADING']);
		            }
		            
		            $currentEvent = $event;
		        }
		        
		        $content  = $results.' - '.$title;  
		        $content .= (!empty($score)) ? ' ['.$score.'] ' : null;
		        $content .= (!empty($city)) ? ' ('.$city.') ' : null;
		        $html    .= str_replace('%CONTENT%', $content, $this->options['LI_FORMAT']);
				
				$item++;
			}
			
			$html .= '</ul></div>'.LINE1;
			
		}
		
		return $html;
	}
	
	private function loadList()
	{
		$data['TYPE']      = 'results_list';
		$data['SEARCH']    = 'sport';
		$data['GROUP_ID']  = $this->options['GROUP_ID'];
		$data['GROUP']     = $this->options['GROUP'];
		
		// if $year is null, list manager will look for all years
		$data['YEAR']      = $this->options['YEAR'] ;
		
		$this->options['LIST_OBJECT'] = wed_getList($data); // returns a list object
	}
	
	// get a number of settings from the URL. It still can be replaced by incoming options
	private function getSettingsFromURL()
	{
		$call_parts = wed_getSystemValue('CALL_PARTS');
		$this->options['CONTROL'] = (isset($call_parts[0])) ? $call_parts[0] : null;
		$this->options['GROUP']   = (isset($call_parts[1])) ? $call_parts[1] : null;
		$this->options['ARTICLE'] = (isset($call_parts[2])) ? $call_parts[2] : null;
		
		if (!is_null($this->options['GROUP']))
		{
			// Get the group title and group_id
			$group_db = wed_getDBObject('content_groups');
			$this->options['GROUP_TITLE'] = $group_db->getGroupTitle($this->options['GROUP']);
			$this->options['GROUP_ID']    = $group_db->getGroupID($this->options['GROUP']);
		}
		
		if ( ($this->options['CONTROL']==='results') &&(!is_null($this->options['ARTICLE'])) )
		{
			// In this situation, the url will look like this:
			// /results/archery/2011 and will show the results for archery
			// for the year 2011
			$this->options['YEAR'] = $this->options['ARTICLE'];
		}
	}
	
	public function setHTML($options=array())
	{
		return $this->buildPresentation();
	}
}

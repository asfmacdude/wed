<?php
/*
 * @version		$Id: email_detail.php 1.0 2009-03-03 $
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
 * email_detail.php
 *
 * Important NOTE: this detail object uses the new detail_object
 * 
 */

class email_detail extends details
{
	public $options  = array();
	public $detail;
	public $message_db;
	
	public function __construct($detail)
	{
		$this->detail = $detail;
		$this->setOptions($detail);
	}
	
	private function setOptions($detail)
	{
		$this->detail                 = $detail;
		$this->options['CLASS_NAME']  = __CLASS__;
		$this->options['LOCAL_PATH']  = dirname(__FILE__);
		$this->options['ID']          = 'email1'; // this is assigned by Presentations
		$this->options['CODE']        = null;
		$this->options['EMAIL']       = null; // This can be a single address or a string of addresses separated by commas
		$this->options['EMAIL_NAME']  = null;
		$this->options['SUBJECT']     = null;
		$this->options['FROM_EMAIL']  = wed_getSystemValue('SUPPORT_EMAIL');
		$this->options['REPLY_EMAIL'] = null;
		$this->options['CC_EMAIL']    = null;
		$this->options['BCC_EMAIL']   = null;
		$this->options['HEADERS']     = null;
		$this->options['MESSAGE']     = null;
		
		$this->addOptions($detail->options);
	}
	
	public function loadMessageTemplate()
	{
		$this->message_db = wed_getDBObject('wed_message_templates');
		return $this->message_db->getRecordByCode($this->options['CODE']);
	}
	
	public function setEmailAddress()
	{
		if (is_null($this->options['EMAIL']))
		{
			$this->options['EMAIL'] = $this->message_db->getDetail('EMAIL');
		}

		return (is_null($this->options['EMAIL'])) ? false : true ;
	}
	
	public function setHeaders()
	{		
		$email       = $this->options['EMAIL'];
		
		wed_addSystemValue('EMAIL',$email); // Add to system values for merging
		wed_addSystemValue('EMAIL_NAME',$this->options['EMAIL_NAME']); // Add to system values for merging
		
		$from_email  = $this->message_db->getDetail('FROM_EMAIL',$this->options['FROM_EMAIL']);
		$reply_email = $this->message_db->getDetail('REPLY_EMAIL');	
		$cc_email    = $this->message_db->getDetail('CC_EMAIL');
		$bcc_email   = $this->message_db->getDetail('BCC_EMAIL');
		
		$headers  = 'MIME-Version: 1.0' . PHP_EOL;
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
		$headers .= "To: $email" . PHP_EOL;
		$headers .= "From: $from_email" . PHP_EOL;
		
		if (!is_null($reply_email))
		{
			$headers .= "Reply-To: $reply_email" . PHP_EOL;
		}
		
		if (!is_null($cc_email))
		{
			$headers .= "Cc: $cc_email" . PHP_EOL;
		}
		
		if (!is_null($bcc_email))
		{
			$headers .= "Bcc: $bcc_email" . PHP_EOL;
		}
		
		$this->options['HEADERS'] = $headers;
	}
	
	public function setMessage()
	{
		$keys    = getImagineer('keys');
		$message = $this->message_db->getValue('message');	
		$this->options['MESSAGE'] = $keys->getHTML(array('HTML'=>$message,'MERGE'=>true));	
	}
	
	public function sendMessage()
	{
		return mail($this->options['EMAIL'], $this->options['SUBJECT'], $this->options['MESSAGE'], $this->options['HEADERS']);
	}
	
	public function setHTML($options=null)
	{
		$status = false;
		
		if ( ($this->loadMessageTemplate()) && ($this->setEmailAddress()) )
		{
			$this->setHeaders();
			$this->setMessage();			
			$status = $this->sendMessage();
		}

		return $status;
	}
}
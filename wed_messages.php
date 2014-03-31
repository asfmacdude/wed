<?php


class messages
{
	public static $msg_list = array();
	public static $options  = array();
	
	public static function addMessage($message,$from="GENERAL")
	{
		if (is_array($message))
		{
			self::addMessageArray($message,$from);
		}
		elseif (is_object($message))
		{
			self::addMessageObject($message,$from);
		}
		else
		{
			self::$msg_list[$from][] = $message;
		}
	}
	
	private static function addMessageArray($msg_array,$from)
	{
		if (is_array($msg_array))
		{
			foreach ($msg_array as $key=>$value)
			{
				$new_msg = '';
				
				if (is_array($value))
				{
					$new_msg = implode('|', $value);
					$new_msg = (strlen($new_msg)>200) ? substr($new_msg,0,200) : $new_msg;
				}
				elseif (is_object($value))
				{
					$new_msg = $value->infoString();
				}
				else
				{
					$new_msg = $value;
				}
				
				self::$msg_list[$from][] = '[' . $key . '] ' . $new_msg;
			}
		}
	}
	
	private static function addMessageObject($msg_object,$from)
	{
		if (is_object($msg_object))
		{
			self::$msg_list[$from][] = $msg_object->infoString();
		}
	}
	
	private static function formatMessages()
	{
		$html = null;
		$list = self::$msg_list;
		
		if (count($list)>0)
		{
			$html = '<div style="background-color:#ddd;padding:10px;">'.LINE1;
			
			foreach ($list as $key=>$value)
			{
				$html .= '<h2>Messages from: ' . $key . '</h2>'.LINE1;
				
				if (is_array($value))
				{
					foreach ($value as $index=>$msg)
					{
						$html .= '<p>'.$msg.'</p>'.LINE1;
					}
				}
				else
				{
					$html .= '<p>No messages from ' . $key . '</p>'.LINE1;
				}
			}
			
			$html .= '</div>'.LINE2;
		}
		
		return $html;
	}
	
	public function getMessage()
    {
	   	return self::formatMessages();
    }
}
<?php

class session_handler {
	
	public static function open($save_path, $session_name){
		return true;
	}
	
	public static function close(){
		return true;
	}
	
	public static function read($session_id){
		$session_id = mysql_real_escape_string($session_id);
		
		$result = mysql_query("SELECT `session_data` FROM `sessions` WHERE `session_id` = '{$session_id}'");
		
		if (mysql_num_rows($result) == 0){
			return '';
		}
		
		$_SESSION = unserialize(mysql_result($result, 0));
		
		return session_encode();
	}
	
	public static function write($session_id, $session_data){
		$session_id		= mysql_real_escape_string($session_id);
		$session_data	= mysql_real_escape_string(serialize($_SESSION));
		
		$sql = "INSERT INTO `sessions` (`session_id`, `session_data`, `session_last_use`)
				VALUES ('{$session_id}', '{$session_data}', UNIX_TIMESTAMP())
				ON DUPLICATE KEY UPDATE `session_data` = '{$session_data}', `session_last_use` = UNIX_TIMESTAMP()";
		
		return mysql_query($sql);
	}
	
	public static function destroy($session_id){
		$session_id		= mysql_real_escape_string($session_id);
		
		return mysql_query("DELETE FROM `sessions` WHERE `session_id` = '{$session_id}'");
	}
	
	public static function gc($max_lifetime){
		return mysql_query("DELETE FROM `sessions` WHERE UNIX_TIMESTAMP() - `session_last_use` > {$max_lifetime}");
	}
	
	public static function fetch_all(){
		$result = mysql_query('SELECT `session_id`, `session_last_use`, CHAR_LENGTH(`session_data`) AS `session_size` FROM `sessions`');
		
		$sessions = array();
		
		while (($row = mysql_fetch_assoc($result)) !== false){
			$sessions[] = array(
				'id'		=> $row['session_id'],
				'last_use'	=> $row['session_last_use'],
				'size'		=> $row['session_size'],
			);
		}
		
		return $sessions;
	}
	
	public static function read_serialized($session_id){
		$session_id = mysql_real_escape_string($session_id);
		
		$result = mysql_query("SELECT `session_data` FROM `sessions` WHERE `session_id` = '{$session_id}'");
		
		if (mysql_num_rows($result) == 0){
			return serialize(array());
		}
		
		return mysql_result($result, 0);
	}
	
}

?>

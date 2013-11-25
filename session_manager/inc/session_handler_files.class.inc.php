<?php

class session_handler {
	
	public static function open($save_path, $session_name){
		return true;
	}
	
	public static function close(){
		return true;
	}
	
	public static function read($session_id){
		if (file_exists("{$GLOBALS['root_path']}/session_files/sess_{$session_id}") === false){
			return '';
		}
		
		$_SESSION = unserialize(file_get_contents("{$GLOBALS['root_path']}/session_files/sess_{$session_id}"));
		
		return session_encode();
	}
	
	public static function write($session_id, $session_data){
		return file_put_contents("{$GLOBALS['root_path']}/session_files/sess_{$session_id}", serialize($_SESSION));
	}
	
	public static function destroy($session_id){
		if (in_srray("{$GLOBALS['root_path']}/session_files/sess_{$session_id}", scandir("{$GLOBALS['root_path']}/session_files")) === false){
			return false;
		}
		
		return unlink("{$GLOBALS['root_path']}/session_files/sess_{$session_id}");
	}
	
	public static function gc($max_lifetime){
		foreach (glob("{$GLOBALS['root_path']}/session_files/sess_*") as $session_file){
			if ((time() - filemtime($session_file)) > $max_lifetime){
				unlink($session_file);
			}
		}
		
		return true;
	}
	
	public static function fetch_all(){
		$sessions = array();
		
		foreach (glob("{$GLOBALS['root_path']}/session_files/sess_*") as $session_file){
			$sessions[] = array(
				'id'		=> substr($session_file, strrpos($session_file, 'sess_') + 5),
				'last_use'	=> filemtime($session_file),
				'size'		=> filesize($session_file),
			);
		}
		
		return $sessions;
	}
	
	public static function read_serialized($session_id){
		if (file_exists("{$GLOBALS['root_path']}/session_files/sess_{$session_id}") === false){
			return serialize(array());
		}
		
		return file_get_contents("{$GLOBALS['root_path']}/session_files/sess_{$session_id}");
	}
	
}

?>

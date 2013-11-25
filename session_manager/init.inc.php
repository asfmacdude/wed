<?php

$root_path = dirname(__FILE__);

include("{$root_path}/config.inc.php");

if (session_manager_config::$storage_method === 'mysql'){
	if (session_manager_config::$storage_method === false){
		mysql_connect(session_manager_config::$mysql_server, session_manager_config::$mysql_username, session_manager_config::$mysql_password);
		mysql_select_db(session_manager_config::$mysql_db_name);
	}
	
	include("{$root_path}/inc/session_handler_mysql.class.inc.php");
}else{
	include("{$root_path}/inc/session_handler_files.class.inc.php");
}

if (basename(dirname($_SERVER['PHP_SELF'])) !== 'admin'){
	if (ini_get('session.auto_start')){
		session_write_close();
	}
	
	session_set_save_handler(
		array('session_handler', 'open'),
		array('session_handler', 'close'),
		array('session_handler', 'read'),
		array('session_handler', 'write'),
		array('session_handler', 'destroy'),
		array('session_handler', 'gc')
	);
}

?>

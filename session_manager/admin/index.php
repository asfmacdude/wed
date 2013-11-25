<?php

include('../init.inc.php');

session_start();

if (isset($_SESSION['password'], $_SESSION['remote_addr']) === false || $_SESSION['remote_addr'] !== $_SERVER['REMOTE_ADDR'] || $_SESSION['password'] !== sha1(session_manager_config::$admin_password)){
	header('Location: login.php');
	die();
}

if (isset($_GET['delete'])){
	session_handler::destroy($_GET['delete']);
	
	header('Location: index.php');
	die();
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" media="all" href="ext/css/main.css" />
		<title>Session Management</title>
	</head>
	<body>
		<div id="wrap">
			<?php
			
			if (isset($_GET['view'])){
				include("{$root_path}/admin/admin_pages/view_session.page.php");
			}else{			
				include("{$root_path}/admin/admin_pages/view_all.page.php");
			}
			
			?>
		</div>
	</body>
</html>

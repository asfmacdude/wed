<?php

include('../init.inc.php');

session_start();

if (isset($_POST['password']) && $_POST['password'] === session_manager_config::$admin_password){
	$_SESSION['password']		= sha1($_POST['password']);
	$_SESSION['remote_addr']	= $_SERVER['REMOTE_ADDR'];
	
	header('Location: index.php');
	die();
}

if (isset($_SESSION['password'], $_SESSION['remote_addr']) && $_SESSION['remote_addr'] === $_SERVER['REMOTE_ADDR'] && $_SESSION['password'] === sha1(session_manager_config::$admin_password)){
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
			
			if (isset($_POST['password'])){
				echo '<div class="error">The password you entered was incorrect.</div>';
			}
			
			?>
			<form action="" class="login" method="post">
				<div>
					<input type="password" class="text" name="password" /><input type="image" src="ext/img/login.png" value="Login" />
				</div>
			</form>
		</div>
	</body>
</html>

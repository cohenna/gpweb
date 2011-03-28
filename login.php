<?php
	require_once 'gp-api.php';
	require_once 'util.php';
	
	$api = new GpAPI();
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if(empty($username) || empty($password)) {
		rickroll('/?e=invalid%20login');
		return;
	}
	
	$result = $api->Authenticate($username, $password);
	if($result == 1) {
		session_start();
		$_SESSION['username'] = $username;
		$_SESSION['user'] = $api->UserGet($username, $password);
		$_SESSION['pwhash'] = GpAPI::gphash($password);
		rickroll('/');
	}
	else {
		rickroll('/?e=invalid%20login');
	}
?>
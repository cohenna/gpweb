<?php
	require_once 'gp-api.php';
	require_once 'util.php';
	
	$api = new GpAPI();
	#echo '['.$_SESSION['username'].']<BR>';
	#echo '['.$_SESSION['pwhash'].']<BR>';
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	if(empty($username) || empty($password)) {
		#echo 'empty';
		redirect('/?e=invalid%20login');
		return;
	}
	
	$result = $api->Authenticate($username, $password);
	#echo $result;
	#return;
	if($result == 1) {
		session_start();
		$_SESSION['username'] = $username;
		$_SESSION['pwhash'] = GpAPI::gphash($password);
		redirect('/');
	}
	else {
		redirect('/?e=invalid%20login');
	}
?>
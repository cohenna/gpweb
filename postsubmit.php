<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	
	
	$postid = $_POST['postid'];
	$tid = array_safe_get('tid', $_POST, '');
	$response = $_POST['response'];
	$subject = $_POST['subject'];
	if(!logged_in() || empty($subject)) {
		rickroll('/');
		return;
	}
	
	$api = new GpAPI();
	$post = $api->PostAdd($postid, $subject, $response);
	
	$url = '/post.php?postid='.$post;
	if(!$post) {
		$url .= '&e=problem';
	}
	if(!empty($tid)) {
		$url .= '&tid='.$tid;
	}
	rickroll($url);
	
?>
<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	
	
	$postid = $_POST['postid'];
	$response = $_POST['response'];
	$subject = $_POST['subject'];
	if(!logged_in() || empty($response) || empty($subject)) {
		redirect('/');
		return;
	}
	
	$api = new GpAPI();
	$post = $api->PostAdd($postid, $subject, $response);
	
	if($post) {
		redirect('/post.php?postid='.$post);
	}
	else {
		redirect('/post.php?postid='.$postid.'&e=problem');
	}
	
?>
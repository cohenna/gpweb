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
	#$unread = $api->PostCountUnread();
	#$posts = $api->Posts($threadid);
	$post = $api->PostAdd($postid, $subject, $response);
	
	#echo var_export($post, TRUE);
	#return;
	if($post) {
		redirect('/post.php?postid='.$post);
	}
	else {
		redirect('/post.php?postid='.$postid.'&e=problem');
	}
	
?>
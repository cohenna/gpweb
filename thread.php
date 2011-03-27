<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	
	$threadid = $_GET['tid'];
	if(empty($threadid) || !logged_in()) {
		rickroll('/');
		return;
	}
	
	$api = new GpAPI();
	$unread = $api->PostCountUnread();
	$posts = $api->Posts($threadid);
	$nextUnreadPostId = $api->PostNextUnreadID();
	$menuSettings = array('nextUnreadPostId' => $nextUnreadPostId, 'returnToBoard' => TRUE);
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Greenpride Mobile </title>
	<?php echo head_links(); ?>
</head>
<body>
<div class="panel" selected="true">
<?php
	//echo menu();
	display_posts($posts);
	echo menu($menuSettings);
?>
</div>

<?php echo footer(); ?>
</body>
</html>

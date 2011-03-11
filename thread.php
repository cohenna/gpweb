<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	
	$threadid = $_GET['tid'];
	if(empty($threadid) || !logged_in()) {
		redirect('/');
		return;
	}
	
	$api = new GpAPI();
	$unread = $api->PostCountUnread();
	$posts = $api->Posts($threadid);
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Greenpride Mobile </title>
	<link rel="icon" type="image/png" href="<?php echo IUI_ROOT; ?>/iui-favicon.png">
	<link rel="apple-touch-icon" href="<?php echo IUI_ROOT; ?>/iui-logo-touch-icon.png" />
	<link rel="stylesheet" href="<?php echo IUI_ROOT; ?>/iui.css" type="text/css" />
	<link rel="stylesheet" title="Default" href="<?php echo IUI_ROOT; ?>/t/default/default-theme.css"  type="text/css"/>
	<link rel="stylesheet" href="<?php echo IUI_WEB_ROOT; ?>/css/iui-panel-list.css" type="text/css" />
</head>
<body>

<div class="panel" selected="true">
<?php
	echo menu();
	display_posts($posts);
	echo menu();
?>
</div>
</body>
</html>

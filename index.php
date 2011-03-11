<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	#session_start();
	#$_SESSION['username'] = '';
	#$_SESSION['pwhash'] = '';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Greenpride Mobile</title>
	<link rel="icon" type="image/png" href="<?php echo IUI_ROOT; ?>/iui-favicon.png">
	<link rel="apple-touch-icon" href="<?php echo IUI_ROOT; ?>/iui-logo-touch-icon.png" />
	<link rel="stylesheet" href="<?php echo IUI_ROOT; ?>/iui.css" type="text/css" />
	<link rel="stylesheet" title="Default" href="<?php echo IUI_ROOT; ?>/t/default/default-theme.css"  type="text/css"/>
	<link rel="stylesheet" href="<?php echo IUI_WEB_ROOT; ?>/css/iui-panel-list.css" type="text/css" />
</head>
<body>

<?php
	if(!logged_in()) {
?>
<form id="login" title="Theaters" class="panel" action="/login.php" method="POST" selected="true"> 
	<img src="/img/Logo.jpg" /><br>
	<h2>Please Login</h2> 
	<fieldset>
<?php
	if(!empty($_GET['e'])) {
?>
	<div class="row"> 
	<b><center><font color="red"><?php echo $_GET['e'] ?></font></center></b>
	</div>
<?php } ?>
		<div class="row"> 
			<label>Username</label> 
			<input type="text" name="username" value=""/> 
		</div> 
		<div class="row"> 
			<label>Password</label> 
			<input type="password" name="password" value=""/> 
		</div> 
	</fieldset> 
	<!--<a class="whiteButton" type="submit" onclick="javascript:this.form.submit();">Login</a> -->
	<input type="submit" class="whiteButton" value="Login" />
</form> 
<?php } else {
	$api = new GpAPI();
	$posts = $api->Posts();
?>
<div class="panel" selected="true">
<?php
	display_threads($posts);
?>
<h2>Create a New Thread</h2> 
<form id="postsubmit" class="panel" action="/postsubmit.php" method="POST" > 
	<fieldset>
<?php
	if(!empty($_GET['e'])) {
?>
	<div class="row"> 
	<b><center><font color="red"><?php echo $_GET['e'] ?></font></center></b>
	</div>
<?php } ?>
		<div class="row"> 
			<label>Subject</label> 
			<input type="text" name="subject" value="<?php echo $subject; ?>" />
		</div> 
		<div class="row"> 
			<label>Response</label> 
			<textarea name="response"></textarea>
		</div> 
	</fieldset> 
	<!--<a class="whiteButton" type="submit" onclick="javascript:this.form.submit();">Login</a> -->
	<input type="submit" class="whiteButton" value="Submit" />
</form> 
</div>
<?php
} ?>
</body>
</html>

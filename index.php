<?php
	require_once 'util.php'; // does a session_start
	require_once 'gp-api.php';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Greenpride Mobile</title>
	<?php echo head_links(); ?>
	<script language="javascript">
	$(document).ready(function() {
		$("p").text("The DOM is now loaded and can be manipulated.");
		$('#newThreadButton').click(function() {
			$('html, body').animate({
				scrollTop: $("#newThreadDiv").offset().top
			}, 500);
			return false;
		});
	});
	</script>
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
	<input type="submit" class="whiteButton" value="Login" />
</form> 
<?php } else { // logged in
	$api = new GpAPI();
	$posts = $api->Posts();
	$nextUnreadPostId = $api->PostNextUnreadID();
?>
<div class="panel" selected="true">
<input id="newThreadButton" value="New Thread" style="" type="button" />
<?php
	$menuSettings = array('nextUnreadPostId' => $nextUnreadPostId, 'returnToBoard' => FALSE);
	echo menu($menuSettings);
	display_posts($posts);
?>
<div id="newThreadDiv">
<h2>Create a New Thread</h2> 
<form id="postsubmit" class="panel" action="/postsubmit.php" method="POST" > 
	<table class="heading">
<?php
	if(!empty($_GET['e'])) {
?>
	<tr>
		<td align="center" colspan="2"><font color="red"><?php echo $_GET['e'] ?></font></td>
	</tr>
<?php } ?>
		<tr> 
			<th>Subject</th>
		</tr> 
		<tr>
			<td><input type="text" name="subject" value="<?php echo $subject; ?>" /></td>
		</tr>
		<tr>
			<th>Response</th>
		</tr>
		<tr>
			<td><textarea rows="5" name="response" style="width:100%"></textarea></td>
		</tr>
		<tr>
			<td><input type="submit" value="Submit" /></td>
		</tr>
	</table>
</form>
</div>
<?php 
	echo menu($menuSettings);
?>
</div>
<?php
} ?>

<?php echo footer(); ?>

</body>
</html>

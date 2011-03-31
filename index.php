<?php
	require_once 'util.php'; // does a session_start
	require_once 'gp-api.php';
	
	function newthread() {
		$html = '';
		$html .= '
			<h2>Create a New Thread</h2> 
			<form class="panel" action="/postsubmit.php" method="POST" > 
			<table class="heading">';
		if(!empty($_GET['e'])) {
			$html .= '
				<tr>
					<td align="center" colspan="2"><font color="red">'.$_GET['e'].'</font></td>
				</tr>';
		}
		
		$html .= '
				<tr> 
					<th>Subject</th>
				</tr> 
				<tr>
					<td><input type="text" name="subject" value="" /></td>
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
		</form>';
		return $html;
	}
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Greenpride Mobile</title>
	<?php echo head_links(); ?>
</head>
<body>
<?php
	
?>
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
<input id="newThreadToggle" type="button" value="New Thread" onclick="javascript:toggleDiv('newThread', 'newThreadToggle', 'New Thread', 'Hide New Thread')" />
<div id="newThread" style="display:none;">
<?php
	echo newthread();
?>
</div>
<?php
	$menuSettings = array('nextUnreadPostId' => $nextUnreadPostId, 'returnToBoard' => FALSE);
	echo menu($menuSettings);
	display_posts($posts);
	echo newthread();
?>



<?php 
	$menuSettings['showStats'] = TRUE;
	$menuSettings['logout'] = TRUE;
	echo menu($menuSettings);
?>
</div>
<?php
} ?>

<?php echo footer(); ?>

</body>
</html>

<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	
	$postid = $_GET['postid'];
	if(empty($postid) || !logged_in()) {
		redirect('/');
		return;
	}
	
	$api = new GpAPI();
	$post = $api->PostGet($postid);
	
	$api->PostMarkAs(1, 0, $postid);
	
	$parent_id = $post['ParentID'];
	if($parent_id != $postid) {
		$parent = $api->PostGet($parent_id);
	}
	else {
		$parent = null;
	}
	
	$subject = $post['Subject'];
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Greenpride Mobile</title>
	<?php echo head_links(); ?>
</head>
<body>
<div class="panel" selected="true">
<?php
	if(!empty($parent)) {
		echo '<i>';
		echo 'In response to: <BR><BR>';
		echo 'Author: '.$parent['AuthorName'];
		echo '<BR>';
		echo 'Subject: ' .$parent['Subject'];
		echo '<BR>';
		echo $parent['Description'];
		echo '<BR>';
		echo '</i>';
	}

	echo '<b>';
	echo 'Author: '.$post['AuthorName'];
	echo '<BR>';
	echo 'Subject: ' .$post['Subject'];
	echo '<BR>';
	echo $post['Description'];
	echo '<BR>';
	echo '</b>';
	
?>
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
			<textarea name="response" ></textarea>
		</div> 
	</fieldset> 
	<input type="submit" class="whiteButton" value="Submit" />
	<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
</form> 

<?php
	$nextUnreadPostId = $api->PostNextUnreadID();
	$menuSettings = array('nextUnreadPostId' => $nextUnreadPostId, 'returnToBoard' => TRUE);
	echo menu($menuSettings);
?>
<!--
<form method="GET" action="/">
<input type="submit" value="Return to Board" />
</form>
-->
</div>

<?php echo footer(); ?>

</body>
</html>

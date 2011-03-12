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
	<script language="javascript">
	function toggleParent() {
		//$('#parent').show();
		var text = ' Previous Message';
		if($('#parent').css('display') == 'none') {
			text = 'Hide' + text;
		}
		else {
			text = 'Show' + text;
		}
		$('#parentToggle').val(text);
		$('#parent').toggle();
	}
	</script
</head>
<body>
<div class="panel" selected="true">

<?php if(!empty($parent)) { ?>
<input id="parentToggle" type="button" class="whiteButton" value="Show Previous Message" onclick="javascript:toggleParent()" />
<div id="parent" class="parent" style="display:none; border: 1px solid #999999;">
<table>
	<tr>
		<td colspan="2" class="parent">In response to:</td>
	</tr>
	<tr></tr>	
	<tr>
		<td class="parentHeading">Author:</td>
		<td class="parent"><?php echo $parent['AuthorName']; ?></td>
	</tr>
	<tr>
		<td class="parentHeading">Subject:</td>
		<td class="parent"><?php echo $parent['Subject']; ?></td>
	</tr>
	<tr>
		<td class="parentHeading">Date:</td>
		<td class="parent"><?php echo formatDate($parent['Date']); ?></td>
	</tr>
	<tr>
		<td class="parent" colspan="2"><?php echo preg_replace('/\n/', '<BR>', $parent['Description']); ?></td>
	</tr>
</table>
</div>
<?php } ?>

<?php
	echo '<b>';
	echo 'Author: '.$post['AuthorName'];
	echo '<BR>';
	echo 'Subject: ' .$post['Subject'];
	echo '<BR>';
	echo 'Date: ' .formatDate($post['Date']);
	echo '<BR>';
	echo preg_replace('/\n/', '<BR>', $post['Description']);
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

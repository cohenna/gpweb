<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	
	$postid = $_GET['postid'];
	if(empty($postid) || !logged_in()) {
		rickroll('/');
		return;
	}
	
	$api = new GpAPI();
	$post = $api->PostGet($postid);
	$threadId = 0;
	#echo var_export($post, TRUE).'<BR>';
	#$threadId = $post[
	
	$api->PostMarkAs(1, 0, $postid);
	
	#$postsParams = array('ThreadID' => $post['ThreadID']);
	$threadPosts = $api->Posts($post['ThreadID']);
	
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
	function toggleThread() {
		var text = ' Thread';
		if($('#thread').css('display') == 'none') {
			text = 'Hide' + text;
		}
		else {
			text = 'Show' + text;
		}
		$('#threadToggle').val(text);
		$('#thread').toggle();
	}
	
	</script
</head>
<body>
<div class="panel" selected="true">


<input id="threadToggle" type="button" class="whiteButton" value="Show Thread" onclick="javascript:toggleThread()" />
<div id="thread" style="display:none; margin-left: 0; padding-left: 0; border: 1px solid #999999;">
<?php
	$settings = array(
		'currentPostId' => $postid,
	);
	echo display_posts($threadPosts, $settings);
?>
</div>
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
	$nextUnreadPostId = $api->PostNextUnreadID($post['ThreadID']);
	$menuSettings = array(
		'nextUnreadPostId' => $nextUnreadPostId, 
		'returnToBoard' => FALSE, 
		'search' => FALSE
	);
	echo menu($menuSettings);
	
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
	$menuSettings = array(
		'nextUnreadPostId' => $nextUnreadPostId, 
		'returnToBoard' => TRUE,
	);
	if(!empty($_GET['q'])) {
		$menuSettings['searchString'] = $_GET['q'];
		$menuSettings['backToSearchResults'] = TRUE;
	}
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

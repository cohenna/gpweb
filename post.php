<?php
	require_once 'util.php';
	require_once 'gp-api.php';
	
	$postid = array_safe_get('postid', $_GET, '');
	if(empty($postid) || !logged_in()) {
		rickroll('/');
		return;
	}
	
	$threadID = array_safe_get('tid', $_GET, 0);
	if($threadID > 0) {
		$threadToggleValue = 'Hide Thread';
		$threadDisplay = 'block';
	}
	else {
		$threadToggleValue = 'Show Thread';
		$threadDisplay = 'none';
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
	function toggleDiv(divid, buttonid, showtext, hidetext) {
		//var text = ' Previous Message';
		var text;
		if($('#' + divid).css('display') == 'none') {
			//text = 'Hide ' + text;
			text = hidetext;
		}
		else {
			//text = 'Show ' + text;
			text = showtext;
		}
		$('#' + buttonid).val(text);
		$('#' + divid).toggle();
	}	
	</script
</head>
<body>
<div class="panel" selected="true">


<?php if(!empty($parent)) { ?>
<input id="parentToggle" type="button" value="Show Previous Message" onclick="javascript:toggleDiv('parent', 'parentToggle', 'Show Previous Message', 'Hide Previous Message')" />
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
		'search' => FALSE,
	);
	echo menu($menuSettings);
?>
<div class="heading">
<table>
	<tr>
		<th>Author</th>
		<td><?php echo $post['AuthorName']; ?>
	</tr>
	<tr>
		<th>Subject</th>
		<td><?php echo $post['Subject']; ?>
	</tr>
	<tr>
		<th>Date</th>
		<td><?php echo formatDate($post['Date']); ?>
	</tr>
</table>
<div class="post">
<?php
	echo preg_replace('/\n/', '<BR>', $post['Description']);
?>
</div>
	

<input id="responseToggle" type="button" value="Respond to Post" onclick="javascript:toggleDiv('response', 'responseToggle', 'Respond to Post', 'Hide Respond to Post')" />
<input id="threadToggle" type="button" value="<?php echo $threadToggleValue; ?>" onclick="javascript:toggleDiv('thread', 'threadToggle', 'Show Thread', 'Hide Thread')" />

<div id="response" style="display:none; margin-left: 0; padding-left: 0; border: 1px solid #999999;">
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
	<input type="hidden" name="postid" value="<?php echo $postid; ?>" />
</form> 
</div>

<div id="thread" style="display:<?php echo $threadDisplay; ?>; margin-left: 0; padding-left: 0; border: 1px solid #999999;">
<?php
	$settings = array(
		'currentPostId' => $postid,
		'threadIDInQueryString' => TRUE,
	);
	echo display_posts($threadPosts, $settings);
?>
</div>

<?php
	$menuSettings = array(
		'showNextUnread' => FALSE, 
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

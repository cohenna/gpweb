<?php
	require_once 'util.php'; // does a session_start
	if(!logged_in()) {
		rickroll('/login.php');
	}
	
	
	require_once 'gp-api.php';
	
	
	$api = new GpAPI();
	$posts = $api->Posts();
	$postsbyuser = array();
	foreach($posts as $post) {
		$postsbyuser[$post['AuthorName']]++;
	}
	asort($postsbyuser, SORT_NUMERIC);
	$postsbyuser = array_reverse($postsbyuser);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
         "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Greenpride Mobile</title>
	<?php echo head_links(); ?>
</head>
<body>
<div class="panel" selected="true">
<?php
	$menuSettings = array();
	echo menu($menuSettings);
	echo '<div class="heading">';
	echo '<table>';
	echo '<tr>';
	echo '<th>Rank</th><th>User</th><th>Num Posts</th>';
	echo '</tr>';
	$i = 1;
	foreach($postsbyuser as $user=>$num_posts) {
		$currentUser = $_SESSION['user']['FullName'] === $user;
		if($currentUser) {
			$user = '<b><font color="purple">'.$user.'</font></b>';
			$i_display = '<b><font color="purple">'.$i.'</font></b>';
			$num_posts = '<b><font color="purple">'.$num_posts.'</font></b>';
		}
		else {
			$i_display = $i;
		}
		if($i & 1) {
			echo '<tr class="odd">';
		}
		else {
			echo '<tr>';
		}
		echo '<td>'.$i_display.'</td>';
		echo '<td>'.$user.'</td>';
		echo '<td>'.$num_posts.'</td>';
		echo '</tr>';
		$i++;
	}
	echo '</table>';
	echo '</div>';
	echo menu($menuSettings);
?>

</div>
<?php echo footer(); ?>
</body>
</html>
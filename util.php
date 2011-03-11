<?php
	define('IUI_WEB_ROOT', '/iui/web-app');
	define('IUI_ROOT', IUI_WEB_ROOT.'/iui');
	
	function logged_in() {
		session_start();
		return !empty($_SESSION['username']);
	}
	
	function redirect($url) {
		header( "Location: $url" ) ;
	}
	
	function b64_pad($b64_digest) {
		while (strlen($b64_digest) % 4) {
			$b64_digest .= '=';
		}
		return $b64_digest;
	}
	
	function display_posts($posts) {
	
		echo "
			<div class=\"panel\" selected=\"true\">
			<ul id=\"home\" title=\"Threads\" selected=\"true\">";
		foreach($posts as $post) {
			$subject = $post['Subject'];
			$postID = $post['PostID'];
			$author = $post['AuthorName'];
			$read = $post['Read'];
			$level = $post['Level'];
			$class = $read ? 'whiteButton' : 'redButton';
			echo "<li><a class=\"$class\" href=\"/post.php?postid=$postID\">";
			echo "$subject <i>by $author</i></a></li>
				";
		}
		echo "
			</ul>
			</div>";
	}
	
	function display_threads($t) {
		$threads = array();
		$unread = sizeof($t);
		foreach($t as $post) {
			$threadid = $post['ThreadID'];
			$read = $post['Read'];
			if(empty($threads[$threadid])) {
				$threads[$threadid] = $post;
				$threads[$threadid]['Unread'] = 0;
			}
			if($read) {
				$unread--;
			}
			else {
				$threads[$threadid]['Unread']++;
			}
			if($post['Sequence'] > $threads[$threadid]['Sequence']) {
				# latest post
				$threads[$threadid]['latestPost'] = $post;
			}
		}
	
		echo "
			<b><font>$unread unread</font></b><br>
			<ul id=\"home\" title=\"Threads\" selected=\"true\">";
		foreach($threads as $thread) {
			$subject = $thread['Subject'];
			$threadid = $thread['ThreadID'];
			$unread = $thread['Unread'];
			$author = $thread['AuthorName'];
			$class = $unread == 0 ? 'whiteButton' : 'redButton';
			echo "
				<li><a class=\"$class\" href=\"/thread.php?tid=$threadid\">$subject <i>by $author</i> - $unread unread</a></li>
				";
		}
		echo "
			</ul>";
	}
?>
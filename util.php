<?php
	define('IUI_WEB_ROOT', '/iui/web-app');
	define('IUI_ROOT', IUI_WEB_ROOT.'/iui');
	session_start();
	
	function logged_in() {
		return !empty($_SESSION['username']);
	}
	
	function menu($menuSettings=array()) {
		
		$nextUnreadPostId = 0;
		$returnToBoard = TRUE;
		if(array_key_exists('nextUnreadPostId', $menuSettings)) {
			$nextUnreadPostId = $menuSettings['nextUnreadPostId'];
		}
		if(array_key_exists('returnToBoard', $menuSettings)) {
			$returnToBoard = $menuSettings['returnToBoard'];
		}
		
		$html = '';
		
		if($returnToBoard) {
			$html .= '<input class="whiteButton" type="Button" onclick="javascript:document.location = \'/\';" value="Return to Board" />';
		}
		
		if($nextUnreadPostId) {
			$html .= '
			<form id="nextUnread" class="panel" action="/post.php" method="GET"> 
				<input type="hidden" name="postid" value="'.$nextUnreadPostId.'" />
				<input type="submit" class="redButton" value="Next Unread" />
			</form>';
		} else {
			$html .= '<input type="submit" disabled class="whiteButton" value="No Unread Posts" />';
		}
		return $html;
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
			</ul>";
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
			$class = 'whiteButton';
			if($unread > 0) {
				$class =  'redButton';
				$subject = "($unread) ".$subject;
			}
			echo "
				<li><a class=\"$class\" href=\"/thread.php?tid=$threadid\">$subject <i>by $author</i></a></li>
				";
		}
		echo "
			</ul>";
	}
?>
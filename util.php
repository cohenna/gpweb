<?php
	
	require_once 'conf.php';
	
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
		
		$html = '<div class="menu">';
		
		if($returnToBoard) {
			$html .= '<input class="whiteButton" type="Button" onclick="javascript:document.location = \'/\';" value="Return to Board" />';
		}
		
		if($nextUnreadPostId) {
			$api = new GpAPI();
			$unreadCount = $api->PostCountUnread();
			$html .= '
			<form id="nextUnread" class="panel" action="/post.php" method="GET"> 
				<input type="hidden" name="postid" value="'.$nextUnreadPostId.'" />
				<input type="submit" class="redButton" value="('.$unreadCount.') Next Unread" />
			</form>';
		} else {
			$html .= '<input type="submit" disabled class="whiteButton" value="No Unread Posts" />';
		}
		$html .= '</div>';
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
	
	function footer() {
		$html = '';
		$google_analytics_id = GOOGLE_ANALYTICS_ID;
		if(!empty($google_analytics_id)) {
			$html .= google_analytics($google_analytics_id);
		}
		return $html;
	}
	
	function google_analytics($id) {
		return "
<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$id']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>";
	}
	
	function formatDate($str, $short=FALSE) {
		// /Date(1299903748000-0700)/
		date_default_timezone_set('America/Chicago');
		if(preg_match("/\((?P<timestamp>.+)-(?P<offset>.+)\)/",
                                     $str,
                                     $matches)) {
			#$sitename = strtolower().".com";
			$time = $matches['timestamp']/1000;
			#echo "offset: ".$matches['offset']."<BR>";
			#$matches['offset'] /= 100;
			#echo "offset: ".$matches['offset']."<BR>";
			#$time += $matches['offset'] * 3600;
			#echo "time: ".$time."<BR>";
			if($short) {
				#$format = 'n/j G:i';
				$format = '%-m/%e %H:%M';
			}
			else {
				#3/11/2011 9:22:28 PM
				#$format = 'n/j/Y g:i A';
				$format = '%-m/%e/%Y %I:%M %p';
			}
			return strftime($format, $time);
		}
		#return strtotime($str);
		#return $str;
	}
	
	function head_links() {
		//<link rel="stylesheet" title="Default" href="'.IUI_ROOT.'/t/default/default-theme.css"  type="text/css" />
		#<link rel="stylesheet" href="'.IUI_WEB_ROOT.'/css/iui-panel-list.css" type="text/css" />
		return '
			<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/> 
			<link rel="stylesheet" href="/css/iui.css" type="text/css" />
			<link rel="stylesheet" href="/css/gp.css"  type="text/css"/>
			<link rel="apple-touch-icon" href="/apple-touch-icon.png"/>
			<script language="javascript" src="/js/jquery-1.5.1.min.js"></script>
			';
	}
	
	function get_initals($phrase) {
		$initals = '';
		$len = strlen($phrase);
		if($len) {
			$initials = $phrase[0];
			$space = FALSE;
			for($i = 1; $i < $len; $i++) {
				$c = trim($phrase[$i]);
				if(empty($c)) {
					$space = TRUE;
				}
				else {
					if($space) {
						$initials .= $c;
					}
					$space = FALSE;
				}
			}
		}
		return $initials;
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
			$date = formatDate($post['Date'], TRUE);
			$indent = 15*$level;
			$class = $read ? 'whiteButton' : 'redButton';
			if($level == 1) {
				$prefix = '-';				
			}
			else {
				$prefix = '';
				$indent += 10;
			}
			$author = get_initals($author);
			echo "<li><a style=\"margin-left:".$indent."px;\" class=\"$class\" href=\"/post.php?postid=$postID\">";
			echo "<span>$prefix <u>$subject</u></span> $date <i>$author</i></a></li>
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
			<ul id=\"home\" title=\"Threads\" selected=\"true\">";
		foreach($threads as $thread) {
			$subject = $thread['Subject'];
			$threadid = $thread['ThreadID'];
			$unread = $thread['Unread'];
			$author = $thread['AuthorName'];
			$author = get_initals($author);
			$class = 'whiteButton';
			if($unread > 0) {
				$class =  'redButton';
				$subject = "($unread) ".$subject;
			}
			echo "
				<li><a class=\"$class\" href=\"/thread.php?tid=$threadid\">$subject <i>$author</i></a></li>
				";
		}
		echo "
			</ul>";
	}
?>
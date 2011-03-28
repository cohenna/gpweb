<?php
	
	require_once 'conf.php';
	
	session_start();
	
	function logged_in() {
		return !empty($_SESSION['user']);
	}
	
	function array_safe_get($key, $array, $default) {
		if(array_key_exists($key, $array)) {
			return $array[$key];
		}
		return $default;
	}
	
	function menu($menuSettings=array()) {
		$showNextUnread = array_safe_get('showNextUnread', $menuSettings, TRUE);
		$nextUnreadPostId = array_safe_get('nextUnreadPostId', $menuSettings, 0);
		$returnToBoard = array_safe_get('returnToBoard', $menuSettings, TRUE);
		$search = array_safe_get('search', $menuSettings, TRUE);
		$backToSearchResults = array_safe_get('backToSearchResults', $menuSettings, FALSE);
		$searchString = array_safe_get('searchString', $menuSettings, '');
		
		$html = '<div class="menu">';
		
		if($backToSearchResults && !empty($searchString)) {
			$url = '/search.php?q='.$searchString;
			$html .= '<input type="Button" onclick="javascript:document.location = \''.$url.'\';" value="Back to Search Results" />';
		}
		
		if($search) {
			$html .= '<form action="search.php" method="get">';
			if(!empty($searchString)) {
				$html .= '<input type="text" name="q" value="'.$searchString.'" />';
			}
			else {
				$html .= '<input type="text" onclick="javascript:$(this).val(\'\');" name="q" value="Search" />';
			}
			$html .= '</form>';
		}
		
		if($returnToBoard) {
			$html .= '<input type="Button" onclick="javascript:document.location = \'/\';" value="Return to Board" />';
		}
		
		if($showNextUnread) {
			if($nextUnreadPostId) {
				$api = new GpAPI();
				$unreadCount = $api->PostCountUnread();
				$html .= '
				<form id="nextUnread" class="panel" action="/post.php" method="GET"> 
					<input type="hidden" name="postid" value="'.$nextUnreadPostId.'" />
					<input type="submit" style="background-color: red;" value="('.$unreadCount.') Next Unread" />
				</form>';
			}
			else {
				$html .= '<input type="submit" onclick="javascript:document.location = \'/\'" value="No Unread Posts" />';
			}
		}
		$html .= '</div>';
		return $html;
	}
	
	/**
	 * For documentation, please visit http://nickcohen.com/rickroll-docs
	 */
	function rickroll($url) {
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
			<script language="javascript" src="/js/jquery.scrollTo-1.4.2-min.js"></script>
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
	
	function display_posts($posts, $settings=array()) {
		$showIndent = TRUE;
		$searchString = '';
		$searchKeywords = array();
		$currentPostId = 0;
		
		$threadIDInQueryString = array_safe_get('threadIDInQueryString', $settings, FALSE);
		
		if(array_key_exists('currentPostId', $settings)) {
			$currentPostId = $settings['currentPostId'];
		}
		if(array_key_exists('searchString', $settings)) {
			$searchString = $settings['searchString'];
		}
		if(array_key_exists('searchKeywords', $settings)) {
			$searchKeywords = $settings['searchKeywords'];
		}
		if(array_key_exists('showIndent', $settings)) {
			$showIndent = $settings['showIndent'];
		}
		echo "
			<ul style=\"margin-left:0;padding-left:0;\" id=\"home\" title=\"Threads\" selected=\"true\">";
		foreach($posts as $post) {
			$subject = $post['Subject'];
			$postID = $post['PostID'];
			$author = $post['AuthorName'];
			$read = $post['Read'];
			$level = $post['Level'];
			$date = formatDate($post['Date'], TRUE);
			if($showIndent) {
				$indent = 15*$level;
				if($level == 1) {
					$prefix = '-';				
				}
				else {
					$prefix = '';
					$indent += 10;
				}
			}
			else {
				$indent = 0;
				$prefix = '';
			}
			$class = $read ? 'whiteButton' : 'redButton';
			$author = get_initals($author);
			$url = "/post.php?postid=$postID";
			if(!empty($searchString)) {
				$url .= '&q='.$searchString;
			}
			if($threadIDInQueryString) {
				$url .= '&tid='.$post['ThreadID'];
			}
			if(!empty($currentPostId) && $postID == $currentPostId) {
				$style = 'list-style: none;background-color: Lightgrey;';
			}
			else {
				$style = 'list-style: none;';
			}
			$user = array_safe_get('user', $_SESSION, null);
			$authorStyle = '';
			if(!empty($user) && array_safe_get('UserID', $user, 0) == $post['AuthorID']) {
				$authorStyle = 'color: purple;';
			}
			echo "<li style=\"$style\"><a style=\"margin-left:{$indent}px;\" class=\"$class\" href=\"$url\">";
			echo "<span>$prefix <u>$subject</u></span> <span style=\"$authorStyle\">$date <i>$author</i></a></span></li>
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
	
	function pretty_pretty($text) {
		$text = preg_replace('/\n/', '<BR>', $text);
		return $text;
	}
?>
<?php
	require_once 'util.php'; // does a session_start
	if(!logged_in()) {
		rickroll('/login.php');
	}
	
	
	require_once 'gp-api.php';
	
	function is_match($text, $keywords) {
		#echo "is_match($text, $keywords)<BR>";
		$matches = array();
		$textwords = explode(' ', $text);
		foreach($keywords as $k) {
			if(empty($k)) {
				continue;
			}
			foreach($textwords as $t) {
				if(empty($t)) {
					continue;
				}
				$sp = strpos($t, $k);
				if($sp !== FALSE
					|| levenshtein($k, $t) <= 1
					) {
					#echo "<b>strpos($t, $k) = ".$sp.'<BR>';
					#echo "levenshtein($k, $t) = ".levenshtein($k, $t).'<BR></b>';
					return TRUE;
					#array_push($matches, $t);
					#return $matches;
					#return array($t);
				}
				#echo "strpos($t, $k) = ".$sp.'<BR>';
			}
		}
		if(!empty($matches)) {
			#echo var_export($matches, TRUE).'<BR>';
		}
		#return $matches;
		return FALSE;
	}
	$searchString = $_GET['q'];
	$keywords = explode(' ', strtolower($searchString));
	$api = new GpAPI();
	$posts = $api->Posts();
	$postmatches = array();
	foreach($posts as $post) {
		#$matches = array();
		#$matches = array_merge($matches, get_matches(strtolower($post['Subject']), $keywords));
		#$matches = array_merge($matches, get_matches(strtolower(strip_tags($post['Description'])), $keywords));
		#$matches = array_merge($matches, get_matches(strtolower($post['Author']), $keywords));
		#echo var_export($matches, TRUE).'<BR>';
		#if(!empty($matches)) {
		#	array_push($postmatches, $post);
		#}
		if(is_match(strtolower($post['Subject']), $keywords)
			|| is_match(strtolower($post['Description']), $keywords)
			|| is_match(strtolower($post['Author']), $keywords)
		) {
			array_push($postmatches, $post);
		}
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
<div class="panel" selected="true">
<?php
	$settings = array(
		'showIndent' => FALSE, 
		'searchKeywords' => $keywords,
		'searchString' => $searchString,
	);
	echo display_posts($postmatches, $settings);
?>

<?php
	$nextUnreadPostId = $api->PostNextUnreadID();
	$menuSettings = array(
		'nextUnreadPostId' => $nextUnreadPostId, 
		'returnToBoard' => TRUE,
	);
	if(!empty($searchString)) {
		$menuSettings['searchString'] = $searchString;
		#$menuSettings['backToSearchResults'] = TRUE;
	}
	echo menu($menuSettings);
?>
</div>
</body>
</html>
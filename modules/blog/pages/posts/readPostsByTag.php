<?php
    
	$layout='2col_std';
	$body = null;
	$sideboxPosts = null;
	$sideboxUsers = null;
	
	$Users = new Users();
	$Posts = new Blog_Posts();
	
	try {
		$AllPosts = $Posts->getPostsByTag($id);
		$TagName  = $Posts->getTagName($id);
	}
	catch ( exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	//Hämtar ut alla inlägg
	$i = 1;
	foreach ($AllPosts as $post) {
		
		$extra = ($i == 1) ? "first" : null;
		$body .= post_Layout($post['id'], $post['header'], $post['content'], $post['date'], $post['author'], $post['authorId'], $post['comments'], null, $extra);
		if ($i <= 10) {
			$sideboxPosts .= "<a href='" . PATH_SITE . "/lasInlagg/id-$post[id]'>$post[header]</a> <br />";	
		}
		$i++;	
	}
	
	$body = "
		<div class='postsHolder'>
			<h1>$lang[POSTS_TAGGED_BY] $TagName[tagname]</h1> 
			$body
		</div>
	";
	
	
	$sideBoxFloat = "right";
	
	$sideBox = require_once(THIS_PATH . "sideboxes/latestPosts.php");
	$sideBox .= require_once(THIS_PATH . "sideboxes/allAuthors.php");
	$sideBox .= require_once(THIS_PATH . "sideboxes/allTags.php");

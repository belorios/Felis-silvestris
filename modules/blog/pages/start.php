<?php
    
	$layout='2col_std';
	$sideboxPosts = null;
	$sideboxUsers = null;
	
	$Posts = new Blog_Posts();
	$Users = new Users();
	
	try {
		$AllPosts = $Posts->getAllPosts(true);
		$PostsStat = $Posts->getPostsStat();
	}
	catch ( exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	//Writes out all blogposts
	$i = 1;
	$posts = null;
	foreach ($AllPosts as $post) {
		
		$extra = ($i == 1) ? "first" : null;
		$posts .= post_Layout($post['id'], $post['header'], $post['content'], $post['date'], $post['author'], $post['authorId'], $post['comments'], null, $extra);
		if ($i <= 10) {
			$sideboxPosts .= "<a href='" . PATH_SITE . "/readBlogPost/id-$post[id]'>$post[header]</a> <br />";	
		}
		
		$i++;	
	}
	
	
	$body = "<div style='float:left'>$posts</div>";
	
	$sideBox = require_once(THIS_PATH . "sideboxes/latestPosts.php");
	$sideBox .= require_once(THIS_PATH . "sideboxes/allAuthors.php");
	$sideBox .= require_once(THIS_PATH . "sideboxes/allTags.php");
	$sideBox .= require_once(THIS_PATH . "sideboxes/statistics.php");
	$sideBoxFloat = "right";
	
	

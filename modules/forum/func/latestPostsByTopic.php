<?php 
	
	require_once(PATH_MODULES . "forum/layout/html_elements.php");
	
	$Topics = new Topics();
	$Posts  = new Posts();
	$topicId = $_SESSION['posts']['topicId'];
	
	try {
		$getTopic  = $Topics->getTopic($topicId);
		$latestPost = $Posts->getPostsByTopic($topicId, 0, 5, "DESC");
	}
	catch ( Exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	//Shows latest post in thread 
	$allPosts = null;
	foreach($latestPost as $posts) {
		$allPosts .= forum_postsLayout($posts['id'], $posts['author'], $posts['title'], $posts['content'], $posts['date'], $posts['time'], $Users->getGravatar($posts['gravatar']));
	}
	
	return "
		<h2>Latest posts in the thread \"$getTopic[title]\"</h2>
		<div style='height: 150px; overflow: auto;'>
			$allPosts
		</div>
	";
	
	

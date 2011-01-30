<?php
	
	$Topics = new Topics();
	$Posts  = new Posts();
	$Users  = new Users();
	
	try {
		$getTopic = $Topics->getTopic($id);
		$getPosts = $Posts->getPostsByTopic($id);
	}
	catch ( Exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	require_once(PATH_MODULES . "forum/layout/html_elements.php");
	$allPosts = null;
	foreach($getPosts as $posts) {
		$edit = null;	
		if ($Users->checkUserRights($posts['authorId'], "adm") == true) {
			$edit = "<a href='".PATH_SITE."/editPost/id-$posts[id]'>$lang[EDIT]</a>";
		}
		$allPosts .= forum_postsLayout($posts['id'], $posts['author'], $posts['title'], $posts['content'], $posts['date'], $posts['time'], $Users->getGravatar($posts['gravatar']), $edit);
	}
	
	$body = "
		<h1>Showing the thread $getTopic[title]</h1>
		$allPosts
		<a href='".PATH_SITE."/newPost/id-{$id}'>Reply</a>
	";
	
	#$body .= postsFormSmall("Quick answer", $id);

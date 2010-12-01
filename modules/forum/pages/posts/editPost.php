<?php 

	$Users = new Users();
	$Users->checkPrivilegies();
	$Posts = new Posts();
	
	try {
		$getPost = $Posts->getPostById($id);
	}
	catch ( Exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	//Gets temporary data for the post
	$title   = (isset($_SESSION['posts']['title']))   ? $_SESSION['posts']['title']   : $getPost['title']; 
	$content = (isset($_SESSION['posts']['content'])) ? $_SESSION['posts']['content'] : $getPost['content']; 
	$_SESSION['posts']['topic'] = $getPost['topic'];
	
	//Creates the editform
	$body = postsForm("Editing post", "edit", $title, $content, $id);
	
	$body .= require_once(PATH_MODULES . "forum/func/latestPostsByTopic.php");
	
	

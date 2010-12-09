<?php 

	$Users = new Users();
	$Users->checkPrivilegies();
	$Posts = new Posts();
	
	try {
		$getPost = $Posts->getPostOrDraftById($id);
	}
	catch ( Exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	if (isset($_SESSION['posts']['topicId'])) {
		if ($_SESSION['posts']['topicId'] != $getPost['topic']) {
			$_SESSION['posts'] = array();
		}
	}
	
	$title   = (isset($_SESSION['posts']['title']))   ? $_SESSION['posts']['title']   : $getPost['title']; 
	$content = (isset($_SESSION['posts']['content'])) ? $_SESSION['posts']['content'] : $getPost['content']; 
	$_SESSION['posts']['topicId'] = (isset($_SESSION['posts']['topicId'])) ? $_SESSION['posts']['topicId'] : $getPost['topic'];
	$_SESSION['posts']['postId']  = (isset($_SESSION['posts']['postId']))  ? $_SESSION['posts']['postId']  : $id;
	
	//Gets temporary data for the post
	if (!empty($id) && $id != $_SESSION['posts']['postId']) {
		$title   = $getPost['title']; 
		$content = $getPost['content']; 
		$_SESSION['posts']['topicId'] = $getPost['topic'];
		$_SESSION['posts']['postId']  = $id;
	} 
	
	$PageClass->addJavascriptSrc("js/jgrowl/jquery.jgrowl.js", 	 PATH_SITE_LIBS);
	$PageClass->addJavascriptSrc("js/jquery.form.js", 			 PATH_SITE_LIBS);
	$PageClass->addJavascriptSrc("js/jquery.autosave.js", 		 PATH_SITE_LIBS);
	$PageClass->addJavascriptSrc("forum/js/ajax_handlePosts.js", PATH_SITE_MODS);
	
	$PageClass->addStyleSheet("jquery.jgrowl.css", PATH_SITE_LIBS . "js/jgrowl/");	
	
	//Creates the editform
	$body = postsForm("Editing post", "save-post", $title, $content, $id);
	
	$body .= "<div id='latest'>";
	$body .= require_once(PATH_MODULES . "forum/func/latestPostsByTopic.php");
	$body .= "</div>";
	

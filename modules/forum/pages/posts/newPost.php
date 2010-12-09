<?php 

	$Users = new Users();
	$Users->checkPrivilegies();
	
	$PageClass->addJavascriptSrc("js/jgrowl/jquery.jgrowl.js", 	 PATH_SITE_LIBS);
	$PageClass->addJavascriptSrc("js/jquery.form.js", 			 PATH_SITE_LIBS);
	$PageClass->addJavascriptSrc("js/jquery.autosave.js", 		 PATH_SITE_LIBS);
	$PageClass->addJavascriptSrc("forum/js/ajax_handlePosts.js", PATH_SITE_MODS);
	
	$PageClass->addStyleSheet("jquery.jgrowl.css", PATH_SITE_LIBS . "js/jgrowl/");	
	
	if (isset($_SESSION['posts']['topicId']) && !isset($_SESSION['posts']['newP'])) {
		$_SESSION['posts'] = array();
		$_SESSION['posts']['newP'] = true;
	}
	
	//Plockar ut tillfällig data
	$title   = (isset($_SESSION['posts']['title']))   ? $_SESSION['posts']['title']   : null; 
	$content = (isset($_SESSION['posts']['content'])) ? $_SESSION['posts']['content'] : null; 
	$_SESSION['posts']['topicId'] = (isset($_SESSION['posts']['topicId'])) ? $_SESSION['posts']['topicId'] : $id;
	
	
	//Skapar formen
	$body = postsForm("Creating new post", "save-post", $title, $content);
	$body .= require_once(PATH_MODULES . "forum/func/latestPostsByTopic.php");

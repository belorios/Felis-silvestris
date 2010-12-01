<?php 

	$Users = new Users();
	$Users->checkPrivilegies();
	
	//Plockar ut tillfällig data
	$title   = (isset($_SESSION['posts']['title']))   ? $_SESSION['posts']['title']   : null; 
	$content = (isset($_SESSION['posts']['content'])) ? $_SESSION['posts']['content'] : null; 
	$_SESSION['posts']['topic'] = $id;
	
	//Skapar formen
	$body = postsForm("Creating new post", "create", $title, $content, $id);
	$body .= require_once(PATH_MODULES . "forum/func/latestPostsByTopic.php");

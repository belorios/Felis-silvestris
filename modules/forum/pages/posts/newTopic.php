<?php 

	$Users = new Users();
	$Users->checkPrivilegies();
	
	//Plockar ut tillfällig data
	$title   = (isset($_SESSION['posts']['title']))   ? $_SESSION['posts']['title']   : null; 
	$content = (isset($_SESSION['posts']['content'])) ? $_SESSION['posts']['content'] : null; 
	
	//Skapar formen
	$body = postsForm("Creating new topic", "create", $title, $content);
	

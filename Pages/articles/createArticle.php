<?php
	
	$Users = new Users();
	$Users->checkPrivilegies();
	
	//Plockar ut tillfällig data
	$title   = (isset($_SESSION['article']['title']))   ? $_SESSION['article']['title']   : null; 
	$content = (isset($_SESSION['article']['content'])) ? $_SESSION['article']['content'] : null; 
	
	//Skapar formen
	require_once(PATH_FUNC . "forms.php");
	$body = articlesForm("Creating new article", "create", $title, $content);

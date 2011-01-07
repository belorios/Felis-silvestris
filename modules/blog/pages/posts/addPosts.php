<?php
	
	$Users = new Users();
	$Users->checkPrivilegies();
	
	//Plockar ut tillfällig data
	$header  = (isset($_SESSION['post']['header']))  ? $_SESSION['post']['header']  : null; 
	$content = (isset($_SESSION['post']['content'])) ? $_SESSION['post']['content'] : null; 
	$contTags = (isset($_SESSION['post']['tags'])) 	  ? $_SESSION['post']['tags'] 	: null;
	
	//Skapar formen
	require_once(PATH_FUNC . "forms.php");
	$body = postsForm($lang['CREATE_NEW_POST'], $lang['CREATE'], $header, $content, $contTags);

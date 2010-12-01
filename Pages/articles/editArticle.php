<?php
	
	$Users = new Users();
	$Users->checkPrivilegies();
	$Articles = new Articles();
	
	try {
		$getArticle = $Articles->getArticle($id);
	}
	catch ( exception $e ) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	//Plockar ut tillfällig data
	$title   = (isset($_SESSION['article']['title']))   ? $_SESSION['article']['title']   : $getArticle['title']; 
	$content = (isset($_SESSION['article']['content'])) ? $_SESSION['article']['content'] : $getArticle['content']; 
	
	//Skapar formen
	require_once(PATH_FUNC . "forms.php");
	$body = articlesForm("Editing an article", "edit", $title, $content, $id);

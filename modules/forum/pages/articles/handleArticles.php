<?php

	$defaults = new defaults;
	
	$body = null;
	
	//Adds an article
	if ($action == "create") {
		
		$Users = new Users();
		$Users->checkPrivilegies();
		$Articles = new Articles();
		
		$header   = $_POST['heading'];
		$content  = $_POST['content'];
		
		//Validerar inmatade värden
		if ($Articles->validateArticle($header, $content)) {
			
			//Skapar posten
			try {
				if ($_POST['add'] == "Save & show") {
					$Articles->createArticle($header, $content, true);
					$lastId = $Articles->getLastId();
					$body .= $defaults->redirect(PATH_SITE . "/article/id-$lastId", "2", "The article is now saved");
				}
				elseif ($_POST['add'] == "Save") {
					$Articles->createArticle($header, $content, true);
					$lastId = $Articles->getLastId();
					$body .= $defaults->redirect(PATH_SITE . "/editArticle/id-$lastId", "0", "The article is now saved");
				}
				
			}
			catch ( exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				header("Location: " . PATH_SITE . "/createArticle");
				exit;
			}
		}
		else {
			header("Location: " . PATH_SITE . "/createArticle");
			exit;
		}
		
	}
	
	//Edits an article
	if ($action == "edit") {
		
		$Users = new Users();
		$Users->checkPrivilegies();
		$Articles = new Articles();
		
		$title   = $_POST['heading'];
		$content = $_POST['content'];
		$thisUrl = "/editArticle/id-$id";
		
		//Validates the inputed data
		if ($Articles->validateArticle($title, $content)) {
			
			//Handles the inputet data and updates the article
			try {
				if ($_POST['add'] == "Save") {
					$Articles->editArticle($id, $title, $content, false);
					$body .= $defaults->redirect(PATH_SITE . $thisUrl, "0", "The article is now saved");
				}
				elseif ($_POST['add'] == "Save & show") {
					$Articles->editArticle($id, $title, $content);
					$body .= $defaults->redirect(PATH_SITE . "/article/id-$id", "2", "The article is now saved");
				}
				
			}
			catch ( exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				header("Location: " . PATH_SITE . $thisUrl);
				exit;
			}
		}
		else {
			header("Location: " . PATH_SITE . $thisUrl);
			exit;
		}
		
	}
	
	//Removes an article
	if ($action == "delete") {
		
		$Users = new Users();
		$Users->checkPrivilegies();
		
		if ($_POST['del'] == "No") {
			$body .= $defaults->redirect(PATH_SITE . "/article/id-$id", "0", "");
		}
		elseif ($_POST['del'] == "Yes") {
			$Articles = new Articles();
			//Raderar posten
			try {
				$Articles->delArticle($id);
				$body .= $defaults->redirect(PATH_SITE . "/hem", "3", "The article has now been removed");
			}
			catch ( exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				header("Location: " . PATH_SITE . "/deleteArticle/id-$id");
				exit;
			}
		}
		
	}
	
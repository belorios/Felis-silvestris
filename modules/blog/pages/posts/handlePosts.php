<?php
	
	$defaults = new defaults;
	
	$body = null;
	
	if ($action == "Create" || $action == "Edit" ) {
		$Users = new Users();
		$Users->checkPrivilegies();
		$Posts = new Blog_Posts();
	
		$header   = $_POST['heading'];
		$content  = $_POST['content'];
		$tags     = $_POST['tags'];
		
		$ErrUrl  = PATH_SITE;
		$ErrUrl .= ($action == "Edit") ?  "/editBlogPost/id-$id" : "/newBlogPost";
		
		if (!$Posts->validatePost($header, $content, $tags)) {
			header("Location: $ErrUrl");
			exit;
		}
		
		
	}
	
	//Lägger till en post
	if ($action == "Create") {
		
		//Skapar posten
		try {
			$Posts->addPost($header, $content, $tags);
			$id = $Posts->getLastId();
			$body .= $defaults->redirect(PATH_SITE . "/readBlogPost/id-$id", "2", $lang['POST_SAVED']);
		}
		catch ( exception $e ) {
			$_SESSION['errorMessage'][] = $e->getMessage();
			header("Location: $ErrUrl");
			exit;
		}
		
	}
	
	//Redigerar en tidigare post
	if ($action == "Edit") {
		
		//Matar in datan i db
		try {
			$Posts->editPost($id, $header, $content, $tags);
			$body .= $defaults->redirect(PATH_SITE . "/readBlogPost/id-$id", "3", $lang['POST_SAVED']);
		}
		catch ( exception $e ) {
			$_SESSION['errorMessage'][] = $e->getMessage();
			header("Location: $ErrUrl");
			exit;
		}
	}
	
	//Raderar en tidigare post
	if ($action == "delete") {
		
		if ($_POST['delete'] == "yes") {
			$Users = new Users();
			$Users->checkPrivilegies();
			$Posts = new Blog_Posts();
			
			//Raderar posten
			try {
				$Posts->delPost($id);
				$body .= $defaults->redirect(THIS_SITE_PATH, "3", $lang['POST_REMOVED']);
			}
			catch ( exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				header("Location: " . PATH_SITE . "/delBlogPost/id-$id");
				exit;
			}
		} 
		else {
			header("Location: " . PATH_SITE . "/readBlogPost/id-$id");
		}
		
	}
	
	//Skapar en kommentar
	if ($action == "createComment") {
		
		$Comments = new Blog_Comments();
		
		//Hämtar ut all data
		$header  	 = $_POST['heading'];
		$content 	 = $_POST['content'];
		$auhtorName  = $_POST['author'];
		$authorEmail = $_POST['email'];
		$authorSite  = $_POST['site'];
		$redirect    = PATH_SITE . "/readBlogPost/id-$id";
		
		//Validerar datan
		if ($Comments->validateComment($header, $content, $auhtorName, $authorEmail, $authorSite)) {
			
			//Matar in datan i db
			try {
				$Comments->addComment($id, $header, $content, $auhtorName, $authorEmail, $authorSite);
				$body .= $defaults->redirect($redirect, "2", $lang['COMMENT_SAVED']);
			}
			catch ( exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				header("Location: $redirect");
				exit;
			}
		}
		else {
			header("Location: $redirect");
			exit;
		}
	}
	
	//Tar bort en kommentar
	if ($action == "deleteComment") {
		
		$Users = new Users();
		$Users->checkPrivilegies();
		$Comments = new Blog_Comments();
		
		$redirect = $_SERVER['HTTP_REFERER'];;
		
		//Raderar den från db
		try {
			$Comments->delComment($id);
			$body .= $defaults->redirect($redirect, "3", $lang['COMMENT_REMOVED']);
		}
		catch ( exception $e ) {
			$_SESSION['errorMessage'][] = $e->getMessage();
			header("Location: $redirect");
			exit;
		}
	}

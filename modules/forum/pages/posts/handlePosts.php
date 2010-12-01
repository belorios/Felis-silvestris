<?php
	$defaults = new defaults;
	$body = null;
	
	//Adds a post/topic
	if ($action == "create") {
		
		$Users = new Users();
		$Users->checkPrivilegies();
		
		$Topics = new Topics();
		$Posts  = new Posts();
		$purifier = new HTMLPurifier();
		
		$header   = $_POST['heading'];
		$content  = $purifier->purify($_POST['content']);
		$page 	  = "/newPost/id-$id";
		$event 	  = "post";
				
		//Validates inputed values
		if ($Posts->validatePosts($header, $content)) {
			
			//Creates the posts/and topic
			try {
				
				//Checks if an id is givin, if not it creates a topic with the posts heading
				if (is_null($id)) {
					$Topics->createTopic($header);
					$id = $Topics->getLastId();
					$page = "/newTopic";
					$event = "topic";
				}
				$Posts->createPost($id, $header, $content);
				$lastId = $Posts->getLastId();
				$body .= $defaults->redirect(PATH_SITE . "/topic/id-$id#$lastId", "2", "The $event is now saved");
				
			}
			catch ( exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				header("Location: " . PATH_SITE . $page);
				exit;
			}
		}
		else {
			header("Location: " . PATH_SITE . $page);
			exit;
		}
		
	}
	
	//Edits a post
	if ($action == "edit") {
		
		$Users = new Users();
		$Users->checkPrivilegies();
		
		$Topics = new Topics();
		$Posts  = new Posts();
		$purifier = new HTMLPurifier();
		
		$header   = $_POST['heading'];
		$content  = $purifier->purify($_POST['content']);
		$topicId  = $_SESSION['posts']['topic'];
		$failpage = "/editPost/id-$id";
				
		//Validates inputed values
		if ($Posts->validatePosts($header, $content)) {
			
			//Creates the posts/and topic
			try {
				$Posts->editPost($id, $header, $content);
				$body .= $defaults->redirect(PATH_SITE . "/topic/id-$topicId#$id", "2", "The post is now saved");
				
			}
			catch ( exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				header("Location: " . PATH_SITE . $failpage);
				exit;
			}
		}
		else {
			header("Location: " . PATH_SITE . $failpage);
			exit;
		}
		
	}

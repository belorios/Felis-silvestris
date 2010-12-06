<?php
	$defaults = new defaults;
	$body = null;
	
	$Users = new Users();
	$Users->checkPrivilegies();
	
	$ajaxRequest = isset($_POST['ajax']) ? $_POST['ajax'] : false;

	function redirect($ajaxRequest, $path, $error=false) {
		if ($ajaxRequest == false) {
			if ($error != false) {
				$_SESSION['errorMessage'] = $error;
			}	
			header("Location: {$path}");
			exit;
		}
		else {
			foreach ($error as $err) {
				echo "$err <br />" ;
			}	
			exit;
		}
	}
	
	
	if ($action == "create" || $action == "edit") {
		$Topics = new Topics();
		$Posts  = new Posts();
		$purifier = new HTMLPurifier();
		
		$header   = $_POST['heading'];
		$content  = $purifier->purify($_POST['content']);
		
		//Validates inputed values
		$validate = $Posts->validatePosts($header, $content);
		
		//Checks if it should flush the sessions
		$flush = ($ajaxRequest != false) ? false : true;
	}
	
	//Adds a post/topic
	if ($action == "create") {
		
		$page 	  = "/newPost/id-$id";
		$event 	  = "post";
				
		if (count($validate) == 0) {
			//Creates the posts/and topic
			try {
				
				//Checks if an id is givin, if not it creates a topic with the posts heading
				if (is_null($id)) {
					$Topics->createTopic($header);
					$id = $Topics->getLastId();
					$page = "/newTopic";
					$event = "topic";
				}
				
				$Posts->createPost($id, $header, $content,  $flush);
				$lastId = $Posts->getLastId();
				
				if ($ajaxRequest != false) {
					$_SESSION['posts']['post'] = $lastId;
					echo $lastId;
					exit;
				}
				else {
					$body .= $defaults->redirect(PATH_SITE . "/topic/id-$id#$lastId", "2", "The $event is now saved");
				}
				
			}
			catch ( exception $e ) {
				redirect($ajaxRequest, PATH_SITE . $page, $e->getMessage());
			}
		}
		else {
			redirect($ajaxRequest, PATH_SITE . $page, $validate);
		}
		
	}
	
	//Edits a post
	if ($action == "edit") {
		
		$header   = $_POST['heading'];
		$content  = $purifier->purify($_POST['content']);
		$topicId  = $_SESSION['posts']['topic'];
		$failpage = "/editPost/id-$id";
				
		//Validates inputed values
		if (count($validate) == 0) {
			
			//Creates the posts/and topic
			try {
				$Posts->editPost($id, $header, $content, $flush);
				
				if ($ajaxRequest != false) {
					echo $id;
					exit;
				}
				else {
					$body .= $defaults->redirect(PATH_SITE . "/topic/id-$id#$lastId", "2", "The post is now saved");
				}
				
			}
			catch ( exception $e ) {
				redirect($ajaxRequest, PATH_SITE . $failpage, $e->getMessage());
			}
		}
		else {
			redirect($ajaxRequest, PATH_SITE . $failpage, $validate);
		}
		
	}

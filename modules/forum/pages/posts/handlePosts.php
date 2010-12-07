<?php
	$save = isset($_POST['save']) ? $_POST['save'] : null;
	
	if ($save == "discard") {
		if (isset($_SESSION['posts']['topicId'])) {
			header("Location: " . PATH_SITE);
			exit;
		}
		else {
			header("Location: " . PATH_SITE . "/topic/id-$_SESSION[posts][topicId]");
			exit;
		}
	}
	
	if ($action == "save-topic" || $action == "save-post") {
		
		$Topics   = new Topics();
		$Posts    = new Posts();
		$purifier = new HTMLPurifier();
		
		$header   = $_POST['heading'];
		$content  = $purifier->purify($_POST['content']);
		$ajax	  = isset($_POST['ajax']) ? true : false;
		
		$Pid	  = isset($_SESSION['posts']['postId'])  ? $_SESSION['posts']['postId']  : null;
		$Tid	  = isset($_SESSION['posts']['topicId']) ? $_SESSION['posts']['topicId'] : null;
		
		
		//Validates inputed values
		$validate = $Posts->validatePosts($header, $content);
		
		//Checks if it should flush the sessions
		$flush = true;
		if (isset($_POST['flush'])) {
			$flush = ($_POST['flush'] == 0) ? false : true;
		}
	}

	function handleError($path, $ajax=false, $error=false) {
		if ($ajax == false) {
			if ($error != false) {
				$_SESSION['errorMessage'] = $error;
			}	
			header("Location: {$path}");
			exit;
		}
		else {
			$fail = $error;
			if (is_array($error)) {
				$fail = implode("<br />", $error);
			}
			echo '{ "header": "Faults found", 
					"error": "' . $fail . '"
			}';
		}
		exit;
	}
	
	function success($message) {
		$Tid = $GLOBALS['Tid'];
		$Pid = $GLOBALS['Pid'];
		
		$returnPath = PATH_SITE . "/topic/id-$Tid#$Pid";
		if ($GLOBALS['ajax'] == true) {
			echo '{
				"path":    "' . $returnPath . '",
				"message": "' . $message . '",
				"PostId":  "' . $Pid . '",
				"TopicId": "' . $Tid . '"
			}';
			exit;
		}
		else {
			$time = 2;
			if ($GLOBALS['save'] == "draft"){
				$returnPath = $_SERVER['HTTP_REFERER'];
				$time = 0;
			}
			return $GLOBALS['defaults']->redirect($returnPath, $time, $message);
		}	
	}	
			
	if ($action == "save-topic") {
		if (count($validate) == 0) {
			try {
				if (isset($_SESSION['posts']['topicId'])) {
					$Topics->updateTopic($id, $header);
				}	
				else {
					$Topics->createTopic($header);
					$Tid = $Topics->getLastId();
					
				}
				if (is_null($Pid)) {
					$Posts->createPost($Tid, $header, $content, $flush);
					$Pid = $Posts->getLastId();
				}
				else {
					$Posts->editPost($Pid, $header, $content, $flush);
				}
				
				if ($flush != true) {
					$_SESSION['posts']['postId']  = $Pid;
					$_SESSION['posts']['topicId'] =	$Tid;
				}
				
				$body = success("Successfully saved the topic");
				
			}
			catch ( Exception $e) {
				handleError(PATH_SITE . "/newTopic", $ajax, $e->getMessage());
			}
		}
		else {
			handleError(PATH_SITE . "/newTopic", $ajax, $validate);
		} 
		
	}
	
	if ($action == "save-post") {
		
		if (count($validate) == 0) {
			try {
				if (is_null($Pid)) {
					$Posts->createPost($Tid, $header, $content, $flush);
					$Pid = $Posts->getLastId();
				}
				else {
					$Posts->editPost($Pid, $header, $content, $flush);
				}
				
				if ($flush != true) {
					$_SESSION['posts']['postId']  = $Pid;
					$_SESSION['posts']['topicId'] =	$Tid;
				}
				
				$body = success("Successfully saved the post");
			}
			catch ( Exception $e) {
				handleError(PATH_SITE . "/newPost", $ajax, $e->getMessage());
			}
		}
		else {
			handleError(PATH_SITE . "/newPost", $ajax, $validate);
		} 
		
	}

	
	

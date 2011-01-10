<?php
	
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
	
	function success($message, $path=false, $ajax) {
		
		$returnPath = ($path == false) ? PATH_SITE : $path;
		if ($ajax == true) {
			echo '{
				"path":    "' . $returnPath . '",
				"message": "' . $message . '",
			}';
			exit;
		}
		else {
			$time = 2;
			
			return $GLOBALS['defaults']->redirect($returnPath, $time, $message);
		}	
	}	
	
	$ajax = false;
	switch ($action) {
		
		case "register" :
			
			$page = PATH_SITE . "/register";
			$Users = new Users();
			
			if ($Users->validateUserInput($_POST['username'], $_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['password'], $_POST['password_conf'], $_POST['email_conf'])) {
				
			
				try {
					$Users->registerUser($_POST['username'], $_POST['fname'], $_POST['lname'], $_POST['email'], $_POST['password']);
					$body = success($lang['SUCCESS_REGISTER'], false, false);
				}
				catch ( Exception $e) {
					$_SESSION['errorMessage'] = $e->getMessage();
					header("Location: $page");
					exit;
				}
				
				
			}
		else {
			header("Location: $page");
			exit;
		}
			break;
	}

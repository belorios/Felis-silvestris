<?php

	$Users = new Users();
	
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
			
			if ($Users->validateUserInput($_POST['username'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['password_conf'], $_POST['email_conf'], $_POST['gravatar'])) {
				
			
				try {
					$Users->registerUser($_POST['username'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['gravatar']);
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
		case "edit" :
			$Users->checkPrivilegies(); 
			$page = PATH_SITE . "/editProfile/$_POST[username]";
			#echo $_POST['password'] . $_POST['password_conf'];
			if ($Users->validateUserInput($_POST['username'], $_POST['name'], $_POST['email'], $_POST['password'], $_POST['password_conf'], $_POST['email_conf'], $_POST['gravatar'], 'edit')) {
				
				if ($Users->passwdHash($_POST['oldPass']) == base64_decode($_SESSION['passhash'])) {
					try {
						$Users->editUser($id, $_POST['name'], $_POST['email'], $_POST['password'], $_POST['gravatar']);
						$body = success($lang['SUCCESS_REGISTER'], false, false);
					}
					catch ( Exception $e) {
						$_SESSION['errorMessage'] = $e->getMessage();
						header("Location: $page");
						exit;
					}
				}
				else {
					$_SESSION['errorMessage'][] = $lang['FAIL_CUR_TYPE_PASS'];
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

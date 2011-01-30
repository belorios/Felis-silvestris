<?php
	
	//Checks users rights to edit this
	$Users = new Users();
	
	if ($Users->checkUserRights($id, "adm", true) == false) {
		return;
	}
	
	//Picks the userdata from the database
	try {
		if (!isset($action)) {
			$userData = $Users->getUserData($id);
		}
		else {
			$userData = $Users->getUserDataByUsername($action);
			$id = $userData['idUsers'];
		}
		
	} 
	catch ( Exception $e ) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	if ($Users->ctlGroup("adm")) {
		$title = (preg_match("/[sxz]/i", substr($userData['username'], -1))) ? $userData['username'] : "{$userData['username']}s" ;
		$title = "Editing $title profile";
	}
	else {
		$title = "Editing your profile info";
	}
	
	//Handle sessions
	$username  = isset($_SESSION['regEdit']['username'])  ? $_SESSION['regEdit']['username']  : $userData['username'];
	$email     = isset($_SESSION['regEdit']['email'])     ? $_SESSION['regEdit']['email'] 	  : $userData['email'];
	$emailConf = isset($_SESSION['regEdit']['emailConf']) ? $_SESSION['regEdit']['emailConf'] : $userData['email'];
	$gravatar  = isset($_SESSION['regEdit']['gravatar'])  ? $_SESSION['regEdit']['gravatar']  : $userData['gravatar'];
	$name      = isset($_SESSION['regEdit']['name'])      ? $_SESSION['regEdit']['name'] 	  : $userData['realname'];
	
	//Writes out form
	$body = regEditForm($title, $username, $name, $email, $emailConf, $gravatar, 'edit', $id);

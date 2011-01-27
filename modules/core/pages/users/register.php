<?php
	
	//Loads classes
	$Users = new Users();

	//Handle sessions
	$username  = isset($_SESSION['regEdit']['username'])  ? $_SESSION['regEdit']['username']  : null;
	$email     = isset($_SESSION['regEdit']['email'])     ? $_SESSION['regEdit']['email'] 	  : null;
	$emailConf = isset($_SESSION['regEdit']['emailConf']) ? $_SESSION['regEdit']['emailConf'] : null;
	$gravatar  = isset($_SESSION['regEdit']['gravatar'])  ? $_SESSION['regEdit']['gravatar']  : null;
	$name      = isset($_SESSION['regEdit']['name'])      ? $_SESSION['regEdit']['name'] 	  : null;
	
	//Writes out form
	$body = regEditForm("Register new user", $username, $name, $email, $emailConf, $gravatar, 'register');
<?php
	
	//Loads classes
	$Users = new Users();

	//Handle sessions
	$username  = isset($_SESSION['regEdit']['username'])  ? $_SESSION['regEdit']['username']  : null;
	$email     = isset($_SESSION['regEdit']['email'])     ? $_SESSION['regEdit']['email'] 	  : null;
	$emailConf = isset($_SESSION['regEdit']['emailConf']) ? $_SESSION['regEdit']['emailConf'] : null;
	$gravatar  = isset($_SESSION['regEdit']['gravatar'])  ? $_SESSION['regEdit']['gravatar']  : null;
	$fname     = isset($_SESSION['regEdit']['fname'])     ? $_SESSION['regEdit']['fname'] 	  : null;
	$lname     = isset($_SESSION['regEdit']['lname'])     ? $_SESSION['regEdit']['lname'] 	  : null;
	
	//Writes out form
	$body = regEditForm("Register new user", $username, $fname, $lname, $email, $emailConf, $gravatar, 'register');
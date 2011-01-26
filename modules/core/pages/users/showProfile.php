<?php
	
	$Users = new Users();
	$Users->checkPrivilegies();
	
	
	
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
#substr($userData['username'], -1)	
	$h1Username = (preg_match("/[sxz]/i", substr($userData['username'], -1))) ? $userData['username'] : "{$userData['username']}s" ;
	
	
	
	$body = "
		
		<div style='float: right'><a href='" . PATH_SITE . "/editProfile/id-$id' />$lang[EDIT]</a></div>
		
		<h1>Looking at $h1Username profile</h1>
		
		
		
	";

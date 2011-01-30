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

	$h1Username = (preg_match("/[sxz]/i", substr($userData['username'], -1))) ? $userData['username'] : "{$userData['username']}s" ;
	
	$gravatar = $Users->getGravatar($userData['gravatar']);
	
	//Checks if it should show an edit button
	$editButton = ($Users->checkUserRights($id, "adm")) ? "<div style='float: right; margin: 5px;'><a class='submitbutton' href='" . PATH_SITE . "/editProfile/id-$id' />$lang[EDIT]</a></div>" : null;
		
	$body = "
		
		$editButton
		
		<h1>Looking at $h1Username profile</h1>
		
		<div id='profile'>
			<img id='profile_pic' src='$gravatar' alt='' />
			<table style='float:left; margin: 3px 10px'>
				<tr>
					<td class='first_element'>$lang[NAME]:</td>
					<td>{$userData['realname']}</td>
				</tr>
				<tr>
					<td class='first_element'>$lang[EMAIL]:</td>
					<td><a href='mailto:{$userData['email']}'>{$userData['email']}</a> </td>
				</tr>
			</table>
			<div style='clear:both;'></div>
		</div>
		
	";

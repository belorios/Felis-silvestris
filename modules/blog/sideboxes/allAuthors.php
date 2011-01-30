<?php

	/*********************
	 *	Sidebox showing all authors
	 */

	try {
		$AllUsers = $Users->getAllUsers(); 
	}
	catch ( exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	foreach ($AllUsers as $user) {
		$sideboxUsers .= "
			<a href='".PATH_SITE."/showUser/id-{$user['idUsers']}'>{$user['realname']} </a> 
			&nbsp;
			<span style='font-size: 9pt;color: #666;'>({$user['shortdesc']})</span><br />
		";
	}

	return 
		sideboxLayout($lang['AUTHORS'], "
			$sideboxUsers
		")
	;

<?php

	/*******************
	 * 	Sidebox for the managermodule
	 */

	
	$menu = new buildManagerMenu();
	
	try {
		$menuItems = $menu->buildManagerMenu(0);
	}
	catch ( Exception $e ) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	$return = null;
	
	foreach($menuItems as $item) {
		$return .= "<ul><a href='". PATH_SITE ."/$item[url]'>".$lang[$item['name']]."</a></ul>";
	}
	
	return "
		
		Meny!
		<li>
			$return
		</li>
	";

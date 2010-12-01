<?php

	$defaults = new defaults;
	$Pages = new CHTMLPage;
	
	switch ($action) {
		
		case "purple":
			if (isset($_SESSION['stylesheet'][PATH_CSS . "purple.css"])) {
				$_SESSION['stylesheet'] = array();
			}
			else {
				$Pages->addStyleSheet(PATH_CSS . "purple.css");
				$_SESSION['stylesheet'] = $Pages->getStyleSheets();
			}
			
		break;
		
		case "onecol":
			$_SESSION['Layout'] = "1col_std";
		break;
		
		case "twocol":
			$_SESSION['Layout'] = "2col_std";
		break;
		
	}
	
	header("Location: ".$_SERVER['HTTP_REFERER']);

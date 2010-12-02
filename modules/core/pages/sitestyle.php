<?php
	
	$defaults = new defaults;
	$Pages = new CHTMLPage;
	
	switch ($action) {
		
		case "purple":
			if (isset($_SESSION['stylesheet']["purple.css"])) {
				$_SESSION['stylesheet'] = array();
			}
			else {
				$Pages->addStyleSheet("purple.css", PATH_CSS);
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

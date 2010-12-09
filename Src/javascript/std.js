	
	
	var appName = "Felis-silvestris";
	var index = true;
	
	var urlParts = window.location.pathname.split("/");
	var appUrl = "http://" + window.location.hostname;
	for (var part in urlParts) {
		
		appUrl += urlParts[part] + "/";
		if (urlParts[part] == appName) {
			if (index == true) {
				appUrl += "index.php/";
			}
			break;
		}
		else {
			
		}
	}
	
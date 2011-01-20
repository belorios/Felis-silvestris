	
	
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
	
	function hideShowBox(boxId) {
		$(boxId).slideToggle('400');
	}
	
	
	
$(document).ready(function() {
		$(":submit").each(function(){
        	$(this).replaceWith("<a class='button submitbutton' id='" + $(this).attr('id') + "' rel='" + $(this).attr('rel') + "' href='' ><span>" + $(this).val() + "</span></a>");
        });
        $(":reset").each(function(){
        	$(this).replaceWith("<a class='button submitbutton' href='' ><span>" + $(this).val() + "</span></a>");
        });

		$(".submitbutton").click(function (event) {
	    	event.preventDefault();
	    	$('#' + $(this).attr('rel')).submit();
	    });
		
	});
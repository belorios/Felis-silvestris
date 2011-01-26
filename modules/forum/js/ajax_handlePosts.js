$(document).ready(function(){
	
	var flush 	 = 0;
	
	var autosave = {
		interval: 5000,
		
		save: function() {
			$('#postEditor').submit();
		},
		
		timer: null,
		
		stop: function() {
			$('#button_draft').attr('disabled', 'disabled');
			clearTimeout(autosave.timer);
			$('#postEditor').live('keyup', autosave.start);
		},
		
		start: function() {
			$('#postEditor').die('keyup');
			$('#button_draft').removeAttr('disabled');
			autosave.timer = setTimeout(autosave.save, autosave.interval);
			$.jGrowl('Timer started!');
		}
	}
	
	$('#postEditor').live('keyup', autosave.start);
	$('#button_draft').attr('disabled', 'disabled');
	
	$('#button_draft').click(function(event) {
		$('#postEditor').submit();
	});
	$('#button_publish').click(function(event) {
		
		flush 	 = 1;
		
		//Changes the hidden flush value to 1
		$('#flush').attr('value', '1');
		$('#postEditor').submit();
	});
	$('#button_discard').click(function(event) {
		$.post(appUrl + 'handlePosts/discard', { flush: 1, ajax: true }, function(data) {
			$.jGrowl("You are being redirected");
			setTimeout(function() {
				history.back();
			}, 1000);
		});
	});
	
	//handleForm(muff);	
	
	
	$('#postEditor').ajaxForm({
			dataType:  'json', 
			data: { ajax: true },
			success: function(data) {
				autosave.stop();
				$('div.jGrowl').find('div.jGrowl-notification').children().parent().remove();		//Removes previous notifications
				if (data.error !== undefined) {
					$.jGrowl(data.error, { header: data.header, sticky: true });
				}
				else {
					$.jGrowl(data.message);
					if (flush == 1) {
						$.jGrowl("You are being redirected");
						setTimeout(function() {
							window.location=data.path;
						}, 2000);
					}
				}
					
			}
		}); 	
	
	/*
	$('#postEditor').click(function(event) {
		var flush = 0;	
		var redirect = false;
		if ($(event.target).is('#button_publish')) {
			flush 	 = 1;
			redirect = true;
		} 
		else if ($(event.target).is('#button_draft')) {
			;
		} 
		else if ($(event.target).is('#button_discard')) {
			$.post(appUrl + 'handlePosts/discard', { flush: 1, ajax: true }, function(data) {
				$.jGrowl("You are being redirected");
				setTimeout(function() {
					history.back();
				}, 1000);
			});
			event.preventDefault();
			
			
		}
		
		handleForm(flush);
	});		
	*/
	
		
});

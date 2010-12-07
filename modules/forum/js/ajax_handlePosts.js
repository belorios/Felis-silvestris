$(document).ready(function(){
			
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
			//alert('discard');
			history.back();
		}
		
		handleForm(flush);
	});		
	
	function handleForm(flush) {
		
		$('#postEditor').ajaxForm({
			dataType:  'json', 
			data: { ajax: true, flush: flush },
			success: function(data) {
				$('div.jGrowl').find('div.jGrowl-notification').children().parent().remove();		//Removes previous notifications
				if (data.error !== undefined) {
					$.jGrowl(data.error, { header: data.header, sticky: true });
				}
				else {
					$.jGrowl(data.message);
					if (flush == true) {
						$.jGrowl("You are being redirected");
						setTimeout(function() {
							window.location=data.path;
						}, 2000);
					}
				}
					
			}
		}); 	
	}
		
});

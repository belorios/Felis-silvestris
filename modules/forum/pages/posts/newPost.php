<?php 

	$Users = new Users();
	$Users->checkPrivilegies();
	
	//Plockar ut tillfällig data
	$title   = (isset($_SESSION['posts']['title']))   ? $_SESSION['posts']['title']   : null; 
	$content = (isset($_SESSION['posts']['content'])) ? $_SESSION['posts']['content'] : null; 
	$_SESSION['posts']['topic'] = $id;
	$postId  = (isset($_SESSION['posts']['post'])) ? $_SESSION['posts']['post'] : 0;
	
	
	$PageClass->addJavascriptSrc("js/jgrowl/jquery.jgrowl.js", PATH_SITE_LIBS);
	$PageClass->addJavascriptSrc("js/jquery.autosave.js", PATH_SITE_LIBS);
	$PageClass->addStyleSheet("jquery.jgrowl.css", PATH_SITE_LIBS . "js/jgrowl/");
	
	//$PATH_CREATE = PATH_SITE_LOC . "/modules/forum/pages/posts/handlePosts.php?action=create";
	$PATH_CREATE = PATH_SITE . "/handlePosts/create/id-$id";
	$PATH_EDIT 	 = PATH_SITE . "/handlePosts/edit/id-";
	$js = <<<EOD
		$(document).ready(function(){
			var id = {$postId};	
			$('#postEditor').click(function(event) {
				if ($(event.target).is('#button_save')) {
					
					event.preventDefault();
				} 
				else if ($(event.target).is('#button_draft')) {
					
					event.preventDefault();
					if (id != 0) {
						var path = "{$PATH_EDIT}" + id;
					}
					else {
						var path = "{$PATH_CREATE}";
					}
					
					 
					$.post(path, { heading: $('#heading').val(), content: $('#content').val(), ajax: true },
				  	function(data){
				  		$('div.jGrowl').find('div.jGrowl-notification').children().parent().remove();		//Removes previous notifications
				    	if (/^\d*$/.test(data)) {
							id = data;
							$.jGrowl("Post saved");
						}
						else {
							
				    		$.jGrowl(data, { header: 'Faults found', sticky: true });							
						}
				   	});
					
				} 
				else if ($(event.target).is('button#discard')) {
					//alert('discard');
					history.back();
				}
			});		
		});
EOD;
	$PageClass->addJavascriptFunc($js);
	
	
	
	//Skapar formen
	$body = postsForm("Creating new post", "create", $title, $content, $id);
	$body .= require_once(PATH_MODULES . "forum/func/latestPostsByTopic.php");

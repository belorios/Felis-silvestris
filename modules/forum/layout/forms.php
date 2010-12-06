<?php

	function postsForm($header, $action, $title, $content, $id=false) {
		
		$idString = ($id != false) ? "/id-$id" : null;
		
		return "
			<h1>$header</h1>
			<div style='width: 605px'>
				<form method='post' id='postEditor' action='".PATH_SITE."/handlePosts/$action{$idString}'>
					<p>
						Title <input type='text' name='heading' id='heading' value='$title' />
						<div>
							<textarea name='content' class='editor' id='content' style='height: 350px; width: 100%;' >$content</textarea> 
						</div>
					</p>
					<div class='righty_buttons'>
						<button type='submit'  id='button_draft' class='wymupdate'>Save draft</button>
						<button type='submit'  id='button_publish' class='wymupdate'>Publish</button>
					</div>
					<div class='clear'></div>
				</form>
			</div>
		";
		
	}
	
	function postsFormSmall($header, $id=false) {
		
		$idString = ($id != false) ? "/id-$id" : null;
		
		return "
			<h2>$header</h2>
			<div style='width: 605px'>
				<form method='post' action='".PATH_SITE."/handlePosts/create{$idString}'>
					<p>
						Title <input type='text' name='heading' />
						<div>
							<textarea name='content' class='simpleeditor' style='width: 100%;'></textarea> 
						</div>
					</p>
					<div class='righty_buttons'>
						<input type='submit' name='add' value='Save' class='wymupdate' />
					</div>
					<div class='clear'></div>
				</form>
			</div>
		";
		
	}
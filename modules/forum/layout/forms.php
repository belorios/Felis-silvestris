<?php

	function postsForm($header, $action, $title, $content, $id=false) {
		
		$idString = ($id != false) ? "/id-$id" : null;
		
		return "
			<h1>$header</h1>
			<div style='width: 605px' id='forum_post' id='editore'>
				<form method='post' id='postEditor' action='".PATH_SITE."/handlePosts/$action{$idString}'>
					<p>
						Title <input type='text' name='heading' value='$title' />
						<textarea name='content' class='editor' style='height: 350px; width: 100%;' >$content</textarea> 
					</p>
					<div class='righty_buttons'>
						<button type='submit' class='nocssbutt' id='button_discard' rel='forum_post' name='save' value='discard'			>Discard</button>
						<button type='submit' class='nocssbutt wymupdate' id='button_draft'   rel='forum_post' name='save' value='draft' 	>Save draft</button>
						<button type='submit' class='nocssbutt wymupdate' id='button_publish' rel='forum_post' name='save' value='publish' 	>Publish</button>
					</div>
					<div class='clear'></div>
					<input type='hidden' id='flush' name='flush' value='0' />
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
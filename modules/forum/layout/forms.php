<?php

	function postsForm($header, $action, $title, $content, $id=false) {
		
		$idString = ($id != false) ? "/id-$id" : null;
		
		return "
			<h1>$header</h1>
			<div style='width: 605px'>
				<form method='post' action='".PATH_SITE."/handlePosts/$action{$idString}'>
						<p>
						Title <input type='text' name='heading' value='$title' />
						<div></div>
						<textarea name='content' class='editor' >$content</textarea> 
						</p>
					<div class='righty_buttons'>
						<input type='reset'  value='Empty' /> &nbsp;
						<input type='submit' name='add' value='Save' />
					</div>
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
						<div></div>
						<textarea name='content' class='simpleeditor' style='width: 100%; height: 140px;' ></textarea> 
						</p>
					<div class='righty_buttons'>
						<input type='reset'  value='Empty' /> &nbsp;
						<input type='submit' name='add' value='Save' />
					</div>
				</form>
			</div>
		";
		
	}
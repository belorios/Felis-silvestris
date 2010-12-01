<?php

	function articlesForm($header, $action, $title, $content, $id=false) {
		
		$idString = ($id != false) ? "/id-$id" : null;
		
		return "
			<h1>$header</h1>
			<div style='width: 405px'>
				<form method='post' action='".PATH_SITE."/handleArticles/$action{$idString}'>
						<p>
						Title <input type='text' name='heading' value='$title' />
						<div></div>
						<textarea name='content'>$content</textarea> 
						</p>
					<div class='righty_buttons'>
						<input type='reset'  value='Empty' /> &nbsp;
						<input type='submit' name='add' value='Save' />
						<input type='submit' name='add' value='Save & show' />
					</div>
				</form>
			</div>
		";
		
	}

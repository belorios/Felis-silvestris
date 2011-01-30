<?php

	function postsForm($header, $action, $title, $content, $tags, $id=false) {
		$lang = $GLOBALS['lang'];
		$idString = ($id != false) ? "/id-$id" : null;

		return "
			<h1>$header</h1>
			<div style='width: 100%' id='editore'>
				<form method='post' id='postEditor' action='".PATH_SITE."/handleBlogPosts/$action{$idString}'>
					<p>
						$lang[TITLE] <input type='text' name='heading' value='$title' />
						<textarea name='content' class='editor' style='height: 350px; width: 100%;' >$content</textarea> 
						<p>
							Tags (separate with ,)<br />
							<input type='text' style='width: 100%;' name='tags' value='$tags' />
						</p>
					</p>
					<div class='righty_buttons'>
						<button type='submit'  id='button_discard' name='save' value='discard'				  	>Discard</button>
						<button type='submit'  id='button_draft'   name='save' value='draft' class='wymupdate'	>Save draft</button>
						<button type='submit'  id='button_publish' name='save' value='publish' class='wymupdate'>Publish</button>
					</div>
					<div class='clear'></div>
				</form>
			</div>
		";
		
	}
	
	function commentsForm($id, $header, $comment, $name, $email, $site) {
			
		$lang = $GLOBALS['lang'];	
					
		return "
			
			<div style='width: 405px; margin: 20px 3px 0px 3px;'>
				<b>{$lang['WRITE_A_COMMENT']}</b>
				<form method='post' action='".PATH_SITE."/handleBlogPosts/createComment/id-$id'>
					
					<div style='float:left; margin: 0px 35px 0px 0px;'>
					<label>{$lang['TITLE']} <span class='markeddot'>(*)</span></label>
					<input type='text' name='heading' value='$header' /> <br />
					
					<label>{$lang['NAME']} <span class='markeddot'>(*)</span></label>
					<input type='text' name='author' value='$name' /> <br />
					</div>
					<div style='float:left;'>
					<label>{$lang['EMAIL']}</label> 
					<input type='email' name='email' value='$email' /> <br />
					
					<label>{$lang['WEBSITE']}</label>
					<input type='text' name='site' value='$site' /> <br />
					</div>
					<div style='clear:both;'></div>
					
					<label>{$lang['CONTENT']} <span class='markeddot'>(*)</span></label>
					<textarea name='content' style='height: 150px;'>$comment</textarea>
					
					<div class='righty_buttons'>
						<input type='reset'  value='{$lang['RESET']}' /> &nbsp;
						<input type='submit' name='addPost' value='{$lang['SEND']}' />
					</div>
					<div class='clear'></div>
				</form>
			</div>
		
		";
	}

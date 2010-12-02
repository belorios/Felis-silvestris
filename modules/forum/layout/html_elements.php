<?php

	function forum_postsLayout($id, $author, $title, $content, $edit=null) {
		return "
			<div class='forumpost' id='$id'>
				<div class='post_header'>$title</div>
				<div class='post_uInfo'>
					$author
					$edit
				</div>
				<div class='post_mess'>
					<div class='post_cont'>$content</div>
				</div>
			</div>
		";
	}

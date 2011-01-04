<?php

	function post_Layout($id, $header, $content, $date, $author, $authorId, $comments, $extra=null, $class=null) {
		
		if ($author != "") {
			$author = "<a href='".PATH_SITE."/visaAnvandare/id-$authorId'>$author</a>";
		}
		else {
			$author = "okänd författare";
		}
		
		$comment = "<span class='mark'>$comments</span> kommentar";
		if ($comments != 1) {
			$comment .= "er";
		}
		
		return "
			
			<h2 class='Post_header'><a href='".PATH_SITE."/lasInlagg/id-$id'>$header</a></h2>
			<div class='Post_comments'><a href='".PATH_SITE."/lasInlagg/id-$id#kommentera'>$comment</a></div>
			<div style='clear:both;'></div>
			<div class='$class Post '>
				<p class='nuller'>&nbsp;</p>
				<div class='date'>$date</div>
				<div class='content'>$content</div>
				<div class='Post_Footer'>Skrevs av $author </div>	
				
			</div>
			$extra
		";
	}
	
	function sidebox_Author($realname, $email, $group, $groupdesc) {
		 
		return sideboxLayout("Författaren", "
			<span class='mark'>$realname</span>
			<a href='mailto:$email' style='font-style: italic;'>$email</a>
			<p>
			Tillhör gruppen <span class='mark'>$group</span>, $groupdesc
			</p>
		");
	}
	

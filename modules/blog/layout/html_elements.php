<?php

	function post_Layout($id, $header, $content, $date, $author, $authorId, $comments, $extra=null, $class=null) {
		
		$lang = $GLOBALS['lang'];
		
		if ($author != "") {
			$author = "<a href='".PATH_SITE."/showUser/id-$authorId'>$author</a>";
		}
		else {
			$author = $lang['UNKNOWN_AUTHOR'];
		}
		
		$comment = "<span class='mark'>$comments</span> ";
		if ($comments != 1) {
			$comment .= $lang['COMMENTS'];
		}
		else {
			$comment .= $lang['COMMENT'];
		}
		
		return "
			
			<h2 class='Post_header'><a href='".PATH_SITE."/readBlogPost/id-$id'>$header</a></h2>
			<div class='Post_comments'><a href='".PATH_SITE."/readBlogPost/id-$id#kommentera'>$comment</a></div>
			<div style='clear:both;'></div>
			<div class='$class Post '>
				<p class='nuller'>&nbsp;</p>
				<div class='date'>$date</div>
				<div class='content'>$content</div>
				<div class='Post_Footer'>$lang[WRITTEN_BY] $author </div>	
				
			</div>
			$extra
		";
	}
	
	function sidebox_Author($realname, $email, $group, $groupdesc) {
		$lang = $GLOBALS['lang']; 
		return sideboxLayout($lang['AUTHOR'], "
			<span class='mark'>$realname</span>
			<a href='mailto:$email' style='font-style: italic;'>$email</a>
			<p>
			{$lang['BELONGS_TO_GROUP']} <span class='mark'>$group</span>, $groupdesc
			</p>
		");
	}
	
	function tagLayout($id, $tagname, $amount, $extra=null) {
		$href  = true;
		$color = "color: #2E5480;";
		$style = "font-weight: normal;";
		if ($amount == 0) {
			$href  = false;
			$color = null;
		}
		if ($amount >= 2) {
			$style = "font-weight: bold;";
		}
		if ($amount >= 5) {
			$style = "font-weight: bold; font-size: 14pt;";
		}
		if ($amount >= 10) {
			$style = "font-weight: bold; font-size: 16pt;";
		}
		
		$tagname = "<span style='$style $color'>$tagname</span>";
		
		$tags = ($href == true) ? "<a href='".PATH_SITE."/readBlogPostByTag/id-$id'>$tagname</a>" : $tagname;
		$tags .= "&nbsp; $extra ";
		
		return $tags;
	}
	

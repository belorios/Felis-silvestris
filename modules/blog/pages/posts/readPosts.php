<?php
   	
	require_once(PATH_FUNC . "forms.php");
	
	//Hämtar in klasser
	
	$Users 	  = new Users();
	#$defaults = new defaults();
	$Posts 	  = new Blog_Posts();
	$Comments = new Blog_Comments();	
	
	//Default variabler
	$comments   = null;
	$authorpost = null;
	$sideBox    = null;
	
	//Hämtar inlägget och dess kommentarer
	try {
		$getPost = $Posts->getPost($id);
		$getComm = $Comments->getComments($id);
		$getTags = $Posts->getTagsByPosts($id, false);
	}
	catch ( Exception $e) {
		$body = "
		<p class='warning'>{$e->getMessage()}</p>";
		return;
	}
	
	//Gets the last 10 posts by this posts author
	if ($getPost['authorId'] != null) {
		try {
			$UserData = $Users->getUserData($getPost['authorId']);
			$latest   = $Posts->getPostsByUser($getPost['authorId'], 10);
		}
		catch ( Exception $e) {
			$body = "<p class='warning'>{$e->getMessage()}</p>";
			return;
		}
		
		foreach ($latest as $post) {
			$authorpost .= "<a href='" . PATH_SITE . "/readBlogPost/id-$post[id]'>$post[header]</a> <br />";
		}
		
		$sideBox .= 
			sidebox_Author($UserData['realname'], $UserData['email'], $UserData['idGroups'], $UserData['groupdesc']) .
			sideboxLayout($lang['AUTHORS_POST'], "$authorpost")
		;
	}
	
	//Hämtar ut alla taggar
	if (count($getTags) > 0) {
		$tags = null;
		foreach ($getTags as $tag) {
			$tags .= tagLayout($tag['id'], $tag['tagname'], $tag['antal'], "<br />");
		}
		
		$sideBox .= sideboxLayout("Tagged with", "$tags"); 
	}
	
	
	//Skriver ut kommentarerna om det finns några
	$comAmount = count($getComm);
	if ($comAmount > 0) {
		foreach ($getComm as $comment) {
			
			//Skriver enbart ut email och hemsida om detta är angivet
			$name = ($comment['email'] != "") ? "<a href='mailto:$comment[email]'>$comment[name]</a>" : "<span class='name_color'>{$comment['name']}</a>";
			$site = ($comment['site'] != "") ? " @ " . $defaults->correctUrl($comment['site']) : null;
			
			$removeButton = ($Users->stdGroupsCtl($getPost['authorId'])) ? "<div style='float: right;'><a href='".PATH_SITE."/handleBlogPosts/deleteComment/id-$comment[id]'>$lang[DEL]</a></div>" : null;
			
			//Skriver ut själva kommentaren
			$comments .= "
					<div class='comments_header'>$comment[header]</div> 
					<div class='comments_date'>$comment[date]</div>
					<div class='clear'></div>
					<div class='comments'>
						<div class='comments_content'>
						$comment[content] <br />
						</div>
						<div class='comments_footer'>
						$lang[WRITTEN_BY] $name $site 
						$removeButton
					</div>
					</div>
					
			";
		}
	}
	else {
		$comments .= $lang['POST_MISSES_COMMENTS'];
	}
	
	$curUserName = null;
	$curUserMail = null;
	if ($Users->checkLoggedIn()) {
		try {
			$curUser = $Users->getUserData();
		}
		catch ( Exception $e) {
			$body = "<p class='warning'>{$e->getMessage()}</p>";
		}
		
		$curUserName = $curUser['realname'];
		$curUserMail = $curUser['email'];
	}
	
	$comHeader  = isset($_SESSION['comment']['header'])  ? $_SESSION['comment']['header']  : null;
	$comContent = isset($_SESSION['comment']['content']) ? $_SESSION['comment']['content'] : null;
	$comName	= isset($_SESSION['comment']['name'])	 ? $_SESSION['comment']['name']    : $curUserName;
	$comEmail	= isset($_SESSION['comment']['email'])   ? $_SESSION['comment']['email']   : $curUserMail;
	$comSite    = isset($_SESSION['comment']['site'])    ? $_SESSION['comment']['site']    : null;
	
	$comments = "
		<div id='postComments'>
			<h3 id='kommentera'>{$lang['COMMENTS']}</h3>
			$comments
		</div>
		<p></p>
	";
	
	$comments .= commentsForm($id, $comHeader, $comContent, $comName, $comEmail, $comSite) . "<p></p>"; 
	$body 	   = "
		<div style='float:left'> 
			" . post_Layout($id, $getPost['header'], $getPost['content'], $getPost['date'], $getPost['author'], $getPost['authorId'], $comAmount, $comments) . "
		</div>
	";
	
	//Visar en ruta för inloggad anvädare för att hantera egna inlägg (alla för admins)
	if ($Users->stdGroupsCtl($getPost['authorId'])) {
		$sideBox = sideboxLayout($lang['HANDLE_POSTS'], "
			<a href='".PATH_SITE."/editBlogPost/id-$id'>$lang[EDIT]</a>  <br />
			<a href='".PATH_SITE."/delBlogPost/id-$id'>$lang[DEL]</a>
		" ) . $sideBox;
	}

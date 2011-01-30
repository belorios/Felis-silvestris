<?php

   	$Users = new Users();
	$Posts = new Blog_Posts();
	
	//Hämtar ut data för posten 
	try {
		$getPost = $Posts->getPost($id);
		$getTags = $Posts->getTagsByPosts($id, false);
	}
	catch ( Exception $e) {
		$body = "
			<p class='warning'>{$e->getMessage()}</p>
		";
		return;
	}
	
	if ($Users->checkUserRights($getPost['authorId'], "adm", true) == false) {
		return;
	}
	
	//Hämtar ut taggar
	$tags = null;
	if (count($getTags) > 0) {
		$i = 1;
		foreach ($getTags as $tag) {
			$tags .= "$tag[tagname]";
			$tags .= ($i < count($getTags)) ? ", " : null;
			$i++;
		}
	}
	
	//Visar datan från hämtad post alternativt inskriven data om sådan finns
	$header   = (isset($_SESSION['post']['header']))  ? $_SESSION['post']['header']  : $getPost['header']; 
	$content  = (isset($_SESSION['post']['content'])) ? $_SESSION['post']['content'] : $getPost['content']; 
	$contTags = (isset($_SESSION['post']['tags'])) 	  ? $_SESSION['post']['tags'] 	 : $tags;
	
	//Hämtar och visar formuläret
	require_once(PATH_FUNC . "forms.php");
	$body = postsForm("Redigerar inlägget $header", "Edit", $header, $content, $contTags, $id);
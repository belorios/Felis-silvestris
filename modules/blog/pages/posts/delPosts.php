<?php
	
	$Users = new Users();
	$Users->checkPrivilegies();
	$Posts = new Blog_Posts();
	
	//Hämstar ut data för posten som ska tas bort
	try {
		$getPost = $Posts->getPost($id);
	}
	catch ( Exception $e) {
		$body = "
			<p class='warning'>{$e->getMessage()}</p>
		";
		return;
	}
	
	$header = $getPost['header']; 

	$body = "
		<h1>{$lang['REMOVE_POST_HEADER']}</h1>
		<p>
			{$lang['REMOVE_POST']} \"$header\"?
			<form method='post' action='".PATH_SITE."/handleBlogPosts/delete/id-$id'>
				<button name='delete' value='yes'>{$lang['YES']}</button>
				<button name='delete' value='no'>{$lang['NO']}</button>
			</form>
		</p>
	";

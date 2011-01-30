<?php
	
	$Users = new Users();
	$Users->checkPrivilegies();
	$Article = new Articles();
	
	//Gets the data for the article that is going to be removed
	try {
		$getArticle = $Article->getArticle($id);
	}
	catch ( Exception $e) {
		$body = "
			<p class='warning'>{$e->getMessage()}</p>
		";
		return;
	}

	$body = "
		<h1>Removing article</h1>
		<p>
			Are you shure you want to delete the article \"$getArticle[title]\"?
			<form method='post' action='".PATH_SITE."/handleArticles/delete/id-$id'>
				<input type='submit' name='del' value='Yes' />
				<input type='submit' name='del' value='No' />
			</form>
		</p>
	";

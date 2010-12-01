<?php

	$Articles = new Articles();
	$Users    = new Users();
	
	try {
		$getArticle = $Articles->getArticle($id);
	}
	catch ( exception $e ) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	$body = "
	
		<h1>$getArticle[title]</h1>
		<div>Skrevs den  $getArticle[date]</div>
		<p>
			$getArticle[content]
		</p>
		<i> Signerat $getArticle[author]  </i>
	";
	
	
	
	if ($getArticle['rights'] == 1) {
		$sideBox = sideboxLayout("Manage article", "
			<a href='".PATH_SITE."/editArticle/id-$id'>Edit</a>  <br />
			<a href='".PATH_SITE."/deleteArticle/id-$id'>Delete</a>
		" );
	}
	$sideBoxFloat = "right";
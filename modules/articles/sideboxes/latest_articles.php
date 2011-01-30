<?php

	$Articles = new Articles();
	$ArticleRows = null;
	try {
		$getArticles = $Articles->getAllArticles(10);
		
		foreach ($getArticles as $article) {
			$ArticleRows .= "
				<a href='".PATH_SITE."/article/id-$article[id]'>$article[title]</a> <br />
			";
		}
	}
	catch ( exception $e ) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	return sideboxLayout(
		"Latest articles", "
		$ArticleRows
	"); 
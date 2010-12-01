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
    
	$layout='2col_std';
	$body = "
		<h1>Lorem ipsum</h1>
		<p>
			Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed egestas congue velit, ut semper tortor sagittis nec. Phasellus vehicula eleifend felis ac consectetur. Duis enim mi, condimentum eget mattis vitae, facilisis in ipsum. Aenean nisl turpis, interdum eget laoreet eget, feugiat ut massa. Cras vel tellus elit, nec auctor risus. Suspendisse potenti. Nunc varius magna gravida tellus consequat id pellentesque ante sodales. Fusce id nibh ut leo vulputate consectetur bibendum non metus. Integer in est ac nibh rhoncus tristique id ut justo. Donec libero turpis, placerat et suscipit sit amet, molestie nec nunc. Donec congue tellus a diam vehicula luctus aliquam elit cursus. Cras magna massa, pulvinar quis blandit sit amet, consectetur id tortor. Ut velit mauris, faucibus porta pellentesque id, porttitor vitae erat. Suspendisse consectetur orci in mauris faucibus eleifend mollis arcu elementum. Sed vestibulum dui id massa imperdiet et luctus magna auctor.
		</p>
		<p>
			Sed venenatis, nunc et ornare luctus, dui ipsum fermentum orci, eu tincidunt risus justo at dolor. Proin pellentesque iaculis elit, id volutpat magna sollicitudin ac. Nunc sit amet felis felis. Sed quis nibh ultrices lectus egestas ultricies. Proin dolor turpis, vehicula ac cursus non, scelerisque at dolor. Sed ut dolor metus. Donec quis nibh a dui euismod sollicitudin. In nisl leo, dictum ac molestie in, luctus nec dui. Duis condimentum purus ac est dictum rhoncus. Suspendisse rhoncus, diam sed sollicitudin luctus, lectus lacus placerat ipsum, nec viverra ante mauris quis justo.
		</p>
		<p>
			Fusce scelerisque lacus sit amet enim rutrum semper. Quisque tortor quam, pretium eget elementum sit amet, blandit vitae diam. Ut viverra lectus non leo dapibus at sodales enim malesuada. Proin vestibulum facilisis arcu non faucibus. Nulla ac elementum mi. Donec lacinia eros accumsan erat mattis sed vulputate odio aliquam. Vestibulum cursus sem at odio mattis pulvinar. Phasellus congue ultricies tellus nec vehicula. Maecenas dignissim tincidunt sagittis. Duis mi est, placerat at elementum eget, volutpat sed sapien. Praesent semper diam nec felis porta ut dictum dui pulvinar. Aenean pharetra elit in nibh laoreet et vestibulum ipsum ultrices. Quisque laoreet eleifend nunc eu scelerisque. Duis posuere aliquam mi et dapibus. Curabitur volutpat aliquam diam vel pellentesque. Proin a mauris et dolor iaculis adipiscing. Nulla tincidunt, nulla ac rhoncus dignissim, magna ligula feugiat risus, et hendrerit felis eros facilisis nibh. Nullam orci sem, sodales at mollis quis, venenatis quis enim. 
		</p>
	";
	
	$sideBox = 
		sideboxLayout("Latest articles", "
			$ArticleRows
		") 
	;
	$sideBoxFloat = "right";
	
	

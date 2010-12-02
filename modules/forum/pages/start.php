<?php
	
	require_once(PATH_MODULES . "forum/layout/html_elements.php");
	
	$Topics = new Topics();
	
	try {
		$getTopic = $Topics->getAllTopics("Y-m-d");
	}
	catch ( Exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}

	$topics = null;
	foreach ($getTopic as $topic) {
		$topics .= forum_topicTableItems($topic['id'], $topic['postId'], $topic['time'], $topic['Updtime'], 
										 $topic['date'], $topic['date'], $topic['author'], $topic['postUser'], 
										 $topic['title'], $topic['answers']
		);
	}
	
	$allTopics = forum_topicTable($topics);
	
	$body = "
		<h1>Forum</h1>
		
		<h2>All topics</h2>
		$allTopics
	";
<?php

	$Topics = new Topics();
	
	try {
		$getTopic = $Topics->getAllTopics("d-m-Y");
	}
	catch ( Exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}

	$allTopics = "
		<table id='postsTable'>
			<tr id='header'>
				<td style='width: 75%;'>Topic</td>
				<td>Latest post</td>
				<td>Answers</td>
			</tr>
	";
	foreach ($getTopic as $topic) {
		$allTopics .= "
		<tr>
			<td>
				<a href='".PATH_SITE."/topic/id-$topic[id]'>$topic[title]</a> <br />
				<span style='font-size: 10pt'>Created by $topic[author] on $topic[date]</span>
			</td>
			<td>
				<a href='".PATH_SITE."/topic/id-$topic[id]#$topic[postId]'>
					<span style='font-size: 10pt'>
						$topic[updated] <br />
						$topic[postUser]
					</span>
				</a> 
			</td>
			<td>
				$topic[answers]
			</td>
		</tr>
			
			
		";
	}
	$allTopics .= "</table>";
	$body = "
		<h1>Forum</h1>
		
		<h2>All topics</h2>
		$allTopics
	";
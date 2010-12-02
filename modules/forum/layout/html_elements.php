<?php

	function forum_postsLayout($id, $author, $title, $content, $date, $time, $edit=null) {
		return "
			<div class='forumpost' id='$id'>
				<div class='post_header'>$title <div class='post_handle'>$edit</div></div>
				<div class='post_uInfo'>
					$author
					$date
					$time
				</div>
				<div class='post_mess'>
					<div class='post_cont'>$content</div>
				</div>
			</div>
		";
	}
	
	
	function forum_topicTable($items) {
		return "
			<table id='postsTable'>
				<tr id='header'>
					<td style='width: 70%;'>Topic</td>
					<td>Latest post</td>
					<td>Answers</td>
				</tr>
				$items
			</table>
		";
	}
	
	function forum_topicTableItems($tId, $pId, $cTime, $uTime, $cDate, $uDate, $cUser, $uUser, $title, $answers) {
		return "
			<tr>
				<td>
					<a href='".PATH_SITE."/topic/id-$tId'>
						$title <br />
						<span style='font-size: 10pt'>Created by $cUser on $cDate <span class='topicTime'>$cTime</span> </span>
					</a>
				</td>
				<td class='topicMiddle'>
					<a href='".PATH_SITE."/topic/id-$tId#$pId'>
						<span class='topicMiddle'>
							$uDate <span class='topicTime'>$uTime</span> <br />
							by $uUser
						</span>
					</a> 
				</td>
				<td>
					$answers
				</td>
			</tr>
		
		";
	}

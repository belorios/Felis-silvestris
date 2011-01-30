<?php

	class Blog_Posts extends Blog_Database{
		
		private $prefix, $dateformat;
		
		public function __construct($db=false) {
			parent::__construct($db);
			
			$this->dateformat = "H:m, j F Y";
			
		}
		
		//Hämtar ut datan från posterna och behandlar den
		public function returnPost($row, $dateformat, $small=false) {
			
			$defaults  = new defaults();
			
			if ($small == true) {
				$content = $defaults->shorten($row['content'], 500, "<p><a href='".PATH_SITE."/readBlogPost/id-$row[idPosts]'>{$this->lang['READ_MORE']}</a></p>");
			}
			else {
				$content = $row['content'];
			}
			
			$dateformat = ($dateformat == false) ? $this->dateformat : $dateformat;
			$date = $defaults->translateDate($dateformat, $row['creationDate']);
			$result = array(
				"id"  	   	=> $row['idPosts'],
				"authorId" 	=> $row['author'],
				"author"   	=> $row['realname'],
				"date"     	=> $date,
				"content"  	=> $content,
				"header"   	=> $row['header'],
				"timestamp" => $row['creationDate'],
			);
			
			if (isset($row['Comments'])) {
				$result["comments"] = $row['Comments'];
			}
			
			return $result;
		}
		
		public function getPostsStat() {
			
			$query = "
				SELECT creationDate FROM {$this->tablePosts}
			";
			
			$get = $this->db->prepare($query);
			$get->execute(); 
			
			$thisYear	= mktime(0,0,0,1,1,date('Y'));
			$thisMonth  = mktime(0,0,0,date('m'),1,date('Y'));
			$last10days = mktime(0,0,0,date('m'),date('d')-10,date('Y'));
			$return = array(
				"year"  => 0, 
				"ten"   => 0, 
				"month" => 0,
			);
			foreach ($get->fetchAll() as $row) {
				if ($row['creationDate'] >= $last10days) {
					$return['ten']++;
				}
				
				if ($row['creationDate'] >= $thisMonth) {
					$return['month']++;
				}
				
				if ($row['creationDate'] >= $thisYear) {
					$return['year']++;
				}
			}
			
			return $return;
			
			
			
		}
		
		//Hämtar ut alla posten efter en tag
		public function getPostsByTag($id, $limit=false, $dateformat=false) {
			
			$limit = ($limit != false) ? "LIMIT 0,$limit" : null;
		
			$get = $this->db->prepare("
				SELECT P.*, U.realname, COUNT(C.idComments) as Comments 
				FROM {$this->tablePosts} P 
				JOIN {$this->tableUsers} U on idUsers = P.author 
				LEFT JOIN {$this->tableComments} C on P.idPosts = C.idPosts
				JOIN {$this->tableTagsPosts} TP ON TP.idPosts = P.idPosts 
				WHERE TP.idTags = :id 
				GROUP BY P.idPosts 
				ORDER BY creationDate DESC
				$limit
			");
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
			
			if ($get->execute()) {
				$result = array();
				foreach ($get->fetchAll() as $row) {
					$result[] = $this->returnPost($row, $dateformat);
				}
				return $result;
			}
			else {
				$this->debug($this->lang['FAIL_CANNOT_GET_POSTS'], $query); 
				return false;
			}
		}
		
		//Hämtar ut alla posten efter användare
		public function getPostsByUser($id, $limit=false, $dateformat=false) {
			
			$limit = ($limit != false) ? "LIMIT 0,$limit" : null;
		
			$get = $this->db->prepare("
				SELECT P.*, U.realname, COUNT(C.idComments) as blogComments 
				FROM {$this->tablePosts} P 
				JOIN {$this->tableUsers} U on idUsers = P.author 
				LEFT JOIN {$this->tableComments} C on P.idPosts = C.idPosts 
				WHERE P.author = :id 
				GROUP BY P.idPosts 
				ORDER BY creationDate DESC
				$limit
			");
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
			
			if ($get->execute()) {
				$result = array();
				foreach ($get->fetchAll() as $row) {
					$result[] = $this->returnPost($row, $dateformat);
				}
				return $result;
			}
			else {
				$this->debug($this->lang['FAIL_CANNOT_GET_POSTS'], $query); 
				return false;
			}
		}
		
		//Hämtar ut en ensam post
		public function getPost($id, $dateformat=false) {
			$get = $this->db->prepare("
				SELECT P.*, U.realname 
				FROM {$this->tablePosts} P 
				LEFT JOIN {$this->tableUsers} U on idUsers = author 
				WHERE idPosts = ?
			");
			$get->execute(array($id)); 
			
			if ($result = $get->fetch()) {
				return $this->returnPost($result, $dateformat);
			}
			else {
				$this->debug($this->lang['FAIL_CANNOT_GET_POSTS'], $query); 
				return false;
			}
			
		}
		
		//Hämtar ut alla inlägg
		public function getAllPosts($small=false, $dateformat=false) {
			
			$query = "
				SELECT P.*, U.realname, COUNT(C.idComments) as Comments
				FROM {$this->tablePosts} P
				LEFT JOIN {$this->tableComments} C on P.idPosts = C.idPosts 
				LEFT JOIN {$this->tableUsers} U on idUsers = P.author 
				GROUP BY P.idPosts 
				ORDER BY creationDate DESC
			";
			
			$get = $this->db->prepare($query);
			$get->execute(); 
			
			$result = array();
			foreach ($get->fetchAll() as $row) {
				$result[] = $this->returnPost($row, $dateformat, $small);
			}
			
			return $result;
			
		}
		
		public function getAllTags($rand=true) {
					
			$rand = ($rand == true) ? "ORDER BY RAND()" : null;	
				
			$query = "
				SELECT T.tagname, T.idTags AS id, count(TP.idTags) AS antal FROM {$this->tableTags} T 
				LEFT JOIN {$this->tableTagsPosts} TP on TP.idTags = T.idTags
				GROUP BY T.idTags $rand
			";
			
			$get = $this->db->prepare($query);
			
			if ($get->execute()) {
				$result = array();
				foreach ($get->fetchAll() as $row) {
					$result[$row['tagname']] = $row;
				}
				return $result;
			}
			else {
				$this->debug($this->lang['FAIL_CANNOT_GET_TAGS'], $query); 
				return false;
			}
		}
		
		public function getTagsByPosts($idPosts, $rand=true) {
			$rand = ($rand == true) ? "ORDER BY RAND()" : null;	
				
			$query = "
				SELECT T.tagname, T.idTags AS id, count(TP.idTags) AS antal FROM {$this->tableTags} T 
				LEFT JOIN {$this->tableTagsPosts} TP on TP.idTags = T.idTags
				WHERE TP.idPosts = :id
				GROUP BY TP.idTags $rand
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $idPosts, PDO::PARAM_INT);
			
			if ($get->execute()) {
				$result = array();
				foreach ($get->fetchAll() as $row) {
					$result[] = $row;
				}
				return $result;
			}
			else {
				$this->debug($this->lang['FAIL_CANNOT_GET_TAGS'], $query); 
				return false;
			}
		}
		
		public function getTagName($idTags) {
				
			$query = "SELECT * FROM {$this->tableTags} WHERE idTags = :id"; 
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $idTags, PDO::PARAM_INT);
			
			if ($get->execute()) {
				return $get->fetch();
			}
			else {
				$this->debug($this->lang['FAIL_CANNOT_GET_TAGS'], $query); 
				return false;
			}
		}
		
		//Tar bort en post
		public function delPost($id) {
			$query = "
				DELETE FROM {$this->tablePosts} WHERE idPosts = :id
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
			//Kontrollerar så att databastransaktionen lyckades
			if (!$get->execute()) {
				$this->debug($this->lang['FAIL_CANNOT_DEL_POST'], $query); 
				return false;
			}
			else {
				$this->unsetSessions();
				$this->createRssFeed();
				return true;
			}
		}
		
		//Uppdaterar en tidigare post
		public function editPost($id, $header, $content, $tags) {
			
			$query = "
				UPDATE {$this->tablePosts} SET
					header  = :header,
					content = :content 
				WHERE idPosts = :id
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id',      $id, 	    PDO::PARAM_INT);
			$get->bindParam(':header',  $header, 	PDO::PARAM_STR);
			$get->bindParam(':content', $content, 	PDO::PARAM_STR);
			
			//Kontrollerar så att databastransaktionen lyckades
			if (!$get->execute()) {
				$this->debug($this->lang['FAIL_CANNOT_EDIT_POST'], $query); 
				return false;
			}
			else {
				$this->saveTags($tags, $id, true);
				$this->unsetSessions();
				$this->createRssFeed();
				return true;
			}
		}
		
		//Lägger till en post
		
		public function addPost($header, $content, $tags) {
			
			$author = $_SESSION['userId'];
			$date   = time();
			
			$query = "
				INSERT INTO {$this->tablePosts} (header, content, creationDate, author)
				VALUES (:header,:content,:date,:author)
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':header',  $header, 	PDO::PARAM_STR);
			$get->bindParam(':content', $content, 	PDO::PARAM_STR);
			$get->bindParam(':date', 	 $date, 	PDO::PARAM_INT);
			$get->bindParam(':author',  $author, 	PDO::PARAM_INT);
			
			
      		//Kontrollerar så att databastransaktionen lyckades
			if (!$get->execute()) {
				$this->debug($this->lang['FAIL_CANNOT_SAVE_POST'], $query); 
				return false;
			}
			else {
				$this->lastInsertedId = $this->db->lastInsertId();

				if (strlen($tags) > 0) {
					$this->saveTags($tags, $postId);
				}
				
				$this->unsetSessions();
				$this->createRssFeed();
				return true;
				
			}
			
		}
		
		private function saveTags($tags, $postId, $update=false) {
			$curTags = $this->getAllTags(false);
					
			$tags = str_ireplace(array(" , ", " ,", ", "), array(",",",",","), $tags);
			$tagParts = explode(",", $tags);
			
			if ($update == true) {
				$this->db->query("DELETE FROM {$this->tableTagsPosts} WHERE idPosts = $postId");
			}
			
			$queryTag  = "INSERT INTO {$this->tableTags} (tagname) VALUES (:tag)";
			$queryLink = "INSERT INTO {$this->tableTagsPosts} (idPosts, idTags) VALUES (:idPosts, :idTags)";
			$stmtTag   = $this->db->prepare($queryTag);
			$stmtlink  = $this->db->prepare($queryLink);
		
			foreach ($tagParts as $tag) {
				if (!array_key_exists($tag, $curTags)) {
	
					$stmtTag->bindParam(':tag',  $tag, PDO::PARAM_STR);
					$stmtTag->execute();
					
					$tagId = $this->db->lastInsertId();
				}
				else {
					$tagg  = $curTags[$tag];
					$tagId = $tagg['id'];
				}
				
				$stmtlink->bindParam(':idPosts', $postId, PDO::PARAM_INT);
				$stmtlink->bindParam(':idTags',  $tagId,  PDO::PARAM_INT);
				$stmtlink->execute();
			}	
		}
		
    //Validerar inmatad data
		public function validatePost($header, $content, $tags) {
			
			$this->savePost($header, $content, $tags);
			
			$Validation = new Validation();
			
			$fail = array();
			if (!$Validation->checkValues("Heading", $header, 3)) {
				$fail[] = $this->lang['VALIDATION_TITLE'];
			}
			
			if (!$Validation->checkValues("Length", $content, 20)) {
				$fail[] = $this->lang['VALIDATION_CONTENT'];
			}
			
			if (count($fail) > 0) {
				$_SESSION['errorMessage'] = $fail;
				return false;
			}
			else {
				return true;
			}
				
		}
		
    //Sparar inmatad post till sessioner
		public function savePost($header, $content, $tags) {
			$_SESSION['post']['header']  = $header;
			$_SESSION['post']['content'] = $content;
			$_SESSION['post']['tags']  	 = $tags;
		}
		
    //Dödar postens sessioner
		private function unsetSessions() {
			$_SESSION['post'] = array();
		}
		
		//Hämtar ut alla inlägg ur databasen och skapar en xml fil för rss flödet
		public function createRssFeed() {
			
			$defaults  = new defaults();
			$query = "SELECT * FROM {$this->tablePosts} ORDER BY creationDate DESC";
			
      //Plockar ut alla rader och börjar skriva in i filen
			if ($get = $this->db->query($query)) {
				//öppnar filen och trunkerar gammaldata  
				$file = fopen(PATH_RSS, "w");
				
				$items = null;
				foreach ($get->fetchAll() as $row) {
					
					$content = $defaults->shorten(strip_tags($row['content']), 250, "...");
					$date = date('D, d M Y H:i:s O',  $row['creationDate']);
			
					$items .= "
						\t\t<item>
							\t\t\t<title>{$row['header']}</title> \n
							\t\t\t<link>".PATH_SITE."/lasInlagg/id-$row[idPosts]</link> \n
							\t\t\t<description>{$content}</description> \n
							\t\t\t<pubDate>$date</pubDate> \n
						\t\t</item>
					";
				}
        
        //Skriver in allt i filen
				$date = date('D, d M Y H:i:s O', time());
				fwrite($file, "
					<rss version='1.0'> \n
						\t<channel>\n
							\t\t<title>Rss feed för bloggen \"".APP_HEADER."\" </title>\n
							\t\t<description>Denna är rss feed innehåller utsnitt från alla inlägg som görs i bloggen</description>\n
							\t\t<link>".PATH_SITE."</link>\n
							\t\t<lastBuildDate>$date</lastBuildDate>\n
							\t\t<pubDate>$date</pubDate>\n
							$items 
						\t</channel>\n
					</rss>\n
				");
			}
			
			
		}
		
	}

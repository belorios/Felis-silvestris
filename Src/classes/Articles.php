<?php

	class Articles {
		
		private $db, $prefix, $dateformat, $lastInsertedId;
		
		public function __construct($db=false) {
			if ($db != false) {
				$this->db = $db;	
			}
			else {
				$this->getConnection();
			}
			
			$this->prefix = DB_PREFIX;
			$this->dateformat = "H:m, j F Y";
		}
		
		public function getConnection() {
			if (!is_object($this->db)) {
				$pdo = new pdoConnection();
				$this->db = $pdo->getConnection();
			}
 		}
		
		private function debug($fail, $query) {
			if ($_SESSION['debug'] == true)
				$fail .= "<p>The faulty query: <br /> <b>$query</b></p>";
			throw new Exception ($fail);
		}
		
		public function getLastId() {
			return $this->lastInsertedId;
		}
		
		//Handles the data for an article
		public function returnArticle($row, $dateformat=false, $small=false) {
			
			$defaults  = new defaults();
			
			if ($small == true) {
				$content = $defaults->shorten($row['content'], 500, "<p><a href='".PATH_SITE."/article/id-$row[idPosts]'>Läs mer</a></p>");
			}
			else {
				$content = $row['content'];
			}
			
			$dateformat = ($dateformat == false) ? $this->dateformat : $dateformat;
			$date = $defaults->sweDate($dateformat, $row['created']);
			$result = array(
				"id"  	  => $row['idArticles'],
				"authorId" => $row['userId'],
				"author"   => $row['realname'],
				"date"     => $date,
				"content"  => $content,
				"title"   => $row['title'],
			);
			
			if (isset($row['rights'])) {
				$result['rights'] = $row['rights'];
			}
			
			return $result;
		}
		
		//Gets all articles created by selected User
		public function getArticlesByUser($id, $limit=false, $dateformat=false) {
			
			$limit = ($limit != false) ? "LIMIT 0,$limit" : null;
			$query = "
				SELECT A.*, U.realname
				FROM {$this->prefix}Articles A
				JOIN {$this->prefix}Users U on idUsers = P.userId 
				WHERE A.userId = :id 
				ORDER BY creationDate DESC
				$limit
			";	
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
			
			if ($get->execute()) {
				$result = array();
				foreach ($get->fetchAll() as $row) {
					$result[] = $this->returnArticle($row, $dateformat);
				}
				return $result;
			}
			else {
				$this->debug("Couldnt get the articles", $query);
				return false;
			}
		}
		
		//Gets an article
		public function getArticle($id, $dateformat=false) {
				
			$userID = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;	
			
			$query = "Call PDisplayArticle($id, $userID)";	
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
			
			if ($get->execute()) {
				$result = $this->returnArticle($get->fetch(), $dateformat);
				return $result;
			}
			else {
				$this->debug("Couldnt get the article", $query);
				return false;
			}
			
		}
		
		public function getAllArticles($limit=false, $dateformat=false) {
			
			$limit = ($limit != false) ? "$limit" : "1000";
			$query = "
				Call PListArticles($limit)
			";
			$get = $this->db->prepare($query);
			
			if ($get->execute()) {
				$result = array();
				foreach ($get->fetchAll() as $row) {
					$result[] = $this->returnArticle($row, $dateformat);
				}
				return $result;
			}
			else {
				$this->debug("Couldnt get the articles", $query);
				return false;
			}
		}
		
		//Removes an article
		public function delArticle($id) {
			$query = "
				DELETE FROM {$this->prefix}Articles WHERE idArticles = :id
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
			//Checks if the transaction to the database succeded
			if ($get->execute()) {
				$this->unsetSessions();
				$this->createRssFeed();
				return true;
			}
			else {
				$this->debug("Couldn't delete the article", $query);
				return false;
			}
		}
		
		//Updates an article
		public function editArticle($id, $title, $content, $flush=true) {
			
			$success = 0;
			$userID  = isset($_SESSION['userId']) ? $_SESSION['userId'] : 0;	
			$query  = "call PUpdateArticle(:id, :updated, :title, :content, :userId, @out)";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id',      $id, 	   PDO::PARAM_INT);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':updated', time(),    PDO::PARAM_INT);
			$get->bindParam(':content', $content, 	PDO::PARAM_STR);
			$get->bindParam(':userId',  $userID,   PDO::PARAM_INT);
			
			
			
			//Checks if the transaction to the database succeded
			if ($get->execute()) {
				$getSuccess = $this->db->query("SELECT @out");
				$fetSuccess = $getSuccess->fetch();
				$success = $fetSuccess['@out'];
				
				if ($success == 1) {
					if ($flush == true) {
						$this->unsetSessions();
					}	
					$this->createRssFeed();
					return true;
				}
				else {
					$this->debug("Couldnt save the edited article, permission denied", $query);
					return false;
				}
				
			}
			else {
				$this->debug("Couldnt save the edited article", $query);
				return false;
			}
		}
		
		//Adds an article
		public function createArticle($title, $content, $flush=true) {
			
			$user = $_SESSION['userId'];
			$date   = time();
			
			$query = "
				INSERT INTO {$this->prefix}Articles (title, content, created, updated, userId)
				VALUES (:title, :content, :created, :updated, :id)
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':content', $content, 	PDO::PARAM_STR);
			$get->bindParam(':created', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':updated', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':id',  	 $user, 		PDO::PARAM_INT);
			
      //Kontrollerar så att databastransaktionen lyckades
			if ($get->execute()) {
				if ($flush == true) {
					$this->unsetSessions();
				}
				$this->createRssFeed();
				$this->lastInsertedId = $this->db->lastInsertId();
				return true;
			}
			else {
				$this->debug("Couldnt save the new article", $query);
				return false;
			}
			
		}
		
    //Validates inserted data
		public function validateArticle($header, $content) {
			
			$this->savePost($header, $content);
			
			$Validation = new Validation();
			
			$fail = array();
			if (!$Validation->checkValues("Heading", $header, 3)) {
				$fail[] = "Rubriken har inte blivit korrekt inmatad (minst 3 tecken) ";
			}
			if (!$Validation->checkValues("Length", $content, 20)) {
				$fail[] = "Innehållet har inte blivit korrekt inmatat (minst 20 tecken) ";
			}
			
			if (count($fail) > 0) {
				$_SESSION['errorMessage'] = $fail;
				return false;
			}
			else {
				return true;
			}
				
		}
		
    	//Saves inserted data to sessions
		public function savePost($title, $content) {
			$_SESSION['article']['title']   = $title;
			$_SESSION['article']['content'] = $content;
		}
		
    	//Kills all sessions related to an article
		private function unsetSessions() {
			$_SESSION['article'] = array();
		}
		
		//Gets all articles and creates an rss xml file from them
		public function createRssFeed() {
			
			$defaults  = new defaults();
			$query = "SELECT * FROM {$this->prefix}Articles ORDER BY updated DESC";
			
      	//Gets all rows from database and starts write to the file
			if ($get = $this->db->query($query)) {
				//Opens the xml file and truncates all old data 
				$file = fopen(PATH_RSS, "w");
				
				$items = null;
				foreach ($get->fetchAll() as $row) {
					
					$content = $defaults->shorten(strip_tags($row['content']), 250, "...");
					$date = date('D, d M Y H:i:s O',  $row['updated']);
			
					$items .= "
						\t\t<item>
							\t\t\t<title>{$row['title']}</title> \n
							\t\t\t<link>".PATH_SITE."/article/id-$row[idArticles]</link> \n
							\t\t\t<description>{$content}</description> \n
							\t\t\t<pubDate>$date</pubDate> \n
						\t\t</item>
					";
				}
        
        //Writes everything to the file
				$date = date('D, d M Y H:i:s O', time());
				fwrite($file, "
					<rss version='1.0'> \n
						\t<channel>\n
							\t\t<title>Rss feed for \"".APP_HEADER."\" </title>\n
							\t\t<description>This rss feed holds parts off all the articles</description>\n
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

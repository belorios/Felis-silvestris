<?php

	class Posts {
		
		private $db, $prefix, $dateformat, $lastInsertedId;
		
		public function __construct($db=false) {
			if ($db != false) {
				$this->db = $db;	
			}
			else {
				$this->getConnection();
			}
			
			$this->prefix = DB_PREFIX;
			$this->dateformat = "Y-m-d";
		}
		
		public function getConnection() {
			if (!is_object($this->db)) {
				$pdo = new pdoConnection();
				$this->db = $pdo->getConnection(false);
			}
 		}
		
		//Checks if it should write out a debugmessage
		private function debug($fail, $query) {
			if ($_SESSION['debug'] == true)
				$fail .= "<p>The faulty query: <br /> <b>$query</b></p>";
			throw new Exception ($fail);
		}
		
		//Returns the last insertedid
		public function getLastId() {
			return $this->lastInsertedId;
		}
		
		//Handles the data for an post
		public function returnPost($row, $dateformat=false) {
			
			$defaults  = new defaults();
			
			$dateformat = ($dateformat == false) ? $this->dateformat : $dateformat;
			$date = $defaults->sweDate($dateformat, $row['created']);
			$result = array(
				"id"  	   => $row['idPosts'],
				"authorId" => $row['idUsers'],
				"author"   => $row['username'],
				"date"     => $date,
				"time"     => date("H:i", $row['updated']),
				"content"  => $row['post'],
				"title"    => $row['title'],
				"topic"	   => $row['idTopics'],
			);
			
			return $result;
		}
		
		public function getPostById($id) {
			
			$query = "
				SELECT P.*, U.username FROM {$this->prefix}Posts P 
				JOIN {$this->prefix}Users U ON U.idUsers = P.idUsers
				WHERE idPosts = :id LIMIT 1
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
      		//Checks if the transaction to the database succeded
			if ($get->execute()) {
				return $this->returnPost($get->fetch());
			}
			else {
				$this->debug("Couldnt get the post", $query);
				return false;
			}
		}
		
		public function getPostsByTopic($id, $limitStart=null, $limitAmount=30, $order="ASC") {
			
			$limit = (!is_null($limitStart)) ? "LIMIT {$limitStart},{$limitAmount}" : null;
			
			$query = "
				SELECT P.*, U.username FROM {$this->prefix}Posts P 
				JOIN {$this->prefix}Users U ON U.idUsers = P.idUsers
				WHERE idTopics = :id ORDER BY created $order $limit
			";
			
			$get = $this->db->prepare($query);
			
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
      		//Checks if the transaction to the database succeded
			if ($get->execute()) {
				$return = array();	
				foreach ($get->fetchAll() as $row) {
					$return[] = $this->returnPost($row);
				}	
				return $return;
			}
			else {
				$this->debug("Couldnt get the posts for the topic", $query);
				return false;
			}
		}
		
		public function createPost($idTopics, $title, $content, $flush=true) {
			
			$user = $_SESSION['userId'];
			$date   = time();
			
			$query = "
				INSERT INTO {$this->prefix}Posts (title, post, created, updated, idUsers, idTopics)
				VALUES (:title, :content, :created, :updated, :id, :topicId)
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':content', $content, 	PDO::PARAM_STR);
			$get->bindParam(':created', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':updated', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':id',  	$user, 		PDO::PARAM_INT);
			$get->bindParam(':topicId', $idTopics, 	PDO::PARAM_INT);
			
      		//Kontrollerar så att databastransaktionen lyckades
			if ($get->execute()) {
				if ($flush == true) {
					$this->unsetSessions();
				}
				#$this->createRssFeed();
				$this->lastInsertedId = $this->db->lastInsertId();
				return true;
			}
			else {
				$this->debug("Couldnt save the new post", $query);
				return false;
			}
			
		}

		public function editPost($idPosts, $title, $content, $flush=true) {
			
			$date   = time();
			
			$query = "
				UPDATE {$this->prefix}Posts SET
					title = :title, post = :content, updated = :updated
				WHERE idPosts = :id
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':content', $content, 	PDO::PARAM_STR);
			$get->bindParam(':updated', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':id', 		$idPosts, 	PDO::PARAM_INT);
			
      		//Kontrollerar så att databastransaktionen lyckades
			if ($get->execute()) {
				if ($flush == true) {
					$this->unsetSessions();
				}
				#$this->createRssFeed();
				$this->lastInsertedId = $this->db->lastInsertId();
				return true;
			}
			else {
				$this->debug("Couldnt save the updated post", $query);
				return false;
			}
			
		}

		//Validates inserted data
		public function validatePosts($header, $content) {
			
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
			$_SESSION['posts']['title']   = $title;
			$_SESSION['posts']['content'] = $content;
		}
		
		//Kills all sessions related to an article
		private function unsetSessions() {
			$_SESSION['posts'] = array();
		}
	}
<?php

	class Posts extends Forum_Database {
		
		private $dateformat;
		
		public function __construct($db=false) {
			parent::__construct($db);
			$this->dateformat = "Y-m-d";
		}
		
		//Handles the data for an post
		public function returnPost($row, $draft=false, $dateformat=false) {
			
			$defaults  = new defaults();
			
			if ($draft == true) {
				$row['username'] = null;
				$row['created']  = null;
			}
			
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
				SELECT P.*, U.username FROM {$this->tablePosts} P 
				JOIN {$this->tableUsers} U ON U.idUsers = P.idUsers
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
		
		public function getPostOrDraftById($id) {
			
			$query = "call {$this->procGetPost}(:id)";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
      		//Checks if the transaction to the database succeded
			if ($get->execute()) {
				return $this->returnPost($get->fetch(), true);
			}
			else {
				$this->debug("Couldnt get the post", $query);
				return false;
			}
		}
		
		public function getPostsByTopic($id, $limitStart=null, $limitAmount=30, $order="ASC") {
			
			$limit = (!is_null($limitStart)) ? "LIMIT {$limitStart},{$limitAmount}" : null;
			
			$query = "
				SELECT P.*, U.username FROM {$this->tablePosts} P 
				JOIN {$this->tableUsers} U ON U.idUsers = P.idUsers
				WHERE idTopics = :id AND Published = 1 ORDER BY created $order $limit
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
		
		public function discardPost($idPosts) {
			$query = "call {$this->procDiscardPost}(:postId)";
			$get = $this->db->prepare($query);
			$get->bindParam(':postId',   $idPosts, 	PDO::PARAM_INT);
			if ($get->execute()) {
				$this->unsetSessions();
				return true;
			}
			else {
				$this->debug("Couldnt save the new post", $query);
				return false;
			}
		}
		
		public function editCreatePost($idPosts, $idTopics, $title, $content, $flush=true) {
			
			$user = $_SESSION['userId'];
			$date   = time();
				
			$query = "call {$this->prefix}handleDraftPost(:postId, :title, :content, :created, :userId, :topicId, @outId)";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':content', $content, 	PDO::PARAM_STR);
			$get->bindParam(':created', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':userId', 	$user, 		PDO::PARAM_INT);
			$get->bindParam(':topicId', $idTopics, 	PDO::PARAM_INT);
			$get->bindParam(':postId',  $idPosts, 	PDO::PARAM_INT);
			
      		//Kontrollerar så att databastransaktionen lyckades
			if ($get->execute()) {
				if ($flush == true) {
					$query = "call {$this->prefix}publishPost(:postId);";
					$get = $this->db->prepare($query);
					$get->bindParam(':postId', $idPosts, PDO::PARAM_INT);
					if (!$get->execute()) {
						$this->debug("Couldnt save the new post", $query);
						return false;
					}
					else {
						$this->unsetSessions();
					}
				}
					$getId = $this->db->query("SELECT @outId");
					$fetId = $getId->fetch();
					$this->lastInsertedId = $fetId['@outId'];
				
				return true;
			}
			else {
				$this->debug("Couldnt save the new post", $query);
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
			
			return $fail;
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
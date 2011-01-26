<?php

	class Topics extends Forum_Database {
		
		private $dateformat;
		
		public function __construct($db=false) {
			parent::__construct($db);
			$this->dateformat = "H:m, j F Y";
		}
		
		//Handles the data returned from a topic
		public function returnTopic($row, $dateformat=false) {
			
			$defaults  = new defaults();
			
			$dateformat = ($dateformat == false) ? $this->dateformat : $dateformat;
			$date = $defaults->sweDate($dateformat, $row['created']);
			$result = array(
				"id"  	   => $row['idTopics'],
				"authorId" => $row['idUsers'],
				"author"   => $row['username'],
				"date"     => $date,
				"time"     => date("H:m", $row['created']),
				"title"    => $row['title'],
			);
			
			if (isset($row['updated'])) {
				$result['updated']  = $defaults->sweDate($dateformat, $row['updated']);
				$result['Updtime']  = date("H:i", $row['updated']);
				$result['postId']   = $row['postId'];
				$result['answers']  = $row['rows'];
			}
			
			return $result;
		}
		
		public function getTopic($id) {
			
			$query = "
				SELECT T.*, U.username FROM {$this->tableTopics} T 
				JOIN {$this->tableUsers} U ON U.idUsers = T.idUsers
				WHERE idTopics = :id LIMIT 1
			";
			
			$get = $this->db->prepare($query);
			
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
      		//Checks if the transaction to the database succeded
			if ($get->execute()) {
				return $this->returnTopic($get->fetch());
			}
			else {
				$this->debug("Couldnt save the new article", $query);
				return false;
			}
		}
		
		public function getAllTopics($dateformat=false, $limitStart=false, $limitAmount=30) {
			
			$limit = ($limitStart != false) ? "{$limitStart},{$limitAmount}" : null;
			
			 $query = "
			 	SELECT 
			 		T.*, 
			 		U1.username, 
			 		COUNT(P.idPosts) as rows, 
			 		MAX(P.created) as updated,
			 		MAX(P.idPosts) as postId 
			 	FROM {$this->tableTopics} T 
				JOIN {$this->tableUsers} U1 ON U1.idUsers = T.idUsers
				JOIN {$this->tablePosts} P ON P.idTopics = T.idTopics AND P.Published = 1
				GROUP BY idTopics ASC
				ORDER BY updated DESC
				$limit			
			";
			
			#echo $query;
			
			$get = $this->db->prepare($query);
			
      		//Checks if the transaction to the database succeded
			if ($get->execute()) {
				$return = array();	
				foreach ($get->fetchAll() as $row) {
					$return[] = $this->returnTopic($row, $dateformat);
				}	
				return $return;
			}
			else {
				$this->debug("Couldnt save the new article", $query);
				return false;
			}
			
		}
		
		public function getPostUserName($idPosts) {
			
			$query = "
				SELECT username FROM {$this->tableUsers} U 
				JOIN {$this->tablePosts} P ON P.idUsers = U.idUsers
				WHERE idPosts = :id
			
			";
			
			$get = $this->db->prepare($query);
			
			$get->bindParam(':id', $idPosts, PDO::PARAM_INT);
			
			//Checks if the transaction to the database succeded
			if ($get->execute()) {
				return $get->fetch();
			}
			else {
				$this->debug("Couldnt get the username", $query);
				return false;
			}
		}
			
		public function createTopic($title) {
			
			$user = $_SESSION['userId'];
			$date   = time();
			
			$query = "
				INSERT INTO {$this->tableTopics} (title, created, idUsers)
				VALUES (:title, :created, :id)
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':created', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':id',  	$user, 		PDO::PARAM_INT);
			
     		 //Controls if the databasetransation succeded
			if ($get->execute()) {
				$this->lastInsertedId = $this->db->lastInsertId();
				return true;
			}
			else {
				$this->debug("Couldnt save the new topic", $query);
				return false;
			}
		}
		
		public function updateTopic($id, $title) {
			
			$query = "
				UPDATE {$this->tableTopics} SET title = :title WHERE idTopics = :id
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':id',  	$id, 		PDO::PARAM_INT);
			
			 //Controls if the databasetransation succeded
			if ($get->execute()) {
				$this->lastInsertedId = $this->db->lastInsertId();
				return true;
			}
			else {
				$this->debug("Couldnt save the topic", $query);
				return false;
			}
		}
		
		
	}
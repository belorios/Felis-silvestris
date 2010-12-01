<?php

	class Topics {
		
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
				"title"    => $row['title'],
			);
			
			if (isset($row['updated'])) {
				$result['updated']  = $defaults->sweDate($dateformat, $row['updated']);
				$result['postUser'] = $row['postUser'];
				$result['postId']   = $row['postId'];
				$result['answers']  = $row['rows'];
			}
			
			return $result;
		}
		
		public function getTopic($id) {
			
			$query = "
				SELECT T.*, U.username FROM {$this->prefix}Topics T 
				JOIN {$this->prefix}Users U ON U.idUsers = T.idUsers
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
			 		U2.username as postUser, 
			 		COUNT(P.idPosts) as rows, 
			 		MAX(P.created) as updated,
			 		P.idPosts as postId 
			 	FROM {$this->prefix}Topics T 
				JOIN {$this->prefix}Users U1 ON U1.idUsers = T.idUsers
				JOIN {$this->prefix}Posts P ON P.idTopics = T.idTopics 
				JOIN {$this->prefix}Users U2 ON U2.idUsers = P.idUsers
				GROUP BY idTopics 
				ORDER BY updated 
				$limit			
			";
			
			$get = $this->db->prepare($query);
			
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
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
			
		public function createTopic($title) {
			
			$user = $_SESSION['userId'];
			$date   = time();
			
			$query = "
				INSERT INTO {$this->prefix}Topics (title, created, idUsers)
				VALUES (:title, :created, :id)
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':title',   $title, 	PDO::PARAM_STR);
			$get->bindParam(':created', $date,	 	PDO::PARAM_INT);
			$get->bindParam(':id',  	$user, 		PDO::PARAM_INT);
			
      //Kontrollerar så att databastransaktionen lyckades
			if ($get->execute()) {
				$this->lastInsertedId = $this->db->lastInsertId();
				return true;
			}
			else {
				$this->debug("Couldnt save the new article", $query);
				return false;
			}
		}
		
		
	}
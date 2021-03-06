<?php

	class Blog_Comments extends Blog_Database{
		
		private $prefix, $dateformat;
		
		public function __construct($db=false) {
			parent::__construct($db);
			$this->dateformat = "H:m, j M Y";
		}
		
		
		//Hämtar ut alla kommentarer för ett inlägg
		public function getComments($id, $dateformat=false) {
			
			$query = "SELECT * FROM {$this->tableComments} WHERE idPosts = :id";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			$get->execute(); 
			
			$result = array();
			
			foreach ($get->fetchAll() as $row) {
				$result[] = $this->returnComment($row, $dateformat);
			}
			
			return $result;
		}
		
		//Adds a comment to a post
		public function addComment($idPosts, $header, $content, $auhtorName, $authorEmail, $authorSite) {
			
			$date   = time();
			
			//Removes ugly html tags
			$content 	  = strip_tags($content); 		//Fix later to support basic BB code instead
			$header  	  = strip_tags($header);
			$auhtorName   = strip_tags($auhtorName);
			$authorEmail  = strip_tags($authorEmail);
			$authorSite   = strip_tags($authorSite);
			
			
			$query = "
				INSERT INTO {$this->tableComments} (idPosts, header, content, creationDate, author, authorEmail, authorSite)
				VALUES (:idPosts, :header,:content,:date,:author, :authorEmail, :authorSite)
			";
			
			//Inserts the data to the prepared_statement query
			$get = $this->db->prepare($query);
			$get->bindParam(':idPosts',  		$idPosts, 		PDO::PARAM_INT);
			$get->bindParam(':header',  		$header, 		PDO::PARAM_STR);
			$get->bindParam(':content', 		$content, 		PDO::PARAM_STR);
			$get->bindParam(':date', 	 		$date, 			PDO::PARAM_INT);
			$get->bindParam(':author',  		$auhtorName,	PDO::PARAM_STR);
			$get->bindParam(':authorEmail', 	$authorEmail, 	PDO::PARAM_STR);
			$get->bindParam(':authorSite',  	$authorSite, 	PDO::PARAM_STR);
			
			//Checks if the transaction succeded
			if (!$get->execute()) {
				$this->debug($this->lang['FAIL_CANNOT_SAVE_COMMENT'], $query); 
				return false;
			}
			else {
				$this->unsetSessions();
				return true;
			}
		}
		
		//Behandlar och retunerar datan för en kommentar	
		public function returnComment($row, $dateformat) {
			
			$defaults  = new defaults();
			
			$dateformat = ($dateformat == false) ? $this->dateformat : $dateformat;
			$date = $defaults->translateDate($dateformat, $row['creationDate']);
			$result = array(
				"id"  	  => $row['idComments'],
				"name"    => $row['author'],
				"email"   => $row['authorEmail'],
				"site"    => $row['authorSite'],
				"date"    => $date,
				"content" => $row['content'],
				"header"  => $row['header'],
			);
			
			return $result;
		}
		
		//Tar bort en kommentar från db
		public function delComment($id) {
			$query = "
				DELETE FROM {$this->tableComments} WHERE idComments = :id
			";
			
			$get = $this->db->prepare($query);
			$get->bindParam(':id', $id, PDO::PARAM_INT);
			
			if (!$get->execute()) {
				$this->debug($this->lang['FAIL_CANNOT_DEL_COMMENT'], $query); 
				return false;
			}
			else {
				$this->unsetSessions();
				return true;
			}
		}
		
		//Validerar inmatad data när man lägger till en kommentar
		public function validateComment($header, $content, $auhtorName, $authorEmail, $authorSite) {
			
			$this->saveComment($header, $content, $auhtorName, $authorEmail, $authorSite);
			
			$Validation = new Validation();
			
			$fail = array();
			
			
			if (!$Validation->checkValues("Heading", $header, 3) || strlen($header) > 33) {
				$fail[] = $this->lang['VALIDATION_TITLE'];
			}
			if (!$Validation->checkValues("Length", $content, 20)) {
				$fail[] = $this->lang['VALIDATION_CONTENT'];
			}
			if (!$Validation->checkValues("Name", $auhtorName, 2)) {
				$fail[] = $this->lang['VALIDATION_NAME'];
			}
			
			if (strlen($authorSite) > 0) {
				if (!$Validation->checkValues("Site", $authorSite, 5)) {
					$fail[] = $this->lang['VALIDATION_SITE'];
				}
			}
			
			if (strlen($authorEmail) > 0) {
				if (!$Validation->checkValues("Mail", $authorEmail, 5)) {
					$fail[] = $this->lang['VALIDATION_MAIL'];
				}
			}
			
			if (count($fail) > 0) {
				$_SESSION['errorMessage'] = $fail;
				return false;
			}
			else {
				return true;
			}
				
		}
		
		//Sparar inmatad data till sessioner
		public function saveComment($header, $content, $auhtorName, $authorEmail, $authorSite) {
			$_SESSION['comment']['header']  = $header;
			$_SESSION['comment']['content'] = $content;
			$_SESSION['comment']['name'] 	= $auhtorName;
			$_SESSION['comment']['email'] 	= $authorEmail;
			$_SESSION['comment']['site']  	= $authorSite;
		}
		
		//Dödar sessionerna
		private function unsetSessions() {
			$_SESSION['comment'] = array();
		}
		
	}

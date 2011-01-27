<?php
	
	/*****************
	 * 	Users.php 
	 * 	Class handling alll Users actions
	 */
	
	class Users extends Database {
		
		private $prefix;
		
		public function __construct($db = false) {
			parent::__construct($db);
		}
		
		public function __destruct() {
			;	
		}
		
		//Method that processes all login information and handles the whole scheme!
		public function processLogin($user, $pass) {
			
			$this->processLogout();
			
			//Hashes the password before checking it with the DB
			$password = $this->passwdHash($pass);
			
			$query = "
				SELECT username, realname, U.idUsers, idGroups, passwd
				FROM {$this->tableUsers} U 
				LEFT JOIN {$this->tableGroupUsers} GU ON GU.idUsers = U.idUsers
				WHERE
				    username = ? AND passwd = ?
				;
			";
		
			$get = $this->db->prepare($query);
			$get->execute(array($user, $password));
			$row = $get->fetch();
			if ($this->pdo->getFault($this->db) != '00000') {
				$_SESSION['errorMessage'] = $this->pdo->getFault($this->db);
				return false;
			}
			
			//Måste vara en träff resultatet
			if($get->rowCount() == 1) {
			    $_SESSION['userId']   = $row['idUsers'];
			    $_SESSION['username'] = $row['username'];    
				$_SESSION['group']	  = $row['idGroups'];
				$_SESSION['realname'] = $row['realname'];
				$_SESSION['passhash'] = base64_encode($row['passwd']); 
				$_SESSION['debug']    = true;
				return true;    
			} 
			else {
				$_SESSION['errorMessage'] = "Inloggningen misslyckades";
			   	return false;
			}
		}
		
		
		//Kills all running session so that the users gets logged out
		public function processLogout() {
			$_SESSION = array();
			if (isset($_COOKIE[session_name()])) {
		  		setcookie(session_name(), '', time()-42000, '/');
			}
			session_destroy();
			session_start(); 
			session_regenerate_id();
		}
		
		
		//Hashes the password   !! NEEDS TO GET REWORKED!
		function passwdHash($string) {
			return sha1($string . sha1($string . "98@£alHAwdk234¥[{", true));
		}
		
		//Kontrollerar om användaren är inloggad och den inloggades rättigheter
		public function checkPrivilegies($grp=false) {
			
			$return = false;
			if (isset($_SESSION['userId'])) {
				if ($this->ctlGroup($grp)) {
					$return = true;
				}
				elseif ($grp == false) {
					$return = true;
				}
				else {
					$_SESSION['errorMessage'] = "Du har inte rätt behörigheter för att kolla på sidan";
				}
			}
			else {
				$_SESSION['errorMessage'] = "Du måste vara inloggad för att komma åt sidan";
				$_SESSION['errorMessagePage'] = (isset($_GET['p'])) ? $_GET['p'] : "?";
				header("Location: ".PATH_SITE."/login");
				exit;
			}

			return $return;
			
		}
		
		public function checkUserRights($id, $grp=false) {
			$return = false;	
			if (isset($_SESSION['userId'])) {
				if ($_SESSION['userId'] == $id)	{
					$return = true;
				}
				if ($this->ctlGroup($grp) || $this->ctlGroup('adm')) {
					$return = true;
				}
				elseif ($grp == false) {
					$return = true;
				}
			}
			return $return;
		}
		
		//Kontrollerar ens rättigheter
		public function stdGroupsCtl($id) {
			$return = false;
			if (isset($_SESSION['group'])) {
				if ($_SESSION['userId'] == $id|| $_SESSION['group'] == 'adm') {
					$return = true;
				}
			}
			return $return;
		}
		
		//kontrollerar om man har rätt grupp
		public function ctlGroup($grp) {
			
			$return = false;
			if (isset($_SESSION['group'])) {
				if ($_SESSION['group'] == $grp) {
					$return = true;
				}
			}
			return $return;
		}
		
		//Returns current group
		public function returnGroup() {
			return $_SESSION['group'];
		}
		
		//Hämtar ut all info om en användare och retunerar resultatet
		public function getUserData($id) {
			
			if (!is_numeric($id) && $id != null) {
				$_SESSION['errorMessage'] = $this->lang['FAULT_READING_USER'];
				return;
			}
			
			$query = "
				SELECT U.*, G.* FROM {$this->tableUsers} U
				JOIN {$this->tableGroupUsers} GU ON GU.idUsers = U.idUsers
				JOIN {$this->tableGroups} G ON G.idGroups = GU.idGroups
				WHERE U.idUsers = :id
			";
			$get = $this->db->prepare($query);
			$get->bindParam(":id", $id, PDO::PARAM_INT);
			
			if ($get->execute()) {
				return $get->fetch();
			}
			else {
				$this->debug($this->lang['FAULT_READING_USER'], $query);
				return false;
			}		
		}
		
		//Returns the users gravatar
		public function getGravatar($gravatar) {
			
			include_once(PATH_LIB . 'gravatar/TalkPHP_Gravatar.php');
			include_once(PATH_LIB . 'gravatar/TalkPHP_Gravatar_Cache.php');
			
			$pAvatar = new TalkPHP_Gravatar();
		
			return $pAvatar	->	setEmail($gravatar)
							->	setSize(80)
							->	setRating('GRAVATAR_RATING_PG')
							->	getAvatar();
			
		}
		
		//Returns recaptcha data
		public function getRecaptcha($type) {
			
			$query = "SELECT name, value FROM {$this->tableConfigValues} WHERE name LIKE 'recap%' AND active = 'y' ";
			if ($get = $this->db->query($query)) {
				if ($get->rowCount() > 0) {
					foreach($get->fetchAll() as $row) {
						if ($row['name'] == "recap_public") {
							$pubId  = $row['value'];
						}
						if ($row['name'] == "recap_private") {
							$privId = $row['value'];
						}
					}		
						
					switch ($type) {
							
						case "pub" :
							return $pubId;
							break;
						
						case "priv" : 
							return $privId;
							break;
						
						case "html" :
							require_once(PATH_LIB . "recaptcha/recaptchalib.php");
							return recaptcha_get_html($pubId);
							break;
						 
					}
				}
				#$get->fetchAll();
			}
		}
		
		//Gets all info about a user from the username
		public function getUserDataByUsername($username) {
			
			$query = "
				SELECT U.*, G.* FROM {$this->tableUsers} U
				JOIN {$this->tableGroupUsers} GU ON GU.idUsers = U.idUsers
				JOIN {$this->tableGroups} G ON G.idGroups = GU.idGroups
				WHERE U.username = :user
			";
			$get = $this->db->prepare($query);
			$get->bindParam(":user", $username, PDO::PARAM_STR);
			
			if ($get->execute()) {
				return $get->fetch();
			}
			else {
				$this->debug($this->lang['FAULT_READING_USER'], $query);
				return false;
			}		
		}
		
		//Hämtar ut alla användare och retunerar resultatet
		public function getAllUsers() {
			
			$query = "
				SELECT U.*, G.* FROM {$this->tableUsers} U
				JOIN {$this->tableGroupUsers} GU ON GU.idUsers = U.idUsers
				JOIN {$this->tableGroups} G ON G.idGroups = GU.idGroups
				ORDER BY U.idUsers
			";
			
			$get = $this->db->prepare($query);
			
			if ($get->execute()) {
				return $get->fetchAll();
			}
			else {
				$fail = "Kunde inte spara inlägget";
				if ($_SESSION['debug'] == true)
					$fail .= "<p>Den felande queryn: <br /> <b>$query</b></p>";
				throw new Exception ($fail);
				return false;
			}	
		}
		
		public function registerUser($username, $name, $email, $password, $gravatar, $group=False) {

			$query = "INSERT INTO {$this->tableUsers} (username, realname, email, passwd, gravatar) VALUES (:user,:real,:mail,:pass,:grav)";
			
			$set = $this->db->prepare($query);
			$set->bindParam("user", strip_tags($username), 	PDO::PARAM_STR);
			$set->bindParam("real", strip_tags($name), 		PDO::PARAM_STR);
			$set->bindParam("mail", strip_tags($email), 	PDO::PARAM_STR);
			$set->bindParam("grav", strip_tags($gravatar),  PDO::PARAM_STR);
			$set->bindParam("pass", $this->passwdHash($password), PDO::PARAM_STR);
			
			if ($set->execute()) {
					
				$gid = ($group != false) ? $group : "std";					
				$uid = $this->db->lastInsertId();
				
				$gQuery = "INSERT INTO {$this->tableGroupUsers} VALUES (:uid, :gid)";
				$gSet = $this->db->prepare($gQuery);
				$gSet->bindParam("uid", $uid, PDO::PARAM_INT);
				$gSet->bindParam("gid", $gid, PDO::PARAM_STR);
				
				if ($gSet->execute()) {
					return true;
				}
				else {
					$this->debug("", $gQuery);
					return false;
				}
				
				$this->unsetSessions();	
			}
			else {
				$this->debug("", $query);
				return false;
			}	
			
		}
		
		//Edits a users profile
		public function editUser($id, $name, $email, $password, $gravatar, $group=False) {
			
			//Checks if it should change the users password
			$passSql = ($password != null) ? ",passwd = :pass" : null;
			
			$query = "
				UPDATE {$this->tableUsers} SET
					realname = :real,
					email 	 = :mail,
					gravatar = :grav
					$passSql
				WHERE idUsers = :id
			";
			
			$set = $this->db->prepare($query);
			$set->bindParam("id",   strip_tags($id), 		PDO::PARAM_INT);
			$set->bindParam("real", strip_tags($name), 		PDO::PARAM_STR);
			$set->bindParam("mail", strip_tags($email), 	PDO::PARAM_STR);
			$set->bindParam("grav", strip_tags($gravatar),  PDO::PARAM_STR);
			if ($password != null) { $set->bindParam("pass", $this->passwdHash($password), PDO::PARAM_STR); }
			
			if ($set->execute()) {
				
				//Changes a users group if one is given	
				if ($group != false) {
					//UPDATE TO WORK WITH MORE THAN ONE GROUP PER USER
					$gQuery = "UPDATE {$this->tableGroupUsers} SET idGroups = :gid WHERE idUsers = :uid";
					$gSet = $this->db->prepare($gQuery);
					$gSet->bindParam("uid", $id,    PDO::PARAM_INT);
					$gSet->bindParam("gid", $group, PDO::PARAM_STR);
	
					if ($gSet->execute()) {
						return true;
					}
					else {
						$this->debug("", $gQuery);
						return false;
					}
				}
				
				//Updates the usersessions if it is the current user thats beeing edited
				if ($_SESSION['userId'] == $id) {
					$_SESSION['realname'] = $name;
					$_SESSION['passhash'] = ($password != null) ? base64_encode($this->passwdHash($password)) : $_SESSION['passhash'];
				}
				
				$this->unsetSessions();	
				
			}
			else {
				$this->debug("", $query);
				return false;
			}	
			
		}

		public function validateRecaptchaInput($inp_challenge, $inp_response) {
			$id = $this->getRecaptcha("priv");
			
			require_once(PATH_LIB . "recaptcha/recaptchalib.php");
			$resp = recaptcha_check_answer ($id, $_SERVER["REMOTE_ADDR"], $inp_challenge, $inp_response);
			
			if ($resp->is_valid) {
				return true;
			}
			else {
				return false;
			}
			
		}
		
		public function validateUserInput($username, $name, $email, $password, $passConf, $emailConf, $gravatar, $type='reg') {
			$this->saveUserTempData($username, $name, $email, $emailConf, $gravatar);

			$Validation = new Validation();
			
			$fail = array();
			if (!$Validation->checkValues("User", $username, 3)) {
				$fail[] = $this->lang['USERFAIL'];
			}
			if (!$Validation->checkValues("Mail", $email, 5)) {
				$fail[] = $this->lang['EMAILFAIL'];
			}
			
			if ($type == 'reg' || strlen($password) > 0) {
				if (!$Validation->checkValues("Pass", $password, 6)) {
					$fail[] = $this->lang['PASSFAIL'];
				}
			}
			
			
			if (!$Validation->checkValues("Name", $name, 2)) {
				$fail[] = $this->lang['NAME_FAIL'];
			}
			
			//Controlls if password and email is the same as the confirm options
			if (!$Validation->CheckSameness($email, $emailConf)) {
				$fail[] = $this->lang['EMAILFAILCONF'];
			}
			
			if ($type == 'reg' || strlen($password) > 0) {
				if (!$Validation->CheckSameness($password, $passConf)) {
					$fail[] = " $password $passConf ";
				}
			}
			
			if ($type == 'reg') {
				if (!$this->validateRecaptchaInput($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"])) {
					$fail[] = $this->lang['RECAPTCHA_FAIL'];
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
		
		private function saveUserTempData($username, $name, $email, $emailConf, $gravatar) {
			$_SESSION['regEdit']['username']  = $username;
			$_SESSION['regEdit']['email'] 	  = $email;
			$_SESSION['regEdit']['name'] 	  = $name;
			$_SESSION['regEdit']['emailConf'] = $emailConf;
			$_SESSION['regEdit']['gravatar']  = $gravatar;
		}
		
		private function unsetSessions() {
			$_SESSION['regEdit'] = array();
		}
	}
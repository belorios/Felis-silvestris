<?php
	
	/*
	 * Class som hanterar allt kring användare
	 */
	
	class Users extends Database {
		
		private $prefix;
		
		public function __construct($db = false) {
			parent::__construct($db);
		}
		
		public function __destruct() {
			;	
		}
		
		//Utför inloggning
		public function processLogin($user, $pass) {
			
			$this->processLogout();
			
			//Hashar lösenordet 
			$password = $this->passwdHash($pass);
			
			$query = "
				SELECT username, realname, U.idUsers, idGroups
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
			    $_SESSION['userId']    = $row['idUsers'];
			    $_SESSION['username']  = $row['username'];    
				$_SESSION['group']	   = $row['idGroups'];
				$_SESSION['realname']  = $row['realname'];
				$_SESSION['debug']     = true;
				return true;    
			} 
			else {
				$_SESSION['errorMessage'] = "Inloggningen misslyckades";
			   	return false;
			}
		}
		
		
		//Dödar alla sessioner så användaren loggas ut
		public function processLogout() {
			$_SESSION = array();
			if (isset($_COOKIE[session_name()])) {
		  		setcookie(session_name(), '', time()-42000, '/');
			}
			session_destroy();
			session_start(); 
			session_regenerate_id();
		}
		
		
		//Hashar lösenorder
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
		
		//Hämtar ut all info om en användare och retunerar resultatet
		public function getUserData($id) {
			
			if (!is_numeric($id) && $id != null) {
				$_SESSION['errorMessage'] = "Kan inte läsa användaren";
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
				$fail = "Kunde inte spara inlägget";
				if ($_SESSION['debug'] == true)
					$fail .= "<p>Den felande queryn: <br /> <b>$query</b></p>";
				throw new Exception ($fail);
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
		
		public function registerUser($username, $fname, $lname, $email, $password, $group=False) {
			$_SESSION['debug'] = true;
			$name = "$fname $lname"; 
			$query = "INSERT INTO {$this->tableUsers} (username, realname, email, passwd) VALUES (:user,:real,:mail,:pass)";
			
			$set = $this->db->prepare($query);
			$set->bindParam("user", strip_tags($username), 	PDO::PARAM_STR);
			$set->bindParam("real", strip_tags($name), 		PDO::PARAM_STR);
			$set->bindParam("mail", strip_tags($email), 	PDO::PARAM_STR);
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
			}
			else {
				$this->debug("", $query);
				return false;
			}	
			
		}
		
		public function validateUserInput($username, $fname, $lname, $email, $password, $passConf, $emailConf) {
			$this->saveUserTempData($username, $fname, $lname, $email, $emailConf);
			
			$Validation = new Validation();
			
			$fail = array();
			if (!$Validation->checkValues("User", $username, 3)) {
				$fail[] = $this->lang['USERFAIL'];
			}
			if (!$Validation->checkValues("Mail", $email, 5)) {
				$fail[] = $this->lang['EMAILFAIL'];
			}
			if (!$Validation->checkValues("Pass", $password, 6)) {
				$fail[] = $this->lang['PASSFAIL'];
			}
			
			if (!$Validation->checkValues("Name", $fname, 2)) {
				$fail[] = $this->lang['FNAME_FAIL'];
			}
			if (!$Validation->checkValues("Name", $lname, 2)) {
				$fail[] = $this->lang['LNAME_FAIL'];
			}
			
			//Controlls if password and email is the same as the confirm options
			if (!$Validation->CheckSameness($email, $emailConf)) {
				$fail[] = $this->lang['EMAILFAILCONF'];
			}
			
			if (!$Validation->CheckSameness($password, $passConf)) {
				$fail[] = $this->lang['PASSFAILCONF'];
			}
			/*
			require_once(PATH_LIB . "recaptcha/recaptchalib.php");
			$resp = recaptcha_check_answer ("", $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
			
			if (!$resp->is_valid) {
				$fail[] = $this->lang['RECAPTCHA_FAIL'];
			}
			*/
			if (count($fail) > 0) {
				$_SESSION['errorMessage'] = $fail;
				return false;
			}
			else {
				return true;
			}
		}
		
		public function saveUserTempData($username, $fname, $lname, $email, $emailConf) {
			$_SESSION['regEdit']['username']  = $username;
			$_SESSION['regEdit']['email'] 	  = $email;
			$_SESSION['regEdit']['fname'] 	  = $fname;
			$_SESSION['regEdit']['lname'] 	  = $lname;
			$_SESSION['regEdit']['emailConf'] = $emailConf;
		}
	}
<?php

	class Database {
		
		protected 	$tableUsers, 
					$tableGroups,
					$tableGroupUsers,
					$tableConfig,
					$tableConfigValues,
					$tablesUsers,
					$db,
					$pdo,
					$lastInsertedId,
					$lang;
					 
		public function __construct($db) {
			
			$this->tablesUsers = array("
				
			");
			
			$this->tableUsers 		 = DB_PREFIX . "Users";
			$this->tableGroups 		 = DB_PREFIX . "Groups";
			$this->tableGroupUsers   = DB_PREFIX . "GroupUsers";
			$this->tableConfig 		 = DB_PREFIX . "config";
			$this->tableConfigValues = DB_PREFIX . "configValues";
			
			$this->lang = $GLOBALS['lang'];
			
			if ($db != false) {
				$this->db = $db;	
			}
			else {
				$this->getConnection();
			}
		}
		
		public function getConnection() {
			if (!is_object($this->db)) {
				$this->pdo = new pdoConnection();
				$this->db = $this->pdo->getConnection();
			}
 		}
		
		//Checks if it should write out a debugmessage
		protected function debug($fail, $query) {
			if ($_SESSION['debug'] == true)
				$fail .= "<p>$lang[DEBUG_QUERY_FAIL] <br /> <b>$query</b></p>";
			throw new Exception ($fail);
		}
		
		//Returns the last insertedid
		public function getLastId() {
			return $this->lastInsertedId;
		}
	}

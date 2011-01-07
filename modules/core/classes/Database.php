<?php

	class Database {
		
		protected 	$tableUsers, 
					$tableGroups,
					$tableGroupUsers,
					$db,
					$pdo;
					 
		public function __construct($db) {
			
			$this->tableUsers = DB_PREFIX . "Users";
			$this->tableGroups = DB_PREFIX . "Groups";
			$this->tableGroupUsers = DB_PREFIX . "GroupUsers";
			
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
	}

<?php

	class Manager_Database extends Database {
		
		protected 	$tableManagerMenu;
					 
		public function __construct($db) {
				
			parent::__construct($db);
			$modulename = "manager_";
			$this->tableManagerMenu	= DB_PREFIX . "{$modulename}menu";
			
		}
	}

<?php

	/*************
	 * 	Installfile for the managermodule
	 */

	$modulename = "manager";
	
	//Array holding all dbtables related to this modules
	$tables = array(
		"manager_menu" => DB_PREFIX . "{$modulename}_menu",
	);	
	
	$sqlTableCreate = array();
	
	$sqlTableCreate['manager_menu'] = "
		CREATE TABLE IF NOT EXISTS $tables[manager_menu] (
		
			-- Primary key
			idMenu BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Attributes
			name VARCHAR(255) NOT NULL, 
			url  VARCHAR(255) NOT NULL,
			parent BIGINT NOT NULL DEFAULT 0
		)
		ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci	
	";
	
	//Creates data for the modules
	
	//Adds menuitems to the manager
	$sqlCreateData['manager_menu'] = "
		INSERT INTO $tables[manager_menu] (name, url, parent) VALUES 
		('CONFIGURATION', 'config', 0)
	";
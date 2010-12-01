<?php
	
	/**********
	 *  install file for the forum
	 *  
	 */

	//Tablenames
	$tableTopics = DB_PREFIX . "Topics";
	$tablePosts  = DB_PREFIX . "Posts";
	
	$dbc->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
	
	if ($clearOld == true) {
		$stmt = $dbc->query("DROP TABLE IF EXISTS $tablePosts");		$body .= ctlPrint($tablePosts,  "Removing the");
		$stmt = $dbc->query("DROP TABLE IF EXISTS $tableTopics");		$body .= ctlPrint($tableTopics, "Removing the");
		$body .= "<tr><td>&nbsp;</td></tr>";
	}
	
	//Creating the usertable
	$stmt = $dbc->query("
		CREATE TABLE IF NOT EXISTS $tableTopics (
		
		  -- Primary key(s)
		  idTopics BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		
		  -- Attributes
		  title    VARCHAR(255) NOT NULL,
		  created  BIGINT(40)   NOT NULL,
		  idUsers   BIGINT		NOT NULL
		  
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	");
	$body .= ctlPrint($tableTopics, "Creating the");
	
	//Creating the usertable
	$stmt = $dbc->query("
		CREATE TABLE IF NOT EXISTS $tablePosts (
		
		  -- Primary key(s)
		  idPosts BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		  
		  -- Foreign key(s)
		  idTopics BIGINT NULL,
		  
		  -- Attributes
		  title    	VARCHAR(255) NOT NULL,
		  post		LONGTEXT	 NOT NULL,
		  created 	BIGINT(40)   NOT NULL,
		  updated 	BIGINT(40)   NOT NULL,
		  idUsers  	BIGINT		 NOT NULL,
		  
		  FOREIGN KEY (idUsers)
				REFERENCES $tableTopics(idTopics)
				ON UPDATE CASCADE ON DELETE CASCADE
		  
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	");
	$body .= ctlPrint($tablePosts, "Creating the");
	
	foreach($fail as $fel) {
		if ($fel != "0") {
			$_SESSION['errorMessage'][] = $fel;
			$fault = true;
		}
	}

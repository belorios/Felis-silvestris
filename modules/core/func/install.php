<?php

	//Tablenames
	$tableGroupUsers = DB_PREFIX . "GroupUsers";
	$tableUsers 	 = DB_PREFIX . "Users";
	$tableGroups	 = DB_PREFIX . "Groups";
	$tableArticles   = DB_PREFIX . "Articles"; 
	$tableStatistics = DB_PREFIX . "Statistics";
	
	//Procedures
	$procedures = array(
		"CreateArticle"  => "PCreateNewArticle",
		"UpdateArticle"  => "PUpdateArticle",
		"DisplayArticle" => "PDisplayArticle",
		"ListArticles"   => "PListArticles",
	);
	
	//UDFs
	$udfs = array(
		"ArticleRights" => "FCheckUserIsOwnerOrAdmin",
	);
	
	//Triggers
	$triggers = array(
		"AddStatUser" => "TInsertUser",
		"AddStatArt"  => "TAddArticle",
		"DelStatArt"  => "TDelArticle",
	);
	
	$tables = array(
		"Statistics" => DB_PREFIX . "Statistics",
		"Articles"   => DB_PREFIX . "Articles",
		"GroupUsers" => DB_PREFIX . "GroupUsers",
		"Users" 	 => DB_PREFIX . "Users",
		"Groups" 	 => DB_PREFIX . "Groups",
	);
	
	//Klasser
	$pdo   = new pdoConnection();
	$dbc   = $pdo->getConnection(false);
	$Users = new Users();
	
	$sqlCreate  = array();
	
	
	
	//Creating the usertable
	$sqlCreate['Users'] = "
		CREATE TABLE IF NOT EXISTS $tables[Users] (
		
		  -- Primary key(s)
		  idUsers BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		
		  -- Attributes
		  username VARCHAR(40)  NOT NULL UNIQUE,
		  realname VARCHAR(60)  NOT NULL,
		  email    VARCHAR(100) NOT NULL,
		  passwd   VARCHAR(64)  NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	//Creating the groupstable
	$sqlCreate['Groups'] = "
		CREATE TABLE IF NOT EXISTS $tables[Groups] (
		
		-- Primary key(s)
			idGroups CHAR(3) NOT NULL PRIMARY KEY,
		
		-- Attributes
			shortdesc	VARCHAR(30)  NOT NULL,
			groupdesc 	VARCHAR(255) NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	//Creating the table that links users to groups
	$sqlCreate['GroupUsers'] = "
		CREATE TABLE IF NOT EXISTS $tables[GroupUsers] (
		
			-- Foreign keys
			idUsers BIGINT NOT NULL,
			idGroups CHAR(3) NOT NULL,
			
			FOREIGN KEY (idUsers)
				REFERENCES $tables[Users](idUsers)
				ON UPDATE CASCADE ON DELETE CASCADE,
			FOREIGN KEY (idGroups)
				REFERENCES $tables[Groups](idGroups)
				ON UPDATE CASCADE ON DELETE CASCADE,
			PRIMARY KEY (idUsers, idGroups)
			
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	//Creates the table holding statistic
	$sqlCreate['Statistics'] = "
			CREATE TABLE IF NOT EXISTS $tables[Statistics] (
		
			-- Primary key(s)
			userId BIGINT NOT NULL PRIMARY KEY,
		
			-- Foreign key(s)
			FOREIGN KEY (userId)
				REFERENCES $tables[Users](idUsers)
				ON UPDATE CASCADE ON DELETE CASCADE,
			
			-- Attributes
			noOfArticles BIGINT NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci;
	";
	
	//Creates table holding articles
	$sqlCreate['Articles'] = "
		CREATE TABLE IF NOT EXISTS $tables[Articles] (
		
		  -- Primary key(s)
		  idArticles BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		  
		  -- Foreign key(s)
		   userId	  		BIGINT 		NOT NULL,
		   
		  -- Attributes
		  title 		VARCHAR(40) NOT NULL,
		  content 		LONGTEXT    NOT NULL,
		  created  		BIGINT(50)  NOT NULL,
		  updated		BIGINT(50)  NOT NULL,
		 
		  FOREIGN KEY (userId)
				REFERENCES $tables[Users](idUsers)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	
	//Creates UDFs used by this application
	$sqlUdfsCreate  = array();
	
	//UDF than controls userrights related to an article
	$sqlUdfsCreate['ArticleRights'] = "
		CREATE FUNCTION $udfs[ArticleRights](vArticleId INT, vUserId INT)
			RETURNS BOOL
			BEGIN
				DECLARE articleUserID INT;
			  	DECLARE UserGroupID CHAR(3);
			  
			  	SELECT userId, idGroups INTO articleUserID, UserGroupID 
				FROM $tableArticles 
				LEFT JOIN $tableGroupUsers ON idUsers = vUserId
				WHERE idArticles = vArticleId;
			
			 	IF (vUserId = articleUserID OR UserGroupID = 'adm') then
					RETURN TRUE;  
				ELSE
					RETURN FALSE; 
				END IF;
			END
	"; 
	
	//Creates the proceduers used by this application
	$sqlProcsCreate = array();
	
  	//Procedure used for creating articles
	$sqlProcsCreate['CreateArticle'] = "
		CREATE PROCEDURE $procedures[CreateArticle] (IN vCreated BIGINT(50), IN vTitle VARCHAR(255), IN vContent TEXT, IN vUserId BIGINT)
		BEGIN
			INSERT INTO $tableArticles(title, content, created, updated, userId) VALUES (vTitle, vContent, vCreated, vCreated, vUserId);
		END
	";
	
	//Procedure used for updating articles
	$sqlProcsCreate['UpdateArticle'] = "
		CREATE PROCEDURE $procedures[UpdateArticle] (IN vIdArticles BIGINT, IN vUpdated BIGINT(50), IN vTitle VARCHAR(255), IN vContent TEXT, IN vIdUsers INT, OUT SUCCESS BOOL)
			BEGIN
				DECLARE rights INT;
			
				SELECT FCheckUserIsOwnerOrAdmin(vIdArticles, vIdUsers) INTO rights;
				
				IF (rights = 1) then
					UPDATE $tableArticles SET 
						title = vTitle,
						content = vContent,
						updated = vUpdated
					WHERE idArticles = vIdArticles;
					SET SUCCESS = 1;
				ELSE
					SET SUCCESS = 0;
				END IF;	
			END
	";
	
	//Procedure used for displaying an article
	$sqlProcsCreate['DisplayArticle'] = "
		CREATE PROCEDURE $procedures[DisplayArticle](IN vIdArticles BIGINT, IN vCurUser BIGINT)
			BEGIN
				SELECT A.*, U.realname, FCheckUserIsOwnerOrAdmin(vIdArticles, vCurUser) as rights 
				FROM $tableArticles A 
				LEFT JOIN $tableUsers U on U.idUsers = A.userId 
				WHERE A.idArticles = vIdArticles;
			END
	";
	//Procedure used for listing articles
	$sqlProcsCreate['ListArticles'] = "
		CREATE PROCEDURE $procedures[ListArticles] (IN vLimit INT)
			BEGIN
				PREPARE STMT FROM \"
					SELECT A.*, U.realname
					FROM $tableArticles A
					JOIN $tableUsers U on idUsers = A.userId 
					ORDER BY updated DESC
					LIMIT ? 
				\";
				SET @LIMIT = vLimit;
				EXECUTE STMT USING @LIMIT; 
			END
	";
	
	//Creating triggers
	$sqlTriggerCreate = array();
	
	//Trigger adding users to statistics
	$sqlTriggerCreate['AddStatUser'] = "
		CREATE TRIGGER $triggers[AddStatUser]
		AFTER INSERT ON $tableUsers
		FOR EACH ROW
		BEGIN
	  		INSERT INTO $tableStatistics (userId) VALUES (NEW.idUsers);
		END 
	";
	
	//Trigger adding article for user in statistics
	$sqlTriggerCreate['AddStatArt'] = "
		CREATE TRIGGER $triggers[AddStatArt]
		AFTER INSERT ON $tableArticles
		FOR EACH ROW
		BEGIN
		  UPDATE $tableStatistics SET noOfArticles = noOfArticles+1 WHERE userId = NEW.userId;
		END
	";
	
	//Trigger deleting article for user in statistics
	$sqlTriggerCreate['DelStatArt'] = "
		CREATE TRIGGER $triggers[DelStatArt]
		AFTER DELETE ON $tableArticles
		FOR EACH ROW
		BEGIN
		  UPDATE $tableStatistics SET noOfArticles = noOfArticles-1 WHERE userId = OLD.userId;
		END 
	";
	
	/************
	 * Creating DATA
	 */
	
	$sqlCreateData = array();
	
	//Creating some users
	$sqlCreateData['Users'] = "
		INSERT INTO $tables[Users] (username, realname, email, passwd) VALUES 
		('kalle', 'Kalle Kubik', 'kalle@example.com', '".$Users->passwdHash("kalle")."'),
		('erik', 'Erik Estrada', 'erik@example.com', '".$Users->passwdHash("erik")."'),
		('jenna', 'Jenna Jeans', 'jenna@example.com', '".$Users->passwdHash("jenna")."')
	";
	
	//Creating some Groups
	$sqlCreateData['Groups'] = "
		INSERT INTO $tables[Groups] (idGroups, shortdesc, groupdesc) VALUES 
		('adm', 'Administratör', 'Administratörerna for sajten'),
		('mod', 'Modes skribent', 'Skriver om mode'),
		('skr', 'Skribent', 'Helt vanlig skribent')
	";
	
	//Maps users against groups
	$sqlCreateData['GroupUsers'] = "
		INSERT INTO $tables[GroupUsers] (idGroups, idUsers) VALUES 
		('adm', 1), 
		('mod', 2),
		('skr', 3)
	";
	
	if ($_POST['dummyData'] == '1') {	
		
		
		//Creating the dummy data if this is chosen
		$sqlCreateData['Articles'] = 
			array(
				"stmt" => "call $procedures[CreateArticle](?, ?, ?, ?)",
				array(time(), "Den första test artikel", "Här testar vi må en massa!", 1),
				array(time(), "Den andra test artikeln", "Här testar vi må en massa!", 2),
				array(time(), "Den tredje test artikeln", "Här testar vi må en massa!", 3),
			);
		;
			
		
		
	}
	
	foreach($fail as $fel) {
		if ($fel != "0") {
			$_SESSION['errorMessage'][] = $fel;
			$fault = true;
		}
	}
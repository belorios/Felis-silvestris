<?php

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
		"Groups" 	 	=> DB_PREFIX . "Groups",
		"Users" 	 	=> DB_PREFIX . "Users",
		"GroupUsers" 	=> DB_PREFIX . "GroupUsers",
		"Statistics"	=> DB_PREFIX . "Statistics",
		"Articles"  	=> DB_PREFIX . "Articles",
		"Config" 	 	=> DB_PREFIX . "Config",
		"ConfigValues"	=> DB_PREFIX . "ConfigValues",
	);
	
	//Klasser
	$Users = new Users();
	
	$sqlTableCreate  = array();
	
	//Creating the usertable
	$sqlTableCreate['Users'] = "
		CREATE TABLE IF NOT EXISTS $tables[Users] (
		
		  -- Primary key(s)
		  idUsers BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		
		  -- Attributes
		  username VARCHAR(40)  NOT NULL UNIQUE,
		  realname VARCHAR(60)  NOT NULL,
		  email    VARCHAR(100) NOT NULL,
		  gravatar VARCHAR(100) NOT NULL,
		  passwd   VARCHAR(64)  NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	//Creating the groupstable
	$sqlTableCreate['Groups'] = "
		CREATE TABLE IF NOT EXISTS $tables[Groups] (
		
		-- Primary key(s)
			idGroups CHAR(3) NOT NULL PRIMARY KEY,
		
		-- Attributes
			shortdesc	VARCHAR(30)  NOT NULL,
			groupdesc 	VARCHAR(255) NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	//Creating the table that links users to groups
	$sqlTableCreate['GroupUsers'] = "
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
	$sqlTableCreate['Statistics'] = "
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
	$sqlTableCreate['Articles'] = "
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
	
	$sqlTableCreate['Config'] = "
		CREATE TABLE IF NOT EXISTS $tables[Config] (
			-- Primary key
			idConfig BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Attributes
			type   		VARCHAR(20) NOT NULL,
			name   		VARCHAR(50) NOT NULL,
			description TEXT NOT NULL,
			descname	VARCHAR(50) NOT NULL,
			module		INT NOT NULL,
			value  		VARCHAR(255) NOT NULL,
			global 		TINYINT(1) NOT NULL,
			active 		ENUM('y','n')
		)
		ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci;	
	";
	
	$sqlTableCreate['ConfigValues'] = "
		CREATE TABLE IF NOT EXISTS $tables[ConfigValues] (
			-- Primary key
			idValue BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
			
			-- Foreign key
			idConfig BIGINT NOT NULL,
			
			-- Atributes
			type   		VARCHAR(20) NOT NULL,
			name   		VARCHAR(50) NOT NULL,
			description TEXT NOT NULL,
			descname	VARCHAR(50) NOT NULL,
			value  		VARCHAR(255) NOT NULL,
			active 		ENUM('y','n'),
			
			FOREIGN KEY (idConfig) 
				REFERENCES $tables[Config](idConfig)
				ON UPDATE CASCADE ON DELETE CASCADE
			
		)
		ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci;
	";
	
	
	//Creates UDFs used by this application
	$sqlUdfsCreate  = array();
	
	//UDF than controls userrights related to an article
	$sqlUdfsCreate['ArticleRights'] = "
		CREATE FUNCTION $udfs[ArticleRights] (vArticleId INT, vUserId INT)
			RETURNS BOOL
			BEGIN
				DECLARE articleUserID INT;
			  	DECLARE UserGroupID CHAR(3);
			  
			  	SELECT userId, idGroups INTO articleUserID, UserGroupID 
				FROM $tables[Articles]
				LEFT JOIN $tables[GroupUsers] ON idUsers = vUserId
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
			INSERT INTO $tables[Articles](title, content, created, updated, userId) VALUES (vTitle, vContent, vCreated, vCreated, vUserId);
		END
	";
	
	//Procedure used for updating articles
	$sqlProcsCreate['UpdateArticle'] = "
		CREATE PROCEDURE $procedures[UpdateArticle] (IN vIdArticles BIGINT, IN vUpdated BIGINT(50), IN vTitle VARCHAR(255), IN vContent TEXT, IN vIdUsers INT, OUT SUCCESS BOOL)
			BEGIN
				DECLARE rights INT;
			
				SELECT FCheckUserIsOwnerOrAdmin(vIdArticles, vIdUsers) INTO rights;
				
				IF (rights = 1) then
					UPDATE $tables[Articles] SET 
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
				FROM $tables[Articles] A 
				LEFT JOIN $tables[Users] U on U.idUsers = A.userId 
				WHERE A.idArticles = vIdArticles;
			END
	";
	//Procedure used for listing articles
	$sqlProcsCreate['ListArticles'] = "
		CREATE PROCEDURE $procedures[ListArticles] (IN vLimit INT)
			BEGIN
				PREPARE STMT FROM \"
					SELECT A.*, U.realname
					FROM $tables[Articles] A
					JOIN $tables[Users] U on idUsers = A.userId 
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
		AFTER INSERT ON $tables[Users]
		FOR EACH ROW
		BEGIN
	  		INSERT INTO $tables[Statistics] (userId) VALUES (NEW.idUsers);
		END 
	";
	
	//Trigger adding article for user in statistics
	$sqlTriggerCreate['AddStatArt'] = "
		CREATE TRIGGER $triggers[AddStatArt]
		AFTER INSERT ON $tables[Articles]
		FOR EACH ROW
		BEGIN
		  UPDATE $tables[Statistics] SET noOfArticles = noOfArticles+1 WHERE userId = NEW.userId;
		END
	";
	
	//Trigger deleting article for user in statistics
	$sqlTriggerCreate['DelStatArt'] = "
		CREATE TRIGGER $triggers[DelStatArt]
		AFTER DELETE ON $tables[Articles]
		FOR EACH ROW
		BEGIN
		  UPDATE $tables[Statistics] SET noOfArticles = noOfArticles-1 WHERE userId = OLD.userId;
		END 
	";
	
	/************
	 * Creating DATA
	 */
	
	$sqlCreateData = array();
	
	//Creating the administrator
	$sqlCreateData['Users'] = array(
		"stmt" => "INSERT INTO $tables[Users] (username, realname, email, passwd) VALUES  (?,?,?,?)",
		array($_SESSION['formdata']['user_name'], $_SESSION['formdata']['user_name'], $_SESSION['formdata']['user_mail'], $Users->passwdHash($_SESSION['formdata']['user_pass'])),
	);
	
	//Creates the two standard groups
	$sqlCreateData['Groups'] = "
		INSERT INTO $tables[Groups] (idGroups, shortdesc, groupdesc) VALUES 
		('adm', 'Administrator', 'Administrators of the site'),
		('std', 'User', 'Regular user')
	";
	
	//Maps users against groups
	$sqlCreateData['GroupUsers'] = "
		INSERT INTO $tables[GroupUsers] (idGroups, idUsers) VALUES 
		('adm', 1) 
	";
	
	//Adding data to the configurationdatabase
	$sqlCreateData['Config'] = "
		INSERT INTO $tables[Config] (type, name, description, descname, module, value, global, active) VALUES 
		('text', 'app_name', 'Sets the name on the application', 'Application name', 1, '{$_SESSION['formdata']['app_name']}', 1, 'y'),
		('text', 'app_footer', 'Sets the name on the footer for the application', 'Application footer', 1, '{$_SESSION['formdata']['app_footer']}', 1, 'y'),
		('text', 'app_payoff', 'Sets the name on the payoff for the application', 'Application Pay off', 1, '{$_SESSION['formdata']['app_payoff']}', 1, 'y'),
		('multitext', 'recaptcha', 'Sets the settings to enable recaptcha', 'Recaptcha', 1, '1', 0, 'y'),
		('multiselect', 'editor', 'Sets which editor the users can use', 'Editor', 1, '1', 0, 'y')
	";
	
	$sqlCreateData['ConfigValues'] = "
		INSERT INTO $tables[ConfigValues] (idConfig, type, name, description, descname, value, active) VALUES 
		(4, 'text', 'recap_public', 'Sets the public id for your recaptcha', 'Public ID', '', 'y'),
		(4, 'text', 'recap_private', 'Sets the private id for your recaptcha', 'Private ID', '', 'y'),
		(5, 'select', 'nicedit', 'Sets nicEdit as the applications editor', 'nicEdit', 0, 'y'),
		(5, 'select', 'markitup', 'Sets markitup as the applications editor', 'markitup', 0, 'y'),
		(5, 'select', 'tinymce', 'Sets tinymce as the applications editor', 'tinymce', 1, 'y'),
		(5, 'select', 'plain', 'Sets a plain editor as the applications editor', 'plain', 0, 'y'),
		(5, 'select', 'wymeditor', 'Sets wymEditor as the applications editor', 'wymEditor', 0, 'y')
	";
	
	if ($_POST['dummyData'] == '1') {	
		
		//Creating some dummy users
		$sqlCreateData['DummyUsers'] = "
			INSERT INTO $tables[Users] (username, realname, email, passwd) VALUES 
			('kalle', 'Kalle Kubik', 'kalle@example.com', '".$Users->passwdHash("kalle")."'),
			('erik', 'Erik Estrada', 'erik@example.com', '".$Users->passwdHash("erik")."'),
			('jenna', 'Jenna Jeans', 'jenna@example.com', '".$Users->passwdHash("jenna")."')
		";
		
		//Creating some dummy Groups
		$sqlCreateData['DummyGroups'] = "
			INSERT INTO $tables[Groups] (idGroups, shortdesc, groupdesc) VALUES 
			('mod', 'Fashion writer', 'Writes about fashion'),
			('skr', 'Writer', 'Regular blog writer')
		";
		
		//Maps the dummy users against groups
		$sqlCreateData['DummyGroupUsers'] = "
			INSERT INTO $tables[GroupUsers] (idGroups, idUsers) VALUES 
			('std', 2), 
			('mod', 3),
			('skr', 4)
		";
		
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
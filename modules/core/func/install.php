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

	//Klasser
	$pdo   = new pdoConnection();
	$dbc   = $pdo->getConnection(false);
	$Users = new Users();
	
	//Removes old udfs if they exists
	foreach ($udfs as $udf) {
		$stmt = $dbc->query("DROP FUNCTION IF EXISTS $udf;");	$body .= ctlPrint($udf, "Removing the", "udf");
	}
	
	//Removes old triggers if the exists in db
	foreach ($triggers as $trigger) {
		$stmt = $dbc->query("DROP TRIGGER IF EXISTS TInsertUser;");	$body .= ctlPrint($trigger, "Removing the", "trigger");
	}
	
	//Removes old proceduers if they exists
	foreach ($procedures as $procedure) {
		$stmt = $dbc->query("DROP PROCEDURE IF EXISTS $procedure;");	$body .= ctlPrint($procedure, "Removing the", "procedure");
	}
	$body .= "<tr><td>&nbsp;</td></tr>";
	
	
	//Drops old tables if they exists
	if ($clearOld == true) {
		$stmt = $dbc->query("DROP TABLE IF EXISTS $tableStatistics");	$body .= ctlPrint($tableStatistics, "Removing the");
		$stmt = $dbc->query("DROP TABLE IF EXISTS $tableArticles");		$body .= ctlPrint($tableGroupUsers, "Removing the");
		$stmt = $dbc->query("DROP TABLE IF EXISTS $tableGroupUsers");	$body .= ctlPrint($tableGroupUsers, "Removing the");
		$stmt = $dbc->query("DROP TABLE IF EXISTS $tableUsers");		$body .= ctlPrint($tableUsers, "Removing the");
		$stmt = $dbc->query("DROP TABLE IF EXISTS $tableGroups");		$body .= ctlPrint($tableGroups, "Removing the");
		$body .= "<tr><td>&nbsp;</td></tr>";
	}
	
	//Creating the usertable
	$stmt = $dbc->query("
		CREATE TABLE IF NOT EXISTS $tableUsers (
		
		  -- Primary key(s)
		  idUsers BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		
		  -- Attributes
		  username VARCHAR(40)  NOT NULL UNIQUE,
		  realname VARCHAR(60)  NOT NULL,
		  email    VARCHAR(100) NOT NULL,
		  passwd   VARCHAR(64)  NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	");
	$body .= ctlPrint($tableUsers, "Creating the");
	
	//Creating the grupptabellen
	$stmt = $dbc->query("
		CREATE TABLE IF NOT EXISTS $tableGroups (
		
		-- Primary key(s)
			idGroups CHAR(3) NOT NULL PRIMARY KEY,
		
		-- Attributes
			shortdesc	VARCHAR(30)  NOT NULL,
			groupdesc 	VARCHAR(255) NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	");
	$body .= ctlPrint($tableGroups, "Creating the");
	
	//Creating the tabellen som länkar ihop användare med grupper
	$stmt = $dbc->query("
		CREATE TABLE IF NOT EXISTS $tableGroupUsers (
		
		-- Foreign keys
			idUsers BIGINT NOT NULL,
			idGroups CHAR(3) NOT NULL,
		
			FOREIGN KEY (idUsers)
				REFERENCES $tableUsers(idUsers)
				ON UPDATE CASCADE ON DELETE CASCADE,
			FOREIGN KEY (idGroups)
				REFERENCES $tableGroups(idGroups)
				ON UPDATE CASCADE ON DELETE CASCADE,
			PRIMARY KEY (idUsers, idGroups)
			
		
		-- Attributes
		-- None
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	");
	$body .= ctlPrint($tableGroupUsers, "Creating the");

	
	//Creaties table holding statistic
	$stmt = $dbc->query("
			CREATE TABLE IF NOT EXISTS $tableStatistics (
		
			-- Primary key(s)
			userId BIGINT NOT NULL PRIMARY KEY,
		
			-- Foreign key(s)
			FOREIGN KEY (userId)
				REFERENCES $tableUsers(idUsers)
				ON UPDATE CASCADE ON DELETE CASCADE,
			
			-- Attributes
			noOfArticles BIGINT NOT NULL
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci;
	");
	$body .= ctlPrint($tableStatistics, "Creating the");
	
	//Creates table holding articles
	$stmt = $dbc->query("
		CREATE TABLE IF NOT EXISTS $tableArticles (
		
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
				REFERENCES $tableUsers(idUsers)
				ON UPDATE CASCADE ON DELETE CASCADE
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	");
	$body .= ctlPrint($tableArticles, "Creating the");
	
	//Kontrollerar om något gått fel vid skapandet av tabeller
	foreach($fail as $fel) {
		if ($fel != "0") {
			$fault = true;
		}
	}
	
	if ($fault == false) {
		
		$body .= "<tr><td>&nbsp;</td></tr>";
		
		$dbc->beginTransaction();
	   
		//Creates UDFs used by this application
		
		//UDF than controls userrights related to an article
		$stmt = $dbc->query("
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
		");
		$body .= ctlPrint($udfs['ArticleRights'], "Creating the", "function");
		
	  	//Creates the proceduers used by this application
	  	
	  	//Procedure used for creating articles
		$stmt = $dbc->query("
			CREATE PROCEDURE $procedures[CreateArticle] (IN vCreated BIGINT(50), IN vTitle VARCHAR(255), IN vContent TEXT, IN vUserId BIGINT)
			BEGIN
				INSERT INTO $tableArticles(title, content, created, updated, userId) VALUES (vTitle, vContent, vCreated, vCreated, vUserId);
			END
		");
		$body .= ctlPrint($procedures['CreateArticle'], "Creating the", "procedure");
	  
	  	//Procedure used for updating articles
		$stmt = $dbc->query("
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
		");
		$body .= ctlPrint($procedures['UpdateArticle'], "Creating the", "procedure");
	  	
		//Procedure used for displaying an article
		$stmt = $dbc->query("
			CREATE PROCEDURE $procedures[DisplayArticle](IN vIdArticles BIGINT, IN vCurUser BIGINT)
			BEGIN
				SELECT A.*, U.realname, FCheckUserIsOwnerOrAdmin(vIdArticles, vCurUser) as rights 
				FROM $tableArticles A 
				LEFT JOIN $tableUsers U on U.idUsers = A.userId 
				WHERE A.idArticles = vIdArticles;
			END
		");
		$body .= ctlPrint($procedures['DisplayArticle'], "Creating the", "procedure");
		
		//Procedure used for displaying an article
		$stmt = $dbc->query("
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
		");
		$body .= ctlPrint($procedures['ListArticles'], "Creating the", "procedure");
	  	
	  
	  //Creating triggers
	  
	  //Trigger adding users to statistics
	  $stmt = $dbc->query("
			CREATE TRIGGER $triggers[AddStatUser]
				AFTER INSERT ON $tableUsers
				FOR EACH ROW
				BEGIN
			  		INSERT INTO $tableStatistics (userId) VALUES (NEW.idUsers);
				END 
		");
		$body .= ctlPrint($triggers['AddStatUser'], "Creating the", "trigger");
	  
	   $stmt = $dbc->query("
			CREATE TRIGGER $triggers[AddStatArt]
			AFTER INSERT ON $tableArticles
			FOR EACH ROW
			BEGIN
			  UPDATE $tableStatistics SET noOfArticles = noOfArticles+1 WHERE userId = NEW.userId;
			END
		");
		$body .= ctlPrint($triggers['AddStatArt'], "Creating the", "trigger");
		
		 $stmt = $dbc->query("
			CREATE TRIGGER $triggers[DelStatArt]
			AFTER DELETE ON $tableArticles
			FOR EACH ROW
			BEGIN
			  UPDATE $tableStatistics SET noOfArticles = noOfArticles-1 WHERE userId = OLD.userId;
			END 
		");
		$body .= ctlPrint($triggers['DelStatArt'], "Creating the", "trigger");
		
		/************
		 * Creating DATA
		 */
	  
	  $body .= "<tr><td>&nbsp;</td></tr>";
	  //Creating the users
		$stmt = $dbc->query("
			INSERT INTO $tableUsers (username, realname, email, passwd) VALUES 
			('kalle', 'Kalle Kubik', 'kalle@example.com', '".$Users->passwdHash("kalle")."'),
			('erik', 'Erik Estrada', 'erik@example.com', '".$Users->passwdHash("erik")."'),
			('jenna', 'Jenna Jeans', 'jenna@example.com', '".$Users->passwdHash("jenna")."')
		");
		$body .= ctlPrint($tableUsers, "Creating data in the");
		
		
		//Creating the grupper
		$stmt = $dbc->query("
			INSERT INTO $tableGroups (idGroups, shortdesc, groupdesc) VALUES 
			('adm', 'Administratör', 'Administratörerna for sajten'),
			('mod', 'Modes skribent', 'Skriver om mode'),
			('skr', 'Skribent', 'Helt vanlig skribent')
		;
		");
		$body .= ctlPrint($tableGroups, "Creating the data for");
		
		//Mappar användare mot grupper
		$stmt = $dbc->query("
			INSERT INTO $tableGroupUsers (idGroups, idUsers) VALUES 
			('adm', 1), 
			('mod', 2),
			('skr', 3)
		;
		");
		$body .= ctlPrint($tableGroupUsers, "Creating the data for");
		
		//Creating the dummy data if this is chosen
		if ($_POST['dummyData'] == '1') {
			//Creating the användare
			$success = null;
			$dbc->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
			try {
				$stmt = $dbc->prepare("call $procedures[CreateArticle](?, ?, ?, ?)");
				$stmt->execute(array(time(), "Den första test artikel", "Här testar vi må en massa!", 1));
				$stmt->execute(array(time(), "Den andra test artikeln", "Här testar vi må en massa!", 2));
				$stmt->execute(array(time(), "Den tredje test artikeln", "Här testar vi må en massa!", 3));
				$success .= "<td style='color: #469E34;'>Succeded</td>";
			}
			catch ( exception $e )
			{
				$fail[] = $e->getMessage();
				$success .= "<td style='color: #CC0000;'>Failed</td>"; 
			}
		}
		
		$body .= "
			<tr>
				<td>Creating data in the table {$tableArticles}... &nbsp; &nbsp; &nbsp; </td>
				$success
		";
		
		$dbc->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		$dbc->commit();
	}
	
	foreach($fail as $fel) {
		if ($fel != "0") {
			$_SESSION['errorMessage'][] = $fel;
			$fault = true;
		}
	}
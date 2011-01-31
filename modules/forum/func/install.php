<?php
	
	/**********
	 *  install file for the forum
	 *  
	 */

	//Tablenames
	$tables = array(
		"topics" => DB_PREFIX . "Topics",
		"posts"  => DB_PREFIX . "Posts",
		"drafts" => DB_PREFIX . "PostsDraft",
	);
	
	$procedures = array(
		"handleDraftPost" => DB_PREFIX . "handleDraftPost",
		"publishPost"	  => DB_PREFIX . "publishPost",
		"getPostOrDraft"  => DB_PREFIX . "getPostOrDraft",
		"discardPost"	  => DB_PREFIX . "discardPost",
	); 
	
	$dbc->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
	
	$sqlTableCreate = array();
	
	//Creating table handling topics
	$sqlTableCreate['topics'] = "
		CREATE TABLE IF NOT EXISTS $tables[topics] (
			-- Primary key(s)
		  	idTopics BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		
		  	-- Attributes
		  	title    VARCHAR(255) NOT NULL,
		  	created  BIGINT(40)   NOT NULL,
		  	idUsers   BIGINT		NOT NULL
		  
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	//Creating table handling posts
	$sqlTableCreate['posts'] = "
		CREATE TABLE IF NOT EXISTS $tables[posts] (
	
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
	  		Published   BOOL		 NOT NULL,
	  
	  		FOREIGN KEY (idTopics)
				REFERENCES $tables[topics](idTopics)
				ON UPDATE CASCADE ON DELETE CASCADE
	  
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	//Creating table handling drafts for posts
	$sqlTableCreate['drafts'] = "
		CREATE TABLE IF NOT EXISTS $tables[drafts] (
	
			-- Primary key(s)
		  	idDrafts BIGINT AUTO_INCREMENT NULL PRIMARY KEY,
		  
		  
		  	-- Foreign key(s)
		  	idPosts BIGINT NULL ,
		  
		  	-- Attributes
		  	title    	VARCHAR(255) NOT NULL,
		  	post		LONGTEXT	 NOT NULL,
		  	updated		BIGINT(40)   NOT NULL,
		  	idUsers 	BIGINT 		 NOT NULL,
		  	idTopics	BIGINT		 NOT NULL,
		  	
		 	FOREIGN KEY (idPosts)
				REFERENCES $tables[posts](idPosts)
				ON UPDATE CASCADE ON DELETE CASCADE
		  
		) ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
	";
	
	/*
	 * Creates the proceduers used by this module
	 */ 
	$sqlProcsCreate = array();
	
	//Procedure for handling draft posts
	$sqlProcsCreate['handleDraftPost'] = "
		CREATE PROCEDURE $procedures[handleDraftPost] (IN vidPosts BIGINT, IN vTitle VARCHAR(255), IN vContent TEXT, IN vUpdated BIGINT(50), IN vIdUsers BIGINT, IN vIdTopics BIGINT, OUT vUsedPost BIGINT)
	    BEGIN
	     DECLARE existing INT;
	        IF (vidPosts = 0)  then
	            INSERT INTO $tables[posts](title, post, created, updated, idUsers, idTopics) 
	            VALUES (vTitle, vContent, vUpdated, vUpdated, vIdUsers, vIdTopics);
	            SELECT LAST_INSERT_ID() INTO vidPosts;
	            INSERT INTO $tables[drafts] (idPosts, idTopics, idUsers, title, post, updated) 
	            VALUES (vidPosts, vIdTopics, vIdUsers, vTitle, vContent, vUpdated);
	        ELSE
	            SELECT count(idDrafts) INTO existing FROM $tables[drafts] WHERE idPosts = vidPosts AND idUsers = vIdUsers;
	            IF (existing = 1) then
	                UPDATE $tables[drafts] SET title = vTitle, post = vContent, idTopics = vIdTopics,updated = vUpdated WHERE idPosts = vidPosts AND idUsers = vIdUsers;
	            ELSE
	                INSERT INTO $tables[drafts] (idPosts, idTopics, idUsers, title, post, updated) VALUES (vidPosts, vIdTopics, vIdUsers, vTitle, vContent, vUpdated);
	            END IF;
	        END IF;
			
			SET vUsedPost = vidPosts; 
	    END
	";
	
	//Procedure for publishing posts
	$sqlProcsCreate['publishPost'] = "
		CREATE PROCEDURE $procedures[publishPost](IN vidPosts BIGINT) 
	    BEGIN
	    	UPDATE $tables[posts] p
			INNER JOIN $tables[drafts] d
			ON p.idPosts = d.idPosts
			SET p.title = d.title, p.post = d.post, p.updated = d.updated, Published = 1
			WHERE p.idPosts = vidPosts;
	       
	        DELETE FROM $tables[drafts] WHERE idPosts = vidPosts;
	    END
	";
	//Procedure for getting posts at pages using the editor
	$sqlProcsCreate['getPostOrDraft'] = "
		CREATE PROCEDURE $procedures[getPostOrDraft](IN vIdPosts BIGINT)
		BEGIN
		    DECLARE draftNewer BIGINT(40);
		          
		    SELECT count(p.idPosts) INTO draftNewer FROM $tables[posts] p
		    LEFT JOIN $tables[drafts] d ON d.idPosts = p.idPosts
		    WHERE p.idPosts = vIdPosts AND d.updated > p.updated;
		    
		    IF (draftNewer = 0) then
		        SELECT * FROM $tables[posts] WHERE idPosts = vIdPosts;
		    ELSE
		        SELECT * FROM $tables[drafts] WHERE idPosts = vIdPosts;
		    END IF;
		END
	";
	
	//FAIL!
	$sqlProcsCreate['discardPost'] = "
		CREATE PROCEDURE $procedures[discardPost](IN vIdPosts BIGINT) 
		BEGIN
		   	DECLARE vPublish BOOL;
		   	DECLARE vTopicPosts INT;
		   	DECLARE vTopicId INT;
			
			SELECT 
		   		Published,
		   		idTopics
		   	INTO 
          		vPublish, 
		   		vTopicId
		   	FROM $tables[posts]
			WHERE idPosts = vIdPosts;
			
		   	SELECT 
		   		COUNT(idPosts) INTO vTopicPosts
		    FROM forum_posts 
			WHERE idTopics = vTopicId AND Published = 1;
		   			   
		    IF (vPublish = 0) then
		        DELETE FROM $tables[posts] WHERE idPosts = vIdPosts;
			ELSE
				DELETE FROM $tables[drafts] WHERE idPosts = vIdPosts;
		    END IF;
			
			IF (vTopicPosts = 0) then
				DELETE FROM $tables[topics] WHERE idTopics = vTopicId;
			END IF;
		END
	";
	
	if ($_POST['dummyData'] == '1') {
		$sqlCreateData = array();
		$sqlCreateData['topics'] = "
			INSERT INTO $tables[topics] (title, created, idUsers)
				VALUES ('Default topic', '".time()."', 1)
			";
		
		$sqlCreateData['posts'] = "
			INSERT INTO $tables[posts] (title, post, created, updated, idUsers, idTopics, Published) 
				VALUES ('Default topic', 'This is just the default post in the default topic', '".time()."', '".time()."', 1, 1, 1)
			";
	
	}
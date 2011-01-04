<?php
    
	/**********
	 *  install file for the blog module
	 *  
	 */
	
	$moduleName = "blog";
	
	//Tablenames
	$tables = array(
#		"Categories" => DB_PREFIX . "{$moduleName}Categories",		##NOT YET IMPLEMENTED
		"Posts" 	 => DB_PREFIX . "{$moduleName}Posts",
		"Comments" 	 => DB_PREFIX . "{$moduleName}Comments",
	);
		
		
	$tableUsers = DB_PREFIX . "Users";
		
		$sqlTableCreate = array();
		
		
		$sqlTableCreate['Posts'] = "
			CREATE TABLE $tables[Posts] (
				-- Primary key(s)
				idPosts BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
				
				-- Foreign key(s)
				author   		BIGINT 		NULL,
				
				-- Attributes
				header   		VARCHAR(255)NOT NULL,
				content  		TEXT		NOT NULL,
				creationDate 	BIGINT(40)	NOT NULL,
				
				FOREIGN KEY (author)
					REFERENCES $tableUsers(idUsers)
					ON DELETE SET NULL ON UPDATE CASCADE
				
			) 
			ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
		";
		
		//Creates a table to hold comments related to the blog posts
		$sqlTableCreate['Comments'] = "
			CREATE TABLE $tables[Comments] (
			-- Primary key(s)
				idComments BIGINT AUTO_INCREMENT NOT NULL PRIMARY KEY,
				
			-- Foreign key(s)
				idPosts BIGINT NOT NULL, 
				
			-- Attributes
				header   		VARCHAR(255)NOT NULL,
				content  		TEXT		NOT NULL,
				creationDate 	BIGINT(40)	NOT NULL,
				author   		VARCHAR(255)NOT NULL,
				authorEmail		VARCHAR(255)NOT NULL,
				authorSite		VARCHAR(255)NOT NULL,
			INDEX (idPosts),
			FOREIGN KEY (idPosts)
				REFERENCES $tables[Posts](idPosts)
				ON UPDATE CASCADE ON DELETE CASCADE
			)
			ENGINE=InnoDB CHARSET=utf8 COLLATE utf8_swedish_ci
		";
		
		$sqlCreateData = array();
		
		$sqlCreateData['Posts'] = "
			INSERT INTO $tables[Posts] (header, content, creationDate, author) VALUES 
				('Lorem ipsum dolor sit amet', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum volutpat felis non dolor tincidunt gravida. Sed feugiat eleifend nibh eu porta. Donec vitae est a ligula consectetur pellentesque. Curabitur rhoncus lobortis quam, in cursus ipsum volutpat condimentum. In eu erat ac leo tristique volutpat at in sapien. Curabitur in est nisl, ut blandit nisi. Proin bibendum, magna et fringilla suscipit, libero tellus pulvinar sem, vitae rutrum urna mauris eget lorem. Proin convallis, justo vehicula porttitor elementum, arcu est adipiscing sapien, a congue nulla arcu at lorem. Aliquam sem elit, laoreet in laoreet sed, malesuada eu dui. Aenean ut mi dui, sed viverra dui.', ".time().", 1), 
				('Duis feugiat semper justo', 'Duis feugiat semper justo, at viverra libero porttitor at. Praesent at ligula nisl. Nullam dignissim, arcu ut ultricies aliquet, odio arcu condimentum odio, aliquet ultricies lacus mi eget ante. Morbi elit elit, egestas at iaculis a, tempor ut lorem. Suspendisse mattis turpis non orci bibendum molestie. Morbi rhoncus libero vitae velit mollis non vehicula elit faucibus. Nulla sit amet leo ut ipsum venenatis dignissim. Cras in diam nisi, id viverra quam. Ut tempor imperdiet vehicula. Aliquam suscipit varius laoreet. Vivamus sodales consectetur felis sit amet accumsan. Ut ante risus, laoreet vel imperdiet ac, tempor nec neque. Vivamus nibh turpis, rutrum non dignissim ut, posuere eget elit. Nullam mauris risus, fringilla id luctus ac, malesuada nec massa. Vivamus nunc augue, vestibulum ac viverra a, venenatis sed tellus. Vivamus gravida bibendum consequat. ', ".time().", 1),
				('Nunc vitae felis justo', 'Nunc vitae felis justo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc ultricies, mi vel accumsan pretium, dui erat feugiat risus, sed cursus risus velit nec justo. Etiam fermentum imperdiet orci a aliquet. Maecenas mauris leo, volutpat quis iaculis vel, volutpat eu dolor. Suspendisse suscipit consequat suscipit. Ut vel nunc quis lorem consectetur rhoncus. Vivamus dapibus bibendum risus. Ut urna magna, ultrices id aliquet id, fringilla nec augue. Nam ac nisi quam. Cras ornare nisi vel nisl tincidunt commodo. Nunc vulputate, metus ac dapibus tempor, quam mauris fringilla arcu, ac fermentum nulla nulla sodales tortor. Mauris consectetur quam nec libero sagittis in placerat ante auctor. Etiam blandit bibendum turpis, eget varius magna molestie ac. In velit lorem, porta quis gravida quis, imperdiet vel ante. Proin luctus tellus id orci tempor eleifend dapibus tortor blandit. Nunc malesuada, eros sed tincidunt pretium, quam purus hendrerit tellus, a ultricies nulla ante et orci. Sed mi sapien, bibendum eu tempor sed, semper sit amet nisi. Nulla ac sagittis lorem. Integer blandit risus id urna auctor consectetur vestibulum eros consequat. ', ".time().", 2),
				('Vivamus iaculis scelerisque ', 'Vivamus iaculis scelerisque sem at ultricies. Vivamus eget ipsum ut nisl venenatis semper. Sed cursus ornare ante at pellentesque. Donec viverra imperdiet augue, vitae ornare lectus cursus at. Etiam egestas urna eu velit rhoncus tempor. Nam porttitor eros eget felis vestibulum tincidunt iaculis enim congue. Nam venenatis metus neque, a lacinia massa. Cras nec eros turpis. Fusce magna velit, gravida at varius a, vulputate ac mauris. Integer eros nibh, mattis sed hendrerit sit amet, pharetra et nulla. Donec ipsum elit, varius quis bibendum quis, sollicitudin non justo. Duis sapien mi, commodo ac malesuada id, vulputate eu turpis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut ornare risus eget urna fermentum posuere. Sed imperdiet ipsum vel eros lacinia non sollicitudin magna faucibus. Fusce placerat bibendum ipsum, quis facilisis sapien sagittis ac. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla id ipsum ut tellus ultricies vulputate at eu sem. Cras lobortis velit a nisi pharetra mattis. Maecenas ullamcorper mi non leo luctus laoreet. ', ".time().", 1),
				('Aenean pretium ultrices quam', 'Aenean pretium ultrices quam, sed imperdiet orci laoreet at. Morbi tellus orci, mattis feugiat iaculis vel, tempus sed nisl. Etiam eu ipsum in sapien euismod accumsan. Quisque elementum commodo leo, quis sodales libero malesuada id. Mauris a luctus nulla. Ut sit amet augue non massa lacinia auctor eget non ipsum. Curabitur ultricies laoreet egestas. Pellentesque nisl felis, varius a iaculis lacinia, pulvinar id justo. Maecenas congue sodales purus, dictum accumsan purus fringilla et. Vestibulum facilisis dictum dapibus. Cras felis turpis, sodales et lobortis at, sollicitudin at arcu. Aliquam fringilla fermentum nisi, eu dignissim nisi facilisis sed. Donec convallis fringilla magna vel lacinia. In aliquam, lectus ac vehicula hendrerit, erat augue ultricies nisl, nec hendrerit lacus quam ut velit. Sed eleifend accumsan sodales. Mauris suscipit diam vitae nisl ultrices eu vestibulum sem semper. Mauris augue mauris, commodo eu fermentum sit amet, vulputate ut turpis. ', ".time().", 3),
				('Morbi nec ornare purus', 'Morbi nec ornare purus. Integer tempus, nibh at accumsan consectetur, erat ante sagittis magna, ut dapibus urna elit vitae nisl. Aliquam tortor metus, condimentum ut pulvinar ut, luctus a nisl. Quisque ultrices ultricies ipsum, eget tincidunt mauris porttitor ut. Phasellus tempus eros ac risus pharetra et venenatis magna tincidunt. In enim velit, sodales sit amet scelerisque vel, tincidunt vel dui. Morbi arcu felis, fringilla eu ullamcorper eget, ornare vel tellus. Donec felis elit, convallis eget dictum a, mollis id eros. Quisque et erat lacus, id adipiscing neque. Sed congue pulvinar nulla, ut dictum purus egestas ut. Nunc lacinia ligula vel turpis consectetur porta. Donec eu lorem et metus mattis porttitor. Aenean quis eros quis lorem sagittis suscipit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi lacinia lobortis hendrerit. Pellentesque suscipit, mi vel placerat posuere, diam diam facilisis nibh, vel tempor ipsum velit at velit. ', ".time().", 2),
				('Nullam dictum leo', 'Nullam dictum leo vitae purus varius blandit. Suspendisse ipsum libero, molestie in convallis sed, vulputate ac ante. Suspendisse ut placerat augue. Nunc adipiscing dapibus sem, nec lobortis tortor mattis vitae. Duis dictum suscipit enim, vitae mollis justo porta sed. Etiam porttitor, urna non porttitor rhoncus, ante ligula viverra neque, at bibendum nulla ipsum quis lectus. Integer interdum felis a erat pellentesque vitae volutpat erat rhoncus. Vivamus sollicitudin augue ac leo tempor eu porta metus pretium. Cras vel velit velit, vitae eleifend nibh. Duis a ultrices tellus. Suspendisse orci lectus, interdum sit amet posuere a, commodo ut sapien. Mauris sit amet quam nulla. Duis non odio at tortor blandit fringilla eget vitae nisi. Donec aliquam, tortor sit amet blandit pharetra, enim eros lacinia metus, non consectetur neque tortor nec sapien. Etiam pretium velit nec orci vulputate a tincidunt nunc aliquam. Donec ac lacus sed sem volutpat dignissim non ac magna. Vestibulum non tellus tellus, at elementum quam. Nam eu nisl in eros ultricies suscipit gravida quis massa. ', ".time().", 1),
				('Sed placerat', 'Sed placerat, massa a posuere luctus, nisi mi sollicitudin mi, vitae consequat neque ante in massa. Suspendisse consectetur laoreet lectus non iaculis. Aliquam interdum pellentesque pretium. Sed sit amet sapien non leo bibendum porttitor a id quam. Cras facilisis aliquam augue. Mauris tincidunt arcu ac ante gravida scelerisque. Aliquam suscipit placerat est, eget fringilla lectus pharetra vitae. Sed metus ipsum, pellentesque vitae venenatis sit amet, ornare et mauris. Proin hendrerit mollis sem. Nulla vulputate malesuada neque vel luctus. ', ".time().", 1),
				('In tincidunt nunc', 'In tincidunt nunc ac turpis tincidunt mollis. Donec at odio ut felis fringilla hendrerit. Aliquam tincidunt enim vitae lorem iaculis condimentum. Curabitur aliquet, velit sit amet eleifend accumsan, dui velit hendrerit nulla, a lobortis lectus nisl ut leo. In lobortis fermentum tortor, sit amet suscipit tortor hendrerit ornare. Nunc molestie dapibus lacus nec elementum. Sed pulvinar rutrum tortor, sit amet rutrum lacus consectetur non. Ut arcu metus, rutrum nec egestas pretium, bibendum sit amet diam. Donec vitae eros justo, sed ullamcorper dolor. Cras adipiscing ipsum at lectus auctor laoreet. Morbi a tempus ligula. Cras purus libero, scelerisque sed tempus vitae, vulputate eu lorem. Duis adipiscing ultricies elit a suscipit. Curabitur vestibulum imperdiet est non convallis. Quisque vitae erat nec justo imperdiet pellentesque. Aliquam quis turpis nibh, id placerat tellus. ', ".time().", 3),
				('Praesent tortor arcu', 'Praesent tortor arcu, molestie ac tempus at, dictum vel enim. Duis lobortis nisi quis est fermentum sit amet commodo sapien consectetur. Nunc a nulla vel libero convallis congue nec in purus. Nulla tincidunt arcu ac tortor varius posuere. Nam mollis rhoncus risus, vel vulputate orci mollis non. Aliquam erat volutpat. Nulla suscipit, est elementum congue tincidunt, arcu erat iaculis sapien, et dignissim elit diam eu risus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Donec cursus quam vitae ipsum volutpat in tristique tellus vestibulum. Maecenas quis posuere enim. Nam ipsum lectus, accumsan aliquam semper in, tristique vitae diam. Nam at risus odio. Praesent urna purus, euismod ut accumsan vitae, tristique eu urna. Etiam in laoreet sapien. ', ".time().", 1)
					
		";
		
		$sqlCreateData['Comments'] = "
			INSERT INTO $tables[Comments] (idPosts, header, content, creationDate, author, authorEmail, authorSite) VALUES 
				(5, 'Testkommentar', 'Lite test innehåll', ".time().", 'Testarn', 'test@example.com', 'www.example.com'), 
				(2, 'Testkommentar och sånt', 'Lite mer testande av kommentarerna', ".time().", 'Testarn', 'test@example.com', 'www.example.com'),
				(4, 'Testkommentar och sånt', 'Lite mer testande av kommentarerna', ".time().", 'Testarn', '', ''),
				(4, 'Testkommentar och sånt', 'Lite mer testande av kommentarerna', ".time().", 'Testarn', '', 'www.example.com'),
				(4, 'Testkommentar och sånt', 'Lite mer testande av kommentarerna', ".time().", 'Testarn', 'test@example.com', ''),
				(1, 'Lorem ipsum som morse', '.-.. --- .-. . -- / .. .--. ... ..- -- / -.. --- .-.. --- .-. / ... .. - / .- -- . - --..-- / -.-. --- -. ... . -.-. - . - ..- .-. / .- -.. .. .--. .. ... -.-. .. -. --. / . .-.. .. - .-.-.- / -.-. ..- .-. .- -... .. - ..- .-. / -. . -.-. / -.. .. -.-. - ..- -- / ...- . .-.. .. - .-.-.- / -. .- -- / -.-. --- -- -- --- -.. --- / .- ..- -.-. - --- .-. / .. .--. ... ..- -- / .. -.. / ... ..- ... -.-. .. .--. .. - .-.-.- / -. ..- .-.. .-.. .- / -. ..- -. -.-. / .- .-. -.-. ..- --..-- / . .-.. . .. ..-. . -. -.. / . - / ... --- .-.. .-.. .. -.-. .. - ..- -.. .. -. / . --. . - --..-- / -- .- - - .. ... / ..- - / -.. .. .- -- .-.-.- / -. ..- .-.. .-.. .- -- / .- / .-. ..- - .-. ..- -- / .--- ..- ... - --- .-.-.- / .. -. - . --. . .-. / .--. --- .-. - - .. - --- .-. / . .-. .- - / -- .. --..-- / --.- ..- .. ... / -- .- - - .. ... / -- .- --. -. .- .-.-.- / -- --- .-. -... .. / .--. . .-.. .-.. . -. - . ... --.- ..- . / - .-. .. ... - .. --.- ..- . / ... --- -.. .- .-.. . ... .-.-.- / --.- ..- .. ... --.- ..- . / ... .. - / .- -- . - / . --. . ... - .- ... / -. ..- .-.. .-.. .- .-.-.- / -.-. .-. .- ... / -.-. --- -- -- --- -.. --- --..-- / .-. .. ... ..- ... / .. -. / ...- . .... .. -.-. ..- .-.. .- / ..- .-.. .-.. .- -- -.-. --- .-. .--. . .-. --..-- / - ..- .-. .--. .. ... / - --- .-. - --- .-. / - . -- .--. ..- ... / .- .-. -.-. ..- --..-- / .. -. / -.. .. --. -. .. ... ... .. -- / .- ..- --. ..- . / - . .-.. .-.. ..- ... / .- -.-. / ... . -- .-.-.- / ...- . ... - .. -... ..- .-.. ..- -- / -... .. -... . -. -.. ..- -- --..-- / -.. --- .-.. --- .-. / .- - / ..-. . .-. -- . -. - ..- -- / ..-. . ..- --. .. .- - --..-- / .-.. . --- / .-.. . -.-. - ..- ... / --- .-. -. .- .-. . / --.- ..- .- -- --..-- / ... . -.. / .--. .-.. .- -.-. . .-. .- - / -- .- ..- .-. .. ... / -. . --.- ..- . / .- -.-. / .-.. . -.-. - ..- ... .-.-.- / . - .. .- -- / - .. -. -.-. .. -.. ..- -. - / ... . -- / . - / . .-. --- ... / - . -- .--. ..- ... / ...- . .-.. / ...- . ... - .. -... ..- .-.. ..- -- / -. ..- .-.. .-.. .- / - . -- .--. ..- ... .-.-.- / .--. . .-.. .-.. . -. - . ... --.- ..- . / . --. . ... - .- ... / --.- ..- .- -- / -. . -.-. / .--- ..- ... - --- / -.-. --- -. ... . -.-. - . - ..- .-. / ... . -- .--. . .-. .-.-.- / . - .. .- -- / -.. .- .--. .. -... ..- ... --..-- / ..-. . .-.. .. ... / ... .. - / .- -- . - / .--. --- .-. - - .. - --- .-. / - . -- .--. --- .-. --..-- / -. ..- -. -.-. / -. .. ... .-.. / ...- . .... .. -.-. ..- .-.. .- / .-.. .. -... . .-. --- --..-- / .. -. / .-.. .- -.-. .. -. .. .- / .-.. .. -... . .-. --- / - . .-.. .-.. ..- ... / --.- ..- .. ... / -- .- ... ... .- .-.-.- / .- .-.. .. --.- ..- .- -- / -.. .- .--. .. -... ..- ... --..-- / .-.. .. --. ..- .-.. .- / -. --- -. / -- --- .-.. .-.. .. ... / ...- ..- .-.. .--. ..- - .- - . --..-- / .-.. . -.-. - ..- ... / .- -. - . / ..-. .- ..- -.-. .. -... ..- ... / .-.. --- .-. . -- --..-- / -. . -.-. / ..-. . .-. -- . -. - ..- -- / .-.. . -.-. - ..- ... / -. .. ... .. / -.-. --- -. ...- .- .-.. .-.. .. ... / .- ..- --. ..- . .-.-.- / -. ..- -. -.-. / ... . -- .--. . .-. / -.. .- .--. .. -... ..- ... / --.- ..- .- -- / . - / -.. .- .--. .. -... ..- ... .-.-.- / -.. --- -. . -.-. / . .-. --- ... / .-.. .. -... . .-. --- --..-- / .--. . .-.. .-.. . -. - . ... --.- ..- . / . --. . - / .--. --- ... ..- . .-. . / . - --..-- / ...- . ... - .. -... ..- .-.. ..- -- / ... . -.. / --- -.. .. --- .-.-.- / --.- ..- .. ... --.- ..- . / -. . --.- ..- . / ..- .-. -. .- --..-- / -.-. --- -. ... . --.- ..- .- - / .- -.-. / -- --- .-.. .-.. .. ... / . - --..-- / -.-. --- -. ... . -.-. - . - ..- .-. / .- / .- ..- --. ..- . .-.-.- / -- .- . -.-. . -. .- ... / - .. -. -.-. .. -.. ..- -. - / . .-. --- ... / --- -.. .. --- --..-- / . ..- / -.-. --- -. ... . --.- ..- .- - / -- .- ... ... .- .-.-.- / -- .- ..- .-. .. ... / ..- .-. -. .- / -.. ..- .. --..-- / ..-. . .-. -- . -. - ..- -- / ...- .. - .- . / .- ..- -.-. - --- .-. / ..- .-.. - .-. .. -.-. .. . ... --..-- / ..-. .-. .. -. --. .. .-.. .-.. .- / -. --- -. / -. ..- -. -.-. .-.-.-', ".time().", 'MorseGeeken', '', '')
		";
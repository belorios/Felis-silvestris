<?php
	
	//Array holding all pages related to this module and the path for them
	$modulePages = array(
		
		//Handling login and logout
		"login"			=> "login/login.php",
		"logout"		=> "login/logoutProcess.php",
		"loginprocess"	=> "login/loginProcess.php",
		
		//Articles
		"createArticle" 	=> "articles/createArticle.php",
		"editArticle" 		=> "articles/editArticle.php",
		"deleteArticle" 	=> "articles/delArticle.php",
		"handleArticles" 	=> "articles/handleArticles.php",
		"article" 			=> "articles/showArticle.php",
		
		//Default pages
		"home"			=> "home.php",
		"hem"			=> "home.php",
		"changestyle"	=> "sitestyle.php",
		"showfiles"		=> "viewfiles/showfiles.php",
		
		//Userpages
		"register"		 => "users/register.php",
		"handleUserForm" => "users/handleUserData.php",
		"showUser"		 => "users/showProfile.php",
		"editProfile"	 => "users/editProfile.php",
	);
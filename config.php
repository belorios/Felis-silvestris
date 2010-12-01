<?php
	
	//Script webpath
	$webPath = $_SERVER['SERVER_NAME'] . str_ireplace("/index.php", "", $_SERVER['SCRIPT_NAME']);
	
	//Settings for the database connection	
	define("DB_USER",   "user");
	define("DB_PASS",   "secret");
	define("DB_HOST",   "localhost");
	define("DB_SCHEMA", "bth");
	define("DB_PREFIX", "forum_");
	
	//Pathes
	define("PATH_PAGES"  , dirname(__FILE__) . "/Pages/");
	define("PATH_SOURCE" , dirname(__FILE__) . "/Src/");
	define("PATH_CLASSES", PATH_SOURCE . "classes/");
	define("PATH_LAYOUT" , PATH_SOURCE . "layout/");
	define("PATH_FUNC"   , PATH_SOURCE . "func/");
	define("PATH_MOD"    , PATH_SOURCE . "mod/");
	define("PATH_RSS"	 , PATH_SOURCE . "rss/feed.xml");

	define("PATH_SITE_LOC" 	  , "http://$webPath");
	define("PATH_SITE" 		  , PATH_SITE_LOC . "/index.php");
	define("PATH_SITE_LAYOUT" , PATH_SITE_LOC . "/Src/layout/");
	define("PATH_CSS" 		  , PATH_SITE_LAYOUT . "css/");	
	define("PATH_SITE_RSS"	  , PATH_SITE_LOC . "/Src/rss/feed.xml");
	
	
	//Default values
	define("APP_HEADER",      "Felis silvestris");
	define("APP_DESCRIPTION", "Try to tame me");
	define("APP_FOOTER",      "Felis silvestris");
	define("APP_VALIDATION", "
		Validates &nbsp;
		<a href=\"http://validator.w3.org/check?uri=referer\">XHTML 5</a> &nbsp; 
		<a href=\"http://jigsaw.w3.org/css-validator/check/referer?profile=css3\">CSS3</a> &nbsp;
	");
	define("APP_STYLE" , PATH_CSS . "std.css");	
	
	//Menu array
	$menuArr = array(
		PATH_SITE => "Home",
		PATH_SITE . "/install" => "Install",
		PATH_SITE . "/showfiles" => "Show files",
		PATH_SITE . "/changestyle/purple" => "Change Stylesheet",
	);
	
	define("APP_MENU", serialize($menuArr));

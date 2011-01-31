<?php
	
	ini_set('display_errors', '1');
	
	//Script webpath
	$webPath = $_SERVER['SERVER_NAME'] . preg_replace("/[A-Z]+.php/i", "", $_SERVER['SCRIPT_NAME']);
	
	//Default values - mooove to database!
	#define("APP_HEADER",      "Felis silvestris");
	#define("APP_DESCRIPTION", "Try to tame me");
	#define("APP_FOOTER",      "Felis silvestris");
	#define("APP_THEME", "default");
	define("APP_VALIDATION", "
		Validates &nbsp;
		<a href=\"http://validator.w3.org/check?uri=referer\">XHTML 5</a> &nbsp; 
		<a href=\"http://jigsaw.w3.org/css-validator/check/referer?profile=css3\">CSS3</a> &nbsp;
	");
	define("APP_STYLE" , "std.css");	
	
	//Filepathes
	define("PATH_PAGES"  , dirname(__FILE__) . "/Pages/");
	define("PATH_SOURCE" , dirname(__FILE__) . "/Src/");
	define("PATH_MODULES", dirname(__FILE__) . "/modules/"); 
	define("PATH_LIB"	 , dirname(__FILE__) . "/libs/");
	
	define("PATH_CONFIG" , PATH_SOURCE . "config/");
	define("PATH_LANG"	 , PATH_SOURCE . "lang/");
	define("PATH_FUNC"   , PATH_SOURCE . "func/");
	define("PATH_MOD"    , PATH_SOURCE . "mod/");
	define("PATH_RSS"	 , PATH_SOURCE . "rss/feed.xml");
	$default_classes_path = PATH_SOURCE . "classes/";
	
	
	
	//Webpathes
	define("PATH_SITE_LOC" 	  , "http://$webPath");
	define("PATH_SITE" 		  , PATH_SITE_LOC . "index.php");
	define("PATH_SITE_SRC" 	  ,	PATH_SITE_LOC . "Src/");
	
	define("PATH_SITE_RSS"	  , PATH_SITE_SRC . "rss/feed.xml");
	define("PATH_JS" 	 	  , PATH_SITE_SRC . "javascript/");
	define("PATH_SITE_LIBS"	  , PATH_SITE_LOC . "/libs/");
	define("PATH_SITE_MODS"   , PATH_SITE_LOC . "/modules/");
	
	
	//Settings for the database connection
	define("USE_DB", true);	
	require_once(PATH_CONFIG . "sql-config.php");
	
	$menuArr = array(
		"home" 		=> "Home",
	);
	
	//Get moduleconfiguration
	require_once(PATH_CONFIG . "module-config.php");
	
	define("PATH_CLASSES", serialize($classes));
	define("PATH_MODCSS",  serialize($css));
	
	$menuArr["showfiles"] = "Show files";
	#$menuArr["changestyle/purple"] = "Change Stylesheet";
	//Menu array
	
	define("APP_MENU", 	   serialize($menuArr));
	define("APP_USERMENU", serialize($userMenu));
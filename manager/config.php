<?php
	
	ini_set('display_errors', '1');
	
	//Script webpath
	$webPath = $_SERVER['SERVER_NAME'] . preg_replace("/[A-Z]+\/+[A-Z]+.php/i", "", $_SERVER['SCRIPT_NAME']);
	
	//Default values - mooove to database!
	define("APP_HEADER",      "Manager");
	define("APP_DESCRIPTION", "Try to tame me");
	define("APP_FOOTER",      "Felis silvestris");
	define("APP_THEME", "default");
	define("APP_VALIDATION", "
		Validates &nbsp;
		<a href=\"http://validator.w3.org/check?uri=referer\">XHTML 5</a> &nbsp; 
		<a href=\"http://jigsaw.w3.org/css-validator/check/referer?profile=css3\">CSS3</a> &nbsp;
	");
	define("APP_STYLE" , "manager.css");	
	
	$directory = str_replace("manager", "", dirname(__FILE__));
	
	//Filepathes
	define("PATH_PAGES"  , $directory . "/Pages/");
	define("PATH_SOURCE" , $directory . "/Src/");
	define("PATH_MODULES", $directory . "/modules/"); 
	define("PATH_LIB"	 , $directory . "/libs/");
	
	define("PATH_CONFIG" , PATH_SOURCE . "config/");
	define("PATH_LAYOUT" , PATH_SOURCE . "layout/" . APP_THEME . "/");
	define("PATH_LANG"	 , PATH_SOURCE . "lang/");
	define("PATH_FUNC"   , PATH_SOURCE . "func/");
	define("PATH_MOD"    , PATH_SOURCE . "mod/");
	define("PATH_RSS"	 , PATH_SOURCE . "rss/feed.xml");
	$default_classes_path = PATH_SOURCE . "classes/";
	
	//Webpathes
	define("PATH_SITE_LOC" 	  , "http://$webPath");
	define("PARENT_SITE"	  , PATH_SITE_LOC . "index.php");	
	define("PATH_SITE" 		  , PATH_SITE_LOC . "manager/index.php");
	define("PATH_SITE_SRC" 	  ,	PATH_SITE_LOC . "Src/");
	
	define("PATH_SITE_LAYOUT" , PATH_SITE_SRC . "/layout/" . APP_THEME . "/");
	define("PATH_CSS" 		  , PATH_SITE_LAYOUT . "css/");	
	define("PATH_SITE_RSS"	  , PATH_SITE_SRC . "rss/feed.xml");
	define("PATH_JS" 	 	  , PATH_SITE_SRC . "javascript/");
	define("PATH_SITE_LIBS"	  , PATH_SITE_LOC . "/libs/");
	define("PATH_SITE_MODS"   , PATH_SITE_LOC . "/modules/");
	
	
	//Settings for the database connection	
	define("USE_DB", true);
	require_once(PATH_CONFIG . "sql-config.php");
	
	$menuArr = array();
	
	//Get moduleconfiguration
	$manager = true;
	require_once(PATH_CONFIG . "module-config.php");
	
	define("PATH_CLASSES", serialize($classes));
	define("PATH_MODCSS",  serialize($css));
	
	
	define("APP_MENU", 	   serialize($menuArr));
	define("APP_USERMENU", serialize($userMenu));
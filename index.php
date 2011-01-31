<?php
	#session_save_path('/home/saxon/students/20101/krlb10/sessions');
	
	if (!file_exists(dirname(__FILE__) . "/Src/config/sql-config.php")) {
		$webPath = $_SERVER['SERVER_NAME'] . preg_replace("/[A-Z]+.php/i", "", $_SERVER['SCRIPT_NAME']);
		echo "Please visit <a href='http://$webPath/install'>this page</a> to install this application</a>";
		exit;
	}
	
	session_start();
	
	//Configfile holding most configvalue
	require_once("config.php");
		 
	//Autoloads classes
	require_once(PATH_FUNC . "autoloader.php");
	spl_autoload_register('autoLoader');
	
	//Starts some classes for the engine
	$defaults  = new defaults();
	$LangClass = new Language("en");
	
	//Gets the userspace config
	require_once(PATH_CONFIG . "get-app-config.php");
	
	//Imports the htmlpurifier lib
	require_once(PATH_LIB . "htmlpurifier/library/HTMLPurifier.auto.php");
	
	//Files holding the default layout
	require_once(PATH_LAYOUT . "html_layout.php");
	require_once(PATH_LAYOUT . "html_elements.php");
	
	//Starts the pageclass handling the design 
	$PageClass = new CHTMLPage();
	
	//Sets som controle values
	$indexIsVisited  = true;
	$updateCssValues = false;
	$styleVar = null;
		
	//Gets the sites
	require_once(PATH_FUNC . "loadPages.php");
		
	//Checks thrue all design values that the pages can set and sets them to some default values if they isnt set
	$body  	      = (isset($body)) ? $body : null;
	$sideBox 	  = (isset($sideBox)) ? $sideBox : false;
	$layout       = (isset($layout)) ? $layout : false;
	$sideBoxFloat = (isset($sideBoxFloat)) ? $sideBoxFloat : 'right';
	$layout 	  = (isset($_SESSION['Layout'])) ? $_SESSION['Layout'] : $layout;
	
	//Controls the layout and sets it
	if ($layout != false) {
		$PageClass->setLayout($layout);
	}
	
	//Loops thru all stylesheets that is saved to a session and loads them into the pagecontroller	
	$_SESSION['stylesheet'] = isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : array();
	foreach ($_SESSION['stylesheet'] as $key => $style) {
		$PageClass->addStyleSheet($key, null, false, $style);
	}
	
	//Defines parts of the pagelayout
	echo $PageClass->defineHeaders($page); 
	echo $PageClass->definePageBody($body, $sideBox, $sideBoxFloat);
	echo $PageClass->definePageFooter(); 
	
	//Prints the final page	
	echo $PageClass->printPage();	
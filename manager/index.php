<?php
	#session_save_path('/home/saxon/students/20101/krlb10/sessions');
	
	session_start();
	
	//Konfigruationsfilen
	require_once("config.php");
		 
	//Designfiler
	require_once(PATH_LAYOUT . "manager_layout.php");
	require_once(PATH_LAYOUT . "html_elements.php");
	
	//Autoloads classes
	require_once(PATH_FUNC . "autoloader.php");
	spl_autoload_register('autoLoader');
	
	require_once(PATH_LIB . "htmlpurifier/library/HTMLPurifier.auto.php");
	
	//Startar klasser
	$defaults  = new defaults();
	$LangClass = new Language("en");
	$LangClass->getLangFiles();
	$PageClass = new manager_design();
	
	//Startar kontrollv�rde s� att man kan anv�nda pagecontrollers
	$indexIsVisited  = true;
	$updateCssValues = false;
	$styleVar = null;
	
	//Hämtar sidor
	require_once(PATH_FUNC . "loadPages.php");
		
	//Går igenom värden för layouten och sätter standard värden ifall de inte är satta
	$body  	      = (isset($body)) ? $body : null;
	$sideBox 	  = (isset($sideBox)) ? $sideBox : false;
	$layout       = (isset($layout)) ? $layout : false;
	$sideBoxFloat = (isset($sideBoxFloat)) ? $sideBoxFloat : 'right';
	#$layout 	  = (isset($_SESSION['Layout'])) ? $_SESSION['Layout'] : $layout;
	$layout  	  = "2col_std";
	if ($layout != false) {
		$PageClass->setLayout($layout);
	}
	
	$_SESSION['stylesheet'] = isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : array();
	foreach ($_SESSION['stylesheet'] as $key => $style) {
		$PageClass->addStyleSheet($key, null, false, $style);
	}
	
	$Users = new Users();
	if ($Users->checkPrivilegies("adm", PARENT_SITE) == false) {
		echo $_SESSION['errorMessage'];
		echo "<br /><a href='".PARENT_SITE."'>$lang[GO_BACK]</a>";
		$_SESSION['errorMessage'] = null;
		return;
	}
	
	echo $PageClass->defineHeaders($page); 
	echo $PageClass->definePageBody($body, $sideBox, $sideBoxFloat);
	echo $PageClass->definePageFooter(); 
		
	echo $PageClass->printPage();	

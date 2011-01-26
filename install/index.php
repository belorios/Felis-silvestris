<?php
    
	session_start();
	$manager = true;
	//Configfile holding most configvalue
	require_once("config.php");
		 
	//Autoloads classes
	require_once(PATH_FUNC . "autoloader.php");
	spl_autoload_register('autoLoader');
	
	$defaults  = new defaults();
	$LangClass = new Language("en");
	$page = "step1.php";
	
	$LangClass->getLangFiles(dirname(__FILE__) . "/lang/");
	
	//Imports the htmlpurifier lib
	require_once(PATH_LIB . "htmlpurifier/library/HTMLPurifier.auto.php");
	
	//Files holding the default layout
	#require_once(PATH_LAYOUT . "html_layout.php");
	require_once(PATH_LAYOUT . "manager_layout.php");
	require_once(PATH_LAYOUT . "html_elements.php");
	
	//Starts some classes from the engine
	$PageClass = new manager_design(false,false,"manager.css");
	/*
	if (isset($_POST['dummyData'])) {
		require_once(PATH_FUNC . "install.php");
	}
	else {
		$prefixText = (DB_PREFIX != "") ? ", all tables is going to be installed with the prefix ".DB_PREFIX : null;
		$body = "
			<h1>Installation</h1>
			<p>
				This is going to install the database for the application{$prefixText}. <br />
				<span style='color: #e62011; font-weight: bold;'>Warning</span> this is going to remove existing tables. 
			</p>
				
				<form action='' id='installForm' method='post'> 
					Do you want to fill the tables with dummy data? &nbsp;
					 <input type='radio' checked='checked' name='dummyData' value='1' /> Yes
					 <input type='radio' name='dummyData' value='0' /> No 
					 <p>
					 	<input type='submit' name='send' rel='installForm' value='Install' />
					 </p>
				</form>
		";
	}		
		*/
		
	if (isset($_GET['step'])) {
		if ($_GET['step'] == 2) {
			require_once("step2.php");
		}
	}	
	else {
		require_once("step1.php");
	}
		
	//Checks thrue all design values that the pages can set and sets them to some default values if they isnt set
	$body  	      = (isset($body)) ? $body : null;
	$sideBox 	  = (isset($sideBox)) ? $sideBox : false;
	$layout       = (isset($layout)) ? $layout : false;
	$sideBoxFloat = (isset($sideBoxFloat)) ? $sideBoxFloat : 'right';
	$layout 	  = "1col_std";
	
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
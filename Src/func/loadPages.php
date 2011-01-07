<?php
	/**
	 * USE ONLY IF THE SERVER DOESNT SUPPORT MOD_REWRITE
	 */
    	
	$data = (isset($_SERVER['PATH_INFO'])) ? explode("/",$_SERVER['PATH_INFO']) : null;
	$page = isset($data[1]) ? $data[1] : "home";
		
	if (isset($data[2])) {
		
		if (substr($data[2], 0, 2) == "id") {
			$id = substr($data[2], 3);
		}
		else {
			$action = $data[2];
			$id = isset($data[3]) ? substr($data[3], 3) : null;
		}
	}
	
	/**
	 * USE ONLY IF THE SERVER SUPPORT MOD_REWRITE
	 */	
	 /*
	$page   = isset($_GET['p']) ? $_GET['p'] : 'hem';
	$id     = isset($_GET['id']) ? substr($_GET['id'], 3) : null;
	$action = isset($_GET['action']) ? $_GET['action'] : 'none';
	*/
	
	//Sätter sessionen för senaste sidan man kollat på
	$_SESSION['currentPage'] = $page;
	$currentPage = "?p=$page";
	
	$selectedPage   = null;
	$moduleLangPath = false;
	
	function require_file($path) {
		if (file_exists($path)) {
			require_once($path);
		}
	}
	
	foreach ($modules as $module) {
		$module_path =  PATH_MODULES . $module['folder'];
		if (file_exists("$module_path/func/loadPages.php")) {
			require_once("$module_path/func/loadPages.php");

			if (array_key_exists($page, $modulePages)) {
				$selectedPage = "$module_path/pages/" . $modulePages[$page];
				
				//Get config file related to the loaded module
				require_file("$module_path/config.php");
				
				//Load sideboxes related to page and module
				require_file("$module_path/sideboxes/activated.php");
				
				//Load forms
				require_file("$module_path/layout/forms.php");
				
				//Load design elements related to the module
				require_file("$module_path/layout/html_elements.php");
				
				//Gets the language file for the module 
				$moduleLangPath = "$module_path/lang/";
				
				if (isset($activated_Sideboxes)) {
					$sideBox = null;
					foreach($activated_Sideboxes as $box) {
						$sideBox .= require_once("$module_path/sideboxes/$box");
					}
				}
				
			}
		}
	}
	
	switch ($page) {
		
		//Hanterar användare
		
		case "install":
			$selectedPage = "install.php";
		break;
	}
	
	if ($selectedPage == null) {
		$selectedPage = PATH_MODULES . $modules['core']['folder'] . "/pages/nofind.php";
	}
	
	//Gets language
	$LangClass->getLangFiles($moduleLangPath);
	
	//Gets the loaded page
	require_once($selectedPage);
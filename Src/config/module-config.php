<?php

	/*******
	 * module-config.php
	 * Loads the configuration from activated modules
	 */
	
	require_once(PATH_MODULES . "config.php");
	
	$manager = isset($manager) ? $manager : false; 
	
	if ($manager == true) {
		$modules = array_merge($manager_modules, $modules);
	}
	
	$classes  = array($default_classes_path);
	$css	  = array();
	$userMenu = array();
	foreach ($modules as $module) {
		$modulepath = PATH_MODULES . $module['folder'];
		//Adds an entry to the top menu if the module has one defined
		if ($module['menuEntry'] != null) {
			$menuArr[$module['folder']] = $module['menuEntry'];
		}
		
		if (isset($module['userMenu'])) {
			foreach($module['userMenu'] as $key => $item) {
				$userMenu[$key] = $item;
			}
		}
		
		//Adds the moudles classes to the path
		$classes[] .= "$modulepath/classes/";
		
		//Gets  the layout related to modules
		if (is_dir("$modulepath/layout/")) {
			$laydir = opendir("$modulepath/layout/");
			while (false !== ($file = readdir($laydir))) {
				if (is_file("$modulepath/layout/$file")) {
					$fileparts = explode(".", $file);
					if (end($fileparts) == "css") {
						$path = PATH_SITE_LOC . "/modules/$module[folder]/layout/$file";
						$css[$file] = "<link rel='stylesheet'  href='$path' type='text/css' media='screen' />";
					}
				}	
			}
		}
	}
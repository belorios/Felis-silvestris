<?php
	
	$LangClass->getLangFiles(false);

	$Conf = new Configuration();
	
	try {
		$ConfItems = $Conf->getAllConfigItems(true);
	}
	catch ( Exception $e ) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}

	$defValArr = array(
		"app_name" => "APP_HEADER",
		"app_footer" => "APP_FOOTER",
		"app_theme" => "APP_THEME",
		"app_payoff" => "APP_DESCRIPTION",
		"app_theme"  => "APP_THEME",
	);
	
	foreach ($ConfItems as $item) {
		
		//Checks for the type of the config, if it is a multi it pics the values from that
		$type = substr($item['type'], 0, 5);
		if ($type == "multi") {
				
			try {
				$ConfValues = $Conf->getConfigValuesForEdit($item['idConfig'], $item['descname']);
			}
			catch ( Exception $e ) {
				$_SESSION['errorMessage'][] = $e->getMessage();
				return;
			}
			foreach ($ConfValues as $value) {
					
				if (array_key_exists($value['name'], $defValArr)) {
					define($defValArr[$value['name']], $value['value']);
				}
				else {
					define(strtoupper($value['name']), $value['value']);
				}
					
			}
		}
		else {
			
			if (array_key_exists($item['name'], $defValArr)) {
				define($defValArr[$item['name']], $item['value']);
			}
			else {
				define(strtoupper($item['name']));
			}
				
		}
		
	}
	
	foreach ($defValArr as $key => $item) {
		if (!defined($item)) {
			define($item, "default");
		}	
	}
	
	define("PATH_LAYOUT" , PATH_SOURCE . "layout/" . APP_THEME . "/");
	define("PATH_SITE_LAYOUT" , PATH_SITE_SRC . "/layout/" . APP_THEME . "/");
	define("PATH_CSS" 		  , PATH_SITE_LAYOUT . "css/");	

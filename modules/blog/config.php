<?php
	
	 $userMenu = array(
	 	"newTopic" => 	array(
					 		"url"  => "newTopic",
					 		"desc" => "Create new topic",
					 	),
	 );	
	 
	 define('MODULE_USERMENU', serialize($userMenu));
	 
	 define('THIS_PATH', PATH_MODULES . "blogg/");

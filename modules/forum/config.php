<?php

	/**
	 * Module forum config.php
	 * 
	 * Contains configuration thingys
	 *  
	 */
	 
	 $userMenu = array(
	 	"newTopic" => 	array(
					 		"url"  => "newTopic",
					 		"desc" => "Create new topic",
					 	),
	 );	
	 
	 define('MODULE_USERMENU', serialize($userMenu));
	 
	 define('THIS_PATH', PATH_MODULES . "forum/");

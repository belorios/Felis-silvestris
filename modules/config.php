<?php

	/**
	 * Modules config.php
	 * 
	 * Contains activated modules
	 *  
	 */
	 
	 /********************
	  * Activated modules
	  */
	 $modules = array();
	 
	 //Core module -- DO NOT REMOVE
	 $modules['core'] = array(
	 	"menuEntry"  	=> null,
	 	"Description"	=> "Core modules",
	 	"folder"    	=> "core",
	 ); 

	 //Forum
	 $modules['forum'] = array(
	 	"menuEntry"  	=> "Forum",
	 	"Description"	=> "Basic forum",
	 	"folder"    	=> "forum",
	 	"userMenu"		=> array(
	 		"newTopic" => 	array(
				"url"  => "newTopic",
				"desc" => "Create new topic",
			),
			"createArticle" => array(
				"url" 	=> "createArticle",
				"desc"	=> "Write new article",
			)
		),	
	 ); 
	 
	$modules['blogg'] = array(
		"menuEntry"  	=> "Blog",
	 	"Description"	=> "Basic blog",
	 	"folder"    	=> "blog",
	 	"userMenu"		=> array(
	 		"newBlogPost" => 	array(
				"url"  => "newBlogPost",
				"desc" => "Create new blogpost",
			),
		),	
	 );
	/********************
	* Unactivated modules
	*/
	$modules_dead = array();
	 
	$modules_dead['blogg'] = array(
		"menuEntry"  	=> "Blogg",
	 	"Description"	=> "Basic blogg",
	 	"folder"    	=> "blogg",
	 	"userMenu"		=> array(
	 		"newBlogPost" => 	array(
				"url"  => "newBlogPost",
				"desc" => "Create new blogpost",
			),
		),	
	 );
	
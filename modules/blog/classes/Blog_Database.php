<?php

	class Blog_Database extends Database {
		
		protected 	$tablePosts,
					$tableComments,
					$tableTags,
					$tableTagsPosts,
					$db;
					 
		public function __construct($db) {
				
			parent::__construct($db);
			
			$this->tablePosts	  = DB_PREFIX . "blogPosts";
			$this->tableComments  = DB_PREFIX . "blogComments";
			$this->tableTags	  = DB_PREFIX . "blogTags";
			$this->tableTagsPosts = DB_PREFIX . "blogTagsPosts";
			
		}
	}

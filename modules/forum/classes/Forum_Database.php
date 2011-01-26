<?php

	class Forum_Database extends Database {
		
		protected 	$tablePosts,
					$tableDrafts,
					$tableTopics,
					
					$procGetPost,
					$procDiscardPost,
					$procHandleDraftPost,
					$procPublishPost;
					 
		public function __construct($db) {
				
			parent::__construct($db);
			
			$this->tablePosts  = DB_PREFIX . "Posts";
			$this->tableDrafts = DB_PREFIX . "PostsDraft";
			$this->tableTopics = DB_PREFIX . "Topics";
			
			$this->procGetPost 		= DB_PREFIX . "getPostOrDraft";
			$this->procDiscardPost 	= DB_PREFIX . "discardPost";
			$this->procHandleDraftPost = DB_PREFIX . "handleDraftPost";
			$this->procPublishPost = DB_PREFIX . "publishPost";
			
			
		}
	}

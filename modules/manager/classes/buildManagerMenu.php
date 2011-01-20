<?php

	class buildManagerMenu extends Manager_Database{
		
		public function __construct($db=false) {
			parent::__construct($db);
		}
		
		public function buildManagerMenu($parent) {
			
			$sql = "
				SELECT * FROM {$this->tableManagerMenu} WHERE parent = ':parent'
			";
			
			$get = $this->db->prepare($sql);
			
			$get->bindParam("parent", $parent, PDO::PARAM_INT);
			
			if ($get->execute()) {
				return $get->fetchAll();
			}
			else {
				$this->debug("Couldnt get the manager menu", $query);
				return false;
			}
		
		}
		
	}

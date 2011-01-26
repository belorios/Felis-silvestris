<?php

	class Configuration extends Database {
		
		public function __construct($db=false) {
			parent::__construct($db);
		}
		
		public function getConfigValue($name) {
			
			$sql = "
				SELECT * FROM {$this->tableConfig} c
				LEFT JOIN {$this->tableConfigValues} cv ON c.idConfig = cv.idConfig
				WHERE name = :name  AND active = 'y'
			";
			$get = $this->db->prepare($sql);
			$get->bindParam("name", $name, PDO::PARAM_STR);
			
			if ($get->execute()) {
				return $get->fetch();
			}
			else {
				$this->debug("Couldnt get the selected configurationvalue, $name", $sql);
				return false;
			}
		}
		
		public function getAllConfigItems($global=false) {
			
			$globalSQL = ($global == true) ? " AND global = 1 " : null;
			
			$sql = "SELECT * FROM {$this->tableConfig} c WHERE active = 'y' $globalSQL";
			$get = $this->db->prepare($sql);	
			
			if ($get->execute()) {
				return $get->fetchAll();
			}
			else {
				$this->debug("Couldnt get the configuration items", $sql);
				return false;
			}
			
		}
		
		public function getConfigValuesForEdit($configId, $name) {
			
			$sql = "SELECT * FROM {$this->tableConfigValues} WHERE idConfig = :idConfig";
			
			$get = $this->db->prepare($sql);
			$get->bindParam("idConfig", $configId, PDO::PARAM_INT);
			
			if ($get->execute()) {
				return $get->fetchAll();
			}
			else {
				$this->debug("Couldnt get the configuration values for this configuration item $name", $sql);
				return false;
			}
			
		}
		
		public function editConfigVal($table, $id, $name, $value) {
			
			$table = str_ireplace(array("conf", "value"), array("{$this->tableConfig}", "{$this->tableConfigValues}"), $table);
			
			$sql = "UPDATE $table SET value = :value WHERE name = :id AND active = 'y' ";
			$get = $this->db->prepare($sql);
			$get->bindParam("id", $id, PDO::PARAM_INT);
			$get->bindParam("value", $value, PDO::PARAM_STR);
			
			if ($get->execute()) {
				return true;
			}
			else {
				$this->debug("Couldnt save the value for the item $name", $sql);
				return false;
			}
		}
	}
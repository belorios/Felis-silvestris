<?php
	
	ini_set('display_errors', '1');

	function ctlPrint($table, $type, $type2='table') {
		$GLOBALS['fail'][$table] = $GLOBALS['pdo']->getFault($GLOBALS['dbc']);
		$body = "
			<tr>
				<td>$type $type2 $table... &nbsp; &nbsp; &nbsp; </td>";
		if ($GLOBALS['fail'][$table] == "0") {
			$body .= "<td style='color: #469E34;'>Succeded</td>";
		}
		else {
			$body .= "<td style='color: #CC0000;'>Failed</td>"; 
		}
		return "$body </tr>";
	}
	
	function showBox($name, $box, $fault, $mess) {
		$msg = ($fault == true) ? "<td style='color: #CC0000;'>Failed</td>" : "<td style='color: #469E34;'>Succeded</td>";
			
		return "
			<tr>
				<td onclick=\"hideShowBox('#show_rm_$name')\" style='cursor: pointer;'>$mess</td>
				$msg
			</tr>
			<tr>
				<td colspan='2'>
					<table id='show_rm_$name'style='display: none; width: 500px; background: #fff; border: 1px solid; margin-left: 10px;'>
						$box
					</table>
				</td>
			</tr>
		";
	}
	
	$pdo   = new pdoConnection();
	$dbc   = $pdo->getConnection(false);
	
	$body   = null;
	$fail  = array();
	$fault = false;	
	$database = DB_SCHEMA;
	$clearOld = TRUE;
	
	$classes = array($default_classes_path);
	$modules_reverse = array_reverse($modules);
	
	/*
	 * Ändra koden så att den först går igenom alla modulers installationskod och lägger till detta i ett stort fett array.
	 * Gå sedan igenom dessa array för att behandla datan, först baklänges för att rensa och sedan framlänges för att lägga in allt igen!
	 */
	
	$moduleArray = array();
	
	foreach ($modules as  $key => $module) {
		$sqlUdfsCreate 		= array();
		$sqlTableCreate 	= array();
		$sqlTriggerCreate 	= array();
		$sqlCreateData 		= array();
		$sqlProcsCreate 	= array();
		
		$udfs 		= array();
		$triggers 	= array();
		$procedures = array();
		$tables 	= array();
		
		require_once(PATH_MODULES . $module['folder'] . "/func/install.php");
			
		$moduleArray[$module['folder']] = array(
			"tables" 	 => $tables,
			"triggers" 	 => $triggers,
			"procedures" => $procedures,
			"udfs" 	 	 => $udfs,
			
			"createTable" => $sqlTableCreate,
			"createTrigg" => $sqlTriggerCreate,
			"createProcs" => $sqlProcsCreate,
			"createUdfs"  => $sqlUdfsCreate,
			"createData"  => $sqlCreateData,
			
		);
			
	}
	
	$modules_reverse = array_reverse($moduleArray);
	
	if ($clearOld == true)  {
		
		$body .= "<tr><td><b>Removing modules</b></td></tr>";
		
		foreach ($modules_reverse as  $name => $module) {
			
			$moduleBody = null;
			
			$Alltables    	= $module['tables'];
			$Alltriggers  	= $module['triggers'];
			$Allprocs	  	= $module['procedures'];
			$Alludfs 	  	= $module['udfs'];
			
			
		
			$RMTables = array_reverse($Alltables);
			
			//Removes old udfs if they exists
			if (count($Alludfs) > 0) {
				foreach ($Alludfs as $udf) {
					$stmt = $dbc->query("DROP FUNCTION IF EXISTS $udf;");	
					$moduleBody .= ctlPrint($udf, "Removing the", "udf");
				}
				$moduleBody .= "<tr><td>&nbsp;</td></tr>";	
			}
			
			
			//Removes old triggers if they exists in db
			if (count($Alltriggers) > 0) {
				foreach ($Alltriggers as $trigger) {
					$stmt = $dbc->query("DROP TRIGGER IF EXISTS $trigger;");	
					$moduleBody .= ctlPrint($trigger, "Removing the", "trigger");
				}
				$moduleBody .= "<tr><td>&nbsp;</td></tr>";	
			}
			
			//Removes old proceduers if they exists
			if (count($Allprocs) > 0) {
				foreach ($Allprocs as $procedure) {
					$stmt = $dbc->query("DROP PROCEDURE IF EXISTS $procedure;");	
					$moduleBody .= ctlPrint($procedure, "Removing the", "procedure");
				}
				$moduleBody .= "<tr><td>&nbsp;</td></tr>";	
			}
			
			//Removes old tables if they exists	
			if (count($RMTables) > 0) {	
				foreach ($RMTables as $key => $table) {
					$stmt = $dbc->query("DROP TABLE IF EXISTS $table");		
					$moduleBody .= ctlPrint($table,  "Removing the");
				}
			}
			
			foreach($fail as $fel) {
				if ($fel != "0") {
					$_SESSION['errorMessage'][] = $fel;
					$fault = true;
					$fail = array();
				}
			}
			
			$body .= showBox("clear{$name}", $moduleBody, $fault, "Deleting the module $name");
			
		}
	}
	
	if ($fault == false) {
		
		$body .= "<tr><td><b>Installing modules</b></td></tr>";
		
		foreach ($moduleArray as  $name => $module) {
			
			
			$Alltables    	= $module['tables'];
			$Alltriggers  	= $module['triggers'];
			$Allprocs	  	= $module['procedures'];
			$Alludfs 	  	= $module['udfs'];
			
			$TableCreate  	= $module['createTable'];
			$TriggerCreate 	= $module['createTrigg'];
			$ProcsCreate   	= $module['createProcs'];
			$UdfsCreate    	= $module['createUdfs'];
			$CreateData	   	= $module['createData'];
			
			$moduleBody = null;
			
			foreach ($TableCreate as $key => $table) {
				$stmt = $dbc->query($table);
				$moduleBody .= ctlPrint($Alltables[$key], "Creating the");
			}
			
			foreach($fail as $fel) {
				if ($fel != "0") {
					$_SESSION['errorMessage'][] = $fel;
					$fault = true;
					$fail = array();
				}
			}
			
			if ($fault == false) {
				
				$dbc->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT );
				
				foreach ($UdfsCreate as $key => $udf) {
					$stmt = $dbc->query($udf);
					$moduleBody .= ctlPrint($Alludfs[$key], "Creating the", "udf");
				}
				foreach ($TriggerCreate as $key => $trigger) {
					$stmt = $dbc->query($trigger);
					$moduleBody .= ctlPrint($Alltriggers[$key], "Creating the", "trigger");
				}	
				
				foreach ($ProcsCreate as $key => $proc) {
					$stmt = $dbc->query($proc);
					$moduleBody .= ctlPrint($Allprocs[$key], "Creating the", "procedure");
				}	
				
				
				if ($_POST['dummyData'] == 1) {
				
					foreach ($CreateData as $key => $table) {
						
						if (is_array($table)) {
							
							$success = null;
							$dbc->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
							
							try {
								foreach ($table as $shunk) {
									if (!is_array($shunk)) {
										$stmt = $dbc->prepare($shunk);
									}
									else {
										$stmt->execute($shunk);
										
									}
								}
								$success .= "<td style='color: #469E34;'>Succeded</td>";
							}
							catch ( exception $e )
							{
								$fail[] = $e->getMessage();
								$success .= "<td style='color: #CC0000;'>Failed</td>"; 
							}
							$moduleBody .= "
								<tr>
									<td>Creating data in the table {$Alltables[$key]}... &nbsp; &nbsp; &nbsp; </td>
									$success
								</tr>
							";
					
						}
						else {
							$stmt = $dbc->query($table);
							$moduleBody .= ctlPrint($Alltables[$key], "Creating ", "data in the table");
						} 
					
					}
				}

				foreach($fail as $fel) {
					if ($fel != "0") {
						$_SESSION['errorMessage'][] = $fel;
						$fault = true;
						$fail = array();
					}
				}
				
				
				$body .= showBox("create{$name}", $moduleBody, $fault, "Installing the module $name");
			
				
				
				
			}
			
		}
	}
	
	
	
	
	if ($fault == true) {
		  
		$success = "
			<p>
				<b>The installation failed</b> <br /> 
				Try to fix the faults and then try again <br />
				<a href='" . PATH_SITE . "/install'>Click here to try again</a>
			</p>
		";
	}
	else {
		
		if ($_POST['dummyData'] == '1') {
      	//Initiera rss flödet
      }	
		
		$success = "<p><b>The installation succeded!</b></p>";
	}
	
	$body = "
		<h1>Installing</h1>
		<p>
			<table style='width: 700px;'>
				$body
			</table>
			$success
		</p>
	";

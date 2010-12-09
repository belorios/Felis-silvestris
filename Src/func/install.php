<?php

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
	
	$body   = null;
	$fail  = array();
	$fault = false;	
	$database = DB_SCHEMA;
	$clearOld = TRUE;
	
	$AllTables 	= array();
	$AllTrig  	= array();
	$AllProcs 	= array();
	$Alludfs  	= array();
	
	$sqlCreate 	= array();
	$udfCreate 	= array();
	$trigCreate = array();
	$procCreate = array();
	$createData = array();
	
	$classes = array($default_classes_path);
	foreach ($modules as $module) {
		require_once(PATH_MODULES . $module['folder'] . "/func/install.php");
		
		$tables   = (isset($tables)) 	 ? $tables 		: array();
		$triggers = (isset($triggers)) 	 ? $triggers 	: array();
		$procs	  = (isset($procedures)) ? $procedures 	: array();
		$udfs 	  = (isset($udfs)) 	 	 ? $udfs 		: array();
		
		$sqlTableCreate   = (isset($sqlTableCreate))   	? $sqlTableCreate 	: array();
		$sqlTriggerCreate = (isset($sqlTriggerCreate)) 	? $sqlTriggerCreate : array();
		$sqlProcsCreate   = (isset($sqlProcsCreate)) 	? $sqlProcsCreate 	: array();
		$sqlUdfsCreate 	  = (isset($sqlUdfsCreate)) 	? $sqlUdfsCreate 	: array();
		$sqlCreateData	  = (isset($sqlCreateData)) 	? $sqlCreateData 	: array();
		
		$AllTables = (array_merge($tables, $AllTables));
		$AllProcs  = (array_merge($procs, $AllProcs));
		$Alludfs   = (array_merge($udfs, $Alludfs));
		$AllTrig   = (array_merge($triggers, $AllTrig));
		
		$sqlCreate  = array_merge($sqlTableCreate, $sqlCreate);
		$udfCreate  = array_merge($sqlUdfsCreate, $udfCreate);
		$trigCreate = array_merge($sqlTriggerCreate, $trigCreate);
		$procCreate = array_merge($sqlProcsCreate, $procCreate);
		$createData = array_merge($sqlCreateData, $createData);
		
	}
	if ($clearOld == true) {
		//Removes old udfs if they exists
		foreach ($Alludfs as $udf) {
			$stmt = $dbc->query("DROP FUNCTION IF EXISTS $udf;");	$body .= ctlPrint($udf, "Removing the", "udf");
		}
		$body .= "<tr><td>&nbsp;</td></tr>";	
		
		//Removes old triggers if they exists in db
		foreach ($AllTrig as $trigger) {
			$stmt = $dbc->query("DROP TRIGGER IF EXISTS $trigger;");	$body .= ctlPrint($trigger, "Removing the", "trigger");
		}
		$body .= "<tr><td>&nbsp;</td></tr>";	
		
		//Removes old proceduers if they exists
		foreach ($AllProcs as $procedure) {
			$stmt = $dbc->query("DROP PROCEDURE IF EXISTS $procedure;");	$body .= ctlPrint($procedure, "Removing the", "procedure");
		}
		$body .= "<tr><td>&nbsp;</td></tr>";	
			
			
		foreach ($AllTables as $key => $table) {
			$stmt = $dbc->query("DROP TABLE IF EXISTS $table");		
			$body .= ctlPrint($table,  "Removing the");
		}
		$body .= "<tr><td>&nbsp;</td></tr>";
		
		
	}
	
	foreach ($sqlCreate as $key => $table) {
		$stmt = $dbc->query($table);
		$body .= ctlPrint($AllTables[$key], "Creating the");
	}
	
	foreach($fail as $fel) {
		if ($fel != "0") {
			$_SESSION['errorMessage'][] = $fel;
			$fault = true;
		}
	}
	
	if ($fault == false) {
		
		foreach ($udfCreate as $key => $udf) {
			$stmt = $dbc->query($udf);
			$body .= ctlPrint($Alludfs[$key], "Creating the", "udf");
		}
			
		foreach ($trigCreate as $key => $trigger) {
			$stmt = $dbc->query($trigger);
			$body .= ctlPrint($AllTrig[$key], "Creating the", "trigger");
		}	
		
		foreach ($procCreate as $key => $proc) {
			$stmt = $dbc->query($proc);
			$body .= ctlPrint($AllProcs[$key], "Creating the", "procedure");
		}	
		
		
		foreach ($createData as $key => $table) {
			
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
				$body .= "
					<tr>
						<td>Creating data in the table {$AllTables[$key]}... &nbsp; &nbsp; &nbsp; </td>
						$success
					</tr>
				";
		
			}
			else {
				$stmt = $dbc->query($table);
				$body .= ctlPrint($AllTables[$key], "Creating ", "data in the table");
			} 
			
			
		}
	}
	
	foreach($fail as $fel) {
		if ($fel != "0") {
			$_SESSION['errorMessage'][] = $fel;
			$fault = true;
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
			<table>
				$body
			</table>
			$success
		</p>
	";
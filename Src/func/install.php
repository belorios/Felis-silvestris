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
	
	$classes = array($default_classes_path);
	foreach ($modules as $module) {
		require_once(PATH_MODULES . $module['folder'] . "/func/install.php");
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
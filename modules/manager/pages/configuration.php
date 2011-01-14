<?php

	$body = "
		<h1>$lang[CONFIGURATION]</h1>
	";

	$Conf = new Configuration();
	
	try {
		$ConfItems = $Conf->getAllConfigItems();
	}
	catch ( Exception $e ) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	function createTextBox($name, $value) {
		return "<td><input type='text' name='$name' value='$value' /></td>";
	}
	
	if (isset($_POST['send'])) {
		foreach ($ConfItems as $item) {
			
			$type = substr($item['type'], 0, 5);
			if ($type != "multi") {
				if ($_POST[$item['name']] != $item['value']) {
					try {
						$Conf->editConfigVal("conf", $_POST[$item['name']], $item['descname'], $_POST['value']);
					}
					catch ( Exception $e ) {
						$_SESSION['errorMessage'][] = $e->getMessage();
						return;
					}
				}
			}
			else {
				$Valtype2 = substr($item['type'], 5);
				try {
					$ConfValues = $Conf->getConfigValuesForEdit($item['idConfig'], $item['descname']);
				}
				catch ( Exception $e ) {
					$_SESSION['errorMessage'][] = $e->getMessage();
					return;
				}
				foreach ($ConfValues as $value) {
					if ($_POST[$value['name']] != $value['value']) {
						try {
							$Conf->editConfigVal("value", $value['name'], $value['descname'], $_POST[$value['name']]);
						}
						catch ( Exception $e ) {
							$_SESSION['errorMessage'][] = $e->getMessage();
							return;
						}
					}
				}
			}
			
		}
	}
	
	$config = null;
	foreach ($ConfItems as $item) {
		
		$type = substr($item['type'], 0, 5);
		switch ($type) {
			
			case "text" :
				$config .= "
					<tr>
						<td>$item[descname]</td>
						" . createTextBox($item['name'], $item['value']) . "
					</tr>	
				";
				break;
			
			case "check" :
				$checked = ($item['value'] == 1) ? "checked='checked'" : null;
				echo "
					<tr>
						<td>$item[descname]</td>
						<td><input type='checkbox' name='$item[name]' value='1' /></td>
					</tr>		
				";
				break;
			
			case "multi" :
				$Valtype2 = substr($item['type'], 5);
				
				$config .= "<tr><td colspan='2' ><h3>$item[descname]</h3></td></tr>";
				try {
					$ConfValues = $Conf->getConfigValuesForEdit($item['idConfig'], $item['descname']);
				}
				catch ( Exception $e ) {
					$_SESSION['errorMessage'][] = $e->getMessage();
					return;
				}
				foreach ($ConfValues as $value) {
					switch ($Valtype2) {
						
						case "text" : 
							$config .= "
								<tr>
									<td>$value[descname]</td>
									" . createTextBox($value['name'], $value['value']) . "
								</tr>
							";
							break;
						case "radio" : 
							$config .= "
								<tr>
									<td>$value[descname]</td>
									<td><input type='radio' name='$value[name]' value='1' /></td>
								</tr>
							";
							break;
						case "check" :
							$config .= "
								<tr>
									<td>$value[descname]</td>
									<td><input type='checkbox' name='$value[name]' value='1' /></td>
								</tr>
							";
							break;
						case "select" :
							break;
						
					}
				}
				break;
		}
	}

	$body .= "
		<form name='configForm' id='configForm' method='post' action=''>
			<table>
				$config
			</table>
			<input type='hidden' name='send' value='1' />
			<input type='submit' rel='configForm' value='Spara' />
		</form>		
	";


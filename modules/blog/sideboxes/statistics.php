<?php

	try {
		$PostsStat = $Posts->getPostsStat();
	}
	catch ( exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	
	return	sideboxLayout("Statistik", "
				<b>Antal inlägg gjorda</b> <br />
				<table>
					<tr>
						<td>Senaste tio dagarna:</td>
						<td>$PostsStat[ten]</td>
					</tr>
					<tr> 
						<td>Senaste månaden:</td>
						<td>$PostsStat[month]</td>
					</tr>
					<tr>
						<td>Senaste året:</td>
						<td>$PostsStat[year]</td>
					</tr>
				</table>
	");
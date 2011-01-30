<?php

	try {
		$PostsStat = $Posts->getPostsStat();
	}
	catch ( exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	return	sideboxLayout($lang['STATISTICS'], "
				<b>{$lang['NUMBER_POSTS_DONE']}</b> <br />
				<table>
					<tr>
						<td>{$lang['LAST_TEN_DAYS']}:</td>
						<td>$PostsStat[ten]</td>
					</tr>
					<tr> 
						<td>{$lang['LAST_MONTH']}:</td>
						<td>$PostsStat[month]</td>
					</tr>
					<tr>
						<td>{$lang['LAST_YEAR']}:</td>
						<td>$PostsStat[year]</td>
					</tr>
				</table>
	");
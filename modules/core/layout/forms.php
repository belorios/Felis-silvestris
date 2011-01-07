<?php


	function regEditForm($formTitle) {
		$lang = $GLOBALS['lang'];
		return "
		
			<h1>$formTitle</h1>
			<form method='post' action=''>
				<table>
					<tr>
						<td>Användarnamn</td>
						<td><input type='text' class='cssTextbox' name='username' value='' /></td>
					</tr>
					<tr>
						<td>$lang[PASSWORD]</td>
						<td><input type='password' class='cssTextbox' name='password' value='' /></td>
					</tr>	
					<tr>
						<td>$lang[EMAIL]</td>
						<td><input required type='email'  name='email' value='' /></td>
					</tr>	
				</table>
				
				<input type='submit' name='submit' value='Register' />
			</form>
		";
		
	}

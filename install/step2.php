<?php
	
	$confPath = PATH_CONFIG. "sql-config.php";
	@$conf = fopen($confPath, "w");
	$confData = '<?php
	/*************
	 * 	sql-config.php
	 * 	Settings for the database connection
	 */

	define("DB_USER",   "' . $_SESSION['formdata']['db_user'] . '");
	define("DB_PASS",   "' . $_SESSION['formdata']['db_pass'] . '");
	define("DB_HOST",   "' . $_SESSION['formdata']['db_host'] . '");
	define("DB_SCHEMA", "' . $_SESSION['formdata']['db_table'] . '");
	define("DB_PREFIX", "' . $_SESSION['formdata']['db_prefix'] . '");
	
	';
	@fwrite($conf, $confData);
	@fclose($conf);
	
	if (!file_exists($confPath)) {
		$body = $lang['CONF_FAIL'];
	}
	else {
		require_once($confPath);
	
		if (isset($_POST['dummyData'])) {
			require_once(PATH_FUNC . "install.php");
		}
		else {
			$prefixText = ($_SESSION['formdata']['db_prefix'] != "") ? ", $lang[INSTALL_TEXT_P2_PREFIX] ".$_SESSION['formdata']['db_prefix'] : null;
			$body = "
				<p>
					{$lang['INSTALL_TEXT_P2_APP']}{$prefixText}. <br />
					<span style='color: #e62011; font-weight: bold;'>$lang[WARNING]</span> $lang[INSTALL_TEXT_P2_WARNING] 
				</p>
					
					<form action='' id='installForm' method='post'> 
						$lang[INSTALL_TEXT_P2_DUMMY] &nbsp;
						 <input type='radio' checked='checked' name='dummyData' value='1' /> $lang[YES]
						 <input type='radio' name='dummyData' value='0' /> $lang[NO] 
						 <p>
						 	<input type='submit' name='send' rel='installForm' value='$lang[INSTALL]' />
						 </p>
					</form>
			";
		}
	} 
	
<?php
	$body = "<h1>{$lang['HEAD_INSTALL']}</h1>";
	
	if (isset($_POST['app_name'])) {

		$_SESSION['formdata'] = $_POST;
		
		#$body .= print_r($_SESSION['formdata'], true);
		$fail = array();
		if (!@mysql_connect($_SESSION['formdata']['db_host'], $_SESSION['formdata']['db_user'], $_SESSION['formdata']['db_pass'])) {
			$fail[] = $lang['DB_CON_FAIL'];
		}
			
		if (!@mysql_select_db($_SESSION['formdata']['db_table'])) {
			$fail[] = $lang['DB_SEL_FAIL'];
		}
		
		$validation = new Validation();
		if (!$validation->checkValues("Name", $_SESSION['formdata']['app_name'], 2)) {
			$fail[] = $lang['FORM_HEAD_FAIL'];
		}
		
		if (!$validation->checkValues("Name", $_SESSION['formdata']['app_footer'], 2)) {
			$fail[] = $lang['FORM_FOOTER_FAIL'];
		}
		
		if (!$validation->checkValues("Name", $_SESSION['formdata']['app_payoff'], 2)) {
			$fail[] = $lang['FORM_PAY_OFF_FAIL'];
		}
		
		
		if (count($fail) > 0) {
			foreach($fail as $fault) {
				$body .= "$fault <br />";
			}
		}
		else {
			header("Location: index.php?step=2");
			exit;
		}
			
	}
	else {
		$_SESSION['formdata']['db_prefix'] = "felis_";
		$_SESSION['formdata']['db_host'] = "localhost";
		$_SESSION['formdata']['db_user'] = null;
		$_SESSION['formdata']['db_pass'] = null;
		$_SESSION['formdata']['db_table'] = null;
		$_SESSION['formdata']['app_payoff'] = null;
		$_SESSION['formdata']['app_footer'] = null;
		$_SESSION['formdata']['app_name'] = null;
	}


	$body .= "
		
		<style>
			label {
				display: block;
				font-size: 9pt;
				margin: 10px 0px 0px 5px;
			}
			
			#box {
				border: 1px solid;
				width: 250px;
				padding: 10px 20px;
			}
			
			#box h2 {
				margin: 0px 0px 10px -5px;
				padding: 0;
				font-size: 1em;
			}
		</style>
	
		<form name='installation_form' method='post' action=''
			<p>
				<p>
					{$lang['INSTALL_TEXT_P1_1']} 
				</p>
				<div id='box'>
					<h2>{$lang['APP_SETTING']}</h2>
					
					<label rel='app_name'>{$lang['FORM_HEAD']}</label>
					<input type='text' name='app_name' id='app_name' value='{$_SESSION['formdata']['app_name']}' />
				
					<label rel='app_footer'>{$lang['FORM_FOOTER']}</label>
					<input type='text' name='app_footer' id='app_footer' value='{$_SESSION['formdata']['app_footer']}' />
				
					<label rel='app_payoff'>{$lang['FORM_PAY_OFF']}</label>
					<input type='text' name='app_payoff' id='app_payoff' value='{$_SESSION['formdata']['app_payoff']}' />
				</div>
			
			</p>
			<p>
				{$lang['INSTALL_TEXT_P1_DB']} 
			</p>
			<div id='box'>
				<h2>{$lang['DB_HEAD']}</h2>
				
				<label rel='db_user'>{$lang['FORM_DB_USER']}</label>
				<input type='text' name='db_user' id='db_user' value='{$_SESSION['formdata']['db_user']}' />
				
				<label rel='db_pass'>{$lang['FORM_DB_PASS']}</label>
				<input type='text' name='db_pass' id='db_pass' value='{$_SESSION['formdata']['db_pass']}' />
				
				<label rel='db_host'>{$lang['FORM_DB_HOST']}</label>
				<input type='text' name='db_host' id='db_host' value='{$_SESSION['formdata']['db_host']}' />
				
				<label rel='db_table'>{$lang['FORM_DB_TABLE']}</label>
				<input type='text' name='db_table' id='db_table' value='{$_SESSION['formdata']['db_table']}' />
				
				<label rel='db_prefix'>{$lang['FORM_DB_PREFIX']}</label>
				<input type='text' name='db_prefix' id='db_prefix' value='{$_SESSION['formdata']['db_prefix']}' />
				
			</div>
			<div class='righty_buttons'>
				<input class='submitbutton' type='submit' rel='installation_form' name='sub' value='{$lang['INSTALL']}' />
			</div>
			<div style='clear:both'>
		</form>
	";

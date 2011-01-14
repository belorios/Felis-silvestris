<?php
    
	$layout = "1col_std";
	
	if (isset($_POST['dummyData'])) {
		require_once(PATH_FUNC . "install.php");
	}
	else {
		$prefixText = (DB_PREFIX != "") ? ", all tables is going to be installed with the prefix ".DB_PREFIX : null;
		$body = "
			<h1>Installera</h1>
			<p>
				This is going to install the database for the application{$prefixText}. <br />
				<span style='color: #e62011; font-weight: bold;'>Warning</span> this is going to remove existing tables. 
			</p>
				
				<form action='' id='installForm' method='post'> 
				Do you want to fill the tables with dummy data? &nbsp;
					 <input type='radio' checked='checked' name='dummyData' value='1' /> Yes
					 <input type='radio' name='dummyData' value='0' /> No 
					 <p>
					 	<input type='submit' name='send' rel='installForm' value='Install' />
					 </p>
				</form>
		";
	}

<?php

	
	function regEditForm($formTitle, $username, $Fname, $Lname, $email, $emailConf, $gravatar, $action ) {
		
		$lang = $GLOBALS['lang'];
		
		$buttonText = ($action == "register") ? $lang['REGISTER'] : $lang[EDIT];
		require_once(PATH_LIB . "recaptcha/recaptchalib.php");
		
		return "
		
			<h1>$formTitle</h1>
			<form method='post' id='regForm' action='" . PATH_SITE . "/handleUserForm/$action'>
				<div >
					<div>
						<label>$lang[USERNAME] <span class='markeddot' title='Hejsan'>(*)</span></label>
						<input id='username' required='required' type='text' class='cssTextbox' name='username' value='$username' />
						
						<label>$lang[PASSWORD] <span class='markeddot' title='Hejsan'>(*)</span></label>
						<input id='password' required='required' type='password' class='cssTextbox' name='password' value='' />
					
						<label>$lang[CONFIRM] $lang[PASSWORD] <span class='markeddot' title='Hejsan'>(*)</span></label>
						<input id='password_conf' required='required' type='password' class='cssTextbox' name='password_conf' value='' />
					</div>	
					
					<div style='float: left; margin: 20px 0px'>
						<label>$lang[FNAME] <span class='markeddot' title='Hejsan'>(*)</span></label>		
						<input id ='fname' required='required' type='text'  name='fname' class='cssTextbox' value='$Fname' />
						
						<label>$lang[EMAIL] <span class='markeddot' title='Hejsan'>(*)</span></label>		
						<input id ='email' required='required' type='email'  name='email' class='cssTextbox' value='$email' />
						
						<label>$lang[GRAVATAR]</label>	
						<input id='gravatar' type='email' class='cssTextbox' name='gravatar' value='$gravatar' />
					</div>	
					<div style='float: left; margin: 20px 20px;'>	
						<label>$lang[LNAME] <span class='markeddot' title='Hejsan'>(*)</span></label>		
						<input id ='lname' required='required' type='text'  name='lname' class='cssTextbox' value='$Lname' />
						
						<label>$lang[CONFIRM] $lang[EMAIL] <span class='markeddot' title='Hejsan'>(*)</span></label>	
						<input id='email_conf' required='required' type='email' class='cssTextbox' name='email_conf' value='$emailConf' />
					</div>	
					<div style='clear: both;'></div>
					
					<div class='righty_buttons'>
						<input class='submitbutton' type='submit' value='$buttonText' name='submit' />
					</div>
				</div>
			</form>
		";
		
	}
#".recaptcha_get_html("")."
	/*
	function regEditForm($formTitle, $username, $Fname, $Lname, $email, $emailConf, $gravatar, $action ) {
		
		require_once(PATH_LIB . "recaptcha/recaptchalib.php");
		
		$lang = $GLOBALS['lang'];
		return "
		
			<h1>$formTitle</h1>
			<form method='post' id='regForm' action='handleUserForm/$action'>
				<div style='width: 350px'>
					<div>
						<label ref='username'>$lang[USERNAME] <span title='Hejsan'>(*)</span><label>
						<input id='username' required='required' type='text' class='cssTextbox' name='username' value='$username' />
						
						<label ref='password'>$lang[PASSWORD]<label>
						<input id='password' required='required' type='password' class='cssTextbox' name='password' value='' />
					
						<label ref='password_conf'>$lang[CONFIRM] $lang[PASSWORD]<label>
						<input id='password_conf' required='required' type='password' class='cssTextbox' name='password_conf' value='' />
					</div>	
					<div style='float: left; margin: 20px 0px'>
						<label ref='fname'>$lang[FNAME]<label>		
						<input id ='fname' required='required' type='text'  name='fname' class='cssTextbox' value='$Fname' />
						
						<label ref='email'>$lang[EMAIL]<label>		
						<input id ='email' required='required' type='email'  name='email' class='cssTextbox' value='$email' />
						
						<label ref='gravatar'>$lang[GRAVATAR]<label>	
						<input id='gravatar' required='required' type='email' class='cssTextbox' name='gravatar' value='$gravatar' />
					</div>	
					<div style='float: left; margin: 20px 20px;'>	
						<label ref='lname'>$lang[LNAME]<label>		
						<input id ='lname' required='required' type='text'  name='lname' class='cssTextbox' value='$Lname' />
						
						<label ref='email_conf'>$lang[CONFIRM] $lang[EMAIL]<label>	
						<input id='email_conf' required='required' type='email' class='cssTextbox' name='email_conf' value='$emailConf' />
						
						
					</div>	
					<div style='clear: both;'></div>
					
					<div class='righty_buttons'>
						<input type='submit' name='submit' rel='regForm' value='$lang[REGISTER]' />
					</div>
				</div>
			</form>
		";
		
	}
	 * 
	 */
#".recaptcha_get_html("")."
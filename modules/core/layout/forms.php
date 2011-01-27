<?php

	
	function regEditForm($formTitle, $username, $name, $email, $emailConf, $gravatar, $action, $id=false ) {
		
		$lang = $GLOBALS['lang'];
		$idString = ($id != false) ? "/id-$id" : null;
		if ($action == "register") {
			$oldPassCheck = null;
			$required 	  = "required='required'";
			$buttonText   = $lang['REGISTER'];
			$userDisable  = null;
			
			$Users = new Users();
			$recaptcha = $Users->getRecaptcha("html");
		}
		if ($action == "edit") {
			$oldPassCheck = "
				<p title='$lang[OLD_PASS_DESC]' style='margin-top: 20px'>
					<label>$lang[OLD_PASS] <span class='markeddot'>(*)</span></label>	
					<input id='oldPass' required='required' type='password' class='cssTextbox' name='oldPass' value='' />
				</p>
			"; 
			$required 	 = null;
			$buttonText  = $lang['EDIT'];
			$userDisable = "readonly='readonly'";
			$recaptcha   = null;
		}
		
		return "
			<h1>$formTitle</h1>
			<form method='post' id='regForm' action='" . PATH_SITE . "/handleUserForm/{$action}{$idString}'>
				<div>
					<div style='float: left; margin: 20px 0px';>
						<div title='$lang[USERNAME_DESC]'>
							<label>$lang[USERNAME] <span class='markeddot'>(*)</span></label>
							<input id='username' required='required' type='text' class='cssTextbox' $userDisable name='username' value='$username' />
						</div>
						<div title='$lang[PASSWORD_DESC]'>
							<label>$lang[PASSWORD] <span class='markeddot'>(*)</span></label>
							<input id='password' $required type='password' class='cssTextbox' name='password' value='' />
						</div>
					</div>	
					<div title='$lang[PASSWORD_DESC]' style='float: left; margin: 65px 0px 0px 20px; '>	
						<label>$lang[CONFIRM] $lang[PASSWORD] <span class='markeddot'>(*)</span></label>
						<input id='password_conf' $required type='password' class='cssTextbox' name='password_conf' value='' />
					</div>	
					<div style='clear: both;'></div>
					
					<div style='float: left;'>
						<div title='$lang[NAME_DESC]'>
							<label>$lang[NAME] <span class='markeddot'>(*)</span></label>		
							<input id='name' required='required' type='text'  name='name' class='cssTextbox' value='$name' />
						</div>
						<div title='$lang[EMAIL_DESC]'>	
							<label>$lang[EMAIL] <span class='markeddot'>(*)</span></label>		
							<input id='email' required='required' type='email'  name='email' class='cssTextbox' value='$email' />
						</div>
					</div>	
					<div style='float: left; margin: 0px 20px;'>	
						<div title='$lang[GRAVATAR_DESC]'>
							<label>$lang[GRAVATAR]</label>	
							<input id='gravatar' type='email' class='cssTextbox' name='gravatar' value='$gravatar' />
						</div>
						<div title='$lang[EMAIL_DESC]'>	
							<label>$lang[CONFIRM] $lang[EMAIL] <span class='markeddot'>(*)</span></label>	
							<input id='email_conf' required='required' type='email' class='cssTextbox' name='email_conf' value='$emailConf' />
						</div>
					</div>	
					<div style='clear: both;'></div>
					
					$oldPassCheck
					
					$recaptcha
					
					<div style='width: 370px; margin: 20px; text-align: right;'>
						<input class='submitbutton' type='submit' value='$buttonText' name='submit' />
					</div>
					
					
				</div>
			</form>
		";
		
	}
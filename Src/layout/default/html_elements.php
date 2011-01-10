<?php
   
	function sideBox($html, $align='right') {
		$float = ($align == 'left') ? "float:left;" : "float:right;";
		return "
			<div id='pageBody_Content_Sidebox' style='$float'>
				{$html}	
			</div>
		";
	}
	
	function html_Menu($items) {
		
		return "
			<ul>
				$items
			</ul>
		";
		
	}
	
	function html_Menu_Items($link, $name, $current=false) {
		$class = ($current != false) ? "class='CurrentPage'" : null;
		return "
			<li $class><a href='" . PATH_SITE . "/$link'>$name</a></li>
		";
		
	}
	
	function html_errorMessage($messages) {
		
		return "
			<div class='errorMessage'>
				<b>Following faults was found: </b>
				<p>
					{$messages}
				</p>
			</div>
		";
	}
	
	function html_Body($body, $float) {
		return "
			<div id='pageBody_Content_Big' class='content_float_$float'>
				$body
			</div>
		";
	}
	
	function sidebox_Login() {
		return sideboxLayout("Login", "
			<div id='LoginBox'>
				<form action='".PATH_SITE."/loginprocess' method='post'>
					<p>
						<input type='text' name='uname' value='Username' onclick='this.value=\"\"' />
					</p>
					<p>
						<input type='password' name='passwd' value='11111' onclick='this.value=\"\"'  />
					</p>
					<div class='righty_buttons' >
						<input type='submit' name='login' value='Login' />
						<a class='button' href='".PATH_SITE."/register'><span>Register</span></a> 
					</div>
					<div class='clear'></div>
				</form>
			</div>
		");
	}
	function sidebox_LoggedIn($username, $realname, $menu) {
		
		$menuItems = null;
		foreach ($menu as $item) {
			$menuItems .= "<li><a href='".PATH_SITE."/$item[url]'>$item[desc]</a></li>";
		}
		
		return sideboxLayout("Logged in as", "
			<span class='mark'>$realname</span><br />
			<ul id='loginMenu'>
				$menuItems
			</ul>
			
		");
	}
	
	function sideboxLayout($header, $body) {
		return "
			<div class='SideBox_Box'> 
				<div class='SideBox_Header'>
					<h2>{$header}</h2>
				</div>
				<div class='body'>
					$body
				</div>
				
			</div>
			
		";
	}
	
	function loginMenu($objects) {
		return "
			<div id='loginBox'>
				$objects
			</div>
			<div class='clear'></div>
		";		
	}

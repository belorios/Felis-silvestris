<?php
	class CHTMLPage {
		
		//Holds data for the layout
		protected 	$menuArr, 
					$charset, 
					$layout,
					$javascript  = array(),
					$javascriptf = false,
		 			$stylesheet  = array(),
		 			$layoutTypes = array();
		
		//Handles the site layout		
		protected 	$Footer, 
					$Heading = array(), 
					$Title,
					$Menu, 
					$Body, 
					$BodyExtra;
				
		
		function __construct($layout = false, $menuArr = false, $stylesheet = APP_STYLE) {
			
			//Handles stylesheet
			$this->addStyleSheet($stylesheet, PATH_CSS);
			$this->stylesheet = array_merge($this->stylesheet, unserialize(PATH_MODCSS));
			
			$this->charset = "UTF-8";
			
			//Sets layout
			$layout = ($layout != false) ? $layout : "2col_std";
			$this->setLayoutTypes();
			
			$this->setLayout($layout);
			
			//Adds js library Jquery
			$this->addJavascriptSrc("jquery/jquery.js");
			$this->addJavascriptSrc("std.js");
			
			//Sets default edtior
			$editor = "plain";
			if (USE_DB == true) {
				$config = new Configuration();
				$editor = $config->getSelectedEditor();
			}
			$this->setHtmlEditor($editor);
			
			//Creates the menu
			$this->menuArr = ($menuArr != false) ? $menuArr : unserialize(APP_MENU);
			
		}
		
		function __destruct() {
			;
		}
		
		//Creates an array holding the standard layouts
		private function setLayoutTypes() {
			$this->layoutTypes = array(
				"2col_std"  => "page_2col.css",
				"1col_std"  => "page_1col.css",
			);
		}
		
		//Sets a layout if it exists in the layouts array
		public function setLayout($layout) {
			if (array_key_exists($layout, $this->layoutTypes) === TRUE) {
				$this->layout = $this->layoutTypes[$layout];
			}
			else {
				throw new Exception("Wrong layout type set!");
			}
			
		}
		
		//Function for defining the page title and headers
		function defineHeaders($page, $header = APP_HEADER, $description = APP_DESCRIPTION) {
			$this->Title   				  = $header;
			$this->Heading['Header']      = $header;
			$this->Heading['Description'] = $description;
			
			//REDO! Totally crappy solution right now!
			$menuItems = null;
			$_SESSION['topPage'] = isset($_SESSION['topPage']) ? $_SESSION['topPage'] : null;
			foreach ($this->menuArr as $link => $name) {
				$_SESSION['currentPage'] = (isset($_SESSION['currentPage'])) ? $_SESSION['currentPage'] : null;
				$_SESSION['topPage'] = ($_SESSION['currentPage'] == $link) ? $_SESSION['currentPage'] : $_SESSION['topPage'];
			}
			foreach ($this->menuArr as $link => $name) {
				$current = ($link == $_SESSION['topPage']) ? true : false;
				$menuItems .= html_Menu_Items($link, $name, $current);
			}
			$this->Menu = html_Menu($menuItems);
		}
		
		//Defines the pagebody with content and sideboxes and also writes out errorMsgs
		function definePageBody($aBody, $sideBox=false, $sideBoxFloat='right') {
			
			$errorMsg = $this->getErrorMessage();
			$pageBodyRight = null;
			$pageBodyLeft  = null;
			
			if (substr(array_search($this->layout, $this->layoutTypes), 0, 4) == "2col") {
				$loginMenu = $this->getLoginSidebox();
				$this->BodyExtra = null;
			}
			else {
				$loginMenu = null;
				$this->BodyExtra = $this->getLoginMenu();
			}
			
			if ($sideBox != false) {
				$sideBox = sideBox($loginMenu . $sideBox, $sideBoxFloat);
			}
			else {
				$sideBox = sideBox($loginMenu, $sideBoxFloat);
			}
			if (substr(array_search($this->layout, $this->layoutTypes), 0, 4) == "1col") 
				$sideBox = null;
			
			$aBody = html_Body($errorMsg . $aBody, $sideBoxFloat);
			
			$this->Body = "
				$sideBox
				$aBody
			";
		}
		
		//Defines the pagefooter
		function definePageFooter($footer = APP_FOOTER, $validation = APP_VALIDATION) {
			$this->Footer = array();
			$this->Footer['left']  = $footer;
			$this->Footer['right'] = $validation;
		}
		
		//Prints the page to screen
		function printPage() {
			
			$stylesheets = null;
			
			if (!is_null($this->layout)) {
				$stylesheets .= "<link rel='stylesheet'  href='".PATH_CSS."{$this->layout}' type='text/css' media='screen' />";
			}
			
			foreach ($this->stylesheet as $key => $style) {
				$stylesheets .= $style;
			}
			
			$JavaScript = null;
			foreach ($this->javascript as $js) {
				$JavaScript .= "<script type='text/javascript' src='$js'></script>";
			}
			
			$JavaScript .= "<script type='text/javascript'>{$this->javascriptf}</script>";
			
			return html_layout($this->Title, $this->Heading, $this->Menu, $this->Body, $this->Footer, $Charset='UTF-8', $this->BodyExtra, $stylesheets, $JavaScript, $MetaTags=false);
		}
		
		
		protected function setLoggedInMenu() {
			
			$Users = new Users();
			$menu = array();
			
			$userMenu = unserialize(APP_USERMENU);
			foreach ($userMenu as $key => $item) {
				if (!array_key_exists($key, $menu)) {
					$menu[$key] = $item;
				}	
			}
			
			//Checks if the modulemenu exists and then adds all record that is not doublets 
			if (defined('MODULE_USERMENU')) {
				$modMenu = unserialize(MODULE_USERMENU);
			
				foreach($modMenu as $key => $item) {
					if (!array_key_exists($key, $menu)) {
						$menu[$key] = $item;
					}	
				}
			}
			
			if ($Users->ctlGroup("adm") == true) {
				$menu['manager'] = array("url" => "/manager", "desc" => "Manage"); 
			}
			$menu['logout'] = array("url" => "logout", "desc" => "Log out"); 
			
			return $menu;			
		}
		
		private function getLoginSideBox() {
			
			if(isset($_SESSION['userId'])) {
				return sidebox_LoggedIn($_SESSION['username'], $_SESSION['realname'], $this->setLoggedInMenu());
			}
			else {
				return sidebox_Login();
			}
			
		}
		
		private function getLoginMenu() {
			
			 if(isset($_SESSION['userId'])) {
				$menu = "Logged in as $_SESSION[username]  ";
				foreach ($this->setLoggedInMenu() as $item) {
					$url = (substr($item['url'], 0, 1) == "/") ? PATH_SITE_LOC . substr($item['url'], 1) : PATH_SITE. "/$item[url]";
					$menu .= " [ <a href='$url'>$item[desc]</a> ]";
				}
			 }
			 else {
			 	$menu = "
					<a href='".PATH_SITE."/login'>Log in</a>
				";
			 }
			
	        $html = "
				<div class='login'>
					$menu
				</div>
			";

       		return loginMenu($html);  
		}
		
		protected function getErrorMessage() {
			
			$return = "";
			
	        if(isset($_SESSION['errorMessage'])) {
	        	
				$message = "";
	            if (is_array($_SESSION['errorMessage'])) {
	            	foreach ($_SESSION['errorMessage'] as $error) {
	            		$message .= "$error <br />";
	            	}
	            }
				else {
					$message = $_SESSION['errorMessage'];
				}
				
				$return = html_errorMessage($message);
	            unset($_SESSION['errorMessage']);
	        }
	
	        return $return;   
	
	    }
		
		public function addStyleSheet($style, $path, $media="screen", $string=false) {
			if ($string != false && !is_null($string)) {
				$this->stylesheet[$style] = $string;
			}
			else {
				$this->stylesheet[$style] = "<link rel='stylesheet'  href='{$path}{$style}' type='text/css' media='$media' /> ";
			}
			
		}
		
		public function getStyleSheets() {
			return $this->stylesheet;
		}
		
		public function addJavascriptFunc($js) {
			if ($this->javascriptf != false) {
				$this->javascriptf .= "\n " . $js;
			}
			else {
				$this->javascriptf = $js;
			}	
		}
		
		public function addJavascriptSrc($jsPath, $src=PATH_JS) {
			$this->javascript[] = $src . $jsPath;
		}
		
		public function setHtmlEditor($editor) {
			
			switch($editor) {
				case "wymeditor": 
					$this->addJavascriptSrc("wymeditor/jquery.wymeditor.pack.js");
					$this->addJavascriptFunc("jQuery(function() { jQuery(\".editor\").wymeditor(); }); jQuery(function() { jQuery(\".simpleeditor\").wymeditor(); });");
				break;
				case "markitup":
					$this->addJavascriptSrc("markitup/jquery.markitup.js");
					$this->addJavascriptSrc("markitup/sets/default/set.js");
					
					$this->addStyleSheet("markitup/skins/markitup/style.css", PATH_JS);
					$this->addStyleSheet("markitup/sets/default/style.css", PATH_JS);	
					
					$this->addJavascriptFunc('
						$(document).ready(function() {$(".editor").markItUp(mySettings); });
						$(document).ready(function() {$(".simpleeditor").markItUp(mySettings); });
					');
					
				break;
				case "nicedit":
					$this->addJavascriptSrc("nicEdit.js");
					$this->addJavascriptFunc("bkLib.onDomLoaded(nicEditors.allTextAreas);");
				break;
				case "tinymce":
					$this->addJavascriptSrc("tiny_mce/tiny_mce.js");
					$this->addJavascriptSrc("tiny_mce/toolsets.js");
				break;
				case "plain":
				break;
				
			}
			
		}
		
	}

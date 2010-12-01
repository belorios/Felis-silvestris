<?php
	class CHTMLPage {
		
		//Hanterar data till sidlayouten
		protected 	$menuArr, 
					$charset, 
					$layout,
					$javascript  = array(),
		 			$stylesheet  = array(),
		 			$layoutTypes = array();
		
		//Hanterar sidlayout		
		private $Footer, 
				$Heading = array(), 
				$Title,
				$Menu, 
				$Body, 
				$BodyExtra;
				
		
		function __construct($layout = false, $menuArr = false, $stylesheet = false) {
			$this->menuArr = ($menuArr != false) ? $menuArr : unserialize(APP_MENU);
			$this->stylesheet[APP_STYLE] = ($stylesheet != false) ? $stylesheet : "<link rel='stylesheet'  href='".APP_STYLE."' type='text/css' media='screen' /> ";
			$this->stylesheet = array_merge($this->stylesheet, unserialize(PATH_MODCSS));
			
			$this->charset = "UTF-8";
			
			$layout = ($layout != false) ? $layout : "2col_std";
			$this->setLayoutTypes();
			if (!$this->setLayout($layout)) {
				throw new Exception("Wrong layout type set!");
			}
			
		}
		
		function __destruct() {
			;
		}
		
		private function setLayoutTypes() {
			$this->layoutTypes = array(
				"2col_std"  => "page_2col.css",
				"1col_std"  => "page_1col.css",
				"2col_xtra" => "xtravisible_2col.css",
				"1col_xtra" => "xtravisible_1col.css",
			);
		}
		
		public function setLayout($layout) {
			
			if (array_key_exists($layout, $this->layoutTypes)) {
				$this->layout = $this->layoutTypes[$layout];
				return true;
			}
			else {
				return false;	
			}
			
		}
		
		function defineHTMLHeader($aTitle = APP_HEADER) {
			$this->Title   = $aTitle;
		}
		
		function definePageHeader($header = APP_HEADER, $description = APP_DESCRIPTION) {
			$menuItems = null;
			
			$curPage = (isset($_GET['p'])) ? $_GET['p'] : ".";
			
			foreach ($this->menuArr as $link => $name) {
				$current = ($link == $curPage) ? true : false;
				$menuItems .= html_Menu_Items($link, $name, $current);
			}
			
			$this->Heading['Header']      = $header;
			$this->Heading['Description'] = $description;
			
			$this->Menu = html_Menu($menuItems);
			
		}
	
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
		
		function definePageFooter($footer = APP_FOOTER, $validation = APP_VALIDATION) {
			$this->Footer = array();
			$this->Footer['left']  = $footer;
			$this->Footer['right'] = $validation;
		}
		
		function printPage() {
			
			$stylesheets = null;
			
			if (!is_null($this->layout)) {
				$stylesheets .= "<link rel='stylesheet'  href='".PATH_CSS."{$this->layout}' type='text/css' media='screen' />";
			}
			
			foreach ($this->stylesheet as $style) {
				$stylesheets .= $style;
			}
			//markitup
			#$stylesheets .= "<link rel='stylesheet' type='text/css' href='" . PATH_JS . "markitup/skins/markitup/style.css' />";
			#$stylesheets .= "<link rel='stylesheet' type='text/css' href='" . PATH_JS . "markitup/sets/default/style.css' />";
			
			//Jquery
			$this->javascript[] = PATH_JS . "jquery/jquery.js";
			
			//nicEdit
			#$this->javascript[] = PATH_JS . "nicEdit.js";
			
			//TinyMCE
			$this->javascript[] = PATH_JS . "/tiny_mce/tiny_mce.js";

			//Wymeditor
			#$this->javascript[] = PATH_JS . "wymeditor/jquery.wymeditor.pack.js";
			
			//markitup
			#$this->javascript[] = PATH_JS . "markitup/jquery.markitup.js";
			#$this->javascript[] = PATH_JS . "markitup/sets/default/set.js";
			
			$JavaScript = null;
			foreach ($this->javascript as $js) {
				$JavaScript .= "<script type='text/javascript' src='$js'></script>";
			}
			
			//nicEdit
			#$JavaScript .= "<script type='text/javascript'>bkLib.onDomLoaded(nicEditors.allTextAreas);</script>";
			
			//Wymeditor
			#$JavaScript .= " <script type=\"text/javascript\"> jQuery(function() { jQuery(\".editor\").wymeditor(); }); </script> ";
			
			//markitup
			#$JavaScript .= ' <script type="text/javascript"> $(document).ready(function() {$(".editor").markItUp(mySettings); }); </script> ';
			$JavaScript .= '
				<script type="text/javascript">
					tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : "save",
						
						theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull",
						theme_advanced_buttons2 : "",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "center",
						theme_advanced_statusbar_location : "bottom",
						theme_advanced_resizing : true,
						editor_selector : "simpleeditor",
					});
					
					tinyMCE.init({
						mode : "textareas",
						theme : "advanced",
						plugins : "safari,spellchecker,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
						// Theme options
						theme_advanced_buttons1 : "save,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,forecolor,backcolor",
						theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,preview,|,hr,sub,sup,|,charmap,emotions,media,fullscreen",
						theme_advanced_buttons3 : "",
						theme_advanced_toolbar_location : "top",
						theme_advanced_toolbar_align : "left",
						theme_advanced_statusbar_location : "bottom",
						theme_advanced_resizing : true,
						editor_selector : "editor",
					});
				</script>
			';	
			return html_layout($this->Title, $this->Heading, $this->Menu, $this->Body, $this->Footer, $Charset='UTF-8', $this->BodyExtra, $stylesheets, $JavaScript, $MetaTags=false);
		}
		
		private function setLoggedInMenu() {
			
			$Users = new Users();
			$menu = array();
			
			$menu['createArticle'] = array("url" => "createArticle", "desc" => "Write new article");
			
			//Checks if the modulemenu exists and then adds all record that is not doublets 
			if (defined('MODULE_USERMENU')) {
				$modMenu = unserialize(MODULE_USERMENU);
			
				foreach($modMenu as $key => $item) {
					if (!array_key_exists($key, $menu)) {
						$menu[$key] = $item;
					}	
				}
			}
			
			
			$menu['logout'] 		 = array("url" => "logout", "desc" => "Log out"); 
			
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
				$menu = "Inloggad som $_SESSION[username]  ";
				foreach ($this->setLoggedInMenu() as $item) {
					$menu .= " [ <a href='".PATH_SITE."/$item[url]'>$item[desc]</a> ]";
				}
				
			 }
			 else {
			 	$menu = "
					<a href='".PATH_SITE."/login'>Logga in</a>
				";
			 }
			
	        $html = "
				<div class='login'>
					$menu
				</div>
			";

       		return loginMenu($html);  
		}
		
		private function getErrorMessage() {
			
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
		
		public function addStyleSheet($style, $media="screen", $string=false) {
			if ($string != false) {
				$this->stylesheet[$style] = $string;
			}
			else {
				$this->stylesheet[$style] = "<link rel='stylesheet'  href='$style' type='text/css' media='$media' /> ";
			}
			
		}
		
		public function getStyleSheets() {
			return $this->stylesheet;
		}
		
	}

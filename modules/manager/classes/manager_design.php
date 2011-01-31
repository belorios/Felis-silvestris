<?php

	class manager_design extends CHTMLPage {
		
		protected $managerTopMenu;
		
		public function __construct($layout = false, $menuArr = false, $stylesheet = APP_STYLE) {
			parent::__construct($layout, $menuArr, $stylesheet);
			$this->buildManagerTopMenu();
		}
		
		public function buildManagerTopMenu() {
				
			if(isset($_SESSION['userId'])) {
				$menu = "Logged in as $_SESSION[username], <a href='".PARENT_SITE."/logout'>Logout</a>";
			}
			else {
				$menu = "";
			}	
			
			$this->managerTopMenu = $menu;
			
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
			
			return html_layout($this->Title, $this->Heading, $this->managerTopMenu, $this->Body, $this->Footer, $Charset='UTF-8', $this->BodyExtra, $stylesheets, $JavaScript, $MetaTags=false);
		}
		
		//Defines the pagebody with content and sideboxes and also writes out errorMsgs
		function definePageBody($aBody, $sideBox=false, $sideBoxFloat='right') {
			
			$errorMsg = $this->getErrorMessage();
			$pageBodyRight = null;
			$pageBodyLeft  = null;
			
			if (substr(array_search($this->layout, $this->layoutTypes), 0, 4) == "2col") {
				$this->BodyExtra = null;
			}
			else {
			}
			
			if (substr(array_search($this->layout, $this->layoutTypes), 0, 4) == "2col") {
				
				$sideboxes = unserialize(SITE_SIDEBOXES);
				
				foreach ($sideboxes as $box) {
					$sideBox .= require_once($box);
				}
				
				if ($sideBox != false) {
					$sideBox = sideBox($sideBox, $sideBoxFloat);
				}
				else {
					$sideBox = "";
				}
			}
			
			$aBody = html_Body($errorMsg . $aBody, $sideBoxFloat);
			
			$this->Body = "
				$sideBox
				$aBody
			";
		}
		
	}

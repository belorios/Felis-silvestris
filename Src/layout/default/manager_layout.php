<?php
    
	function html_layout($Title, $Heading, $Menu, $Body, $Footer, $Charset='UTF-8', $login=false, $StyleSheets=false, $JavaScript=false, $MetaTags=false) {
		
		return "
			
			<!DOCTYPE HTML>
			<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' lang='en' >
				<head>
					<meta http-equiv='Content-Type' content='text/html; charset=$Charset'>
					<title>$Title</title>
					$StyleSheets
					<!--[if IE 7]>
						<link rel='stylesheet' type='text/css' href='".PATH_CSS."ie.css' />
					<![endif]-->
					
					$JavaScript

				</head>
				<body>
					<div id='container'>
						<div id='top_ribbon'>
							<div id='top_ribbon_text'>
								<a href='".PATH_SITE."'><h1>$Heading[Header]</h1></a>
								<nav id='menu' >$Menu</nav>
							</div>
						</div>
						<div id='content_contanier'>
							$Body
						</div>
					</div>
					
				</body>
			</html>
				
		";
		
		$old = "
	<div id='BackgroundContainer'>	
						<div id='BackgroundContainer_Klet'>
							<div id='Container'>
								<div id='LogoBox'>
									<div id='logo_heading'>
										$Heading[Header]
									</div>
									<div id='logo_decoration'></div>
									<div id='logo_desc'>$Heading[Description] <span style='font-size: 18pt'>\"</span></div>
									<div class='clear'></div>
									<nav id='MenuHolder'>
										$Menu
									</nav>
								</div>
								$login
								<div id='pageBody'>
									<div id='pageBody_Top'>
										
									</div>
									<div id='pageBody_Content'>
										$Body
										<div class='clear'></div>
									</div>
									<div id='pageBody_Footer'>
										<div style='float:right;'>
											<a href='".PATH_SITE."/changestyle/purple'><img src='".PATH_SITE_LAYOUT."images/buttons/xtra.gif' title='Extra stor storlek på text'  /></a>
											<a href='".PATH_SITE."/changestyle/onecol'><img src='".PATH_SITE_LAYOUT."images/buttons/twocol.gif' title='Två kolumns layout'  /></a>
											<a href='".PATH_SITE."/changestyle/twocol'><img src='".PATH_SITE_LAYOUT."images/buttons/onecol.gif' title='En kolumns layout'  /></a>
										</div>
										<div class='clear'></div>
									</div>
								</div>
								<div id='FooterInfo'>
									<div id='FooterInfo_Left'>
										{$Footer['left']}
									</div>
									<div id='FooterInfo_Right'>
										{$Footer['right']}
									</div>
								</div>
							</div>
						</div>
						</div>
	
	";
		
	}
	
	
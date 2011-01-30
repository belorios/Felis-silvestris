<?php
	
	try {
		$AllTags  = $Posts->getAllTags();
	}
	catch ( exception $e) {
		$_SESSION['errorMessage'][] = $e->getMessage();
		return;
	}
	
	$tags = null;
	foreach ($AllTags as $tag) {
		$tags .= tagLayout($tag['id'], "$tag[tagname]($tag[antal])", $tag['antal']);
	}

	return sideboxLayout(
		$lang['TAGS'], "
		$tags
	");
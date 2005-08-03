<?php
	global $gBitSystem;
	$gBitSystem->registerPackage( 'categories', dirname( __FILE__).'/' );

	if($gBitSystem->isPackageActive( 'categories' ) ) {
		// creates the main categories object
		require_once( CATEGORIES_PKG_PATH.'categ_lib.php' );
		
		if($gBitUser->hasPermission( 'bit_p_view_categories' ) ) {
			$gBitSystem->registerAppMenu( 'categories', 'Categories', CATEGORIES_PKG_URL.'index.php', 'bitpackage:categories/menu_categories.tpl', 'categories');
		}
	}
?>

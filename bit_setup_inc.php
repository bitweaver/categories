<?php
	global $gBitSystem;
	$gBitSystem->registerPackage( 'categories', dirname( __FILE__).'/' );

	if($gBitSystem->isPackageActive( 'categories' ) ) {
		$gLibertySystem->registerService( LIBERTY_SERVICE_CATEGORIZATION, CATEGORIES_PKG_NAME, array(
			'content_display_function' => 'categories_display',
			'content_edit_function' => 'categories_object_edit',
			'content_expunge_function' => 'categories_object_expunge',
			'content_preview_function' => 'categories_object_edit',
			'content_store_function' => 'categories_categorize',
			'content_edit_tpl' => 'bitpackage:categories/categorize.tpl',
			'content_view_tpl' => 'bitpackage:categories/categories_objects.tpl',
			'content_nav_tpl' => 'bitpackage:categories/categories_nav.tpl',
		) );
		// creates the main categories object
		require_once( CATEGORIES_PKG_PATH.'categ_lib.php' );

		if($gBitUser->hasPermission( 'bit_p_view_categories' ) ) {
			$gBitSystem->registerAppMenu( 'categories', 'Categories', CATEGORIES_PKG_URL.'index.php', 'bitpackage:categories/menu_categories.tpl', 'categories');
		}
	}
?>

<?php

global $categlib;

if( $gBitSystem->isPackageActive( 'categories' ) ) {
	// Check to see if page is categorized
	$cats = $categlib->get_object_categories( $cat_obj_type, $cat_objid );
	// Display category path or not (like {catpath()})
	if ( $cats ) {
		if( $gBitSystem->isFeatureActive( 'feature_categorypath' ) ) {
			$display_catpath = $categlib->get_category_path($cats);
			$smarty->assign('display_catpath',$display_catpath);
		}
		// Display current category objects or not (like {category()})
		if( $gBitSystem->isFeatureActive( 'feature_categoryobjects' ) ) {
			$display_catobjects = $categlib->get_categoryobjects( $cats );
			$smarty->assign( 'display_catobjects',$display_catobjects );
		}
	}
}


?>
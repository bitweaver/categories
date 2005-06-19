<?php

// $Header: /cvsroot/bitweaver/_bit_categories/Attic/categorize_inc.php,v 1.1 2005/06/19 03:58:14 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
include_once( CATEGORIES_PKG_PATH.'categ_lib.php');

global $categlib, $cat_obj_type, $cat_objid, $cat_content_id;

if ($gBitSystem->isPackageActive( 'categories' )) {
	$smarty->assign('cat_categorize', 'n');

	$categlib->uncategorize($cat_obj_type, $cat_objid, $cat_content_id);
	if (!empty($_REQUEST["cat_categories"])) {
		foreach ($_REQUEST["cat_categories"] as $cat_acat) {
			if ($cat_acat) {
				$cat_object_id = $categlib->is_categorized($cat_obj_type, $cat_objid, $cat_content_id);

				if (!$cat_object_id) {
					// The object is not cateorized
					$cat_object_id = $categlib->add_categorized_object($cat_obj_type, $cat_objid, $cat_desc, $cat_name, $cat_href);
				}

				$categlib->categorize($cat_object_id, $cat_acat);
			}
		}
	}

	$categories = $categlib->list_all_categories(0, -1, 'name_asc', '', $cat_obj_type, $cat_objid);
	$smarty->assign_by_ref('categories', $categories["data"]);
}

?>

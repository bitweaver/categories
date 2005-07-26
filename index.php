<?php

// $Header: /cvsroot/bitweaver/_bit_categories/index.php,v 1.1.1.1.2.1 2005/07/26 15:50:03 drewslater Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//
// $Header: /cvsroot/bitweaver/_bit_categories/index.php,v 1.1.1.1.2.1 2005/07/26 15:50:03 drewslater Exp $
//

// Initialization
require_once( '../bit_setup_inc.php' );

include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
include_once( CATEGORIES_PKG_PATH.'CatBrowseTreeMaker.php' );

if ($package_categories != 'y') {
	$gBitSmarty->assign('msg', tra("This feature is disabled").": package_categories");

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!$gBitUser->hasPermission( 'bit_p_view_categories' )) {
	$gBitSmarty->assign('msg', tra("You dont have permission to use this feature"));
	$gBitSystem->display( 'error.tpl' );
	die;
}

// Check for parent category or set to 0 if not present
if (!isset($_REQUEST["parent_id"])) {
	$_REQUEST["parent_id"] = 0;
}

$gBitSmarty->assign('parent_id', $_REQUEST["parent_id"]);

// If the parent category is not zero get the category path
if ($_REQUEST["parent_id"]) {
	$path = $categlib->get_category_path_browse($_REQUEST["parent_id"]);

	$p_info = $categlib->get_category($_REQUEST["parent_id"]);
	$father = $p_info["parent_id"];
} else {
	$path = tra("TOP");

	$father = 0;
}

$gBitSmarty->assign('path', $path);
$gBitSmarty->assign('father', $father);

//$children = $categlib->get_child_categories($_REQUEST["parent_id"]);
//$gBitSmarty->assign_by_ref('children',$children);

// Convert $childrens
//$debugger->var_dump('$children');
$ctall = $categlib->get_all_categories();
$tree_nodes = array();

foreach ($ctall as $c) {
	$tree_nodes[] = array(
		"id" => $c["category_id"],
		"parent" => $c["parent_id"],
		"data" => '<a class="catname" href="'.CATEGORIES_PKG_URL.'index.php?parent_id=' . $c["category_id"] . '">' . $c["name"] . '</a><br />'
	);
}

//$debugger->var_dump('$tree_nodes');
$tm = new CatBrowseTreeMaker("categ");
$res = $tm->make_tree($_REQUEST["parent_id"], $tree_nodes);
$gBitSmarty->assign('tree', $res);

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
if (isset($_REQUEST['page'])) {
	$page = &$_REQUEST['page'];
	$offset = ($page - 1) * $maxRecords;
}
$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$pagination_url = $gBitSystem->pagination_url($find, $sort_mode, 'parentId', $_REQUEST["parentId"] = 0);
$gBitSmarty->assign_by_ref('pagination_url', $pagination_url);

if (isset($_REQUEST["deep"]) && $_REQUEST["deep"] == 'on') {
	$objects = $categlib->list_category_objects_deep($_REQUEST["parent_id"], $offset, $maxRecords, $sort_mode, $find);
	$gBitSmarty->assign('deep', 'on');
} else {
	$objects = $categlib->list_category_objects($_REQUEST["parent_id"], $offset, $maxRecords, $sort_mode, $find);
	$gBitSmarty->assign('deep', 'off');
}

$gBitSmarty->assign_by_ref('objects', $objects["data"]);
$gBitSmarty->assign_by_ref('cantobjects', $objects["cant"]);

$cant_pages = ceil($objects["cant2"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($objects["cant2"] > ($offset + $maxRecords)) {
	$gBitSmarty->assign('next_offset', $offset + $maxRecords);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

$section = 'categories';


// Display the template
$gBitSystem->display( 'bitpackage:categories/browse_categories.tpl');

?>

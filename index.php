<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.

//
// $Header$
//

// Initialization
require_once( '../kernel/setup_inc.php' );

$gBitSystem->verifyPackage( 'categories' );
$gBitSystem->verifyPermission( 'p_categories_view' );

include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
include_once( CATEGORIES_PKG_PATH.'CatBrowseTreeMaker.php' );

// Check for parent category or set to 0 if not present
if (!isset($_REQUEST["parent_id"])) {
	$_REQUEST["parent_id"] = 1;
}

$gBitSmarty->assign('parent_id', $_REQUEST["parent_id"]);

// If the parent category is not zero get the category path
if ($_REQUEST["parent_id"]) {
	$path = $categlib->get_category_path_browse($_REQUEST["parent_id"]);

	$p_info = $categlib->get_category($_REQUEST["parent_id"]);
	$father = $p_info["parent_id"];
}

$gBitSmarty->assign('path', $path);
$gBitSmarty->assign('father', $father);

$children = $categlib->get_child_categories($_REQUEST["parent_id"]);
$gBitSmarty->assign_by_ref('children',$children);

// Convert $childrens
//vd($children);
$ctall = $categlib->get_all_categories();
$tree_nodes = array();

foreach ($ctall as $c) {
	foreach ($children as $ch) {
		if ($ch['category_id'] == $c['category_id']) {
			$c = array_merge($c, $ch);
			break;
		}
	}
	//vd($c);
	$tree_nodes[] = array(
		"id" => $c["category_id"],
		"parent" => $c["parent_id"],
		"children" => ((empty($c["children"])) ? (0) : ($c["children"])),
		"objects" => ((empty($c["objects"])) ? (0) : ($c["objects"])),
		"data" => '<a class="catname" href="'.CATEGORIES_PKG_URL.'index.php?parent_id=' . $c["category_id"] . '">' . $c["name"] . '</a>&nbsp;'
	);
}

//vd($tree_nodes);
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
	$offset = ($page - 1) * $max_records;
}
$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);
$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
//$pagination_url = $gBitSystem->pagination_url($find, $sort_mode, 'parent_id', $_REQUEST["parent_id"]);
//$gBitSmarty->assign_by_ref('pagination_url', $pagination_url);

if (isset($_REQUEST["deep"]) && $_REQUEST["deep"] == 'on') {
	$objects = $categlib->list_category_objects_deep($_REQUEST["parent_id"], $offset, $max_records, $sort_mode, $find);
	$gBitSmarty->assign('deep', 'on');
} else {
	$objects = $categlib->list_category_objects($_REQUEST["parent_id"], $offset, $max_records, $sort_mode, $find);
	$gBitSmarty->assign('deep', 'off');
}

$gBitSmarty->assign_by_ref('objects', $objects["data"]);
$gBitSmarty->assign_by_ref('cantobjects', $objects["cant"]);

$cant_pages = ceil($objects["cant2"] / $max_records);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $max_records));

if ($objects["cant2"] > ($offset + $max_records)) {
	$gBitSmarty->assign('next_offset', $offset + $max_records);
} else {
	$gBitSmarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$gBitSmarty->assign('prev_offset', $offset - $max_records);
} else {
	$gBitSmarty->assign('prev_offset', -1);
}

// Display the template
$gBitSystem->display( 'bitpackage:categories/browse_categories.tpl', NULL, array( 'display_mode' => 'display' ));

?>

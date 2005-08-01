<?php

// $Header: /cvsroot/bitweaver/_bit_categories/admin/index.php,v 1.4 2005/08/01 18:40:06 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//
// $Header: /cvsroot/bitweaver/_bit_categories/admin/index.php,v 1.4 2005/08/01 18:40:06 squareing Exp $
//

// Initialization
require_once( '../../bit_setup_inc.php' );

if( defined( 'FILEGALS_PKG_PATH' ) ) {
	include_once( FILEGALS_PKG_PATH.'filegal_lib.php' );
}
if( defined( 'POLLS_PKG_PATH' ) ) {
	include_once( POLLS_PKG_PATH.'poll_lib.php' );
	if (!isset($polllib)) {
		$polllib = new PollLib();
	}
}
if( defined( 'CATEGORIES_PKG_PATH' ) ) {
	include_once( CATEGORIES_PKG_PATH.'categ_lib.php');
	include_once( CATEGORIES_PKG_PATH.'CatAdminTreeMaker.php' );
}
if( defined( 'ARTICLES_PKG_PATH' ) ) {
	include_once( ARTICLES_PKG_PATH.'art_lib.php' );
}
if( defined( 'BLOGS_PKG_PATH' ) ) {
	include_once( BLOGS_PKG_PATH.'BitBlog.php' );
}
if( defined( 'WIKI_PKG_PATH' ) ) {
	include_once( WIKI_PKG_PATH.'BitPage.php' );
}
if( defined( 'DIRECTORY_PKG_PATH' ) ) {
	include_once( DIRECTORY_PKG_PATH.'dir_lib.php' );
}
if( defined( 'TRACKERS_PKG_PATH' ) ) {
	include_once( TRACKERS_PKG_PATH.'tracker_lib.php' );
}

if( !$gBitSystem->isPackageActive( 'categories' ) ) {
	$gBitSystem->fatalError( tra("This feature is disabled").": package_categories" );
}

if (!$gBitUser->hasPermission( 'bit_p_admin_categories' )) {
	$gBitSystem->fatalError( tra("You dont have permission to use this feature") );
}

// Check for parent category or set to 0 if not present
if (!isset($_REQUEST["parent_id"])) {
	$_REQUEST["parent_id"] = 0;
}

$gBitSmarty->assign('parent_id', $_REQUEST["parent_id"]);

if (isset($_REQUEST["addpage"])) {

	// Here we categorize multiple pages at once
	foreach ($_REQUEST['class_content'] as $contentId ) {
		$categlib->categorize_page( $contentId, $_REQUEST["parent_id"] );
	}
}

if (isset($_REQUEST["addpoll"])) {

	// Here we categorize a poll
	$categlib->categorize_poll($_REQUEST["poll_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addfaq"])) {

	// Here we categorize a faq
	$categlib->categorize_faq($_REQUEST["faq_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addtracker"])) {

	// Here we categorize a tracker
	$categlib->categorize_tracker($_REQUEST["tracker_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addquiz"])) {

	// Here we categorize a quiz
	$categlib->categorize_quiz($_REQUEST["quiz_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addforum"])) {

	// Here we categorize a forum
	$categlib->categorize_forum($_REQUEST["forum_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addgallery"])) {

	// Here we categorize an image gallery
	$categlib->categorize_gallery($_REQUEST["gallery_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addfilegallery"])) {

	// Here we categorize a file gallery
	$categlib->categorize_file_gallery($_REQUEST["file_gallery_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addarticle"])) {

	// Here we categorize an article
	$categlib->categorize_article($_REQUEST["article_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["addblog"])) {

	// Here we categorize a blog
	$categlib->categorize_blog($_REQUEST["blog_id"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["adddirectory"])) {

	// Here we categorize a directory category
	$categlib->categorize_directory($_REQUEST["directoryId"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["category_id"])) {
	$info = $categlib->get_category($_REQUEST["category_id"]);
} else {
	$_REQUEST["category_id"] = 0;

	$info["name"] = '';
	$info["description"] = '';
}

if (isset($_REQUEST["removeObject"])) {

	$categlib->remove_object_from_category($_REQUEST["removeObject"], $_REQUEST["parent_id"]);
}

if (isset($_REQUEST["removeCat"])) {

	$categlib->remove_category($_REQUEST["removeCat"]);
}

if (isset($_REQUEST["save"]) && isset($_REQUEST["name"]) && strlen($_REQUEST["name"]) > 0) {

	// Save
	if ($_REQUEST["category_id"]) {
		$categlib->update_category($_REQUEST["category_id"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["parent_id"]);
	} else {
		$categlib->add_category($_REQUEST["parent_id"], $_REQUEST["name"], $_REQUEST["description"]);
	}

	$info["name"] = '';
	$info["description"] = '';
	$_REQUEST["category_id"] = 0;
}

$gBitSmarty->assign('category_id', $_REQUEST["category_id"]);
$gBitSmarty->assign('name', $info["name"]);
$gBitSmarty->assign('description', $info["description"]);

// If the parent category is not zero get the category path
if( empty( $_REQUEST["parent_id"] ) ) {
	$_REQUEST["parent_id"] = 0;
}
$catInfo = $categlib->get_category($_REQUEST["parent_id"]);
$catInfo['path'] = $categlib->get_category_path_admin($_REQUEST["parent_id"]);

$gBitSmarty->assign('catInfo', $catInfo);

// Convert $childrens
//$debugger->var_dump('$children');
$ctall = $categlib->get_all_categories_ext();
$tree_nodes = array();

// XINGICON need to remove these non biticon icon paths!
foreach ($ctall as $c) {
	$tree_nodes[] = array(
		"id" => $c["category_id"],
		"parent" => $c["parent_id"],
		"data" => '<a class="catname" href="'.CATEGORIES_PKG_URL.'admin/index.php?parent_id=' . $c["category_id"] . '" title="' . tra( 'Objects in category'). ':' . $c["objects"] . '">' . $c["name"] . '</a>',
//		"data" => '<a class="catname" href="'.CATEGORIES_PKG_URL.'admin/index.php?parent_id=' . $c["category_id"] . '" title="' . tra( 'Child categories'). ':' . $c["children"] . ' ' . tra( 'Objects in category'). ':' . $c["objects"] . '">' . $c["name"] . '</a>',
		"edit" =>
			'<a class="floaticon" href="'.CATEGORIES_PKG_URL.'admin/index.php?parent_id=' . $c["parent_id"] . '&amp;category_id=' . $c["category_id"] . '#editcreate" title="' . tra( 'edit'). '"><img class="icon" src="'.LIBERTY_PKG_URL.'icons/edit.gif" /></a>',
		"remove" =>
			'<a class="floaticon" href="'.CATEGORIES_PKG_URL.'admin/index.php?parent_id=' . $c["parent_id"] . '&amp;removeCat=' . $c["category_id"] . '" title="' . tra( 'remove'). '"><img class="icon" src="'.LIBERTY_PKG_URL.'icons/delete.png" /></a>',
		"children" => 0,
//		"children" => $c["children"],
		"objects" => $c["objects"]
	);
}

//$debugger->var_dump('$tree_nodes');
$tm = new CatAdminTreeMaker("admcat");
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

$gBitSmarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$gBitSmarty->assign('find', $find);

if (isset($_REQUEST["find_objects"])) {
	$find_objects = $_REQUEST["find_objects"];
} else {
	$find_objects = '';
}

$gBitSmarty->assign('find_objects', $find_objects);

$gBitSmarty->assign_by_ref('sort_mode', $sort_mode);
$gBitSmarty->assign_by_ref('find', $find);

$objects = $categlib->list_category_objects($_REQUEST["parent_id"], $offset, $maxRecords, $sort_mode, $find);
$gBitSmarty->assign_by_ref('objects', $objects["data"]);

$cant_pages = ceil($objects["cant"] / $maxRecords);
$gBitSmarty->assign_by_ref('cant_pages', $cant_pages);
$gBitSmarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($objects["cant"] > ($offset + $maxRecords)) {
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

$categories = $categlib->get_all_categories();
$gBitSmarty->assign_by_ref('categories', $categories);

if ( $gBitSystem->isPackageActive( 'imagegals' ) ) {
	$galleries = $gBitSystem->list_galleries(0, -1, 'name_desc', 'admin', $find_objects);
	$gBitSmarty->assign_by_ref('galleries', $galleries["data"]);
}
if ( $gBitSystem->isPackageActive( 'filegals' ) ) {
	$file_galleries = $filegallib->list_file_galleries(0, -1, 'name_desc', 'admin', $find_objects);
	$gBitSmarty->assign_by_ref('file_galleries', $file_galleries["data"]);
}
if ( $gBitSystem->isPackageActive( 'tiki_forums' ) ) {
	$forums = $gBitSystem->list_forums(0, -1, 'name_asc', $find_objects);
	$gBitSmarty->assign_by_ref('forums', $forums["data"]);
}
if ( $gBitSystem->isPackageActive( 'polls' ) ) {
	$polls = $polllib->list_polls(0, -1, 'title_asc', $find_objects);
	$gBitSmarty->assign_by_ref('polls', $polls["data"]);
}
if ( $gBitSystem->isPackageActive( 'blogs' ) ) {
	$blogs = $gBlog->list_blogs(0, -1, 'title_asc', $find_objects);
	$gBitSmarty->assign_by_ref('blogs', $blogs["data"]);
}
if ( $gBitSystem->isPackageActive( 'wiki' ) ) {
	$pages = $wikilib->getList(0, -1, 'title_asc', $find_objects);
	$gBitSmarty->assign_by_ref('pages', $pages["data"]);
}
if ( $gBitSystem->isPackageActive( 'faqs' ) ) {
	$faqs = $gBitSystem->list_faqs(0, -1, 'title_asc', $find_objects);
	$gBitSmarty->assign_by_ref('faqs', $faqs["data"]);
}
if ( $gBitSystem->isPackageActive( 'quizzes' ) ) {
	$quizzes = $gBitSystem->list_quizzes(0, -1, 'name_asc', $find_objects);
	$gBitSmarty->assign_by_ref('quizzes', $quizzes["data"]);
}
if ( $gBitSystem->isPackageActive( 'trackers' ) ) {
	$trackers = $trklib->list_trackers(0, -1, 'name_asc', $find_objects);
	$gBitSmarty->assign_by_ref('trackers', $trackers["data"]);
}
if ( $gBitSystem->isPackageActive( 'articles' ) ) {
	$articles = $artlib->list_articles(0, -1, 'title_asc', $find_objects);
	$gBitSmarty->assign_by_ref('articles', $articles["data"]);
}
if ( $gBitSystem->isPackageActive( 'directory' ) ) {
	$directories = $dirlib->dir_list_all_categories(0, -1, 'name_asc', $find_objects);
	$gBitSmarty->assign_by_ref('directories', $directories["data"]);
}



// Display the template
$gBitSystem->display( 'bitpackage:categories/admin_categories.tpl');

?>

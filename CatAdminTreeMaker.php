<?php
/** \file
 * $Header: /cvsroot/bitweaver/_bit_categories/CatAdminTreeMaker.php,v 1.3 2005/07/17 17:36:01 squareing Exp $
 *
 * Categories browse tree
 *
 * @author zaufi@sendmail.ru
 * @package categories
 */

/**
 * required setup
 */
require_once( UTIL_PKG_PATH.'tree.php' );

/**
 * Class to render categories browse tree
 *
 * @package  categories
 * @subpackage  CatAdminTreeMaker
 */
class CatAdminTreeMaker extends TreeMaker {
	/// Collect javascript cookie set code (internaly used after make_tree() method)
	var $jsscriptblock;

	/// Generated ID (private usage only)
	var $itemID;

	/// Constructor
	function CatAdminTreeMaker($prefix) {
		$this->TreeMaker($prefix);

		$this->jsscriptblock = '';
	}

	/// Generate HTML code for tree. Need to redefine to add javascript cookies block
	function make_tree($rootid, $ar) {
		global $debugger;

		$r = '<ul class="tree">'.$this->make_tree_r($rootid, $ar).'</ul>';
		// $debugger->var_dump('$r');
		// return tree with java script block that opens the nodes as remembered in cookies
		return $r . "<script language='Javascript' type='text/javascript'> " . $this->jsscriptblock . " </script>\n";
	}

	//
	// Change default (no code 'cept user data) generation behaviour
	//  
	// Need to generate:
	//
	// [node start = <div class=treenode><table><tr>]
	//  [flipper] user data    [edit][del]
	// [node data end = </div>]
	// [node child start = <div class=tree>]
	//   [childs code]
	// [node child end = </div>]
	//
	// Unsymmetrical calls is not important :)
	//
	function node_start_code($nodeinfo) {
		return '<li class="treenode">';
	}

	//
	function node_flipper_code($nodeinfo) {
		$this->itemID = $this->prefix . 'id' . $nodeinfo["id"];

		$this->jsscriptblock .= "setFlipWithSign('" . $this->itemID . "'); ";
		return '<a class="catname" title="' . tra( 'child categories'). ': ' . $nodeinfo["children"] . ', ' . tra('objects in category'). ': ' . $nodeinfo["objects"] . '" id="flipper' . $this->itemID . '" href="javascript:flipWithSign(\'' . $this->itemID . '\')">[+]</a>';
	}

	//
	function node_data_start_code($nodeinfo) {
		return $nodeinfo["edit"] . $nodeinfo["remove"] ;
	}

	//
	function node_data_end_code($nodeinfo) {
		if( !empty( $nodeinfo['objects'] ) ) {
			return '('.$nodeinfo['objects'].')';
		}
	}

	//
	function node_child_start_code($nodeinfo) {
		return "\n".'<ul class="tree" id="' . $this->itemID . '" style="display: none;">'."\n";
	}

	//
	function node_child_end_code($nodeinfo) {
		return '</ul>';
	}

	//
	function node_end_code($nodeinfo) {
		return "</li>\n";
	}
}

?>

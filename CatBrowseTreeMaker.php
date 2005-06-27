<?php
/** \file
 * $Header: /cvsroot/bitweaver/_bit_categories/CatBrowseTreeMaker.php,v 1.1.1.1.2.1 2005/06/27 10:08:39 lsces Exp $
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
 * @subpackage  CatBrowseTreeMaker
 */
class CatBrowseTreeMaker extends TreeMaker {
	/// Collect javascript cookie set code (internaly used after make_tree() method)
	var $jsscriptblock;

	/// Generated ID (private usage only)
	var $itemID;

	/// Constructor
	function CatBrowseTreeMaker($prefix) {
		$this->TreeMaker($prefix);

		$this->jsscriptblock = '';
	}

	/// Generate HTML code for tree. Need to redefine to add javascript cookies block
	function make_tree($rootid, $ar) {
		global $debugger;

		$r = $this->make_tree_r($rootid, $ar);
		// $debugger->var_dump('$r');
		// return tree with java script block that opens the nodes as remembered in cookies
		return $r . "<script language='Javascript' type='text/javascript'> " . $this->jsscriptblock . " </script>\n";
	}

	//
	// Change default (no code 'cept user data) generation behaviour
	//  
	// Need to generate:
	//
	// [node start = <div class=treenode>]
	//  [flipper] user data
	// [node data end = </div>]
	// [node child start = <div class=tree>]
	//   [childs code]
	// [node child end = </div>]
	//
	// Unsymmetrical calls is not important :)
	//
	function node_start_code($nodeinfo) {
		return '<div class="treenode">';
	}

	//
	function node_flipper_code($nodeinfo) {
		$this->itemID = $this->prefix . 'id' . $nodeinfo["id"];

		$this->jsscriptblock .= "setFlipWithSign('" . $this->itemID . "'); ";
		return '<a id="flipper' . $this->itemID . '" href="javascript:flipWithSign(\'' . $this->itemID . '\')">[+]</a>&nbsp;';
	}

	//
	function node_data_start_code($nodeinfo) {
		return '';
	}

	//
	function node_data_end_code($nodeinfo) {
		return '</div>';
	}

	//
	function node_child_start_code($nodeinfo) {
		return '<div class="tree" id="' . $this->itemID . '" style="display: none;">';
	}

	//
	function node_child_end_code($nodeinfo) {
		return '</div>';
	}

	//
	function node_end_code($nodeinfo) {
		return '';
	}
}

?>

<?php
/**
 * $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id$
 * @package categories
 * @subpackage modules
 */
global $gQueryUserId, $module_rows, $module_params, $categlib, $gContent;
/**
 * required setup
 */
if (isset($gContent)) {
	require_once( CATEGORIES_PKG_PATH.'categ_lib.php');

	$categories = $categlib->get_object_categories( $gContent->getContentType(), $gContent->getContentId() );
	$cats = array();
	foreach($categories as $cat)
		$cats[] = $cat["category_id"];

	$content = $categlib->get_related($cats, $module_rows);
	foreach($content as $con) {
		$tmp = $gContent->getLibertyObject( $con );
		$whatsRelated[] = $tmp->getDisplayLink($tmp->getTitle(), $tmp->mInfo);
	}
	$_template->tpl_vars['whatsRelated'] = new Smarty_variable( $whatsRelated);
}
?>

<?php
/**
 * $Header: /cvsroot/bitweaver/_bit_categories/modules/mod_whats_related.php,v 1.1.1.1.2.6 2005/12/07 06:24:42 wolff_borg Exp $
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details
 *
 * $Id: mod_whats_related.php,v 1.1.1.1.2.6 2005/12/07 06:24:42 wolff_borg Exp $
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
	$gBitSmarty->assign_by_ref('whatsRelated', $whatsRelated);
}
?>

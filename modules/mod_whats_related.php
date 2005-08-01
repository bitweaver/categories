<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_categories/modules/mod_whats_related.php,v 1.3 2005/08/01 18:40:06 squareing Exp $
 * @package categories
 * @subpackage modules
 */

/**
 * required setup
 */
require_once( CATEGORIES_PKG_PATH.'categ_lib.php');

//test
//$WhatsRelated=$categlib->get_link_related($_SERVER["REQUEST_URI"]);
$gBitSmarty->assign_by_ref('WhatsRelated', $WhatsRelated);


?>

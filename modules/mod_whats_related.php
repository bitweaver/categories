<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_categories/modules/mod_whats_related.php,v 1.1.1.1.2.2 2005/07/26 15:50:04 drewslater Exp $
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

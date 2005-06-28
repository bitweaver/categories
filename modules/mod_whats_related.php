<?php
/**
 * @version $Header: /cvsroot/bitweaver/_bit_categories/modules/mod_whats_related.php,v 1.2 2005/06/28 07:45:41 spiderr Exp $
 * @package categories
 * @subpackage modules
 */

/**
 * required setup
 */
require_once( CATEGORIES_PKG_PATH.'categ_lib.php');

//test
//$WhatsRelated=$categlib->get_link_related($_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('WhatsRelated', $WhatsRelated);


?>

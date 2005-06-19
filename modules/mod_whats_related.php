<?php

require_once( CATEGORIES_PKG_PATH.'categ_lib.php');

//test
//$WhatsRelated=$categlib->get_link_related($_SERVER["REQUEST_URI"]);
$smarty->assign_by_ref('WhatsRelated', $WhatsRelated);


?>

<?php

// $Header: /cvsroot/bitweaver/_bit_categories/admin/admin_categories_inc.php,v 1.1 2006/09/10 17:30:35 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.


$formFeaturesBit['categories_objects'] = array(
	'label' => 'Show Category Objects',
	'note' => 'Display a list of items that are part of a particular category at the bottom of the page.',
);
$formFeaturesBit['categories_path'] = array(
	'label' => 'Show Category Path',
	'note' => 'Display the category path at the top of the page',
);
$gBitSmarty->assign( 'formFeaturesBit',$formFeaturesBit );

$processForm = set_tab();
if( $processForm ) {
	$featureToggles = array_merge( $formFeaturesBit,$formFeaturesHelp );
	foreach( $featureToggles as $item => $info ) {
		simple_set_toggle( $item, CATEGORIES_PKG_NAME );
	}
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');
?>

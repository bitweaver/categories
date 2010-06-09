<?php

// $Header$

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See below for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details.


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
	foreach( $formFeaturesBit as $item => $info ) {
		simple_set_toggle( $item, CATEGORIES_PKG_NAME );
	}
}

$gBitSystem->setHelpInfo('Features','Settings','Help with the features settings');
?>

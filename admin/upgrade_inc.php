<?php

global $gBitSystem, $gUpgradeFrom, $gUpgradeTo;

$upgrades = array(


	'BWR1' => array(
		'BWR2' => array(
// de-tikify tables
array( 'DATADICT' => array(
	array( 'RENAMETABLE' => array(
		'tiki_categories' => 'categories',
		'tiki_categorized_objects' => 'categories_objects',
		'tiki_categories_objects' => 'categories_objects_map',
	)),
)),
		)
	),

'BONNIE' => array(
	'BWR1' => array(

// STEP 1
array( 'DATADICT' => array(
array( 'RENAMECOLUMN' => array(
	'tiki_categories' => array( '`categId`' => '`category_id` I4 AUTO',
								'`parentId`' => '`parent_id` I4' ),
	'tiki_categorized_objects' => array( '`catObjectId`' => '`cat_object_id` I4',
									  '`type`' => '`object_type` C(50)' ),
	'tiki_category_objects' => array( '`catObjectId`' => '`cat_object_id` I4',
									  '`categId`' => '`category_id` I4' ),
	'tiki_category_sites' => array( '`categId`' => '`category_id` I4',
									'`siteId`' => '`site_id` I4' ),
	),
),
array( 'ALTER' => array(
	'tiki_categorized_objects' => array(
		'object_id' => array( '`object_id`', 'I4' ), // , 'NOTNULL' ),
	)
)),
)),

// STEP 3
array( 'DATADICT' => array(
	array( 'DROPCOLUMN' => array(
//		'tiki_categorized_objects' => array( '`objId`' ),
	)),
)),


	)
)
);

if( isset( $upgrades[$gUpgradeFrom][$gUpgradeTo] ) ) {
	$gBitSystem->registerUpgrade( CATEGORIES_PKG_NAME, $upgrades[$gUpgradeFrom][$gUpgradeTo] );
}


?>

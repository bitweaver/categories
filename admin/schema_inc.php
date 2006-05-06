<?php

$tables = array(

'categories' => "
	category_id I4 AUTO PRIMARY,
	name C(100),
	description C(250),
	parent_id I4,
	hits I4
",

'categories_objects' => "
	cat_object_id I4 AUTO PRIMARY,
	object_type C(20) NOTNULL,
	object_id I4 NOTNULL,
	description X,
	created I8,
	name C(200),
	href C(200),
	hits I4
",

'categories_objects_map' => "
	cat_object_id I4 PRIMARY,
	category_id I4 PRIMARY
"

);

global $gBitInstaller;

foreach( array_keys( $tables ) AS $tableName ) {
	$gBitInstaller->registerSchemaTable( CATEGORIES_PKG_NAME, $tableName, $tables[$tableName] );
}

$gBitInstaller->registerPackageInfo( CATEGORIES_PKG_NAME, array(
	'description' => "Using this package you can categorise any object on this site, allowing for grouping of similar objects. It is also possible to allocate specific themes to categories.",
	'license' => '<a href="http://www.gnu.org/licenses/licenses.html#LGPL">LGPL</a>',
) );


// ### Indexes
$indices = array (
	'cat_obj_obj_idx' => array( 'table' => 'categories_objects', 'cols' => 'object_id', 'opts' => NULL ),
	'cat_obj_type_idx' => array( 'table' => 'categories_objects', 'cols' => 'object_type', 'opts' => NULL ),
	'cat_obj_map_cat_idx' => array( 'table' => 'categories_objects_map', 'cols' => 'category_id', 'opts' => NULL ),
	'cat_obj_map_cat_obj_idx' => array( 'table' => 'categories_objects_map', 'cols' => 'cat_object_id', 'opts' => NULL )
);
$gBitInstaller->registerSchemaIndexes( CATEGORIES_PKG_NAME, $indices );

// ### Default Preferences
$gBitInstaller->registerPreferences( CATEGORIES_PKG_NAME, array(
	array('', 'categories_objects','n'),
	array('', 'categories_path','n')
) );

// ### Default UserPermissions
$gBitInstaller->registerUserPermissions( CATEGORIES_PKG_NAME, array(
	array('p_categories_admin', 'Can admin categories', 'editors', CATEGORIES_PKG_NAME),
	array('p_categories_view', 'Can browse categories', 'registered', CATEGORIES_PKG_NAME)
) );

$schemaDefault = array();
global $gBitDbType;
if ( $gBitDbType == 'mssql' ) {
	$schemaDefault[] = "SET IDENTITY_INSERT `".BIT_DB_PREFIX."categories` ON";
}

$schemaDefault[] = "INSERT INTO `".BIT_DB_PREFIX."categories` (`category_id`, `name`, `description`, `parent_id`, `hits` ) VALUES ( 0, '".tra("TOP")."', '', 0, 0 )";

$gBitInstaller->registerSchemaDefault( CATEGORIES_PKG_NAME, $schemaDefault );

?>

<?php

/**
* Plugin Name: Intimation Defaults
* Plugin URI: https: //intimation.uk
* Description: Removes admin notices for updates, limits user capabilities and provides a custom dashboard
* Version: 1.0
* Author: Paul Spence - Intimation
* Author URI: https: //intimation.uk
* Licence: GPL
*/

/**
 * Define Constants
 * @since 1.0.0
 */
define( 'IDEF_BASE_FILE', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'IDEF_BASE_FOLDER', basename( dirname( __FILE__ ) ) );
define( 'IDEF_ABS_FOLDER', dirname( __FILE__ ) );
define( 'IDEF_ABS_FOLDER_FILE', dirname( __FILE__ )  . '/' . basename( __FILE__ ) );

/**
 * Include files
 * @since 1.0
 */
include_once plugin_dir_path( __FILE__ ) . '/classes/loader.php'; // main plugin functionality
// include_once plugin_dir_path( __FILE__ ) . '/admin/admin.php'; // admin functionality

/**
 * Load the plugin
 * @since 1.0
 */
function loader()
{
    $instance = Loader::instance( __FILE__ , '1.0' );

    return $instance;
}

loader();
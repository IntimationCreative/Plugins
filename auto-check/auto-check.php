<?php

/**
 * Plugin Name: Auto Check Updates Remotley
 * Plugin URI: https://intimation.uk
 * Description: Manage remote plugin updates
 * Version: 1.0
 * Author: Paul Spence - Intimation
 * Author URI: https://intimation.uk
 * Licence: GPL
 */

/**
 * Define Constants
 * @since 1.0
 */
define( 'AC_BASE_FILE', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'AC_BASE_FOLDER', basename( dirname( __FILE__ ) ) );
define( 'AC_ABS_FOLDER', dirname( __FILE__ ) );

/**
 * WP Requires
 * @since 1.0
 */
require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

/**
 * Include files
 * @since 1.0
 */
include_once plugin_dir_path( __FILE__ ) . '/classes/class-autocheck.php'; // main plugin functionality
include_once plugin_dir_path( __FILE__ ) . '/admin/admin.php'; // admin functionality

/**
 * Load the plugin
 * @since 1.0
 */
function autocheck()
{
    $instance = AutoCheck::instance( __FILE__ , '1.0' );

    return $instance;
}

autocheck();
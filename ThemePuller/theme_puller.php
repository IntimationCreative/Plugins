<?php

/**
 * Plugin Name: Theme Puller
 * Plugin URI: https://intimation.uk
 * Description: listens for a HTTP request from a given control site
 * Author Name: Paul Spence - Intimation
 * Author URI: https://intimation.uk
 * Version: 1.0
 * Licence: GPL
 */

/**
 * Include Plugin Files
 */
include_once plugin_dir_path( __FILE__ ) . '/classes/class_theme_puller.php';

/**
 * Define Constants
 */
define( 'BASE_FILE', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'BASE_FOLDER', basename( dirname( __FILE__ ) ) );
define( 'ABS_FOLDER', dirname( __FILE__ ) );

/**
 * Load the Plugin
 */
function theme_puller()
{
    $instance = ThemePuller::instance( __FILE__, '1.0' );
    return $instance;
}

theme_puller();
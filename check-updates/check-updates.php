<?php

/**
 * Plugin Name: Auto Check Updates Remotley
 * Plugin URI: https://intimation.uk
 * Description: Manage remote site / sites updates
 * Version: 1.0
 * Author: Paul Spence - Intimation
 * Author URI: https://intimation.uk
 * Licence: GPL
 */

/**
 * Define Constants
 */
define( 'O_BASE_FILE', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'O_BASE_FOLDER', basename( dirname( __FILE__ ) ) );
define( 'O_ABS_FOLDER', dirname( __FILE__ ) );

/**
 * Include files
 */
include_once plugin_dir_path( __FILE__ ) . '/classes/class_autocheck.php';

/**
 * Load the plugin
 */
function autocheck()
{
    $instance = AutoCheck::instance( __FILE__ , '1.0' );

    return $instance;
}
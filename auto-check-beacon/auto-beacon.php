<?php

/**
 * Plugin Name: Auto Check Beacon
 * Plugin URI: https://intimation.uk
 * Description: listen for the control plugin
 * Version: 1.0
 * Author: Paul Spence - Intimation
 * Author URI: https://intimation.uk
 * Licence: GPL
 */

/**
 * Define Constants
 */
define( 'AB_BASE_FILE', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'AB_BASE_FOLDER', basename( dirname( __FILE__ ) ) );
define( 'AB_ABS_FOLDER', dirname( __FILE__ ) );

/**
 * Include files
 */
include_once plugin_dir_path( __FILE__ ) . '/classes/class_autobeacon.php';
include_once plugin_dir_path( __FILE__ ) . '/classes/class_quiet_skin.php';

/**
 * Load the plugin
 */
function autobeacon()
{
    $instance = AutoBeacon::instance( __FILE__ , '1.0' );

    return $instance;
}

autobeacon();
<?php

/**
 * Plugin Name: Intimation Export Users
 * Plugin URI: http://intimation.uk
 * Description: Export Users in CSV
 * Version: 0.1
 * Author: Paul Spence - Intimation
 * Author URI: http://intimation.uk
 * License: GPL
 */

 /**
 * Include the main plugin class.
 */
include_once plugin_dir_path( __FILE__ ) . 'classes/class-inti-export-users.php';

// define the path to plugin file (export-users/export-users.php)
define( 'IEU_PLUGIN_BASENAME', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

// define the plugin base folder
define( 'IEU_PLUGIN_BASE_FOLDER', basename( dirname( __FILE__ ) ) );

//define abs path to plugin folder (/var/www/vhosts/quorum.dev/httpdocs/wp-content/plugins/export-users)
define( 'IEU_PLUGIN_ABS_FOLDER', dirname( __FILE__ ) );

define( 'IEU_SITE_URL', '/var/www/vhosts/quorumbusiness.co.uk/httpdocs/' );

/**
 * Loads the whole plugin.
 *
 * @since 1.0.0
 * @return inti_export_users
 */
function inti_export_users() 
{
	$instance = Inti_Export_Users::instance( __FILE__, '0.1' );

	return $instance;
}

inti_export_users();
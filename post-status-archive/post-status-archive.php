<?php

/**
 * Plugin Name: Post Status Archive
 * Plugin URI: http://intimation.uk
 * Description: Automatically archive posts
 * Version: 0.1
 * Author: Paul Spence
 * Author URI: http://intimation.uk
 * License: GPL
 */

 /**
 * Include the main plugin class.
 */
include_once plugin_dir_path( __FILE__ ) . 'inc/class-post-status-archive.php';

define( 'PSA_PLUGIN_BASENAME', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );

/**
 * Loads the whole plugin.
 *
 * @since 1.0.0
 * @return PS_Post_Status_Archive
 */
function PS_Post_Status_Archive() 
{
	$instance = PS_Post_Status_Archive::instance( __FILE__, '0.1' );

	return $instance;
}

PS_Post_Status_Archive();
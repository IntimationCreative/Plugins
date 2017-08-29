<?php

/*
 * Plugin Name: My Oauth
 * Plugin URI: http://paulspence.me.uk
 * Description: An easy to use oauth generator. This plugin was written to interact with the Wordpress API using OAuth 1.0
 * Version: 1.0
 * Author: Paul
 * Author URI: http://paulspence.me.uk
 * License: GPL2
 */


/**
 * Include Files
 */
include_once plugin_dir_path( __FILE__ ) . 'classes/class-oauth.php';
include_once plugin_dir_path( __FILE__ ) . 'classes/oauth-settings.php';
include_once plugin_dir_path( __FILE__ ) . 'classes/class-oauth-woo.php';

/**
 * Define Constants
 */
define( 'O_BASE_FILE', basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ) );
define( 'O_BASE_FOLDER', basename( dirname( __FILE__ ) ) );
define( 'O_ABS_FOLDER', dirname( __FILE__ ) );

/**
 * Load the plugin
 *
 * @since 1.0
 * @return oauth
 */
function oauth_loader()
{
    $instance = Oauth::instance( __FILE__, '1.0' );

    return $instance;
}

oauth_loader();
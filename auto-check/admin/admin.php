<?php 
/**
 * Admin Setup
 * @since 1.0
 */

include_once plugin_dir_path( __FILE__ ) . '/classes/class-autocheck-admin.php';
include_once plugin_dir_path( __FILE__ ) . '/classes/class-autocheck-listtable.php';
include_once plugin_dir_path( __FILE__ ) . '/classes/class-autocheck-crud.php';

$admin_settings = new AutoCheckAdmin(); // start admin
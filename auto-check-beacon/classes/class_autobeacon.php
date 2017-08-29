<?php

class AutoBeacon
{
    private static $instance;

    function __construct()
    {
        add_action( 'wp_loaded', array($this, 'get_updates'));
        add_action( 'wp_loaded', array($this, 'bulk_update'));
        // add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'modify_transient' ), 10, 1 );
    }

    /**
     * Instance loader
     * @since 1.0
     * @param string $file current file
     * @param string $version current version number
     * @return object $instance singleton instance of the class
     */
    public static function instance( $file, $version )
    {
        if ( is_null(self::$instance) ) {
            self::$instance = new self( $file, $version );
        }
        return self::$instance;
    }

    public function get_updates()
    {
        if (!isset($_POST['get_updates'])) {
            return;
        }

        wp_update_plugins();

        $data = array();

        $data['current'] = get_site_transient( 'update_plugins' );

        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( ! function_exists( 'get_plugin_updates' ) ) {
            require_once ABSPATH . 'wp-admin/includes/update.php';
        }

        wp_send_json_success( get_plugin_updates() );

        exit;

    }

    function bulk_update()
    {
        if (!isset($_POST['bulk_update'])) {
            return;
        }

        $data = array();
        if ( ! class_exists('Plugin_Upgrader') ) {
            include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
            include_once ABSPATH . 'wp-admin/includes/file.php';
            include_once ABSPATH . 'wp-admin/includes/misc.php';
            
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
            require_once ABSPATH . 'wp-admin/includes/update.php';
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
            include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
            require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader-skin.php';
            require_once ABSPATH . 'wp-admin/includes/class-bulk-plugin-upgrader-skin.php';
        }
        
        $bulkskin = new Bulk_Plugin_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader( new Quiet_Skin() );

        $plugins = get_plugin_updates();
        $plugins_array = array();

        if ($_POST['additional'] !== '') {
            $plugins = $_POST['additional'];
            
            foreach ($plugins as $key => $plugin) {
                $plugins_array[] = $plugin;
            }
        } else {
            foreach ($plugins as $key => $plugin) {
                $plugins_array[] = $key;
            }
        } 

        $data['plugin'] = $upgrader->bulk_upgrade($plugins_array);

        wp_update_plugins();

        wp_send_json_success( $data );
        exit;
    }
}

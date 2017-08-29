<?php
/**
 * AutoCheck
 * Holds the main plugin functionality
 * @since 1.0
 */
class AutoCheck
{
    private static $instance;

    public $directory;
    public $settings;

    public function __construct()
    {
        add_action( 'admin_enqueue_scripts', array($this, 'load_scripts') );
        add_action( 'init', array( $this, 'autocheck_post_type') );

        add_action( 'wp_ajax_nopriv_get_updates', array($this, 'get_updates') );
        add_action( 'wp_ajax_get_updates', array($this, 'get_updates') );

        add_action( 'wp_ajax_nopriv_bulk_update', array($this, 'bulk_update') );
        add_action( 'wp_ajax_bulk_update', array($this, 'bulk_update') );

        add_action( 'wp_ajax_nopriv_single_update', array($this, 'single_update') );
        add_action( 'wp_ajax_single_update', array($this, 'single_update') );

        $this->set_directory_value();
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

    /**
     * get all plugins that needs updated from remote site
     * @since 1.0
     */
    public function get_updates()
    {
        $this->auto_check('get_updates');
    }

    /**
     * bulk update plugins from remote site
     * @since 1.0
     */
    public function bulk_update()
    {
        $this->auto_check('bulk_update');
    }

    /**
     * Auto Checker
     * make a HTTP POST request
     * to a remote site with the beacon installed
     * @since 1.0
     * @param string $action
     * @return void - exits on completion
     */
    public function auto_check($action)
    {  
        $data = array();
        if ( isset( $_POST['additional'] ) ) {
            $additional = $_POST['additional'];
        } else {
            $additional = "";
        }
        $args = array(
            'headers' => array(),
            'body' => array(
                $action => $action,
                'additional' => $additional
            )    
        );
        $url = $_POST['url'];
        $request = wp_remote_post( $url, $args );
        $body = wp_remote_retrieve_body( $request );
        $responses = json_decode( $body );
        foreach ( $responses as $response ) {
            $data['plugins'] = $response;
        }
        wp_send_json_success( $data );
        exit;
    }

    /**
     * scripts and styles
     * @since 1.0
     * Load all the scripts and styles
     **/
    public function load_scripts()
    {
        wp_enqueue_script( 
            'autocheck-js', 
            $this->directory . '/assets/js/autocheck-scripts.js', 
            array('jquery') 
        );
        wp_enqueue_style( 
            'autocheck-css', 
            $this->directory . '/assets/css/autocheck-styles.css' 
        );
        $data = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'autocheck' )
        );
        wp_localize_script( 'autocheck-js', 'autocheck', $data );
    }

    /**
     * Set the directory
     * @since 1.0
     */
    public function set_directory_value(){
        $this->directory = plugins_url() . '/' . AC_BASE_FOLDER;
    }

    /**
     * register post type
     * @since 1.0
     * @return void
     */
    function autocheck_post_type() {
        register_post_type( 'sites_auto_check', array(
            'labels' => array(
                'name' => __( 'Site', 'autocheck' ),
                'singular_name' => __( 'Sites', 'autocheck' ),
            ),
            'public' => false,
            'hierarchical' => false,
            'rewrite' => false,
        ) );
    }
}
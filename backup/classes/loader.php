<?php
/**
 * Loader
 * @since 1.0.0
 */

include_once plugin_dir_path( __FILE__ ) . '/anti-nag/class-anti-nag.php';
include_once plugin_dir_path( __FILE__ ) . '/custom-dash/class-custom-dash.php';
include_once plugin_dir_path( __FILE__ ) . '/custom-dash/class-analytics.php';
include_once plugin_dir_path( __FILE__ ) . '/user-caps/class-user-caps.php';

/**
 * Loader Nag
 */

class Loader
{
    private static $instance;
    public $directory;

    public function __construct()
    {
        $anti_nag = new AntiNag(); // start anti nag
        $custom_dash = new CustomDash(); // start anti nag
        $user_caps = new UserCaps(); // start anti nag

        add_action( 'admin_enqueue_scripts', array($this, 'load_scripts') );
    }

    /**
     * Static Singleton Instance
     *
     * @param string $file
     * @param string $version
     * @return $instance
     */
    public static function instance( $file, $version )
    {
        if ( is_null(self::$instance) ) {
            self::$instance = new self( $file, $version );
        }
        return self::$instance;
    }

    /**
     * scripts and styles
     * @since 1.0
     * Load all the scripts and styles
     **/
    public function load_scripts()
    {

        wp_enqueue_script( 
            'autocheck-js-2', 
            $this->directory . '/wp-content/plugins/intimation-defaults/assets/js/moment.js', 
            array('jquery') 
        );

        wp_enqueue_script( 
            'autocheck-js', 
            $this->directory . '/wp-content/plugins/intimation-defaults/assets/js/chart.min.js', 
            array('jquery') 
        );
    }

    /**
     * Set the directory
     * @since 1.0
     */
    public function set_directory_value(){
        $this->directory = plugins_url() . '/' . IDEF_BASE_FOLDER;
    }
}


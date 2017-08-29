<?php

class AutoCheck
{
    private static $instance;

    public $directory;

    public function __construct()
    {
        $this->load_hooks();
        $this->load_scripts();

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
     * action hooks
     * @since 1.0
     * Load all the action hooks
     **/
    private function load_hooks()
    {
        if (is_admin() && isset($_GET['page']) == oauth )
		{
			add_action( 'admin_enqueue_scripts', array($this, 'load_scripts'));
		}
		
		// AJAX ACTIONS
		// add_action( 'wp_ajax_nopriv_request', array($this, 'request') );
		// add_action( 'wp_ajax_request', array($this, 'request') );
    }

    /**
     * scripts and styles
     * @since 1.0
     * Load all the scripts and styles
     **/
    private function load_scripts()
    {
        // enqueue the script
        wp_enqueue_script( 
            'autocheck-js', 
            $this->directory . '/assets/js/autocheck-scripts.js', 
            array('jquery') 
        );

        wp_enqueue_style( 
            'autocheck-css', 
            $this->directory . '/assets/css/autocheck-styles.css' 
        );

        // data array to pass to localize scripts
        $data = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'autocheck' )
        );
        wp_localize_script( 'autocheck-js', 'autocheck', $data );
    }

    /**
     * Set the directory
     */
    public function set_directory_value(){
        $this->directory = plugins_url() . '/' . O_BASE_FOLDER;
    }
}

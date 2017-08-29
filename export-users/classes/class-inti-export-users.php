<?php

/**
 * Intimation Export Users
 */

class Inti_Export_Users
{

    private static $instance = null;
    
    protected $directory;
    
    function __construct()
    {
        // Activation
        //register_activation_hook( IEU_PLUGIN_BASENAME, array($this, 'psa_archive_posts_on_activation') );

        // Initialise actions
        add_action('admin_menu', array($this, 'ieu_options_page')); // options page
        add_action('admin_init', array($this, 'ieu_admin_settings_init')); // settings

         // load admin scripts and styles
        add_action( 'admin_enqueue_scripts', array($this, 'ieu_scripts_and_styles') );

        // AJAX
        add_action( 'wp_ajax_nopriv_ieu_export', array($this, 'ieu_export') );
        add_action( 'wp_ajax_ieu_export', array($this, 'ieu_export') );

        // Deactivation
        // register_deactivation_hook( IEU_PLUGIN_BASENAME, array($this, 'psa_archive_posts_on_deactivation') );

        // set the directory value for use
        $this->set_directory_value();

        
    }

    /**
	 * Sets up the main instance
     */
    public static function instance( $file = '', $version = '0.1')
    {
        if ( is_null(self::$instance) ) 
        {
            self::$instance = new self( $file, $version );
        }

        return self::$instance;
    }

    /**
     * Set the directory
     */
    public function set_directory_value(){
        $this->directory = plugins_url() . '/' . IEU_PLUGIN_BASE_FOLDER;
    }


    /**
     * Callback to load the admin view HTML
     */
    function ieu_options_page_view()
    {
        if ( !current_user_can('manage_options') ) return;

        include IEU_PLUGIN_ABS_FOLDER . '/admin/ieu_options_view.php';
    }

    
    /**
     * Add a menu item called export users
     * @params for add_menu_page: 
     * string $page_title, string $menu_title, string $capability, 
     * string $menu_slug, callable $function = '', string $icon_url = '', int $position = null
     */
    
    function ieu_options_page()
    {
        add_menu_page(
            'Export Users',
            'EXPORT USERS',
            'manage_options',
            'inti_export_users',
            array($this, 'ieu_options_page_view'),
            'dashicons-download',
            33
        );
    }
    
    /**
     * register settings
     */
    function ieu_admin_settings_init()
    {
        register_setting(
            'export_users_settings', // Option group
            'registered_qcard_users', // Option Name
            array($this, 'sanitize') // Callback
        );

        add_settings_section(
            'registered_qcard_users_section', // ID
            ' ', // Title
            array($this, 'ieu_get_users'), // Callback
            'inti_export_users' // Page
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
    	$new_input = array();
    	if( isset($input['show_manufacturers']) )
    		$new_input['show_manufacturers'] = $input['show_manufacturers'];

    	// if( isset( $input['title'] ) )
    	// 	$new_input['title'] = sanitize_text_field( $input['title'] );

    	return $new_input;
    }

    /**
     * Intimation Get Users
     */
    public function ieu_get_users() 
    {
        global $wpdb;

        $users = $wpdb->get_results( 
            "
            SELECT ID, user_email, display_name 
            FROM $wpdb->users
            "
        );

        // echo count($users);

        return $users;
    }

    /**
     * handle the export
     */
    public function ieu_export()
    {
        
        // Security check.
        check_ajax_referer( 'ieu_export', 'nonce' );

        // create the headers and set the content type
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=qcard-users.csv');

        // open a file for writing
        $file = fopen( IEU_SITE_URL . 'qcard-users.csv', 'w');        

        // varibles to pass into the fputcsv
        $handle = $file;
        $fields = $this->ieu_get_users();
        $delimiter = ',';

        // output the column headings
        fputcsv( $file, array('ID', 'Email', 'Name', 'Company', 'Qcard ID', 'First Name', 'Last Name') );

        foreach ($fields as $field) {
            // company
            $occupier = get_user_meta($field->ID, 'occupier', true);
            $occupier = get_post( $occupier );
            $company = $occupier->post_title;
            // qcard
            $qcardid = get_user_meta($field->ID, 'qcard_id', true);

            //First and Last Name
            $first = get_user_meta($field->ID, 'first_name', true);
            $last = get_user_meta($field->ID, 'last_name', true);
            
            $field = array( $field->ID, $field->user_email, str_replace('-', ' ', $field->display_name), $company, $qcardid, $first, $last );
            fputcsv( $handle, $field, $delimiter );
        }

        fclose($file);

        $outputLink = site_url( '/qcard-users.csv' );

        $data = $outputLink;

        wp_send_json_success( $data );   

        exit;     

    }

    /**
     * Import scripts and styles
     */
    public function ieu_scripts_and_styles()
    {
        wp_enqueue_script( 
            'ieu-scripts',
            $this->directory . '/js/ieu-scripts.js', 
            array('jquery')
        );

        $data = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'ieu_export' )
        );
        wp_localize_script( 'ieu-scripts', 'ieu_export', $data );

        wp_enqueue_style( 
            'ieu-styles', 
            $this->directory . '/css/ieu_export_users.css'
        );
    }
}

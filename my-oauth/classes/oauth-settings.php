<?php

/**
 * Oauth Settings Class
 */

class OauthSettings
{

    private $settings;

    function __construct()
    {
        $this->set_admin_settings();
        
        // print_r( $this->settings );
        // Plugin settings
        add_action('admin_menu', array($this, 'oauth_options_page')); // options page
        add_action('admin_init', array($this, 'oauth_admin_settings_init')); // settings
    }

    /**
     * Plugin Settings
     * Add a menu item called theme Settings
     */        
    function oauth_options_page()
    {
        add_menu_page(
            'Oauth', // PAGE TITLE
            'Oauth 1.0a', // MENU TITLE
            'manage_options', // CAPABILITY
            'oauth', // MENU SLUG
            array($this, 'oauth_settings_view'), // FUNCTION NAME
            'dashicons-share', // ICON URL
            33 // POSITION
        );
    }


    /**
     * Callback to load the admin view HTML
     */
    function oauth_settings_view()
    {
        if ( !current_user_can('manage_options') ) return;
        include O_ABS_FOLDER  . '/views/settings_views.php';
    }


    /**
     * register settings
     */
    function oauth_admin_settings_init()
    {
        if (false == get_option( 'oauth_display_options' )) {
            add_option( 'oauth_display_options' );
        }
        
        // register a new section in the "oauth" page
        add_settings_section(
            'oauth_options_info', // ID
            'Authorized Sites', // TITLE
            array($this, 'oauth_options_instructions'), // CB
            'oauth_options_info' // PAGE
        );

        // register a new section in the "oauth" page
        add_settings_section(
            'oauth_options', // ID
            '', // TITLE
            '', // CB
            'oauth_display_options' // PAGE
        );

        if(isset($_GET["tab"]))
        {
            if($_GET["tab"] == "site-list")
            {
                add_settings_field( 
                    'site_list', // ID
                    '', // TITLE
                    array($this, 'site_list'), // CB
                    'oauth_display_options', // PAGE
                    'oauth_options' // SECTION ID
                );

                register_setting( 
                    'oauth_display_options', // OPTION GROUP 
                    'oauth_display_options',// OPTION NAME
                    array($this, 'sanitize' ) // SANITIZE CB
                );
            } else 
            {
                add_settings_field( 
                    'product_list', // ID
                    '', // TITLE
                    array($this, 'product_list'), // CB
                    'oauth_display_options', // PAGE
                    'oauth_options' // SECTION ID
                );

                register_setting( 
                    'oauth_display_options', // OPTION GROUP 
                    'oauth_display_options',// OPTION NAME
                    array($this, 'sanitize' ) // SANITIZE CB
                );
            }
        } else {
            add_settings_field( 
                'site_list', // ID
                '', // TITLE
                array($this, 'site_list'), // CB
                'oauth_display_options', // PAGE
                'oauth_options' // SECTION ID
            );

            register_setting( 
                'oauth_display_options', // OPTION GROUP 
                'oauth_display_options',// OPTION NAME
                array($this, 'sanitize' ) // SANITIZE CB
            );
        }
        
    }

    public function oauth_options_instructions()
    {
        echo '
            You can use the Site Details tab to complete the "3 legged OAuth 1.0a process" 
            to gain authorized access to an external site that has the WP REST API server installed.
            <br /><br />
            Once you are authorized with an external site you can use the Manage Products tab to update 
            any woocomerce products as long as the REST API is available.
            <br /><br />';
            
        $options = get_option( 'oauth_display_options' );
        print_r($options);
    }

    public function site_list()
    {
        $options = get_option( 'oauth_display_options' );
        include O_ABS_FOLDER  . '/views/authorize.php';
    }

    public function product_list()
    {
        $options = get_option( 'oauth_display_options' );
        include O_ABS_FOLDER  . '/views/product-list.php';
    }


    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $options = get_option( 'oauth_display_options' );
        foreach ( $input as $key => $value )
        {
            $options[$key] = $value;
        }
    	return $options;
    }

    public function sanitizeOLD( $input )
    {
    	// $new_input = array();

        $options = get_option( 'oauth_display_options' );

        foreach ( $input as $key => $value )
        {
            $options[$key] = $value;
        }
        
        // if( isset($input['site_url']) )
    	// 	$options['site_url'] = $input['site_url'];

        // if( isset($input['oauth_consumer_key']) )
    	// 	$options['oauth_consumer_key'] = $input['oauth_consumer_key'];

        // if( isset($input['oauth_consumer_secret']) )
    	// 	$options['oauth_consumer_secret'] = $input['oauth_consumer_secret'];

        // if( isset($input['oauth_token']) )
    	// 	$options['oauth_token'] = $input['oauth_token'];

        // if( isset($input['oauth_secret']) )
    	// 	$options['oauth_secret'] = $input['oauth_secret'];

        // if( isset($input['callback_url']) )
    	// 	$options['callback_url'] = $input['callback_url'];

        // if( isset($input['oauth_verifier']) )
    	// 	$options['oauth_verifier'] = $input['oauth_verifier'];

        // if( isset($input['product_list']['product_name']) )
    	// 	$options['product_list']['product_name'] = $input['product_list']['product_name'];

        // if( isset($input['product_list']['product_sku']) )
    	// 	$options['product_list']['product_sku'] = $input['product_list']['product_sku'];

        // if( isset($input['product_list']['product_price']) )
    	// 	$options['product_list']['product_price'] = $input['product_list']['product_price'];

        // if( isset($input['product_list']['product_date']) )
    	// 	$options['product_list']['product_date'] = $input['product_list']['product_date'];
            
    	return $options;
    }

    public function set_admin_settings()
    {
        $this->settings = get_option( 'oauth_display_options' );
    }

    public function get_admin_settings()
    {
        return $this->settings;
    }

    /**
	 * Get the URL for an admin page.
	 *
	 * @param array|string $params Map of parameter key => value, or wp_parse_args string.
	 * @return string Requested URL.
	 */
	public function get_url( $params = array() ) {
		$url = admin_url( 'admin.php' );
		$params = array( 'page' => 'oauth' ) + wp_parse_args( $params );
		return add_query_arg( urlencode_deep( $params ), $url );
	}

    /**
     * Get Auth URL
     */
    public function get_auth_url()
    {
        $settings = $this->get_admin_settings();
        $auth_url = $settings['site_url'] . '/oauth1/authorize'. '?oauth_token=' . $settings['oauth_token'] . '&oauth_callback=' . rawurlencode($settings['callback_url']);
        return $auth_url;
    }
}


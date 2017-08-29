<?php

/**
 * AutoCheck Admin Class
 * @since 1.0
 */

class AutoCheckAdmin
{

    private $settings;

    function __construct()
    {
        add_action('admin_menu', array($this, 'autocheck_options_page')); // options page
        add_action('admin_init', array($this, 'autocheck_admin_settings_init')); // settings

        // $this->set_admin_settings();
    }


    /**
     * Plugin Settings
     * Add a menu item called theme Settings
     * @since 1.0
     */        
    function autocheck_options_page()
    {
        add_menu_page(
            'AutoCheck', // PAGE TITLE
            'AutoCheck 1.0', // MENU TITLE
            'manage_options', // CAPABILITY
            'autocheck', // MENU SLUG
            // array($this, 'autocheck_settings_view'), // FUNCTION NAME
            array($this, 'load_admin_step'), // FUNCTION NAME
            'dashicons-schedule', // ICON URL
            33 // POSITION
        );

        add_submenu_page( 'autocheck', 'Site List', 'Site List', 'manage_options', 'autocheck' );
        add_submenu_page( 'autocheck', 'Add Site', 'New Site', 'manage_options', 'autocheck&action=add', array($this, 'autocheck_add_site') );
    }


    /**
     * register settings that will appear in the options page
     * @since 1.0
     */
    function autocheck_admin_settings_init()
    {
        if (false == get_option( 'autocheck_display_options' )) {
            add_option( 'autocheck_display_options' );
        }
        add_settings_section(
            'autocheck_options_info', // ID
            'Remote Site Manager', // TITLE
            array($this, 'autocheck_options_instructions'), // CB
            'autocheck_options_info' // PAGE
        );
        add_settings_section(
            'autocheck_options', // ID
            '', // TITLE
            '', // CB
            'autocheck_display_options' // PAGE
        );
        add_settings_field( 
            'site_list', // ID
            '', // TITLE
            array($this, 'site_list'), // CB
            'autocheck_display_options', // PAGE
            'autocheck_options' // SECTION ID
        );
        register_setting( 
            'autocheck_display_options', // OPTION GROUP 
            'autocheck_display_options',// OPTION NAME
            array($this, 'sanitize' ) // SANITIZE CB
        );
    }

    /**
     * Check current action
     * @since 1.0
     */
    function get_current_action()
    {
        return isset( $_GET['action'] ) ? $_GET['action'] : '';
    }


    /**
     * callback to load a settings page based on action
     * @since 1.0
     */
    function load_admin_step()
    {
        switch ( $this->get_current_action() ) {
            case 'add':
            case 'edit':
                $this->autocheck_add_edit_site();
                break;
            case 'update':
                $this->autocheck_update_site();
                break;
            case 'delete':
                $this->autocheck_delete();
            default:
                $this->autocheck_site_list();
                break;
        }
    }


    /**
     * load the admin list HTML
     * @since 1.0
     */
    function autocheck_site_list()
    {
        if ( !current_user_can('manage_options') ) return;
        $auto_check_site_manager = new Autocheck_List_Table();
        $auto_check_site_manager->prepare_items();

        include AC_ABS_FOLDER  . '/views/site-list.php';
    }


    /**
     * load the admin add new form
     * @since 1.0
     */
    function autocheck_add_edit_site()
    {
        if ( !current_user_can('manage_options') ) return;

        $site = '';

        if ( ! empty( $_REQUEST['id'] ) ) {
            $id = absint( $_REQUEST['id'] );
            $site = new Autocheck_CRUD();
            $site = $site->get( $id );
            // echo '<pre>'; print_r($site); echo '</pre>';
            $url = get_post_meta( $id, 'url', true);
        }
        
        if ( ! empty( $_POST['submit'] ) ) {
            $this->form_handler( $this->get_current_action(), $site );
        }

        include AC_ABS_FOLDER  . '/views/add-edit-site.php';
        
    }


    /**
     * load the admin edit form
     * @since 1.0
     */
    function autocheck_update_site()
    {
         if ( ! empty( $_REQUEST['id'] ) ) {
            $id = absint( $_REQUEST['id'] );
            $site = new Autocheck_CRUD();
            $site = $site->get( $id );
            // echo '<pre>'; print_r($site); echo '</pre>';
            $url = get_post_meta( $id, 'url', true);
        }
        if ( !current_user_can('manage_options') ) return;
        include AC_ABS_FOLDER  . '/views/update-site.php';
    }


    /**
     * Callback to display the description on the options page
     * @since 1.0
     * @return void
     */
    public function autocheck_options_instructions()
    {
        echo '
            Please enter a URL of a site you would like to manage.
            <br /><br />';
            
        $options = get_option( 'autocheck_display_options' );
        //print_r($options);
    }


    /**
	 * Get the URL for an admin page.
     * @since 1.0
	 * @param array|string $params Map of parameter key => value, or wp_parse_args string.
	 * @return string Requested URL.
	 */
	public function get_url( $params = array() ) {
		$url = admin_url( 'admin.php' );
		$params = array( 'page' => 'autocheck' ) + wp_parse_args( $params );
		return add_query_arg( urlencode_deep( $params ), $url );
	}


    /**
     * Handle the form submissions
     * @since 1.0
     */
    function form_handler( $action = '', $site )
    {
        $params['action'] = $action;

        if ( isset($_POST) ) {
            foreach ( $_POST as $key => $value ) {
                $params[$key] = $value;
            }

            $data = array(
                'ID'   => $params['ID'],
                'name' => $params['name'],
                'meta' => array(
                    'url' => $params['site_url']
                )
            );
        }

        $crud = new AutoCheck_CRUD();

        if ( $action == 'edit' ) {
            $crud->update( $data, $site );
        } else {
            $crud->create( $data );
        }
    }


    /**
     * Delete a Site
     * @since 1.0
     */
    function autocheck_delete()
    {
        if ( empty( $_GET['id'] ) ) return;

        $id = $_GET['id'];

        if ( ! current_user_can( 'delete_post', $id ) ) {
            wp_die( 'you can not delete a post', 403 );
        }

        $crud = new AutoCheck_CRUD();
        $crud->get( $id );

        if ( ! $crud->delete( $id ) ) {
			$message = 'Invalid consumer ID';
			wp_die( $message );
			return;
		}
    }
}


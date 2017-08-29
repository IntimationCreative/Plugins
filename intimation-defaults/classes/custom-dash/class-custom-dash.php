<?php
/**
 * Dashboars Customisation
 */

class CustomDash
{
    private static $instance;

    public function __construct()
    {
        // constructor
        add_action('wp_dashboard_setup', array($this, 'remove_dashboard_widgets') );

        remove_action( 'welcome_panel', 'wp_welcome_panel' );
        add_action( 'welcome_panel', array($this, 'custom_welcome_panel') );

        add_filter( 'wp_dashboard_widgets', array($this, 'filter_dashboard'), 10, 1 );
        add_filter( 'wp_user_dashboard_widgets', array($this, 'filter_dashboard'), 10, 1 );

        add_action( 'wp_dashboard_setup', array($this, 'users_analytics_widget') );
        add_action( 'wp_dashboard_setup', array($this, 'sessions_analytics_widget') );
        add_action( 'wp_dashboard_setup', array($this, 'bouncerate_analytics_widget') );
        add_action( 'wp_dashboard_setup', array($this, 'sessionDuration_analytics_widget') );
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


    function filter_dashboard( $dashboard_widgets )
    {
        global $wp_registered_widgets;
        return $dashboard_widgets;
    }

    function remove_dashboard_widgets()
    {
        global $wp_meta_boxes;
        unset($wp_meta_boxes['dashboard']);
    }

    function custom_welcome_panel()
    {
        include IDEF_ABS_FOLDER . '/classes/custom-dash/custom-dash-view.php';
    }

    function users_analytics_widget()
    {
        wp_add_dashboard_widget( 'users_analytics_widget', 'Users', array($this, 'users_analytics_widget_view') );
    }

    function users_analytics_widget_view()
    {
        $users = new DashAnalytics('users');
        $users->users_by_day();
    }
    
    function sessions_analytics_widget()
    {
        wp_add_dashboard_widget( 'sessions_analytics_widget', 'Sessions', array($this, 'sessions_analytics_widget_view') );
    }

    function sessions_analytics_widget_view()
    {
        $sessions = new DashAnalytics('sessions');
        $sessions->sessions_by_day();
    }
    
    function bouncerate_analytics_widget()
    {
        wp_add_dashboard_widget( 'bouncerate_analytics_widget', 'Bounce Rate', array($this, 'bouncerate_analytics_widget_view') );
    }

    function bouncerate_analytics_widget_view()
    {
        $bouncerate = new DashAnalytics('bouncerate');
        $bouncerate->bouncerate_by_day();
    }
    
    function sessionDuration_analytics_widget()
    {
        wp_add_dashboard_widget( 'sessionDuration_analytics_widget', 'Session Duration', array($this, 'sessionDuration_analytics_widget_view') );
    }

    function sessionDuration_analytics_widget_view()
    {
        $sessionDuration = new DashAnalytics('sessionDuration');
        $sessionDuration->sessionDuration_by_day();
    }
}
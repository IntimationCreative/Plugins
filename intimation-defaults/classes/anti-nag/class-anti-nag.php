<?php

/**
 * Anti Nag
 */

class AntiNag
{
    private static $instance;

    public function __construct()
    {
        // constructor
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
}


// Admin menu hook
add_action( 'admin_menu', 'remove_core_update_nag', 2 );

/**
 * Remove the original update nag
 */
function remove_core_update_nag() {
    remove_action( 'admin_notices', 'update_nag', 3 );
    remove_action( 'network_admin_notices', 'update_nag', 3 );
}

// Admin notice hook
add_action( 'admin_notices', 'custom_update_nag', 99 );
add_action( 'network_admin_notices', 'custom_update_nag', 99 );


/**
 * Custom update nag
 */
function custom_update_nag() {
    if ( is_multisite() && !current_user_can('update_core') )
        return false;

    global $pagenow;

    if ( 'update-core.php' == $pagenow )
        return;

    $cur = get_preferred_from_update_core();

    if ( ! isset( $cur->response ) || $cur->response != 'upgrade' )
        return false;

    $user = wp_get_current_user();

    $nicename = $user->user_nicename;


    if ( current_user_can('update_core') && $nicename != 'intimation' ) {
        $msg = sprintf( __('<a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> is available! <a href="%2$s">Contact Intimation to Update</a>.'), $cur->current, 'https://intimation.uk/wordpress-maintenance-plans/' );
        echo "<div class='update-nag'>$msg</div>";
    } else {
        $msg = sprintf( __('<a href="http://codex.wordpress.org/Version_%1$s">WordPress %1$s</a> is available! <a href="%2$s">Contact Intimation to Update</a>.'), $cur->current, 'https://intimation.uk/wordpress-maintenance-plans/' );
        echo "<div class='update-nag'>$msg</div>";
    }
}


function remove_items()
{
    $user = wp_get_current_user();
    $nicename = $user->user_nicename;
    if ( $nicename != 'intimation' ) {
        add_action( 'wp_before_admin_bar_render', 'remove_top_menu_items' );
        add_action('admin_menu', 'remove_menu_items', 999);
        add_action( 'admin_bar_menu', 'modify_admin_bar' );
    }
}

add_action( 'init', 'remove_items' );


/**
 * Modify the admin bar object
 *
 * @param [type] $wp_admin_bar
 * @return void
 */
function modify_admin_bar( $wp_admin_bar ){
    $myaccount = $wp_admin_bar->get_node('my-account');
    $myaccount->title = str_replace('Howdy', 'Hey', $myaccount->title) ;
    $wp_admin_bar->remove_node('my-account');
    $wp_admin_bar->add_node($myaccount);
}


/**
 * Remove menu items
 */
function remove_top_menu_items()
{
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wp-logo');          // Remove the WordPress logo
    $wp_admin_bar->remove_menu('about');            // Remove the about WordPress link
    $wp_admin_bar->remove_menu('wporg');            // Remove the WordPress.org link
    $wp_admin_bar->remove_menu('documentation');    // Remove the WordPress documentation link
    $wp_admin_bar->remove_menu('support-forums');   // Remove the support forums link
    $wp_admin_bar->remove_menu('feedback');         // Remove the feedback link
    $wp_admin_bar->remove_menu('updates');          // Remove the updates link
    $wp_admin_bar->remove_menu('comments');         // Remove the comments link
}

function remove_menu_items () {
    global $menu;

    $show_items = array(
        __('Dashboard'),
        __('Posts'),
        __('Media'),
        __('Pages')
    );

    foreach ($menu as $key => $value) {
        if ( ! in_array( $value[0] != NULL ? $value[0] : "", $show_items ) ) {
            unset($menu[$key]);
        }
    }

    // additional pages to remove
    remove_submenu_page( 'index.php', 'update-core.php' ); // Dashboard update
}


/*
function sample_admin_notice__error() {
	$class = 'notice notice-error';
	$message = __( 'Irks! An error has occurred.', 'sample-text-domain' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}
add_action( 'admin_notices', 'sample_admin_notice__error' );

function sample_admin_notice__success() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p><?php _e( 'Done!', 'sample-text-domain' ); ?></p>
    </div>
    <?php
}
add_action( 'admin_notices', 'sample_admin_notice__success' );
*/
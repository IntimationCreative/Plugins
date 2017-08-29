<?php

/**
 * User Capabilities
 */

class UserCaps
{
    private static $instance;

    public function __construct()
    {
        register_activation_hook( IDEF_ABS_FOLDER_FILE, array($this, 'add_user_role') );
        register_deactivation_hook( IDEF_ABS_FOLDER_FILE, array($this, 'remove_user_role') );
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
     * Add User Level
     * A custom user level
     *
     * @return void
     */
    public static function add_user_role()
    {
        add_role('web_admin',
		    'Web Admin',
            array(
                'read' => true,
                'edit_posts' => false,
                'delete_posts' => false,
                'publish_posts' => false,
                'upload_files' => false,
                'editor' => true,
                'moderate_comments' => true,
                'manage_categories' => true,
                'manage_links' => true,
                'unfiltered_html' => true,
                'edit_others_posts' => true,
                'edit_published_posts' => true,
                'edit_pages' => true,
                'level_7' => true,
                'level_6' => true,
                'level_5' => true,
                'level_4' => true,
                'level_3' => true,
                'level_2' => true,
                'level_1' => true,
                'level_0' => true
			)
		);
    }


    public static function remove_user_role()
    {
        remove_role( 'web_admin' );
    }
}
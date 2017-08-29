<?php

/**
 * Main Post Status Archive Class
 */

class PS_Post_Status_Archive
{

    private static $instance = null;

    private $directory;

    protected $post_type;

    function __construct()
    {

        $this->set_directory_value();
        $this->ps_set_post_type();


        // Activation
        register_activation_hook( PSA_PLUGIN_BASENAME, array($this, 'psa_archive_posts_on_activation') );
        register_deactivation_hook( PSA_PLUGIN_BASENAME, array($this, 'psa_archive_posts_on_deactivation') );

        add_action( 'init', array($this, 'ps_register_custom_post_status') );
        add_action( 'post_submitbox_misc_actions', array($this, 'ps_add_to_post_status_list') );
        add_filter( 'display_post_states', array($this, 'ps_display_archive_states') );
        add_filter( 'manage' . $this->post_type . '_posts_columns', array($this, 'ps_add_archive_button_col') );
        add_action( 'manage' . $this->post_type . '_posts_custom_column', array($this, 'ps_button_col_content') );

        add_action( 'manage_posts_extra_tablenav', array($this, 'ps_add_archive_all') );

        // load admin scripts and styles
        add_action( 'admin_enqueue_scripts', array($this, 'load_admin_script_styles') );

        // AJAX
        add_action( 'wp_ajax_nopriv_ps_archive_post', array($this, 'ps_archive_post') );
        add_action( 'wp_ajax_ps_archive_post', array($this, 'ps_archive_post') );
        add_action( 'wp_ajax_nopriv_ps_archive_all_posts', array($this, 'ps_archive_all_posts') );
        add_action( 'wp_ajax_ps_archive_all_posts', array($this, 'ps_archive_all_posts') );

        // CRON
        add_filter( 'cron_schedules', array($this, 'psa_cron_schedule') );
        add_action( 'psa_cron_do_archive_posts', array( $this, 'ps_cron_archive_posts' ) );

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
        $this->directory = plugins_url() . '/post-status-archive';
    }

    /**
	 * registers the post status
     */
    public function ps_register_custom_post_status()
    {
        $args = array(
            'label' => _x( 'Archive', 'post' ),
            'public' => true,
            'internal' => true,
            'exclude_from_search' => true,
            'show_in_admin_all_list'    => true,
		    'show_in_admin_status_list' => true,
            'label_count' => _n_noop( 'Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>' )
        );
        
        register_post_status( 'Archive', $args );
    }


    
    /**
     * Set up which posts should be affected
     */
    function ps_set_post_type()
    {
        $this->post_type = '_' . 'qcards';
    }


    /**
	 * add the post status to the admin dropdown
     */
    public function ps_add_to_post_status_list()
    {
        global $post;

        if ($post->post_status == 'archive') : 
            $text = 'Archived';
            
            ?>
            <script>
            jQuery(document).ready(function($){
                $("#post-status-display").text("<?php echo $text; ?>");
                $('#save-post').attr('value', 'Save Archive');

                $("#post_status").on('change', function()
                {   
                    var text = $(this).val();

                    console.log(text);
                    if (text == 'Archive') {

                        $('.save-post-status, #save-post').on("click", function(){
                            $('#save-post').attr('value', 'Save Archive');
                        });

                        
                    }
                });
            });
            </script>
            <?php

        endif;

        ?>
        <script>
        jQuery(document).ready(function($){
            $("select#post_status").append("<option value=\"Archive\" <?php selected('archive', $post->post_status); ?>>Archive</option>");
        });
        </script>
        <?php

    }

    /**
     * Add the text of the created post status to the post list
     */

    public function ps_display_archive_states( $states )
    {
        global $post;
        $arg = get_query_var( 'post_status' );

        if ( $arg != 'archive') 
        {
            if ( $post->post_status == 'archive' ) 
            {
                return array('Archive');
            }
        }
    }

    
    /** 
     * Add Column Headings in Posts 
     */
    
    function ps_add_archive_button_col($columns) 
    {
        $columns['archived'] = 'Archive?';
        return $columns;
    }

    /**
     * Add an archive all button
     */
    
    function ps_add_archive_all()
    {
        if ( $this->post_type == '_qcards' ) {
            echo '<button class="button archive-all">Archive All</button>';
        }
    }

    /**
    * Add content for the column - manage_posts_custom_column
    */
    
    function ps_button_col_content( $column_name, $post_id = '' ) 
    {
        $post = get_post( $post_id );
        $time = current_time('timestamp', true);

        $postdate = strtotime( str_replace( ',', ' ', get_the_date($post_id) ) );
        $lastmonth = strtotime('-2 month', $time);

        $datetime=new DateTime();
        $datetime->setTimestamp($postdate);
        $datetime->modify('+1 month');

        if ( 'archived' == $column_name ) {
            $output = $post_id;

            if ( $postdate < $lastmonth )
            {
                echo $button = '<button class="archive button fa" data-id="' . $post->ID . '">Archive</button>';

            } else
            {
                echo $over_a_month = "Can be archived on: <br>" . $datetime->format("Y-m-d"); //2015-03-18;
            }
        }
        
    }

    /**
     * Sets all posts than can be archived
     */

    function ps_archive_all_posts()
    {
        global $wpdb;

        // Security check.
        check_ajax_referer( 'post_status_archive_all', 'nonce' );

        $post_id = stripslashes( $_POST['post_id'] );

        $posts = $wpdb->get_results( 
            "
            SELECT ID, post_title, post_date 
            FROM $wpdb->posts
            WHERE post_status = 'publish' 
            AND post_type = 'qcards'
            "
        );

        // check the post dates and then archive them
        $posts_to_archive = array();

        foreach ( $posts as $post ) {
            
            $time = current_time('timestamp', true);
            
            // $postdate = strtotime( str_replace( ',', ' ', get_the_date($post->ID) ) );
            $postdate = strtotime( $post->post_date );
            $lastmonth = strtotime('-2 month', $time);

             if ( $postdate < $lastmonth )
            {
                // echo $button = '<button class="archive button fa" data-id="' . $post->ID . '">Archive</button>';
                // Archive posts
                $posts_to_archive[] = $post->ID;

            } else
            {   
                $posts_not_archive[] = $post->ID;
                // echo $over_a_month = "Can be archived on: <br>" . $datetime->format("Y-m-d"); //2015-03-18;
            }
        }

        foreach ($posts_to_archive as $post) {
        
            $table = $wpdb->posts;
            $data = array( 'post_status' => 'archive' );
            $where = array( 'ID' => $post, 'post_status' => 'publish' );

            $results = $wpdb->update( $table, $data, $where );

            if ( $results > 0 )
            {
                // update was successful
                $result += 'Post ' + $post + ' was archived!';
            } else {
                // update was not successful
                $result = 'Post ' + $post + ' was NOT archived!';
            }
            
        }

        wp_send_json_success( $result );

        exit;

    }

    /**
     * Set a post to be archived
     */

    function ps_archive_post()
    {
        global $wpdb;

        // Security check.
        check_ajax_referer( 'post_status_archive', 'nonce' );

        $post_id = stripslashes( $_POST['post_id'] );

        $table = $wpdb->posts;
        $data = array( 'post_status' => 'archive' );
        $where = array( 'ID' => $post_id, 'post_status' => 'publish' );

        $results = $wpdb->update( $table, $data, $where );

        if ( $results > 0 )
        {
            // update was successful
            $result = 'Post was archived!';
            wp_send_json_success( $result );
        } else {
            // update was not successful
            $result = 'Post was NOT archived!';
            wp_send_json_success( $result );
        }

        exit;

    }


    /**
     * Add a cron shedule for every day
     */
    function psa_cron_schedule( $schedules )
    {
        $schedules['every_day'] = array(
            'interval' => 100 * 24 * 60 * 60,
            // 'interval' => 60,
            'display'  => __( 'Every 1 day', 'post_status_archive' )
        );
        return $schedules;
    }


    /**
     * register the cron job on activation
    */
    function psa_archive_posts_on_activation()
    {   
        //echo PSA_PLUGIN_BASENAME;
        
        $timestamp = wp_next_scheduled( 'psa_cron_do_archive_posts' );

        if ( $timestamp == false ) {
            wp_schedule_event( time(), 'every_day', 'psa_cron_do_archive_posts' );
        }
    }


    /**
     * Sets all posts than can be archived via cron
     */

    function ps_cron_archive_posts()
    {
        global $wpdb;

        // add_option('PSA_CRON_RAN2'); // used for testing

        $posts = $wpdb->get_results( 
            "
            SELECT ID, post_title, post_date 
            FROM $wpdb->posts
            WHERE post_status = 'publish' 
            AND post_type = 'qcards'
            "
        );

        // check the post dates and then archive them
        $posts_to_archive = array();

        foreach ( $posts as $post ) {
            
            $time = current_time('timestamp', true);
            
            $postdate = strtotime( $post->post_date );
            $lastmonth = strtotime('-2 month', $time);

             if ( $postdate < $lastmonth )
            {
            
                $table = $wpdb->posts;
                $data = array( 'post_status' => 'archive' );
                $where = array( 'ID' => $post->ID, 'post_status' => 'publish' );

                $results = $wpdb->update( $table, $data, $where );

            } 
        }
    }


    /**
     * unregister the cron job on activation
    */
    function psa_archive_posts_on_deactivation(){
        wp_clear_scheduled_hook( 'psa_cron_do_archive_posts' );
    }


    /**
     * enqueue admin script
     */

    function load_admin_script_styles()
    {   
        wp_enqueue_style(
            'post-status-archive',
            $this->directory . '/css/post-status-archive.css'
        );

        wp_enqueue_script( 
            'post-status-archive', 
            $this->directory . '/js/post-status-archive.js', 
            array('jquery') 
        );

        $data = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'post_status_archive' )
        );
        wp_localize_script( 'post-status-archive', 'post_status_archive', $data );

        $data = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'post_status_archive_all' )
        );
        wp_localize_script( 'post-status-archive', 'post_status_archive_all', $data );
    }

}

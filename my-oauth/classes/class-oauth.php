<?php 

/**
 * Oauth Class
 * 
 * @package Wordpress
 * @subpackage Gecko
 * @version 1.0
 * @author Paul Spence
 */

Class Oauth {

    private static $instance;
	private $directory;

	protected $oauth_consumer_key;
	protected $oauth_consumer_secret;
	protected $oauth_access_token;
	protected $oauth_access_secret;

	protected $oauth_verifier;
	public $oauth_params;
	public $oauth_header = '';

	public $site_url;
	public $callback_url;

	
	/**
	 * Set up the initial properties from the provided settings
	 */
	public function __construct( $settings )
	{
		$this->set_directory_value();
		
		if (is_admin() && isset($_GET['page']) == oauth )
		{
			add_action( 'admin_enqueue_scripts', array($this, 'add_oauth_scripts'));
		}
		
		// AJAX ACTIONS
		add_action( 'wp_ajax_nopriv_request', array($this, 'request') );
		add_action( 'wp_ajax_request', array($this, 'request') );

		add_action( 'wp_ajax_nopriv_access', array($this, 'access') );
		add_action( 'wp_ajax_access', array($this, 'access') );

		add_action( 'wp_ajax_nopriv_request_add', array($this, 'request_add') );
		add_action( 'wp_ajax_request_add', array($this, 'request_add') );

		$admin_settings = new OauthSettings();
		$settings = $admin_settings->get_admin_settings();
		
		// setup of the class vars
		$this->oauth_consumer_key = $settings['oauth_consumer_key'];
		$this->oauth_consumer_secret = $settings['oauth_consumer_secret'];
		$this->site_url = $settings['site_url'];
		$this->callback_url = $settings['callback_url'];

		$params = array(
			'oauth_consumer_key'     => $this->oauth_consumer_key,
			'oauth_nonce'            => md5(time().rand()),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_timestamp'        => current_time('timestamp', true),
			'oauth_version'          => '1.0'
		);

		$this->oauth_params = $params;

		if ( isset( $_GET['oauth_verifier'] ) ) {
			print_r($_GET);
			$this->setVerifier();
		}

		// $woo = new WooAuth();
	}


    /**
     * Instance loader
     * @since 1.0
     * @param string $file current file
     * @param string $version current version number
     * @return object $instance singleton instance of the class
     */
    public static function instance( $file = '', $version = '1.0')
    {
        if ( is_null(self::$instance) ) {
            self::$instance = new self( $file, $version );
        }

        return self::$instance;
    }


	/**
	 * Set up the Oauth Params
	 */
	public function addParams( $key, $val )
	{
		$params = $this->oauth_params;

		if ( !empty($key) && !empty($val) ) 
		{
			$params[$key] = $val;
		}

		ksort( $params );

		$this->oauth_params = $params;
	}


	/**
	 * get the set Oauth Params
	 */
	public function getParams()
	{
		return $this->oauth_params;
	}


	/**
	 * Set the header for a GET / POST request
	 */
	public function setHeader( $var )
	{

		$header = 'OAuth ';

		$params = $this->oauth_params;

		foreach ($params as $key => $value) {
			$oauth_params[] = $key . '=' . '"' . $value . '"';
		}

		ksort( $oauth_params );

		$header = $header . implode(', ', $oauth_params);

		$this->oauth_header = $header;

	}
	

	/**
	 * Get the stored header for a GET / POST request
	 */
	public function getHeader()
	{
		return $this->oauth_header;
	}


	/**
	 * Build the request args
	 */
	public function buildRequestArgs($args = '')
	{
		
		$params = $this->oauth_params;

		foreach ($params as $key => $value) {
			$body[$key] = $value;
		}

		if ( !empty($args) ) {
			foreach ($args as $key => $value) {
				$body[$key] = $value;
			}
		}

		ksort( $body );

		$args = array(
			'headers' => array( 
				'Authorization: ' => $this->getHeader()
			),
			'body' => $body,
			'sslverify' => false
		);

		return $args;

	}


	/**
	 * create a signature base string from the oauth params array
	 * @return a URL encoded string
	 */

	public function build_signature_base_string( $request_url, $method, $oauth_params ) {

		$string_params = array();

		ksort( $oauth_params );

		foreach ( $oauth_params as $key => $value ) {
			// convert oauth parameters to key-value pair
			$string_params[] = "$key=$value";
		}

		return "$method&" . rawurlencode( $request_url ) . '&' . rawurlencode( implode( '&', $string_params ) );
	}


	/**
	 * generate the oauth signature
	 * combine consumer and token secret keys using & to a use as the hmac key
	 * @return $oauth_signature
	 */

	public function generate_oauth_signature( $data, $consumer_secret, $token_secret = '' ) {

		$hash_hmac_key = $consumer_secret . '&' . $token_secret;

		// echo "KEY USED: " . $hash_hmac_key . '<br>';

		$oauth_signature = base64_encode( hash_hmac( 'sha1', $data, $hash_hmac_key, true ) );

		return $oauth_signature;
	}

	/**
	 * Build all of the parts needed to make an authenticated 
	 * GET/POST request
	 * @param string $site_url
	 * @param string $request_type - define if the request will be
	 * 		  either request, authorize or access
	 * @param string $method - 'POST' or 'GET'
	 * @return $response
	 */

	/**
	 * request access
	 */

	public function request()
	{
		// Security check.
        // check_ajax_referer( 'oauth_auth', 'nonce' );

		$data = array();

		$test = 'Request Ran!';

		$req_url = $this->site_url . '/oauth1/request';
		$base_string = $this->build_signature_base_string( $req_url, 'POST', $this->oauth_params );
		$oauth_signature = $this->generate_oauth_signature( $base_string, $this->oauth_consumer_secret );

		$this->addParams('oauth_signature', $oauth_signature);
		$this->setHeader();
		$args = $this->buildRequestArgs();

		$req_response = wp_remote_post( esc_url_raw( $req_url ), $args );
		$req_response = wp_remote_retrieve_body( $req_response );


		$this->setToken($req_response);

		$data['test'] = $test;
		$data['req_url'] = $req_url;
		$data['base_string'] = $base_string;
		$data['oauth_signature'] = $oauth_signature;
		$data['args'] = $args;
		$data['req_response'] = $req_response;

		wp_send_json_success( $data );
		exit;
	}

	public function authorize()
	{
		$this->setHeader();
		$args = $this->buildRequestArgs();
		$auth_url = $this->site_url . '/oauth1/authorize'. '?oauth_token=' . $this->oauth_access_token . '&oauth_callback=' . rawurlencode($this->callback_url);

		$auth_response = wp_remote_post( esc_url_raw( $auth_url ), $args );
		$auth_response = wp_remote_retrieve_body( $auth_response );

		// print_r( $auth_response );	
	}

	/**
	 * Build the access request
	 */

	public function access()
	{
		$data = array();

		// $test = "ACCESS RAN!";
		// set the url
		$url = $this->site_url . '/oauth1/access';

		$options = get_option( 'oauth_display_options' );
		$this->oauth_verifier = $options['oauth_verifier'];
		
		$token = $options['oauth_token'];
		$this->oauth_access_token = $options['oauth_token'];

		// $params = $this->oauth_params;
		$this->addParams('oauth_token', $this->oauth_access_token);
		$this->addParams('oauth_verifier', $this->oauth_verifier);

		// retreve the secret from DB
		$this->oauth_access_secret = get_option( 'oauth1_access_' . $this->oauth_access_token, null );
		// rebuild base string
		$base_string = $this->build_signature_base_string( $url, 'POST', $this->oauth_params );
		// regen oauth sig with oauth access secret
		$oauth_signature = $this->generate_oauth_signature( $base_string, $this->oauth_consumer_secret, $this->oauth_access_secret );
		
		$this->addParams('oauth_signature', $oauth_signature);
		
		// rebuild the header
		// rebuild args
		$this->setHeader();
		$args = $this->buildRequestArgs();

		// set up the access url
		$url = $url . '?oauth_verifier=' . $this->oauth_verifier;

		$response = wp_remote_post( esc_url_raw( $url ), $args );
		$response = wp_remote_retrieve_body( $response );

		// Access should now be complete
		$auth = true;
		$oauth_params = $this->setToken($response, $auth);

		// $data['test'] = $test;
		// $data['url'] = $url;
		$data['oauth_verifier'] = $this->oauth_verifier;
		$data['token'] = $token;

		$data['params'] = $this->oauth_params;

		$data['access_secret'] = $this->oauth_access_secret;
		$data['consumer_secret'] = $this->oauth_consumer_secret;
		$data['base_string'] = $base_string;
		$data['oauth_signature'] = $oauth_signature;
		$data['args'] = $args;
		$data['response'] = $response;
		$data['oauth_params'] = $oauth_params;

		wp_send_json_success( $data );
		exit;
	}


	/**
	 * Make a request to add
	 */
	public function request_add()
	{
		$data = array();
		// set up the access url
		$url = $this->site_url . '/wp-json/wp/v2/posts';

		// get and set the token and secret
		$auth = get_option( 'oauth1_authorized', null );
		$token = $auth['token'];
		$secret = $auth['secret'];

		// add the content for the request to the params
		$args = array(
			'title' => 'Test Post From Satellite', 
			'content' => 'This is some test content.');

		$this->addParams('title', rawurlencode($args['title']) );
		$this->addParams('content', rawurlencode($args['content']) );
		$this->addParams('oauth_token', $token);

		// rebuild base string
		$base_string = $this->build_signature_base_string( $url, 'POST', $this->oauth_params );

		// regen oauth sig with oauth access secret
		$oauth_signature = $this->generate_oauth_signature( $base_string, $this->oauth_consumer_secret, $secret );
		$this->addParams('oauth_signature', $oauth_signature);
		
		// rebuild the header and the request with our args passed
		$this->setHeader();
		$args = $this->buildRequestArgs( $args );

		// make the request
		$response = wp_remote_post( esc_url_raw( $url ), $args );
		$response = wp_remote_retrieve_body( $response );

		$data['url'] = $url;
		$data['auth'] = $auth;
		$data['token'] = $token;
		$data['secret'] = $secret;
		$data['args'] = $args;
		$data['base_string'] = $base_string;
		$data['oauth_signature'] = $oauth_signature;
		$data['response'] = $response;

		wp_send_json_success( $data );
		exit;
	}

	public function requestUpdate($post_id, $content = '')
	{
		// set up the access url
		$url = $this->site_url . 'wp-json/wp/v2/posts/' . $post_id;

		// get and set the token and secret
		$auth = get_option( 'oauth1_authorized', null );
		$token = $auth['token'];
		$secret = $auth['secret'];

		// add the content for the request to the params
		$args = array(
			'title' => 'Test Post From Satellite - UPDATED', 
			'content' => 'This is some updated test content.');

		$this->addParams('title', rawurlencode($args['title']) );
		$this->addParams('content', rawurlencode($args['content']) );
		$this->addParams('oauth_token', $token);

		// rebuild base string
		$base_string = $this->build_signature_base_string( $url, 'POST', $this->oauth_params );

		// regen oauth sig with oauth access secret
		$oauth_signature = $this->generate_oauth_signature( $base_string, $this->oauth_consumer_secret, $secret );
		$this->addParams('oauth_signature', $oauth_signature);
		
		// rebuild the header and the request with our args passed
		$this->setHeader();
		$args = $this->buildRequestArgs( $args );

		// make the request
		$response = wp_remote_post( esc_url_raw( $url ), $args );
		$response = wp_remote_retrieve_body( $response );

		// print the response
		print_r($response);
	}

	public function requestDelete($post_id)
	{
		// set up the access url
		$url = $this->site_url . 'wp-json/wp/v2/posts/' . $post_id;

		// get and set the token and secret
		$auth = get_option( 'oauth1_authorized', null );
		$token = $auth['token'];
		$secret = $auth['secret'];

		// add the content for the request to the params
		$this->addParams('oauth_token', $token);

		// rebuild base string
		$base_string = $this->build_signature_base_string( $url, 'DELETE', $this->oauth_params );

		// regen oauth sig with oauth access secret
		$oauth_signature = $this->generate_oauth_signature( $base_string, $this->oauth_consumer_secret, $secret );
		$this->addParams('oauth_signature', $oauth_signature);
		
		// rebuild the header and the request with our args passed
		$this->setHeader();
		$args = $this->buildRequestArgs();

		$args['method'] = 'DELETE';

		// make the request
		$response = wp_remote_request( esc_url_raw( $url ), $args );
		$response = wp_remote_retrieve_body( $response );

		// print the response
		print_r($response);
	}


	/**
	 * set the token and secret
	 */
	public function setToken( $response, $authorized = false )
	{
		
		$token_fields = explode('&', $response);

		// print_r($token_fields);

		foreach ($token_fields as $field) {
			
			$field = explode('=', $field);



			if ($field[0] == 'oauth_token') 
			{	
				$this->addParams($field[0], $field[1]);
				$this->oauth_access_token = $field[1];
			} 
			elseif ($field[0] == 'oauth_token_secret') 
			{
				$this->oauth_access_secret = $field[1];
			}
		}

		// Store the token and secret to db
		if ( ! $authorized ) {
			add_option( 'oauth1_access_' . $this->oauth_access_token, $this->oauth_access_secret, null, 'no' );
			
			$options = get_option( 'oauth_display_options' );
			$options['oauth_token'] = $this->oauth_access_token;
			$options['oauth_secret'] = $this->oauth_access_secret;
			update_option( 'oauth_display_options', $options );
		} else {
			$token = array(
				'token' => $this->oauth_access_token,
				'secret' => $this->oauth_access_secret);
			// add_option( 'oauth1_authorized_' . $this->oauth_access_token, $this->oauth_access_secret, null, 'no' );
			add_option( 'oauth1_authorized', $token, null, 'no' );

			$options = get_option( 'oauth_display_options' );
			$options['oauth_token'] = $this->oauth_access_token;
			$options['oauth_secret'] = $this->oauth_access_secret;
			update_option( 'oauth_display_options', $options );
		}
		
	}

	public function getToken()
	{
		return $this->oauth_access_token;
	}

	/**
	 * set the verifier
	 */
	public function setVerifier()
	{
		$this->oauth_verifier = $_GET['oauth_verifier'];
		$this->addParams('oauth_verifier', $this->oauth_verifier);

		$this->oauth_access_token = $_GET['oauth_token'];
		$this->addParams('oauth_token', $this->oauth_access_token);

		$options = get_option( 'oauth_display_options' );
		// $options['oauth_token'] = $this->oauth_access_token;
		$options['oauth_verifier'] = $this->oauth_verifier;
		update_option( 'oauth_display_options', $options );
	}

	
	/**
     * scripts and styles
     * @since 1.0
     * Load all the scripts and styles
     **/
    public function add_oauth_scripts()
    {
        // enqueue the script
        wp_enqueue_script( 
            'oauth-js', 
            $this->directory . '/assets/js/oauth-scripts.js', 
            array('jquery') 
        );

        wp_enqueue_style( 
            'oauth-css', 
            $this->directory . '/assets/css/oauth-styles.css' 
        );

        // data array to pass to localize scripts
        $data = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'oauth_auth' )
        );
        wp_localize_script( 'oauth-js', 'oauth', $data );
    }

	/**
     * Set the directory
     */
    public function set_directory_value(){
        $this->directory = plugins_url() . '/' . O_BASE_FOLDER;
    }
	

}

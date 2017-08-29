<?php 

/**
 * WooAuth Class
 * 
 * @package Wordpress
 * @subpackage Gecko
 * @version 1.0
 * @author Paul Spence
 */

Class WooAuth extends Oauth {


    public $woo_consumer_key = 'ck_b72571692a3bb448d3ada4eea61471da894fc208';
    public $woo_consumer_secret = 'cs_ccd625cecd93a76538c50be4f1acf6fd2490f095';

    /**
	 * Set up the initial properties from the provided settings
	 */
	public function __construct()
	{
        $params = $this->getParams();
        var_dump($params);
		// AJAX ACTIONS
		add_action( 'wp_ajax_nopriv_products', array($this, 'products') );
		add_action( 'wp_ajax_products', array($this, 'products') );
	}
    
    /**
     * Request GET
     */
    public function products()
    {
        $data = array();

        // $url = $this->site_url . '/wc-api/v3/products';
        $url = 'http://icl3.co.uk/wc-api/v3/products';

        // get and set the token and secret
		$auth = get_option( 'oauth1_authorized', null );
		$token = $auth['token'];
		$secret = $auth['secret'];

		// $this->addParams('oauth_token', $token);
		$this->addParams('oauth_consumer_key', $this->woo_consumer_key);

		// rebuild base string
		$base_string = $this->build_signature_base_string( $url, 'GET', $this->oauth_params );
        // GET&http%3A%2F%2Ficl3.co.uk%2Fwc-api%2Fv3%2Fproducts&oauth_consumer_key%3Dck_b72571692a3bb448d3ada4eea61471da894fc208

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
		// $data['auth'] = $auth;
		// $data['token'] = $token;
		// $data['secret'] = $secret;
		$data['args'] = $args;
		$data['base_string'] = $base_string;
		// $data['oauth_signature'] = $oauth_signature;
		$data['response'] = $response;

		wp_send_json_success( $data );
		exit;
    }
    
}

new WooAuth();
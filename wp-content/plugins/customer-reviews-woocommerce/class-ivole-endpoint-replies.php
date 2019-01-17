<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once('firebase/src/JWT.php');
use \ivole\Firebase\JWT\JWT;

if ( ! class_exists( 'Ivole_Endpoint_Replies' ) ) :

	class Ivole_Endpoint_Replies {
	  public function __construct() {
			add_action( 'rest_api_init', array( $this, 'init_endpoint' ) );
	  }

		public function init_endpoint( ) {
			$this->register_routes();
		}

		public function register_routes() {
	    $version = '1';
	    $namespace = 'ivole/v' . $version;
	    register_rest_route( $namespace, '/review-reply', array(
	      array(
	        'methods'         => 'POST, PATCH, DELETE',
	        'callback'        => array( $this, 'manage_replies' ),
	        'permission_callback' => array( $this, 'manage_replies_permissions_check' ),
	        'args'            => array(),
	      ),
	    ) );
	  }

		public function manage_replies( $request ) {
			return new WP_REST_Response( 'Generic error', 500 );
		}

		public function manage_replies_permissions_check( WP_REST_Request $request ) {
			return false;
			$body = $request->get_body();
			$body2 = json_decode( $body );
			if( json_last_error() === JSON_ERROR_NONE ) {
				if( isset( $body2->message ) ) {
					$key = "your-256-bit-secret";
					try {
						$decoded = JWT::decode($body2->message, $key, array('HS256'));
					} catch( Exception $e ) {
						//error_log( print_r( $e->getMessage(), true ) );
					}
					//error_log( print_r( $decoded, true ) );
				}
			}
			return false;
		}
	}

endif;

?>

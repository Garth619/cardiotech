<?php
if ( ! defined( 'ABSPATH' ) ){
    exit;
}

class Porto_Importer_API {

    protected $demo = '';
    protected $code = '';

    protected $path_tmp = '';
    protected $path_demo = '';

    protected $url = array(
        'changelog' => 'https://www.portotheme.com/activation/porto_wp/download/changelog.php',
        'theme_version' => 'https://www.portotheme.com/activation/porto_wp/download/theme_version.php',
        'theme' => 'https://www.portotheme.com/activation/porto_wp/download/theme.php',
        'plugins_version' => 'https://www.portotheme.com/activation/porto_wp/download/plugins_version.php',
        'plugins' => 'https://www.portotheme.com/activation/porto_wp/download/plugins.php',
        'demos' => 'https://www.portotheme.com/activation/porto_wp/download/demos.php',
    );

    public function __construct( $demo = false ){
        if ( $demo ) {
            $this->demo = $demo;
            $upload_dir = wp_upload_dir();
            $this->path_tmp = wp_normalize_path( $upload_dir['basedir'] . '/porto_tmp_dir' );
            $this->mkdir();
        }
        $this->code = Porto()->get_purchase_code();
    }

    public function get_url( $id ) {
        return $this->url[$id];
    }
    
    /**
     * Create directories
     */
    protected function mkdir(){

        if( ! file_exists( $this->path_tmp ) ){
            wp_mkdir_p( $this->path_tmp );
        }
        
        $this->path_demo = wp_normalize_path( $this->path_tmp .'/'. $this->demo );
        if( ! file_exists( $this->path_demo ) ){
            wp_mkdir_p( $this->path_demo );
        }
    }
    
    /**
     * Delete temporary directory
     */
    public function delete_temp_dir(){

        // filesystem
        global $wp_filesystem;
        // Initialize the Wordpress filesystem, no more using file_put_contents function
        if ( empty( $wp_filesystem ) ) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }
        
        // directory is located outside wp uploads dir
        $upload_dir = wp_upload_dir();
        if ( false === strpos( str_replace( '\\', '/', $this->path_demo ), str_replace( '\\', '/', $upload_dir['basedir'] ) ) ) {
            return false;
        }
        
        $wp_filesystem->delete( $this->path_demo, true );
    }

    /**
     * Get response
     */
    public function get_response( $target, $args = array() ){

        if ( ! $args ) {
            $args = array(
                'user-agent'    => 'WordPress/'. get_bloginfo( 'version' ) .'; '. network_site_url(),
                'timeout'       => 30,
            );
        }

        $response = wp_remote_get( $this->get_url( $target ), $args );

        if( is_wp_error( $response ) ){
            return $response;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if( isset( $data['error'] ) ){
            return new WP_Error( 'invalid_response', $data['error'] );
        }

        return $data;
    }

    public function generate_args( $ish = true ) {
        preg_match("/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/", parse_url(site_url(), PHP_URL_HOST), $_domain_tld);
        if ( isset( $_domain_tld[0] ) ) {
            $domain = $_domain_tld[0];
        } else {
            $domain = parse_url(site_url(), PHP_URL_HOST);
        }
        $args = array(
            'code' => $this->code,
            'domain' => $domain
        );
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );
        if ( in_array( $_SERVER['REMOTE_ADDR'], $whitelist ) ) {
            $args['local'] = 'true';
        }
        if ( $ish && Porto()->is_envato_hosted() ) {
            $args[ 'ish' ] = Porto()->get_ish();
        }
        return $args;
    }

    /**
     * Get remote demo files
     */
    public function get_remote_demo( $target = 'demos' ) {

        $path_unzip = wp_normalize_path( $this->path_demo .'/'. $this->demo );
        if ( is_dir( $path_unzip ) ) {
            return $path_unzip;
        }

        $url = $this->url[$target];
        $args = $this->generate_args();
        $args['demo'] = $this->demo;

        $url = add_query_arg( $args, $url );

        $args = array(
            'user-agent'    => 'WordPress/'. get_bloginfo( 'version' ) .'; '. network_site_url(),
            'timeout'       => 60,
        );

        $response = wp_remote_get( $url, $args );

        if( is_wp_error( $response ) ){
            return $response;
        }
        $body = wp_remote_retrieve_body( $response );

        // remote get fallback
        if( empty( $body ) ) {
            if( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ){
                $body = @file_get_contents( $url );
            }
        }

        if( empty( $body ) ){
            return new WP_Error( 'error_download', __( 'The package could not be downloaded.', 'porto' ) );
        }

        if( $json = json_decode( $body, true ) ){
            if( isset( $json['error'] ) ){
                return new WP_Error( 'invalid_response', $json['error'] );
            }
        }

        // filesystem
        global $wp_filesystem;
        // Initialize the Wordpress filesystem, no more using file_put_contents function
        if ( empty( $wp_filesystem ) ) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        $path_package = wp_normalize_path( $this->path_demo .'/'. $this->demo .'.zip' );

        if( ! $wp_filesystem->put_contents( $path_package, $body, FS_CHMOD_FILE ) ){
            // put_contents fallback
            @unlink( $path_package );
            $fp = @fopen( $path_package, 'w' );
            $fwrite = @fwrite( $fp, $body );
            @fclose( $fp );
            if( false === $fwrite ){
                return new WP_Error( 'error_fs', __( 'WordPress filesystem error.', 'porto' ) );
            }
            
        }

        $unzip = unzip_file( $path_package, $this->path_demo );
        if( is_wp_error( $unzip ) ){
            return new WP_Error( 'error_unzip', __( 'The package could not be unziped.', 'porto' ) );
        }

        if( ! is_dir( $path_unzip ) ) {
            return new WP_Error( 'error_folder', sprintf( __( 'Demo data directory does not exist (%s).', 'porto' ), $path_unzip ) );
        }

        return $path_unzip;
    }

    /**
     * Get remote theme version
     */
    public function get_latest_theme_version() {
        $response = $this->get_response( 'theme_version' );
        if( is_wp_error( $response ) ){
            return false;
        }
        if( empty( $response['version'] ) ){
            return false;
        }
        return $response['version'];
    }
}

<?php
/**
 * Theme Core Functions
 *
 * @package Porto
 */

require_once( porto_functions . '/general.php' );
require_once( porto_functions . '/shortcodes.php' );
require_once( porto_functions . '/widgets.php' );
require_once( porto_functions . '/post.php' );
if ( class_exists( 'WooCommerce' ) ) {
    require_once(porto_functions . '/woocommerce.php') ;
}

require_once( porto_functions . '/layout.php' );
require_once( porto_functions . '/html_block.php' );

require_once( porto_functions . '/class-dynamic-style.php' );

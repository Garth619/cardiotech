<?php
/**
 * Content wrappers
 * @version     3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$extra_class = '';
global $porto_layout, $porto_product_layout;
if ( $porto_layout === 'widewidth' && ( !is_product() || !isset( $porto_product_layout ) || 'full_width' !== $porto_product_layout ) ) {
    $extra_class .= ' m-b-xl';
    if ( porto_get_wrapper_type() !== 'boxed' ) {
        $extra_class .= ' m-r-md m-l-md';
    }
}
?>
<div id="primary" class="content-area"><main id="content" class="site-main<?php echo $extra_class; ?>" role="main">

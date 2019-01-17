<?php
/**
 * The template for displaying product widget entries
 *
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product, $porto_settings;

if ( ! is_a( $product, 'WC_Product' ) ) {
    return;
}

?>

<li>
    <?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<a class="product-image" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>">
		<?php porto_widget_product_thumbnail(); ?>
	</a>

    <div class="product-details">
        <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_name() ); ?>">
            <?php echo esc_html( $product->get_name() ); ?>
        </a>

        <?php if ( ! empty( $show_rating ) || $porto_settings['woo-show-rating'] ) : ?>
            <?php echo porto_get_rating_html( $product ); ?>
        <?php endif; ?>
        <?php echo wp_kses_post( $product->get_price_html() ); ?>
    </div>

    <?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
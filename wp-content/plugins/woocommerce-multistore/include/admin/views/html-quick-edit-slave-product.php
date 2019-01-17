<?php
/**
 * Admin View: Quick Edit Slave Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<fieldset id="woocommerce-multistore-slave-fields" class="inline-edit-col">

    <input type="hidden" name="product_blog_id" value="" />';

    <h4>&nbsp;</h4>
	<p class="form-field _woonet_description inline">
		<span class="description"><?php _e( 'Child product, can\'t be re-published to other sites', 'woonet' ); ?></span>
	</p>
	<h4>&nbsp;</h4>
	<p class="form-field no_label _woonet_child_inherit_updates inline">
		<input type="checkbox" class="_woonet_child_inherit_updates inline" name="_woonet_child_inherit_updates" id="_woonet_child_inherit_updates" value="yes" checked="checked" />
		<span class="description"><?php _e( 'If checked, this product will inherit any parent updates', 'woonet' ); ?></span>
	</p>
	<p class="form-field no_label _woonet_child_stock_synchronize inline">
		<input type="checkbox" class="_woonet_child_stock_synchronize inline" name="_woonet_child_stock_synchronize" id="_woonet_child_stock_synchronize" value="yes" checked="checked" />
		<span class="description"><?php _e( 'If checked, any stock change will syncronize across product tree.', 'woonet' ); ?></span>
	</p>

	<input type="hidden" name="woocommerce_multisite_quick_edit" value="1" />
	<input type="hidden" name="woocommerce_multisite_quick_edit_nonce" value="<?php echo wp_create_nonce( 'woocommerce_multisite_quick_edit_nonce' ); ?>" />

</fieldset>

<?php
/**
 * Admin View: Quick Edit Product
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $WOO_MSTORE;

$options  = $WOO_MSTORE->functions->get_options();
$blog_ids = $WOO_MSTORE->functions->get_active_woocommerce_blog_ids();

?>

<table style="display: none" data-blog-id="<?php echo get_current_blog_id(); ?>">
	<tbody id="more-inlineedit">
	<tr id="bulk-distribution" class="inline-edit-row inline-edit-row-post inline-edit-product" style="display: none">
		<td>
			<legend class="inline-edit-legend">Bulk Distribution</legend>
			<p>
				<strong>
					<?php _e( 'You will be able to Bulk Distribute only the products that are parent products of this store.', 'woonet' ) ?>
				</strong>
			</p>
			<div id="bulk-title-div">
				<div id="bulk-titles"></div>
			</div>
			<fieldset id="woocommerce-multistore-fields" class="inline-edit-col">

				<h4><?php _e( 'Multisite - Publish to', 'woonet' ); ?></h4>

				<div class="inline-edit-col">
					<p class="form-field no_label woonet_toggle_all_sites inline">
						<input class="woonet_toggle_all_sites inline" name="woonet_toggle_all_sites"
							   id="woonet_toggle_all_sites" value="yes" type="checkbox">
						<b><span class="description">Toggle all Sites</span></b>
					</p>
					<div class="woonet_sites">
						<?php
							$current_blog_id = get_current_blog_id();
							foreach ( $blog_ids as $blog_id ) {
								if ( ! is_network_admin() && $current_blog_id == $blog_id ) {
									continue;
								}

								switch_to_blog( $blog_id );

								echo '<p class="form-field no_label _woonet_publish_to inline" data-group-id="' . $blog_id . '">';

								echo '<label class="alignleft">';
								printf(
									'<input type="checkbox" value="yes" name="_woonet_publish_to_%1$d" class="_woonet_publish_to" data-blog-id="%1$d" />',
									$blog_id
								);
								printf(
									'<span class="checkbox-title">%s</span>',
									get_bloginfo( 'name' )
								);
								echo '</label><br class="clear">';

								echo '<label class="alignleft pl">';
								printf(
									'<input type="checkbox" value="yes" name="_woonet_publish_to_%1$d_child_inheir" data-blog-id="%1$s" />',
									$blog_id
								);
								printf(
									'<span class="checkbox-title">%s</span>',
									__( 'Child product inherit Parent changes', 'woonet' )
								);
								echo '</label><br class="clear">';

								echo '<label class="alignleft pl">';
								printf(
									'<input type="checkbox" value="yes" name="_woonet_%1$d_child_stock_synchronize" %2$s data-blog-id="%1$s" />',
									$blog_id,
									'yes' == $options['synchronize-stock'] ? 'disabled="disabled"' : ''
								);
								printf(
									'<span class="checkbox-title">%s</span>',
									__( 'If checked, any stock change will syncronize across product tree.', 'woonet' )
								);
								echo '</label><br class="clear">';

								echo '</p>';

								restore_current_blog();
							}
						?>
					</div>

				</div>

				<input type="hidden" name="woocommerce_multisite_bulk_distribution_edit_nonce"
					   value="<?php echo wp_create_nonce( 'woocommerce_multisite_bulk_distribution_edit_nonce' ); ?>"/>

				<p class="submit inline-edit-save">
					<button type="button" class="button cancel alignleft"><?php _e( 'Cancel', 'woonet' ); ?></button>
					<button type="button"
							class="button button-primary save alignright"><?php _e( 'Update', 'woonet' ); ?></button>

					<span class="spinner"></span>
					<span class="progress">Progress: <span></span></span>

					<span class="error" style="display:none"></span>
					<br class="clear"/>
				</p>

			</fieldset>
		</td>
	</tr>
	</tbody>
</table>
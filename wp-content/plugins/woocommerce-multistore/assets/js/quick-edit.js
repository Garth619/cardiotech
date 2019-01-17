/*global ajaxurl, inlineEditPost, inlineEditL10n */
jQuery( function( $ ) {
	$( '#the-list' ).on( 'click', '.editinline', function() {
		inlineEditPost.revert();

		const post_id = $(this).closest('tr').attr('id').replace('post-', ''),
			  $wm_inline_data = $('#woocommerce_multistore_inline_' + post_id);

		$( 'div', $wm_inline_data ).each( function( index, element ) {
			const name  = $(element).attr('class'),
				  value = $(element).text();

			if ( '_is_master_product' === name ) {
				$( 'input[name="_is_master_product"]', '.inline-edit-row' ).val( value );
			} else if ( 'master_blog_id' === name ) {
				$( 'input[name="master_blog_id"]', '.inline-edit-row' ).val( value );
				$( 'p[data-group-id="' + value + '"]' ).hide();
			} else if ( 'product_blog_id' === name ) {
				$( 'input[name="product_blog_id"]', '.inline-edit-row' ).val( value );
			} else {
				$( 'input[name="' + name + '"]', '.inline-edit-row' ).prop( 'checked', 'yes' === value );
			}
		});
	});

	$('.inline-edit-row #woonet_toggle_all_sites').change(function () {
		const checked = $(this).is(":checked");

		$('.inline-edit-row .woonet_sites input[type="checkbox"]._woonet_publish_to').each(function () {
			if (jQuery(this).prop('disabled') === false) {
				jQuery(this).attr('checked', checked);
				jQuery(this).trigger('change');
			}
		});
	});

	$('.ptitle').on( 'focus', function(e) {
		const row = $(this).closest('tr.inline-editor');

		if ( 'yes' === $( 'input[name="_is_master_product"]', row ).val() ) {
			$("#woocommerce-multistore-slave-fields", row ).remove();
		} else {
			$("#woocommerce-multistore-fields", row ).remove();
		}

		$('input[name$="_child_stock_synchronize"]', row ).prop('disabled', 'yes' === woonet_options['synchronize-stock']);
	} );
});

<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Scheduled Review Reminders', IVOLE_TEXT_DOMAIN ); ?></h1>
    <hr class="wp-header-end">
    <p><?php esc_attr_e( 'A list of review reminders scheduled by the plugin to be sent in the future.', IVOLE_TEXT_DOMAIN ); ?></p>

    <?php $list_table->views(); ?>

    <form id="reminders-form" method="get">
        <input type="hidden" name="page" value="ivole-reviews-reminders" />
        <input type="hidden" name="_total" value="<?php echo esc_attr( $list_table->get_pagination_arg( 'total_items' ) ); ?>" />
        <input type="hidden" name="_per_page" value="<?php echo esc_attr( $list_table->get_pagination_arg( 'per_page' ) ); ?>" />
        <input type="hidden" name="_page" value="<?php echo esc_attr( $list_table->get_pagination_arg( 'page' ) ); ?>" />

        <?php if ( isset( $_REQUEST['paged'] ) ) { ?>
            <input type="hidden" name="paged" value="<?php echo esc_attr( absint( $_REQUEST['paged'] ) ); ?>" />
        <?php } ?>

        <?php $list_table->display(); ?>
	</form>

	<script type="text/javascript">
		jQuery(function($) {
			var ivole_confirm_cancel = function() {
				return confirm("<?php esc_attr_e( 'Are you sure you want to cancel the review reminder(s)?', IVOLE_TEXT_DOMAIN ); ?>");
			};

			var ivole_confirm_send = function() {
				return confirm("<?php esc_attr_e( 'Are you sure you want to send the review reminder(s) now?', IVOLE_TEXT_DOMAIN ); ?>");
			};

			$('.action-link.cancel').on('click', function(event) {
				if (!ivole_confirm_cancel()) {
					event.preventDefault();
				}
			});

			$('.action-link.send').on('click', function(event) {
				if (!ivole_confirm_send()) {
					event.preventDefault();
				}
			});

			$('#reminders-form').on('submit', function(event) {
				var action = $('#bulk-action-selector-top').val();
				var selected = $('input.reminder-checkbox:checked');

				if (selected.length) {
					if (action == 'cancel') {
						if (!ivole_confirm_cancel()) {
							event.preventDefault();
						}
					} else if (action == 'send') {
						if (!ivole_confirm_send()) {
							event.preventDefault();
						}
					}
				}
			});
		});
	</script>
</div>

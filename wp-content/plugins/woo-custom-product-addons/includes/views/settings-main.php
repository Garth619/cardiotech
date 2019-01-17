<div class="wrap">
    <div id="icon-options-general" class="icon32"></div>
    <h1><?php echo WCPA_PLUGIN_NAME; ?></h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">
                        <div class="inside">
                            <form method="post" action="">
                                <?php wp_nonce_field('wcpa_save_settings', 'wcpa_nonce'); ?>
                                <table>

                                    <tr valign="top">
                                        <th scope="row" width="30%">
                                            <label for="myplugin_option_name">
                                                <?php _e('Add to cart button text', 'woo-custom-product-addons'); ?> <br>
                                                <small><?php _e('Add to cart button text in archive/product listing page in case product has additional fields', 'woo-custom-product-addons'); ?> </small>
                                            </label>
                                        </th>
                                        <td><input type="text"

                                                   name="add_to_cart_text"
                                                   value="<?php echo wcpa_get_option('add_to_cart_text', 'Select options'); ?>" /></td>
                                    </tr>


                                    <tr valign="top">
                                        <th scope="row">
                                            <label>
                                                <?php _e('Display custom fields data in ', 'woo-custom-product-addons'); ?> 

                                            </label>
                                        </th>

                                        <td>
                                            <label>
                                                <input type="checkbox"
                                                       name="wcpa_show_meta_in_cart"
                                                       value="1"  <?php checked(wcpa_get_option('show_meta_in_cart')); ?> />
                                                       <?php _e('Show in cart', 'woo-custom-product-addons'); ?> 
                                            </label>
                                            <label>
                                                <input type="checkbox"
                                                       name="wcpa_show_meta_in_checkout"
                                                       value="1"  <?php checked(wcpa_get_option('show_meta_in_checkout')); ?> />
                                                       <?php _e('Show in Checkout', 'woo-custom-product-addons'); ?> 
                                            </label>
                                            <label>
                                                <input type="checkbox"
                                                       name="wcpa_show_meta_in_order"
                                                       value="1"<?php checked(wcpa_get_option('show_meta_in_order')); ?> />
                                                       <?php _e('Show in Order', 'woo-custom-product-addons'); ?> 
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            <label>
                                                <?php _e('Load form in recency order', 'woo-custom-product-addons'); ?> 

                                            </label>
                                        </th>
                                        <td>
                                            <input type="checkbox" name="form_loading_order_by_date" id="form_loading_order_by_date"
                                                   value="1" <?php checked(wcpa_get_option('form_loading_order_by_date', false)); ?>> 
                                            <label for="form_loading_order_by_date"> 
                                                <?php _e('If a product has assigned multiple forms, it will be loaded based on form created date', 'woo-custom-product-addons'); ?> </label>
                                        </td>
                                    </tr>
                                </table>
                                <?php submit_button(null, 'primary', 'wcpa_save_settings'); ?>

                            </form>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables .ui-sortable -->

            </div>
            <!-- post-body-content -->


            <!-- #postbox-container-1 .postbox-container -->

        </div>
        <!-- #post-body .metabox-holder .columns-2 -->
        <div id="post-body" class="metabox-holder columns-2">

        </div>

        <br class="clear">
    </div>
    <!-- #poststuff -->

</div> <!-- .wrap -->
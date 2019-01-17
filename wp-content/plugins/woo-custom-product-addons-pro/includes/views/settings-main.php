<div class="wrap wcpa_settings">
    <div id="icon-options-general" class="icon32"></div>
    <h1><?php echo WCPA_PLUGIN_NAME; ?></h1>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="postbox">

                        <div class="inside ">
                            <form method="post" action="">
                                <?php wp_nonce_field('wcpa_save_settings', 'wcpa_nonce'); ?>
                                <ul class="wcpa_g_set_tabs">
                                    <li> <a href="#wcpa_disp_settings"><?php _e('Display Settings', 'wcpa-text-domain'); ?></a> </li>
                                    <!-- <li> <a href="#wcpa_price_settings">Price Settings</a> </li> -->
                                    <li> <a href="#wcpa_content_settings"><?php _e('Contents/Strings', 'wcpa-text-domain'); ?></a> </li>
                                    <li> <a href="#wcpa_other_settings"><?php _e('Other', 'wcpa-text-domain'); ?></a> </li>
                                    <li> <a href="#wcpa_import_import"><?php _e('Import/Export', 'wcpa-text-domain'); ?></a> </li>
                                </ul>
                                <div class="wcpa_g_set_tabcontents clearfix">
                                    <div id="wcpa_disp_settings" class="wcpa_tabcontent">

                                        <div class="options_group">
                                            <h3><?php _e('Price', 'wcpa-text-domain') ?></h3>
                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="disp_show_field_price" id="disp_show_field_price"
                                                           value="1"  <?php checked(wcpa_get_option('disp_show_field_price', true)); ?>> 
                                                    <label for="disp_show_field_price"><?php _e('Show price against each feilds', 'wcpa-text-domain'); ?>
                                                    </label>
                                                </li>
                                            </ul>
                                            <h3><?php _e('Price Summary Section', 'wcpa-text-domain') ?></h3> 
                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="disp_summ_show_total_price" id="disp_summ_show_total_price"
                                                           value="1" <?php checked(wcpa_get_option('disp_summ_show_total_price', true)); ?>> 
                                                    <label for="disp_summ_show_total_price">
                                                        <?php _e('Show Total', 'wcpa-text-domain') ?> </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="disp_summ_show_product_price" id="disp_summ_show_product_price"
                                                           value="1" <?php checked(wcpa_get_option('disp_summ_show_product_price', true)); ?>> 
                                                    <label for="disp_summ_show_product_price">
                                                        <?php _e('Show Product price', 'wcpa-text-domain') ?> </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="disp_summ_show_option_price"  id="disp_summ_show_option_price" 
                                                           value="1" <?php checked(wcpa_get_option('disp_summ_show_option_price', true)); ?>> 
                                                    <label for="disp_summ_show_option_price" >
                                                        <?php _e('Show Options Price', 'wcpa-text-domain') ?> 
                                                    </label>
                                                </li>
                                            </ul>
                                            <h3> <?php _e('Custom options data', 'wcpa-text-domain') ?> </h3>
                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="show_meta_in_cart" id="show_meta_in_cart"
                                                           value="1" <?php checked(wcpa_get_option('show_meta_in_cart', true)); ?>> 
                                                    <label for="show_meta_in_cart"> <?php _e('Show in cart', 'wcpa-text-domain'); ?>  </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="show_meta_in_checkout" id="show_meta_in_checkout"
                                                           value="1" <?php checked(wcpa_get_option('show_meta_in_checkout', true)); ?>> 
                                                    <label for="show_meta_in_checkout"> 
                                                        <?php _e('Show in Checkout', 'wcpa-text-domain'); ?>  </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="show_meta_in_order" id="show_meta_in_order"
                                                           value="1" <?php checked(wcpa_get_option('show_meta_in_order', true)); ?>> 
                                                    <label for="show_meta_in_order"> 
                                                        <?php _e('Show in Order', 'wcpa-text-domain'); ?>  </label>
                                                </li>
                                            </ul>
                                            <h3> <?php _e('Show or Hide Price in', 'wcpa-text-domain') ?> </h3>
                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="show_price_in_cart" id="show_price_in_cart"
                                                           value="1" <?php checked(wcpa_get_option('show_price_in_cart', true)); ?>> 
                                                    <label for="show_price_in_cart"> <?php _e('Show in cart', 'wcpa-text-domain'); ?>  </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="show_price_in_checkout" id="show_price_in_checkout"
                                                           value="1" <?php checked(wcpa_get_option('show_price_in_checkout', true)); ?>> 
                                                    <label for="show_price_in_checkout"> 
                                                        <?php _e('Show in Checkout', 'wcpa-text-domain'); ?>  </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="show_price_in_order" id="show_price_in_order"
                                                           value="1" <?php checked(wcpa_get_option('show_price_in_order', true)); ?>> 
                                                    <label for="show_price_in_order"> 
                                                        <?php _e('Show in Order', 'wcpa-text-domain'); ?>  </label>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="show_price_in_order_meta" id="show_price_in_order_meta"
                                                           value="1" <?php checked(wcpa_get_option('show_price_in_order_meta', true)); ?>> 
                                                    <label for="show_price_in_order_meta"> 
                                                        <?php _e('Add in Order Meta( Price will be saved along with order meta, Third party plugins will be using this data)', 'wcpa-text-domain'); ?>  </label>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div id="wcpa_content_settings" class="wcpa_tabcontent" style="display: none">
                                        <div class="options_group">
                                            <h3><?php _e('Price summary section labels', 'wcpa-text-domain') ?></h3> 
                                            <ul>
                                                <li>

                                                    <label for="options_total_label" > <?php
                                                        _e('Options Price Label:', 'wcpa-text-domain');
                                                        ?></label>
                                                    <input type="text" name="options_total_label"  id="options_total_label" 
                                                           value="<?php echo wcpa_get_option('options_total_label', 'Options Price'); ?>" > 
                                                </li>
                                                <li>

                                                    <label for="options_product_label" > <?php
                                                        _e('Product Price Label:', 'wcpa-text-domain');
                                                        ?></label>
                                                    <input type="text" name="options_product_label"  id="options_product_label" 
                                                           value="<?php echo wcpa_get_option('options_product_label', 'Product Price'); ?>"  > 
                                                </li>
                                                <li>

                                                    <label for="total_label" ><?php
                                                        _e('Total Label:', 'wcpa-text-domain');
                                                        ?> </label>
                                                    <input type="text" name="total_label"  id="total_label" 
                                                           value="<?php echo wcpa_get_option('total_label', 'Total'); ?>" > 
                                                </li>
                                                <li>
                                                    <label for="fee_label" ><?php
                                                        _e('Fee Label:', 'wcpa-text-domain');
                                                        ?> </label>
                                                    <input type="text" name="fee_label"  id="fee_label" 
                                                           value="<?php echo wcpa_get_option('fee_label', 'Fee'); ?>" > 
                                                </li>

                                            </ul>
                                        </div>
                                        <div class="options_group">

                                            <ul>
                                                <li>

                                                    <label for="add_to_cart_text">
                                                        <?php _e('Add to cart button text', 'wcpa-text-domain'); ?> <br>
                                                        <small><?php _e('Add to cart button text in archive/product listing page in case product has additional fields', 'wcpa-text-domain'); ?> </small>
                                                    </label>
                                                    <input type="text" name="add_to_cart_text"  id="add_to_cart_text" 
                                                           value="<?php echo wcpa_get_option('add_to_cart_text', 'Select options'); ?>" > 
                                                </li>
                                                <li>

                                                    <label for="price_prefix_label">
                                                        <?php _e('Product Price prefix', 'wcpa-text-domain'); ?> <br>
                                                        <small><?php _e('Set a prefix text before the price in archive and product page. Leave blank if no prefix needed. eg: \'Starting at\' ', 'wcpa-text-domain'); ?> </small>
                                                    </label>
                                                    <input type="text" name="price_prefix_label"  id="price_prefix_label" 
                                                           value="<?php echo wcpa_get_option('price_prefix_label', ''); ?>" > 
                                                </li>




                                            </ul>
                                        </div>
                                    </div>

                                    <div id="wcpa_other_settings" class="wcpa_tabcontent" style="display: none">
                                        <div class="options_group">

                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="form_loading_order_by_date" id="form_loading_order_by_date"
                                                           value="1" <?php checked(wcpa_get_option('form_loading_order_by_date', false)); ?>> 
                                                    <label for="form_loading_order_by_date"> 
                                                        <?php _e('Load form in recency order', 'wcpa-text-domain'); ?> <br>
                                                        <small><?php _e('If a product has assigned multiple forms, it will be loaded based on form created order', 'wcpa-text-domain'); ?> </small></label>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="options_group">

                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="hide_empty_data" id="hide_empty_data"
                                                           value="1" <?php checked(wcpa_get_option('hide_empty_data', false)); ?>> 
                                                    <label for="hide_empty_data"> 
                                                        <?php _e('Hide empty fields in cart', 'wcpa-text-domain'); ?> <br>
                                                        <small><?php _e('Hide empty fields in cart, checkout and order', 'wcpa-text-domain'); ?> </small></label>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="options_group">

                                            <ul>
                                                <!--                                                <li>
                                                                                                    <input type="checkbox" name="enable_recaptcha" id="enable_recaptcha"
                                                                                                           value="1" <?php checked(wcpa_get_option('enable_recaptcha', false)); ?>> 
                                                                                                    <label for="enable_recaptcha"> 
                                                <?php _e('Enable reCAPTCHA for all WCPA forms', 'wcpa-text-domain'); ?> <br>
                                                                                                        <small><?php _e('You can also enable captcha for individual fields only by editing the forms', 'wcpa-text-domain'); ?> </small></label>
                                                                                                </li>-->
                                                <li>

                                                    <label for="recaptcha_site_key" > <?php
                                                        _e('reCAPTCHA site key:', 'wcpa-text-domain');
                                                        ?></label>
                                                    <input type="text" name="recaptcha_site_key"  id="recaptcha_site_key" 
                                                           value="<?php echo wcpa_get_option('recaptcha_site_key', ''); ?>" > 
                                                    <a href="https://www.google.com/recaptcha/admin" target="_blank">How to get Keys</a>
                                                </li>
                                                <li>

                                                    <label for="recaptcha_secret_key" > <?php
                                                        _e('reCAPTCHA Secret key:', 'wcpa-text-domain');
                                                        ?></label>
                                                    <input type="text" name="recaptcha_secret_key"  id="recaptcha_secret_key" 
                                                           value="<?php echo wcpa_get_option('recaptcha_secret_key', ''); ?>" > 
                                                    <a href="https://www.google.com/recaptcha/admin" target="_blank">How to get Keys</a>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="options_group">
                                            <ul>
                                                <li>

                                                    <label for="google_map_api_key" > <?php
                                                        _e('Google Map API key:', 'wcpa-text-domain');
                                                        ?></label>
                                                    <input type="text" name="google_map_api_key"  id="options_total_label" 
                                                           value="<?php echo wcpa_get_option('google_map_api_key', ''); ?>" > 
                                                    <a href="https://developers.google.com/maps/documentation/embed/get-api-key" target="_blank">How to get API key</a><br>
                                                    Don't forget to restrict the API key by site domains.
                                                </li>

                                            </ul>
                                        </div>

                                    </div>
                                    <div id="wcpa_import_import" class="wcpa_tabcontent" style="display: none">
                                        <div class="options_group">
                                            <ul>
                                                <li>
                                                    <div>
                                                        <p><h3><?php _e('This can be used to import single product form', 'wcpa-text-domain'); ?></h3></p>
                                                        <label><?php _e('Input the exported data here and press <strong>Import From</strong>', 'wcpa-text-domain'); ?></label>
                                                        <textarea rows="5" id="wcpa_import_form_data" style="width:80%"></textarea>
                                                        <?php wp_nonce_field('wcpa_form_import_nonce', 'wcpa_form_import_nonce'); ?>
                                                    </div>
                                                    <button class="button-secondary" id="wcpa_import_form"><?php
                                                        _e('Import Form', 'wcpa-text-domain');
                                                        ?></button>
                                                </li>
                                                <li>
                                                    <div>
                                                        <p><h3><?php _e('Export All Forms', 'wcpa-text-domain'); ?></h3></p>

                                                        <a href="<?php echo admin_url('export.php?download=true&content=' . WCPA_POST_TYPE . '&submit=Download+Export+File'); ?>" class="button-secondary"><?php
                                                            _e('Export Form', 'wcpa-text-domain');
                                                            ?></a>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div>
                                                        <p><h3><?php _e('Import All Forms', 'wcpa-text-domain'); ?></h3></p>
                                                        <p><?php _e('You can import the xml file using Wordpress default post import option at <a href="' . admin_url('import.php') . '">Tools&#187;Import</a>', 'wcpa-text-domain'); ?></p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>    <div style="clear: both">

                                </div>
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
            <?php
            $license = get_option('wcpa_activation_license_key');
            $status = get_option('wcpa_activation_license_status');
            ?>
            <!-- main content -->
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable" id="wcpa-license">
                    <div class="postbox">
                        <div class="inside">
                            <form method="post" action="options.php">

                                <?php settings_fields('wcpa_license'); ?>

                                <table class="form-table">
                                    <tbody>
                                        <tr valign="top">
                                            <th scope="row" valign="top">
                                                <?php _e('License Key'); ?>
                                            </th>
                                            <td>
                                                <input id="edd_sample_license_key" name="wcpa_activation_license_key" type="text" class="regular-text" value="<?php esc_attr_e($license); ?>" />
                                                <label class="description" for="wcpa_activation_license_key"><?php _e('Enter your license key'); ?></label>
                                            </td>
                                        </tr>
                                        <?php if (false !== $license) { ?>
                                            <tr valign="top">
                                                <th scope="row" valign="top">
                                                    <?php _e('Activate License'); ?>
                                                </th>
                                                <td>
                                                    <?php if ($status !== false && $status == 'valid') { ?>
                                                        <span style="color:green;"><?php _e('active'); ?></span>
                                                        <?php wp_nonce_field('wcpa_deactivate', 'wcpa_nounce'); ?>

                                                        <input type="submit" class="button-secondary" name="wcpa_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <?php wp_nonce_field('wcpa_activate', 'wcpa_nounce'); ?>

                                                        <input type="submit" class="button-secondary" name="wcpa_license_activate" value="<?php _e('Activate License'); ?>"/>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                                <?php
                                if (false === $license) {
                                    submit_button();
                                }
                                ?>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br class="clear">
    </div>
    <!-- #poststuff -->

</div> <!-- .wrap -->
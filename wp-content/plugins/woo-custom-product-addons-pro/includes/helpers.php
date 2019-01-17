<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


if (!function_exists('wcpa_price')) {

    /**
     * @return string
     */
    function wcpa_price($price, $no_style = 0, $args = array()) {


        extract(array(
            'ex_tax_label' => false,
            'currency' => isset($args['currency']) ? $args['currency'] : '',
            'decimal_separator' => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimals' => wc_get_price_decimals(),
            'price_format' => get_woocommerce_price_format()));
        if ($decimal_separator) {
            $decimal_separator = trim($decimal_separator);
            $price = str_replace($decimal_separator, '.', $price);
        }
        //$unformatted_price = $price;
        $negative = $price < 0;
        $price = floatval($negative ? $price * -1 : $price);
//        
        $price = apply_filters('raw_woocommerce_price', floatval($negative ? $price * -1 : $price));

        $price = number_format($price, $decimals, $decimal_separator, $thousand_separator);


        $formatted_price = ( $negative ? '-' : '' ) . sprintf($price_format, '<span class="woocommerce-Price-currencySymbol">' .
                        get_woocommerce_currency_symbol($currency) . '</span>', '<span class="price_value">' . $price . '</span>');
        $return = '<span class="wcpa_price">' . $formatted_price . '</span>';
        if ($no_style) {
            $return = html_entity_decode(( $negative ? '-' : '' ) . sprintf($price_format, get_woocommerce_currency_symbol($currency), $price));
        }

        return $return;
    }

}
if (!function_exists('wcpa_get_price_shop')) {

    function wcpa_get_price_shop($product, $args = array()) {

        if (!is_array($args) && $args !== false) {
            $args = array(
                'qty' => 1,
                'price' => $args,
            );
        }
        if (!isset($args['qty']) || empty($args['qty'])) {
            $args['qty'] = 1;
        }
        if (!isset($args['price'])) {
            $args['price'] = $product->get_price();
        }
//        else {
//            $args['price'] = apply_filters('woocommerce_product_get_price', $args['price'], $product);
//        }



        $price = (float) $args['price'];
        $qty = (int) $args['qty'];

        return 'incl' === get_option('woocommerce_tax_display_shop') ?
                wc_get_price_including_tax($product, array(
                    'qty' => $qty,
                    'price' => $price,
                )) :
                wc_get_price_excluding_tax($product, array(
                    'qty' => $qty,
                    'price' => $price,
        ));
    }

}
if (!function_exists('wcpa_get_price_cart')) {

    function wcpa_get_price_cart($product, $args = array()) {
        if (!is_array($args) && $args !== false) {
            $args = array(
                'qty' => 1,
                'price' => $args,
            );
        }


        if (!isset($args['qty']) || empty($args['qty'])) {
            $args['qty'] = 1;
        }
        if (!isset($args['price'])) {
            $args['price'] = $product->get_price();
        }
//        else {
//            $args['price'] = apply_filters('woocommerce_product_get_price', $args['price'], $product);
//        }



        $price = (float) $args['price'];
        $qty = (int) $args['qty'];


        if (WC()->cart->display_prices_including_tax()) {
            $product_price = wc_get_price_including_tax($product, array(
                'qty' => $qty,
                'price' => $price,
            ));
        } else {
            $product_price = wc_get_price_excluding_tax($product, array(
                'qty' => $qty,
                'price' => $price
            ));
        }
        return $product_price;
    }

}
if (!function_exists('wcpa_empty')) {

    /**
     * @return string
     */
    function wcpa_empty($var) {
        if (is_array($var)) {
            return empty($var);
        } else {
            return ($var === null || $var === false || $var === '');
        }
    }

}
if (!function_exists('wcpa_iscolorLight')) {

    /**
     * @return string
     */
    function wcpa_colorClass($hex) {
        $hex = str_replace('#', '', $hex);
        $c_r = hexdec(substr($hex, 0, 2));
        $c_g = hexdec(substr($hex, 2, 2));
        $c_b = hexdec(substr($hex, 4, 2));
        $color = ((($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000);
        $class = '';
        if ($color > 235) {
            $class .= 'wcpa_clb_border '; // border needed
        }
        if ($color > 210) {
            $class .= 'wcpa_clb_nowhite '; // no white color
        }
        return $class;
    }

}


if (!function_exists('wcpa_get_option')) {

    /**
     * @return string
     */
    function wcpa_get_option($option, $default = false, $translate = false) {
        $settings = get_option(WCPA_SETTINGS_KEY);

        $settings = apply_filters('wcpa_configurations', $settings);
        $response = isset($settings[$option]) ? $settings[$option] : $default;
        if ($translate) {
            if (function_exists('pll__')) {
                return pll__($response);
            } else {
                return __($response, 'wcpa-text-domain');
            }
        }
        return $response;
    }

}
if (!function_exists('wcpa_get_post_meta')) {

    /**
     * @return string
     */
    function wcpa_get_post_meta($pos_id, $key, $default = false) {

        $settings = get_post_meta($pos_id, WCPA_META_SETTINGS_KEY, true);

        return isset($settings[$key]) ? $settings[$key] : $default;
    }

}
if (!function_exists('wcpa_export_form')) {

    /**
     * @return string
     */
    function wcpa_export_form($post_id) {
        $json_value = get_post_meta($post_id, WCPA_FORM_META_KEY, true);

        $export = [
            'form_json' => $json_value,
            'title' => get_the_title($post_id),
            'wcpa_settings' => get_post_meta($post_id, WCPA_META_SETTINGS_KEY, true),
            'other_settings' => [
                'wcpa_drct_prchsble' => get_post_meta($post_id, 'wcpa_drct_prchsble', true)
            ]
        ];

        return base64_encode(serialize($export));
    }

}
if (!function_exists('wcpa_style')) {

    /**
     * @return string
     */
    function wcpa_style($style = array(), $echo = false) {

        $o = '';
        foreach ($style as $k => $v) {
            $o .= $k . ':' . $v . ';';
        }

        if ($echo) {
            echo $o;
        } else {
            return $o;
        }
    }

}



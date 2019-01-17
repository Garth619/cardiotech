<?php

// Porto One Page Category Products
add_shortcode( 'porto_one_page_category_products', 'porto_shortcode_one_page_category_products' );
add_action( 'vc_after_init', 'porto_load_one_page_category_products_shortcode' );

function porto_shortcode_one_page_category_products( $atts, $content = null ) {
    ob_start();
    if ( $template = porto_shortcode_woo_template( 'porto_one_page_category_products' ) )
        include $template;
    return ob_get_clean();
}

function porto_load_one_page_category_products_shortcode() {
    $custom_class = porto_vc_custom_class();
    $order_by_values = porto_vc_woo_order_by();
    $order_way_values = porto_vc_woo_order_way();

    // woocommerce product categories
    vc_map(
        array(
            'name' => "Porto " . __( 'One Page Category', 'js_composer' ),
            'base' => 'porto_one_page_category_products',
            'icon' => 'porto_vc_woocommerce',
            'category' => __( 'WooCommerce', 'js_composer' ),
            'description' => __( 'Display one page navigation of product categories and products by category.', 'porto-shortcodes' ),
            'params' => array(
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Category Order by', 'js_composer' ),
                    'param_name' => 'category_orderby',
                    'value' => $order_by_values,
                    'description' => sprintf( __( 'Select how to sort categories. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Category Order way', 'js_composer' ),
                    'param_name' => 'category_order',
                    'value' => $order_way_values,
                    'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' )
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Hide empty categories', 'porto' ),
                    'param_name' => 'hide_empty',
                    'std' => 'yes',
                    'value' => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
                    'admin_label' => true
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Show Products', 'porto' ),
                    'description' => __( 'If you uncheck this option, only category lists will be displayed on the left side of the page and products will not be displayed. If you click category in the list, it will redirect to that page.', 'porto' ),
                    'param_name' => 'show_products',
                    'std' => 'yes',
                    'value' => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
                    'admin_label' => true
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Ajax load', 'porto' ),
                    'description' => __( 'Show category products one by one category using ajax infinite load when the page is scrolling to the bottom.', 'porto' ),
                    'param_name' => 'infinite_scroll',
                    'std' => '',
                    'value' => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
                    'dependency' => array( 'element' => 'show_products', 'not_empty' => true ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Products View mode', 'porto-shortcodes' ),
                    'param_name' => 'view',
                    'value' => array(
                        __( 'Carousel', 'porto-shortcodes' )  => 'products-slider',
                        __( 'Grid', 'porto-shortcodes' )=> 'grid',
                    ),
                    'dependency' => array( 'element' => 'show_products', 'not_empty' => true ),
                    'admin_label' => true,
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Count', 'js_composer' ),
                    'param_name' => 'count',
                    'description' => __( 'The number of products in a category.', 'js_composer' ),
                    'dependency' => array( 'element' => 'show_products', 'not_empty' => true ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Product Columns', 'porto-shortcodes' ),
                    'param_name' => 'columns',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider', 'grid' )),
                    'std' => '4',
                    'value' => porto_sh_commons('products_columns')
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Product Columns on mobile ( <= 575px )', 'porto-shortcodes' ),
                    'param_name' => 'columns_mobile',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider', 'grid' )),
                    'std' => '',
                    'value' => array(
                        __( 'Default', 'porto-shortcodes' ) => '',
                        '1' => '1',
                        '2' => '2',
                        '3' => '3'
                    )
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Product Column Width', 'porto-shortcodes' ),
                    'param_name' => 'column_width',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider', 'grid' )),
                    'value' => porto_sh_commons('products_column_width')
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Product Order by', 'js_composer' ),
                    'param_name' => 'product_orderby',
                    'value' => $order_by_values,
                    'description' => sprintf( __( 'Select how to sort retrieved products. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
                    'dependency' => array( 'element' => 'show_products', 'not_empty' => true ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Product Order way', 'js_composer' ),
                    'param_name' => 'product_order',
                    'value' => $order_way_values,
                    'description' => sprintf( __( 'Designates the ascending or descending order. More at %s.', 'js_composer' ), '<a href="http://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">WordPress codex page</a>' ),
                    'dependency' => array( 'element' => 'show_products', 'not_empty' => true ),
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __( 'Add Links Position', 'porto-shortcodes' ),
                    'description' => __('Select position of add to cart, add to wishlist, quickview.', 'porto-shortcodes'),
                    'param_name' => 'addlinks_pos',
                    'value' => porto_sh_commons('products_addlinks_pos'),
                    'dependency' => array( 'element' => 'show_products', 'not_empty' => true ),
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Show Slider Navigation', 'porto-shortcodes' ),
                    'param_name' => 'navigation',
                    'std' => 'yes',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider' )),
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __( 'Show Slider Pagination', 'porto-shortcodes' ),
                    'param_name' => 'pagination',
                    'std' => 'no',
                    'dependency' => Array('element' => 'view', 'value' => array( 'products-slider' )),
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
                ),
                $custom_class,
            )
        )
    );

    if (!class_exists('WPBakeryShortCode_Porto_One_Page_Category_Products')) {
        class WPBakeryShortCode_Porto_One_Page_Category_Products extends WPBakeryShortCode {
        }
    }
}
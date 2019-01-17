<?php
global $porto_settings;
$output = $title = $view = $columns = $column_width = $addlinks_pos = $orderby = $order = $ids = $pagination = $navigation = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract( shortcode_atts( array(
    'title' => '',
    'view' => 'grid',
    'columns' => 4,
    'columns_mobile' => '',
    'column_width' => '',
    'count' => '',
    'pagination_style' => '',
    'category_filter' => '',
    'orderby' => 'date',
    'order' => 'desc',
    'category' => '',
    'ids' => '',
    'addlinks_pos' => '',
    'navigation' => 1,
    'pagination' => 0,
    'animation_type' => '',
    'animation_duration' => 1000,
    'animation_delay' => 0,
    'el_class' => '',
    'className' => '',
    'status' => ''
), $atts ) );

$el_class = porto_shortcode_extract_class( $el_class );

if ( $className ) {
    if ( $el_class ) {
        $el_class = ' ' . $className;
    } else {
        $el_class = $className;
    }
}

$output = '<div class="porto-products wpb_content_element'. ( $category_filter ? ' show-category' : '' ) . ( $pagination_style ? ' archive-products' : '' ) . esc_attr( $el_class ) . '"';
if ($animation_type) {
    $output .= ' data-appear-animation="'.$animation_type.'"';
    if ($animation_delay)
        $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
    if ($animation_duration && $animation_duration != 1000)
        $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
}
$output .= '>';

if ( $category_filter || $pagination_style ) {
    $output .= '<form class="pagination-form d-none">';
    $output .= '<input type="hidden" name="count" value="'. esc_attr( $count ) .'" >';
    $output .= '<input type="hidden" name="original_orderby" value="'. esc_attr( $orderby ) .'" >';
    $output .= '<input type="hidden" name="orderby" value="'. esc_attr( $orderby ) .'" >';
    $output .= '<input type="hidden" name="category" value="" >';
    $output .= '<input type="hidden" name="ids" value="'. esc_attr( $ids ) .'" >';
    $output .= '<input type="hidden" name="columns" value="'. esc_attr( $columns ) .'" >';
    $output .= '<input type="hidden" name="pagination_style" value="'. esc_attr( $pagination_style ) .'" >';
    $output .= '</form>';
}

if ( $title ) {
    if ($view == 'products-slider')
        $output .= '<h2 class="slider-title"><span class="inline-title">'.$title.'</span><span class="line"></span></h2>';
    else
        $output .= '<h2 class="section-title">'.$title.'</h2>';
}

if ( $category_filter ) {
    $terms = get_terms( 'product_cat', array( 'hide_empty' => true ) );
    $category_html = '<h4 class="section-title">'. esc_html__( 'Sort By', 'porto' ) .'</h4>';
    $category_html .= '<ul class="product-categories">';
    $category_html .= '<li><a href="javascript:void(0)" data-sort_id="date">'. esc_html__( 'New Arrivals', 'porto' ) .'</a></li>';
    foreach ($terms as $term_cat) {
        if ( 'Uncategorized' == $term_cat->name ) {
            continue;
        }
        $id = $term_cat->term_id; 
        $name = $term_cat->name;
        $slug = $term_cat->slug;
        $category_html .= '<li><a href="'. esc_url( get_term_link( $id, 'product_cat' ) ) .'" data-cat_id="'. esc_attr( $id ) .'">'. esc_html( $name ) .'</a></li>';
    }
    $category_html .= '</ul>';
    $output .= '<div class="products-filter">';
        if ( apply_filters( 'porto_wooocommerce_products_shortcode_sticky_filter', true ) ) {
            $output .= '<div data-plugin-sticky data-plugin-options="{&quot;autoInit&quot;: true, &quot;minWidth&quot;: 991, &quot;containerSelector&quot;: &quot;.porto-products&quot;, &quot;autoFit&quot;:true, &quot;paddingOffsetBottom&quot;: 10}">';
        }
                $output .= apply_filters( 'porto_wooocommerce_products_shortcode_categories_html', $category_html );
        if ( apply_filters( 'porto_wooocommerce_products_shortcode_sticky_filter', true ) ) {
            $output .= '</div>';
        }
    $output .= '</div>';
}
if ($view == 'products-slider') {
    $pagination_style = '';
    $output .= '<div class="slider-wrapper">';
}

global $porto_woocommerce_loop;

$porto_woocommerce_loop['view'] = $view;
$porto_woocommerce_loop['columns'] = $columns;
if ( $columns_mobile ) {
    $porto_woocommerce_loop['columns_mobile'] = $columns_mobile;
}
$porto_woocommerce_loop['column_width'] = $column_width;
$porto_woocommerce_loop['pagination'] = $pagination;
$porto_woocommerce_loop['navigation'] = $navigation;
$porto_woocommerce_loop['addlinks_pos'] = $addlinks_pos;

$extra_atts = '';
if ( $category ) {
    $extra_atts .= ' category="'. esc_attr( $category ) .'"';
}
if ( $count ) {
    $extra_atts .= ' limit="'. esc_attr( $count ) . '"';
}
if ( $pagination_style ) {
    $extra_atts .= ' paginate="true"';
    $porto_settings_backup = $porto_settings['product-infinite'];
    $porto_settings['product-infinite'] = $pagination_style;
    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_pagination', 50 );
}

if ( $status == 'featured' ) {
    $extra_atts .= ' visibility="featured"';
} else if ( $status == 'on_sale' ) {
    $extra_atts .= ' on_sale="1"';
}
$output .= do_shortcode('[products columns="'.$columns.'" orderby="'.$orderby.'" order="'.$order.'" ids="'.$ids.'"'. $extra_atts .']');

if ( $pagination_style && isset( $porto_settings_backup ) ) {
    $porto_settings['product-infinite'] = $porto_settings_backup;
}
if ($view == 'products-slider')
    $output .= '</div>';

$output .= '</div>';

echo $output;
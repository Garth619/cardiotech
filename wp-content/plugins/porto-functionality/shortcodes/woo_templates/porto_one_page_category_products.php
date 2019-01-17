<?php

extract( shortcode_atts( array(
    'category_orderby' => 'ID',
    'category_order' => 'desc',
    'hide_empty' => 'yes',
    'show_products' => 'yes',
    'infinite_scroll' => '',
    'view' => 'products-slider',
    'count' => '',
    'columns' => 4,
    'columns_mobile' => '',
    'column_width' => '',
    'product_orderby' => 'date',
    'product_order' => 'desc',
    'addlinks_pos' => '',
    'navigation' => 1,
    'pagination' => 0,
    'el_class' => ''
), $atts ) );

$el_class = porto_shortcode_extract_class( $el_class );

$wrapper_classes = 'porto-onepage-category';
if ( $show_products ) {
    $wrapper_classes .= ' show-products';
}
if ( $el_class ) {
    $wrapper_classes .= ' ' . $el_class;
}
if ( $infinite_scroll ) {
    $wrapper_classes .= ' ajax-load';
}

$column_class = '';
switch ($columns) {
    case 1: $cols_md = 1; $cols_xs = 1; $cols_ls = 1; break;
    case 2: $cols_md = 2; $cols_xs = 2; $cols_ls = 1; break;
    case 3: $cols_md = 3; $cols_xs = 2; $cols_ls = 1; break;
    case 4: $cols_md = 3; $cols_xs = 2; $cols_ls = 1; break;
    case 5: $cols_md = 3; $cols_xs = 2; $cols_ls = 1; break;
    case 6: $cols_md = 5; $cols_xs = 3; $cols_ls = 2; break;
    case 7: $cols_md = 6; $cols_xs = 3; $cols_ls = 2; break;
    case 8: $cols_md = 6; $cols_xs = 3; $cols_ls = 2; break;
    default: $columns = 4; $cols_md = 3; $cols_xs = 2; $cols_ls = 1;
}
$subcategory_class = 'sub-category products pcols-lg-'. $columns .' pcols-md-'. $cols_md .' pcols-xs-'. $cols_xs .' pcols-ls-'. $cols_ls;

if ( $show_products ) {
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
}

$output = '';
$output .= '<div class="'. esc_attr( $wrapper_classes ) .'">';
$terms = get_terms( 'product_cat', array( 'parent' => 0, 'hide_empty' => ( 'yes' == $hide_empty ? true : false ), 'orderby' => $category_orderby, 'order' => $category_order ) );
    $output .= '<nav class="category-list">';
        $output .= '<ul class="product-cats" data-plugin-sticky data-plugin-options="'. esc_attr('{"autoInit": true, "minWidth": 767, "containerSelector": "'. ( $show_products ? '.porto-onepage-category' : '#main' ) .'","autoFit":true, "paddingOffsetTop": 1}') .'">';
        foreach ($terms as $term_cat) {
            if ( 'Uncategorized' == $term_cat->name ) {
                continue;
            }
            $id = $term_cat->term_id;
            $name = $term_cat->name;
            $slug = $term_cat->slug;
            $output .= '<li><a class="nav-link '. esc_attr( $slug ) .'" href="'. ( $show_products ? '#category-' . esc_attr( $term_cat->term_id ) : esc_url( get_term_link( $id, 'product_cat' ) ) )  .'" data-cat_id="'. esc_attr( $id ) .'">';
            $thumbnail_id = get_woocommerce_term_meta( $term_cat->term_id, 'thumbnail_id', true );
            $image = wp_get_attachment_image_src( $thumbnail_id );
            if ( $thumbnail_id && $image ) {
                $output .= '<span class="category-icon"><img src="'. esc_url( $image[0] ) .'" alt="'. esc_html( $name ) .'" width="'. esc_attr( $image[1] ) .'" height="'. $image[2] .'" /></span>';
            }
            $output .= '<span class="category-title">'. esc_html( $name ) .'</span></a></li>';
        }
        $output .= '</ul>';
    $output .= '</nav>';

if ( $show_products && !empty( $terms ) ) {
    $output .= '<div class="category-details">';
        $output .= '<form class="ajax-form d-none">';
            $output .= '<input type="hidden" name="count" value="'. esc_attr( $count ) .'" >';
            $output .= '<input type="hidden" name="orderby" value="'. esc_attr( $product_orderby ) .'" >';
            $output .= '<input type="hidden" name="order" value="'. esc_attr( $product_order ) .'" >';
            $output .= '<input type="hidden" name="columns" value="'. esc_attr( $columns ) .'" >';
            $output .= '<input type="hidden" name="view" value="'. esc_attr( $view ) .'" >';
        $output .= '</form>';
        foreach ($terms as $index => $term_cat) {
            if ( 'Uncategorized' == $term_cat->name ) {
                continue;
            }
            $output .= '<section id="category-'. esc_attr( $term_cat->term_id ) .'" class="category-section'. ( $infinite_scroll && $index === 1 ? ' ajax-loaded' : '' ) .'">';
                $output .= '<div class="category-title">';
                    $output .= '<div class="dropdown">';
                        $child_categories = wp_list_categories( array( 'child_of' => $term_cat->term_id, 'echo' => false, 'taxonomy' => 'product_cat', 'hide_title_if_empty' => true, 'title_li' => '', 'show_option_none' => '', 'orderby' => $category_orderby, 'order' => $category_order ) );
                        $output .= '<h4 class="cat-title dropdown-toggle'. ( $child_categories ? ' has-sub-cat' : '' ) .'" data-display="static" data-toggle="dropdown" aria-expanded="false"><span>'. esc_html( $term_cat->name ) .'</span></h4>';
                        if ( $child_categories ) {
                            $output .= '<ul class="dropdown-menu '. $subcategory_class .'">'. $child_categories .'</ul>';
                        }
                    $output .= '</div>';
                    $output .= '<div class="category-link"><a href="'. esc_url( get_term_link( $term_cat->term_id, 'product_cat' ) ) .'" class="btn btn-outline btn-outline-primary">'. esc_html__( 'See more', 'porto' ) .'</a></div>';
                $output .= '</div>';

                if ( $infinite_scroll && $index === 1 ) {
                    $output .= do_shortcode('[product_category per_page="'. $count .'" columns="'. $columns .'" orderby="'. $product_orderby .'" order="'. $product_order .'" category="'. $term_cat->term_id .'"]');

                    if ( $term_cat->description ) {
                        $output .= '<div class="category-description">';
                        $output .= do_shortcode( $term_cat->description );
                        $output .= '</div>';
                    }
                }
            $output .= '</section>';
        }
    $output .= '</div>';
}

$output .= '</div>';

echo $output;
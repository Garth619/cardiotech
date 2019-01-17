<?php
function porto_check_theme_options() {
    // check default options
    global $porto_settings;
    ob_start();
    include( porto_admin . '/theme_options/default_options.php' );
    $options = ob_get_clean();
    $porto_default_settings = json_decode($options, true);
    foreach ( $porto_default_settings as $key => $value ) {
        if ( is_array( $value ) ) {
            foreach ( $value as $key1 => $value1 ) {
                if ( $key1 != 'google' && ( !isset( $porto_settings[$key][$key1] ) || !$porto_settings[$key][$key1] ) ) {
                    $porto_settings[$key][$key1] = $porto_default_settings[$key][$key1];
                }
            }
        } else {
            if ( !isset( $porto_settings[$key] ) ) {
                $porto_settings[$key] = $porto_default_settings[$key];
            }
        }
    }
    return $porto_settings;
}

if ( !function_exists( 'porto_options_sidebars' ) ) :
    function porto_options_sidebars() {
        return array(
            'wide-left-sidebar',
            'wide-right-sidebar',
            'left-sidebar',
            'right-sidebar'
        );
    }
endif;

if ( !function_exists( 'porto_options_body_wrapper' ) ) :
    function porto_options_body_wrapper() {
        return array(
            'wide' => array('alt' => 'Wide', 'img' => porto_options_uri.'/layouts/body_wide.jpg'),
            'full' => array('alt' => 'Full', 'img' => porto_options_uri.'/layouts/body_full.jpg'),
            'boxed' => array('alt' => 'Boxed', 'img' => porto_options_uri.'/layouts/body_boxed.jpg'),
        );
    }
endif;

if ( !function_exists( 'porto_options_layouts' ) ) :
    function porto_options_layouts() {
        return array(
            "widewidth" => array('alt' => 'Wide Width', 'img' => porto_options_uri.'/layouts/page_wide.jpg'),
            "wide-left-sidebar" => array('alt' => 'Wide Left Sidebar', 'img' => porto_options_uri.'/layouts/page_wide_left.jpg'),
            "wide-right-sidebar" => array('alt' => 'Wide Right Sidebar', 'img' => porto_options_uri.'/layouts/page_wide_right.jpg'),
            "fullwidth" => array('alt' => 'Without Sidebar', 'img' => porto_options_uri.'/layouts/page_full.jpg'),
            "left-sidebar" => array('alt' => "Left Sidebar", 'img' => porto_options_uri.'/layouts/page_full_left.jpg'),
            "right-sidebar" => array('alt' => "Right Sidebar", 'img' => porto_options_uri.'/layouts/page_full_right.jpg')
        );
    }
endif;

if ( !function_exists( 'porto_options_wrapper' ) ) :
    function porto_options_wrapper() {
        return array(
            'wide' => array('alt' => 'Wide', 'img' => porto_options_uri.'/layouts/content_wide.jpg'),
            'full' => array('alt' => 'Full', 'img' => porto_options_uri.'/layouts/content_full.jpg'),
            'boxed' => array('alt' => 'Boxed', 'img' => porto_options_uri.'/layouts/content_boxed.jpg'),
        );
    }
endif;

if ( !function_exists( 'porto_options_banner_wrapper' ) ) :
    function porto_options_banner_wrapper() {
        return array(
            'wide' => array('alt' => 'Wide', 'img' => porto_options_uri.'/layouts/content_wide.jpg'),
            'boxed' => array('alt' => 'Boxed', 'img' => porto_options_uri.'/layouts/content_boxed.jpg'),
        );
    }
endif;

if ( !function_exists( 'porto_options_header_types' ) ) :
    function porto_options_header_types() {
        return array(
            '10' => array('alt' => 'Header Type 10', 'title' => '10', 'img' => porto_options_uri.'/headers/header_10.png'),
            '11' => array('alt' => 'Header Type 11', 'title' => '11', 'img' => porto_options_uri.'/headers/header_11.png'),
            '12' => array('alt' => 'Header Type 12', 'title' => '12', 'img' => porto_options_uri.'/headers/header_12.png'),
            '13' => array('alt' => 'Header Type 13', 'title' => '13', 'img' => porto_options_uri.'/headers/header_13.png'),
            '14' => array('alt' => 'Header Type 14', 'title' => '14', 'img' => porto_options_uri.'/headers/header_14.png'),
            '15' => array('alt' => 'Header Type 15', 'title' => '15', 'img' => porto_options_uri.'/headers/header_15.png'),
            '16' => array('alt' => 'Header Type 16', 'title' => '16', 'img' => porto_options_uri.'/headers/header_16.png'),
            '17' => array('alt' => 'Header Type 17', 'title' => '17', 'img' => porto_options_uri.'/headers/header_17.png'),

            '1' => array('alt' => 'Header Type 1', 'title' => '1', 'img' => porto_options_uri.'/headers/header_01.png'),
            '2' => array('alt' => 'Header Type 2', 'title' => '2', 'img' => porto_options_uri.'/headers/header_02.png'),
            '3' => array('alt' => 'Header Type 3', 'title' => '3', 'img' => porto_options_uri.'/headers/header_03.jpg'),
            '4' => array('alt' => 'Header Type 4', 'title' => '4', 'img' => porto_options_uri.'/headers/header_04.png'),
            '5' => array('alt' => 'Header Type 5', 'title' => '5', 'img' => porto_options_uri.'/headers/header_05.jpg'),
            '6' => array('alt' => 'Header Type 6', 'title' => '6', 'img' => porto_options_uri.'/headers/header_06.jpg'),
            '7' => array('alt' => 'Header Type 7', 'title' => '7', 'img' => porto_options_uri.'/headers/header_07.jpg'),
            '8' => array('alt' => 'Header Type 8', 'title' => '8', 'img' => porto_options_uri.'/headers/header_08.png'),
            '9' => array('alt' => 'Header Type 9', 'title' => '9', 'img' => porto_options_uri.'/headers/header_09.png'),
            
            '18' => array('alt' => 'Header Type 18', 'title' => '18', 'img' => porto_options_uri.'/headers/header_18.jpg'),
            '19' => array('alt' => 'Header Type 19', 'title' => '19', 'img' => porto_options_uri.'/headers/header_19.png'),
            'side' => array('alt' => 'Header Type(Side Navigation)', 'title' => 'Side', 'img' => porto_options_uri.'/headers/header_side.jpg'),
        );
    }
endif;

if ( !function_exists( 'porto_options_footer_types' ) ) :
    function porto_options_footer_types() {
        return array(
            '1' => array('alt' => 'Footer Type 1', 'img' => porto_options_uri.'/footers/footer_01.jpg'),
            '2' => array('alt' => 'Footer Type 2', 'img' => porto_options_uri.'/footers/footer_02.jpg'),
            '3' => array('alt' => 'Footer Type 3', 'img' => porto_options_uri.'/footers/footer_03.jpg')
        );
    }
endif;

if ( !function_exists( 'porto_options_breadcrumbs_types' ) ) :
    function porto_options_breadcrumbs_types() {
        return array(
            '1' => array('alt' => 'Breadcrumbs Type 1', 'img' => porto_options_uri.'/breadcrumbs/breadcrumbs_01.jpg'),
            '2' => array('alt' => 'Breadcrumbs Type 2', 'img' => porto_options_uri.'/breadcrumbs/breadcrumbs_02.jpg'),
            '3' => array('alt' => 'Breadcrumbs Type 3', 'img' => porto_options_uri.'/breadcrumbs/breadcrumbs_03.jpg'),
            '4' => array('alt' => 'Breadcrumbs Type 4', 'img' => porto_options_uri.'/breadcrumbs/breadcrumbs_04.jpg'),
            '5' => array('alt' => 'Breadcrumbs Type 5', 'img' => porto_options_uri.'/breadcrumbs/breadcrumbs_05.jpg'),
            '6' => array('alt' => 'Breadcrumbs Type 6', 'img' => porto_options_uri.'/breadcrumbs/breadcrumbs_06.jpg'),
        );
    }
endif;

if ( !function_exists( 'porto_options_footer_columns' ) ) :
    function porto_options_footer_columns() {
        return array(
            '1' => __('1 column - 1/12', 'porto'),
            '2' => __('2 columns - 1/6', 'porto'),
            '3' => __('3 columns - 1/4', 'porto'),
            '4' => __('4 columns - 1/3', 'porto'),
            '5' => __('5 columns - 5/12', 'porto'),
            '6' => __('6 columns - 1/2', 'porto'),
            '7' => __('7 columns - 7/12', 'porto'),
            '8' => __('8 columns - 2/3', 'porto'),
            '9' => __('9 columns - 3/4', 'porto'),
            '10' => __('10 columns - 5/6', 'porto'),
            '11' => __('11 columns - 11/12)', 'porto'),
            '12' => __('12 columns - 1/1', 'porto')
        );
    }
endif;
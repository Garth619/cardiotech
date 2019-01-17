<?php
/**
 * Porto Customizer Config
 *
 * @author     Porto Themes
 * @category   Admin Functions
 * @since      4.8.0
 */

defined( 'ABSPATH' ) || exit;

function porto_customizer_enqueue_stylesheets() {
    wp_enqueue_script( 'porto-customizer-jquery-sortable', porto_js.'/admin/jquery-ui.sortable.min.js', array( 'jquery' ), porto_version, true );
    wp_enqueue_script( 'porto-customizer-admin-js', porto_js . '/admin/customizer-admin.min.js', array( 'porto-customizer-jquery-sortable' ), porto_version, 'all' );
    wp_localize_script( 'porto-customizer-admin-js', 'customizer_admin_vars', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce' => wp_create_nonce( 'porto-customizer' )
    ) );

    global $porto_settings;
    echo '<style>';
    echo '.header-builder span, .header-builder .element-infinite { background:'. esc_attr( $porto_settings['skin-color'] ) . '}.header-builder-wrapper .element-cont { background: none; }';
    echo '</style>';
}
add_action( 'customize_controls_print_styles', 'porto_customizer_enqueue_stylesheets' );

function porto_customizer_live_scripts() {
    wp_enqueue_script( 'porto-customizer-preview-js', porto_js . '/admin/customizer-preview.min.js', NULL, porto_version, 'all' );

    if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
        wp_enqueue_style( 'porto-customizer-preview-css', porto_css . '/customizer-preview.css', false, porto_version, 'all' );

        global $porto_settings;
        $header_bg = false;
        if ( isset( $porto_settings['header-bg'] ) && $porto_settings['header-bg'] && $porto_settings['header-bg']['background-color'] && 'transparent' != $porto_settings['header-bg']['background-color'] ) {
            $header_bg = $porto_settings['header-bg']['background-color'];
        } else if ( isset( $porto_settings['header-wrap-bg'] ) && $porto_settings['header-wrap-bg'] && $porto_settings['header-wrap-bg']['background-color'] && 'transparent' != $porto_settings['header-wrap-bg']['background-color'] ) {
            $header_bg = $porto_settings['header-wrap-bg']['background-color'];
        } else if ( isset( $porto_settings['sticky-header-bg'] ) && $porto_settings['sticky-header-bg'] && $porto_settings['sticky-header-bg']['background-color'] && 'transparent' != $porto_settings['sticky-header-bg']['background-color'] ) {
            $header_bg = $porto_settings['sticky-header-bg']['background-color'];
        }
        if ( $header_bg ) {
            require_once ( porto_lib . '/lib/color-lib.php' );
            $portoColorLib = PortoColorLib::getInstance();
            $rgb = $portoColorLib->hexToRGB( $header_bg, false );
            if ( $rgb[0] <= 10 && $rgb[1] >= 126 && $rgb[1] <= 146 && $rgb[2] >= 194 && $rgb[2] <= 214 ) {
                $custom_css = '.porto-tooltip { background: rgba(26, 26, 26, 0.7); }.porto-tooltip:hover { background: rgba(26, 26, 26, 1); }';
                wp_add_inline_style( 'porto-customizer-preview-css', $custom_css );
            }
        }
    }
}
add_action( 'customize_preview_init', 'porto_customizer_live_scripts' );
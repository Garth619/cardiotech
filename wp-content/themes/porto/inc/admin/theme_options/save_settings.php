<?php

if ( !defined( 'ABSPATH' ) )
    exit; // Exit if accessed directly

add_action( 'admin_notices', 'porto_theme_options_notices' );
add_action( 'redux/options/porto_settings/saved', 'porto_save_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/import', 'porto_save_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/reset', 'porto_save_theme_settings' );
add_action( 'redux/options/porto_settings/section/reset', 'porto_save_theme_settings' );
add_action( 'redux/options/porto_settings/import', 'porto_import_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/compiler', 'porto_generate_bootstrap_css_after_options_save', 11, 1 );
add_action( 'redux/options/porto_settings/validate', 'porto_restore_empty_theme_options', 10, 3 );

function porto_theme_options_notices() {
    global $pagenow;
    $upload_dir = wp_upload_dir();
    if ( $pagenow == 'themes.php' && isset( $_GET['page'] ) && 'porto_settings' === $_GET['page'] && !wp_is_writable( $upload_dir['basedir'] ) ) {
        add_settings_error( 'porto_admin_theme_options', 'porto_admin_theme_options', __( 'Uploads folder must be writable. Please set write permission to your wp-content/uploads folder.', 'porto' ), 'error' );
        settings_errors( 'porto_admin_theme_options' );
    }
}

if ( !function_exists( 'porto_check_file_write_permission' ) ):
    function porto_check_file_write_permission( $filename ) {
        if ( is_writable( dirname( $filename ) ) == false ) {
            @chmod( dirname( $filename ), 0755 );
        }
        if ( file_exists( $filename ) ) {
            if ( is_writable( $filename ) == false ) {
                @chmod( $filename, 0755 );
            }
            @unlink( $filename );
        }
    }
endif;

function porto_get_all_shortcode_list() {
    $shortcode_list    = array();
    $all_vc_shortcodes = WPBMap::getAllShortCodes();
    $all_vc_categories = WPBMap::getCategories();
    if ( !empty( $all_vc_shortcodes ) ) {
        foreach ( $all_vc_shortcodes as $key => $s ) {
            if ( $key == 'vc_row' || $key == 'vc_row_inner' || $key == 'vc_column' || $key == 'vc_column_inner' ) {
                continue;
            }
            $shortcode_list[] = $key;
        }
    }
    return apply_filters( 'porto_all_shortcode_list', $shortcode_list );
}

function porto_get_used_shortcode_list( $shortcode_list = array(), $return_ids = false ) {
    if ( empty( $shortcode_list ) ) {
        $shortcode_list = porto_get_all_shortcode_list();
    }
    global $wpdb, $porto_settings;
    $post_contents = $wpdb->get_results( "SELECT ID, post_content, post_excerpt FROM $wpdb->posts WHERE post_type not in ('revision', 'attachment') AND post_status = 'publish' AND (post_content != '' or post_excerpt != '')" );

    $post_meta_contents = $wpdb->get_results( "SELECT post_id as ID, meta_value as post_content FROM $wpdb->postmeta WHERE meta_key in ('video_code', 'member_overview') and meta_value != ''" );
    $post_contents      = array_merge( $post_contents, $post_meta_contents );
    
    $sidebars_array = get_option( 'sidebars_widgets' );
    if ( empty( $post_contents ) || !is_array( $post_contents ) ) {
        $post_contents = array();
    }
    foreach ( $sidebars_array as $sidebar => $widgets ) {
        if ( !empty( $widgets ) && is_array( $widgets ) ) {
            foreach ( $widgets as $sidebar_widget ) {
                $widget_type = trim( substr( $sidebar_widget, 0, strrpos( $sidebar_widget, '-' ) ) );
                if ( !array_key_exists( $widget_type, $post_contents ) ) {
                    $post_contents[$widget_type] = get_option( 'widget_' . $widget_type );
                }
            }
        }
    }

    $porto_settings_keys = array(
        'footer-tooltip',
        'welcome-msg',
        'header-contact-info',
        'menu-title',
        'menu-block',
        'header-copyright',
        'post-banner-block',
        'portfolio-banner-block',
        'member-banner-block',
        'event-banner-block' 
    );
    $custom_tabs_count   = isset( $porto_settings['product-custom-tabs-count'] ) ? (int) $porto_settings['product-custom-tabs-count'] : 2;
    for ( $index = 1; $index <= $custom_tabs_count; $index++ ) {
        $porto_settings_keys[] = 'custom_tab_content' . $index;
    }
    foreach ( $porto_settings_keys as $key ) {
        if ( isset( $porto_settings[$key] ) ) {
            $post_contents[] = $porto_settings[$key];
        }
    }
    
    $used = array();
    if ( $return_ids ) {
        foreach ( $post_contents as $post_content ) {
            if ( isset( $post_content->ID ) ) {
                $content = $post_content->post_content;
                foreach ( $shortcode_list as $shortcode ) {
                    if ( false === strpos( $content, '[' ) && false === strpos( $content, 'wp:porto/porto-' ) ) {
                        continue;
                    }
                    if ( !in_array( $post_content->ID, $used ) && ( stripos( $content, '[' . $shortcode . ' ' ) !== false || stripos( $content, 'wp:porto/' . str_replace( '_', '-', $shortcode ) ) !== false ) ) {
                        $used[] = $post_content->ID;
                    }
                }
            }
        }
    } else {
        $excerpt_arr = array(
            'post_content',
            'post_excerpt' 
        );
        foreach ( $post_contents as $post_content ) {
            foreach ( $excerpt_arr as $excerpt_key ) {
                if ( is_string( $post_content ) && 'post_excerpt' == $excerpt_key ) {
                    break;
                }
                if ( !is_string( $post_content ) && 'post_excerpt' == $excerpt_key && !isset( $post_content->post_excerpt ) ) {
                    break;
                }
                $content = is_string( $post_content ) ? $post_content : ( isset( $post_content->{$excerpt_key} ) ? $post_content->{$excerpt_key} : '' );
                
                foreach ( $shortcode_list as $shortcode ) {
                    if ( false === strpos( $content, '[' ) && false === strpos( $content, 'wp:porto/porto-' ) ) {
                        continue;
                    }
                    if ( !in_array( $shortcode, $used ) && ( stripos( $content, '[' . $shortcode . ' ' ) !== false || stripos( $content, 'wp:porto/' . str_replace( '_', '-', $shortcode ) ) !== false ) ) {
                        $used[] = $shortcode;
                    }
                }
                $shortcode_list = array_diff( $shortcode_list, $used );
            }
        }
    }
    
    return apply_filters( 'porto_used_shortcode_list', $used, $return_ids );
}

function porto_compile_css( $process = null ) {
    
    if ( !current_user_can( 'manage_options' ) ) {
        return;
    }
    
    global $porto_settings, $porto_settings_optimize;
    if ( !$porto_settings_optimize || empty( $porto_settings_optimize ) ) {
        $porto_settings_optimize = get_option( 'porto_settings_optimize', array ());
    }
    
    @ini_set( ' max_execution_time', '10000' );
    @ini_set( 'memory_limit', '256M' );
    $template_dir = porto_dir;
    $upload_dir   = wp_upload_dir();
    $style_path   = $upload_dir['basedir'] . '/porto_styles';
    if ( !file_exists( $style_path ) ) {
        wp_mkdir_p( $style_path );
    }
    
    if ( $process === 'shortcodes' ) {
        
        global $reduxPortoSettings;
        $reduxFramework = $reduxPortoSettings->ReduxFramework;
        
        $is_success = false;

        // compile visual composer css file
        if ( !class_exists( 'lessc' ) ) {
            require_once( porto_admin . '/lessphp/lessc.inc.php' );
        }
        ob_start();
        include $template_dir . '/less/js_composer/less/lib/front.less.php';
        $_config_css = ob_get_clean();

        ob_start();
        $less = new lessc;
        $less->setFormatter( 'compressed' );
        $less->setImportDir( $template_dir . '/less/js_composer/less/lib' );
        echo $less->compile( '@import "../config/variables.less";' . $_config_css );
        $_config_css = ob_get_clean();
        
        $filename = $style_path . '/js_composer.css';
        porto_check_file_write_permission( $filename );
        $reduxFramework->filesystem->execute( 'put_contents', $filename, array(
             'content' => $_config_css 
        ) );
        
        // compile porto shortcodes css file
        if ( !class_exists( 'scssc' ) ) {
            require_once( porto_admin . '/scssphp/scss.inc.php' );
        }
        ob_start();
        require dirname( __FILE__ ) . '/config_scss_shortcodes.php';
        $_config_css = ob_get_clean();
        
        $scss = new scssc();
        $scss->setImportPaths( $template_dir . '/scss' );
        if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
            $scss->setFormatter( 'scss_formatter_crunched' );
        } else {
            $scss->setFormatter( 'scss_formatter' );
        }
        
        try {
            ob_start();
            echo $scss->compile( '$rtl: 0; $dir: ltr !default; $theme_uri: "'. porto_uri .'"; @import "theme/theme-imports";' . $_config_css );
            $_config_css = ob_get_clean();
            $filename    = $style_path . '/shortcodes.css';
            porto_check_file_write_permission( $filename );
            $reduxFramework->filesystem->execute( 'put_contents', $filename, array(
                 'content' => $_config_css 
            ) );
        }
        catch ( Exception $e ) {
        }
        
        // compile porto shortcodes rtl css file
        try {
            ob_start();
            echo $scss->compile( '$rtl: 1; $dir: rtl !default; $theme_uri: "'. porto_uri .'"; @import "theme/theme-imports";' . $_config_css );
            $_config_css = ob_get_clean();
            $filename    = $style_path . '/shortcodes_rtl.css';
            porto_check_file_write_permission( $filename );
            $reduxFramework->filesystem->execute( 'put_contents', $filename, array(
                 'content' => $_config_css 
            ) );
            $is_success = true;
        }
        catch ( Exception $e ) {
        }
        
        return $is_success;
    }
    
    if ( $process === 'bootstrap' ) {
        global $reduxPortoSettings;
        $reduxFramework = $reduxPortoSettings->ReduxFramework;
        
        // Compile SCSS files
        if ( !class_exists( 'scssc' ) ) {
            require_once( porto_admin . '/scssphp/scss.inc.php' );
        }
        // config file
        ob_start();
        require dirname( __FILE__ ) . '/config_scss_bootstrap.php';
        $_config_css = ob_get_clean();

        $scss = new scssc();
        $scss->setImportPaths( $template_dir . '/scss' );
        if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
            $scss->setFormatter( 'scss_formatter_crunched' );
        } else {
            $scss->setFormatter( 'scss_formatter' );
        }
        try {
            // bootstrap styles
            ob_start();
            $optimize_suffix = '';
            if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
                $optimize_suffix = '.optimized';
            }

            echo $scss->compile( '$rtl: 0; $dir: ltr !default; @import "plugins/directional"; '. $_config_css .' @import "plugins/bootstrap/bootstrap' . $optimize_suffix . '";' );
            $_config_css = ob_get_clean();

            $filename    = $style_path . '/bootstrap.css';
            porto_check_file_write_permission( $filename );
            
            $reduxFramework->filesystem->execute( 'put_contents', $filename, array(
                 'content' => $_config_css 
            ) );
            update_option( 'porto_bootstrap_style', true );
        }
        catch ( Exception $e ) {
        }
    } else if ( $process === 'bootstrap_rtl' ) {
        global $reduxPortoSettings;
        $reduxFramework = $reduxPortoSettings->ReduxFramework;
        
        // Compile SCSS files
        if ( !class_exists( 'scssc' ) ) {
            require_once( porto_admin . '/scssphp/scss.inc.php' );
        }
        // config file
        ob_start();
        require dirname( __FILE__ ) . '/config_scss_bootstrap.php';
        $_config_css = ob_get_clean();

        $scss = new scssc();
        $scss->setImportPaths( $template_dir . '/scss' );
        if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
            $scss->setFormatter( 'scss_formatter_crunched' );
        } else {
            $scss->setFormatter( 'scss_formatter' );
        }
        try {
            // bootstrap styles
            ob_start();
            $optimize_suffix = '';
            if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
                $optimize_suffix = '.optimized';
            }
            echo $scss->compile( '$rtl: 1; $dir: rtl !default; @import "plugins/directional"; '. $_config_css .' @import "plugins/bootstrap/bootstrap' . $optimize_suffix . '";' );
            $_config_css = ob_get_clean();

            $filename    = $style_path . '/bootstrap_rtl.css';
            porto_check_file_write_permission( $filename );
            
            $reduxFramework->filesystem->execute( 'put_contents', $filename, array(
                 'content' => $_config_css 
            ) );
            update_option( 'porto_bootstrap_rtl_style', true );
        }
        catch ( Exception $e ) {
        }
    }
}

if ( !function_exists( 'porto_config_value' ) ):
    function porto_config_value( $value ) {
        return isset( $value ) ? $value : 0;
    }
endif;

function porto_save_theme_settings() {
    
    update_option( 'porto_init_theme', '1' );
    
    do_action( 'porto_admin_save_theme_settings' );
}

function porto_import_theme_settings() {
    if ( is_rtl() ) {
        porto_compile_css( 'bootstrap_rtl' );
    } else {
        porto_compile_css( 'bootstrap' );
    }
}

if ( !function_exists( 'porto_generate_bootstrap_css_after_options_save' ) ):
    function porto_generate_bootstrap_css_after_options_save( $options ) {
        porto_compile_css( 'bootstrap_rtl' );
        porto_compile_css( 'bootstrap' );
    }
endif;

function porto_restore_empty_theme_options( &$plugin_options, $old_options, $last_changed_values ) {
    if ( isset( $plugin_options ) && isset( $old_options ) ) {
        $plugin_options = wp_parse_args( $plugin_options, $old_options );
    }
}
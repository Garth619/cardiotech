<?php

if ( ! defined( 'ABSPATH' ) ) {
    die();
}

/**
 * Define variables
 */
define('porto_lib',                   get_template_directory() . '/inc');       // library directory
define('porto_admin',                 porto_lib . '/admin');                    // admin directory
define('porto_plugins',               porto_lib . '/plugins');                  // plugins directory
define('porto_content_types',         porto_lib . '/content_types');            // content_types directory
define('porto_menu',                  porto_lib . '/menu');                     // menu directory
define('porto_functions',             porto_lib . '/functions');                // functions directory
define('porto_options_dir',           porto_admin . '/theme_options');          // options directory
define('porto_dir',                   get_template_directory());                // template directory
define('porto_uri',                   get_template_directory_uri());            // template directory uri
define('porto_css',                   porto_uri . '/css');                      // css uri
define('porto_js',                    porto_uri . '/js');                       // javascript uri
define('porto_plugins_uri',           porto_uri . '/inc/plugins');              // plugins uri
define('porto_options_uri',           porto_uri . '/inc/admin/theme_options');  // theme options uri
define('porto_lib_uri',               porto_uri . '/inc/lib');                  // library uri
$theme_version = '';
$theme = wp_get_theme();
if ( is_child_theme() ) {
    $theme = wp_get_theme( $theme->template );
}
$theme_version = $theme->version;
define('porto_version',               $theme_version);                    // set current version
/**
 * Wordpress theme check
 */
// set content width
if ( ! isset( $content_width ) ) $content_width = 900;
/**
 * Porto content types functions
 */
require_once(porto_functions . '/content_type.php');
/**
 * Porto functions
 */
require_once(porto_functions . '/functions.php');
/**
 * Menu
 */
require_once(porto_menu . '/menu.php');
/**
 * Content Types
 */
require_once(porto_content_types . '/content_types.php');
/**
 * Porto admin options
 */
require_once(porto_admin . '/admin.php');
/**
 * Porto Extensions
 */
require_once(porto_lib . '/lib/setup.php');
/**
 * Install Plugins
 */
require_once(porto_plugins . '/plugins.php');
/**
 * Theme support & Theme setup
 */
// theme setup
if ( !function_exists( 'porto_setup' ) ) :
    function porto_setup() {
        add_theme_support( 'title-tag' );
        //add_theme_support( 'custom-header', array() );
        //add_theme_support( 'custom-background', array() );
        add_editor_style( array( 'style.css', 'style_rtl.css' ) );
        if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
            if ( version_compare( WOOCOMMERCE_VERSION, "2.1" ) >= 0 ) {
                add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
            } else {
                define( 'WOOCOMMERCE_USE_CSS', false );
            }
        }
        // translation
        load_theme_textdomain( 'porto', porto_dir.'/languages' );
        load_child_theme_textdomain( 'porto', get_stylesheet_directory() . '/languages' );
        
        global $porto_settings, $porto_settings_optimize;
        // default rss feed links
        add_theme_support( 'automatic-feed-links' );
        // add support for post thumbnails
        add_theme_support( 'post-thumbnails' );
        // add image sizes
        add_image_size( 'blog-large', 1140, 445, true );
        add_image_size( 'blog-medium', 463, 348, true );
        add_image_size( 'related-post', (isset($porto_settings['post-related-image-size']) && (int)$porto_settings['post-related-image-size']['width']) ? (int)$porto_settings['post-related-image-size']['width'] : 450, (isset($porto_settings['post-related-image-size']) && (int)$porto_settings['post-related-image-size']['height']) ? (int)$porto_settings['post-related-image-size']['height'] : 231, true );
        if (isset($porto_settings['enable-portfolio']) && $porto_settings['enable-portfolio']) {
            add_image_size( 'portfolio-grid-one', 1140, 595, true );
            add_image_size( 'portfolio-grid-two', 560, 560, true );
            add_image_size( 'portfolio-grid', 367, 367, true );
            add_image_size( 'portfolio-masonry', 367, 9999, false );
            add_image_size( 'portfolio-full', 1140, 595, true );
            add_image_size( 'portfolio-large', 560, 367, true );
            add_image_size( 'portfolio-medium', 367, 367, true );
            add_image_size( 'portfolio-timeline', 560, 560, true );
            add_image_size( 'related-portfolio', 367, 367, true );
            add_image_size( 'portfolio-cat-stripes', 494, 1080, true );
            add_image_size( 'portfolio-cat-parallax', 1970, 627, true );
            add_image_size( 'portfolio-thumbnail', 200, 150, true );
        }
        
        if (isset($porto_settings['enable-member']) && $porto_settings['enable-member']) {
            add_image_size( 'member-two', 560, 560, true );
            add_image_size( 'member', 367, 367, true );
        }
        add_image_size( 'widget-thumb-medium', 85, 85, true );
        add_image_size( 'widget-thumb', 50, 50, true );
        // woocommerce support
        add_theme_support( 'woocommerce', array( 'gallery_thumbnail_image_width' => 150 ) );
        // allow shortcodes in widget text
        add_filter( 'widget_text', 'do_shortcode' );
        // register menus
        register_nav_menus( array(
            'main_menu' => __( 'Main Menu', 'porto' ),
            'secondary_menu' => __( 'Secondary Menu', 'porto' ),
            'sidebar_menu' => __( 'Sidebar Menu', 'porto' ),
            'top_nav' => __( 'Top Navigation', 'porto' ),
            'view_switcher' => __( 'View Switcher', 'porto' ),
            'currency_switcher' => __( 'Currency Switcher', 'porto' )
        ) );
        // add post formats
        add_theme_support( 'post-formats', array( 'aside', 'gallery', 'link', 'image', 'quote', 'video', 'audio', 'chat' ) );
        // disable master slider woocommerce product slider
        $options = get_option( 'msp_woocommerce' );
        if ( isset( $options ) && isset( $options['enable_single_product_slider'] ) && $options['enable_single_product_slider'] == 'on' ) {
            $options['enable_single_product_slider'] = '';
            update_option( 'msp_woocommerce', $options );
        }

        $porto_settings_optimize = get_option( 'porto_settings_optimize', array() );

        if ( isset( $porto_settings['google-webfont-loader'] ) && $porto_settings['google-webfont-loader'] ) {
            add_filter( 'wp_head', 'porto_google_webfont_loader' );
        }
    }
endif;
add_action( 'after_setup_theme', 'porto_setup' );
/**
 * Enqueue css, js files
 */
add_action( 'wp_enqueue_scripts',       'porto_css', 1000 );
add_action( 'wp_enqueue_scripts',       'porto_scripts', 1000 );
add_action( 'admin_enqueue_scripts',    'porto_admin_css', 1000 );
add_action( 'admin_enqueue_scripts',    'porto_admin_scripts', 1000 );
add_action( 'wp_footer',                'porto_footer_hook', 1 );
if ( is_admin() ) {
    add_action( 'enqueue_block_editor_assets', 'porto_admin_block_css', 1000 );
}

function porto_google_webfont_loader() {

    global $porto_settings;

    $gfont = array();
    $gfont_weight = array( 200, 300, 400, 700, 800 );
    $fonts = porto_settings_google_fonts();
    foreach ( $fonts as $option ) {
        if ( isset( $porto_settings[$option.'-font']['google'] ) && $porto_settings[$option.'-font']['google'] !== 'false' ) {
            $font = isset( $porto_settings[$option.'-font']['font-family'] ) ? urlencode( $porto_settings[$option.'-font']['font-family'] ) : '';
            $font_weight = isset( $porto_settings[$option.'-font']['font-weight'] ) ? $porto_settings[$option.'-font']['font-weight'] : '';
            if ( $font && !in_array( $font, $gfont ) ) {
                $gfont[] = $font;
            }
            if ( $font_weight && !in_array( $font_weight, $gfont_weight ) ) {
                $gfont_weight[] = $font_weight;
            }
        }
    }

    // font weight
    $gfont_weight = implode( ',', $gfont_weight );

    // charset
    $charsets = array();
    $subsets = '';
    if ( isset( $porto_settings['select-google-charset'] ) && $porto_settings['select-google-charset'] && isset( $porto_settings['google-charsets'] ) && $porto_settings['google-charsets'] ) {
        foreach ( $porto_settings['google-charsets'] as $charset ) {
            if ( $charset && !in_array( $charset, $charsets ) ) {
                $charsets[] = $charset;
            }
        }
    }
    if ( !empty( $charsets ) ) {
        $subsets = implode( ',', $charsets );
    }

    $font_family_arr = array();
    foreach ( $gfont as $font ) {
        $font_family_arr[] = "'" . str_replace( ' ', '+', $font ) . ':' . $gfont_weight . ( $subsets ? ':' . $subsets : '' ) . "'";
        $subsets = '';
    }
    if ( !empty( $font_family_arr ) ) {
    ?>
        <script type="text/javascript">
        WebFontConfig = {
          google: { families: [ <?php echo implode( ',', $font_family_arr ) ?> ] }
        };
        (function(d) {
          var wf = d.createElement('script'), s = d.scripts[0];
          wf.src = 'https://ajax.googleapis.com/ajax/libs/webfont/1.6.26/webfont.js';
          wf.async = true;
          s.parentNode.insertBefore(wf, s);
        })(document);</script>
    <?php
    }
}

function porto_css() {
    // deregister plugin styles
    wp_deregister_style( 'font-awesome' );
    wp_dequeue_style( 'font-awesome' );
    wp_dequeue_style( 'yith-wcwl-font-awesome' );
    wp_dequeue_style( 'bsf-Simple-Line-Icons' );

    global $porto_settings, $porto_settings_optimize, $post;

    // import revslider js/css files for only used pages
    if ( class_exists( 'RevSlider' ) && isset( $porto_settings_optimize['optimize_revslider'] ) && $porto_settings_optimize['optimize_revslider'] ) {
        $use_revslider = false;
        $banner_type = porto_get_meta_value('banner_type');
        $rev_slider = porto_get_meta_value('rev_slider');
        if ( $banner_type === 'rev_slider' && !empty($rev_slider) ) {
            $use_revslider = true;
        }
        if ( !$use_revslider && is_singular( 'portfolio' ) ) {
            $portfolio_layout = get_post_meta($post->ID, 'portfolio_layout', true);
            $portfolio_layout = ($portfolio_layout == 'default' || !$portfolio_layout) ? $porto_settings['portfolio-content-layout'] : $portfolio_layout;
            if ( 'carousel' == $portfolio_layout ) {
                $use_revslider = true;
            }
        }
        if ( !$use_revslider && isset( $porto_settings_optimize['optimize_revslider_pages'] ) ) {
            $rev_pages = $porto_settings_optimize['optimize_revslider_pages'];
            if ( $rev_pages && !empty( $rev_pages ) ) {
                if ( !is_search() && !is_404() && isset( $post->ID ) && in_array( $post->ID, $rev_pages ) ) {
                    $use_revslider = true;
                }
            }
        }
        if ( !$use_revslider ) {
            wp_dequeue_style( 'rs-plugin-settings' );
            wp_dequeue_script( 'tp-tools' );
            wp_dequeue_script( 'revmin' );
        }
    }

    if ( !wp_style_is( 'js_composer_front' ) ) {
        wp_enqueue_style( 'js_composer_front' );
    }
    // load ultimate addons default js
    $bsf_options = get_option( 'bsf_options' );
    $ultimate_global_scripts = ( isset( $bsf_options['ultimate_global_scripts'] ) ) ? $bsf_options['ultimate_global_scripts'] : false;
    if ( $ultimate_global_scripts !== 'enable' ) {
        $ultimate_css = get_option( 'ultimate_css' );
        if ( $ultimate_css == "enable" ) {
            if ( !wp_style_is( 'ultimate-style-min' ) )
                wp_enqueue_style( 'ultimate-style-min' );
        } else {
            if ( !wp_style_is( 'ultimate-style' ) ) {
                wp_enqueue_style( 'ultimate-style' );
            }
        }
    }

    /* register styles */
    // plugins styles
    wp_deregister_style( 'porto-plugins' );
    if ( is_rtl() ) {
        wp_register_style( 'porto-plugins', porto_uri.'/css/plugins_rtl.css?ver=' . porto_version );
    } else {
        wp_register_style( 'porto-plugins', porto_uri.'/css/plugins.css?ver=' . porto_version );
    }

    // default styles
    wp_deregister_style( 'porto-theme' );
    if ( is_rtl() ) {
        wp_register_style( 'porto-theme', porto_uri.'/css/theme_rtl.css?ver=' . porto_version );
    } else {
        wp_register_style( 'porto-theme', porto_uri.'/css/theme.css?ver=' . porto_version );
    }

    // shortcodes styles
    wp_deregister_style( 'porto-shortcodes' );
    if ( is_rtl() ) {
        porto_register_style( 'porto-shortcodes', 'shortcodes_rtl', false, true );
    } else {
        porto_register_style( 'porto-shortcodes', 'shortcodes', false, true );
    }

    // woocommerce styles
    if ( class_exists( 'WooCommerce' ) ) {
        wp_deregister_style( 'porto-theme-shop' );
        if ( is_rtl() ) {
            wp_register_style( 'porto-theme-shop', porto_uri.'/css/theme_rtl_shop.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-theme-shop', porto_uri.'/css/theme_shop.css?ver=' . porto_version );
        }
    }

    // bbpress, buddypress styles
    if ( class_exists( 'bbPress' ) || class_exists( 'BuddyPress' ) ) {
        wp_deregister_style( 'porto-theme-bbpress' );
        if ( is_rtl() ) {
            wp_register_style( 'porto-theme-bbpress', porto_uri.'/css/theme_rtl_bbpress.css?ver=' . porto_version );
        } else {
            wp_register_style( 'porto-theme-bbpress', porto_uri.'/css/theme_bbpress.css?ver=' . porto_version );
        }
    }

    // custom styles
    wp_deregister_style( 'porto-style' );
    wp_register_style( 'porto-style', porto_uri . '/style.css' );
    if ( is_rtl() ) {
        wp_deregister_style( 'porto-style-rtl' );
        wp_register_style( 'porto-style-rtl', porto_uri . '/style_rtl.css' );
    }

    // Load Google Fonts
    if ( !isset( $porto_settings['google-webfont-loader'] ) || !$porto_settings['google-webfont-loader'] ) {
        $gfont = array();
        $gfont_weight = array( 200, 300, 400, 700, 800 );
        $fonts = porto_settings_google_fonts();
        foreach ( $fonts as $option ) {
            if ( isset( $porto_settings[$option.'-font']['google'] ) && $porto_settings[$option.'-font']['google'] !== 'false' ) {
                $font = isset( $porto_settings[$option.'-font']['font-family'] ) ? urlencode( $porto_settings[$option.'-font']['font-family'] ) : '';
                $font_weight = isset( $porto_settings[$option.'-font']['font-weight'] ) ? $porto_settings[$option.'-font']['font-weight'] : '';
                if ( $font && !in_array( $font, $gfont ) ) {
                    $gfont[] = $font;
                }
                if ( $font_weight && !in_array( $font_weight, $gfont_weight ) ) {
                    $gfont_weight[] = $font_weight;
                }
            }
        }
        $gfont_weight = implode( ',', $gfont_weight );
        $font_family = '';
        $font_family_arr = array();
        foreach ( $gfont as $font ) {
            $font_family_arr[] = str_replace( ' ', '+', $font ) . ':' . $gfont_weight;
        }
        if ( !empty( $font_family_arr ) ) {
            $font_family = implode( '%7C', $font_family_arr );
        }
        if ( $font_family ) {
            $charsets = array();
            if ( isset( $porto_settings['select-google-charset'] ) && $porto_settings['select-google-charset'] && isset( $porto_settings['google-charsets'] ) && $porto_settings['google-charsets'] ) {
                foreach ( $porto_settings['google-charsets'] as $charset ) {
                    if ( $charset && !in_array( $charset, $charsets ) ) {
                        $charsets[] = $charset;
                    }
                }
            }

            $custom_font_args = array(
                'family' => $font_family
            );
            if ( !empty( $charsets ) ) {
                $custom_font_args['subset'] = implode( ',', $charsets );
            }

            $google_font_url = add_query_arg( $custom_font_args, '//fonts.googleapis.com/css' );
            wp_register_style( 'porto-google-fonts', $google_font_url );
            wp_enqueue_style( 'porto-google-fonts' );
        }
    }

    // ie styles
    global $wp_styles;
    wp_deregister_style( 'porto-ie' );
    wp_register_style( 'porto-ie', porto_uri.'/css/ie.css?ver=' . porto_version );

    /* enqueue styles */
    wp_enqueue_style( 'porto-bootstrap' );
    wp_enqueue_style( 'porto-plugins' );
    wp_enqueue_style( 'porto-theme' );
    wp_enqueue_style( 'porto-shortcodes' );
    if ( class_exists( 'WooCommerce' ) ) {
        wp_enqueue_style( 'porto-theme-shop' );
    }
    if ( class_exists( 'bbPress' ) || class_exists( 'BuddyPress' ) ) {
        wp_enqueue_style( 'porto-theme-bbpress' );
    }
    wp_enqueue_style( 'porto-dynamic-style' );
    wp_enqueue_style( 'porto-style' );
    if ( is_rtl() ) {
        wp_enqueue_style( 'porto-style-rtl' );
    }

    wp_enqueue_style( 'porto-ie' );
    $wp_styles->add_data( 'porto-ie', 'conditional', 'lt IE 10' );
    if ( current_user_can( 'edit_theme_options' ) ) {
        // admin style
        wp_enqueue_style( 'porto_admin_bar', porto_css . '/admin_bar.css', false, porto_version, 'all' );
    }
    porto_enqueue_revslider_css();
}
function porto_register_style( $handle, $filename, $themedir = true, $load_default = true ) {
    if ( $themedir ) {
        $blog_id = porto_get_blog_id();
        $css_file = porto_dir.'/css/'.$filename.'_'.$blog_id.'.css';
        $css_uri = porto_uri.'/css/'.$filename.'_'.$blog_id.'.css';
    } else {
        $upload_dir = wp_upload_dir();
        $css_file = $upload_dir['basedir'] . '/porto_styles/' . $filename.'.css';
        $css_uri = $upload_dir['baseurl'] . '/porto_styles/' . $filename.'.css';
    }
    if ( file_exists( $css_file ) ) {
        wp_register_style( $handle, $css_uri.'?ver=' . porto_version );
    } else if ( $load_default ) {
        wp_register_style( $handle, porto_uri.'/css/'.$filename.'.css?ver=' . porto_version );
    }
}
function porto_scripts() {
    global $porto_settings, $porto_settings_optimize;
    if ( !is_admin() && !in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
        wp_reset_postdata();

        // comment reply
        if ( is_singular() && get_option( 'thread_comments' ) ) {
            wp_enqueue_script( 'comment-reply' );
        }
        // load wc variation script
        wp_enqueue_script( 'wc-add-to-cart-variation' );
        // load visual composer default js
        if ( !wp_script_is( 'wpb_composer_front_js' ) ) {
            wp_enqueue_script( 'wpb_composer_front_js' );
        }
        // load ultimate addons default js
        $bsf_options = get_option('bsf_options');
        $ultimate_global_scripts = ( isset( $bsf_options['ultimate_global_scripts'] ) ) ? $bsf_options['ultimate_global_scripts'] : false;
        if ( $ultimate_global_scripts !== 'enable' ) {
            $isAjax = false;
            $ultimate_ajax_theme = get_option( 'ultimate_ajax_theme' );
            if ( $ultimate_ajax_theme == 'enable' )
                $isAjax = true;
            $ultimate_js = get_option('ultimate_js', 'disable');
            $bsf_dev_mode = (isset($bsf_options['dev_mode'])) ? $bsf_options['dev_mode'] : false;
            if ( ( $ultimate_js == 'enable' || $isAjax == true ) && ( $bsf_dev_mode != 'enable' ) ) {
                if ( !wp_script_is( 'ultimate-script' ) ) {
                    wp_enqueue_script( 'ultimate-script' );
                }
            }
        }

        $min_suffix = '';
        if ( isset( $porto_settings_optimize['minify_css'] ) && $porto_settings_optimize['minify_css'] ) {
            $min_suffix = '.min';
        }
        // porto scripts
        wp_deregister_script( 'porto-plugins-popper' );
        wp_register_script( 'porto-plugins-popper', porto_js .'/popper'. $min_suffix .'.js', array('jquery', 'jquery-migrate'), porto_version, true );
        wp_enqueue_script( 'porto-plugins-popper' );

        $optimize_suffix = '';
        if ( isset( $porto_settings_optimize['optimize_bootstrap'] ) && $porto_settings_optimize['optimize_bootstrap'] ) {
            $optimize_suffix = '.optimized';
        }
        wp_deregister_script( 'porto-bootstrap' );
        wp_register_script( 'porto-bootstrap', porto_js .'/bootstrap'. $optimize_suffix . $min_suffix .'.js', array('porto-plugins-popper'), porto_version, true );
        wp_enqueue_script( 'porto-bootstrap' );

        wp_deregister_script( 'porto-plugins' );
        wp_register_script( 'porto-plugins', porto_js .'/plugins'. $min_suffix .'.js', array('porto-bootstrap'), porto_version, true );
        wp_enqueue_script( 'porto-plugins' );

        if ( class_exists( 'Woocommerce' ) ) {
            wp_register_script( 'porto-woocommerce-plugins', porto_js .'/woocommerce-plugins'. $min_suffix .'.js', array('porto-plugins'), porto_version, true );
            wp_enqueue_script( 'porto-woocommerce-plugins' );
            wp_register_script( 'porto-shop-tabs-js', porto_js . '/jquery.easyResponsiveTabs'. $min_suffix .'.js', array('jquery'), porto_version, true );
        }

        wp_register_script( 'porto-slick-js', porto_js .'/jquery.slick.min.js', array('jquery'), porto_version, true );
        global $porto_product_layout;
        if ( class_exists( 'Woocommerce' ) && isset( $porto_product_layout ) && $porto_product_layout ) {
            if ( 'transparent' == $porto_product_layout ) {
                wp_enqueue_script( 'porto-slick-js' );
            }

            wp_register_script( 'porto-shop-tabs-js', porto_js . '/jquery.easyResponsiveTabs'. $min_suffix .'.js', array('jquery'), porto_version, true );
            wp_enqueue_script( 'porto-shop-tabs-js' );
        }

        // load porto theme js file
        wp_deregister_script( 'porto-theme' );
        wp_register_script( 'porto-theme', porto_js .'/theme'. $min_suffix .'.js', array('jquery'), porto_version, true );
        wp_enqueue_script( 'porto-theme' );
        if ( class_exists( 'Woocommerce' ) ) {
            wp_register_script( 'porto-woocommerce-theme', porto_js .'/woocommerce-theme'. $min_suffix .'.js', array('porto-theme'), porto_version, true );
            wp_enqueue_script( 'porto-woocommerce-theme' );
        }

        // compatible check with product filter plugin
        $js_wc_prdctfltr = false;
        if (class_exists('WC_Prdctfltr')) {
            $porto_settings['category-ajax'] = false;
            if ( get_option( 'wc_settings_prdctfltr_use_ajax', 'no' ) == 'yes' ) {
                $js_wc_prdctfltr = true;
            }
        }
        $sticky_header = porto_get_meta_value('sticky_header');
        $show_sticky_header = false;
        if ( 'no' !== $sticky_header && ( 'yes' === $sticky_header || ('yes' !== $sticky_header && $porto_settings['enable-sticky-header'] ) ) ) {
            $show_sticky_header = true;
        }

        global $porto_product_layout;
        wp_localize_script( 'porto-theme', 'js_porto_vars', array(
            'rtl' => esc_js(is_rtl() ? true : false),
            'ajax_url' => esc_js(admin_url( 'admin-ajax.php' )),
            'change_logo' => esc_js($porto_settings['change-header-logo']),
            'container_width' => esc_js($porto_settings['container-width']),
            'grid_gutter_width' => esc_js($porto_settings['grid-gutter-width']),
            'show_sticky_header' => esc_js($show_sticky_header),
            'show_sticky_header_tablet' => esc_js($porto_settings['enable-sticky-header-tablet']),
            'show_sticky_header_mobile' => esc_js($porto_settings['enable-sticky-header-mobile']),
            'ajax_loader_url' => esc_js(str_replace(array('http:', 'https:'), array('', ''), porto_uri . '/images/ajax-loader@2x.gif')),
            'category_ajax' => esc_js($porto_settings['category-ajax']),
            'prdctfltr_ajax' => esc_js($js_wc_prdctfltr),
            'show_minicart' => esc_js($porto_settings['show-minicart']),
            'slider_loop' => esc_js($porto_settings['slider-loop']),
            'slider_autoplay' => esc_js($porto_settings['slider-autoplay']),
            'slider_autoheight' => esc_js($porto_settings['slider-autoheight']),
            'slider_speed' => esc_js($porto_settings['slider-speed']),
            'slider_nav' => esc_js($porto_settings['slider-nav']),
            'slider_nav_hover' => esc_js($porto_settings['slider-nav-hover']),
            'slider_margin' => esc_js($porto_settings['slider-margin']),
            'slider_dots' => esc_js($porto_settings['slider-dots']),
            'slider_animatein' => esc_js($porto_settings['slider-animatein']),
            'slider_animateout' => esc_js($porto_settings['slider-animateout']),
            'product_thumbs_count' => esc_js($porto_settings['product-thumbs-count']),
            'product_zoom' => isset( $porto_product_layout ) && ( 'extended' === $porto_product_layout || 'full_width' === $porto_product_layout ) ? 0 : esc_js($porto_settings['product-zoom']),
            'product_zoom_mobile' => esc_js($porto_settings['product-zoom-mobile']),
            'product_image_popup' => esc_js($porto_settings['product-image-popup']),
            'zoom_type' => esc_js($porto_settings['zoom-type']),
            'zoom_scroll' => esc_js($porto_settings['zoom-scroll']),
            'zoom_lens_size' => esc_js($porto_settings['zoom-lens-size']),
            'zoom_lens_shape' => esc_js($porto_settings['zoom-lens-shape']),
            'zoom_contain_lens' => esc_js($porto_settings['zoom-contain-lens']),
            'zoom_lens_border' => esc_js($porto_settings['zoom-lens-border']),
            'zoom_border_color' => esc_js($porto_settings['zoom-border-color']),
            'zoom_border' => esc_js($porto_settings['zoom-type'] == 'inner' ? 0 : $porto_settings['zoom-border']),
            'screen_lg' => esc_js($porto_settings['container-width'] + $porto_settings['grid-gutter-width']),
            'mfp_counter' => esc_js(__('%curr% of %total%', 'porto')),
            'mfp_img_error' => esc_js(__('<a href="%url%">The image</a> could not be loaded.', 'porto')),
            'mfp_ajax_error' => esc_js(__('<a href="%url%">The content</a> could not be loaded.', 'porto')),
            'popup_close' => esc_js(__('Close', 'porto')),
            'popup_prev' => esc_js(__('Previous', 'porto')),
            'popup_next' => esc_js(__('Next', 'porto')),
            'request_error' => esc_js(__('The requested content cannot be loaded.<br/>Please try again later.', 'porto')),
            'loader_text' => esc_js(__('Loading...', 'porto')),
        ) );
    }
}
function porto_admin_css() {
    wp_deregister_style( 'font-awesome' );
    wp_dequeue_style( 'font-awesome' );
    wp_dequeue_style( 'yith-wcwl-font-awesome' );
    wp_enqueue_style( 'font-awesome', porto_css . '/font-awesome.min.css', false, porto_version, 'all' );

    //porto icon font
    wp_enqueue_style( 'porto-font', porto_css . '/Porto-Font/Porto-Font.css', false, porto_version, 'all' );
    // simple line icon font
    wp_dequeue_style( 'bsf-Simple-Line-Icons' );
    wp_dequeue_style( 'porto_shortcodes_simpleline' );
    wp_enqueue_style( 'porto-sli-font', porto_css . '/Simple-Line-Icons/Simple-Line-Icons.css', false, porto_version, 'all' );
    // wp default styles
    wp_enqueue_style( 'wp-color-picker' );
    // codemirror
    //wp_enqueue_style('porto_codemirror', porto_css . '/codemirror.css', false, porto_version, 'all');
    // admin style
    wp_enqueue_style( 'porto_admin', porto_css . '/admin.css', false, porto_version, 'all' );
    wp_enqueue_style( 'porto_admin_bar', porto_css . '/admin_bar.css', false, porto_version, 'all' );
    porto_enqueue_revslider_css();
}
function porto_admin_block_css() {
    if ( is_rtl() ) {
        porto_register_style( 'porto-shortcodes', 'shortcodes_rtl', false, true );
    } else {
        porto_register_style( 'porto-shortcodes', 'shortcodes', false, true );
    }

    wp_enqueue_style( 'porto-shortcodes' );
}
function porto_admin_scripts() {
    if (function_exists('add_thickbox'))
        add_thickbox();
    wp_enqueue_media();
    global $pagenow;
    if (in_array($pagenow, array('post.php', 'post-new.php', 'term.php'))) {
        // codemirror
        /*wp_register_script('porto-codemirror', porto_js.'/codemirror.js', array('jquery'), porto_version, true);
        wp_enqueue_script('porto-codemirror');
        wp_register_script('porto-codemirror-css', porto_js.'/codemirror/css.js', array('porto-codemirror'), porto_version, true);
        wp_enqueue_script('porto-codemirror-css');
        wp_register_script('porto-codemirror-js', porto_js.'/codemirror/javascript.js', array('porto-codemirror'), porto_version, true);
        wp_enqueue_script('porto-codemirror-js');*/
    }
    if ($pagenow == 'themes.php' && isset($_GET['page']) && 'porto_settings' === $_GET['page'] && defined( 'WPB_VC_VERSION' ) && !wp_script_is( 'waypoints', 'registered' )) {
        wp_register_script( 'waypoints', vc_asset_url( 'lib/waypoints/waypoints.min.js' ), array( 'jquery' ), WPB_VC_VERSION, true );
        wp_enqueue_script( 'waypoints' );
    }
    // admin script
    wp_register_script('porto-admin', porto_js.'/admin/admin.min.js', array('common', 'jquery', 'media-upload', 'thickbox', 'wp-color-picker'), porto_version, true);
    wp_enqueue_script('porto-admin');
    wp_localize_script( 'porto-admin', 'js_porto_admin_vars', array(
        'import_options_msg' => __('If you want to import demo, please backup current theme options in "Import / Export" section before import. Do you want to import demo?', 'porto'),
        'theme_option_url' => admin_url('admin.php?page=porto_settings')
    ) );
}
// Disable the WordPress Admin Bar for all but admins
if (! current_user_can('edit_posts')):
    show_admin_bar(false);
endif;
function porto_footer_hook() {
    add_filter('style_loader_tag', 'porto_style_loader_tag');
}
function porto_style_loader_tag($tag) {
    return str_replace("rel='stylesheet'", "rel='stylesheet' property='stylesheet'", $tag);
}
function porto_enqueue_revslider_css() {
    global $porto_settings;
    $style = '';
    if ($porto_settings['skin-color']) {
        $style = '.tparrows:before{color:' . $porto_settings['skin-color'] . ';text-shadow:0 0 3px #fff;}';
    }
    $style .= '.revslider-initialised .tp-loader{z-index:18;}';
    wp_add_inline_style('rs-plugin-settings', $style);
}
// retrieves the attachment ID from the file URL
function porto_get_image_id($image_url) {
    global $wpdb;
    $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url ));
    if ( isset( $attachment[0] ) ) {
        return $attachment[0]; 
    } else {
        return false;
    }
}
// gravityform notifications
add_filter( 'gform_validation_message', 'porto_gform_validation_message', 10, 2 );
function porto_gform_validation_message( $message, $form ) {
    return '<div class="alert alert-danger br-normal"><strong>'. strip_tags( $message ) .'</strong></div>';
}
add_filter( 'gform_confirmation', 'porto_gform_confirmation', 10, 4 );
function porto_gform_confirmation( $confirmation, $form, $entry, $ajax ) {
    if ( is_array( $confirmation ) ) {
        return $confirmation;
    }
    return '<div class="alert alert-success br-normal">'. strip_tags( $confirmation, '<script>' ) .'</div>';
}

// Fix for PHP Fatal error:  Call to undefined function YIT_Pointers() in \plugins\yith-woocommerce-wishlist\includes\class.yith-wcwl-admin-init.php
if ( ! function_exists( 'is_plugin_activate' )){
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}
if( is_plugin_active('yith-woocommerce-wishlist/init.php') ){
    if( ! class_exists( 'YIT_Pointers' ) ){
        require_once( get_template_directory() . '/woocommerce/yit-pointers.php' );
    }
}

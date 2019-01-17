<?php
/**
 * @package Porto
 * @author SW-Theme
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $porto_is_dark, $porto_settings, $porto_save_settings_is_rtl;
$porto_settings_backup = $porto_settings;
$b = porto_check_theme_options();
$porto_settings = $porto_settings_backup;
$porto_is_dark = ( $b['css-type'] == 'dark' );
$dark = $porto_is_dark;

if ( ( isset( $porto_save_settings_is_rtl ) && $porto_save_settings_is_rtl ) || is_rtl() ) {
    $left = 'right';
    $right = 'left';
    $rtl = true;
} else {
    $left = 'left';
    $right = 'right';
    $rtl = false;
}

if ( !function_exists( 'if_dark' ) ) {
    function if_dark( $if, $else = '' ) {
        global $porto_is_dark;
        if ( $porto_is_dark ) {
            return $if;
        }
        return $else;
    }
}
if ( !function_exists( 'if_light' ) ) {
    function if_light( $if, $else = '' ) {
        global $porto_is_dark;
        if ( !$porto_is_dark ) {
            return $if;
        }
        return $else;
    }
}
if ( !function_exists( 'calcContainerWidth' ) ) :
    function calcContainerWidth( $container, $flag = false, $container_width, $grid_gutter_width ) {
        if ( !$flag ) :
        ?>
            @media (min-width: 768px) {
                <?php echo $container; ?> { max-width: <?php echo 720 + $grid_gutter_width; ?>px; }
            }
            @media (min-width: 992px) {
                <?php echo $container; ?> { max-width: <?php echo 960 + $grid_gutter_width; ?>px; }
            }
            @media (min-width: <?php echo $container_width + $grid_gutter_width; ?>px) {
                <?php echo $container; ?> { max-width: <?php echo $container_width + $grid_gutter_width; ?>px; }
            }
        <?php else : ?>
            @media (min-width: 768px) {
                <?php echo $container; ?> { max-width: <?php echo 720 - $grid_gutter_width; ?>px; }
                <?php echo $container; ?> .container { max-width: <?php echo 720 - $grid_gutter_width * 2; ?>; }
            }
            @media (min-width: 992px) {
                <?php echo $container; ?> { max-width: <?php echo 960 - $grid_gutter_width; ?>px; }
                <?php echo $container; ?> .container { max-width: <?php echo 960 - $grid_gutter_width * 2; ?>px; }
            }
            @media (min-width: <?php echo $container_width + $grid_gutter_width; ?>px) {
                <?php echo $container; ?> { max-width: <?php echo $container_width - $grid_gutter_width; ?>px; }
                <?php echo $container; ?> .container { max-width: <?php echo $container_width - $grid_gutter_width * 2; ?>px; }
            }
        <?php
        endif;
    }
endif;

require_once ( porto_lib . '/lib/color-lib.php' );
$portoColorLib = PortoColorLib::getInstance();

if ( !function_exists( 'backgroundOpacity' ) ) {
    function backgroundOpacity( $portoColorLib, $bgColor, $opacity ) {
        if ( empty( $bgColor ) ) {
            return;
        }
        if ( 'transparent' == $bgColor || !$opacity ) {
            echo 'box-shadow: none;';
        } else {
            echo 'background-color: rgba(' . $portoColorLib->hexToRGB( $bgColor ) . ',' . $opacity . ');';
        }
    }
}

if ( $dark ) {
    $color_dark = $b['color-dark'];
} else {
    $color_dark = '#1d2127';
}
$color_dark_inverse = '#fff';
$color_dark_1 = $color_dark;
$color_dark_2 = $portoColorLib->lighten($color_dark_1, 2);
$color_dark_3 = $portoColorLib->lighten($color_dark_1, 5);
$color_dark_4 = $portoColorLib->lighten($color_dark_1, 8);
$color_dark_5 = $portoColorLib->lighten($color_dark_1, 3);
$color_darken_1 = $portoColorLib->darken($color_dark_1, 2);

$dark_bg = $color_dark;
$dark_default_text = '#808697';

if ( $dark ) {
    $color_price = '#eee';

    $widget_bg_color = $color_dark_3;
    $widget_title_bg_color = $color_dark_4;
    $widget_border_color = 'transparent';

    $input_border_color = $color_dark_3;
    $image_border_color = $color_dark_4;
    $color_widget_title = '#fff';

    $price_slide_bg_color = $color_dark;
    $panel_default_border = $color_dark_3;
} else {
    $color_price = '#465157';

    $widget_bg_color = '#fbfbfb';
    $widget_title_bg_color = '#f5f5f5';
    $widget_border_color = '#ddd';

    $input_border_color = '#ccc';
    $image_border_color = '#ddd';
    $color_widget_title = '#313131';
    $price_slide_bg_color = '#eee';
    $panel_default_border = '#ddd';
}

$screen_large = "(max-width: ". ( $b['container-width'] + $b['grid-gutter-width'] - 1 ) . "px)";
$input_lists = 'input[type="email"],
                input[type="number"],
                input[type="password"],
                input[type="search"],
                input[type="tel"],
                input[type="text"],
                input[type="url"],
                input[type="color"],
                input[type="date"],
                input[type="datetime"],
                input[type="datetime-local"],
                input[type="month"],
                input[type="time"],
                input[type="week"]';


$body_mobile_font_size_scale = ((float)$b['body-font']['font-size'] == 0 || (float)$b['body-mobile-font']['font-size'] == 0) ? 1 : ((float)$b['body-mobile-font']['font-size'] / (float)$b['body-font']['font-size']);
$body_mobile_line_height_scale = ((float)$b['body-font']['line-height'] == 0 || (float)$b['body-mobile-font']['line-height'] == 0) ? 1 : ((float)$b['body-mobile-font']['line-height'] / (float)$b['body-font']['line-height']);

$header_bg_empty = ( empty( $b['header-bg']['background-color'] ) || $b['header-bg']['background-color'] == 'transparent' ) && ( empty( $b['header-bg']['background-image'] ) || $b['header-bg']['background-image'] == 'none' );
$breadcrumb_bg_empty = ( empty( $b['breadcrumbs-bg']['background-color'] ) || $b['breadcrumbs-bg']['background-color'] == 'transparent' ) && ( empty( $b['breadcrumbs-bg']['background-image'] ) || $b['breadcrumbs-bg']['background-image'] == 'none' );
$content_bg_empty = ( empty( $b['content-bg']['background-color'] ) || $b['content-bg']['background-color'] == 'transparent' ) && ( empty( $b['content-bg']['background-image'] ) || $b['content-bg']['background-image'] == 'none' );
$footer_bg_empty = ( empty( $b['footer-bg']['background-color'] ) || $b['footer-bg']['background-color'] == 'transparent' ) && ( empty( $b['footer-bg']['background-image'] ) || $b['footer-bg']['background-image'] == 'none' );
?>
/*-------------------- layout --------------------- */
body {
    font-family: <?php echo $b['body-font']['font-family']; ?>, sans-serif;
    <?php if ( $b['body-font']['font-weight'] ) : ?>
        font-weight: <?php echo $b['body-font']['font-weight']; ?>;
    <?php endif; ?>
    <?php if ( $b['body-font']['font-size'] ) : ?>
        font-size: <?php echo $b['body-font']['font-size']; ?>;
    <?php endif; ?>
    <?php if ( $b['body-font']['line-height'] ) : ?>
        line-height: <?php echo $b['body-font']['line-height']; ?>;
    <?php endif; ?>
    <?php if ( $b['body-font']['letter-spacing'] ) : ?>
        letter-spacing: <?php echo $b['body-font']['letter-spacing']; ?>;
    <?php endif; ?>
}
h1 {
    <?php if ( $b['h1-font']['font-family'] ) : ?>
        font-family: <?php echo $b['h1-font']['font-family']; ?>, sans-serif;
    <?php endif; ?>
    <?php if ( $b['h1-font']['font-weight'] ) : ?>
        font-weight: <?php echo $b['h1-font']['font-weight']; ?>;
    <?php endif; ?>
    <?php if ( $b['h1-font']['font-size'] ) : ?>
        font-size: <?php echo $b['h1-font']['font-size']; ?>;
    <?php endif; ?>
    <?php if ( $b['h1-font']['line-height'] ) : ?>
        line-height: <?php echo $b['h1-font']['line-height']; ?>;
    <?php endif; ?>
    <?php if ( $b['h1-font']['letter-spacing'] ) : ?>
        letter-spacing: <?php echo $b['h1-font']['letter-spacing']; ?>;
    <?php endif; ?>
}
h1.big {
    <?php if ( $b['h1-font']['font-size'] ) : ?>
        font-size: <?php echo round( (float)$b['h1-font']['font-size'] * 1.6154, 4 ); ?>px;
    <?php endif; ?>
    <?php if ( $b['h1-font']['line-height'] ) : ?>
        line-height: <?php echo round( (float)$b['h1-font']['line-height'] * 1.2273, 4 ); ?>px;
    <?php endif; ?>
}
h1.small {
    <?php if ( $b['h1-font']['font-size'] ) : ?>
        font-size: <?php echo round( (float)$b['h1-font']['font-size'] * 0.8462, 4 ); ?>px;
    <?php endif; ?>
    <?php if ( $b['h1-font']['line-height'] ) : ?>
        line-height: <?php echo round( (float)$b['h1-font']['line-height'] * 0.9545, 4 ); ?>px;
    <?php endif; ?>
    font-weight: 600;
}
<?php for( $i = 2; $i <= 6; $i++ ) { ?>
    h<?php echo $i; ?> {
        <?php if ( $b['h'. $i .'-font']['font-family'] ) : ?>
            font-family: <?php echo $b['h'. $i .'-font']['font-family']; ?>, sans-serif;
        <?php endif; ?>
        <?php if ( $b['h'. $i .'-font']['font-weight'] ) : ?>
            font-weight: <?php echo $b['h'. $i .'-font']['font-weight']; ?>;
        <?php endif; ?>
        <?php if ( $b['h'. $i .'-font']['font-size'] ) : ?>
            font-size: <?php echo $b['h'. $i .'-font']['font-size']; ?>;
        <?php endif; ?>
        <?php if ( $b['h'. $i .'-font']['line-height'] ) : ?>
            line-height: <?php echo $b['h'. $i .'-font']['line-height']; ?>;
        <?php endif; ?>
        <?php if ( $b['h'. $i .'-font']['letter-spacing'] ) : ?>
            letter-spacing: <?php echo $b['h'. $i .'-font']['letter-spacing']; ?>;
        <?php endif; ?>
    }
<?php } ?>

<?php if ( (float)$body_mobile_font_size_scale !== (float)1 || (float)$body_mobile_line_height_scale !== (float)1 ) : ?>
    @media (max-width: 575px) {
        body {
            <?php if ( $b['body-font']['font-size'] ) : ?>
                font-size: <?php echo round( (float)$b['body-font']['font-size'] * $body_mobile_font_size_scale, 4 ); ?>px;
            <?php endif; ?>
            <?php if ( $b['body-font']['line-height'] ) : ?>
                line-height: <?php echo round( (float)$b['body-font']['line-height'] * $body_mobile_line_height_scale, 4 ); ?>px;
            <?php endif; ?>
            <?php if ( $b['body-mobile-font']['letter-spacing'] ) : ?>
                letter-spacing: <?php echo $b['body-mobile-font']['letter-spacing']; ?>;
            <?php endif; ?>
        }
        h1 {
            <?php if ( $b['h1-font']['font-size'] ) : ?>
                font-size: <?php echo round( (float)$b['h1-font']['font-size'] * $body_mobile_font_size_scale, 4 ); ?>px;
            <?php endif; ?>
            <?php if ( $b['h1-font']['line-height'] ) : ?>
                line-height: <?php echo round( (float)$b['h1-font']['line-height'] * $body_mobile_line_height_scale, 4 ); ?>px;
            <?php endif; ?>
        }
        h1.big {
            <?php if ( $b['h1-font']['font-size'] ) : ?>
                font-size: <?php echo round( (float)$b['h1-font']['font-size'] * $body_mobile_font_size_scale * 1.6154, 4 ); ?>px;
            <?php endif; ?>
            <?php if ( $b['h1-font']['line-height'] ) : ?>
                line-height: <?php echo round( (float)$b['h1-font']['line-height'] * $body_mobile_line_height_scale * 1.2273, 4 ); ?>px;
            <?php endif; ?>
        }
        <?php for( $i = 2; $i <= 6; $i++ ) { ?>
            h<?php echo $i; ?> {
                <?php if ( $b['h'. $i .'-font']['font-size'] ) : ?>
                    font-size: <?php echo round( (float)$b['h'. $i .'-font']['font-size'] * $body_mobile_font_size_scale, 4 ); ?>px;
                <?php endif; ?>
                <?php if ( $b['h'. $i .'-font']['line-height'] ) : ?>
                    line-height: <?php echo round( (float)$b['h'. $i .'-font']['line-height'] * $body_mobile_line_height_scale, 4 ); ?>px;
                <?php endif; ?>
            }
        <?php } ?>
    }
<?php endif; ?>

<?php if ( $b['body-font']['letter-spacing'] ) : ?>
    p { letter-spacing: <?php echo $b['body-font']['letter-spacing']; ?>; }
<?php endif; ?>

/*-------------------- plugins -------------------- */
<?php if ($b['border-radius']) : ?>
    .owl-carousel .owl-nav [class*='owl-'],
    .scrollbar-rail > .scroll-element .scroll-bar,
    .scrollbar-chrome > .scroll-element .scroll-bar { border-radius: 3px; }
    .resp-vtabs .resp-tabs-container,
    .fancybox-skin { border-radius: 4px; }
    .scrollbar-inner > .scroll-element .scroll-element_outer, .scrollbar-inner > .scroll-element .scroll-element_track, .scrollbar-inner > .scroll-element .scroll-bar,
    .scrollbar-outer > .scroll-element .scroll-element_outer, .scrollbar-outer > .scroll-element .scroll-element_track, .scrollbar-outer > .scroll-element .scroll-bar { border-radius: 8px; }
    .scrollbar-macosx > .scroll-element .scroll-bar,
    .scrollbar-dynamic > .scroll-element .scroll-bar { border-radius: 7px; }
    .scrollbar-light > .scroll-element .scroll-element_outer,
    .scrollbar-light > .scroll-element .scroll-element_size,
    .scrollbar-light > .scroll-element .scroll-bar { border-radius: 10px; }
    .scrollbar-dynamic > .scroll-element .scroll-element_outer,
    .scrollbar-dynamic > .scroll-element .scroll-element_size { border-radius: 12px; }
    .scrollbar-dynamic > .scroll-element:hover .scroll-element_outer .scroll-bar,
    .scrollbar-dynamic > .scroll-element.scroll-draggable .scroll-element_outer .scroll-bar { border-radius: 6px; }
<?php endif; ?>
<?php if ( $dark ) : ?>
    .fancybox-skin { background: <?php echo $b['color-dark']; ?>; }
<?php endif; ?>
.owl-carousel.show-nav-title .owl-nav [class*="owl-"] { color: #21293c; /* skin color for old */ }

/*------------------ general ---------------------- */
.porto-column { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }

/*------ Search Border Radius ------- */
<?php if( $b['search-border-radius'] ): ?>
    #header .searchform { border-radius: 20px; line-height: 40px; }
    #header .searchform input,
    #header .searchform select,
    #header .searchform .selectric .label,
    #header .searchform button { height: 40px; }
    #header .searchform select,
    #header .searchform .selectric .label { line-height: inherit; }
    #header .searchform input { border-radius: <?php echo $rtl ? "0 20px 20px 0" : "20px 0 0 20px"; ?>; }
    #header .searchform button { border-radius: <?php echo $rtl ? "20px 0 0 20px" : "0 20px 20px 0"; ?>; }
    #header .searchform .autocomplete-suggestions { left: 15px; right: 15px; }
    @media (max-width: 991px) {
        #header .searchform { border-radius: 25px; }
    }
    #header .search-popup .searchform { border-radius: 25px; }
    ul.products li.product-col.show-outimage-q-onimage-alt .product-image .labels .onhot,
    ul.products li.product-col.show-outimage-q-onimage-alt .product-image .labels .onsale { border-radius: 20px; }

    #header .searchform select,
    #header .searchform .selectric .label { padding: <?php echo $rtl ? "0 10px 0 15px" : "0 15px 0 10px"; ?>; }

    #header .searchform input { padding: <?php echo $rtl ? "0 20px 0 15px" : "0 15px 0 20px"; ?>; }

    #header .searchform button { padding: <?php echo $rtl ? "0 13px 0 16px" : "0 16px 0 13px"; ?>; }
<?php endif; ?>

<?php
/* theme */
?>
/*------------------ header ---------------------- */
<?php if ( isset( $b['header-bottom-height'] ) ) : ?>
.header-bottom { min-height: <?php echo $b['header-bottom-height']; ?>px }
<?php endif; ?>
<?php if ( isset( $b['header-top-height'] ) ) : ?>
.header-top { min-height: <?php echo $b['header-top-height']; ?>px }
<?php endif; ?>

<?php
    $header_type = porto_get_header_type();
?>
/* menu */
<?php if ( !$b['header-top-menu-hide-sep']) : ?>
    #header .header-top .top-links > li.menu-item:first-child > a { padding-<?php echo $left; ?>: 0; }
    #header .header-top .top-links > li.menu-item:last-child:after { display: none; }
<?php endif; ?>
<?php if ( 'transparent' == $b['switcher-hbg-color'] || !$b['switcher-top-level-hover'] ) : ?>
    #header .porto-view-switcher:first-child > li.menu-item:first-child > a { padding-<?php echo $left; ?>: 0; }
<?php endif; ?>
<?php if ( $b['switcher-top-level-hover'] ) : ?>
    #header .porto-view-switcher > li.menu-item:hover > a,
    #header .porto-view-switcher > li.menu-item > a.active { color: <?php echo $b['switcher-link-color']['hover']; ?>; background: <?php echo $b['switcher-hbg-color']; ?> }
<?php endif; ?>

/* search form */
@media (min-width: 768px) and <?php echo $screen_large; ?> {
    #header .searchform input { width: 318px; }
    #header .searchform.searchform-cats input { width: 190px; }
}
<?php if ( 4 == $header_type ) : ?>
    #header .searchform input { width: 298px; }
    #header .searchform.searchform-cats input { width: 170px; }
    @media <?php echo $screen_large; ?> {
        #header .searchform input { width: 240px; }
        #header .searchform.searchform-cats input { width: 112px; }
    }
<?php endif; ?>

/* header type */
<?php if ( 1 == $header_type || 4 == $header_type || 9 == $header_type || 13 == $header_type || 14 == $header_type || 17 == $header_type ) : ?>
    @media (min-width: 992px) {
        #header .header-main { transition: none; }
        #header .header-main .logo img { transition: none; -webkit-transform: scale(1); transform: scale(1); }
    }
<?php endif; ?>

/* mini cart */
<?php
    $minicart_type = porto_get_minicart_type();
?>
<?php if ( 'minicart-arrow-alt' == $minicart_type ) : ?>
    #mini-cart { line-height: 39px; }
    #mini-cart .cart-head:after { position: absolute; top: 0; <?php echo $right; ?>: 0; font-family: fontawesome; font-size: 17px; }
    #header.sticky-header #mini-cart { top: 0; <?php echo $right; ?>: 0; }
    #header.sticky-header #mini-cart .minicart-icon { font-size: 23px; }
    #header:not(.sticky-header) #mini-cart .cart-head { padding-<?php echo $right; ?>: 26px; }
    #header:not(.sticky-header) #mini-cart .cart-head:after { content: "\f107"; }
    @media (max-width: 991px) {
        #mini-cart .minicart-icon { font-size: 23px; }
        #mini-cart .cart-items { top: 1px; }
        #header:not(.sticky-header) #mini-cart .cart-head { min-width: 62px; padding-<?php echo $right; ?>: 16px; }
    }
    #mini-cart .cart-items-text { display: none; margin-<?php echo $left; ?>: 4px; }
<?php else : ?>
    #mini-cart { margin: <?php echo $rtl ? '3px 7px 3px 0' : '3px 0 3px 7px'; ?>; }
    #mini-cart .cart-head { padding: 0 10px; line-height: 24px; height: 26px; white-space: nowrap; }
    #mini-cart .cart-subtotal { font-size: 1em; font-weight: 600; text-transform: uppercase; vertical-align: middle; }
    .main-menu-wrap #mini-cart { margin-top: 3px; margin-<?php echo $left; ?>: 5px; }
    #mini-cart .minicart-icon { font-size: 25px; }
    @media (min-width: 992px) {
        #mini-cart .minicart-icon,
        #mini-cart .cart-items { display: none; }
    }
    @media (max-width: 991px) {
        #mini-cart { margin-<?php echo $left; ?>: 0; }
        #mini-cart .cart-subtotal { display: none; }
        #mini-cart .cart-popup:before { right: 10.7px; }
        #mini-cart .cart-popup:after { right: 10px; }
    }
<?php endif; ?>

/* shop header type */
<?php if ( ( (int)$header_type >= 1 && (int)$header_type <= 9 ) || 18 == $header_type || 19 == $header_type || ( 'side' == $header_type && class_exists( 'Woocommerce' ) ) ) : ?>
    #header .header-top .top-links > li.menu-item:last-child > a { padding-<?php echo $right; ?>: 0; }
    #header .header-top .top-links > li.menu-item:last-child:after { display: none; }

    .sidebar-menu .wide .popup li.sub > a,
    #header .main-menu .wide .popup li.sub > a { font-weight: 700; }
    #header .main-menu .wide .popup li.menu-item li.menu-item > a:hover { background: none; <?php echo !empty( $b['mainmenu-popup-text-color']['regular'] ) ? ' color: '. $b['mainmenu-popup-text-color']['regular'] : ''; ?> }
    #header .main-menu .wide .popup li.menu-item li.menu-item > a:hover,
    #header .main-menu .wide .popup li.sub > a:hover { text-decoration: underline; }
    .sidebar-menu .wide .popup > .inner,
    .mega-menu .wide .popup > .inner { padding: 10px; }
    .sidebar-menu .wide .popup li.sub,
    .mega-menu .wide .popup li.sub { padding: 15px 10px 0; }

    #header .main-menu .popup { <?php echo $left; ?>: -15px; }
    #header .main-menu .narrow.pos-right .popup { <?php echo $right; ?>: -15px; left: auto; }
    .mega-menu.show-arrow > li.has-sub > a:after { content: '\e81c'; font-family: 'porto'; }
    .mega-menu .narrow .popup li.menu-item > a { border-bottom: none; padding-left: 15px; padding-right: 15px; }
    .mega-menu .narrow .popup ul.sub-menu { padding-left: 0; padding-right: 0; }
    .mega-menu .narrow .popup li.menu-item-has-children > a:before { margin-<?php echo $right; ?>: 0; }
    .mega-menu.show-arrow > li.has-sub:before,
    .mega-menu.show-arrow > li.has-sub:after { content: ''; position: absolute; bottom: -1px; z-index: 112; opacity: 0; <?php echo $left; ?>: 50%; border: solid transparent; height: 0; width: 0; pointer-events: none; }
    .mega-menu.show-arrow > li.has-sub:before { bottom: 0; }
    .mega-menu.show-arrow > li.has-sub:hover:before,
    .mega-menu.show-arrow > li.has-sub:hover:after { opacity: 1; }
    .mega-menu.show-arrow > li.has-sub:before { border-bottom-color: #fff; border-width: 10px; margin-<?php echo $left; ?>: -14px; }
    .mega-menu.show-arrow > li.has-sub:after { border-bottom-color: #fff; border-width: 9px; margin-<?php echo $left; ?>: -13px; }
    #header .header-top .porto-view-switcher.show-arrow > li.has-sub:before,
    #header .header-top .porto-view-switcher.show-arrow > li.has-sub:after { border-bottom-color: #fff; }

    /* menu effect down */
    .mega-menu.show-arrow > li.has-sub:before,
    .mega-menu.show-arrow > li.has-sub:after { bottom: 4px; transition: bottom .2s ease-out; }
    .mega-menu.show-arrow > li.has-sub:before { bottom: 5px; }
    .mega-menu.show-arrow > li.has-sub:hover:before { bottom: 0; }
    .mega-menu.show-arrow > li.has-sub:hover:after { bottom: -1px; }
    /* end menu effect down */

    .sidebar-menu .wide .popup { border-<?php echo $left; ?>: none; }
    .sidebar-menu .wide .popup >.inner { margin-<?php echo $left; ?>: 0; }
    .sidebar-menu .wide ul.sub-menu { font-size: 13px; }
    .sidebar-menu > li.menu-item > a,
    .sidebar-menu > li.menu-custom-item a { font-weight: 600; }
    .sidebar-menu .wide .popup li.menu-item li.menu-item > a { font-weight: 600; }
    .sidebar-menu > li.menu-item>.arrow { <?php echo $right; ?>: 28px; font-size: 15px; }
    .sidebar-menu > li.menu-item .popup:before { content: ''; position: absolute; border-<?php echo $right; ?>: 12px solid #fff; border-top: 10px solid transparent; border-bottom: 10px solid transparent; left: -12px; top: 13px; z-index: 112; }

    <?php if ( !porto_header_type_is_side() && porto_header_type_is_preset() ) : ?>
        #header .header-contact { border-<?php echo $right; ?>: 1px solid #dde0e2; padding-<?php echo $right; ?>: 35px; margin-<?php echo $right; ?>: 18px; line-height:22px; }
        #header .header-top .header-contact { margin-<?php echo $right; ?>: 0; border-<?php echo $right; ?>: none; padding-<?php echo $right; ?>: 0; }
    <?php endif; ?>
    #header .switcher-wrap img { position: relative; top: -1px; margin-<?php echo $right; ?>: 3px; }
    
    <?php if ( 8 == $header_type ) : ?>
        @media (max-width: 360px) {
            #header .header-contact { display: none; }
        }
    <?php elseif ( 18 != $header_type ): ?>
        @media (max-width: 991px) {
            #header .header-contact { display: none; }
        }
    <?php endif; ?>

    #header .top-links a:not(.nolink):hover { text-decoration: none; }
    #header .porto-view-switcher .narrow ul.sub-menu,
    #header .top-links .narrow ul.sub-menu { padding: 5px 0; }

    <?php if ( 6 != $header_type && 7 != $header_type && 8 != $header_type ) : ?>
        @media (max-width: 767px) {
            #header .header-top { display: block; }
            #header .switcher-wrap { display: inline-block; }
        }
    <?php endif; ?>

    <?php if ( (int)$header_type >= 2 ) : ?>
        .mega-menu .menu-item .popup { box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2); }
    <?php elseif ( porto_header_type_is_side() ) : ?>
        .sidebar-menu .menu-item .popup { box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2); }
    <?php endif; ?>
<?php endif; ?>

<?php if ( 1 == $header_type || 2 == $header_type || 9 == $header_type ) : ?>
    @media (max-width: 991px) {
        #header .header-main .header-center { text-align: <?php echo $right; ?>; }
        #header .header-main .header-right { width: 1%; }
    }
    #header .mobile-toggle { font-size: 18px; padding-left: 11px; padding-right: 11px; }
    #header .header-top .porto-view-switcher .narrow .popup > .inner > ul.sub-menu { border: 1px solid #ccc; }
    #header .header-top .porto-view-switcher.show-arrow > li.has-sub:before { border-bottom-color: #ccc; }
    .mega-menu > li.menu-item { margin-<?php echo $right; ?>: 2px; }

<?php endif; ?>

<?php if ( 1 == $header_type ) : ?>
    #header.sticky-header .main-menu { background: none; }

<?php elseif ( 2 == $header_type ) : ?>
    @media (min-width: 992px) {
        #header .header-main .header-center { text-align: <?php echo $left; ?>; }
        #header.sticky-header .header-main .header-center { text-align: <?php echo $left; ?>; }
        #header .searchform-popup .search-toggle i { position: relative; top: -2px; }
    }
    #header .mobile-toggle { margin-<?php echo $left; ?>: 0; }
    #header.sticky-header .header-main .header-left { width: 1%; }
    #header .header-main .mega-menu { position: relative; padding-<?php echo $right; ?>: 10px; margin-<?php echo $right; ?>: 5px; }
    #header:not(.sticky-header) .header-main .mega-menu:after { content: ''; position: absolute; height: 24px; top: 7px; <?php echo $right; ?>: 0; border-<?php echo $right; ?>: 1px solid #dde0e2; }
    #header.sticky-header .header-main.change-logo .container > div { padding-top: 0; padding-bottom: 0; }
    #header .searchform-popup .search-toggle { line-height: 39px; }
    #header .searchform-popup .search-toggle i:before { font-family: inherit; content: '\f002'; font-weight: normal; }
    
    #header .header-main .mega-menu.show-arrow > li.has-sub:before { border-bottom-color: #f0f0f0; }

<?php elseif ( 3 == $header_type ) : ?>
    #nav-panel .mega-menu>li.menu-item { float: none; }
    #nav-panel .menu-custom-block { margin-top: 0; margin-bottom: 0; }
    #header .header-right .menu-custom-block { display: inline-block; }
    @media (max-width: 991px) {
        #header .header-right .menu-custom-block { display: none; }
    }

<?php elseif ( 4 == $header_type ) : ?>
    .mega-menu.show-arrow > li.has-sub:before { border-bottom-color: #f0f0f0; }

<?php elseif ( 5 == $header_type ) : ?>
    @media (max-width: 991px) {
        #header .header-main .header-left,
        #header .header-main .header-center { display: inline-block; }
    }

<?php elseif ( 6 == $header_type ) : ?>
    #header { border-bottom: 1px solid rgba(138, 137, 132, 0.5) !important; }
    #header .header-main .header-right { width: 275px; white-space: nowrap; text-align: <?php echo $left; ?>; }
    #header .header-main .header-center { width: 1%; white-space: nowrap; }
    #header .header-main .header-left,
    #header .header-main .header-right,
    #header .header-main .header-center { padding: 0 !important; border-<?php echo $right; ?>: 1px solid rgba(138, 137, 132, 0.5); }
    #header .header-main .header-center > div { padding-left: 20px; padding-right: 20px; }
    #header .header-main .header-right-bottom { padding-left: 15px; padding-right: 15px; }
    #header .header-main .header-center-top,
    #header .header-main .header-right-top { border-bottom: 1px solid rgba(138, 137, 132, 0.5); }
    #header .header-main .header-right > div,
    #header .header-main .header-center > div { height: 50px; display: -webkit-flex; display: -ms-flexbox; display: flex; -webkit-align-items: center; -ms-align-items: center; align-items: center; }
    #header .header-main .header-center > div { -webkit-justify-content: flex-end; -ms-justify-content: flex-end; justify-content: flex-end; -ms-flex-pack: end; }
    #header .header-main .header-right > div { -webkit-justify-content: center; -ms-justify-content: center; justify-content: center; -ms-flex-pack: center; }
    #header.sticky-header .header-main .header-center-top,
    #header.sticky-header .header-main .header-right-top { display: none; }

    #header .header-main #main-menu { display: block; }
    #header .main-menu { text-align: inherit; white-space: nowrap; }
    .mega-menu>li.menu-item { display: inline-block; float: none; }
    #header .porto-view-switcher { margin-<?php echo $right; ?>: 15px; }
    #header.header-6 .porto-view-switcher > li.menu-item > a { background: none; }
    #header .porto-view-switcher > li.menu-item > a { font-size: 14px; padding-top: 1px; }
    #header .header-right .porto-view-switcher > li.menu-item:hover > a { background: none; }
    #header .porto-view-switcher .narrow ul.sub-menu, #header .top-links .narrow ul.sub-menu { padding: 0; }
    #header .top-links > li.menu-item { float: none; }
    #header .top-links > li.menu-item > a { font-size: 13px; padding: 0 10px; }
    #header .mobile-toggle { margin: 0; }

    #header .searchform-popup .search-toggle { display: none; }
    #header.header-6 .header-right .searchform-popup .searchform { border: none; background: none; display: block; position: static; box-shadow: none; border-radius: 0; }
    #header .searchform input,
    #header .searchform button { font-size: 20px; }
    #header .searchform input { width: 218px !important; color: inherit; font-size: 13px; text-transform: uppercase; }
    #header .searchform input, #header .searchform select, #header .searchform .selectric { border-<?php echo $right; ?>: none; }
    #header .searchform button i:before { font-weight: 400; }

    #mini-cart .minicart-icon { font-size: 26px; }
    #mini-cart .cart-items { top: 4px; }
    #header:not(.sticky-header) #mini-cart .cart-items { <?php echo $left; ?>: 18px; }

    @media (max-width: 575px) {
        #header .searchform input { max-width: 140px !important; }
        #header .header-main .header-right { width: 174px; }
        #header .header-main .header-right .searchform-popup { margin-<?php echo $left; ?>: -75px; }
        #header .header-main .header-center { border-right: none; }
    }
    @media (max-width: 767px) {
        #header .header-right { text-align: center; }
    }

<?php elseif ( 7 == $header_type ) : ?>
    @media (max-width: 991px) {
        #header .header-main .header-left { display: none; }
    }
    #header.logo-center .header-main .header-left,
    #header.logo-center .header-main .header-right { width: 40%; }
    #header.logo-center .header-main .header-center { width: 20%; }
    @media <?php echo $screen_large; ?> {
        #header .mobile-toggle { display: inline-block; }
        #header .main-menu { display: none; }
        #header .header-main .container .header-left { display: none; }
        #header.logo-center .header-main .header-center { width: auto; text-align: <?php echo $left; ?>; }
        #header.logo-center .header-main .header-right { width: auto; }
    }
    @media (max-width: 991px) {
        #header .header-contact { display: none; }
        #header .header-main .header-center { text-align: <?php echo $right; ?>; }
        #header .header-main .header-right { width: 1%; }
    }
    @media (min-width: <?php echo $b['container-width'] + $b['grid-gutter-width']; ?>px) {
        #header.logo-center .header-main .header-right { padding-<?php echo $left; ?>: 0; }
    }
    #header .logo { padding-top: 10px; padding-bottom: 10px; }
    #header.sticky-header .logo,
    #header.sticky-header .header-main.change-logo .container>div { padding-top: 0; padding-bottom: 0; }
    .page-top ul.breadcrumb > li a { text-transform: inherit; }
    .switcher-wrap .mega-menu .popup,
    #header .currency-switcher,
    #header .view-switcher,
    #header .top-links { font-size: 13px; font-weight: 500; }
    #header .currency-switcher .popup,
    #header .view-switcher .popup,
    #header .top-links .popup { font-size: 11px; }
    #header .currency-switcher > li.menu-item > a,
    #header .view-switcher > li.menu-item > a,
    #header .top-links > li.menu-item > a { font-weight: 500; }
    #header .top-links { margin-<?php echo $left; ?>: 15px; margin-<?php echo $right; ?>: 20px; text-transform: uppercase; }
    #header .mobile-toggle { font-size: 18px; }
    @media (min-width: 576px) {
        #header .mobile-toggle { padding: 7px 10px; }
    }
    #header .header-main .header-contact { font-size: 16px; line-height: 36px; margin-<?php echo $right; ?>: 5px; padding-<?php echo $right; ?>: 30px; border-<?php echo $right; ?>-color: rgba(255, 255, 255, 0.3); }
    #header .searchform-popup .search-toggle { font-size: 16px; }
    #mini-cart .minicart-icon { font-size: 24px; }
    #header:not(.sticky-header) #mini-cart .cart-head { min-width: 62px; padding-right: 16px; }
    #mini-cart .cart-items { top: 1px; }
    #mini-cart .cart-popup:before { right: 28.7px; }
    #mini-cart .cart-popup:after { right: 28px; }
    @media (max-width: 575px) {
        #header .mobile-toggle { margin-<?php echo $left; ?>: 0; }
    }

<?php elseif ( 8 == $header_type ) : ?>
    .mega-menu > li.menu-item > a { padding: 9px 12px; }
    .mega-menu.show-arrow > li.has-sub > a:after { position: relative; top: -1px; }
    #header .header-main .header-contact { padding-<?php echo $right; ?>: 0; }
    #header .mobile-toggle { font-size: 20px; padding: 7px 10px; }
    #mini-cart .minicart-icon { font-size: 33px; }
    #mini-cart .cart-items { top: 0; <?php echo $left; ?>: 26px; }
    #mini-cart .cart-popup:before { <?php echo $right; ?>: 32.7px; }
    #mini-cart .cart-popup:after { <?php echo $right; ?>: 32px; }
    #header:not(.sticky-header) #mini-cart .cart-head { width: 60px; }
    #header:not(.sticky-header) #mini-cart .cart-head { padding-<?php echo $right; ?>: 20px; }
    @media (max-width: 767px) {
        #header .header-top { display: block; }
    }

<?php elseif ( 9 == $header_type ) : ?>
    #header .mobile-toggle { margin-<?php echo $left; ?>: 0; }
    #main-toggle-menu .toggle-menu-wrap { box-shadow: none; }
    #main-toggle-menu .toggle-menu-wrap > ul { border-bottom: none; }
    #main-toggle-menu .menu-title { padding-<?php echo $left; ?>: 30px; }
    #main-toggle-menu .menu-title .toggle { position: relative; top: -1px; }
    .sidebar-menu > li.menu-item > a, .sidebar-menu .menu-custom-block a { border-top-color: #e6ebee; margin-<?php echo $left; ?>: 16px; margin-<?php echo $right; ?>: 18px; padding: 14px 12px; }
    .widget_sidebar_menu .widget-title { padding: 14px 28px; }
    #main-menu .menu-custom-block { text-align: <?php echo $right; ?>; }

/* classic header type */
<?php elseif ( (int)$header_type >= 10 && (int)$header_type <= 17 ) : ?>
    @media (min-width: 992px) {
        #header .header-main .header-right { padding-<?php echo $left; ?>: <?php echo $b['grid-gutter-width']; ?>px; }
        #header .header-main .header-right .searchform-popup { margin-<?php echo $right; ?>: 0; }
    }

    #header .searchform { box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
    #header .searchform select,
    #header .searchform button,
    #header .searchform .selectric .label { height: 34px; line-height: 34px; }
    #header .searchform input { height: 34px; border: none; padding: 0 12px; width: 140px; line-height: 1.5; }
    #header .searchform select { border-<?php echo $left; ?>: 1px solid #ccc; padding-<?php echo $left; ?>: 8px; margin-<?php echo $right; ?>: -3px; font-size: 13px; }
    #header .searchform .selectric { border-<?php echo $left; ?>: 1px solid #ccc; }
    #header .searchform .selectric .label { padding-<?php echo $left; ?>: 8px; margin-<?php echo $right; ?>: -3px; }
    #header .searchform button { padding: 0 12px; }

    #header .share-links { margin-top: 0; margin-bottom: 0; }
    #header .share-links a { width: 30px; height: 30px; border-radius: 30px; margin: 0 2px; overflow: hidden; box-shadow: 0 2px 2px 0 rgba(0, 0, 0, 0.3); transition: all 0.2s; font-size: 14px; }
    #header .share-links a:not(:hover) { background-color: #fff; color: #333; }
<?php endif; ?>
<?php if ( 10 == $header_type ) : ?>
    #header .header-right-bottom { margin-top: 10px; }
    #header.sticky-header .header-right-bottom { margin-top: 0; }
    @media (max-width: 991px) {
        #header .header-right-bottom { margin-top: 0; }
    }
    @media (max-width: 575px) {
        #header .share-links { display: none; }
    }
    #header .header-main .header-left,
    #header .header-main .header-center,
    #header .header-main .header-right { padding-top: 15px; padding-bottom: 15px; }
    @media (min-width: 992px) {
        #header .header-main.sticky .header-right-top { display: none; }
        #header .header-main.sticky .header-right-bottom { margin-top: 0; }
        #header.sticky-header .header-main.sticky .header-left,
        #header.sticky-header .header-main.sticky .header-center,
        #header.sticky-header .header-main.sticky .header-right { padding-top: 0; padding-bottom: 0; }
        #header .header-contact { margin: 0 0 4px; }
        #header .searchform { margin-bottom: 4px; margin-<?php echo $left; ?>: 15px; }
        #header #mini-cart { margin: <?php echo $rtl ? "0 15px 0 0" : "0 0 0 15px"; ?>; }
    }
<?php endif; ?>
<?php if ( (int)$header_type >= 11 && (int)$header_type <= 17 ) : ?>
    #header .header-main .searchform-popup, #header .header-main #mini-cart { display: none; }
    @media (min-width: 768px) {
        #header .switcher-wrap { margin-<?php echo $right; ?>: 5px; }
        #header .block-inline { line-height: 50px; margin-bottom: 5px; }
        #header .header-left .block-inline { margin-<?php echo $right; ?>: 8px; }
        #header .header-left .block-inline > * { margin: <?php echo $rtl ? "0 0 0 7px" : "0 7px 0 0"; ?>; }
        #header .header-right .block-inline { margin-<?php echo $left; ?>: 8px; }
        #header .header-right .block-inline > * { margin: <?php echo $rtl ? "0 7px 0 0" : "0 0 0 7px"; ?>; }
        #header .share-links { line-height: 1; }
    }
    #header .header-top .welcome-msg { font-size: 1.15em; }
    #header .header-top #mini-cart { font-size: 1em; }
    #header .header-top #mini-cart:first-child { margin-left: 0; margin-right: 0; }
    @media (max-width: 991px) {
        #header .header-top .header-left > *, #header .header-top .header-right > * { display: none; }
        #header .header-top .header-left > .block-inline, #header .header-top .header-right > .block-inline { display: block; }
        #header .header-top .searchform-popup, #header .header-top #mini-cart { display: none; }
        #header .header-main .searchform-popup, #header .header-main #mini-cart { display: inline-block; }
    }
<?php endif; ?>
<?php if ( 11 == $header_type || 12 == $header_type ) : ?>
    @media (min-width: 992px) {
        #header .header-main .header-left,
        #header .header-main .header-right,
        #header .header-main .header-center,
        .fixed-header #header .header-main .header-left,
        .fixed-header #header .header-main .header-right,
        .fixed-header #header .header-main .header-center,
        #header.sticky-header .header-main.sticky .header-left,
        #header.sticky-header .header-main.sticky .header-right,
        #header.sticky-header .header-main.sticky .header-center { padding-top: 0; padding-bottom: 0; }
        #header .main-menu > li.menu-item > a { border-radius: 0; margin-bottom: 0; }
        #header .main-menu .popup { margin-top: 0; }
        #header .main-menu .wide .popup,
        .header-wrapper #header .main-menu .wide .popup > .inner,
        #header .main-menu .narrow .popup,
        #header .main-menu .narrow .popup > .inner,
        #header .main-menu .narrow .popup > .inner > ul.sub-menu,
        #header .main-menu .narrow ul.sub-menu ul.sub-menu { border-radius: 0; }
        #header .main-menu > li.menu-item > a .tip { top: <?php echo $b['mainmenu-toplevel-padding2']['padding-top'] - 18; ?>px; }
        #header .share-links { margin-top: <?php echo $b['mainmenu-toplevel-padding2']['padding-top'] - $b['mainmenu-toplevel-padding2']['padding-bottom'] - 1; ?>px; }
    }
    @media (min-width: <?php echo $b['container-width'] + $b['grid-gutter-width']; ?>px) {
        #header .main-menu > li.menu-item > a .tip { top: <?php echo $b['mainmenu-toplevel-padding1']['padding-top'] - 18; ?>px; }
        #header .share-links { margin-top: <?php echo $b['mainmenu-toplevel-padding1']['padding-top'] - $b['mainmenu-toplevel-padding1']['padding-bottom'] - 1; ?>px; }
    }
<?php endif; ?>
<?php if ( 11 == $header_type || 15 == $header_type || 16 == $header_type ) : ?>
    #header .searchform { margin-<?php echo $left; ?>: 15px; }
    @media (max-width: 991px) {
        #header .share-links { display: none; }
    }
<?php endif; ?>
<?php if ( 11 == $header_type ) : ?>
    @media (min-width: 992px) {
        #header .header-main.change-logo .main-menu > li.menu-item,
        #header .header-main.change-logo .main-menu > li.menu-item.active,
        #header .header-main.change-logo .main-menu > li.menu-item:hover { margin-top: 0; }
        #header .header-main.change-logo .main-menu > li.menu-item > a,
        #header .header-main.change-logo .main-menu > li.menu-item.active > a,
        #header .header-main.change-logo .main-menu > li.menu-item:hover > a { border-width: 0; }
        #header .show-header-top .main-menu > li.menu-item,
        #header .show-header-top .main-menu > li.menu-item.active,
        #header .show-header-top .main-menu > li.menu-item:hover { margin-top: 0; }
        #header .show-header-top .main-menu > li.menu-item > a,
        #header .show-header-top .main-menu > li.menu-item.active > a,
        #header .show-header-top .main-menu > li.menu-item:hover > a { border-width: 0; }
    }
<?php elseif ( 12 == $header_type ) : ?>
    #header .share-links a { background: none; box-shadow: none;<?php if ( $b['header-link-color']['regular'] ) { echo 'color: '.$b['header-link-color']['regular']; } ?>; }
    @media (max-width: 991px) {
        #header .header-main .share-links { margin-<?php echo $left; ?>: 0; }
    }
    @media (max-width: 575px) {
        #header .header-main .share-links { display: none; }
    }
<?php elseif ( 13 == $header_type ) : ?>
    @media (max-width: 991px) {
        #header .header-main .container .header-left { display: none; }
    }
<?php elseif ( 14 == $header_type ) : ?>
    #header .main-menu > li.menu-item { margin-<?php echo $right; ?>: 2px; }

<?php elseif ( 18 == $header_type ) : ?>
    #header .header-right .mega-menu > li.menu-item > a {
        padding: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-top']); ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-right']); ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-bottom']); ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-left']); ?>px;
    }
    @media <?php echo $screen_large; ?> {
        #header .header-right .mega-menu > li.menu-item > a {
            padding: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-top']); ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-right']); ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-bottom']); ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-left']); ?>px;
        }
    }
    #header .header-contact { border-<?php echo $right; ?>: none; padding-<?php echo $right; ?>: 30px; }

    #header #mini-cart .minicart-icon { font-size: 24px; }
    #header #mini-cart .cart-items { font-size: 11px; font-weight: 400; }
    #mini-cart .minicart-icon { font-size: 27px; }
    #mini-cart .cart-items { background-color: #b7597c; top: 3px; <?php echo $left; ?>: 25px; width: 14px; height: 14px; line-height: 14px; font-size: 9px; }
    #mini-cart .cart-head:after { top: -1px; font-size: 16px; }
    #mini-cart .cart-popup:before { <?php echo $right; ?>: 25.7px; }
    #mini-cart .cart-popup:after { <?php echo $right; ?>: 25px; }
    #header:not(.sticky-header) #mini-cart .cart-head { min-width: 56px; padding-<?php echo $right; ?>: 17px; }
    #header.header-18 .searchform-popup .search-toggle { font-size: 20px; line-height: 39px; }
    #header .searchform-popup .search-toggle i:before { font-weight: 400; }

    .mega-menu.show-arrow > li.has-sub:before { border-bottom-color: #f0f0f0; }
    #header .mobile-toggle { font-size: 21px; }

    @media (min-width: 992px) {
        #header .header-main .header-right .searchform-popup { margin-left: 18px; margin-right: 18px; }
        .fixed-header #header .header-main .header-left { padding: 25px 0; }
    }

<?php elseif ( 19 == $header_type ) : ?>
    @media (min-width: 992px) {
        #header .header-main .header-center { padding: 30px 0; }
    }
    @media (max-width: 575px) {
        #header.logo-center .header-main .header-center .logo { margin: 0 !important; }
    }
    @media (max-width: 991px) {
        #header.logo-center .header-main .header-left { display: none; }
    }
    #header .header-main #main-menu { display: table; }
    #header.header-19 .searchform-popup .search-toggle { color: <?php echo $b['header-top-link-color']['regular']; ?>; }
    #header.header-19 .searchform-popup .search-toggle:hover { color: <?php echo $b['header-top-link-color']['hover']; ?>; }
    #header .searchform { line-height: 36px; font-family: <?php echo $b['body-font']['font-family']; ?> }
    #header .searchform input,
    #header .searchform.searchform-cats input { width: 180px; }
    <?php if( $b['search-border-radius'] ): ?>
        #header.header-19 .searchform { border-radius: 4px; border: none; }
    <?php endif; ?>
    #header .searchform input, #header .searchform select, #header .searchform .selectric .label, #header .searchform button { height: 36px; }
    <?php if ( !$b['show-minicart'] || !class_exists( 'Woocommerce' ) ) : ?>
        #header .header-top { padding: 5px 0; }
    <?php endif; ?>
    #header .header-top .share-links { margin: 5px; }
    #header .searchform input { padding: 0 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    #header .searchform button { padding: 0 12px; font-size: 12px; }
    #header .header-right > *:not(:last-child),
    #header .top-links>li.menu-item { margin-<?php echo $right; ?>: 15px; }
    #header .header-main .header-left { text-align: <?php echo $right; ?> }
    @media (min-width: 992px) {
        #header .header-main .header-left .main-menu { display: inline-block; }
    }
    #header .searchform:not(.searchform-cats) input { border-<?php echo $right; ?>: none; }
    #header .header-main { border-bottom: 1px solid rgba(0, 0, 0, 0.075); }
    <?php if ( $b['show-minicart'] && class_exists( 'Woocommerce' ) ) : ?>
        #mini-cart { padding: <?php echo $rtl ? '5px 25px 5px 0' : '5px 0 5px 25px'; ?>; border-<?php echo $left; ?>: 1px solid rgba(255, 255, 255, 0.15) }
        #mini-cart .cart-items,
        #mini-cart .cart-head:after { display: none; }
        #mini-cart .cart-items-text { display: inline; text-transform: uppercase; font-weight: 300; vertical-align: middle; }
        #mini-cart .minicart-icon { border-bottom: 18px solid; border-left: 1px solid transparent; border-right: 1px solid transparent; width: 18px; position: relative; top: 2px; margin-right: 6px; }
        #mini-cart .minicart-icon:before { content: ''; position: absolute; bottom: 100%; left: 50%; margin-left: -5px; border-width: 2px 2px 0 2px; width: 10px; height: 5px; border-radius: 3px 3px 0 0; border-style: solid; }
    <?php endif; ?>

<?php elseif ( porto_header_type_is_side() ) : ?>
    .header-wrapper #header .side-top { display: block; text-align: <?php echo $left; ?>; }
    .header-wrapper #header .side-top:after { content: ''; display: block; clear: both; }
    .header-wrapper #header .side-top > .container { display: block; min-height: 0 !important; position: static; padding-top: 0; padding-bottom: 0; }
    .header-wrapper #header .side-top > .container > * { display: inline-block; padding: 0 !important; }
    .header-wrapper #header .side-top > .container > *:first-child { margin-left: 0; margin-right: 0; }
    .header-wrapper #header .share-links { margin: 0 0 16px; }
    #header .share-links a { width: 30px; height: 30px; margin: 1px; box-shadow: none; }

    @media (min-width: 992px) {
        .admin-bar .header-wrapper #header { min-height: calc(100vh - 32px); }
        .header-wrapper { position: absolute; top: 0; bottom: 0; z-index: 1002; }
        .header-wrapper #header { position: fixed; top: 0; <?php echo $left; ?>: 0; width: 256px; min-height: 100vh; padding: 10px 15px 160px; }
        .header-wrapper #header.initialize { position: absolute; }
        .header-wrapper #header.fixed-bottom { position: fixed; bottom: 0; top: auto; }
        .header-wrapper #header .side-top > .container { padding: 0; width: 100%; }
        .header-wrapper #header .header-main > .container { position: static; padding: 0; width: 100%; display: block; }
        .header-wrapper #header .header-main > .container > * { position: static; display: block; padding: 0; }
        #header .logo { text-align: center; margin: 30px auto; }
        .header-wrapper #header .searchform { margin-bottom: 20px; }
        .header-wrapper #header .searchform input { padding: 0 10px; border-width: 0; width: 190px; }
        .header-wrapper #header .searchform.searchform-cats input { width: 95px; }
        .header-wrapper #header .searchform button { padding: <?php echo $rtl ? '0 8px 0 9px' : '0 9px 0 8px'; ?>; }
        .header-wrapper #header .searchform select { border-width: 0;  padding: 0 5px; width: 93px; }
        .header-wrapper #header .searchform .selectric-cat { width: 93px; }
        .header-wrapper #header .searchform .selectric { border-width: 0; }
        .header-wrapper #header .searchform .selectric .label { padding: 0 5px; }
        .header-wrapper #header .searchform .autocomplete-suggestions { left: -1px; right: -1px; }
        .header-wrapper #header .top-links { display: block; font-size: .8em; margin-bottom: 20px; }
        .header-wrapper #header .top-links li.menu-item { display: block; float: none; margin: 0; }
        .header-wrapper #header .top-links li.menu-item:after { display: none; }
        .header-wrapper #header .top-links li.menu-item > a { margin: 0; padding-top: 7px; padding-bottom: 7px; }
        .header-wrapper #header .top-links li.menu-item:first-child > a { border-top-width: 0; }
        .header-wrapper #header .header-contact { margin: 5px 0 20px; white-space: normal; }
        .header-wrapper #header .header-copyright { font-size: .9em; }
        .header-wrapper #mini-cart .cart-popup { <?php echo $left; ?>: 0; <?php echo $right; ?>: auto; }
        .header-wrapper #mini-cart .cart-popup:before,
        .header-wrapper #mini-cart .cart-popup:after { <?php echo $right; ?>: auto; <?php echo $left; ?>: 10px; }
        .header-wrapper .side-bottom { text-align: center; position: absolute; bottom: 0; left: 0; right: 0; margin: 20px 10px; }
        .page-wrapper.side-nav .page-top.fixed-pos { position: fixed; z-index: 1001; width: 100%; box-shadow: 0 1px 0 0 rgba(0, 0, 0, .1); }
        .page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup { <?php echo $left; ?>: -32px; }
    }
    @media (min-width: 1440px) {
        .header-wrapper #header { width: 300px; padding-left: 30px; padding-right: 30px; }
        .page-wrapper.side-nav > * { padding-<?php echo $left; ?>: 300px; }
        .header-wrapper #header .searchform input { width: 204px; }
        #header .header-contact { float: <?php echo $right; ?>; }
        #header .share-links { float: <?php echo $left; ?>; }
        #header .header-copyright { clear: both; }
        .header-wrapper .side-bottom { margin-left: 30px; margin-right: 30px; }
    }
    @media (max-width: 1439px) {
        .header-wrapper #header .header-contact { margin-<?php echo $right; ?>: 5px; }
    }

    .sidebar-menu { margin-bottom: 20px; }
    .sidebar-menu > li.menu-item > a, .sidebar-menu .menu-custom-block a { border-top: none; }
    .sidebar-menu > li.menu-item > a { margin-left: 0; margin-right: 0; }
    .sidebar-menu > li.menu-item > .arrow { <?php echo $right; ?>: 5px; }
    .sidebar-menu > li.menu-item:last-child:hover { border-radius: 0; }
    .sidebar-menu .menu-custom-block a { margin-left: 0; margin-right: 0; padding-left: 5px; padding-right: 5px; }
    .sidebar-menu .menu-custom-block .fa { width: 18px; text-align: center; }
    .sidebar-menu .menu-custom-block .fa,
    .sidebar-menu .menu-custom-block .avatar { margin-<?php echo $right; ?>: 5px; }
    .sidebar-menu .menu-custom-block > .avatar img { margin-top: -5px; }

    @media (max-width: 991px) {
        .header-wrapper #header .side-top { padding: 10px 0 0; }
        .header-wrapper #header .side-top .porto-view-switcher { float: <?php echo $left; ?>; }
        .header-wrapper #header .side-top .mini-cart { float: <?php echo $right; ?>; }
        .header-wrapper #header .logo { margin-bottom: 5px; }
        .header-wrapper #header .sidebar-menu { display: none; }
        .header-wrapper #header .share-links { margin: <?php echo $rtl ? "0 10px 0 0" : "0 0 0 10px"; ?>; }
        .header-wrapper #header .share-links a:last-child { margin-<?php echo $right; ?>: 0; }
        .header-wrapper #header .header-copyright { display: none; }

        .header-wrapper #header .side-top { padding-top: 0; }
        .header-wrapper #header .side-top > .container > * { display: none !important; }
        .header-wrapper #header .side-top > .container > .mini-cart { display: block !important; position: absolute !important; top: 50%; bottom: 50%; height: 26px; margin-top: -12px; <?php echo $right; ?>: 15px; z-index: 1001; }
        .header-wrapper #header .logo { margin: 0; }
        .header-wrapper #header .share-links { display: none; }
        .header-wrapper #header .show-minicart .header-contact { margin-<?php echo $right; ?>: 80px; }
    }

    @media (min-width: 992px) {
        body.boxed.body-side .header-wrapper { <?php echo $left; ?>: -276px; margin-top: -30px; }
        body.boxed.body-side .page-wrapper.side-nav { max-width: 100%; }
        body.boxed.body-side .page-wrapper.side-nav > * { padding-<?php echo $left; ?> : 0; }
        body.boxed.body-side .page-wrapper.side-nav .page-top.fixed-pos { width: auto; }
    }

    <?php if ( class_exists( 'Woocommerce' ) ) : ?>
        #mini-cart { margin: 0; text-transform: uppercase; }
        #mini-cart .cart-head { padding: 0; }
        #mini-cart .cart-head:after { content: '\e81c'; font-family: 'porto'; font-size: 15px; margin-<?php echo $left; ?>: 5px; vertical-align: top; position: relative; top: -1px; }
        #mini-cart .cart-subtotal { font-size: 13px; font-weight: 500; margin-<?php echo $left; ?>: 1px; }
        #header .side-top .header-minicart { float: <?php echo $right; ?>; margin-top: 3px; }
    <?php endif; ?>
    #header .searchform-popup .search-toggle { font-size: 16px; }
    @media (max-width: 991px) {
        #header .header-right { width: 1%; white-space: nowrap; }
        #header .header-main .header-center { text-align: <?php echo $right; ?> }
        #header .mobile-toggle { font-size: 20px; margin-<?php echo $left; ?>: 0; }
        .fixed-header #header .header-main { padding-top: 5px; padding-bottom: 5px; }
        #mini-cart .minicart-icon { font-size: 22px; }
        #mini-cart .cart-head:after { display: none; }
    }

/* header builder type */
<?php elseif ( empty( $header_type ) ) : ?>

<?php endif; ?>

/* breadcrumb type */
<?php
    $page_header_type = $porto_settings['breadcrumbs-type'] ? $porto_settings['breadcrumbs-type'] : '1';
?>
<?php if ( 1 === (int)$page_header_type ) : ?>
    .page-top .page-title { margin-bottom: <?php echo -1 * (int)($b['breadcrumbs-bottom-border']['border-top']); ?>px; padding-bottom: 12px; border-bottom: <?php echo $b['breadcrumbs-bottom-border']['border-top']; ?> solid <?php echo $b['skin-color']; ?>; }
<?php elseif ( 3 === (int)$page_header_type || 4 === (int)$page_header_type || 5 === (int)$page_header_type ) : ?>
    <?php if ( class_exists( 'Woocommerce' ) ) : ?>
        .page-top .product-nav { position: static; height: auto; margin-top: 0; }
        .page-top .product-nav .product-prev,
        .page-top .product-nav .product-next { float: none; position: absolute; height: 30px; top: 50%; bottom: 50%; margin-top: -15px; }
        .page-top .product-nav .product-prev { <?php echo $right; ?>: 10px; }
        .page-top .product-nav .product-next { <?php echo $left; ?>: 10px; }
        .page-top .product-nav .product-next .product-popup { <?php echo $right; ?>: auto; <?php echo $left; ?>: 0; }
        .page-top .product-nav .product-next .product-popup:before { <?php echo $right; ?>: auto; <?php echo $left; ?>: 6px; }
    <?php endif; ?>
    .page-top .sort-source { position: static; text-align: center; margin-top: 5px; border-width: 0; }
    <?php if ( 3 === (int)$page_header_type ) : ?>
        .page-top ul.breadcrumb { -webkit-justify-content: center; -ms-justify-content: center; justify-content: center; -ms-flex-pack: center; }
    <?php endif; ?>
<?php endif; ?>
<?php if ( 4 === (int)$page_header_type || 5 === (int)$page_header_type ) : ?>
    .page-top { padding-top: 20px; padding-bottom: 20px; }
    .page-top .page-title { padding-bottom: 0; }
    @media (max-width: 991px) {
        .page-top .page-sub-title { margin-bottom: 5px; margin-top: 0; }
        .page-top .breadcrumbs-wrap { margin-bottom: 5px; }
    }
    @media (min-width: 992px) {
        .page-top .page-title { min-height: 0; line-height: 1.25; }
        .page-top .page-sub-title { line-height: 1.6; }
        <?php if ( class_exists( 'Woocommerce' ) ) : ?>
            .page-top .product-nav { display: inline-block; height: 30px; vertical-align: middle; margin-<?php echo $left; ?>: 10px; }
            .page-top .product-nav .product-prev,
            .page-top .product-nav .product-next { position: relative; }
            .page-top .product-nav .product-prev { float: <?php echo $left; ?>; <?php echo $left; ?>: 0; }
            .page-top .product-nav .product-prev .product-popup { <?php echo $right; ?>: auto; <?php echo $left; ?>: -26px; }
            .page-top .product-nav .product-prev:before { <?php echo $right; ?>: auto; <?php echo $left; ?>: 32px; }
            .page-top .product-nav .product-next { float: <?php echo $left; ?>; <?php echo $left; ?>: 0; }
            .page-top .product-nav .product-next .product-popup { <?php echo $right; ?>: auto; <?php echo $left; ?>: 0; }
            .page-top .product-nav .product-next .product-popup:before { <?php echo $right; ?>: auto; }
        <?php endif; ?>
    }
<?php endif; ?>
<?php if ( 4 === (int)$page_header_type ) : ?>
    @media (min-width: 992px) {
        <?php if ( class_exists( 'Woocommerce' ) ) : ?>
            .page-top .product-nav { height: auto; }
        <?php endif; ?>
        .page-top .breadcrumb { -webkit-justify-content: flex-end; -ms-justify-content: flex-end; justify-content: flex-end; -ms-flex-pack: end; }
    }
<?php elseif ( 6 === (int)$page_header_type ) : ?>
    .page-top ul.breadcrumb > li.home { display: inline-block; }
    .page-top ul.breadcrumb > li.home a { position: relative; width: 14px; text-indent: -9999px; }
    .page-top ul.breadcrumb > li.home a:after { content: "\e883"; font-family: 'porto'; position: absolute; <?php echo $left; ?>: 0; top: 0; text-indent: 0; }
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) && ( 1 === (int)$page_header_type || 2 === (int)$page_header_type ) ) : ?>
    body.single-product .page-top .breadcrumbs-wrap { padding-<?php echo $right; ?>: 55px; }
<?php endif; ?>

/*------------------ footer ---------------------- */


/*------------------ theme ---------------------- */
/*------ Grid Gutter Width ------- */
/* footer */
#footer .logo { margin-<?php echo $right; ?>: <?php echo $b['grid-gutter-width'] / 2 + 10; ?>px; }

/* header side */
@media (min-width: 992px) {
    #footer .footer-bottom .footer-left .widget { margin-<?php echo $right; ?>: <?php echo $b['grid-gutter-width'] / 2 + 5; ?>px; }
    #footer .footer-bottom .footer-right .widget { margin-<?php echo $left; ?>: <?php echo $b['grid-gutter-width'] / 2 + 5; ?>px; }
    body.boxed.body-side { padding-<?php echo $left; ?>: <?php echo $b['grid-gutter-width'] + 256; ?>px; padding-<?php echo $right; ?>: <?php echo $b['grid-gutter-width']; ?>px; }
    body.boxed.body-side.modal-open { padding-<?php echo $left; ?>: <?php echo $b['grid-gutter-width'] + 256; ?>px !important; padding-<?php echo $right; ?>: <?php echo $b['grid-gutter-width']; ?>px !important; }
    body.boxed.body-side .page-wrapper.side-nav .container { padding-<?php echo $left; ?>: <?php echo $b['grid-gutter-width']; ?>px; padding-<?php echo $right; ?>: <?php echo $b['grid-gutter-width']; ?>px; }
    body.boxed.body-side .page-wrapper.side-nav .page-top.fixed-pos { <?php echo $left; ?>: <?php echo $b['grid-gutter-width'] + 256; ?>px; <?php echo $right; ?>: <?php echo $b['grid-gutter-width']; ?>px; }
}

/* header */
@media (min-width: 768px) {
    #header-boxed #header.sticky-header .header-main.sticky { max-width: <?php echo $b['grid-gutter-width'] + 720; ?>px; }
}
@media (min-width: 992px) {
    #header-boxed #header.sticky-header .header-main.sticky,
    #header-boxed #header.sticky-header .main-menu-wrap { max-width: <?php echo $b['grid-gutter-width'] + 960; ?>px; }
}

/* page top */
.page-top .sort-source { <?php echo $right; ?>: <?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* post */
.post-carousel .post-item,
.widget .row .post-item-small { margin: 0 <?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* carousel */
.owl-carousel.show-nav-hover .owl-nav .owl-prev { <?php echo $left; ?>: <?php echo (-15 - (($b['grid-gutter-width'] - 20) / 2)); ?>px; }
.owl-carousel.show-nav-hover .owl-nav .owl-next { <?php echo $right; ?>: <?php echo (-15 - (($b['grid-gutter-width'] - 20) / 2)); ?>px; }
.owl-carousel.show-nav-title.post-carousel .owl-nav,
.owl-carousel.show-nav-title.portfolio-carousel .owl-nav,
.owl-carousel.show-nav-title.member-carousel .owl-nav,
.owl-carousel.show-nav-title.product-carousel .owl-nav { <?php echo $right; ?>: <?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* featured box */
.featured-box .box-content { padding: 30px <?php echo $b['grid-gutter-width']; ?>px 10px <?php echo $b['grid-gutter-width']; ?>px; border-top-color: <?php echo if_dark( $color_dark_5, '#dfdfdf' ); ?>; }
@media (max-width: 767px) {
    .featured-box .box-content { padding: 25px <?php echo $b['grid-gutter-width'] / 2; ?>px 5px <?php echo $b['grid-gutter-width'] / 2; ?>px; }
}

/* navs */
.sticky-nav-wrapper { margin: 0 <?php echo (-( $b['grid-gutter-width'] / 2)); ?>px; }

/* pricing table */
.pricing-table { padding: 0 <?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* sections */
.vc_row.section { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
.col-half-section { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; max-width: <?php echo ($b['container-width'] / 2) - ($b['grid-gutter-width'] / 2); ?>px; }
@media (min-width: 992px) and <?php echo $screen_large; ?> {
    .col-half-section { max-width: <?php echo (960 / 2) - ($b['grid-gutter-width'] / 2); ?>px; }
}
@media (max-width: 991px) {
    .col-half-section { max-width: <?php echo (720 / 2) - ($b['grid-gutter-width'] / 2); ?>px; }
    .col-half-section.col-fullwidth-md { max-width: 720px; float: none !important; margin-left: auto !important; margin-right: auto !important; -webkit-align-self: auto; -ms-flex-item-align: auto; align-self: auto; }
}
@media (max-width: 767px) {
    .col-half-section { max-width: 540px; float: none !important; margin-left: auto !important; margin-right: auto !important; -webkit-align-self: auto; -ms-flex-item-align: auto; align-self: auto; }
}
@media (max-width: 575px) {
    .col-half-section { padding-left: 0; padding-right: 0; }
}

/* shortcodes */
.porto-map-section { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
#main.main-boxed .porto-map-section .map-content { padding-left: <?php echo $b['grid-gutter-width']; ?>px; padding-right: <?php echo $b['grid-gutter-width']; ?>px; }
.porto-preview-image,
.porto-image-frame { margin-bottom: <?php echo $b['grid-gutter-width']; ?>px; }
@media (min-width: <?php echo $b['container-width'] + $b['grid-gutter-width']; ?>px) {
    .porto-diamonds > li:nth-child(3) { margin-<?php echo $right; ?>: 8px; }
    .porto-diamonds > li:nth-child(4) { <?php echo $right; ?>: 153px; top: 10px; position: absolute; }
    .porto-diamonds > li:nth-child(5) { margin-<?php echo $left; ?>: 500px; margin-top: -68px; }
    .porto-diamonds > li:nth-child(6) { position: absolute; margin: <?php echo $rtl ? "-7px -30px 0 0" : "-7px 0 0 -30px"; ?>; }
    .porto-diamonds > li:nth-child(7) { position: absolute; margin: <?php echo $rtl ? "92px -128px 0 0" : "92px 0 0 -128px"; ?>; }
    .porto-diamonds .diamond-sm,
    .porto-diamonds .diamond-sm .content { height: 123px; width: 123px; }
    .porto-diamonds .diamond-sm .content img { max-width: 195px; }
}
@media (max-width: <?php echo $b['container-width'] + $b['grid-gutter-width'] - 1; ?>px) {
    .csstransforms3d .porto-diamonds,
    .porto-diamonds { padding-<?php echo $left; ?>: 0; max-width: 935px; }
    .porto-diamonds > li:nth-child(2n+2) { margin-<?php echo $right; ?>: 0; margin-bottom: 130px; }
    .porto-diamonds > li:last-child { margin-bottom: 50px; margin-<?php echo $right; ?>: 36px; margin-top: -100px; padding-<?php echo $left; ?>: 35px; }
}

/* siders */
body.boxed #revolutionSliderCarouselContainer,
#main.main-boxed #revolutionSliderCarouselContainer { margin-left: -<?php echo $b['grid-gutter-width']; ?>px; margin-right: -<?php echo $b['grid-gutter-width']; ?>px; }
@media (max-width: 767px) {
    body.boxed #revolutionSliderCarouselContainer,
    #main.main-boxed #revolutionSliderCarouselContainer { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
}

/* toggles */
.toggle > .toggle-content { padding-<?php echo $left; ?>: <?php echo $b['grid-gutter-width'] / 2 + 5; ?>px; }

/* visual composer */
.vc_row.wpb_row.vc_row-no-padding .vc_column_container.section { padding-left: <?php echo $b['grid-gutter-width']; ?>px; padding-right: <?php echo $b['grid-gutter-width']; ?>px; }
@media (max-width: 767px) {
    .vc_row.wpb_row.vc_row-no-padding .vc_column_container.section { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
}
body.vc_row { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* layouts boxed */
body.boxed .porto-container.container,
#main.main-boxed .porto-container.container { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
body.boxed .vc_row[data-vc-stretch-content].section,
#main.main-boxed .vc_row[data-vc-stretch-content].section { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
@media (min-width: 768px) {
    body.boxed .vc_row[data-vc-stretch-content],
    #main.main-boxed .vc_row[data-vc-stretch-content] { margin-left: -<?php echo $b['grid-gutter-width']; ?>px !important; margin-right: -<?php echo $b['grid-gutter-width']; ?>px !important; max-width: <?php echo 720 + $b['grid-gutter-width']; ?>px; }
}
@media (min-width: 992px) {
    body.boxed .vc_row[data-vc-stretch-content],
    #main.main-boxed .vc_row[data-vc-stretch-content] { max-width: <?php echo 960 + $b['grid-gutter-width']; ?>px; }
}
body.boxed #main.wide .vc_row[data-vc-stretch-content] .porto-wrap-container { padding-left: <?php echo $b['grid-gutter-width']; ?>px; padding-right: <?php echo $b['grid-gutter-width']; ?>px; }
@media (max-width: 767px) {
    body.boxed #main.wide .vc_row[data-vc-stretch-content] .porto-wrap-container { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
}
body.boxed #main.wide .container .vc_row { margin-left: -<?php echo $b['grid-gutter-width']; ?>px; margin-right: -<?php echo $b['grid-gutter-width']; ?>px; padding-left: <?php echo $b['grid-gutter-width']; ?>px; padding-right: <?php echo $b['grid-gutter-width']; ?>px; }
@media (max-width: 767px) {
    body.boxed #main.wide .container .vc_row { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
}
body.boxed #main.wide .container .vc_row .vc_row { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
@media (min-width: 768px) {
    body.boxed #header.sticky-header .header-main.sticky { max-width: <?php echo 720 + $b['grid-gutter-width']; ?>px; }
}
@media (min-width: 992px) {
    body.boxed #header.sticky-header .header-main.sticky,
    body.boxed #header.sticky-header .main-menu-wrap { max-width: <?php echo 960 + $b['grid-gutter-width']; ?>px; }
}
#breadcrumbs-boxed .page-top { padding: 13px <?php echo $b['grid-gutter-width'] / 2; ?>px 3px <?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* layouts defaults */
body.wide .container:not(.inner-container) { padding-left: <?php echo $b['grid-gutter-width']; ?>px; padding-right: <?php echo $b['grid-gutter-width']; ?>px; }
#main.wide .container .vc_row,
#main.wide > .container > .row { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* member */
.member-row { margin: 0 -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
.member-row .member { padding: 0 <?php echo $b['grid-gutter-width'] / 2; ?>px; margin-bottom: <?php echo $b['grid-gutter-width']; ?>px; }
.member-carousel .member-item { margin-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }

/* home */
body .menu-ads-container { margin-left: -<?php echo 20 + $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo 20 + $b['grid-gutter-width'] / 2; ?>px; }
body .ads-container-blue,
body.boxed .ads-container-full,
#main.main-boxed .ads-container-full,
body.boxed #main.wide .ads-container-full { margin-left: -<?php echo $b['grid-gutter-width']; ?>px !important; margin-right: -<?php echo $b['grid-gutter-width']; ?>px !important; }
@media (max-width: 767px) {
    body.boxed .ads-container-full,
    #main.main-boxed .ads-container-full,
    body.boxed #main.wide .ads-container-full { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px !important; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px !important; }
}

/* portfolio */
.popup-inline-content hr.solid,
.mfp-content .ajax-container hr.solid,
body.boxed .portfolio .portfolio-image.wide,
body.boxed .portfolio hr.solid,
body.boxed #portfolioAjaxBox .portfolio-image.wide,
body.boxed #portfolioAjaxBox hr.solid,
#main.main-boxed .portfolio .portfolio-image.wide,
#main.main-boxed .portfolio hr.solid,
#main.main-boxed #portfolioAjaxBox .portfolio-image.wide,
#main.main-boxed #portfolioAjaxBox hr.solid,
body.boxed .portfolio-row.full { margin-left: -<?php echo $b['grid-gutter-width']; ?>px; margin-right: -<?php echo $b['grid-gutter-width']; ?>px; }
.popup-inline-content .portfolio-image.wide { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
@media (max-width: 767px) {
    .popup-inline-content .portfolio-image.wide { margin-left: -<?php echo $b['grid-gutter-width'] / 4; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 4; ?>px; }
    body.boxed .portfolio .portfolio-image.wide,
    body.boxed .portfolio hr.solid,
    body.boxed #portfolioAjaxBox .portfolio-image.wide,
    body.boxed #portfolioAjaxBox hr.solid,
    #main.main-boxed .portfolio .portfolio-image.wide,
    #main.main-boxed .portfolio hr.solid,
    #main.main-boxed #portfolioAjaxBox .portfolio-image.wide,
    #main.main-boxed #portfolioAjaxBox hr.solid,
    body.boxed .portfolio-row.full { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
}
.portfolio-carousel .portfolio-item { margin-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
.portfolio-row { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
.portfolio-row .portfolio { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; margin-bottom: <?php echo $b['grid-gutter-width']; ?>px; }
.portfolio-modal .vc_row[data-vc-full-width],
body.boxed .portfolio-modal .vc_row[data-vc-full-width],
#main.main-boxed .portfolio-modal .vc_row[data-vc-full-width],
.portfolio-modal .vc_row[data-vc-stretch-content],
body.boxed .portfolio-modal .vc_row[data-vc-stretch-content],
#main.main-boxed .portfolio-modal .vc_row[data-vc-stretch-content],
.portfolio-ajax-modal .vc_row[data-vc-full-width],
body.boxed .portfolio-ajax-modal .vc_row[data-vc-full-width],
#main.main-boxed .portfolio-ajax-modal .vc_row[data-vc-full-width],
.portfolio-ajax-modal .vc_row[data-vc-stretch-content],
body.boxed .portfolio-ajax-modal .vc_row[data-vc-stretch-content],
#main.main-boxed .portfolio-ajax-modal .vc_row[data-vc-stretch-content] { padding-left: <?php echo $b['grid-gutter-width']; ?>px !important; padding-right: <?php echo $b['grid-gutter-width']; ?>px !important; }
@media (max-width: 767px) {
    .portfolio-modal .vc_row[data-vc-full-width],
    body.boxed .portfolio-modal .vc_row[data-vc-full-width],
    #main.main-boxed .portfolio-modal .vc_row[data-vc-full-width],
    .portfolio-modal .vc_row[data-vc-stretch-content],
    body.boxed .portfolio-modal .vc_row[data-vc-stretch-content],
    #main.main-boxed .portfolio-modal .vc_row[data-vc-stretch-content],
    .portfolio-ajax-modal .vc_row[data-vc-full-width],
    body.boxed .portfolio-ajax-modal .vc_row[data-vc-full-width],
    #main.main-boxed .portfolio-ajax-modal .vc_row[data-vc-full-width],
    .portfolio-ajax-modal .vc_row[data-vc-stretch-content],
    body.boxed .portfolio-ajax-modal .vc_row[data-vc-stretch-content],
    #main.main-boxed .portfolio-ajax-modal .vc_row[data-vc-stretch-content] { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px !important; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px !important; }
}

/* shop */
.cross-sells .slider-wrapper .products .product { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
.col2-set { margin-left: -<?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: -<?php echo $b['grid-gutter-width'] / 2; ?>px; }
.col2-set .col-1, .col2-set .col-2 { padding-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; padding-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
.product-carousel.owl-carousel .product { margin-left: <?php echo $b['grid-gutter-width'] / 2; ?>px; margin-right: <?php echo $b['grid-gutter-width'] / 2; ?>px; }
.single-product .variations:after { <?php echo $left; ?>: <?php echo $b['grid-gutter-width'] / 2; ?>px; width: calc(100% - <?php echo $b['grid-gutter-width']; ?>px); }

/*------ Screen Large Variable ------- */
@media <?php echo $screen_large; ?> {
    /*#header .header-top .porto-view-switcher > li.menu-item > a,
    #header .header-top .top-links > li.menu-item > a { padding-top: 3px !important; padding-bottom: 3px !important; }*/

    .mega-menu > li.menu-item > a { padding: 9px 9px 8px; }

    .widget_sidebar_menu .widget-title { font-size: 0.8571em; line-height: 13px; padding: 10px 15px; }

    .sidebar-menu > li.menu-item > a { font-size: 0.9286em; line-height: 17px; padding: 9px 5px; }
    .sidebar-menu .menu-custom-block a { font-size: 0.9286em; line-height: 16px; padding: 9px 5px; }

    .porto-links-block { font-size: 13px; }
    /*.porto-links-block .links-title { padding: 8px 12px 6px; }
    .porto-links-block li.porto-links-item > a,
    .porto-links-block li.porto-links-item > span { padding: 7px 5px; line-height: 19px; margin: 0 7px -1px; }*/

    body .sidebar-menu .menu-ads-container .vc_column_container .porto-sicon-box.left-icon { padding: 15px 0; }
    body .sidebar-menu .menu-ads-container .vc_column_container .left-icon .porto-sicon-left { display: block; }
    body .sidebar-menu .menu-ads-container .vc_column_container .left-icon .porto-sicon-left .porto-icon { font-size: 25px !important; margin-bottom: 10px; }
    body .sidebar-menu .menu-ads-container .vc_column_container .left-icon .porto-sicon-body { display: block; text-align: center; }


<?php if ( class_exists( 'Woocommerce' ) ) : ?>
    ul.pcols-md-6 { margin: 0 -3px; }
    ul.pcols-md-6 li.product-col { max-width: 16.6666%; flex: 0 0 16.6666%; padding: 0 3px; }
    ul.pwidth-md-6 .product-image { font-size: 0.8em; }
    ul.pwidth-md-6 .add-links { font-size: 0.85em; }
    ul.pcols-md-5 { margin: 0 -6px; }
    ul.pcols-md-5 li.product-col { max-width: 20%; flex: 0 0 20%; padding: 0 6px; }
    ul.pwidth-md-5 .product-image { font-size: 0.9em; }
    ul.pwidth-md-5 .add-links { font-size: 0.95em; }
    ul.pcols-md-4 { margin: 0 -8px; }
    ul.pcols-md-4 li.product-col { max-width: 25%; flex: 0 0 25%; padding: 0 8px; }
    ul.pwidth-md-4 .product-image { font-size: 1em; }
    ul.pwidth-md-4 .add-links { font-size: 1em; }
    ul.pcols-md-3 { margin: 0 -10px; }
    ul.pcols-md-3 li.product-col { max-width: 33.3333%; flex: 0 0 33.3333%; padding: 0 10px; }
    ul.pwidth-md-3 .product-image { font-size: 1.15em; }
    ul.pwidth-md-3 .add-links { font-size: 1em; }
    ul.pcols-md-2 { margin: 0 -12px; }
    ul.pcols-md-2 li.product-col { max-width: 50%; flex: 0 0 50%; padding: 0 12px; }
    ul.pwidth-md-2 .product-image { font-size: 1.4em; }
    ul.pwidth-md-2 .add-links { font-size: 1em; }


    ul.list.pcols-md-6 li.product .product-image { width: 17%; font-size: 0.75em; }
    ul.list.pcols-md-6 li.product .product-inner > * { padding-<?php echo $left; ?>: 18.8%; }
    ul.list.pcols-md-5 li.product .product-image { width: 20%; font-size: 0.8em; }
    ul.list.pcols-md-5 li.product .product-inner > * { padding-<?php echo $left; ?>: 21.8%; }
    ul.list.pcols-md-2 li.product .product-image,
    ul.list.pcols-md-3 li.product .product-image,
    ul.list.pcols-md-4 li.product .product-image { width: 20%; font-size: 0.8em; }
    ul.list.pcols-md-2 li.product .product-inner > *,
    ul.list.pcols-md-3 li.product .product-inner > *,
    ul.list.pcols-md-4 li.product .product-inner > * { padding-<?php echo $left; ?>: 21.8%; }

<?php endif; ?>
}

@media (min-width: 992px) and <?php echo $screen_large; ?> {
    .member-row .member-col-6 { width: 20%; }

    .portfolio-row .portfolio-col-6 { width: 20%; }
    .portfolio-row .portfolio-col-6.w2 { width: 40%; }

    .ccols-lg-2 > * {
        flex: 0 0 50%;
        max-width: 50%;
    }
    .ccols-lg-3 > * {
        flex: 0 0 33.3333%;
        max-width: 33.3333%;
    }
    .ccols-lg-4 > * {
        flex: 0 0 25%;
        max-width: 25%;
    }
    .ccols-lg-5 > * {
        flex: 0 0 20%;
        max-width: 20%;
    }
    .ccols-lg-6 > * {
        flex: 0 0 16.6666%;
        max-width: 16.6666%;
    }
    .ccols-lg-7 > * {
        flex: 0 0 14.2857%;
        max-width: 14.2857%;
    }

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
    .column2 ul.pcols-md-5 { margin: 0 -3px; }
    .column2 ul.pcols-md-5 li.product-col { padding: 0 3px; }
    .column2 ul.pwidth-md-5 .product-image { font-size: 0.75em; }
    .column2 ul.pwidth-md-5 .add-links { font-size: 0.8em; }
    .column2 ul.pcols-md-4 { margin: 0 -5px; }
    .column2 ul.pcols-md-4 li.product-col { padding: 0 5px; }
    .column2 ul.pwidth-md-4 .product-image { font-size: 0.8em; }
    .column2 ul.pwidth-md-4 .add-links { font-size: 0.9em; }
    .column2 ul.pcols-md-3 { margin: 0 -7px; }
    .column2 ul.pcols-md-3 li.product-col { padding: 0 7px; }
    .column2 ul.pwidth-md-3 .product-image { font-size: 0.9em; }
    .column2 ul.pwidth-md-3 .add-links { font-size: 1em; }
    .column2 ul.pcols-md-2 { margin: 0 -10px; }
    .column2 ul.pcols-md-2 li.product-col { padding: 0 10px; }
    .column2 ul.pwidth-md-2 .product-image { font-size: 1.1em; }
    .column2 ul.pwidth-md-2 .add-links { font-size: 1em; }

    .column2 ul.list.pcols-lg-6 li.product .product-image,
    .column2 ul.list.pcols-lg-5 li.product .product-image { width: 20%; font-size: 0.8em; }
    .column2 ul.list.pcols-lg-6 li.product .product-inner > *,
    .column2 ul.list.pcols-lg-5 li.product .product-inner > * { padding-<?php echo $left; ?>: 21.8%; }
    .column2 ul.list.pcols-lg-4 li.product .product-inner > *,
    .column2 ul.list.pcols-lg-3 li.product .product-inner > *,
    .column2 ul.list.pcols-lg-2 li.product .product-inner > * { padding-<?php echo $left; ?>: 24.3%; }
    .column2 ul.list.pcols-lg-4 li.product .product-image,
    .column2 ul.list.pcols-lg-3 li.product .product-image,
    .column2 ul.list.pcols-lg-2 li.product .product-image { width: 22.5%; font-size: 0.8em; }

    .column2 .shop-loop-before .woocommerce-pagination ul { margin-<?php echo $left; ?>: -5px; }


    .quickview-wrap { width: 720px; }
    ul.product_list_widget li,
    .widget ul.product_list_widget li { padding-<?php echo $left; ?>: 85px;}
    ul.product_list_widget li .product-image,
    .widget ul.product_list_widget li .product-image { width: 70px; margin-<?php echo $left; ?>: -85px; }
<?php endif; ?>
}

@media (min-width: 768px) and <?php echo $screen_large; ?> {
    .column2 .member-row .member-col-4 { width: 33.33333333%; }
    .column2 .member-row .member-col-5,
    .column2 .member-row .member-col-6 { width: 25%; }

    .column2 .portfolio-row .portfolio-col-4 { width: 33.33333333%; }
    .column2 .portfolio-row .portfolio-col-4.w2 { width: 66.66666666%; }
    .column2 .portfolio-row .portfolio-col-5,
    .column2 .portfolio-row .portfolio-col-6 { width: 25%; }
    .column2 .portfolio-row .portfolio-col-5.w2,
    .column2 .portfolio-row .portfolio-col-6.w2 { width: 50%; }
}

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
    @media (min-width: 768px) and (max-width: 991px) {
        ul.pcols-sm-4,
        ul.pcols-sm-3,
        ul.pcols-sm-2 { margin: 0 -10px; }
        ul.pcols-sm-4 li.product-col { max-width: 25%; flex: 0 0 25%; padding: 0 10px; }
        ul.pcols-sm-3 li.product-col { max-width: 33.3333%; flex: 0 0 33.3333%; padding: 0 10px; }
        ul.pcols-sm-2 li.product-col { max-width: 50%; flex: 0 0 50%; padding: 0 10px; }
    }
    @media (max-width: 767px) {
        ul.pcols-xs-4 { margin: 0 -4px; }
        ul.pcols-xs-4 li.product-col { max-width: 25%; flex: 0 0 25%; padding: 0 4px; }
        ul.pcols-xs-3 { margin: 0 -3px; }
        ul.pcols-xs-3 li.product-col { max-width: 33.3333%; flex: 0 0 33.3333%; padding: 0 3px; }
        ul.pwidth-xs-3 .product-image { font-size: .85em; }
        ul.pwidth-xs-3 .add-links { font-size: .85em; }
        ul.pcols-xs-2 { margin: 0 -6px; }
        ul.pcols-xs-2 li.product-col { max-width: 50%; flex: 0 0 50%; padding: 0 6px; }
        ul.pwidth-xs-2 .product-image { font-size: 1em; }
        ul.pwidth-xs-2 .add-links { font-size: 1em; }
        ul.pcols-xs-1 { margin: 0; }
        ul.pcols-xs-1 li.product-col { max-width: none; flex: 0 0 100%; padding: 0; }
        ul.pwidth-xs-1 .product-image { font-size: 1.2em; }
        ul.pwidth-xs-1 .add-links { font-size: 1em; }

        ul.list.pcols-xs-3 li.product .product-inner > *,
        ul.list.pcols-xs-2 li.product .product-inner > *,
        ul.list.pcols-xs-1 li.product .product-inner > * { padding-<?php echo $left; ?>: 0; }
        ul.list.pcols-xs-3 li.product .product-image,
        ul.list.pcols-xs-2 li.product .product-image,
        ul.list.pcols-xs-1 li.product .product-image { width: 30%; margin-<?php echo $right; ?>: 18px; font-size: .8em; }
        ul.list.pcols-xs-3 li.product.show-outimage-q-onimage-alt > div > *,
        ul.list.pcols-xs-2 li.product.show-outimage-q-onimage-alt > div > *,
        ul.list.pcols-xs-1 li.product.show-outimage-q-onimage-alt > div > * { padding-<?php echo $left; ?>: 0; }
    }
    @media (max-width: 575px) {
        ul.pcols-ls-2 { margin: 0 -4px; }
        ul.pcols-ls-2 li.product-col { max-width: 50%; flex: 0 0 50%; padding: 0 4px; }
        ul.pwidth-ls-2 .product-image { font-size: .8em; }
        ul.pwidth-ls-2 .add-links { font-size: .85em; }
        ul.pcols-ls-1 { margin: 0; }
        ul.pcols-ls-1 li.product-col { max-width: none; flex: 0 0 100%; padding: 0; }
        ul.pwidth-ls-1 .product-image { font-size: 1.1em; }
        ul.pwidth-ls-1 .add-links { font-size: 1em; }

        ul.list.pcols-ls-2 li.product .product-image,
        ul.list.pcols-ls-1 li.product .product-image { width: 40%; margin-<?php echo $right; ?>: 15px; font-size: .75em; }
    }

    ul.list li.product, .column2 ul.list li.product { max-width: none; flex: 0 0 100%; padding: 0; margin-bottom: 2.1429em; text-align: <?php echo $left; ?> }
<?php endif; ?>

/*------ Border Radius ------- */
<?php if ($b['border-radius']) : ?>
    .wcvashopswatchlabel { border-radius: 1px; }

    .accordion-menu .tip,
    #header .searchform .autocomplete-suggestion span.yith_wcas_result_on_sale,
    #header .searchform .autocomplete-suggestion span.yith_wcas_result_featured,
    #header .menu-custom-block .tip,
    .mega-menu .tip,
    #nav-panel .menu-custom-block .tip,
    #side-nav-panel .menu-custom-block .tip,
    .sidebar-menu .tip,
    article.post .post-date .sticky,
    .post-item .post-date .sticky,
    article.post .post-date .format,
    .post-item .post-date .format,
    .thumb-info .thumb-info-type,
    .wcvaswatchinput.active .wcvashopswatchlabel { border-radius: 2px; }
    article.post .post-date .month,
    .post-item .post-date .month { border-radius: 0 0 2px 2px; }
    article.post .post-date .day,
    .post-item .post-date .day { border-radius: 2px 2px 0 0; }
    .pricing-table h3 { border-radius: 2px 2px 0 0; }

    .accordion-menu .arrow,
    #footer .thumbnail img,
    #footer .img-thumbnail img,
    .widget_sidebar_menu,
    .widget_sidebar_menu .widget-title .toggle,
    <?php if ( (class_exists('bbPress') && is_bbpress() ) || ( class_exists('BuddyPress') && is_buddypress() ) ) : ?>
        .bbp-pagination-links a,
        .bbp-pagination-links span.current,
        .bbp-topic-pagination a,
        #bbpress-forums #bbp-your-profile fieldset input,
        #bbpress-forums #bbp-your-profile fieldset textarea,
        #bbpress-forums p.bbp-topic-meta img.avatar,
        #bbpress-forums ul.bbp-reply-revision-log img.avatar,
        #bbpress-forums ul.bbp-topic-revision-log img.avatar,
        #bbpress-forums div.bbp-template-notice img.avatar,
        .widget_display_topics img.avatar,
        .widget_display_replies img.avatar,
        #buddypress div.pagination .pagination-links a,
        #buddypress div.pagination .pagination-links span.current,
        #buddypress form#whats-new-form textarea,
        #buddypress .activity-list .activity-content .activity-header img.avatar,
        #buddypress div.activity-comments form .ac-textarea,
    <?php endif; ?>
    .pagination > a,
    .pagination > span,
    .page-links > a,
    .page-links > span,
    .accordion .card-header,
    .progress-bar-tooltip,
    <?php echo $input_lists; ?>,
    textarea,
    select,
    input[type="submit"],
    .thumb-info img,
    .toggle-simple .toggle > label:after,
    body .btn-sm,
    body .btn-group-sm > .btn,
    body .btn-xs,
    body .btn-group-xs > .btn,
    .widget .tagcloud a,
    .tm-collapse .tm-section-label,
    body .ads-container,
    body .ads-container-light,
    body .ads-container-blue,
    .chosen-container-single .chosen-single,
    .woocommerce-checkout .form-row .chosen-container-single .chosen-single,
    .select2-container .select2-choice,
    .product-nav .product-popup .product-image img,
    div.quantity .minus,
    div.quantity .plus,
    .gridlist-toggle > a,
    .wcvaswatchlabel,
    .widget_product_categories .widget-title .toggle,
    .widget_price_filter .widget-title .toggle,
    .widget_layered_nav .widget-title .toggle,
    .widget_layered_nav_filters .widget-title .toggle,
    .widget_rating_filter .widget-title .toggle,
    ul.product_list_widget li .product-image img,
    .widget ul.product_list_widget li .product-image img,
    .woocommerce-password-strength { border-radius: 3px; }
    .carousel-areas .porto-carousel-wrapper .slick-prev,
    .carousel-areas .porto-carousel-wrapper .slick-next { border-radius: 3px !important; }
    .widget_sidebar_menu .widget-title,
    .member-item.member-item-3 .thumb-info-wrapper img { border-radius: 3px 3px 0 0; }
    body .menu-ads-container { border-radius: 0 0 3px 3px; }
    body .newsletter-banner .widget_wysija_cont .wysija-submit { border-radius: <?php echo $rtl ? "3px 0 0 3px" : "0 3px 3px 0"; ?>; }
    @media (max-width: 767px) {
        body .newsletter-banner .widget_wysija_cont .wysija-submit { border-radius: 3px; }
    }

    #header .porto-view-switcher > li.menu-item > a,
    #header .top-links > li.menu-item > a,
    #header .searchform .autocomplete-suggestion img,
    #mini-cart .cart-popup .widget_shopping_cart_content,
    #header .mobile-toggle,
    .mega-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
    .mega-menu > li.menu-item.active > a,
    .mega-menu > li.menu-item:hover > a,
    .mega-menu .wide .popup,
    .mega-menu .wide .popup li.sub li.menu-item > a,
    .mega-menu .narrow .popup ul.sub-menu ul.sub-menu,
    #nav-panel .mobile-menu li > a,
    .sidebar-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
    .sidebar-menu .wide .popup li.menu-item li.menu-item > a,
    #bbpress-forums div.bbp-forum-author img.avatar,
    #bbpress-forums div.bbp-topic-author img.avatar,
    #bbpress-forums div.bbp-reply-author img.avatar,
    div.bbp-template-notice,
    div.indicator-hint,
    <?php if ( (class_exists('bbPress') && is_bbpress() ) || ( class_exists('BuddyPress') && is_buddypress() ) ) : ?>
        #buddypress .activity-list li.load-more,
        #buddypress .activity-list li.load-newest,
        #buddypress .standard-form textarea,
        #buddypress .standard-form input[type=text],
        #buddypress .standard-form input[type=color],
        #buddypress .standard-form input[type=date],
        #buddypress .standard-form input[type=datetime],
        #buddypress .standard-form input[type=datetime-local],
        #buddypress .standard-form input[type=email],
        #buddypress .standard-form input[type=month],
        #buddypress .standard-form input[type=number],
        #buddypress .standard-form input[type=range],
        #buddypress .standard-form input[type=search],
        #buddypress .standard-form input[type=tel],
        #buddypress .standard-form input[type=time],
        #buddypress .standard-form input[type=url],
        #buddypress .standard-form input[type=week],
        #buddypress .standard-form select,
        #buddypress .standard-form input[type=password],
        #buddypress .dir-search input[type=search],
        #buddypress .dir-search input[type=text],
        #buddypress .groups-members-search input[type=search],
        #buddypress .groups-members-search input[type=text],
        #buddypress button,
        #buddypress a.button,
        #buddypress input[type=submit],
        #buddypress input[type=button],
        #buddypress input[type=reset],
        #buddypress ul.button-nav li a,
        #buddypress div.generic-button a,
        #buddypress .comment-reply-link,
        a.bp-title-button,
        #buddypress div.item-list-tabs ul li.selected a,
        #buddypress div.item-list-tabs ul li.current a,
    <?php endif; ?>
    .posts-grid .grid-box,
    .img-rounded, .rounded,
    .img-thumbnail,
    .img-thumbnail img,
    .img-thumbnail .inner,
    .page-wrapper .fdm-item-image,
    .share-links a,
    .tabs,
    .testimonial.testimonial-style-2 blockquote,
    .testimonial.testimonial-style-3 blockquote,
    .testimonial.testimonial-style-4 blockquote,
    .testimonial.testimonial-style-5 blockquote,
    .testimonial.testimonial-style-6 blockquote,
    .thumb-info,
    .thumb-info .thumb-info-wrapper,
    .thumb-info .thumb-info-wrapper:after,
    section.timeline .timeline-date,
    section.timeline .timeline-box,
    body .btn,
    body .btn-md,
    body .btn-group-md > .btn,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_rounded,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_rounded img,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow img,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_border,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_outline,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_border img,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_outline img,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border img,
    div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_3d img,
    div.wpb_single_image .porto-vc-zoom.porto-vc-zoom-hover-icon:before,
    div.wpb_single_image.vc_box_border,
    div.wpb_single_image.vc_box_outline,
    div.wpb_single_image.vc_box_shadow_border,
    div.wpb_single_image.vc_box_border img,
    div.wpb_single_image.vc_box_outline img,
    div.wpb_single_image.vc_box_shadow_border img,
    .flickr_badge_image,
    .wpb_content_element .flickr_badge_image,
    .tm-collapse,
    .tm-box,
    div.wpcf7-response-output,
    .success-message-container button,
    #header .header-contact .nav-top a,
    #header .header-contact .nav-top span { border-radius: 4px; }
    <?php if ( class_exists( 'Woocommerce' ) ) : ?>
        .product-image .labels .onhot,
        .product-image .labels .onsale,
        .yith-wcbm-badge,
        .summary-before .labels .onhot,
        .summary-before .labels .onsale,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li a,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li a,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li.chosen a,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li a:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li.chosen a,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li a:hover,
        .shop_table.wishlist_table .add_to_cart { border-radius: 4px; }
    <?php endif; ?>
    #header .porto-view-switcher > li.menu-item:hover > a,
    #header .top-links > li.menu-item:hover > a,
    .mega-menu > li.menu-item.has-sub:hover > a,
    html #topcontrol,
    .tabs.tabs-bottom .tab-content,
    .member-item.member-item-3 .thumb-info,
    .member-item.member-item-3 .thumb-info-wrapper { border-radius: 4px 4px 0 0; }
    .mega-menu .wide .popup > .inner,
    .resp-tab-content,
    .tab-content { border-radius: 0 0 4px 4px; }
    .mega-menu .wide.pos-left .popup,
    .mega-menu .narrow.pos-left .popup > .inner > ul.sub-menu { border-radius: <?php echo $rtl ? '4px 0 4px 4px' : '0 4px 4px 4px'; ?>; }
    .mega-menu .wide.pos-right .popup,
    .mega-menu .narrow.pos-right .popup > .inner > ul.sub-menu { border-radius: <?php echo $rtl ? '0 4px 4px 4px' : '4px 0 4px 4px'; ?>; }
    .mega-menu .narrow .popup > .inner > ul.sub-menu { border-radius: <?php echo $rtl ? "4px 0 4px 4px" : "0 4px 4px 4px"; ?>; }
    .owl-carousel.full-width .owl-nav .owl-prev,
    .owl-carousel.big-nav .owl-nav .owl-prev,
    .resp-vtabs .resp-tabs-container { border-radius: <?php echo $rtl ? "4px 0 0 4px" : "0 4px 4px 0"; ?>; }
    .owl-carousel.full-width .owl-nav .owl-next,
    .owl-carousel.big-nav .owl-nav .owl-next { border-radius: <?php echo $rtl ? "0 4px 4px 0" : "4px 0 0 4px"; ?>; }

    @media (min-width: 992px) {
        .header-wrapper.header-side-nav #header .searchform { border-radius: 5px; }
        .header-wrapper.header-side-nav #header .searchform input { border-radius: <?php echo $rtl ? "0 5px 5px 0" : "5px 0 0 5px"; ?>; }
        .header-wrapper.header-side-nav #header .searchform button { border-radius: <?php echo $rtl ? "5px 0 0 5px" : "0 5px 5px 0"; ?>; }
    }
    <?php if ( (class_exists('bbPress') && is_bbpress() ) || ( class_exists('BuddyPress') && is_buddypress() ) ) : ?>
        #buddypress form#whats-new-form #whats-new-avatar img.avatar,
        #buddypress .activity-list li.mini .activity-avatar img.avatar,
        #buddypress .activity-list li.mini .activity-avatar img.FB_profile_pic,
        #buddypress .activity-permalink .activity-list li.mini .activity-avatar img.avatar,
        #buddypress .activity-permalink .activity-list li.mini .activity-avatar img.FB_profile_pic,
        #buddypress div#message p,
        #sitewide-notice p,
        #bp-uploader-warning,
        #bp-webcam-message p.warning,
        #buddypress table.forum td img.avatar,
        #buddypress div#item-header ul img.avatar,
        #buddypress div#item-header ul.avatars img.avatar,
        #buddypress ul.item-list li img.avatar,
        #buddypress div#message-thread img.avatar,
        #buddypress #message-threads img.avatar,
        .widget.buddypress div.item-avatar img.avatar,
        .widget.buddypress ul.item-list img.avatar,
        .bp-login-widget-user-avatar img.avatar { border-radius: 5px; }
        @media only screen and (max-width: 240px) {
            #buddypress ul.item-list li img.avatar { border-radius: 5px; }
        }
    <?php endif; ?>
    @media (max-width: 767px) {
        ul.comments ul.children > li .comment-body,
        ul.comments > li .comment-body { border-radius: 5px; }
    }
    ul.comments .comment-block,
    .pricing-table .plan,
    .tabs-navigation,
    .toggle > label,
    body.boxed .page-wrapper { border-radius: 5px; }
    <?php if ( class_exists( 'Woocommerce' ) ) : ?>
        .add-links .add_to_cart_button,
        .add-links .add_to_cart_read_more,
        .add-links .add_to_cart_button.loading.viewcart-style-1:after,
        .yith-wcwl-add-to-wishlist span.ajax-loading,
        .add-links .quickview.loading:after,
        .commentlist li .comment-text,
        .product-image img,
        .shop_table,
        .product-nav .product-popup .product-image,
        .product-summary-wrap .yith-wcwl-add-to-wishlist a:before,
        .product-summary-wrap .yith-wcwl-add-to-wishlist span:not(.ajax-loading):before,
        ul.product_list_widget li .product-image,
        .widget ul.product_list_widget li .product-image,
        .widget_recent_reviews .product_list_widget li img,
        .widget.widget_recent_reviews .product_list_widget li img { border-radius: 5px; }
        .shop_table thead tr:first-child th:first-child,
        .shop_table thead tr:first-child td:first-child { border-radius: <?php echo $rtl ? "0 5px 0 0" : "5px 0 0 0"; ?>; }
        .shop_table thead tr:first-child th:last-child,
        .shop_table thead tr:first-child td:last-child { border-radius: <?php echo $rtl ? "5px 0 0 0" : "0 5px 0 0"; ?>; }
        .shop_table thead tr:first-child th:only-child,
        .shop_table thead tr:first-child td:only-child { border-radius: 5px 5px 0 0; }
        .shop_table tfoot tr:last-child th:first-child,
        .shop_table tfoot tr:last-child td:first-child { border-radius: <?php echo $rtl ? "0 0 5px 0" : "0 0 0 5px"; ?>; }
        .shop_table tfoot tr:last-child th:last-child,
        .shop_table tfoot tr:last-child td:last-child { border-radius: <?php echo $rtl ? "0 0 0 5px" : "0 0 5px 0"; ?>; }
        .shop_table tfoot tr:last-child th:only-child,
        .shop_table tfoot tr:last-child td:only-child { border-radius: 0 0 5px 5px; }
        @media (max-width: 575px) {
            .commentlist li .comment_container { border-radius: 5px; }
        }
        .add-links .add_to_cart_read_more,
        .add-links .add_to_cart_button,
        .yith-wcwl-add-to-wishlist a,
        .yith-wcwl-add-to-wishlist span,
        .add-links .quickview,
        ul.products li.product-col .links-on-image .add-links-wrap .add-links .add_to_cart_button,
        ul.products li.product-col .links-on-image .add-links-wrap .add-links .add_to_cart_read_more,
        ul.products li.product-col .links-on-image .add-links-wrap .add-links .yith-wcwl-add-to-wishlist > div,
        ul.products li.product-col .links-on-image .add-links-wrap .add-links .quickview { border-radius: 5px !important; }
    <?php endif; ?>
    .br-normal { border-radius: 5px !important; }
    .resp-tabs-list li,
    .nav-tabs li .nav-link,
    .tabs-navigation .nav-tabs > li:first-child .nav-link { border-radius: 5px 5px 0 0; }
    .tabs.tabs-bottom .nav-tabs li .nav-link,
    .tabs-navigation .nav-tabs > li:last-child .nav-link { border-radius: 0 0 5px 5px; }
    .tabs-left .tab-content { border-radius: 0 5px 5px 5px; }
    .tabs-left .nav-tabs > li:first-child .nav-link { border-radius: 5px 0 0 0; }
    .tabs-left .nav-tabs > li:last-child .nav-link { border-radius: 0 0 0 5px; }
    .tabs-right .tab-content { border-radius: 5px 0 5px 5px; }
    .tabs-right .nav-tabs > li:first-child .nav-link { border-radius: 0 5px 0 0; }
    .tabs-right .nav-tabs > li:last-child .nav-link { border-radius: 0 0 5px 0; }
    .resp-tabs-list li:first-child,
    .nav-tabs.nav-justified li:first-child .nav-link,
    .nav-tabs.nav-justified li:first-child .nav-link:hover { border-radius: <?php echo $rtl ? "0 5px 0 0" : "5px 0 0 0"; ?>; }
    .nav-tabs.nav-justified li:last-child .nav-link,
    .nav-tabs.nav-justified li:last-child .nav-link:hover { border-radius: <?php echo $rtl ? "5px 0 0 0" : "0 5px 0 0"; ?>; }
    .resp-tabs-list li:last-child,
    .tabs.tabs-bottom .nav.nav-tabs.nav-justified li:first-child .nav-link { border-radius: <?php echo $rtl ? "0 0 5px 0" : "0 0 0 5px"; ?>; }
    .tabs.tabs-bottom .nav.nav-tabs.nav-justified li:last-child .nav-link { border-radius: <?php echo $rtl ? "0 0 0 5px" : "0 0 5px 0"; ?>; }
    @media (max-width: 575px) {
        .tabs .nav.nav-tabs.nav-justified li:first-child .nav-link,
        .tabs .nav.nav-tabs.nav-justified li:first-child .nav-link:hover { border-radius: 5px 5px 0 0; }
        .tabs.tabs-bottom .nav.nav-tabs.nav-justified li:last-child .nav-link,
        .tabs.tabs-bottom .nav.nav-tabs.nav-justified li:last-child .nav-link:hover { border-radius: 0 0 5px 5px; }
    }

    #mini-cart .cart-popup,
    #header .main-menu,
    .sidebar-menu .narrow .popup ul.sub-menu,
    article .comment-respond input[type="submit"],
    .btn-3d,
    .carousel-areas,
    .stats-block.counter-with-border,
    .gmap-rounded,
    .gmap-rounded .porto_google_map,
    blockquote.with-borders,
    .tparrows,
    .testimonial.testimonial-style-4,
    body .cart-actions .button,
    body .checkout-button,
    body #place_order,
    body .btn-lg,
    body .btn-group-lg > .btn,
    body input.submit.btn-lg,
    body input.btn.btn-lg[type="submit"], 
    body input.button.btn-lg[type="submit"],
    body .return-to-shop .button { border-radius: 6px; }
    #header .porto-view-switcher .narrow .popup > .inner > ul.sub-menu,
    #header .top-links .narrow .popup > .inner > ul.sub-menu { border-radius: 0 0 6px 6px; }
    .mobile-sidebar .sidebar-toggle { border-radius: <?php echo $rtl ? "6px 0 0 6px" : "0 6px 6px 0"; ?>; }
    .sidebar-menu .wide .popup,
    .sidebar-menu .wide .popup > .inner,
    .sidebar-menu .narrow .popup > .inner > ul.sub-menu { border-radius: <?php echo $rtl ? "6px 0 6px 6px" : "0 6px 6px 6px"; ?>; }
    .right-sidebar .sidebar-menu .wide .popup,
    .right-sidebar .sidebar-menu .wide .popup > .inner,
    .right-sidebar .sidebar-menu .narrow .popup > .inner > ul.sub-menu { border-radius: <?php echo $rtl ? "0 6px 6px 6px" : "6px 0 6px 6px"; ?>; }
    <?php if ( class_exists( 'Woocommerce' ) ) : ?>
        .widget_product_categories .widget-title,
        .widget_price_filter .widget-title,
        .widget_layered_nav .widget-title,
        .widget_layered_nav_filters .widget-title,
        .widget_rating_filter .widget-title { border-radius: 6px 6px 0 0; }
        .category-image,
        .widget_product_categories.closed .widget-title,
        .widget_price_filter.closed .widget-title,
        .widget_layered_nav.closed .widget-title,
        .widget_layered_nav_filters.closed .widget-title,
        .widget_rating_filter.closed .widget-title { border-radius: 6px; }
        .shop_table.responsive.cart-total tbody tr:first-child th,
        .shop_table.shop_table_responsive.cart-total tbody tr:first-child th { border-radius: <?php echo $rtl ? "0 6px 0 0" : "6px 0 0 0"; ?>; }
        .shop_table.responsive.cart-total tbody tr:last-child th,
        .shop_table.shop_table_responsive.cart-total tbody tr:last-child th { border-radius: <?php echo $rtl ? "0 0 6px 0" : "0 0 0 6px"; ?>; }
    <?php endif; ?>

    .widget_sidebar_menu.closed .widget-title,
    .img-opacity-effect a img,
    #content .master-slider,
    #content-inner-top .master-slider,
    #content-inner-bottom .master-slider,
    #content .master-slider .ms-slide .ms-slide-bgcont,
    #content-inner-top .master-slider .ms-slide .ms-slide-bgcont,
    #content-inner-bottom .master-slider .ms-slide .ms-slide-bgcont,
    #content .master-slider .ms-slide .ms-slide-bgvideocont,
    #content-inner-top .master-slider .ms-slide .ms-slide-bgvideocont,
    #content-inner-bottom .master-slider .ms-slide .ms-slide-bgvideocont,
    #content .rev_slider_wrapper,
    #content-inner-top .rev_slider_wrapper,
    #content-inner-bottom .rev_slider_wrapper,
    #content .rev_slider_wrapper li.tp-revslider-slidesli,
    #content-inner-top .rev_slider_wrapper li.tp-revslider-slidesli,
    #content-inner-bottom .rev_slider_wrapper li.tp-revslider-slidesli,
    .porto-links-block { border-radius: 7px; }
    .sidebar-menu > li.menu-item:last-child:hover,
    .sidebar-menu .menu-custom-block a:last-child:hover { border-radius: 0 0 7px 7px; }
    .porto-links-block .links-title { border-radius: 7px 7px 0 0; }
    .sidebar-menu > li.menu-item:last-child.menu-item-has-children:hover { border-radius: <?php echo $rtl ? "0 0 7px 0" : "0 0 0 7px"; ?>; }
    .right-sidebar .sidebar-menu > li.menu-item:last-child.menu-item-has-children:hover { border-radius: <?php echo $rtl ? "0 0 0 7px" : "0 0 7px 0"; ?>; }
    .br-thick { border-radius: 7px !important; }
    <?php if ( class_exists( 'Woocommerce' ) ) : ?>
        ul.products li.product-col .links-on-image .add-links-wrap .add-links .quickview { border-radius: <?php echo $rtl ? "7px 0 7px 0" : "0 7px 0 7px"; ?> !important; }
        ul.products li.product-col.show-outimage-q-onimage-alt > div,
        .product-image,
        .widget_product_categories,
        .widget_price_filter,
        .widget_layered_nav,
        .widget_layered_nav_filters,
        .widget_rating_filter,
        .widget_layered_nav .yith-wcan-select-wrapper { border-radius: 7px; }
        ul.products li.product-col.show-outimage-q-onimage .links-on-image .add-links-wrap .add-links .quickview { border-radius: 0 0 7px 7px !important; }
    <?php endif; ?>

    .featured-box,
    .featured-box .box-content,
    .testimonial blockquote { border-radius: 8px; }

    <?php if ( (class_exists('bbPress') && is_bbpress() ) || ( class_exists('BuddyPress') && is_buddypress() ) ) : ?>
        #bbpress-forums #bbp-single-user-details #bbp-user-avatar img.avatar,
        #buddypress div#item-header img.avatar { border-radius: 16px; }
    <?php endif; ?>

    .vc_progress_bar .vc_single_bar.progress,
    .progress,
    .vc_progress_bar .vc_single_bar.progress .vc_bar,
    .progress-bar { border-radius: 25px; }
<?php else : ?>
    <?php if ( class_exists( 'Woocommerce' ) ): ?>
        .wishlist_table .add_to_cart.button,
        .yith-wcwl-add-button a.add_to_wishlist,
        .yith-wcwl-popup-button a.add_to_wishlist,
        .wishlist_table a.ask-an-estimate-button,
        .wishlist-title a.show-title-form,
        .hidden-title-form a.hide-title-form,
        .woocommerce .yith-wcwl-wishlist-new button,
        .wishlist_manage_table a.create-new-wishlist,
        .wishlist_manage_table button.submit-wishlist-changes,
        .yith-wcwl-wishlist-search-form button.wishlist-search-button { border-radius: 0; }
    <?php endif; ?>
<?php endif; ?>

/*------ Thumb Padding ------- */
<?php if ($b['thumb-padding']) : ?>
    .mega-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
    .sidebar-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
    .page-wrapper .fdm-item-image,
    .thumb-info-side-image .thumb-info-side-image-wrapper,
    .flickr_badge_image,
    .wpb_content_element .flickr_badge_image { padding: 4px; }
    .img-thumbnail .zoom { <?php echo $right; ?>: 8px; bottom: 8px; }
    .thumb-info .thumb-info-wrapper { margin: 4px; }
    .thumb-info .thumb-info-wrapper:after { bottom: -4px; top: -4px; left: -4px; right: -4px; }

    .flickr_badge_image,
    .wpb_content_element .flickr_badge_image { border: 1px solid <?php echo $dark ? $color_dark_3 : "#ddd"; ?>; }

    .owl-carousel .img-thumbnail { max-width: 99.5%; }
    <?php if ( class_exists( 'Woocommerce' ) ) : ?>
        .yith-wcbm-badge { margin: 5px; }
        .yith-wcbm-badge img { margin: -5px !important; }
        .product-images .zoom { <?php echo $right; ?>: 8px; bottom: 8px; }

        .product-image-slider.owl-carousel .img-thumbnail { width: 99.5%; padding: 3px; }
        .product-thumbs-slider.owl-carousel .img-thumbnail { width: 99.5%; padding: 3px; }

        ul.list li.product .product-image,
        .column2 ul.list li.product .product-image { padding-<?php echo $left; ?>: 0.2381em !important; }
        .product-image { padding: 0.2381em; }

        ul.products li.product-col .links-on-image .add-links-wrap .add-links .quickview { top: -1px; <?php echo $right; ?>: -1px; }

        .widget_recent_reviews .product_list_widget li img,
        .widget.widget_recent_reviews .product_list_widget li img { border: 1px solid <?php echo $dark ? $color_dark_3 : "#ddd"; ?>; padding: 3px; }

        .product-nav .product-popup .product-image,
        ul.product_list_widget li .product-image,
        .widget ul.product_list_widget li .product-image { padding: 3px; }
    <?php endif; ?>
<?php else : ?>
    .page-wrapper .fdm-item-image,
    .thumb-info { border-width: 0; }
<?php endif; ?>

/*------ Dark version ------- */
<?php if ( (class_exists('bbPress') && is_bbpress() ) || ( class_exists('BuddyPress') && is_buddypress() ) ) : ?>
    .bbp-pagination-links a:hover,
    .bbp-pagination-links span.current:hover { background: <?php echo if_dark( $color_dark_3, '#eee' ); ?>; border: 1px solid <?php echo if_dark( $color_dark_4, '#ddd' ); ?>; }
    #bbpress-forums div.wp-editor-container { border: 1px solid <?php echo if_dark( $color_dark_3, '#dedede' ); ?>; }
    #bbpress-forums #bbp-single-user-details #bbp-user-navigation li.current a { background: <?php echo if_dark( $color_dark_3, '#eee' ); ?>; }
    #buddypress div.pagination .pagination-links a:hover,
    #buddypress div.pagination .pagination-links span.current:hover { background: <?php echo if_dark( $color_dark_3, '#eee' ); ?>; border: 1px solid <?php echo if_dark( $color_dark_4, '#ddd' ); ?>; }
    #buddypress form#whats-new-form textarea { background: <?php echo if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo if_dark( '#999', '#777' ); ?>; }
    #buddypress .activity-list li.load-more a:hover,
    #buddypress .activity-list li.load-newest a:hover { color: <?php echo if_dark( '#999', '#333' ); ?>; }
    #buddypress a.bp-primary-action span,
    #buddypress #reply-title small a span { background: <?php echo if_dark( '#555', '#fff' ); ?>; <?php echo if_dark( '', 'color: #999;' ); ?> }
    #buddypress a.bp-primary-action:hover span,
    #buddypress #reply-title small a:hover span { background: <?php echo if_dark( '#777', '#fff' ); ?>; <?php echo if_dark( '', 'color: #999;' ); ?> }
    #buddypress div.activity-comments ul li { border-top: 1px solid <?php echo if_dark( $color_dark_3, '#eee' ); ?>; }
    #buddypress div.activity-comments form .ac-textarea { background: <?php echo if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo if_dark( '#999', '#777' ); ?>; border: 1px solid <?php echo if_dark( $color_dark_3, '#ccc' ); ?>; }
    #buddypress div.activity-comments form textarea { color: <?php echo if_dark( '#999', '#777' ); ?>; }
    #buddypress #pass-strength-result { border-color: <?php echo if_dark( $color_dark_4, '#ddd' ); ?>; }
    #buddypress .standard-form textarea,
    #buddypress .standard-form input[type=text],
    #buddypress .standard-form input[type=color],
    #buddypress .standard-form input[type=date],
    #buddypress .standard-form input[type=datetime],
    #buddypress .standard-form input[type=datetime-local],
    #buddypress .standard-form input[type=email],
    #buddypress .standard-form input[type=month],
    #buddypress .standard-form input[type=number],
    #buddypress .standard-form input[type=range],
    #buddypress .standard-form input[type=search],
    #buddypress .standard-form input[type=tel],
    #buddypress .standard-form input[type=time],
    #buddypress .standard-form input[type=url],
    #buddypress .standard-form input[type=week],
    #buddypress .standard-form select,
    #buddypress .standard-form input[type=password],
    #buddypress .dir-search input[type=search],
    #buddypress .dir-search input[type=text],
    #buddypress .groups-members-search input[type=search],
    #buddypress .groups-members-search input[type=text] {
    <?php if ( $dark ) : ?>
        border: 1px solid <?php echo $color_dark_3; ?>;
        background: <?php echo $color_dark_3; ?>;
        color: #999;
    <?php endif; ?>
        color: <?php echo if_dark( '#999', '#777' ); ?>;
    }
    #buddypress .standard-form input:focus,
    #buddypress .standard-form textarea:focus,
    #buddypress .standard-form select:focus {
    <?php if ( $dark ) : ?>
        background: $color-dark-3;
    <?php endif; ?>
        color: <?php echo if_dark( '#999', '#777' ); ?>;
    }
    #buddypress table.forum tr td.label {
    <?php if ( $dark ) : ?>
        border-<?php echo $right; ?>-width: 0;
    <?php endif; ?>
        color: <?php echo if_dark( '#fff', '#777' ); ?>;
    }
    #buddypress div.item-list-tabs ul li.selected a span,
    #buddypress div.item-list-tabs ul li.current a span {
        border-color: <?php echo if_dark( $color_dark_3, '#fff' ); ?>;
    <?php if ( $dark ) : ?>
        background-color: <?php echo $color_dark_3; ?>;
    <?php endif; ?>
    }
    <?php if ( $dark ) : ?>
        #buddypress div.pagination .pagination-links a,
        #buddypress div.pagination .pagination-links span.current { background: <?php echo $color_dark_3; ?>; }
        #buddypress div.pagination .pagination-links a.dots,
        #buddypress div.pagination .pagination-links span.current.dots { background: transparent; }
        #buddypress .activity-list li.load-more a,
        #buddypress .activity-list li.load-newest a { color: #777; }
        #buddypress .activity-list li.new_forum_post .activity-content .activity-inner,
        #buddypress .activity-list li.new_forum_topic .activity-content .activity-inner { border-<?php echo $left; ?>: 2px solid <?php echo $color_dark_3; ?>; }
        #buddypress .activity-list .activity-content img.thumbnail { border: 2px solid <?php echo $color_dark_3; ?>; }
        #buddypress .activity-list li.load-more,
        #buddypress .activity-list li.load-newest { background: <?php echo $color_dark_3; ?>; }
        #buddypress div.ac-reply-avatar img { border: 1px solid <?php echo $color_dark_3; ?>; }
        #buddypress #pass-strength-result { background-color: <?php echo $color_dark_3; ?>; }
        #buddypress div#invite-list { background: transparent; }
        #buddypress table.notifications tr.alt td,
        #buddypress table.notifications-settings tr.alt td,
        #buddypress table.profile-settings tr.alt td,
        #buddypress table.profile-fields tr.alt td,
        #buddypress table.wp-profile-fields tr.alt td,
        #buddypress table.messages-notices tr.alt td,
        #buddypress table.forum tr.alt td { background: transparent; }
        #buddypress ul.item-list { border-top: 1px solid <?php echo $color_dark_3; ?>; }
        #buddypress ul.item-list li { border-bottom: 1px solid <?php echo $color_dark_3; ?> }
        #buddypress div.item-list-tabs ul li a span { background: <?php echo $color_dark_3; ?>; border: 1px solid <?php echo $color_dark_3; ?>; }
        #buddypress div.item-list-tabs ul li.selected a span,
        #buddypress div.item-list-tabs ul li.current a span,
        #buddypress div.item-list-tabs ul li a:hover span { background-color: <?php echo $color_dark_3; ?>; border-color: <?php echo $color_dark_3; ?>; }
        #buddypress div#message-thread div.alt { background: <?php echo $color_dark_3; ?>; }
        .bp-avatar-nav ul.avatar-nav-items li.current { border-color: <?php echo $color_dark_4; ?>; }
        .bp-avatar-nav ul { border-color: <?php echo $color_dark_4; ?>; }
        #drag-drop-area { border-color: <?php echo $color_dark_4; ?>; }
        #buddypress input[type="submit"].pending:hover,
        #buddypress input[type="button"].pending:hover,
        #buddypress input[type="reset"].pending:hover,
        #buddypress input[type="submit"].disabled:hover,
        #buddypress input[type="button"].disabled:hover,
        #buddypress input[type="reset"].disabled:hover,
        #buddypress button.pending:hover,
        #buddypress button.disabled:hover,
        #buddypress div.pending a:hover,
        #buddypress a.disabled:hover { border-color: <?php echo $color_dark_3; ?>; }
        #buddypress ul#topic-post-list li.alt { background: transparent; }
        #buddypress table.notifications thead tr,
        #buddypress table.notifications-settings thead tr,
        #buddypress table.profile-settings thead tr,
        #buddypress table.profile-fields thead tr,
        #buddypress table.wp-profile-fields thead tr,
        #buddypress table.messages-notices thead tr,
        #buddypress table.forum thead tr { background: <?php echo $color_dark_3; ?>; }

        #bbpress-forums div.even, #bbpress-forums ul.even { background-color: <?php echo $color_dark; ?>; }
        #bbpress-forums div.odd, #bbpress-forums ul.odd { background-color: <?php echo $color_dark_2; ?>; }
        #bbpress-forums div.bbp-forum-header,
        #bbpress-forums div.bbp-topic-header,
        #bbpress-forums div.bbp-reply-header { background-color: <?php echo $color_dark_5; ?>; }
        #bbpress-forums .status-trash.even, #bbpress-forums .status-spam.even { background-color: <?php echo $color_dark; ?>; }
        #bbpress-forums .status-trash.odd, #bbpress-forums .status-spam.odd { background-color: <?php echo $color_dark_2; ?>; }
        #bbpress-forums ul.bbp-lead-topic,
        #bbpress-forums ul.bbp-topics,
        #bbpress-forums ul.bbp-forums,
        #bbpress-forums ul.bbp-replies,
        #bbpress-forums ul.bbp-search-results { border: 1px solid <?php echo $color_dark_3; ?>; }
        #bbpress-forums li.bbp-header,
        #bbpress-forums li.bbp-footer { background: <?php echo $color_dark_3; ?>; border-top: <?php echo $color_dark_3; ?>; }
        #bbpress-forums li.bbp-header { background: <?php echo $color_dark_4; ?>; }
        #bbpress-forums .bbp-forums-list { border-<?php echo $left; ?>: 1px solid <?php echo $color_dark_4; ?>; }
        #bbpress-forums li.bbp-body ul.forum,
        #bbpress-forums li.bbp-body ul.topic { border-top: 1px solid <?php echo $color_dark_3; ?>; }
        div.bbp-forum-header,
        div.bbp-topic-header,
        div.bbp-reply-header { border-top: 1px solid <?php echo $color_dark_4; ?>; }
        #bbpress-forums div.bbp-topic-content code,
        #bbpress-forums div.bbp-topic-content pre,
        #bbpress-forums div.bbp-reply-content code,
        #bbpress-forums div.bbp-reply-content pre  { background-color: <?php echo $color_dark_4; ?>; border: 1px solid <?php echo $color_dark_4; ?>; }
        .bbp-pagination-links a,
        .bbp-pagination-links span.current,
        .bbp-topic-pagination a { background: <?php echo $color_dark_3; ?>; }
        .bbp-pagination-links a.dots,
        .bbp-pagination-links span.current.dots,
        .bbp-topic-pagination a.dots { background: transparent; }
        #bbpress-forums fieldset.bbp-form { border: 1px solid <?php echo $color_dark_3; ?>; }
        body.topic-edit .bbp-topic-form div.avatar img,
        body.reply-edit .bbp-reply-form div.avatar img,
        body.single-forum .bbp-topic-form div.avatar img,
        body.single-reply .bbp-reply-form div.avatar img { border: 1px solid <?php echo $color_dark_4; ?>; background-color: <?php echo $color_dark_4; ?>; }
        #bbpress-forums  div.bbp-the-content-wrapper div.quicktags-toolbar { background: <?php echo $color_dark_4; ?>; border-bottom-color: <?php echo $color_dark_3; ?>; }
        #bbpress-forums #bbp-your-profile fieldset input,
        #bbpress-forums #bbp-your-profile fieldset textarea { background: <?php echo $color_dark_3; ?>; border: 1px solid <?php echo $color_dark_3; ?>; }
        #bbpress-forums #bbp-your-profile fieldset input:focus,
        #bbpress-forums #bbp-your-profile fieldset textarea:focus { border: 1px solid <?php echo $color_dark_3; ?>; }
        #bbpress-forums #bbp-your-profile fieldset span.description { border: transparent 1px solid; background-color: transparent; }
        .bbp-topics-front ul.super-sticky,
        .bbp-topics ul.super-sticky,
        .bbp-topics ul.sticky,
        .bbp-forum-content ul.sticky { background-color: <?php echo $color_dark_3; ?> !important; }
        #bbpress-forums .bbp-topic-content ul.bbp-topic-revision-log,
        #bbpress-forums .bbp-reply-content ul.bbp-topic-revision-log,
        #bbpress-forums .bbp-reply-content ul.bbp-reply-revision-log { border-top: 1px dotted <?php echo $color_dark_4; ?>; }
        .activity-list li.bbp_topic_create .activity-content .activity-inner,
        .activity-list li.bbp_reply_create .activity-content .activity-inner { border-<?php echo $left; ?>: 2px solid <?php echo $color_dark_4; ?>; }
    <?php endif; ?>
<?php endif; ?>
<?php if ( $dark ) : ?>
    .pagination > a,
    .pagination > span,
    .page-links > a,
    .page-links > span { background: <?php echo $color_dark_3; ?>; }
    .pagination > a.dots,
    .pagination > span.dots,
    .page-links > a.dots,
    .page-links > span.dots { background: transparent; }
    .dir-arrow { background: transparent url(<?php echo porto_uri; ?>/images/arrows-dark.png) no-repeat 0 0; }
    .dir-arrow.arrow-light { background: transparent url(<?php echo porto_uri; ?>/images/arrows.png) no-repeat 0 0; }
    hr, .divider { background-image: -webkit-linear-gradient(<?php echo $left; ?>, transparent, rgba(255, 255, 255, 0.15), transparent); background-image: linear-gradient(to <?php echo $right; ?>, transparent, rgba(255, 255, 255, 0.15), transparent); }
    hr.light { background-image: -webkit-linear-gradient(<?php echo $left; ?>, transparent, rgba(0, 0, 0, 0.15), transparent); background-image:linear-gradient(to <?php echo $right; ?>, transparent, rgba(0, 0, 0, 0.15), transparent); }
    .featured-boxes-style-6 .featured-box .icon-featured { background: <?php echo $color_dark_3; ?>; }
    .featured-boxes-style-7 .featured-box .icon-featured { background: <?php echo $color_dark_3; ?>; }
    .featured-boxes-style-7 .featured-box .icon-featured:after { box-shadow: 3px 3px <?php echo $portoColorLib->darken($color_dark, 3); ?>; }
    .porto-concept { background-image: url(<?php echo porto_uri; ?>/images/concept-dark.png); }
    .porto-concept .process-image { background-image: url(<?php echo porto_uri; ?>/images/concept-item-dark.png); }
    .porto-concept .project-image { background-image: url(<?php echo porto_uri; ?>/images/concept-item-dark.png); }
    .porto-concept .sun { background-image: url(<?php echo porto_uri; ?>/images/concept-icons-dark.png); }
    .porto-concept .cloud { background-image: url(<?php echo porto_uri; ?>/images/concept-icons-dark.png); }
    .porto-map-section { background-image: url(<?php echo porto_uri; ?>/images/map-dark.png); }
    .slider-title .line { background-image: -webkit-linear-gradient(<?php echo $left; ?>, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.15) 70%, rgba(255, 255, 255, 0) 100%); background-image:linear-gradient(to <?php echo $right; ?>, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0.15) 70%, rgba(255, 255, 255, 0) 100%); }
    @media (max-width: 767px) {
        .resp-tab-content:last-child,
        .resp-vtabs .resp-tab-content:last-child { border-bottom: 1px solid <?php echo $color_dark_4; ?> !important; }
    }
    .resp-easy-accordion h2.resp-tab-active { background: <?php echo $color_dark_4; ?> !important; }
    .vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line { background-image: -webkit-linear-gradient(<?php echo $left; ?>, transparent, rgba(255, 255, 255, 0.15));background-image:linear-gradient(to <?php echo $right; ?>, transparent, rgba(255, 255, 255, 0.15)); }
    .vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line { background-image: -webkit-linear-gradient(<?php echo $right; ?>, transparent, rgba(255, 255, 255, 0.15));background-image:linear-gradient(to <?php echo $left; ?>, transparent, rgba(255, 255, 255, 0.15)); }
    .card > .card-header { background-color: <?php echo $color_dark_4; ?>; }
    .btn-default { background-color: <?php echo $color_dark_2; ?> !important; border-color: <?php echo $color_dark_2; ?> !important; }
    .porto-history .thumb { background: transparent url(<?php echo porto_uri; ?>/images/history-thumb-dark.png) no-repeat 0 <?php echo $rtl ? '-200px' : '0'; ?>; }
<?php else: ?>
    .dir-arrow { background: transparent url(<?php echo porto_uri; ?>/images/arrows.png) no-repeat 0 0; }
    .dir-arrow.arrow-light { background: transparent url(<?php echo porto_uri; ?>/images/arrows-dark.png) no-repeat 0 0; }
    hr, .divider { background-image: -webkit-linear-gradient(<?php echo $left; ?>, transparent, rgba(0, 0, 0, 0.15), transparent); background-image:linear-gradient(to <?php echo $right; ?>, transparent, rgba(0, 0, 0, 0.15), transparent); }
    hr.light { background-image: -webkit-linear-gradient(<?php echo $left; ?>, transparent, rgba(255, 255, 255, 0.15), transparent); background-image:linear-gradient(to <?php echo $right; ?>, transparent, rgba(255, 255, 255, 0.15), transparent); }
    .slider-title .line { background-image: -webkit-linear-gradient(<?php echo $left; ?>, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 70%, rgba(0, 0, 0, 0) 100%); background-image: linear-gradient(to <?php echo $right; ?>, rgba(0, 0, 0, 0.15), rgba(0, 0, 0, 0.15) 70%, rgba(0, 0, 0, 0) 100%); }
    .vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line { background-image: -webkit-linear-gradient(<?php echo $left; ?>, transparent, rgba(0, 0, 0, 0.15));background-image:linear-gradient(to <?php echo $right; ?>, transparent, rgba(0, 0, 0, 0.15)); }
    .vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line { background-image: -webkit-linear-gradient(<?php echo $right; ?>, transparent, rgba(0, 0, 0, 0.15));background-image:linear-gradient(to <?php echo $left; ?>, transparent, rgba(0, 0, 0, 0.15)); }
    .porto-history .thumb { background: transparent url(<?php echo porto_uri; ?>/images/history-thumb.png) no-repeat 0 <?php echo $rtl ? '-200px' : '0'; ?>; }
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
    <?php if ( $dark ) : ?>
        .add-links .add_to_cart_button.loading.viewcart-style-1:after,
        .yith-wcwl-add-to-wishlist span.ajax-loading,
        .add-links .quickview.loading:after,
        .wcml-switcher li.loading,
        ul.product_list_widget li .ajax-loading,
        .widget ul.product_list_widget li .ajax-loading { background-color: <?php echo $color_dark_3; ?>; }
        .select2-drop, .select2-drop-active,
        .gridlist-toggle > a,
        .woocommerce-pagination ul li a,
        .woocommerce-pagination ul li span { background: <?php echo $color_dark_3; ?>; }
        .select2-drop input, .select2-drop-active input,
        .select2-drop .select2-results .select2-highlighted,
        .select2-drop-active .select2-results .select2-highlighted { background: <?php echo $color_dark_2; ?>; }
        .woocommerce-pagination ul li a.dots,
        .woocommerce-pagination ul li span.dots { background: transparent; }
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li a:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li span:hover,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
        .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen a:hover,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span,
        .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-color li.chosen span:hover { border-color: #ccc; }
    <?php else : ?>
        .add-links .add_to_cart_button.loading.viewcart-style-1:after,
        .yith-wcwl-add-to-wishlist span.ajax-loading,
        .add-links .quickview.loading:after,
        .wcml-switcher li.loading,
        ul.product_list_widget li .ajax-loading,
        .widget ul.product_list_widget li .ajax-loading { background-color: #fff; }
    <?php endif; ?>
<?php endif; ?>

#header.sticky-header .header-main.sticky,
#header.sticky-header .main-menu-wrap,
.fixed-header #header.sticky-header .main-menu-wrap { box-shadow: 0 1px 0 0 <?php echo $dark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'; ?>; }
#mini-cart .cart-popup .widget_shopping_cart_content { background: <?php echo if_dark( $color_dark_3, '#fff' ); ?>; }
.mega-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper,
.sidebar-menu li.menu-item > a > .thumb-info-preview .thumb-info-wrapper { background: <?php echo if_dark( $color_dark_4, '#fff' ); ?>; }
.mega-menu .wide .popup > .inner,
.sidebar-menu .wide .popup > .inner { background: <?php echo if_dark( $color_dark_3, '#fff' ); ?>; }
.mega-menu .wide .popup li.sub > a,
.sidebar-menu .wide .popup li.sub > a { color: <?php echo if_dark( '#fff', '#333' ); ?>; }
.mega-menu .wide .popup li.menu-item li.menu-item > a:hover { background: <?php echo if_dark( $portoColorLib->lighten($color_dark_3, 5), '#f4f4f4' ); ?>; }
@media (max-width: 991px) {
    .mobile-sidebar,
    .mobile-sidebar .sidebar-toggle { background: <?php echo if_dark( $dark_bg, '#fff' ); ?>; }
}
.widget_sidebar_menu .widget-title .toggle { color: <?php echo if_dark( '#999', '#ccc' ); ?>; background: <?php echo if_dark( $color_dark, '#fff' ); ?>; border: 1px solid <?php echo $dark ? $color_dark_3 : '#ccc'; ?>; }
.sidebar-menu > li.menu-item > a,
.sidebar-menu .menu-custom-block a { border-top: 1px solid <?php echo if_dark( $portoColorLib->lighten( $dark_bg, 12 ), '#ddd' ); ?>; }
.blog-posts article { border-bottom: 1px solid <?php echo if_dark( $color_dark_3, '#ddd' ); ?>; }
.posts-grid .grid-box { border: 1px solid <?php echo if_dark( $color_dark_3, '#e5e5e5' ); ?>; background: <?php echo if_dark( $color_dark_3, '#fff' ); ?>; }
article.post .post-date .day,
.post-item .post-date .day,
ul.comments .comment-block { background: <?php echo if_dark( $color_dark_3, '#f4f4f4' ); ?>; }
.post-item-small { border-top: 1px solid <?php echo if_dark( $color_dark_3, '#ececec' ); ?>; }
.post-block,
.post-share,
article.post .comment-respond,
article.portfolio .comment-respond { border-top: 1px solid <?php echo if_dark( $color_dark_3, '#ddd' ); ?>; }
ul.comments .comment-arrow { border-<?php echo $right; ?>: 15px solid <?php echo if_dark( $color_dark_3, '#f4f4f4' ); ?>; }
@media (max-width: 767px) {
    ul.comments li { border-<?php echo $left; ?>: 8px solid <?php echo if_dark( $color_dark_3, '#ddd' ); ?>; padding-<?php echo $left; ?>: 10px; }
}
.vc_progress_bar .vc_single_bar.progress,
.progress { background: <?php echo if_dark( $color_dark_4, '#fafafa' ); ?>; }
.btn-default { color: <?php echo if_dark( '#777', '#666' ); ?>; }
[type="submit"].btn-default { color: <?php echo if_dark( '#666', '#333' ); ?>; }
.btn-default.btn:hover { color: <?php echo if_dark( '#777', '#333' ); ?>; }
.owl-carousel.top-border { border-top: 1px solid <?php echo if_dark( '#3F4247', '#dbdbdb' ); ?>; }
.slick-slider .slick-dots li i { color: <?php echo if_dark( $color_dark_4.'!important', '#d6d6d6' ); ?>; }
.porto-ajax-loading:after { background-color: <?php echo if_dark( $dark_bg, '#fff' ); ?>; }
hr.solid,
.divider.divider-solid,
.vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line.solid,
.vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line.solid { background: <?php echo if_dark( 'rgba(255, 255, 255, 0.15)', 'rgba(0, 0, 0, 0.15)' ); ?>; }
.divider i { background: <?php echo if_dark( $color_dark, '#fff' ); ?>; }
.divider.divider-style-2 i { background: <?php echo if_dark( $color_dark_2, '#f4f4f4' ); ?>; }
.divider.divider-style-3 i,
.divider.divider-style-4 i { border: 1px solid <?php echo if_dark( '#3f4247', '#cecece' ); ?>; }
.divider.divider-style-4 i:after { border: 3px solid <?php echo if_dark( $color_dark_2, '#f4f4f4' ); ?>; }
.divider.divider-small hr { background: <?php echo if_dark( '#3f4247', '#555' ); ?>; }
.divider.divider-small.divider-light hr { background: <?php echo if_dark( '#3f4247', '#ddd' ); ?>; }
hr.dashed:after,
.divider.dashed:after,
.vc_separator .vc_sep_holder.vc_sep_holder_l .vc_sep_line.dashed:after,
.vc_separator .vc_sep_holder.vc_sep_holder_r .vc_sep_line.dashed:after { border: 1px dashed <?php echo if_dark( 'rgba(255, 255, 255, 0.15)', 'rgba(0, 0, 0, 0.15)' ); ?>; }
.stats-block.counter-with-border,
blockquote.with-borders,
.vc_general.vc_cta3.vc_cta3-style-custom { border-top: 1px solid <?php echo if_dark( $color_dark_4, '#dfdfdf' ); ?>; border-bottom: 1px solid <?php echo if_dark( $color_dark_4, '#dfdfdf' ); ?>; border-left: 1px solid <?php echo if_dark( $color_dark_3, '#ececec' ); ?>; border-right: 1px solid <?php echo if_dark( $color_dark_3, '#ececec' ); ?>; }
.featured-box { background: <?php echo if_dark( $color_dark_4, '#f5f5f5' ); ?>; border-bottom: 1px solid <?php echo if_dark( $color_dark_4, '#dfdfdf' ); ?>; border-left: 1px solid <?php echo if_dark( $color_dark_4, '#ececec' ); ?>; border-right: 1px solid <?php echo if_dark( $color_dark_4, '#ececec' ); ?>; }
<?php if ( !$dark ) : ?>
    .featured-box { background: -webkit-linear-gradient(top, #fff 1%, #f9f9f9 98%) repeat scroll 0 0 #f5f5f5;background:linear-gradient(to bottom, #fff 1%, #f9f9f9 98%) repeat scroll 0 0 #f5f5f5; }
<?php endif; ?>
.resp-tab-content { border: 1px solid <?php echo $dark ? $color_dark_4 : '#eee'; ?>; }
.featured-boxes-style-6 .featured-box .icon-featured,
.feature-box.feature-box-style-6 .feature-box-icon,
.porto-sicon-wrapper.featured-icon .porto-icon { border: 1px solid <?php echo $dark ? $color_dark_4 : '#cecece'; ?>; }
.featured-boxes-style-6 .featured-box .icon-featured:after { border: 5px solid <?php echo $dark ? $color_dark_4 : '#f4f4f4'; ?>; }
.featured-boxes-flat .featured-box .box-content,
.featured-boxes-style-8 .featured-box .icon-featured { background: <?php echo if_dark( $color_dark_4, '#fff' ); ?>; }
.featured-boxes-style-3 .featured-box .icon-featured,
body #wp-link-wrap { background: <?php echo if_dark( $color_dark, '#fff' ); ?>; }
.featured-boxes-style-5 .featured-box .box-content h4,
.featured-boxes-style-6 .featured-box .box-content h4,
.featured-boxes-style-7 .featured-box .box-content h4 { color: <?php echo if_dark( '#fff', $color_dark_4 ); ?>; }
.featured-boxes-style-5 .featured-box .icon-featured,
.featured-boxes-style-6 .featured-box .icon-featured,
.featured-boxes-style-7 .featured-box .icon-featured { background: <?php echo if_dark( $color_dark_3, '#fff' ); ?>; border: 1px solid <?php echo if_dark( $color_dark_4, '#dfdfdf' ); ?>; }
.featured-box-effect-1 .icon-featured:after { box-shadow: 0 0 0 3px <?php echo if_dark( $color_dark_4, '#fff' ); ?>; }
.feature-box.feature-box-style-2 h4,
.feature-box.feature-box-style-3 h4,
.feature-box.feature-box-style-4 h4 { color: <?php echo if_dark( '#fff', $color_dark ); ?>; }
.feature-box.feature-box-style-6 .feature-box-icon:after,
.porto-sicon-wrapper.featured-icon .porto-icon:after { border: 3px solid <?php echo $dark ? $color_dark_4 : '#f4f4f4'; ?>; }
<?php echo $input_lists; ?>,
textarea,
.form-control,
select { background-color: <?php echo if_dark( $color_dark_3, '#fff' ); ?>; color: <?php echo if_dark( '#999', '#777' ); ?>; border-color: <?php echo if_dark( $color_dark_3, '#ccc' ); ?>; }
.form-control:focus { border-color: <?php echo if_dark( $color_dark_4, '#ccc' ); ?>; }
body #wp-link-wrap #link-modal-title { background: <?php echo if_dark( $color_dark_4, '#fcfcfc' ); ?>; border-bottom: 1px solid <?php echo if_dark( $color_dark_4, '#dfdfdf' ); ?>; }
body #wp-link-wrap .submitbox { background: <?php echo if_dark( $color_dark_4, '#fcfcfc' ); ?>; border-top: 1px solid <?php echo if_dark( $color_dark_4, '#dfdfdf' ); ?>; }
.heading.heading-bottom-border h1 { border-bottom: 5px solid <?php echo if_dark( '#3f4247', '#dbdbdb' ); ?>; padding-bottom: 10px; }
.heading.heading-bottom-border h2,
.heading.heading-bottom-border h3 { border-bottom: 2px solid <?php echo if_dark( '#3f4247', '#dbdbdb' ); ?>; padding-bottom: 10px; }
.heading.heading-bottom-border h4,
.heading.heading-bottom-border h5,
.heading.heading-bottom-border h6 { border-bottom: 1px solid <?php echo if_dark( '#3f4247', '#dbdbdb' ); ?>; padding-bottom: 5px; }
.heading.heading-bottom-double-border h1,
.heading.heading-bottom-double-border h2,
.heading.heading-bottom-double-border h3 { border-bottom: 3px double <?php echo if_dark( '#3f4247', '#dbdbdb' ); ?>; padding-bottom: 10px; }
.heading.heading-bottom-double-border h4,
.heading.heading-bottom-double-border h5,
.heading.heading-bottom-double-border h6 { border-bottom: 3px double <?php echo if_dark( '#3f4247', '#dbdbdb' ); ?>; padding-bottom: 5px; }
.heading.heading-middle-border:before { border-top: 1px solid <?php echo if_dark( '#3f4247', '#dbdbdb' ); ?>; }
.heading.heading-middle-border h1,
.heading.heading-middle-border h2,
.heading.heading-middle-border h3,
.heading.heading-middle-border h4,
.heading.heading-middle-border h5,
.heading.heading-middle-border h6,
.dialog { background: <?php echo if_dark( $color_dark, '#fff' ); ?>; }
h1, h2, h3, h4, h5, h6 { color: <?php echo if_dark( '#fff', $color_dark ); ?>; }
.popup-inline-content,
.mfp-content .ajax-container,
.loading-overlay { background: <?php echo if_dark( $dark_bg, '#fff' ); ?>; }
.fontawesome-icon-list > div,
.sample-icon-list > div { color: <?php echo if_dark( '#ddd', '#222' ); ?>; }
.content-grid .content-grid-item:before { border-left: 1px solid <?php echo if_dark( $color_dark_4, '#dadada' ); ?>; }
.content-grid .content-grid-item:after { border-bottom: 1px solid <?php echo if_dark( $color_dark_4, '#dadada' ); ?>; }
.content-grid.content-grid-dashed .content-grid-item:before { border-left: 1px dashed <?php echo if_dark( $color_dark_4, '#dadada' ); ?>; }
.content-grid.content-grid-dashed .content-grid-item:after { border-bottom: 1px dashed <?php echo if_dark( $color_dark_4, '#dadada' ); ?>; }
ul.nav-list li a, ul[class^="wsp-"] li a { border-bottom: 1px solid <?php echo if_dark( $color_dark_3, '#ededde' ); ?>; }
ul.nav-list li a:before, ul[class^="wsp-"] li a:before { border-<?php echo $left; ?>-color: <?php echo if_light( '#333', '#555' ); ?>; }
ul.nav-list li a:hover, ul[class^="wsp-"] li a:hover { background-color: <?php echo if_dark( $color_dark_3, '#eee' ); ?>; text-decoration: none; }
ul.nav-list.show-bg-active .active > a,
ul.nav-list.show-bg-active a.active,
ul[class^="wsp-"].show-bg-active .active > a,
ul[class^="wsp-"].show-bg-active a.active { background-color: <?php echo if_light( '#f5f5f5', $color_dark_4 ); ?>; }
ul.nav-list.show-bg-active .active > a:hover,
ul.nav-list.show-bg-active a.active:hover,
ul[class^="wsp-"].show-bg-active .active > a:hover,
ul[class^="wsp-"].show-bg-active a.active:hover { background-color: <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
.page-wrapper .fdm-item-image { background-color: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border: 1px solid <?php echo if_light( '#ddd', $color_dark_3 ); ?>; padding: 0; }
.pricing-table li { border-top: 1px solid <?php echo if_light( '#ddd', $color_dark_2 ); ?>; }
.pricing-table h3 { background-color: <?php echo if_light( '#eee', $color_dark_2 ); ?>; }
.pricing-table h3 span { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border: 5px solid <?php echo if_light( '#fff', $color_dark_5 ); ?>; box-shadow: 0 5px 20px <?php echo if_light( '#ddd', $color_dark_5 ); ?> inset, 0 3px 0 <?php echo if_light( '#999', $color_dark_3 ); ?> inset; }
.pricing-table .most-popular { border: 3px solid <?php echo if_light( '#ccc', $color_dark_3 ); ?>; }
.pricing-table .most-popular h3 { background-color: <?php echo if_light( '#666', $color_dark_3 ); ?>; text-shadow: <?php echo if_light( '0 1px #555', 'none' ); ?>; }
.pricing-table .plan-ribbon { background-color: <?php echo if_light( '#bfdc7a', $color_dark_3 ); ?>; }
.pricing-table .plan { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border: 1px solid <?php echo if_light( '#ddd', $color_dark_3 ); ?>; text-shadow: <?php echo if_light( '0 1px rgba(255, 255, 255, 0.8)', 'none' ); ?>; }
.pricing-table.pricing-table-sm h3 span { border: 3px solid <?php echo if_light( '#fff', $color_dark_5 ); ?>; box-shadow: 0 5px 20px <?php echo if_light( '#ddd', $color_dark_5 ); ?> inset, 0 3px 0 <?php echo if_light( '#999', $color_dark_3 ); ?> inset; }
.pricing-table.pricing-table-flat .plan-btn-bottom li:last-child { border-bottom: 1px solid <?php echo if_light( '#ddd', $color_dark_2 ); ?>; }
.section { background-color: <?php echo if_light( '#f4f4f4', $color_dark_2 ); ?>; border-top: 5px solid <?php echo if_light( '#f1f1f1', $color_dark_3 ); ?>; }
.porto-map-section .map-content { background-color: <?php echo if_light( 'rgba(244, 244, 244, 0.8)', 'rgba(33, 38, 45, 0.8)' ); ?>; border-top: 5px solid <?php echo if_light( 'rgba(241, 241, 241, 0.8)', 'rgba(40, 45, 54, 0.8)' ); ?>; }
#revolutionSliderCarousel { border-top: 1px solid <?php echo if_light( 'rgba(0, 0, 0, 0.15)', 'rgba(255, 255, 255, 0.15)' ); ?>; border-bottom: 1px solid <?php echo if_light( 'rgba(0, 0, 0, 0.15)', 'rgba(255, 255, 255, 0.15)' ); ?>; }
@media (max-width: 767px) {
    .resp-tab-content,
    .resp-vtabs .resp-tab-content { border-color: <?php echo if_light( '#ddd', $color_dark_4 ); ?>; }
}
.resp-tabs-list { border-bottom: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.resp-tabs-list li,
.resp-tabs-list li:hover,
.nav-tabs li .nav-link,
.nav-tabs li .nav-link:hover { background: <?php echo if_light( '#f4f4f4', $color_dark_3 ); ?>; border-left: 1px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; border-right: 1px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; border-top: 3px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
.resp-tabs-list li.resp-tab-active { background: <?php echo if_light( '#fff', $color_dark_4 ); ?>; border-left: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?>; border-right: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.resp-vtabs .resp-tabs-container { border: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?>; background: <?php echo if_light( '#fff', $color_dark_4 ); ?>; }
.resp-vtabs .resp-tabs-list li:first-child { border-top: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?> !important; }
.resp-vtabs .resp-tabs-list li:last-child { border-bottom: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?> !important; }
.resp-vtabs .resp-tabs-list li,
.resp-vtabs .resp-tabs-list li:hover { border-<?php echo $left; ?>: 3px solid <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.resp-vtabs .resp-tabs-list li.resp-tab-active { background: <?php echo if_light( '#fff', $color_dark_4 ); ?>; }
h2.resp-accordion { background: <?php echo if_light( '#f5f5f5', $color_dark_3 ); ?> !important; border-color: <?php echo if_light( '#ddd', $color_dark_4 ); ?>; }
h2.resp-accordion:first-child { border-top-color: <?php echo if_light( '#ddd', $color_dark_4 ); ?> !important; }
h2.resp-tab-active { background: <?php echo if_light( '#f5f5f5', $color_dark_3 ); ?> !important; border-bottom: 1px solid <?php echo if_light( '#ddd', $color_dark_3 ); ?> !important; }
.resp-easy-accordion .resp-tab-content { border-color: <?php echo if_light( '#ddd', $color_dark_4 ); ?>; background: <?php echo if_light( '#fff', $color_dark_4 ); ?>; }
.resp-easy-accordion .resp-tab-content:last-child { border-color: <?php echo if_light( '#ddd', $color_dark_3 ); ?> !important; }
.nav-tabs { border-bottom-color: <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
.nav-tabs li .nav-link:hover { border-top-color: <?php echo if_light( '#ccc', '#808697' ); ?>; }
.nav-tabs li.active a,
.nav-tabs li.active a:hover,
.nav-tabs li.active a:focus { background: <?php echo if_light( '#fff', $color_dark_4 ); ?>; border-left-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; border-right-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; border-top: 3px solid <?php echo if_light( '#ccc', '#808697' ); ?>; }
.tab-content { background: <?php echo if_light( '#fff', $color_dark_4 ); ?>; border-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.tabs.tabs-bottom .tab-content,
.tabs.tabs-bottom .nav-tabs { border-bottom: none; border-top: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.tabs.tabs-bottom .nav-tabs li .nav-link { border-bottom-color: <?php echo if_light( '#eee', $color_dark_3 ); ?>; border-top: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?> !important; }
.tabs.tabs-bottom .nav-tabs li .nav-link:hover { border-bottom-color: <?php echo if_light( '#ccc', '#808697' ); ?>; }
.tabs.tabs-bottom .nav-tabs li.active a,
.tabs.tabs-bottom .nav-tabs li.active a:hover,
.tabs.tabs-bottom .nav-tabs li.active a:focus { border-bottom: 3px solid <?php echo if_light( '#ccc', '#808697' ); ?>; border-top-color: transparent !important; }
.tabs-vertical { border-top-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.tabs-left .nav-tabs > li:last-child .nav-link,
.tabs-right .nav-tabs > li:last-child .nav-link,
.nav-tabs.nav-justified li .nav-link,
.nav-tabs.nav-justified li .nav-link:hover,
.nav-tabs.nav-justified li .nav-link:focus { border-bottom: 1px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
.tabs-left .nav-tabs > li .nav-link { border-<?php echo $right; ?>: 1px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; border-<?php echo $left; ?>: 3px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
.tabs-left .nav-tabs > li.active .nav-link,
.tabs-left .nav-tabs > li.active .nav-link:hover,
.tabs-left .nav-tabs > li.active .nav-link:focus { border-<?php echo $right; ?>-color: <?php echo if_light( '#fff', $color_dark_4 ); ?>; }
.tabs-right .nav-tabs > li .nav-link { border-<?php echo $right; ?>: 3px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; border-<?php echo $left; ?>: 1px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
.tabs-right .nav-tabs > li.active .nav-link,
.tabs-right .nav-tabs > li.active .nav-link:hover,
.tabs-right .nav-tabs > li.active .nav-link:focus { border-<?php echo $left; ?>-color: <?php echo if_light( '#fff', $color_dark_4 ); ?>; }
.nav-tabs.nav-justified li.active .nav-link,
.nav-tabs.nav-justified li.active .nav-link:hover,
.nav-tabs.nav-justified li.active .nav-link:focus { background: <?php echo if_light( '#fff', $color_dark_4 ); ?>; border-left-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; border-right-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; border-top-width: 3px; border-bottom: 1px solid <?php echo if_light( '#fff', $color_dark_4 ); ?>; }
.tabs.tabs-bottom .nav.nav-tabs.nav-justified li .nav-link { border-top: 1px solid <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
.tabs.tabs-bottom .nav.nav-tabs.nav-justified li.active .nav-link,
.tabs.tabs-bottom .nav.nav-tabs.nav-justified li.active .nav-link:hover,
.tabs.tabs-bottom .nav.nav-tabs.nav-justified li.active .nav-link:focus { border-top: 1px solid <?php echo if_light( '#fff', $color_dark_4 ); ?>; }
.tabs-navigation .nav-tabs > li:first-child .nav-link { border-top: 1px solid <?php echo if_light( '#eee', $color_dark_4 ); ?> !important; }
.tabs-navigation .nav-tabs > li.active .nav-link,
.tabs-navigation .nav-tabs > li.active .nav-link:hover,
.tabs-navigation .nav-tabs > li.active .nav-link:focus { border-left-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; border-right-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.tabs.tabs-simple .nav-tabs > li .nav-link,
.tabs.tabs-simple .nav-tabs > li .nav-link:hover,
.tabs.tabs-simple .nav-tabs > li .nav-link:focus { border-bottom-color: <?php echo if_light( '#eee', $color_dark_4 ); ?>; }
.testimonial .testimonial-author strong { color: <?php echo if_light( '#111', '#fff' ); ?>; }
.testimonial.testimonial-style-3 blockquote { background: <?php echo if_light( '#f2f2f2', $color_dark_4 ); ?>; }
.testimonial.testimonial-style-3 .testimonial-arrow-down { border-top: 10px solid <?php echo if_light( '#f2f2f2', $color_dark_4 ); ?> !important; }
.testimonial.testimonial-style-4 { border-top-color: <?php echo if_light( '#dfdfdf', $color_dark_4 ); ?>; border-bottom-color: <?php echo if_light( '#dfdfdf', $color_dark_4 ); ?>; border-left-color: <?php echo if_light( '#ececec', $color_dark_4 ); ?>; border-right-color: <?php echo if_light( '#ececec', $color_dark_4 ); ?>; }
.testimonial.testimonial-style-5 .testimonial-author { border-top: 1px solid <?php echo if_light( '#f2f2f2', $color_dark_4 ); ?>; }
.thumb-info { background-color: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border-color: <?php echo if_light( '#ddd', $color_dark_3 ); ?>; }
.thumb-info .thumb-info-wrapper:after { background: <?php echo if_light( 'rgba(23, 23, 23, 0.8)', 'rgba('. $portoColorLib->hexToRGB( $color_dark ) .', 0.9)' ); ?>; }
.thumb-info.thumb-info-bottom-info:not(.thumb-info-bottom-info-dark) .thumb-info-title { background: <?php echo if_light( '#fff', $color_dark ); ?>; }
.thumb-info-side-image { border: 1px solid <?php echo if_light( '#ddd', $color_dark_3 ); ?>; }
.thumb-info-social-icons { border-top: <?php echo if_light( '1px dotted #ddd', '1px solid ' . $portoColorLib->lighten( $dark_bg, 12 ) ); ?>; }
section.timeline .timeline-date { border: 1px solid <?php echo if_light( '#e5e5e5', $color_dark_3 ); ?>; background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; text-shadow: <?php echo if_light( '0 1px 1px #fff', 'none' ); ?>; }
section.timeline .timeline-title { background: <?php echo if_light( '#f4f4f4', $color_dark_3 ); ?>; }
section.timeline .timeline-box { border: 1px solid <?php echo if_light( '#e5e5e5', $color_dark_3 ); ?>; background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; }
section.timeline .timeline-box.left:after { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border-<?php echo $right; ?>: 1px solid <?php echo if_light( '#e5e5e5', $color_dark_3 ); ?>; border-top: 1px solid <?php echo if_light( '#e5e5e5', $color_dark_3 ); ?>; }
section.timeline .timeline-box.right:after { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border-<?php echo $left; ?>: 1px solid <?php echo if_light( '#e5e5e5', $color_dark_3 ); ?>; border-bottom: 1px solid <?php echo if_light( '#e5e5e5', $color_dark_3 ); ?>; }
section.exp-timeline .timeline-box.right:after { border: none; }
.toggle > label { background: <?php echo if_light( '#f4f4f4', $color_dark_4 ); ?>; }
.toggle > label:hover { background: <?php echo if_light( '#f5f5f5', $color_dark_3 ); ?>; }
.toggle.active > label { background: <?php echo if_light( '#f4f4f4', $color_dark_3 ); ?>; }
.toggle-simple .toggle > label,
.toggle-simple .toggle.active > label { color: <?php echo if_light( $color_dark, '#fff' ); ?>; }
div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border,
div.wpb_single_image .vc_single_image-wrapper.vc_box_shadow_border_circle,
.product-image,
.product-image .viewcart,
.product-image .stock { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; }
div.wpb_single_image .vc_single_image-wrapper.vc_box_outline.vc_box_border_grey,
div.wpb_single_image .vc_single_image-wrapper.vc_box_outline_circle.vc_box_border_grey { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border-color: <?php echo if_light( '#ddd', $color_dark_3 ); ?>; }
.toggle-simple .toggle.active > label { color: <?php echo if_light( $color_dark, '#fff' ); ?>; }
.porto-links-block .links-title { color: <?php echo if_light( '#465157', '#fff' ); ?>; }
.porto-links-block li.porto-links-item > a,
.porto-links-block li.porto-links-item > span { border-top: 1px solid <?php echo if_light( '#ddd', $portoColorLib->lighten( $dark_bg, 12 ) ); ?>; }
.widget > div > ul,
.widget > ul { border-bottom-color: <?php echo if_light( '#ededed', $color_dark_3 ); ?>; }
.widget > div > ul li,
.widget > ul li { border-top-color: <?php echo if_light( '#ededed', $color_dark_3 ); ?>; }
.widget_recent_entries > ul li:before,
.widget_recent_comments > ul li:before,
.widget_pages > ul li:before,
.widget_meta > ul li:before,
.widget_nav_menu > div > ul li:before,
.widget_archive > ul li:before,
.widget_categories > ul li:before,
.widget_rss > ul li:before { border-<?php echo $left; ?>: 4px solid <?php echo if_light( '#333', '#555' ); ?>; }
.widget .tagcloud a { border: 1px solid <?php echo if_light( '#ccc', $color_dark_4 ); ?>; background: <?php echo if_light( '#efefef', $color_dark_4 ); ?>; }
.flickr_badge_image,
.wpb_content_element .flickr_badge_image { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; }
.sidebar-content .widget.widget_wysija, .sidebar-content .wpcf7-form .widget_wysija { background: <?php echo if_light( '#f4f4f4', $color_dark_4 ); ?>; }
.tm-collapse .tm-section-label { background: <?php echo if_light( '#f5f5f5', $color_dark_3 ); ?>; }
.tm-box { border: 1px solid <?php echo if_light( '#ddd', $color_dark_3 ); ?>; }
body.boxed .page-wrapper,
#content-top,
#content-bottom,
.member-item.member-item-3 .thumb-info-caption { background: <?php echo if_light( '#fff', $color_dark ); ?>; }
body { background: <?php echo if_light( '#fff', '#000' ); ?>; }
#main { background: <?php echo if_light( '#fff', $dark_bg ); ?>; }
.member-share-links { border-top: 1px solid <?php echo if_light( '#ddd', $portoColorLib->lighten( $color_dark, 12 ) ); ?>; }
body .menu-ads-container { background: <?php echo if_light( '#f6f6f6', $color_dark_4 ); ?>; border: 2px solid <?php echo if_light( '#fff', $color_dark_3 ); ?>; }
body .menu-ads-container .vc_column_container { border-<?php echo $left; ?>: 2px solid <?php echo if_light( '#fff', $color_dark_3 ); ?>; }
.portfolio-info ul li { border-<?php echo $right; ?>: 1px solid <?php echo if_light( '#e6e6e6', $color_dark_4 ); ?>; }
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
    .add-links .add_to_cart_button,
    .add-links .add_to_cart_read_more { border: 1px solid <?php echo if_light( '#ccc', '#777' ); ?>; color: <?php echo if_light( '#2b2b2b', '#ddd' ); ?>; }
    @media (max-width: 575px) {
        .commentlist li .comment_container { background: <?php echo if_light( '#f5f7f7', $color_dark_3 ); ?>; }
    }
    .commentlist li .comment-text { background: <?php echo if_light( '#f5f7f7', $color_dark_3 ); ?>; }
    .product-image .stock { background: <?php echo if_light( 'rgba(255, 255, 255, .9)', 'rgba(0, 0, 0, .9)' ); ?>; }
    .shop_table { border: 1px solid <?php echo if_light( '#dcdcdc', $color_dark_3 ); ?>; }
    .shop_table td,
    .shop_table tbody th,
    .shop_table tfoot th { border-<?php echo $left; ?>: 1px solid <?php echo if_light( '#dcdcdc', $color_dark_3 ); ?>; border-top: 1px solid <?php echo if_light( '#ddd', $color_dark_3 ); ?>; }
    .shop_table th { background: <?php echo if_light( '#f6f6f6', $color_dark_3 ); ?>; }
    @media (max-width: 767px) {
        .shop_table.shop_table_responsive tr,
        .shop_table.responsive tr,
        .shop_table.shop_table_responsive tfoot tr:first-child,
        .shop_table.responsive tfoot tr:first-child { border-top: 1px solid <?php echo if_light( '#ddd', $color_dark_3 ); ?>; }
    }
    .featured-box .shop_table .quantity input.qty { border-color: <?php echo if_light( '#c8bfc6', 'transparent' ); ?>; }
    .featured-box .shop_table .quantity .minus,
    .featured-box .shop_table .quantity .plus { background: <?php echo if_light( '#f4f4f4', $color_dark_2 ); ?>; border-color: <?php echo if_light( '#c8bfc6', 'transparent' ); ?>; }
    .chosen-container-single .chosen-single,
    .woocommerce-checkout .form-row .chosen-container-single .chosen-single,
    .select2-container .select2-choice { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; border-color: <?php echo if_light( '#ccc', $color_dark_3 ); ?>; }
    .chosen-container-active.chosen-with-drop .chosen-single,
    .select2-container-active .select2-choice,
    .select2-drop,
    .select2-drop-active { border-color: <?php echo if_light( '#ccc', $color_dark_3 ); ?>; }
    .select2-drop .select2-results,
    .select2-drop-active .select2-results,
    .form-row input[type="email"], .form-row input[type="number"], .form-row input[type="password"], .form-row input[type="search"], .form-row input[type="tel"], .form-row input[type="text"], .form-row input[type="url"], .form-row input[type="color"], .form-row input[type="date"], .form-row input[type="datetime"], .form-row input[type="datetime-local"], .form-row input[type="month"], .form-row input[type="time"], .form-row input[type="week"], .form-row select, .form-row textarea { background-color: <?php echo if_light( '#fff', $color_dark_3 ); ?>; }
    .woocommerce-account .woocommerce-MyAccount-navigation ul li a { border-bottom: 1px solid <?php echo if_light( '#ededde', $color_dark_3 ); ?>; }
    .woocommerce-account .woocommerce-MyAccount-navigation ul li a:before { border-<?php echo $left; ?>: 4px solid <?php echo if_light( '#333', '#555' ); ?>; }
    .woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active > a { background-color: <?php echo if_light( '#f5f5f5', $color_dark_4 ); ?>; }
    .woocommerce-account .woocommerce-MyAccount-navigation ul li a:hover,
    .woocommerce-account .woocommerce-MyAccount-navigation ul li.is-active > a:hover { background-color: <?php echo if_light( '#eee', $color_dark_3 ); ?>; }
    .order-info mark { color: <?php echo if_light( '#000', '#fff' ); ?>; }
    #yith-wcwl-popup-message { background: <?php echo if_light( '#fff', $dark_bg ); ?>; }
    .product_title,
    .product_title a { color: <?php echo if_light( '#555', '#fff' ); ?>; }
    #reviews .commentlist li .comment-text:before { border-<?php echo $right; ?>: 15px solid <?php echo if_light( '#f5f7f7', $color_dark_3 ); ?>; }
    div.quantity .minus,
    div.quantity .plus { background: <?php echo if_light( 'transparent', $color_dark_3 ); ?>; border-color: <?php echo if_light( $input_border_color, $color_dark_3 ); ?>; }
    .star-rating:before { color: <?php echo if_light( 'rgba(0,0,0,0.16)', 'rgba(255,255,255,0.13)' ); ?>; }
    .wcvashopswatchlabel { border: 1px solid <?php echo if_light( '#fff', $color_dark_3 ); ?>; box-shadow: 0 0 0 1px <?php echo if_light( '#ccc', '#444' ); ?>; }
    .wcvaswatchinput.active .wcvashopswatchlabel { border: 1px solid <?php echo if_light( '#000', '#ccc' ); ?>; }
    .wcvaswatchlabel { border: 2px solid <?php echo if_light( '#fff', $color_dark_3 ); ?>; box-shadow: 0 0 0 1px <?php echo if_light( '#ccc', '#444' ); ?>; }
    .wcvaswatch input:checked + .wcvaswatchlabel { border: 2px solid <?php echo if_light( '#000', '#ccc' ); ?>; box-shadow: 0 0 0 0 <?php echo if_light( '#000', '#ccc' ); ?>; }
    .widget_product_categories .widget-title .toggle,
    .widget_price_filter .widget-title .toggle,
    .widget_layered_nav .widget-title .toggle,
    .widget_layered_nav_filters .widget-title .toggle,
    .widget_rating_filter .widget-title .toggle { color: <?php echo if_light( $input_border_color, '#999' ); ?>; }
    .woocommerce .yith-woo-ajax-navigation ul.yith-wcan-label li a,
    .woocommerce-page .yith-woo-ajax-navigation ul.yith-wcan-label li a { border: 1px solid <?php echo if_light( '#e9e9e9', $color_dark ); ?>; background: <?php echo if_light( '#fff', $color_dark ); ?>; }
    .widget_recent_reviews .product_list_widget li img,
    .widget.widget_recent_reviews .product_list_widget li img { background: <?php echo if_light( '#fff', $color_dark_3 ); ?>; }
    .woocommerce table.shop_table.wishlist_table tbody td,
    .woocommerce table.shop_table.wishlist_table tfoot td { border-color: <?php echo if_light( '#ddd', $color_dark_3 ); ?>; }

    <?php if ( isset( $b['woo-show-product-border'] ) && $b['woo-show-product-border'] ) : ?>
        .product-image { border: 1px solid <?php echo $dark ? $color_dark_3 : "#ddd"; ?>; width: 99.9999%; }
    <?php endif; ?>
    <?php if ( !$b['thumb-padding'] ) : ?>
        .product-images .product-image-slider.owl-carousel .img-thumbnail { padding-right: 1px; padding-left: 1px; }
        .product-images .img-thumbnail .inner { border: 1px solid <?php echo $dark ? $color_dark_3 : "#ddd"; ?>; }
    <?php endif; ?>
    .product-thumbs-slider.owl-carousel .img-thumbnail { border-color: <?php echo $dark ? $color_dark_3 : "#ddd"; ?>; }
<?php endif; ?>

.mobile-sidebar .sidebar-toggle:hover,
.feature-box.feature-box-style-5 h4,
.feature-box.feature-box-style-6 h4,
h1.dark,
h2.dark,
h3.dark,
h4.dark,
h5.dark { color: <?php echo $color_dark; ?>; }
.text-dark,
.text-dark.wpb_text_column p { color: <?php echo $color_dark; ?> !important; }
.alert.alert-dark { background-color: <?php echo $portoColorLib->lighten($color_dark, 10); ?>; border-color: <?php echo $portoColorLib->darken($color_dark, 10); ?>; color: <?php echo $portoColorLib->lighten($color_dark, 70); ?>; }
.alert.alert-dark .alert-link { color: <?php echo $portoColorLib->lighten($color_dark, 85); ?>; }
.section.section-text-dark,
.section.section-text-dark h1,
.section.section-text-dark h2,
.section.section-text-dark h3,
.section.section-text-dark h4,
.section.section-text-dark h5,
.section.section-text-dark h6,
.vc_general.vc_cta3 h2,
.vc_general.vc_cta3 h4,
.vc_general.vc_cta3.vc_cta3-style-flat .vc_cta3-content-header h2,
.vc_general.vc_cta3.vc_cta3-style-flat .vc_cta3-content-header h4 { color: <?php echo $color_dark; ?>; }
.section.section-text-dark p { color: <?php echo $portoColorLib->lighten($color_dark, 10); ?>; }
body.boxed .page-wrapper { border-bottom-color: <?php echo $color_dark; ?>; }

html.dark .text-muted { color: <?php echo $portoColorLib->darken( $dark_default_text, 20 ); ?> !important; }

<?php if ( class_exists( 'Woocommerce' ) ) : ?>
    .price,
    td.product-price,
    td.product-subtotal,
    td.product-total,
    td.order-total,
    tr.cart-subtotal,
    .product-nav .product-popup .product-details .amount,
    ul.product_list_widget li .product-details .amount,
    .widget ul.product_list_widget li .product-details .amount { color: <?php echo $color_price; ?>; }

    .widget_price_filter .price_slider { background: <?php echo $price_slide_bg_color; ?>; }
<?php endif; ?>

.porto-links-block { border-color: <?php echo $widget_border_color; ?>; background: <?php echo $widget_bg_color; ?>; }

.widget_sidebar_menu .widget-title,
.porto-links-block .links-title { background: <?php echo $widget_title_bg_color; ?>; border-bottom-color: <?php echo $widget_border_color; ?>; }

.widget_sidebar_menu,
.tm-collapse,
.widget_layered_nav .yith-wcan-select-wrapper { border-color: <?php echo $widget_border_color; ?>; }

.mobile-sidebar .sidebar-toggle,
.pagination > a,
.pagination > span,
.page-links > a,
.page-links > span { border-color: <?php echo $input_border_color; ?>; }
<?php if ( (class_exists('bbPress') && is_bbpress() ) || ( class_exists('BuddyPress') && is_buddypress() ) ) : ?>
    .bbp-pagination-links a,
    .bbp-pagination-links span.current,
    .bbp-topic-pagination a,
    #buddypress div.pagination .pagination-links a,
    #buddypress div.pagination .pagination-links span.current { border-color: <?php echo $input_border_color; ?>; }
    #buddypress #whats-new:focus { border-color: <?php echo $input_border_color; ?> !important; outline-color: <?php echo $input_border_color; ?>; }
<?php endif; ?>

.section-title,
.slider-title,
.widget .widget-title,
.widget .widget-title a,
.widget_calendar caption { color: <?php echo $color_widget_title; ?>; }

.accordion.without-borders .card { border-bottom-color: <?php echo $panel_default_border; ?>; }

/*------------------ header type 17 ---------------------- */
<?php if ( 17 == $header_type ) : ?>
    #header .main-menu-wrap .menu-right { position: relative; top: auto; padding-<?php echo $left; ?>: 0; display: table-cell; }
    #header .main-menu-wrap #mini-cart,
    #header .main-menu-wrap .searchform-popup { display: inline-block; }
    #header .main-menu-wrap .searchform-popup .search-toggle { display: none; }
    #header .main-menu-wrap .searchform-popup .searchform { position: static; display: block; border-width: 0; box-shadow: none; background: rgba(0, 0, 0, 0.07); }
    #header .main-menu-wrap .menu-right .searchform-popup .searchform { border-radius: 0; }
    #header .main-menu-wrap .searchform-popup .searchform fieldset { margin-<?php echo $right; ?>: 0; }
    #header .main-menu-wrap .searchform-popup .searchform input,
    #header .main-menu-wrap .searchform-popup .searchform select,
    #header .main-menu-wrap .searchform-popup .searchform button { border-radius: 0; color: #fff; height: 60px; }
    #header .main-menu-wrap .searchform-popup .searchform input::-webkit-input-placeholder,
    #header .main-menu-wrap .searchform-popup .searchform select::-webkit-input-placeholder,
    #header .main-menu-wrap .searchform-popup .searchform button::-webkit-input-placeholder { color: #fff; opacity: 0.4; text-transform: uppercase; }
    #header .main-menu-wrap .searchform-popup .searchform input:-ms-input-placeholder,
    #header .main-menu-wrap .searchform-popup .searchform select:-ms-input-placeholder,
    #header .main-menu-wrap .searchform-popup .searchform button:-ms-input-placeholder { color: #fff; opacity: 0.4; text-transform: uppercase; }
    #header .main-menu-wrap .searchform-popup .searchform .selectric .label { height: 60px; line-height: 62px; }
    #header .main-menu-wrap .searchform-popup .searchform input { font-weight: 700; width: 200px; padding: <?php echo $rtl ? '6px 22px 6px 12px' : '6px 12px 6px 22px'; ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
    @media <?php echo $screen_large; ?> {
        #header .main-menu-wrap .searchform-popup .searchform input,
        #header .main-menu-wrap .searchform-popup .searchform select,
        #header .main-menu-wrap .searchform-popup .searchform button { height: 50px; }
        #header .main-menu-wrap .searchform-popup .searchform .selectric .label { height: 50px; line-height: 52px; }
        #header .main-menu-wrap .searchform-popup .searchform input { width: 198px; }
    }
    #header .main-menu-wrap .searchform-popup .searchform select { font-weight: 700; width: 120px; padding: <?php echo $rtl ? "6px 22px 6px 12px" : "6px 12px 6px 22px"; ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
    #header .main-menu-wrap .searchform-popup .searchform .selectric-cat { width: 120px; }
    #header .main-menu-wrap .searchform-popup .searchform .selectric .label { font-weight: 700; padding: <?php echo $rtl ? "6px 22px 6px 12px" : "6px 12px 6px 22px"; ?>; box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; }
    #header .main-menu-wrap .searchform-popup .searchform button { margin-<?php echo $left; ?>: -1px; font-size: 20px; padding: 6px 15px; color: #fff; opacity: 0.4; }
    #header .main-menu-wrap .searchform-popup .searchform button:hover { color: #000; }
    #header .main-menu-wrap .searchform-popup .searchform button .fa { font-family: "Simple-Line-Icons"; vertical-align: middle; }
    #header .main-menu-wrap .searchform-popup .searchform button .fa:before { content: "\e090"; font-family: inherit; }
    @media (min-width: 768px) {
        #header .header-main .header-left,
        #header .header-main .header-center,
        #header .header-main .header-right { padding-top: 0; padding-bottom: 0; }
    }
    #header .feature-box .feature-box-icon,
    #header .feature-box .feature-box-info { float: <?php echo $left; ?>; padding-<?php echo $left; ?>: 0; }
    #header .feature-box .feature-box-icon { height: auto; top: 0; margin-<?php echo $right; ?>: 0; }
    #header .feature-box .feature-box-icon > i { margin: 0; }
    #header .feature-box .feature-box-info > h4 { line-height: 110px; margin: 0; }
    #header .header-contact { margin: 0; }
    #header .header-extra-info li { padding-<?php echo $right; ?>: 32px; margin-<?php echo $left; ?>: 22px; border-<?php echo $right; ?>: 1px solid #e9e9e9; }
    @media <?php echo $screen_large; ?> {
        #header .header-extra-info li { padding-<?php echo $right; ?>: 30px; margin-<?php echo $left; ?>: 20px; }
    }
    #header .header-extra-info li:first-child { margin-<?php echo $left; ?>: 0; }
    #header .header-extra-info li:last-child { padding-<?php echo $right; ?>: 0; border-<?php echo $right; ?>: medium none; }
    @media (max-width: 991px) {
        #header .header-extra-info li { padding-<?php echo $right; ?>: 15px; margin-<?php echo $left; ?>: 0; border-<?php echo $right; ?>: medium none; }
        #header .header-extra-info li:last-child { padding-<?php echo $right; ?>: 15px; }
    }
    #header.sticky-header .mobile-toggle { margin: 0; }
<?php endif; ?>

/*------------------ Skin ---------------------- */
<?php
    $bodyColor = $b['body-font']['color'];
    $skinColor = $b['skin-color'];

    $bg_gradient_arr = array(
        array( 'body', 'body-bg-gradient', 'body-bg-gcolor' ),
        array( '.header-wrapper', 'header-wrap-bg-gradient', 'header-wrap-bg-gcolor' ),
        array( '#header .header-main', 'header-bg-gradient', 'header-bg-gcolor' ),
        array( '#main', 'content-bg-gradient', 'content-bg-gcolor' ),
        array( '#main .content-bottom-wrapper', 'content-bottom-bg-gradient', 'content-bottom-bg-gcolor' ),
        array( '.page-top', 'breadcrumbs-bg-gradient', 'breadcrumbs-bg-gcolor' ),
        array( '#footer', 'footer-bg-gradient', 'footer-bg-gcolor' ),
        array( '#footer .footer-main', 'footer-main-bg-gradient', 'footer-main-bg-gcolor' ),
        array( '.footer-top', 'footer-top-bg-gradient', 'footer-top-bg-gcolor' ),
        array( '#footer .footer-bottom', 'footer-bottom-bg-gradient', 'footer-bottom-bg-gcolor' ),
    );
?>
<?php foreach ( $bg_gradient_arr as $bg_gradient ) : ?>
    <?php if ($b[$bg_gradient[1]] && $b[$bg_gradient[2]]['from'] && $b[$bg_gradient[2]]['to']) : ?>
        <?php echo $bg_gradient[0]; ?> {
            background-image: -moz-linear-gradient(top, <?php echo $b[$bg_gradient[2]]['from']; ?>, <?php echo $b[$bg_gradient[2]]['to']; ?>);
            background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $b[$bg_gradient[2]]['from']; ?>), to(<?php echo $b[$bg_gradient[2]]['to']; ?>));
            background-image: -webkit-linear-gradient(top, <?php echo $b[$bg_gradient[2]]['from']; ?>, <?php echo $b[$bg_gradient[2]]['to']; ?>);
            background-image: linear-gradient(to bottom, <?php echo $b[$bg_gradient[2]]['from']; ?>, <?php echo $b[$bg_gradient[2]]['to']; ?>);
            background-repeat: repeat-x;
        }
    <?php endif; ?>
<?php endforeach; ?>
@media (min-width: 992px) {
    .header-wrapper.header-side-nav:not(.fixed-header) #header {
        background-color: <?php echo $b['header-bg']['background-color']; ?>;
        <?php if ( !empty( $b['header-bg']['background-repeat'] ) ) { ?> background-repeat: <?php echo $b['header-bg']['background-repeat'] ?>; <?php } ?>
        <?php if ( !empty( $b['header-bg']['background-size'] ) ) { ?> background-size: <?php echo $b['header-bg']['background-size'] ?>; <?php } ?>
        <?php if ( !empty( $b['header-bg']['background-attachment'] ) ) { ?> background-attachment: <?php echo $b['header-bg']['background-attachment'] ?>; <?php } ?>
        <?php if ( !empty( $b['header-bg']['background-position'] ) ) { ?> background-position: <?php echo $b['header-bg']['background-position'] ?>; <?php } ?>
        <?php if ( !empty( $b['header-bg']['background-image'] ) ) { ?> background-image: url(<?php echo str_replace(array('http://', 'https://'), array('//', '//'), $b['header-bg']['background-image']); ?>); <?php } ?>
    <?php if ($b['header-bg-gradient'] && $b['header-bg-gcolor']['from'] && $b['header-bg-gcolor']['to']) : ?>
        background-image: -moz-linear-gradient(top, <?php echo $b['header-bg-gcolor']['from']; ?>, <?php echo $b['header-bg-gcolor']['to']; ?>);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(<?php echo $b['header-bg-gcolor']['from']; ?>), to(<?php echo $b['header-bg-gcolor']['to']; ?>));
        background-image: -webkit-linear-gradient(top, <?php echo $b['header-bg-gcolor']['from']; ?>, <?php echo $b['header-bg-gcolor']['to']; ?>);
        background-image: linear-gradient(to bottom, <?php echo $b['header-bg-gcolor']['from']; ?>, <?php echo $b['header-bg-gcolor']['to']; ?>);
        background-repeat: repeat-x;
    <?php endif; ?>
    }
}

<?php if ( isset( $b['content-bottom-padding'] ) && ( !empty( $b['content-bottom-padding']['padding-top'] ) || !empty( $b['content-bottom-padding']['padding-bottom'] ) ) ) : ?>
    #main .content-bottom-wrapper {
        <?php if ( !empty( $b['content-bottom-padding']['padding-top'] ) ) : ?> padding-top: <?php echo $b['content-bottom-padding']['padding-top']; ?>px; <?php endif; ?>
        <?php if ( !empty( $b['content-bottom-padding']['padding-bottom'] ) ) : ?> padding-bottom: <?php echo $b['content-bottom-padding']['padding-bottom']; ?>px; <?php endif; ?>
    }
<?php endif; ?>
<?php if ( isset( $b['footer-top-padding'] ) && ( !empty( $b['footer-top-padding']['padding-top'] ) || !empty( $b['footer-top-padding']['padding-bottom'] ) ) ) : ?>
    .footer-top {
        <?php if ( !empty( $b['footer-top-padding']['padding-top'] ) ) : ?> padding-top: <?php echo $b['footer-top-padding']['padding-top']; ?>px; <?php endif; ?>
        <?php if ( !empty( $b['footer-top-padding']['padding-bottom'] ) ) : ?> padding-bottom: <?php echo $b['footer-top-padding']['padding-bottom']; ?>px; <?php endif; ?>
    }
<?php endif; ?>

/* layout */
<?php calcContainerWidth( '#banner-wrapper.banner-wrapper-boxed', ( $header_bg_empty && !$content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php calcContainerWidth( '#main.main-boxed', ( $header_bg_empty && !$content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php calcContainerWidth( 'body.boxed .page-wrapper', false, $b['container-width'], $b['grid-gutter-width'] ); ?>
<?php calcContainerWidth( '#main.main-boxed .vc_row[data-vc-stretch-content]', ( $header_bg_empty && !$content_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>
@media (min-width: <?php echo $b['container-width'] + $b['grid-gutter-width']; ?>px) {
    body.boxed .vc_row[data-vc-stretch-content],
    body.boxed #header.sticky-header .header-main.sticky,
    body.boxed #header.sticky-header .main-menu-wrap,
    body.boxed #header.sticky-header .header-main.sticky,
    #header-boxed #header.sticky-header .header-main.sticky,
    body.boxed #header.sticky-header .main-menu-wrap,
    #header-boxed #header.sticky-header .main-menu-wrap {
        max-width: <?php echo $b['container-width'] + $b['grid-gutter-width']; ?>px;
    }
}

/* header */
<?php
    $header_bg_color = $b['header-bg']['background-color'];
    $header_opacity = ((int)$b['header-opacity']) ? (int)$b['header-opacity'] : 80;
    $header_opacity = (float)$header_opacity / 100;

    $searchform_opacity = ((int)$b['searchform-opacity']) ? (int)$b['searchform-opacity'] : 50;
    $searchform_opacity = (float)$searchform_opacity / 100;
    $menuwrap_opacity = ((int)$b['menuwrap-opacity']) ? (int)$b['menuwrap-opacity'] : 30;
    $menuwrap_opacity = (float)$menuwrap_opacity / 100;
    $menu_opacity = ((int)$b['menu-opacity']) ? (int)$b['menu-opacity'] : 30;
    $menu_opacity = (float)$menu_opacity / 100;

    $footer_opacity = ((int)$b['footer-opacity']) ? (int)$b['footer-opacity'] : 80;
    $footer_opacity = (float)$footer_opacity / 100;
?>
#mini-cart .cart-popup { color: <?php echo $bodyColor; ?> }
.fixed-header #header .header-main {
<?php if ( 'transparent' == $header_bg_color ) : ?>
    box-shadow: none;
<?php elseif ( $header_bg_color ): ?>
    background-color: rgba(<?php echo $portoColorLib->hexToRGB( $header_bg_color ); ?>, <?php echo $header_opacity; ?>);
<?php endif; ?>
}
@media (min-width: 992px) {
    .header-wrapper.header-side-nav.fixed-header #header {
        <?php backgroundOpacity( $portoColorLib, $header_bg_color, $header_opacity ); ?>
    }
}

<?php
    if ($b['mainmenu-wrap-bg-color-sticky'] && $b['mainmenu-wrap-bg-color-sticky'] != 'transparent') {
        $sticky_menu_bg_color = $b['mainmenu-wrap-bg-color-sticky'];
    } else if ($b['mainmenu-bg-color'] && $b['mainmenu-bg-color'] != 'transparent') {
        $sticky_menu_bg_color = $b['mainmenu-bg-color'];
    } else if ($b['mainmenu-wrap-bg-color'] && $b['mainmenu-wrap-bg-color'] != 'transparent') {
        $sticky_menu_bg_color = $b['mainmenu-wrap-bg-color'];
    } else {
        $sticky_menu_bg_color = $b['sticky-header-bg']['background-color'];
    }
?>
#header.sticky-header .header-main,
.fixed-header #header.sticky-header .header-main
<?php echo 'transparent' == $sticky_menu_bg_color ? ', #header.sticky-header .main-menu-wrap, .fixed-header #header.sticky-header .main-menu-wrap' : ''; ?> {
    <?php if ( !empty( $b['sticky-header-bg']['background-color'] ) && 'transparent' != $b['sticky-header-bg']['background-color'] ) { ?> background-color: rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg']['background-color']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>); <?php } ?>
    <?php if ( !empty( $b['sticky-header-bg']['background-repeat'] ) ) { ?> background-repeat: <?php echo $b['sticky-header-bg']['background-repeat'] ?>; <?php } ?>
    <?php if ( !empty( $b['sticky-header-bg']['background-size'] ) ) { ?> background-size: <?php echo $b['sticky-header-bg']['background-size'] ?>; <?php } ?>
    <?php if ( !empty( $b['sticky-header-bg']['background-attachment'] ) ) { ?> background-attachment: <?php echo $b['sticky-header-bg']['background-attachment'] ?>; <?php } ?>
    <?php if ( !empty( $b['sticky-header-bg']['background-position'] ) ) { ?> background-position: <?php echo $b['sticky-header-bg']['background-position'] ?>; <?php } ?>
    <?php if ( !empty( $b['sticky-header-bg']['background-image'] ) ) { ?> background-image: url(<?php echo str_replace(array('http://', 'https://'), array('//', '//'), $b['sticky-header-bg']['background-image']); ?>); <?php } ?>
<?php if ( $b['sticky-header-bg-gradient'] && $b['sticky-header-bg-gcolor']['from'] && $b['sticky-header-bg-gcolor']['to'] && 'transparent' != $b['sticky-header-bg-gcolor']['from'] && 'transparent' != $b['sticky-header-bg-gcolor']['to'] ) : ?>
    background-image: -moz-linear-gradient(top, rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['from']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>), rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['to']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>));
    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['from']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>)), to(rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['to']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>)));
    background-image: -webkit-linear-gradient(top, rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['from']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>), rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['to']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>));
    background-image: linear-gradient(to bottom, rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['from']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>), rgba(<?php echo $portoColorLib->hexToRGB($b['sticky-header-bg-gcolor']['to']); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>));
    background-repeat: repeat-x;
<?php endif; ?>
}
<?php if ( 'transparent' != $sticky_menu_bg_color ) : ?>
#header.sticky-header .main-menu-wrap,
.fixed-header #header.sticky-header .main-menu-wrap {
    background-color: rgba(<?php echo $portoColorLib->hexToRGB($sticky_menu_bg_color); ?>, <?php echo (float)str_replace( '%', '', $b['sticky-header-opacity'] ) / 100; ?>);
}
<?php endif; ?>
<?php if ( !empty( $b['sticky-header-bg']['background-image'] ) ) { ?>
    #header.header-loaded .header-main { transition: none; }
<?php } ?>

.fixed-header #header .searchform {
    <?php backgroundOpacity( $portoColorLib, $b['searchform-bg-color'], $searchform_opacity ); ?>
    <?php if ( 'transparent' != $b['searchform-border-color'] && $searchform_opacity ) : ?>
        border-color: rgba(<?php echo $portoColorLib->hexToRGB( $b['searchform-border-color'] ); ?>, <?php echo $searchform_opacity; ?>);
    <?php endif; ?>
}
@media (max-width: 991px) {
    .fixed-header #header .searchform {
        <?php backgroundOpacity( $portoColorLib, $b['searchform-bg-color'], 1 ); ?>
    }
}
.fixed-header #header .searchform-popup .searchform {
    <?php backgroundOpacity( $portoColorLib, $b['searchform-bg-color'], 1 ); ?>
}
.fixed-header #header .main-menu-wrap {
    <?php backgroundOpacity( $portoColorLib, $b['mainmenu-wrap-bg-color'], $menuwrap_opacity ); ?>
}
.fixed-header #header .main-menu {
    <?php backgroundOpacity( $portoColorLib, $b['mainmenu-bg-color'], $menu_opacity ); ?>
}
#header .searchform,
.fixed-header #header.sticky-header .searchform {
    <?php if ( $b['searchform-bg-color'] ) : ?>
        background: <?php echo $b['searchform-bg-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['searchform-border-color'] ) : ?>
        border-color: <?php echo $b['searchform-border-color']; ?>;
    <?php endif; ?>
}
.fixed-header #header.sticky-header .searchform {
    border-radius: <?php echo $b['search-border-radius'] ? '20px' : '0'; ?>;
}
<?php if ( $b['mainmenu-bg-color'] && 'transparent' != $b['mainmenu-bg-color'] ) : ?>
    .fixed-header #header.sticky-header .main-menu,
    #header .main-menu,
    #main-toggle-menu .toggle-menu-wrap {
        background-color: <?php echo $b['mainmenu-bg-color']; ?>;
    }
<?php endif; ?>

<?php if ( $b['header-link-color']['regular'] ) : ?>
    #header .header-main .header-contact a,
    #header .tooltip-icon,
    #header .top-links > li.menu-item > a,
    #header .searchform-popup .search-toggle {
        color: <?php echo $b['header-link-color']['regular']; ?>;
    }
    #header .tooltip-icon { border-color: <?php echo $b['header-link-color']['regular']; ?>; }
<?php endif; ?>
<?php if ( $b['header-link-color']['hover'] ) : ?>
    #header .header-main .header-contact a:hover,
    #header .top-links > li.menu-item:hover > a,
    #header .top-links > li.menu-item > a.active,
    #header .top-links > li.menu-item > a.focus,
    #header .top-links > li.menu-item.has-sub:hover > a,
    #header .searchform-popup .search-toggle:hover {
        color: <?php echo $b['header-link-color']['hover']; ?>;
    }
<?php endif; ?>
<?php if ( $b['header-bg']['background-color'] ) : ?>
    #header .header-main .header-contact .nav-top > li > a:hover {
        background-color: <?php echo $portoColorLib->darken( $b['header-bg']['background-color'], 5 ); ?>;
    }
<?php endif; ?>
<?php if ( $b['header-top-link-color']['regular'] ) : ?>
    #header .header-top .header-contact a,
    #header .header-top .top-links > li.menu-item > a,
    .header-top .welcome-msg a,
    #header:not(.header-corporate) .header-top .share-links > a {
        color: <?php echo $b['header-top-link-color']['regular']; ?>;
    }
<?php endif; ?>
<?php if ( $b['header-top-link-color']['hover'] ) : ?>
    #header .header-top .header-contact a:hover,
    #header .header-top .top-links > li.menu-item.active > a,
    #header .header-top .top-links > li.menu-item:hover > a,
    #header .header-top .top-links > li.menu-item > a.active,
    #header .header-top .top-links > li.menu-item.has-sub:hover > a,
    .header-top .welcome-msg a:hover,
    #header:not(.header-corporate) .header-top .share-links > a:hover {
        color: <?php echo $b['header-top-link-color']['hover']; ?>;
    }
<?php endif; ?>
<?php if ( $b['header-top-bg-color'] ) : ?>
    #header .header-top .header-contact .nav-top > li > a:hover {
        background-color: <?php echo $portoColorLib->darken( $b['header-top-bg-color'] ? $b['header-top-bg-color'] : 'transparent', 5 ); ?>;
    }
<?php endif; ?>
<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
    #header .top-links > li.menu-item.has-sub:hover > a {
        background-color: <?php echo $b['mainmenu-toplevel-hbg-color']; ?>;
    }
<?php endif; ?>
<?php if ( $b['mainmenu-popup-bg-color'] ) : ?>
    #header .top-links .narrow ul.sub-menu,
    #header .main-menu .wide .popup > .inner,
    .header-side-nav .sidebar-menu .wide .popup > .inner,
    .toggle-menu-wrap .sidebar-menu .wide .popup > .inner,
    .sidebar-menu .narrow ul.sub-menu {
        background-color: <?php echo $b['mainmenu-popup-bg-color']; ?>;
    }
    #header .top-links.show-arrow > li.has-sub:before,
    #header .top-links.show-arrow > li.has-sub:after {
        border-bottom-color: <?php echo $b['mainmenu-popup-bg-color']; ?>;
    }
    .sidebar-menu .menu-custom-block a:hover,
    .sidebar-menu .menu-custom-block a:hover + a {
        border-top-color: <?php echo $b['mainmenu-popup-bg-color']; ?>;
    }
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-color']['regular'] ) : ?>
    #header .top-links .narrow li.menu-item > a,
    #header .main-menu .wide .popup li.sub li.menu-item > a,
    .header-side-nav .sidebar-menu .wide .popup > .inner > ul.sub-menu > li.menu-item li.menu-item > a,
    .toggle-menu-wrap .sidebar-menu .wide .popup > .inner > ul.sub-menu > li.menu-item li.menu-item > a,
    .sidebar-menu .wide .popup li.sub li.menu-item > a,
    .sidebar-menu .narrow li.menu-item > a {
        color: <?php echo $b['mainmenu-popup-text-color']['regular']; ?>;
    }
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
    #header .top-links .narrow li.menu-item:hover > a {
        color: <?php echo $b['mainmenu-popup-text-color']['hover']; ?>;
    }
<?php endif; ?>
<?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
    #header .top-links .narrow li.menu-item:hover > a,
    .sidebar-menu .narrow li.menu-item:hover > a { background-color: <?php echo $b['mainmenu-popup-text-hbg-color']; ?>; }
<?php endif; ?>
.header-side-nav .sidebar-menu .wide .popup > .inner > ul.sub-menu > li.menu-item li.menu-item > a:hover,
.toggle-menu-wrap .sidebar-menu .wide .popup > .inner > ul.sub-menu > li.menu-item li.menu-item > a:hover {
    <?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
        background-color: <?php echo $b['mainmenu-popup-text-hbg-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
        color: <?php echo $b['mainmenu-popup-text-color']['hover']; ?>;
    <?php endif; ?>
}
<?php calcContainerWidth( '#header-boxed', false, $b['container-width'], $b['grid-gutter-width'] ); ?>

<?php if ( $b['header-top-menu-padding']['padding-top'] || $b['header-top-menu-padding']['padding-bottom'] || $b['header-top-menu-padding']['padding-left'] || $b['header-top-menu-padding']['padding-right'] ) : ?>
    #header .header-top .top-links > li.menu-item > a {
        <?php if ( $b['header-top-menu-padding']['padding-top'] ) : ?>
            padding-top: <?php echo $b['header-top-menu-padding']['padding-top']; ?>px;
        <?php endif; ?>
        <?php if ( $b['header-top-menu-padding']['padding-bottom'] ) : ?>
            padding-bottom: <?php echo $b['header-top-menu-padding']['padding-bottom']; ?>px;
        <?php endif; ?>
        <?php if ( $b['header-top-menu-padding']['padding-left'] ) : ?>
            padding-left: <?php echo $b['header-top-menu-padding']['padding-left']; ?>px;
        <?php endif; ?>
        <?php if ( $b['header-top-menu-padding']['padding-right'] ) : ?>
            padding-right: <?php echo $b['header-top-menu-padding']['padding-right']; ?>px;
        <?php endif; ?>
    }
<?php endif; ?>
#header .header-top .top-links .narrow li.menu-item:hover > a { text-decoration: none; }
<?php if ( $b['header-top-menu-hide-sep'] ) : ?>
    #header .top-links > li.menu-item:after { content: ''; display: none; }
    #header .header-top .gap { visibility: hidden; }
<?php endif; ?>

.header-top {
    <?php if ( $b['header-top-bottom-border']['border-top'] && $b['header-top-bottom-border']['border-top'] != '0px' ) : ?>
        border-bottom: <?php echo $b['header-top-bottom-border']['border-top']; ?> solid <?php echo $b['header-top-bottom-border']['border-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['header-top-bg-color'] ) : ?>
        background-color: <?php echo $b['header-top-bg-color']; ?>;
    <?php endif; ?>
}

.main-menu-wrap {
    <?php if ( $b['mainmenu-wrap-bg-color'] ) : ?>
        background-color: <?php echo $b['mainmenu-wrap-bg-color']; ?>;
    <?php endif; ?>
    padding: <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-'. $right]) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-'. $left]) ?>px;
}
#header.sticky-header .main-menu-wrap {
    padding: <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-'. $right]) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-'. $left]) ?>px;
}
<?php if ( $b['search-border-radius'] ) : ?>
    #header .main-menu-wrap .searchform-popup .searchform { border-radius: 25px; }
<?php endif; ?>
<?php if ( $b['border-radius'] && ( empty( $b['mainmenu-bg-color'] ) || 'transparent' == $b['mainmenu-bg-color'] ) && ( empty( $b['mainmenu-toplevel-hbg-color'] ) || 'transparent' == $b['mainmenu-toplevel-hbg-color'] ) ) : ?>
    .main-menu-wrap .main-menu .wide .popup,
    .main-menu-wrap .main-menu .wide .popup > .inner,
    .main-menu-wrap .main-menu .wide.pos-left .popup,
    .main-menu-wrap .main-menu .wide.pos-right .popup,
    .main-menu-wrap .main-menu .wide.pos-left .popup > .inner,
    .main-menu-wrap .main-menu .wide.pos-right .popup > .inner,
    .main-menu-wrap .main-menu .narrow .popup > .inner > ul.sub-menu,
    .main-menu-wrap .main-menu .narrow.pos-left .popup > .inner > ul.sub-menu,
    .main-menu-wrap .main-menu .narrow.pos-right .popup > .inner > ul.sub-menu { border-radius: 0 0 2px 2px; }
<?php endif; ?>
.main-menu-wrap .main-menu > li.menu-item > a .tip {
    <?php echo $right; ?>: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-'. $right]) ?>px;
    top: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-top']) - 15; ?>px;
}
@media <?php echo $screen_large; ?> {
    .main-menu-wrap .main-menu > li.menu-item > a .tip {
        <?php echo $right; ?>: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-'. $right]) ?>px;
        top: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-top']) - 15; ?>px;
    }
}
#header .main-menu-wrap .main-menu .menu-custom-block a,
#header .main-menu-wrap .main-menu .menu-custom-block span {
    padding: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-'. $right]) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-'. $left]) ?>px;
}
@media <?php echo $screen_large; ?> {
    #header .main-menu-wrap .main-menu .menu-custom-block a,
    #header .main-menu-wrap .main-menu .menu-custom-block span {
        padding: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-'. $right]) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-'. $left]) ?>px;
    }
}
#header .main-menu-wrap .main-menu .menu-custom-block .tip {
    <?php echo $right; ?>: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-'. $left]) ?>px;
    top: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-top']) - 15; ?>px;
}

#header .main-menu > li.menu-item > a {
    <?php if ( $b['menu-font']['font-family'] ) : ?>
        font-family: <?php echo $b['menu-font']['font-family']; ?>, sans-serif;
    <?php endif; ?>
    <?php if ( $b['menu-font']['font-size'] ) : ?>
        font-size: <?php echo $b['menu-font']['font-size']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-font']['font-weight'] ) : ?>
        font-weight: <?php echo $b['menu-font']['font-weight']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-font']['line-height'] ) : ?>
        line-height: <?php echo $b['menu-font']['line-height']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-font']['letter-spacing'] ) : ?>
        letter-spacing: <?php echo $b['menu-font']['letter-spacing']; ?>;
    <?php endif; ?>
    <?php if ( $b['mainmenu-toplevel-link-color']['regular'] ) : ?>
        color: <?php echo $b['mainmenu-toplevel-link-color']['regular']; ?>;
    <?php endif; ?>
    padding: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-'. $right]) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-'. $left]) ?>px;
}
<?php
    $main_menu_level1_abg_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-abg-color'] : $b['mainmenu-toplevel-hbg-color'];
    $main_menu_level1_active_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-alink-color'] : $b['mainmenu-toplevel-link-color']['hover'];
?>
#header .main-menu > li.menu-item.active > a {
    <?php if ( $main_menu_level1_abg_color ) : ?>
        background-color: <?php echo $main_menu_level1_abg_color; ?>;
    <?php endif; ?>
    <?php if ( $main_menu_level1_active_color ) : ?>
        color: <?php echo $main_menu_level1_active_color; ?>;
    <?php endif; ?>
}
#header .main-menu > li.menu-item.active:hover > a,
#header .main-menu > li.menu-item:hover > a {
    <?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
        background-color: <?php echo $b['mainmenu-toplevel-hbg-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['mainmenu-toplevel-link-color']['hover'] ) : ?>
        color: <?php echo $b['mainmenu-toplevel-link-color']['hover']; ?>;
    <?php endif; ?>
}
#header .main-menu .popup li.menu-item a,
.header-side-nav .sidebar-menu .popup,
.toggle-menu-wrap .sidebar-menu .popup {
    <?php if ( $b['menu-popup-font']['font-family'] ) : ?>
        font-family: <?php echo $b['menu-popup-font']['font-family']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-popup-font']['font-size'] ) : ?>
        font-size: <?php echo $b['menu-popup-font']['font-size']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-popup-font']['font-weight'] ) : ?>
        font-weight: <?php echo $b['menu-popup-font']['font-weight']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-popup-font']['line-height'] ) : ?>
        line-height: <?php echo $b['menu-popup-font']['line-height']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-popup-font']['letter-spacing'] ) : ?>
        letter-spacing: <?php echo $b['menu-popup-font']['letter-spacing']; ?>;
    <?php endif; ?>
}
<?php if ( !$b['mainmenu-popup-border'] ) : ?>
    #header .main-menu .popup > .inner { margin-top: 0; }
<?php endif; ?>
<?php if ( !$b['mainmenu-popup-border'] ) : ?>
    #header .main-menu .wide .popup {
        border-width: 0;
        <?php if ( $b['mainmenu-popup-border'] && $b['mainmenu-popup-border-color'] ) : ?>
            border-top-color: <?php echo $b['mainmenu-popup-border-color']; ?>;
        <?php endif; ?>
    }
    <?php if ( $b['border-radius'] ) : ?>
        #header .main-menu .wide .popup > .inner { border-radius: 2px; }
        #header .main-menu .wide.pos-left .popup > .inner { border-radius: 0 2px 2px 2px; }
        #header .main-menu .wide.pos-right .popup > .inner { border-radius: 2px 0 2px 2px; }
    <?php endif; ?>
<?php endif; ?>
<?php if ( $b['mainmenu-popup-heading-color'] ) : ?>
    #header .main-menu .wide .popup li.sub > a,
    .header-side-nav .sidebar-menu .wide .popup > .inner > ul.sub-menu > li.menu-item > a,
    .toggle-menu-wrap .sidebar-menu .wide .popup > .inner > ul.sub-menu > li.menu-item > a {
        color: <?php echo $b['mainmenu-popup-heading-color']; ?>;
    }
<?php endif; ?>
<?php if ( isset( $b['mainmenu-popup-narrow-type'] ) && $b['mainmenu-popup-narrow-type'] && $b['mainmenu-toplevel-hbg-color'] && 'transparent' != $b['mainmenu-toplevel-hbg-color'] ) : ?>
    #header .main-menu .narrow .popup ul.sub-menu {
        background: <?php echo $b['mainmenu-toplevel-hbg-color']; ?>;
    }
    #header .main-menu .narrow .popup li.menu-item > a {
        color: <?php echo $b['mainmenu-toplevel-link-color']['hover']; ?>;
        background-color: <?php echo $b['mainmenu-toplevel-hbg-color']; ?>;
    }
    #header .main-menu .narrow .popup li.menu-item:hover > a {
        background: <?php echo $portoColorLib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ); ?>
    }
<?php else : ?>
    #header .main-menu .narrow .popup ul.sub-menu {
        <?php if ( $b['mainmenu-popup-bg-color'] ) : ?>
            background-color: <?php echo $b['mainmenu-popup-bg-color']; ?>;
        <?php endif; ?>
        <?php if ( $b['mainmenu-popup-border'] ) : ?>
            border-top-width: 3px;
            <?php if ( $b['mainmenu-popup-border-color'] ) : ?>
                border-top-color: <?php echo $b['mainmenu-popup-border-color']; ?>;
            <?php endif; ?>
        <?php endif; ?>
    }
    <?php if ( $b['mainmenu-popup-border'] ) : ?>
        #header .main-menu .narrow .popup ul.sub-menu li.menu-item:hover > ul.sub-menu { top: -8px; }
    <?php endif; ?>
    #header .main-menu .narrow .popup li.menu-item > a {
        <?php if ( $b['mainmenu-popup-text-color']['regular'] ) : ?>
            color: <?php echo $b['mainmenu-popup-text-color']['regular']; ?>;
        <?php endif; ?>
        <?php if ( $b['mainmenu-popup-bg-color'] && 'transparent' != $b['mainmenu-popup-bg-color'] ) :
            $main_menu_popup_bg_arr = $portoColorLib->hexToRGB( $b['mainmenu-popup-bg-color'], false );
            if ( ( $main_menu_popup_bg_arr[0] * 256 + $main_menu_popup_bg_arr[1] * 16 + $main_menu_popup_bg_arr[2] ) < ( 79 * 256 + 255 * 16 + 255 ) ) : ?>
                border-bottom-color: <?php echo $portoColorLib->lighten( $b['mainmenu-popup-bg-color'], 5 ); ?>;
            <?php else: ?>
                border-bottom-color: <?php echo $portoColorLib->darken( $b['mainmenu-popup-bg-color'], 5 ); ?>;
            <?php endif; ?>
        <?php endif; ?>
    }
    <?php if ( $b['mainmenu-popup-border'] && $b['mainmenu-popup-border-color'] ) : ?>
        #header .main-menu .narrow .popup li.menu-item > a:before { color: <?php echo $b['mainmenu-popup-border-color']; ?>; }
    <?php endif; ?>
    #header .main-menu .narrow .popup li.menu-item:hover > a {
        <?php if ( $b['mainmenu-popup-text-color']['hover'] ) : ?>
            color: <?php echo $b['mainmenu-popup-text-color']['hover']; ?>;
        <?php endif; ?>
        <?php if ( $b['mainmenu-popup-text-hbg-color'] ) : ?>
            background-color: <?php echo $b['mainmenu-popup-text-hbg-color']; ?>;
        <?php endif; ?>
    }
<?php endif; ?>

<?php if ( $b['menu-custom-text-color'] ) : ?>
    #header .menu-custom-block,
    #header .menu-custom-block span { color: <?php echo $b['menu-custom-text-color']; ?>; }
<?php endif; ?>
#header .menu-custom-block span,
#header .menu-custom-block a {
    <?php if ( $b['menu-font']['font-family'] ) : ?>
        font-family: <?php echo $b['menu-font']['font-family']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-font']['font-size'] ) : ?>
        font-size: <?php echo $b['menu-font']['font-size']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-font']['font-weight'] ) : ?>
        font-weight: <?php echo $b['menu-font']['font-weight']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-font']['line-height'] ) : ?>
        line-height: <?php echo $b['menu-font']['line-height']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-font']['letter-spacing'] ) : ?>
        letter-spacing: <?php echo $b['menu-font']['letter-spacing']; ?>;
    <?php endif; ?>
}
#header .menu-custom-block a {
    <?php if ( $b['menu-text-transform'] ) : ?>
        text-transform: <?php echo $b['menu-text-transform']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-custom-link']['regular'] ) : ?>
        color: <?php echo $b['menu-custom-link']['regular']; ?>;
    <?php endif; ?>
}
<?php if ( $b['menu-custom-link']['hover'] ) : ?>
    #header .menu-custom-block a:hover { color: <?php echo $b['menu-custom-link']['hover']; ?>; }
<?php endif; ?>

<?php if ( $b['switcher-link-color']['regular'] ) : ?>
    #header .porto-view-switcher > li.menu-item:before,
    #header .porto-view-switcher > li.menu-item > a { color: <?php echo $b['switcher-link-color']['regular']; ?>; }
<?php endif; ?>
<?php if ( $b['switcher-bg-color'] ) : ?>
    #header .porto-view-switcher > li.menu-item > a { background-color: <?php echo $b['switcher-bg-color']; ?>; }
<?php endif; ?>
<?php if ( $b['switcher-hbg-color'] ) : ?>
    #header .porto-view-switcher .narrow ul.sub-menu { background: <?php echo $b['switcher-hbg-color']; ?>; }
<?php endif; ?>
<?php if ( $b['switcher-link-color']['hover'] ) : ?>
    #header .porto-view-switcher .narrow li.menu-item > a { color: <?php echo $b['switcher-link-color']['hover']; ?>; }
<?php endif; ?>
#header .porto-view-switcher .narrow li.menu-item > a.active,
#header .porto-view-switcher .narrow li.menu-item:hover > a {
    <?php if ( $b['switcher-link-color']['hover'] ) : ?>
        color: <?php echo $b['switcher-link-color']['hover']; ?>;
    <?php endif; ?>
    <?php if ( $b['switcher-hbg-color'] ) : ?>
        background: <?php echo $portoColorLib->darken( $b['switcher-hbg-color'], 5 ); ?>;
    <?php endif; ?>
}
<?php if ( $b['switcher-hbg-color'] && 'transparent' != $b['switcher-hbg-color'] ) : ?>
    #header .porto-view-switcher.show-arrow > li.has-sub:before,
    #header .porto-view-switcher.show-arrow > li.has-sub:after { border-bottom-color: <?php echo $b['switcher-hbg-color']; ?>; }
<?php endif; ?>
<?php if ( $b['searchform-text-color'] ) : ?>
    #header .searchform input,
    #header .searchform select,
    #header .searchform button,
    #header .searchform .selectric .label,
    #header .searchform .selectric-items li,
    #header .searchform .selectric-items li:hover,
    #header .searchform .selectric-items li.selected,
    #header .searchform .autocomplete-suggestion .yith_wcas_result_content .title { color: <?php echo $b['searchform-text-color']; ?> }
    #header .searchform input:-ms-input-placeholder { color: <?php echo $b['searchform-text-color']; ?> }
    #header .searchform input::-ms-input-placeholder { color: <?php echo $b['searchform-text-color']; ?> }
    #header .searchform input::placeholder { color: <?php echo $b['searchform-text-color']; ?> }
<?php endif; ?>
<?php if ( $b['searchform-border-color'] ) : ?>
    #header .searchform input,
    #header .searchform select,
    #header .searchform .selectric,
    #header .searchform .selectric-hover .selectric,
    #header .searchform .selectric-open .selectric,
    #header .searchform .autocomplete-suggestions,
    #header .searchform .selectric-items { border-color: <?php echo $b['searchform-border-color']; ?> }
<?php endif; ?>
<?php if ( $b['searchform-hover-color'] ) : ?>
    #header .searchform button {
        color: <?php echo $b['searchform-hover-color']; ?>;
    }
<?php endif; ?>
#header .searchform select option,
#header .searchform .autocomplete-suggestion,
#header .searchform .autocomplete-suggestions,
#header .searchform .selectric-items {
    <?php if ( $b['searchform-text-color'] ) : ?>
        color: <?php echo $b['searchform-text-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['searchform-bg-color'] ) : ?>
        background-color: <?php echo $b['searchform-bg-color']; ?>;
    <?php endif; ?>
}
<?php if ( $b['searchform-bg-color'] ) : ?>
    #header .searchform .selectric-items li:hover,
    #header .searchform .selectric-items li.selected { background-color: <?php echo $portoColorLib->darken( $b['searchform-bg-color'], 10 ); ?> }
<?php endif; ?>
<?php if ( $b['searchform-popup-border-color'] ) : ?>
    #header .searchform-popup .search-toggle:after { border-bottom-color: <?php echo $b['searchform-popup-border-color']; ?>; }
    #header .search-popup .searchform { border-color: <?php echo $b['searchform-popup-border-color']; ?>; }
    @media (max-width: 991px) {
        #header .searchform { border-color: <?php echo $b['searchform-popup-border-color']; ?>; }
    }
<?php endif; ?>
#header .mobile-toggle {
    <?php if ( $b['mobile-menu-toggle-text-color'] ) : ?>
        color: <?php echo $b['mobile-menu-toggle-text-color']; ?>;
    <?php endif; ?>
    background-color: <?php echo empty( $b['mobile-menu-toggle-bg-color'] ) ? $b['skin-color'] : $b['mobile-menu-toggle-bg-color']; ?>;
}

@media <?php echo $screen_large; ?> {
    #header .main-menu-wrap .main-menu .menu-custom-block .tip {
        <?php echo $right; ?>: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-'. $left]) ?>px;
        top: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-top']) - 15; ?>px;
    }
    #header .main-menu > li.menu-item > a,
    #header .menu-custom-block span,
    #header .menu-custom-block a {
        padding: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-'. $right]) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-'. $left]) ?>px;
        <?php if ( $b['menu-font-md']['line-height'] ) : ?>
            line-height: <?php echo $b['menu-font-md']['line-height']; ?>;
        <?php endif; ?>
    }
    #header .main-menu > li.menu-item > a {
        <?php if ( $b['menu-font-md']['font-size'] ) : ?>
            font-size: <?php echo $b['menu-font-md']['font-size']; ?>;
        <?php endif; ?>
        <?php if ( $b['menu-font-md']['letter-spacing'] ) : ?>
            letter-spacing: <?php echo $b['menu-font-md']['letter-spacing']; ?>;
        <?php endif; ?>
    }
}

/* side header */
<?php if ( porto_header_type_is_side() ) : ?>
    @media (min-width: 992px) {
        .page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup { <?php echo $left; ?>: 0; <?php echo $right; ?>: auto; }
        .page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup:before,
        .page-wrapper.side-nav:not(.side-nav-right) #mini-cart .cart-popup:after { <?php echo $left; ?>: 38.7px; <?php echo $right; ?>: auto; }
        .page-wrapper.side-nav.side-nav-right > * { padding-<?php echo $left; ?>: 0; padding-<?php echo $right; ?>: 256px; }
        .page-wrapper.side-nav.side-nav-right > .header-wrapper.header-side-nav { <?php echo $right; ?>: 0; }
        .page-wrapper.side-nav.side-nav-right > .header-wrapper.header-side-nav #header { <?php echo $left; ?>: auto; <?php echo $right; ?>: 0; }
        .page-wrapper.side-nav.side-nav-right > .header-wrapper.header-side-nav #header.initialize { position: fixed; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item > a { text-align: <?php echo $right; ?>; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item > a > .thumb-info-preview { <?php echo $left; ?>: auto; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item > .arrow { <?php echo $right; ?>: auto; <?php echo $left; ?>: -5px; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item > .arrow:before { border-<?php echo $left; ?>: none; border-<?php echo $right; ?>: 5px solid <?php echo $skinColor; ?>; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu li.menu-item:hover > .arrow:before { color: #fff; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu .popup,
        .page-wrapper.side-nav.side-nav-right .sidebar-menu .narrow ul.sub-menu ul.sub-menu { <?php echo $left; ?>: auto; <?php echo $right; ?>: 100%; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu .narrow li.menu-item-has-children > a:before { float: <?php echo $left; ?>; content: "\f0d9"; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo $left; ?> > li.menu-item .popup,
        .page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo $left; ?> .narrow ul.sub-menu li.menu-item > ul.sub-menu { -webkit-animation: menuFadeInRight 0.2s ease-out; animation: menuFadeInRight 0.2s ease-out; }
        .page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo $right; ?> > li.menu-item .popup,
        .page-wrapper.side-nav.side-nav-right .sidebar-menu.subeffect-fadein-<?php echo $right; ?> > .narrow ul.sub-menu li.menu-item > ul.sub-menu { -webkit-animation: menuFadeInLeft 0.2s ease-out; animation: menuFadeInLeft 0.2s ease-out; }
        .page-wrapper.side-nav.side-nav-right #mini-cart .cart-popup { <?php echo $left; ?>: auto; <?php echo $right; ?>: 0; }
        .page-wrapper.side-nav.side-nav-right #mini-cart .cart-popup:before,
        .page-wrapper.side-nav.side-nav-right #mini-cart .cart-popup:after { <?php echo $right; ?>: 10px; <?php echo $left; ?>: auto; }
    }
<?php endif; ?>

/* sticky header */
<?php if ( !$porto_settings['show-sticky-logo'] ) : ?>
    #header.sticky-header .logo { display: none !important; }
<?php endif; ?>
<?php if ( !$porto_settings['show-sticky-searchform'] ) : ?>
    #header.sticky-header .searchform-popup { display: none !important; }
<?php endif; ?>
<?php if ( !$porto_settings['show-sticky-minicart'] ) : ?>
    #header.sticky-header #mini-cart { display: none !important; }
<?php endif; ?>
<?php if ( !$porto_settings['show-sticky-menu-custom-content'] ) : ?>
    #header.sticky-header .menu-custom-content { display: none !important; }
<?php endif; ?>

/* header type */
<?php if ( (int)$header_type >= 11 && (int)$header_type <= 17 ) : ?>
    @media (min-width: 992px) {
        #header .searchform button { color: <?php echo $portoColorLib->lighten( $b['searchform-text-color'], 43 ); ?>; }
    }
<?php endif; ?>
<?php if ( 9 == $header_type ) : ?>
    #header.sticky-header .main-menu-wrap,
    .fixed-header #header.sticky-header .main-menu-wrap { background-color: <?php echo $b['mainmenu-wrap-bg-color']; ?>; }
<?php elseif ( 11 == $header_type && $b['header-top-border']['border-top'] && $b['header-top-border']['border-top'] != '0px' ) : ?>
    <?php
        $main_menu_level1_abg_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-abg-color'] : $b['mainmenu-toplevel-hbg-color'];
        $main_menu_level1_active_color = $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-alink-color'] : $b['mainmenu-toplevel-link-color']['hover'];
    ?>
    @media (min-width: 992px) {
        #header .main-menu > li.menu-item { margin-top: -<?php echo $b['header-top-border']['border-top']; ?>; }
        #header .main-menu > li.menu-item > a { border-top: <?php echo $b['header-top-border']['border-top']; ?> solid transparent; }
        #header .main-menu > li.menu-item.active > a {
            border-top: <?php echo $b['header-top-border']['border-top']; ?> solid <?php echo 'transparent' == $main_menu_level1_abg_color ? $main_menu_level1_active_color : $main_menu_level1_abg_color; ?>;
        }
        #header .main-menu > li.menu-item:hover > a {
            border-top: <?php echo $b['header-top-border']['border-top']; ?> solid <?php echo 'transparent' == $b['mainmenu-toplevel-hbg-color'] ? $b['mainmenu-toplevel-link-color']['hover'] : $b['mainmenu-toplevel-hbg-color']; ?>;
        }
    }
<?php elseif ( 18 == $header_type ) : ?>
    #header .searchform-popup .search-toggle { color: <?php echo $skinColor; ?>; font-size: 13px; }

<?php elseif ( 'side' == $header_type ) : ?>
    <?php if ( !$b['side-social-bg-color'] || 'transparent' == $b['side-social-bg-color'] ) : ?>
        #header .share-links a { background-color: transparent; color: <?php echo $b['side-social-color']; ?>; }
    <?php else: ?>
        #header .share-links a:not(:hover) { background-color: <?php echo $b['side-social-bg-color']; ?>; color: <?php echo $b['side-social-color']; ?>; }
    <?php endif; ?>
    .header-wrapper #header .header-copyright { color: <?php echo $b['side-copyright-color']; ?>; }

    @media (min-width: 992px) {
        .header-wrapper #header .header-main { position: static; background: transparent; }
        <?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
        .header-wrapper #header .top-links li.menu-item > a { border-top-color: <?php echo $portoColorLib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ); ?>; }
        <?php endif; ?>
        .header-wrapper #header .top-links li.menu-item > a,
        .header-wrapper #header .top-links li.menu-item.active > a { color: <?php echo $b['mainmenu-toplevel-link-color']['regular']; ?>; }
        .header-wrapper #header .top-links li.menu-item:hover,
        .header-wrapper #header .top-links li.menu-item.active:hover { background: <?php echo $b['mainmenu-toplevel-hbg-color'] ?>; }
        .header-wrapper #header .top-links li.menu-item:hover > a,
        .header-wrapper #header .top-links li.menu-item.active:hover > a { color: <?php echo $b['mainmenu-toplevel-link-color']['hover']; ?>; }
    }
<?php endif; ?>

/* mini cart */
<?php if ( $b['minicart-popup-border-color'] ) : ?>
    #mini-cart .cart-popup { border: 1px solid <?php echo $b['minicart-popup-border-color']; ?>; }
    #mini-cart .cart-popup:after { border-bottom-color: <?php echo $b['minicart-popup-border-color']; ?>; }
<?php endif; ?>
<?php if ( $b['sticky-minicart-popup-border-color'] ) : ?>
    .sticky-header #mini-cart .cart-popup { border: 1px solid <?php echo $b['minicart-popup-border-color']; ?>; }
    .sticky-header #mini-cart .cart-popup:after { border-bottom-color: <?php echo $b['minicart-popup-border-color']; ?>; }
<?php endif; ?>
<?php if ( $b['border-radius'] && $b['minicart-bg-color'] ) : ?>
    #mini-cart { border-radius: 4px; }
<?php endif; ?>
<?php if ( $b['border-radius'] && $b['sticky-minicart-bg-color'] ) : ?>
    .sticky-header #mini-cart { border-radius: 4px; }
<?php endif; ?>

/* mobile panel */
<?php
    $panel_link_color = empty( $b['panel-link-color']['regular'] ) ? ( 'side' == $b['mobile-panel-type'] ? '#fff' : '#333' ) : $b['panel-link-color']['regular'];
?>
<?php if ( !empty( $b['panel-bg-color'] ) ) : ?>
    <?php if ( 'side' == $b['mobile-panel-type'] ) :
        echo '#side-nav-panel';
    else:
        echo '#nav-panel .mobile-nav-wrap';
    endif; ?> { background-color: <?php echo $b['panel-bg-color']; ?>; }
    <?php if ( 'side' == $b['mobile-panel-type'] ) :
        echo '#side-nav-panel .accordion-menu li.menu-item.active > a,';
        echo '#side-nav-panel .menu-custom-block a:hover';
    else:
        echo '#nav-panel .menu-custom-block a:hover';
    endif; ?> { background-color: <?php echo $portoColorLib->lighten( $b['panel-bg-color'], 5 ); ?>; }
<?php endif; ?>
<?php if ( !empty( $b['panel-text-color'] ) ) : ?>
    <?php if ( 'side' == $b['mobile-panel-type'] ) :
        echo '#side-nav-panel, #side-nav-panel .welcome-msg, #side-nav-panel .accordion-menu, #side-nav-panel .menu-custom-block, #side-nav-panel .menu-custom-block span';
    else:
        echo '#nav-panel, #nav-panel .welcome-msg, #nav-panel .accordion-menu, #nav-panel .menu-custom-block, #nav-panel .menu-custom-block span';
    endif; ?> { color: <?php echo $b['panel-text-color']; ?>; }
<?php endif; ?>
<?php if ( 'side' == $b['mobile-panel-type'] ) :
    echo '#side-nav-panel .accordion-menu li';
else:
    echo '#nav-panel .mobile-menu li';
endif; ?> { border-bottom-color: <?php echo $b['panel-border-color']; ?>; }
<?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
    #nav-panel .accordion-menu .sub-menu li:not(.active):hover > a { background: <?php echo $b['panel-link-hbgcolor']; ?>; }
    #nav-panel .accordion-menu li.menu-item > a,
    #nav-panel .accordion-menu .arrow,
    #nav-panel .menu-custom-block a { color: <?php echo $panel_link_color; ?>; }
    #nav-panel .accordion-menu > li.menu-item > a,
    #nav-panel .accordion-menu > li.menu-item > .arrow { color: <?php echo $skinColor; ?>; }
    #nav-panel .accordion-menu li.menu-item.active > a { background-color: <?php echo $skinColor; ?> }
    #nav-panel .mobile-nav-wrap::-webkit-scrollbar-thumb { background: <?php echo if_light('rgba(204, 204, 204, 0.5)', '#39404c'); ?>;<?php echo if_dark( 'border-color: transparent', '' ); ?> }
<?php else: ?>
    #side-nav-panel .accordion-menu li.menu-item > a,
    #side-nav-panel .menu-custom-block a { color: <?php echo $panel_link_color; ?>; }
<?php endif; ?>
<?php if ( !empty( $b['panel-link-color']['hover'] ) ) : ?>
    <?php if ( empty( $b['mobile-panel-type'] ) ) : ?>
        #nav-panel .accordion-menu li.menu-item:hover > a,
        #nav-panel .accordion-menu .arrow:hover,
        #nav-panel .menu-custom-block a:hover { color: <?php echo $b['panel-link-color']['hover']; ?>; }
    <?php else: ?>
        #side-nav-panel .accordion-menu li.menu-item.active > a,
        #side-nav-panel .menu-custom-block a:hover { color: <?php echo $b['panel-link-color']['hover']; ?>; }
    <?php endif; ?>
<?php endif; ?>

/* footer */
.footer-wrapper.fixed #footer .footer-bottom {
<?php if ( empty( $b['footer-bottom-bg']['background-color'] ) ) : ?>
<?php elseif ( 'transparent' == $b['footer-bottom-bg']['background-color'] ) : ?>
    box-shadow: none;
<?php else: ?>
    background-color: rgba(<?php echo $portoColorLib->hexToRGB( $b['footer-bottom-bg']['background-color'] ); ?>, <?php echo $footer_opacity; ?>);
<?php endif; ?>
}

/* skin color */
<?php 
    // Color Variables
    $themeColors = array( 'primary' => $skinColor, 'secondary' => $b['secondary-color'], 'tertiary' => $b['tertiary-color'], 'quaternary' => $b['quaternary-color'], 'dark' => $b['dark-color'], 'light' => $b['light-color'] );
    $themeColorsInverse = array( 'primary' => $b['skin-color-inverse'], 'secondary' => $b['secondary-color-inverse'], 'tertiary' => $b['tertiary-color-inverse'], 'quaternary' => $b['quaternary-color-inverse'], 'dark' => $b['dark-color-inverse'], 'light' => $b['light-color-inverse'] );

?>
body,
ul.list.icons li a,
.pricing-table li,
.pricing-table h3 .desc,
.pricing-table h3 span,
.pricing-table .plan,
.home-intro .get-started a:not(.btn),
.page-not-found h4,
.color-body,
.color-body a,
.color-body a:hover,
.color-body a:focus,
.mobile-sidebar .sidebar-toggle,
.page-top .product-nav .product-popup,
.thumb-info-bottom-info .thumb-info-title,
.thumb-info-bottom-info .thumb-info-title a,
.thumb-info-bottom-info .thumb-info-title a:hover,
.tabs.tabs-simple .nav-tabs > li .nav-link,
.tabs.tabs-simple .nav-tabs > li .nav-link:hover,
.tabs.tabs-simple .nav-tabs > li .nav-link:focus,
.tabs.tabs-simple .nav-tabs > li.active .nav-link,
.tabs.tabs-simple .nav-tabs > li.active .nav-link:hover,
.tabs.tabs-simple .nav-tabs > li.active .nav-link:focus,
.porto-links-block li.porto-links-item > a,
.porto-links-block li.porto-links-item > span,
.vc_general.vc_cta3.vc_cta3-color-white.vc_cta3-style-flat,
.widget .tagcloud a,
.mega-menu .wide .popup,
.mega-menu .wide .popup li.menu-item li.menu-item > a,
.sidebar-menu .popup,
.post-title-simple .post-author p .name,
.post-title-simple .post-author p .name a,
.testimonial.testimonial-with-quotes blockquote:before,
.testimonial.testimonial-style-2 blockquote p,
.testimonial.testimonial-style-5 blockquote p,
.testimonial.testimonial-style-6 blockquote p,
.testimonial.testimonial-style-3 blockquote:before,
.testimonial.testimonial-style-4 blockquote:before,
.testimonial.testimonial-style-3 blockquote:after,
.testimonial.testimonial-style-4 blockquote:after,
.testimonial.testimonial-style-3 blockquote p,
.testimonial.testimonial-style-4 blockquote p,
.testimonial.testimonial-with-quotes blockquote:after,
.testimonial.testimonial-with-quotes blockquote:before,
.testimonial.testimonial-with-quotes blockquote p {
    color: <?php echo $bodyColor; ?>;
}

.widget > div > ul li,
.widget > ul li,
.widget > div > ul li > a,
.widget > ul li > a {
    color: <?php echo $portoColorLib->darken( $bodyColor, 6.67 ); ?>;
}

.widget .rss-date,
.widget .post-date,
.widget .comment-author-link {
    color: <?php echo $portoColorLib->lighten( $bodyColor, 6.67 ); ?>;
}

article.post .post-title,
ul.list.icons li i,
ul.list.icons li a:hover,
.list.list-icons li .fa,
.list.list-ordened li:before,
ul[class^="wsp-"] li:before,
.fontawesome-icon-list > div:hover,
.sample-icon-list > div:hover,
.fontawesome-icon-list > div:hover .text-muted,
.sample-icon-list > div:hover .text-muted,
.accordion .card-header a,
section.toggle label,
.porto-concept strong,
.fc-slideshow nav .fc-left i,
.fc-slideshow nav .fc-right i,
.circular-bar.only-icon .fa,
.home-intro p em,
.home-intro.light p,
.woocommerce .featured-box h2,
.woocommerce-page .featured-box h2,
.woocommerce .featured-box h3,
.woocommerce-page .featured-box h3,
.woocommerce .featured-box h4,
.woocommerce-page .featured-box h4,
.featured-box .porto-sicon-header h3.porto-sicon-title,
.featured-box .wpb_heading,
.featured-boxes-style-3 .featured-box .icon-featured,
.featured-boxes-style-4 .featured-box .icon-featured,
.featured-boxes-style-5 .featured-box .icon-featured,
.featured-boxes-style-6 .featured-box .icon-featured,
.featured-boxes-style-7 .featured-box .icon-featured,
.featured-boxes-style-8 .featured-box .icon-featured,
.feature-box.feature-box-style-2 .feature-box-icon i,
.feature-box.feature-box-style-3 .feature-box-icon i,
.feature-box.feature-box-style-4 .feature-box-icon i,
.feature-box.feature-box-style-5 .feature-box-icon i,
.feature-box.feature-box-style-6 .feature-box-icon i,
.mobile-sidebar .sidebar-toggle:hover,
.sidebar-content .filter-title,
.page-top .sort-source > li.active > a,
.product-thumbs-slider.owl-carousel .thumb-nav .thumb-next,
.product-thumbs-slider.owl-carousel .thumb-nav .thumb-prev,
.master-slider .ms-container .ms-nav-prev,
.master-slider .ms-container .ms-nav-next,
.master-slider .ms-container .ms-slide-vpbtn,
.master-slider .ms-container .ms-video-btn,
.resp-tabs-list li,
h2.resp-accordion,
.tabs ul.nav-tabs a,
.tabs ul.nav-tabs a:hover,
.tabs ul.nav-tabs li.active a,
.tabs ul.nav-tabs li.active a:hover,
.tabs ul.nav-tabs li.active a:focus,
.wpb_wrapper .porto-sicon-read,
.vc_custom_heading em,
.widget .widgettitle a:hover,
.widget .widget-title a:hover,
.widget li > a:hover,
.widget li.current-cat > a,
.widget li.current-cat-parent > a,
.widget li.active > a,
.widget li.current-menu-item > a,
.widget_wysija_cont .showerrors,
.sidebar-menu > li.menu-item.active > a,
.post-block h3,
.post-share h3,
article.post .comment-respond h3,
article.portfolio .comment-respond h3,
.related-posts h3,
article.post .post-date .day,
.post-item .post-date .day,
section.timeline .timeline-date h3,
.post-carousel .post-item.style-5 .cat-names,
.post-grid .post-item.style-5 .cat-names,
.post-timeline .post-item.style-5 .cat-names,
.post-carousel .post-item.style-5 .post-meta .post-views-icon.dashicons,
.post-grid .post-item.style-5 .post-meta .post-views-icon.dashicons,
.post-timeline .post-item.style-5 .post-meta .post-views-icon.dashicons,
.portfolio-info ul li a:hover,
article.member .member-role,
.tm-extra-product-options .tm-epo-field-label,
.tm-extra-product-options-totals .amount.final,
html #topcontrol:hover {
    color: <?php echo $skinColor ?>;
}

a:hover,
.wpb_wrapper .porto-sicon-read:hover { color: <?php echo $portoColorLib->lighten( $skinColor, 5 ); ?>; }

a:active, a:focus { color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>; }

.slick-slider .slick-dots li.slick-active i,
.slick-slider .slick-dots li:hover i {
    color: <?php echo $portoColorLib->darken( $skinColor, 6 ); ?> !important;
}

.list.list-icons li .fa,
.list.list-ordened li:before,
<?php if ( !$dark ) : ?>
    .pricing-table .most-popular,
<?php endif; ?>
section.toggle.active > label,
.timeline-balloon .balloon-time .time-dot:before,
.featured-box .icon-featured:after,
.featured-boxes-style-3 .featured-box .icon-featured,
.featured-boxes-style-4 .featured-box .icon-featured,
.feature-box.feature-box-style-3 .feature-box-icon,
.owl-carousel.dots-color-primary .owl-dots .owl-dot,
.master-slider .ms-slide .ms-slide-loading:before,
.widget .tagcloud a:hover,
.widget_sidebar_menu .widget-title .toggle:hover,
.pagination a:hover,
.page-links a:hover,
.pagination a:focus,
.page-links a:focus,
.pagination span.current,
.page-links span.current,
#footer .widget .tagcloud a:hover {
    border-color: <?php echo $skinColor ?>;
}

section.toggle label,
.resp-vtabs .resp-tabs-list li:hover,
.resp-vtabs .resp-tabs-list li:focus,
.resp-vtabs .resp-tabs-list li.resp-tab-active,
.sidebar-menu .wide .popup {
    border-<?php echo $left; ?>-color: <?php echo $skinColor ?>;
}

.tabs.tabs-vertical.tabs-left ul.nav-tabs li .nav-link:hover,
.tabs.tabs-vertical.tabs-left ul.nav-tabs li.active .nav-link,
.tabs.tabs-vertical.tabs-left ul.nav-tabs li.active .nav-link:hover,
.tabs.tabs-vertical.tabs-left ul.nav-tabs li.active .nav-link:focus {
    border-left-color: <?php echo $skinColor ?>;
}

.thumb-info-ribbon:before,
.right-sidebar .sidebar-menu .wide .popup {
    border-<?php echo $right; ?>-color: <?php echo $portoColorLib->darken( $skinColor, 15 ); ?>;
}

.tabs.tabs-vertical.tabs-right ul.nav-tabs li .nav-link:hover,
.tabs.tabs-vertical.tabs-right ul.nav-tabs li.active .nav-link,
.tabs.tabs-vertical.tabs-right ul.nav-tabs li.active .nav-link:hover,
.tabs.tabs-vertical.tabs-right ul.nav-tabs li.active .nav-link:focus {
    border-right-color: <?php echo $skinColor ?>;
}

.porto-history .featured-box .box-content,
body.boxed .page-wrapper,
.master-slider .ms-loading-container .ms-loading:before,
.master-slider .ms-slide .ms-slide-loading:before,
#fancybox-loading:before,
#fancybox-loading:after,
.slick-slider .slick-loading .slick-list:before,
.fullscreen-carousel > .owl-carousel:before,
.fullscreen-carousel > .owl-carousel:after,
.porto-loading-icon,
.resp-tabs-list li:hover,
.resp-tabs-list li:focus,
.resp-tabs-list li.resp-tab-active,
.tabs ul.nav-tabs a:hover,
.tabs ul.nav-tabs a:focus,
.tabs ul.nav-tabs li.active a,
.tabs ul.nav-tabs li.active a:hover,
.tabs ul.nav-tabs li.active a:focus,
.tabs ul.nav-tabs.nav-justified .nav-link:hover,
.tabs ul.nav-tabs.nav-justified .nav-link:focus,
.sidebar-content .widget.widget_wysija .box-content,
.mega-menu .wide .popup,
.sidebar-menu > li.menu-item:hover > a {
    border-top-color: <?php echo $skinColor; ?>;
}

.testimonial .testimonial-arrow-down {
    border-top-color: <?php echo $portoColorLib->lighten( $skinColor, 5 ); ?>;
}

.page-top .product-nav .product-popup:before,
.tabs.tabs-bottom ul.nav-tabs li .nav-link:hover,
.tabs.tabs-bottom ul.nav-tabs li.active a,
.tabs.tabs-bottom ul.nav-tabs li.active a:hover,
.tabs.tabs-bottom ul.nav-tabs li.active a:focus,
.tabs.tabs-simple .nav-tabs > li .nav-link:hover,
.tabs.tabs-simple .nav-tabs > li .nav-link:focus,
.tabs.tabs-simple .nav-tabs > li.active .nav-link {
    border-bottom-color: <?php echo $skinColor; ?>;
}

.product-thumbs-slider.owl-carousel .owl-item.selected .img-thumbnail,
.product-thumbs-slider.owl-carousel .owl-item:hover .img-thumbnail {
    border: 2px solid <?php echo $skinColor; ?>;
}

article.post .post-date .month,
article.post .post-date .format,
.post-item .post-date .month,
.post-item .post-date .format,
.list.list-icons.list-icons-style-3 li .fa,
.list.list-ordened.list-ordened-style-3 li:before,
html .list-primary.list-ordened.list-ordened-style-3 li:before,
html .list-secondary.list-ordened.list-ordened-style-3 li:before,
html .list-tertiary.list-ordened.list-ordened-style-3 li:before,
html .list-quaternary.list-ordened.list-ordened-style-3 li:before,
html .list-dark.list-ordened.list-ordened-style-3 li:before,
html .list-light.list-ordened.list-ordened-style-3 li:before,
ul.nav-pills > li.active > a,
ul.nav-pills > li.active > a:hover,
ul.nav-pills > li.active > a:focus,
section.toggle.active > label,
.toggle-simple section.toggle > label:after,
div.wpb_single_image .porto-vc-zoom .zoom-icon,
.img-thumbnail .zoom,
.thumb-info .zoom,
.img-thumbnail .link,
.thumb-info .link,
.pricing-table .most-popular h3,
.pricing-table.pricing-table-flat .plan h3,
.pricing-table.pricing-table-flat .plan h3 span,
.timeline-balloon .balloon-time .time-dot:after,
section.exp-timeline .timeline-box.right:after,
.floating-menu .floating-menu-btn-collapse-nav,
.icon-featured,
.featured-box .icon-featured,
.featured-box-effect-3:hover .icon-featured,
.feature-box .feature-box-icon,
.inverted,
.master-slider .ms-container .ms-bullet,
.share-links a,
.thumb-info .thumb-info-type,
.thumb-info .thumb-info-action-icon,
.thumb-info-ribbon,
.thumb-info-social-icons a,
.widget .tagcloud a:hover,
.widget_sidebar_menu .widget-title .toggle:hover,
.mega-menu > li.menu-item.active > a,
.mega-menu > li.menu-item:hover > a,
.mega-menu .narrow ul.sub-menu,
.sidebar-menu > li.menu-item:hover,
.sidebar-menu .menu-custom-block a:hover,
.pagination a:hover,
.page-links a:hover,
.pagination a:focus,
.page-links a:focus,
.pagination span.current,
.page-links span.current,
.member-item.member-item-3 .thumb-info:hover .thumb-info-caption,
#footer .widget .tagcloud a:hover {
    background-color: <?php echo $skinColor ?>;
}

div.wpb_single_image .porto-vc-zoom .zoom-icon:hover,
.img-thumbnail .zoom:hover,
.thumb-info .zoom:hover,
.img-thumbnail .link:hover,
.thumb-info .link:hover,
.mega-menu .narrow li.menu-item:hover > a,
.testimonial blockquote {
    background-color: <?php echo $portoColorLib->lighten( $skinColor, 5 ); ?>;
}

.owl-carousel .owl-dots .owl-dot.active span,
.owl-carousel .owl-dots .owl-dot:hover span {
    background-color: <?php echo $portoColorLib->darken( $skinColor, 6 ); ?>;
}

.featured-box-effect-2 .icon-featured:after { box-shadow: 0 0 0 3px <?php echo $skinColor; ?>; }
.featured-box-effect-3 .icon-featured:after { box-shadow: 0 0 0 10px <?php echo $skinColor; ?>; }

section.toggle.active > label,
.pricing-table .most-popular h3,
.pricing-table .most-popular h3 .desc,
.pricing-table.pricing-table-flat .plan h3,
.pricing-table.pricing-table-flat .plan h3 .desc,
.pricing-table.pricing-table-flat .plan h3 span,
ul.nav-pills > li.active > a,
ul.nav-pills > li.active > a:hover,
ul.nav-pills > li.active > a:focus,
.tparrows.tparrows-carousel.tp-leftarrow:before,
.tparrows.tparrows-carousel.tp-rightarrow:before,
.thumb-info .thumb-info-action-icon i,
.thumb-info-ribbon,
.thumb-info-social-icons a i,
.portfolio-item .thumb-info .thumb-info-type .portfolio-like i,
.portfolio-item .thumb-info .thumb-info-type .portfolio-liked i,
.member-item.member-item-3 .thumb-info:hover .thumb-info-caption,
.member-item.member-item-3 .thumb-info:hover .thumb-info-caption * {
    color: <?php echo $b['skin-color-inverse']; ?>;
}

.member-item.member-item-3 .thumb-info:hover .thumb-info-social-icons {
    border-color: <?php echo $b['skin-color-inverse']; ?>;
}

.member-item.member-item-3 .thumb-info:hover .share-links a {
    background-color: <?php echo $b['skin-color-inverse']; ?>;
    color: <?php echo $skinColor; ?>;
}

@media (min-width: 992px) {
    .floating-menu .floating-menu-nav-main nav > ul > li > a:after { background-color: <?php echo $skinColor ?>; }
}

/* secondary color */
<?php if ( $b['secondary-color'] ) : ?>
    .post-carousel .post-item.style-5 .post-meta a,
    .post-grid .post-item.style-5 .post-meta a,
    .post-timeline .post-item.style-5 .post-meta a {
        color: <?php echo $b['secondary-color']; ?>;
    }
<?php endif; ?>

/* quaternary color */
<?php if ( $b['quaternary-color'] ) : ?>
    .post-share-advance-bg,
    .post-share-advance .fa-share-alt {
        background: <?php echo $b['quaternary-color']; ?>;
    }
<?php endif; ?>

/* dark color */
<?php if ( $b['dark-color'] ) : ?>
    section.exp-timeline .timeline-bar { background-color: <?php echo $b['dark-color']; ?>; }

    section.exp-timeline .timeline-box.right:before {
        background-color: <?php echo $b['dark-color']; ?> !important;
        box-shadow: 0 0 0 3px #ecf1f7, 0 0 0 6px <?php echo $b['dark-color']; ?> !important;
    }
<?php endif; ?>

/* misc */
<?php foreach( $themeColors as $key => $themeColor ) : ?>
    html .list-<?php echo $key; ?>.list-icons li .fa,
    html .list-<?php echo $key; ?>.list-ordened li:before,
    html ul.nav-pills-<?php echo $key; ?> a,
    html .toggle-<?php echo $key; ?> .toggle label,
    html .divider.divider-<?php echo $key; ?> i,
    .feature-box.feature-box-<?php echo $key; ?>[class*="feature-box-style-"] .feature-box-icon i,
    .featured-box-<?php echo $key; ?> h4,
    .featured-boxes-style-3 .featured-box.featured-box-<?php echo $key; ?> .icon-featured,
    .featured-boxes-style-4 .featured-box.featured-box-<?php echo $key; ?> .icon-featured,
    .featured-boxes-style-5 .featured-box.featured-box-<?php echo $key; ?> .icon-featured,
    .featured-boxes-style-6 .featured-box.featured-box-<?php echo $key; ?> .icon-featured,
    .featured-boxes-style-8 .featured-box.featured-box-<?php echo $key; ?> .icon-featured,
    .featured-box-effect-7.featured-box-<?php echo $key; ?> .icon-featured:before { color: <?php echo $themeColor; ?>; }

    html .heading-<?php echo $key; ?>,
    html .lnk-<?php echo $key; ?>,
    html .text-color-<?php echo $key; ?> { color: <?php echo $themeColor; ?> !important; }

    html ul.nav-pills-<?php echo $key; ?> a:hover,
    html ul.nav-pills-<?php echo $key; ?> a:focus { color: <?php echo $portoColorLib->lighten( $themeColor, 5 ); ?>; }
    html ul.nav-pills-<?php echo $key; ?> a:active { color: <?php echo $portoColorLib->darken( $themeColor, 5 ); ?>; }

    html .list-<?php echo $key; ?>.list-icons.list-icons-style-3 li .fa,
    html ul.nav-pills-<?php echo $key; ?> > li.active > a,
    html ul.nav-pills-<?php echo $key; ?> > li.active > a:hover,
    html ul.nav-pills-<?php echo $key; ?> > li.active > a:focus,
    html .toggle-<?php echo $key; ?> .toggle.active > label,
    html .toggle-<?php echo $key; ?>.toggle-simple .toggle > label:after,
    html .label-<?php echo $key; ?>,
    html .alert-<?php echo $key; ?>,
    html .divider.divider-<?php echo $key; ?>.divider-small hr,
    html .divider.divider-style-2.divider-<?php echo $key; ?> i,
    html .pricing-table .plan-<?php echo $key; ?> h3,
    html .pricing-table.pricing-table-flat .plan-<?php echo $key; ?> h3,
    html .pricing-table.pricing-table-flat .plan-<?php echo $key; ?> h3 span,
    html .home-intro-<?php echo $key; ?>,
    .feature-box .feature-box-icon-<?php echo $key; ?>,
    .featured-box-<?php echo $key; ?> .icon-featured,
    html .inverted-<?php echo $key; ?>,
    html .thumb-info .thumb-info-action-icon-<?php echo $key; ?>,
    html .thumb-info-ribbon-<?php echo $key; ?>,
    html .thumb-info-social-icons a.thumb-info-social-links-<?php echo $key; ?> { background-color: <?php echo $themeColor; ?>; }

    html .accordion.accordion-<?php echo $key; ?> .card-header,
    html .section.section-<?php echo $key; ?>,
    html .popover-<?php echo $key; ?> .popover-title,
    html .background-color-<?php echo $key; ?>,
    .featured-box-effect-3.featured-box-<?php echo $key; ?>:hover .icon-featured { background-color: <?php echo $themeColor; ?> !important; }
    html .section.section-<?php echo $key; ?>-scale-2 { background-color: <?php echo $portoColorLib->darken( $themeColor, 10 ); ?> !important; }

    html .list-<?php echo $key; ?>.list-icons.list-icons-style-3 li .fa,
    html .list-<?php echo $key; ?>.list-ordened.list-ordened-style-3 li:before,
    html .accordion.accordion-<?php echo $key; ?> .card-header a,
    html .toggle-<?php echo $key; ?> .toggle.active > label,
    html .alert-<?php echo $key; ?>,
    html .alert-<?php echo $key; ?> .alert-link,
    html .section.section-<?php echo $key; ?>,
    html .section.section-<?php echo $key; ?>:not([class*=" section-text-"]) h1,
    html .section.section-<?php echo $key; ?>:not([class*=" section-text-"]) h2,
    html .section.section-<?php echo $key; ?>:not([class*=" section-text-"]) h3,
    html .section.section-<?php echo $key; ?>:not([class*=" section-text-"]) h4,
    html .section.section-<?php echo $key; ?>:not([class*=" section-text-"]) h5,
    html .section.section-<?php echo $key; ?>:not([class*=" section-text-"]) h6,
    html .section.section-<?php echo $key; ?>-scale-2 .sort-source.sort-source-style-2 > li > a,
    html .section.section-<?php echo $key; ?>-scale-2 .sort-source.sort-source-style-2 > li > a:focus,
    html .section.section-<?php echo $key; ?>-scale-2 .sort-source.sort-source-style-2 > li > a:hover,
    html .divider.divider-style-2.divider-<?php echo $key; ?> i,
    html .pricing-table .plan-<?php echo $key; ?> h3,
    html .pricing-table .plan-<?php echo $key; ?> h3 .desc,
    html .pricing-table.pricing-table-flat .plan-<?php echo $key; ?> h3,
    html .pricing-table.pricing-table-flat .plan-<?php echo $key; ?> h3 .desc,
    html .pricing-table.pricing-table-flat .plan-<?php echo $key; ?> h3 span,
    html .home-intro-<?php echo $key; ?>,
    html .home-intro-<?php echo $key; ?> .get-started a:not(.btn),
    html .home-intro-<?php echo $key; ?> p,
    html .home-intro-<?php echo $key; ?> p em,
    html .home-intro-<?php echo $key; ?>.light p,
    html .thumb-info .thumb-info-action-icon-<?php echo $key; ?> i,
    html .thumb-info-ribbon-<?php echo $key; ?>,
    html .thumb-info-social-icons a.thumb-info-social-links-<?php echo $key; ?> i { color: <?php echo $themeColorsInverse[$key]; ?>; }

    html .section.section-<?php echo $key; ?>:not([class*=" section-text-"]) p { color: <?php echo $portoColorLib->darken( $themeColorsInverse[$key], 10 ); ?>; }
    html .popover-<?php echo $key; ?> .popover-title { color: <?php echo $themeColorsInverse[$key]; ?> !important; }

    html .list-<?php echo $key; ?>.list-icons li .fa,
    html .toggle-<?php echo $key; ?> .toggle.active > label,
    html .label-<?php echo $key; ?>,
    <?php if ( !$dark ) : ?>
        html .pricing-table .plan-<?php echo $key; ?>,
    <?php endif; ?>
    html .divider.divider-style-3.divider-<?php echo $key; ?> i,
    .featured-box-<?php echo $key; ?> .icon-featured:after,
    .featured-boxes-style-3 .featured-box.featured-box-<?php echo $key; ?> .icon-featured,
    .featured-boxes-style-4 .featured-box.featured-box-<?php echo $key; ?> .icon-featured,
    html .heading.heading-<?php echo $key; ?> h1,
    html .heading.heading-<?php echo $key; ?> h2,
    html .heading.heading-<?php echo $key; ?> h3,
    html .heading.heading-<?php echo $key; ?> h4,
    html .heading.heading-<?php echo $key; ?> h5,
    html .heading.heading-<?php echo $key; ?> h6 { border-color: <?php echo $themeColor; ?>; }

    html .blockquote-<?php echo $key; ?> { border-color: <?php echo $themeColor; ?> !important; }

    .featured-box-<?php echo $key; ?> .box-content { border-top-color: <?php echo $themeColor; ?>; }

    html .toggle-<?php echo $key; ?> .toggle label { border-left-color: <?php echo $themeColor; ?>; border-right-color: <?php echo $themeColor; ?>; }
    html .alert-<?php echo $key; ?> { border-color: <?php echo $portoColorLib->darken( $themeColor, 3 ); ?>; }
    html .section.section-<?php echo $key; ?> { border-color: <?php echo $portoColorLib->darken( $themeColor, 5 ); ?> !important; }
    html .section.section-<?php echo $key; ?>-scale-2 { border-color: <?php echo $portoColorLib->darken( $themeColor, 15 ); ?> !important; }
    html .section.section-<?php echo $key; ?>-scale-2 .sort-source.sort-source-style-2 > li.active > a:after { border-top-color: <?php echo $portoColorLib->darken( $themeColor, 10 ); ?>; }

    html .thumb-info-ribbon-<?php echo $key; ?>:before {
        border-<?php echo $right; ?>-color: <?php echo $portoColorLib->darken( $themeColor, 15 ); ?>;
    }

    .featured-box-effect-2.featured-box-<?php echo $key; ?> .icon-featured:after { box-shadow: 0 0 0 3px <?php echo $themeColor; ?>; }
    .featured-box-effect-3.featured-box-<?php echo $key; ?> .icon-featured:after { box-shadow: 0 0 0 10px <?php echo $themeColor; ?>; }

    html .toggle-<?php echo $key; ?>.toggle-simple .toggle > label {
        background: transparent;
        <?php if ( $b['h3-font']['color'] ) : ?>
            color: <?php echo $b['h3-font']['color']; ?>;
        <?php endif; ?>
    }

<?php endforeach; ?>

<?php
    if ( $dark ) {
        $color_background = '#1d2127';
        $functionName = 'lighten';
    } else {
        $color_background = '#f4f4f4';
        $functionName = 'darken';
    }

    $color_background_scale = array( $portoColorLib->$functionName( $color_background, 10 ), $portoColorLib->$functionName( $color_background, 20 ), $portoColorLib->$functionName( $color_background, 30 ), $portoColorLib->$functionName( $color_background, 40 ), $portoColorLib->$functionName( $color_background, 50 ), $portoColorLib->$functionName( $color_background, 60 ), $portoColorLib->$functionName( $color_background, 70 ), $portoColorLib->$functionName( $color_background, 80 ), $portoColorLib->$functionName( $color_background, 90 ) );
?>
<?php foreach( $color_background_scale as $index => $value ) : ?>
    html .section.section-default-scale-<?php echo $index + 1; ?> {
        background-color: <?php echo $value; ?> !important;
        border-top-color: <?php echo $portoColorLib->darken( $value, 3 ); ?> !important;
    }
<?php endforeach; ?>

<?php if ( $b['h1-font']['color'] ) : ?>
    h1 { color: <?php echo $b['h1-font']['color']; ?>; }
<?php endif; ?>
<?php if ( $b['h2-font']['color'] ) : ?>
    h2,
    article.post.post-title-simple .post-title,
    .post-item.post-title-simple .post-title,
    article.post.post-title-simple .post-title h2,
    .post-item.post-title-simple .post-title h2,
    article.post.post-title-simple .entry-title,
    .post-item.post-title-simple .entry-title,
    article.post.post-title-simple .entry-title a,
    .post-item.post-title-simple .entry-title a { color: <?php echo $b['h2-font']['color']; ?>; }
<?php endif; ?>
<?php if ( $b['h3-font']['color'] ) : ?>
    h3 { color: <?php echo $b['h3-font']['color']; ?>; }
<?php endif; ?>
<?php if ( $b['h4-font']['color'] ) : ?>
    h4,
    .member-item.member-item-3 .view-more,
    .fdm-item-panel .fdm-item-title { color: <?php echo $b['h4-font']['color']; ?>; }
<?php endif; ?>
<?php if ( $b['h5-font']['color'] ) : ?>
    h5 { color: <?php echo $b['h5-font']['color']; ?>; }
<?php endif; ?>
<?php if ( $b['h6-font']['color'] ) : ?>
    h6 { color: <?php echo $b['h6-font']['color']; ?>; }
<?php endif; ?>

/* side menu */
.header-side-nav .sidebar-menu > li.menu-item > a,
.toggle-menu-wrap .sidebar-menu > li.menu-item > a,
.header-side-nav .sidebar-menu .menu-custom-block span,
.toggle-menu-wrap .sidebar-menu .menu-custom-block span,
.header-side-nav .sidebar-menu .menu-custom-block a,
.toggle-menu-wrap .sidebar-menu .menu-custom-block a {
    <?php if ( $b['menu-side-font']['font-family'] ) : ?>
        font-family: <?php echo $b['menu-side-font']['font-family']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-side-font']['font-size'] ) : ?>
        font-size: <?php echo $b['menu-side-font']['font-size']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-side-font']['font-weight'] ) : ?>
        font-weight: <?php echo $b['menu-side-font']['font-weight']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-side-font']['line-height'] ) : ?>
        line-height: <?php echo $b['menu-side-font']['line-height']; ?>;
    <?php endif; ?>
    <?php if ( $b['menu-side-font']['letter-spacing'] ) : ?>
        letter-spacing: <?php echo $b['menu-side-font']['letter-spacing']; ?>;
    <?php endif; ?>
}
<?php if ( $b['mainmenu-toplevel-link-color']['regular'] ) : ?>
    .header-side-nav .sidebar-menu > li.menu-item > a,
    .toggle-menu-wrap .sidebar-menu > li.menu-item > a,
    .header-side-nav .sidebar-menu > li.menu-item.active > a,
    .toggle-menu-wrap .sidebar-menu > li.menu-item.active > a,
    .header-side-nav .sidebar-menu > li.menu-item > .arrow:before,
    .toggle-menu-wrap .sidebar-menu > li.menu-item > .arrow:before,
    .header-side-nav .sidebar-menu .menu-custom-block a,
    .toggle-menu-wrap .sidebar-menu .menu-custom-block a {
        color: <?php echo $b['mainmenu-toplevel-link-color']['regular']; ?>
    }
<?php endif; ?>
<?php if ( $b['mainmenu-toplevel-hbg-color'] ) : ?>
    .header-side-nav .sidebar-menu > li.menu-item > a,
    .toggle-menu-wrap .sidebar-menu > li.menu-item > a,
    .header-side-nav .sidebar-menu > li.menu-item:hover > a,
    .toggle-menu-wrap .sidebar-menu > li.menu-item:hover > a,
    .header-side-nav .sidebar-menu .menu-custom-block a,
    .toggle-menu-wrap .sidebar-menu .menu-custom-block a,
    .header-side-nav .sidebar-menu .menu-custom-block a:hover,
    .toggle-menu-wrap .sidebar-menu .menu-custom-block a:hover,
    .header-side-nav .sidebar-menu .menu-custom-block a:hover + a,
    .toggle-menu-wrap .sidebar-menu .menu-custom-block a:hover + a {
        border-top-color: <?php echo $portoColorLib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ); ?>;
    }
    .header-side-nav .sidebar-menu > li.menu-item:hover,
    .toggle-menu-wrap .sidebar-menu > li.menu-item:hover,
    .header-side-nav .sidebar-menu > li.menu-item.active:hover,
    .toggle-menu-wrap .sidebar-menu > li.menu-item.active:hover,
    .header-side-nav .sidebar-menu .narrow ul.sub-menu,
    .toggle-menu-wrap .sidebar-menu .narrow ul.sub-menu,
    .header-side-nav .sidebar-menu .menu-custom-block a:hover,
    .toggle-menu-wrap .sidebar-menu .menu-custom-block a:hover {
        background-color: <?php echo $b['mainmenu-toplevel-hbg-color']; ?>;
    }
    .header-side-nav .sidebar-menu .narrow li.menu-item:hover > a,
    .toggle-menu-wrap .sidebar-menu .narrow li.menu-item:hover > a {
        background-color: <?php echo $portoColorLib->lighten( $b['mainmenu-toplevel-hbg-color'], 5 ); ?>;
    }
    .header-side-nav .sidebar-menu .wide .popup,
    .toggle-menu-wrap .sidebar-menu .wide .popup {
        border-<?php echo $left; ?>-color: <?php echo $b['mainmenu-toplevel-hbg-color']; ?>;
    }
<?php endif; ?>
<?php if ( $b['mainmenu-toplevel-link-color']['hover'] ) : ?>
    .header-side-nav .sidebar-menu > li.menu-item:hover > a,
    .toggle-menu-wrap .sidebar-menu > li.menu-item:hover > a,
    .header-side-nav .sidebar-menu > li.menu-item.active:hover > a,
    .toggle-menu-wrap .sidebar-menu > li.menu-item.active:hover > a,
    .header-side-nav .sidebar-menu > li.menu-item:hover > .arrow:before,
    .toggle-menu-wrap .sidebar-menu > li.menu-item:hover > .arrow:before,
    .header-side-nav .sidebar-menu .narrow li.menu-item > a,
    .toggle-menu-wrap .sidebar-menu .narrow li.menu-item > a,
    .header-side-nav .sidebar-menu .menu-custom-block a:hover,
    .toggle-menu-wrap .sidebar-menu .menu-custom-block a:hover {
        color: <?php echo $b['mainmenu-toplevel-link-color']['hover']; ?>;
    }
<?php endif; ?>
.toggle-menu-wrap .sidebar-menu > li.menu-item > a { border-top-color: rgba(0, 0, 0, 0.125); }

/* breadcrumb */
.page-top {
    <?php if ( $b['breadcrumbs-top-border']['border-top'] && '0px' != $b['breadcrumbs-top-border']['border-top'] ) : ?>
        border-top: <?php echo $b['breadcrumbs-top-border']['border-top']; ?> solid <?php echo $b['breadcrumbs-top-border']['border-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['breadcrumbs-bottom-border']['border-top'] && '0px' != $b['breadcrumbs-bottom-border']['border-top'] ) : ?>
        border-bottom: <?php echo $b['breadcrumbs-bottom-border']['border-top']; ?> solid <?php echo $b['breadcrumbs-bottom-border']['border-color']; ?>;
    <?php endif; ?>
}
.page-top > .container {
    padding: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-top']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-'. $right]) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-bottom']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-'. $left]) ?>px;
}
<?php if ( $b['breadcrumbs-text-color'] ) : ?>
    .page-top ul.breadcrumb > li,
    .page-top ul.breadcrumb > li .delimiter,
    .page-top .yoast-breadcrumbs,
    .page-top .breadcrumbs-wrap {
        color: <?php echo $b['breadcrumbs-text-color']; ?>;
    }
<?php endif; ?>
<?php if ( $b['breadcrumbs-link-color'] ) : ?>
    .page-top ul.breadcrumb > li a,
    .page-top .yoast-breadcrumbs a,
    .page-top .breadcrumbs-wrap a,
    .page-top .product-nav .product-link {
        color: <?php echo $b['breadcrumbs-link-color']; ?>;
    }
<?php endif; ?>
.page-top .page-title {
    <?php if ( $b['breadcrumbs-title-color'] ) : ?>
        color: <?php echo $b['breadcrumbs-title-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['h1-font']['font-family'] ) : ?>
        font-family: <?php echo $b['h1-font']['font-family']; ?>;
    <?php endif; ?>
}
.page-top .page-sub-title {
    <?php if ( $b['breadcrumbs-subtitle-color'] ) : ?>
        color: <?php echo $b['breadcrumbs-subtitle-color']; ?>;
    <?php endif; ?>
    margin: <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-top']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-'. $right]) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-bottom']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-'. $left]) ?>px;
}
<?php if ( $b['breadcrumbs-title-color'] ) : ?>
    .page-top .sort-source > li > a { color: <?php echo $b['breadcrumbs-title-color']; ?>; }
<?php endif; ?>
@media (max-width: 767px) {
    .page-top .sort-source {
        <?php if ( $b['breadcrumbs-bg']['background-color'] ) : ?>
            background: <?php echo $b['breadcrumbs-bg']['background-color']; ?>;
        <?php endif; ?>
        <?php if ( $b['breadcrumbs-bottom-border']['border-top'] && '0px' != $b['breadcrumbs-bottom-border']['border-top'] ) : ?>
            border-top: <?php echo $b['breadcrumbs-bottom-border']['border-top']; ?> solid <?php echo $b['breadcrumbs-bottom-border']['border-color']; ?>;
        <?php endif; ?>
        <?php if ( $b['breadcrumbs-padding']['padding-'. $left] ) : ?>
            padding-<?php echo $left; ?>: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-'. $left]) ?>px;
            margin-<?php echo $left; ?>: -<?php echo porto_config_value($b['breadcrumbs-padding']['padding-'. $left]) ?>px;
        <?php endif; ?>
        <?php if ( $b['breadcrumbs-padding']['padding-'. $right] ) : ?>
            padding-<?php echo $right; ?>: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-'. $right]) ?>px;
            margin-<?php echo $right; ?>: -<?php echo porto_config_value($b['breadcrumbs-padding']['padding-'. $right]) ?>px;
        <?php endif; ?>
        <?php if ( $b['breadcrumbs-bottom-border']['border-top'] ) : ?>
            margin-bottom: -<?php echo $b['breadcrumbs-bottom-border']['border-top']; ?>;
            bottom: -<?php echo porto_config_value( $b['breadcrumbs-bottom-border']['border-top'] ) + 1; ?>px;
        <?php endif; ?>
    }
}
<?php calcContainerWidth( '#breadcrumbs-boxed', ( $header_bg_empty && !$breadcrumb_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>

.owl-carousel .owl-nav [class*="owl-"],
.tparrows.tparrows-carousel.tp-leftarrow,
.tparrows.tparrows-carousel.tp-rightarrow,
.btn-primary,
.button,
input.submit {
    color: #fff;
    text-shadow: 0 -1px 0 rgba(0,0,0,.25);
    background-color: <?php echo $skinColor; ?>;
    border-color: <?php echo $skinColor; ?>;
}
.owl-carousel .owl-nav [class*="owl-"]:hover,
.owl-carousel .owl-nav [class*="owl-"]:active,
.owl-carousel .owl-nav [class*="owl-"]:focus,
.tparrows.tparrows-carousel.tp-leftarrow:hover,
.tparrows.tparrows-carousel.tp-rightarrow:hover,
.tparrows.tparrows-carousel.tp-leftarrow:active,
.tparrows.tparrows-carousel.tp-rightarrow:active,
.tparrows.tparrows-carousel.tp-leftarrow:focus,
.tparrows.tparrows-carousel.tp-rightarrow:focus {
    background-color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>;
    border-color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>;
}

.widget.follow-us .share-links a:not(:hover) {
    color: #525252;
    background-color: #fff;
}
#footer .follow-us .share-links a:not(:hover) { color: #525252; }
<?php if ( $b['social-color'] ) : ?>
    #main .share-links a {
        background-color: <?php echo $skinColor; ?> !important;
        color: <?php echo $b['skin-color-inverse']; ?> !important;
    }
    #main .share-links a:hover {
        background-color: <?php echo $portoColorLib->lighten( $skinColor, 5 ); ?> !important;
    }
<?php endif; ?>


/* button */
.btn-primary:hover,
.button:hover,
input.submit:hover,
.btn-primary:active,
.button:active,
input.submit:active,
.btn-primary:focus,
.button:focus,
input.submit:focus {
    border-color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>;
    background-color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>;
    color: <?php echo $b['skin-color-inverse']; ?>;
}
.btn-primary.disabled,
.button.disabled,
input.submit.disabled,
.btn-primary[disabled],
.button[disabled],
input.submit[disabled],
fieldset[disabled] .btn-primary,
fieldset[disabled] .button,
fieldset[disabled] input.submit,
.btn-primary.disabled:hover,
.button.disabled:hover,
input.submit.disabled:hover,
.btn-primary[disabled]:hover,
.button[disabled]:hover,
input.submit[disabled]:hover,
fieldset[disabled] .btn-primary:hover,
fieldset[disabled] .button:hover,
fieldset[disabled] input.submit:hover,
.btn-primary.disabled:focus,
.button.disabled:focus,
input.submit.disabled:focus,
.btn-primary[disabled]:focus,
.button[disabled]:focus,
input.submit[disabled]:focus,
fieldset[disabled] .btn-primary:focus,
fieldset[disabled] .button:focus,
fieldset[disabled] input.submit:focus,
.btn-primary.disabled.focus,
.button.disabled.focus,
input.submit.disabled.focus,
.btn-primary[disabled].focus,
.button[disabled].focus,
input.submit[disabled].focus,
fieldset[disabled] .btn-primary.focus,
fieldset[disabled] .button.focus,
fieldset[disabled] input.submit.focus,
.btn-primary.disabled:active,
.button.disabled:active,
input.submit.disabled:active,
.btn-primary[disabled]:active,
.button[disabled]:active,
input.submit[disabled]:active,
fieldset[disabled] .btn-primary:active,
fieldset[disabled] .button:active,
fieldset[disabled] input.submit:active,
.btn-primary.disabled.active,
.button.disabled.active,
input.submit.disabled.active,
.btn-primary[disabled].active,
.button[disabled].active,
input.submit[disabled].active,
fieldset[disabled] .btn-primary.active,
fieldset[disabled] .button.active,
fieldset[disabled] input.submit.active,
[type="submit"],
.geodir-search [type="button"],
.geodir-search [type="submit"],
#geodir-wrapper [type="button"],
#geodir-wrapper [type="submit"] {
    background-color: <?php echo $skinColor; ?>;
    border-color: <?php echo $skinColor; ?>;
}
[type="submit"]:hover,
.geodir-search [type="button"]:hover,
.geodir-search [type="submit"]:hover,
#geodir-wrapper [type="button"]:hover,
#geodir-wrapper [type="submit"]:hover,
[type="submit"]:active,
.geodir-search [type="button"]:active,
.geodir-search [type="submit"]:active,
#geodir-wrapper [type="button"]:active,
#geodir-wrapper [type="submit"]:active {
    border-color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>;
    background-color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>;
}
[type="submit"].disabled,
.geodir-search [type="button"].disabled,
.geodir-search [type="submit"].disabled,
#geodir-wrapper [type="button"].disabled,
#geodir-wrapper [type="submit"].disabled,
[type="submit"][disabled],
.geodir-search [type="button"][disabled],
.geodir-search [type="submit"][disabled],
#geodir-wrapper [type="button"][disabled],
#geodir-wrapper [type="submit"][disabled],
fieldset[disabled] [type="submit"],
fieldset[disabled] .geodir-search [type="button"],
fieldset[disabled] .geodir-search [type="submit"],
fieldset[disabled] #geodir-wrapper [type="button"],
fieldset[disabled] #geodir-wrapper [type="submit"],
[type="submit"].disabled:hover,
.geodir-search [type="button"].disabled:hover,
.geodir-search [type="submit"].disabled:hover,
#geodir-wrapper [type="button"].disabled:hover,
#geodir-wrapper [type="submit"].disabled:hover,
[type="submit"][disabled]:hover,
.geodir-search [type="button"][disabled]:hover,
.geodir-search [type="submit"][disabled]:hover,
#geodir-wrapper [type="button"][disabled]:hover,
#geodir-wrapper [type="submit"][disabled]:hover,
fieldset[disabled] [type="submit"]:hover,
fieldset[disabled] .geodir-search [type="button"]:hover,
fieldset[disabled] .geodir-search [type="submit"]:hover,
fieldset[disabled] #geodir-wrapper [type="button"]:hover,
fieldset[disabled] #geodir-wrapper [type="submit"]:hover,
[type="submit"].disabled:focus,
.geodir-search [type="button"].disabled:focus,
.geodir-search [type="submit"].disabled:focus,
#geodir-wrapper [type="button"].disabled:focus,
#geodir-wrapper [type="submit"].disabled:focus,
[type="submit"][disabled]:focus,
.geodir-search [type="button"][disabled]:focus,
.geodir-search [type="submit"][disabled]:focus,
#geodir-wrapper [type="button"][disabled]:focus,
#geodir-wrapper [type="submit"][disabled]:focus,
fieldset[disabled] [type="submit"]:focus,
fieldset[disabled] .geodir-search [type="button"]:focus,
fieldset[disabled] .geodir-search [type="submit"]:focus,
fieldset[disabled] #geodir-wrapper [type="button"]:focus,
fieldset[disabled] #geodir-wrapper [type="submit"]:focus,
[type="submit"].disabled:active,
.geodir-search [type="button"].disabled:active,
.geodir-search [type="submit"].disabled:active,
#geodir-wrapper [type="button"].disabled:active,
#geodir-wrapper [type="submit"].disabled:active,
[type="submit"][disabled]:active,
.geodir-search [type="button"][disabled]:active,
.geodir-search [type="submit"][disabled]:active,
#geodir-wrapper [type="button"][disabled]:active,
#geodir-wrapper [type="submit"][disabled]:active,
fieldset[disabled] [type="submit"]:active,
fieldset[disabled] .geodir-search [type="button"]:active,
fieldset[disabled] .geodir-search [type="submit"]:active,
fieldset[disabled] #geodir-wrapper [type="button"]:active,
fieldset[disabled] #geodir-wrapper [type="submit"]:active {
    background-color: <?php echo $skinColor; ?>;
    border-color: <?php echo $skinColor; ?>;
}
<?php foreach( $themeColors as $key => $themeColor ) : ?>
    html .btn-<?php echo $key; ?> {
        color: <?php echo $themeColorsInverse[$key]; ?>;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        background-color: <?php echo $themeColor; ?>;
        border-color: <?php echo $themeColor; ?> <?php echo $themeColor; ?> <?php echo $portoColorLib->darken( $themeColor, 10 ); ?>;
    }
    html .btn-<?php echo $key; ?>:hover,
    html .btn-<?php echo $key; ?>:focus,
    html .btn-<?php echo $key; ?>:active {
        color: <?php echo $themeColorsInverse[$key]; ?>;
        background-color: <?php echo $portoColorLib->darken( $themeColor, 5 ); ?>;
        border-color: <?php echo $themeColor; ?> <?php echo $themeColor; ?> <?php echo $portoColorLib->darken( $themeColor, 15 ); ?>;
    }
    html .btn-<?php echo $key; ?>-scale-2 {
        color: <?php echo $themeColorsInverse[$key]; ?>;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        background-color: <?php echo $portoColorLib->darken( $themeColor, 10 ); ?>;
        border-color: <?php echo $portoColorLib->darken( $themeColor, 10 ); ?> <?php echo $portoColorLib->darken( $themeColor, 10 ); ?> <?php echo $portoColorLib->darken( $themeColor, 20 ); ?>;
    }
    html .btn-<?php echo $key; ?>-scale-2:hover,
    html .btn-<?php echo $key; ?>-scale-2:active,
    html .btn-<?php echo $key; ?>-scale-2:focus {
        border-color: <?php echo $portoColorLib->darken( $themeColor, 15 ); ?>;
        background-color: <?php echo $portoColorLib->darken( $themeColor, 15 ); ?>;
    }

    html .btn-borders.btn-<?php echo $key; ?> {
        background: transparent;
        border-color: <?php echo $themeColor; ?>;
        color: <?php echo $themeColor; ?>;
        text-shadow: none;
    }
    html .btn-borders.btn-<?php echo $key; ?>:hover,
    html .btn-borders.btn-<?php echo $key; ?>:focus,
    html .btn-borders.btn-<?php echo $key; ?>:active {
        background-color: <?php echo $portoColorLib->darken( $themeColor, 5 ); ?>;
        border-color: <?php echo $themeColor; ?> !important;
        color: <?php echo $themeColorsInverse[$key]; ?>;
    }
<?php endforeach; ?>
<?php if ( 'btn-borders' == porto_get_button_style() ) : ?>
    .btn, .button, input.submit,
    [type="submit"].btn-primary,
    [type="submit"] {
        background: transparent;
        border-color: <?php echo $skinColor; ?>;
        color: <?php echo $skinColor; ?>;
        text-shadow: none;
        border-width: 3px;
        padding: 4px 10px;
    }
    .btn.btn-lg, .button.btn-lg, input.submit.btn-lg,
    [type="submit"].btn-primary.btn-lg,
    [type="submit"].btn-lg,
    .btn-group-lg > .btn, .btn-group-lg > .button, .btn-group-lg > input.submit,
    .btn-group-lg > [type="submit"].btn-primary,
    .btn-group-lg > [type="submit"] {
        padding: 8px 14px;
    }
    .btn.btn-sm, .button.btn-sm, input.submit.btn-sm,
    [type="submit"].btn-primary.btn-sm,
    [type="submit"].btn-sm,
    .btn-group-sm > .btn, .btn-group-sm > .button, .btn-group-sm > input.submit,
    .btn-group-sm > [type="submit"].btn-primary,
    .btn-group-sm > [type="submit"] {
        border-width: 2px;
        padding: 4px 10px;
    }
    .btn.btn-xs, .button.btn-xs, input.submit.btn-xs,
    [type="submit"].btn-primary.btn-xs,
    [type="submit"].btn-xs,
    .btn-group-xs > .btn, .btn-group-xs > .button, .btn-group-xs > input.submit,
    .btn-group-xs > [type="submit"].btn-primary,
    .btn-group-xs > [type="submit"] {
        padding: 1px 5px;
        border-width: 1px;
    }
    .btn:hover, .button:hover, input.submit:hover,
    [type="submit"].btn-primary:hover,
    [type="submit"]:hover,
    .btn:focus, .button:focus, input.submit:focus,
    [type="submit"].btn-primary:focus,
    [type="submit"]:focus,
    .btn:active, .button:active, input.submit:active,
    [type="submit"].btn-primary:active,
    [type="submit"]:active {
        background-color: <?php echo $portoColorLib->darken( $skinColor, 5 ); ?>;
        border-color: <?php echo $skinColor; ?> !important;
        color: <?php echo $b['skin-color-inverse']; ?>;
    }

    .btn-default,
    [type="submit"].btn-default {
        border-color: #ccc;
        color: #333;
    }
    .btn-default:hover,
    [type="submit"].btn-default:hover,
    .btn-default:focus,
    [type="submit"].btn-default:focus,
    .btn-default:active,
    [type="submit"].btn-default:active {
        background-color: #e6e6e6;
        border-color: #adadad !important;
        color: #333;
    }

    body .cart-actions .button,
    body .checkout-button,
    body #place_order {
        padding: 8px 14px;
    }
    <?php foreach( $themeColors as $key => $themeColor ) : ?>
        .btn-<?php echo $key; ?>,
        [type="submit"].btn-<?php echo $key; ?> {
            background: transparent;
            border-color: <?php echo $themeColor; ?>;
            color: <?php echo $themeColor; ?>;
            text-shadow: none;
            border-width: 3px;
        }
        .btn-<?php echo $key; ?>:hover,
        [type="submit"].btn-<?php echo $key; ?>:hover,
        .btn-<?php echo $key; ?>:focus,
        [type="submit"].btn-<?php echo $key; ?>:focus,
        .btn-<?php echo $key; ?>:active,
        [type="submit"].btn-<?php echo $key; ?>:active {
            background-color: <?php echo $portoColorLib->darken( $themeColor, 5 ); ?>;
            border-color: <?php echo $themeColor; ?> !important;
            color: <?php echo $themeColorsInverse[$key]; ?>;
        }

        html .tabs-<?php echo $key; ?> .nav-tabs li .nav-link,
        html .tabs-<?php echo $key; ?> .nav-tabs.nav-justified li .nav-link,
        html .tabs-<?php echo $key; ?> .nav-tabs li .nav-link:hover,
        html .tabs-<?php echo $key; ?> .nav-tabs.nav-justified li .nav-link:hover {
            color: <?php echo $themeColor; ?>;
        }
        html .tabs-<?php echo $key; ?> .nav-tabs li .nav-link:hover,
        html .tabs-<?php echo $key; ?> .nav-tabs.nav-justified li .nav-link:hover {
            border-top-color: <?php echo $themeColor; ?>;
        }
        html .tabs-<?php echo $key; ?> .nav-tabs li.active .nav-link,
        html .tabs-<?php echo $key; ?> .nav-tabs.nav-justified li.active .nav-link,
        html .tabs-<?php echo $key; ?> .nav-tabs li.active .nav-link:hover,
        html .tabs-<?php echo $key; ?> .nav-tabs.nav-justified li.active .nav-link:hover,
        html .tabs-<?php echo $key; ?> .nav-tabs li.active .nav-link:focus,
        html .tabs-<?php echo $key; ?> .nav-tabs.nav-justified li.active .nav-link:focus {
            border-top-color: <?php echo $themeColor; ?>;
            color: <?php echo $themeColor; ?>;
        }
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs li .nav-link:hover,
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs.nav-justified li .nav-link:hover,
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs li.active .nav-link,
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs.nav-justified li.active .nav-link,
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs li.active .nav-link:hover,
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs.nav-justified li.active .nav-link:hover,
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs li.active .nav-link:focus,
        html .tabs-<?php echo $key; ?>.tabs-bottom .nav-tabs.nav-justified li.active .nav-link:focus {
            border-bottom-color: <?php echo $themeColor; ?>;
        }
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-left li .nav-link:hover,
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-left li.active .nav-link,
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-left li.active .nav-link:hover,
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-left li.active .nav-link:focus {
            border-left-color: <?php echo $themeColor; ?>;
        }
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-right li .nav-link:hover ,
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-right li.active .nav-link,
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-right li.active .nav-link:hover,
        html .tabs-<?php echo $key; ?>.tabs-vertical.tabs-right li.active .nav-link:focus {
            border-right-color: <?php echo $themeColor; ?>;
        }

        html .stats-block.counter-<?php echo $key; ?> .stats-number,
        html .stats-block.counter-<?php echo $key; ?> div.counter_prefix,
        html .stats-block.counter-<?php echo $key; ?> div.counter_suffix {
            color: <?php echo $themeColor; ?>;
        }

        html .testimonial-<?php echo $key; ?> blockquote {
          background: <?php echo $portoColorLib->lighten( $themeColor, 5 ); ?>;
        }
        html .testimonial-<?php echo $key; ?> .testimonial-arrow-down {
          border-top-color: <?php echo $portoColorLib->lighten( $themeColor, 5 ); ?>;
        }
    <?php endforeach; ?>
<?php endif; ?>

<?php if ( $b['tertiary-color'] ) : ?>
    .portfolio-item:hover .thumb-info-icons .thumb-info-icon {
        background-color: <?php echo $b['tertiary-color']; ?> !important;
    }
<?php endif; ?>

.widget_sidebar_menu .widget-title,
.sidebar-menu > li.menu-item > a,
.sidebar-menu .menu-custom-block a {
    color: #465157;
}

.sidebar-menu > li.menu-item > .arrow:before {
  color: #838b90;
}

.mega-menu > li.menu-item > a,
.mega-menu .wide .popup li.sub > a,
.header-side .sidebar-menu > li.menu-item > a,
.sidebar-menu .wide .popup li.sub > a,
.porto-view-switcher .narrow li.menu-item > a {
    text-transform: <?php echo $b['menu-text-transform']; ?>;
}

.popup .sub-menu {
    text-transform: <?php echo $b['menu-popup-text-transform']; ?>;
}

<?php if ( $b['mainmenu-tip-bg-color'] ) : ?>
    .mega-menu .tip,
    .sidebar-menu .tip,
    .accordion-menu .tip,
    .menu-custom-block .tip {
        background: <?php echo $b['mainmenu-tip-bg-color']; ?>;
    }
    .mega-menu .tip .tip-arrow,
    .sidebar-menu .tip .tip-arrow,
    .accordion-menu .tip .tip-arrow,
    .menu-custom-block .tip .tip-arrow {
        color: <?php echo $b['mainmenu-tip-bg-color']; ?>;
    }
<?php endif; ?>

section.timeline .timeline-box.left:before,
section.timeline .timeline-box.right:before {
    background: <?php echo $skinColor; ?>;
    box-shadow: 0 0 0 3px #ffffff, 0 0 0 6px <?php echo $skinColor; ?>;
}


article.post .post-date .sticky,
.post-item .post-date .sticky {
    <?php if ( $b['hot-color'] ) : ?>
        background: <?php echo $b['hot-color']; ?>;
    <?php endif; ?>
    <?php if ( $b['hot-color-inverse'] ) : ?>
        color: <?php echo $b['hot-color-inverse']; ?>;
    <?php endif; ?>
}

/* footer */
<?php if ( $b['footer-text-color'] ) : ?>
    #footer,
    #footer p,
    #footer .widget > div > ul li,
    #footer .widget > ul li  {
        color: <?php echo $b['footer-text-color']; ?>;
    }
    <?php if ( 'transparent' != $b['footer-text-color'] ) : ?>
        #footer .widget .tagcloud a,
        #footer .widget > div > ul,
        #footer .widget > ul,
        #footer .widget > div > ul li,
        #footer .widget > ul li,
        #footer .post-item-small {
            border-color: rgba(<?php echo $portoColorLib->hexToRGB( $b['footer-text-color'] ); ?>, 0.3);
        }
    <?php endif; ?>
<?php endif; ?>
<?php if ( $b['footer-link-color']['regular'] ) : ?>
    #footer a,
    #footer .tooltip-icon {
        color: <?php echo $b['footer-link-color']['regular']; ?>;
    }
    #footer .tooltip-icon {
        border-color: <?php echo $b['footer-link-color']['regular']; ?>;
    }
<?php endif; ?>
<?php if ( $b['footer-link-color']['hover'] ) : ?>
    #footer a:hover {
        color: <?php echo $b['footer-link-color']['hover']; ?>;
    }
<?php endif; ?>
<?php if ( $b['footer-heading-color'] ) : ?>
    #footer h1,
    #footer h2,
    #footer h3,
    #footer h4,
    #footer h5,
    #footer h6,
    #footer .widget-title,
    #footer .widgettitle,
    #footer h1 a,
    #footer h2 a,
    #footer h3 a,
    #footer h4 a,
    #footer h5 a,
    #footer h6 a,
    #footer .widget-title a,
    #footer .widgettitle a {
        color: <?php echo $b['footer-heading-color']; ?>;
    }
<?php endif; ?>
#footer .widget-title,
#footer .widget-title a,
#footer .widgettitle,
#footer .widgettitle a {
    font-weight: 200;
}
<?php if ( $b['footer-ribbon-bg-color'] ) : ?>
    #footer .footer-ribbon {
        background-color: <?php echo $b['footer-ribbon-bg-color']; ?>;
    }
    #footer .footer-ribbon:before {
        border-<?php echo $right; ?>-color: <?php echo $portoColorLib->darken( $b['footer-ribbon-bg-color'], 15 ); ?>;
    }
<?php endif; ?>
<?php if ( $b['footer-ribbon-text-color'] ) : ?>
    #footer .footer-ribbon,
    #footer .footer-ribbon a,
    #footer .footer-ribbon a:hover,
    #footer .footer-ribbon a:focus {
        color: <?php echo $b['footer-ribbon-text-color']; ?>;
    }
<?php endif; ?>
<?php  if ( $b['footer-bottom-link-color']['regular'] ) : ?>
    #footer .footer-bottom a {
        color: <?php echo $b['footer-bottom-link-color']['regular']; ?>;
    }
<?php endif; ?>
<?php  if ( $b['footer-bottom-link-color']['hover'] ) : ?>
    #footer .footer-bottom a:hover {
        color: <?php echo $b['footer-bottom-link-color']['hover']; ?>;
    }
<?php endif; ?>
<?php if ( 'transparent' ==  $b['footer-social-bg-color' ] ) : ?>
    #footer .follow-us .share-links a,
    .footer-top .follow-us .share-links a {
        background: none;
        <?php if ( $b['footer-social-link-color'] ) : ?>
            color: <?php echo $b['footer-social-link-color']; ?>;
        <?php endif; ?>
    }
<?php else: ?>
    #footer .follow-us .share-links a:not(:hover),
    .footer-top .follow-us .share-links a:not(:hover) {
        <?php if ( $b['footer-social-bg-color'] ) : ?>
            background: <?php echo $b['footer-social-bg-color']; ?>;
        <?php endif; ?>
        <?php if ( $b['footer-social-link-color'] ) : ?>
            color: <?php echo $b['footer-social-link-color']; ?>;
        <?php endif; ?>
    }
<?php endif; ?>
<?php calcContainerWidth( '#footer-boxed', ( $header_bg_empty && !$footer_bg_empty ), $b['container-width'], $b['grid-gutter-width'] ); ?>

/*------------------ Fonts ---------------------- */
<?php if ( $b['alt-font']['font-family'] ) : ?>
    .porto-concept strong,
    .home-intro p em,
    .alternative-font,
    .thumb-info-ribbon span,
    .stats-block.counter-alternative .stats-number,
    .vc_custom_heading em,
    #footer .footer-ribbon { font-family: <?php echo $b['alt-font']['font-family']; ?>; }
<?php endif; ?>
<?php if ( $b['alt-font']['font-weight'] ) : ?>
    .alternative-font,
    #footer .footer-ribbon { font-weight: <?php echo $b['alt-font']['font-weight']; ?>; }
<?php endif; ?>
.pricing-table.pricing-table-flat .plan h3 span,
.testimonial.testimonial-style-3 blockquote p,
.testimonial.testimonial-style-4 blockquote p,
.testimonial.testimonial-style-5 blockquote p,
.searchform .live-search-list .autocomplete-suggestion { font-family: <?php echo $b['body-font']['font-family'] ?>; }
<?php if ( class_exists( 'Woocommerce' ) && isset( $b['add-to-cart-font'] ) && !empty( $b['add-to-cart-font']['font-family'] ) ) : ?>
    #mini-cart .buttons a,
    .quantity .qty,
    .single_add_to_cart_button,
    .shop_table.wishlist_table .add_to_cart.button,
    .woocommerce table.wishlist_table .add_to_cart.button,
    ul.products li.product-col .add_to_cart_button,
    ul.products li.product-col .add_to_cart_read_more,
    ul.products li.product-col.show-outimage-q-onimage-alt .price { font-family: <?php echo $b['add-to-cart-font']['font-family']; ?>; }
<?php endif; ?>

.owl-carousel.dots-color-primary .owl-dots .owl-dot span { background-color: #43a6a3; }
.master-slider { direction: ltr; }

/*------------------ bbPress ---------------------- */
<?php if ( (class_exists('bbPress') && is_bbpress() ) || ( class_exists('BuddyPress') && is_buddypress() ) ) : ?>
    #bbpress-forums .bbp-topic-pagination a:hover,
    #bbpress-forums .bbp-topic-pagination a:focus,
    #bbpress-forums .bbp-topic-pagination span.current,
    #bbpress-forums .bbp-pagination a:hover,
    #bbpress-forums .bbp-pagination a:focus,
    #bbpress-forums .bbp-pagination span.current,
    #buddypress button,
    #buddypress a.button,
    #buddypress input[type="submit"],
    #buddypress input[type="button"],
    #buddypress input[type="reset"],
    #buddypress ul.button-nav li a,
    #buddypress div.generic-button a,
    #buddypress .comment-reply-link,
    a.bp-title-button {
        background-color: <?php echo $skinColor; ?>;
        border-color: <?php echo $skinColor; ?>;
    }

    #buddypress div.item-list-tabs ul li.selected a,
    #buddypress div.item-list-tabs ul li.current a {
        background-color: <?php echo $skinColor; ?>;
        color: <?php echo $b['skin-color-inverse'] ?>;
    }

    #buddypress button:hover,
    #buddypress a.button:hover,
    #buddypress input[type="submit"]:hover,
    #buddypress input[type="button"]:hover,
    #buddypress input[type="reset"]:hover,
    #buddypress ul.button-nav li a:hover,
    #buddypress div.generic-button a:hover,
    #buddypress .comment-reply-link:hover,
    a.bp-title-button:hover,
    #buddypress button:focus,
    #buddypress a.button:focus,
    #buddypress input[type="submit"]:focus,
    #buddypress input[type="button"]:focus,
    #buddypress input[type="reset"]:focus,
    #buddypress ul.button-nav li a:focus,
    #buddypress div.generic-button a:focus,
    #buddypress .comment-reply-link:focus,
    a.bp-title-button:focus,
    #buddypress .comment-reply-link:hover,
    #buddypress a.button:focus,
    #buddypress a.button:hover,
    #buddypress button:hover,
    #buddypress div.generic-button a:hover
    #buddypress input[type="button"]:hover,
    #buddypress input[type="reset"]:hover,
    #buddypress input[type="submit"]:hover,
    #buddypress ul.button-nav li a:hover,
    #buddypress ul.button-nav li.current a,
    #buddypress input.pending[type="submit"],
    #buddypress input.pending[type="button"],
    #buddypress input.pending[type="reset"],
    #buddypress input.disabled[type="submit"],
    #buddypress input.disabled[type="button"],
    #buddypress input.disabled[type="reset"],
    #buddypress input[type="submit"][disabled="disabled"],
    #buddypress button.pending,
    #buddypress button.disabled,
    #buddypress div.pending a,
    #buddypress a.disabled {
        background: <?php echo $portoColorLib->darken($skinColor, 5); ?>;
        border-color: <?php echo $portoColorLib->darken($skinColor, 5); ?>;
    }

    @keyframes loader-pulsate {
        from {
            background: <?php echo $skinColor; ?>;
            border-color: <?php echo $skinColor; ?>;
            box-shadow: none;
        }
        to {
            background: <?php echo $portoColorLib->darken($skinColor, 5); ?>;
            border-color: <?php echo $portoColorLib->darken($skinColor, 5); ?>;
            box-shadow: none;
        }
    }
    #buddypress div.pagination .pagination-links a:hover,
    #buddypress div.pagination .pagination-links a:focus,
    #buddypress div.pagination .pagination-links a:active,
    #buddypress div.pagination .pagination-links span.current,
    #buddypress div.pagination .pagination-links span.current:hover {
        background: <?php echo $skinColor; ?>;
        border-color: <?php echo $skinColor; ?>;
    }
<?php endif; ?>

/*------------------ Theme Shop ---------------------- */
<?php if ( class_exists( 'Woocommerce' ) ) : ?>
    .product-layout-grid .product-images .img-thumbnail { margin-bottom: <?php echo $b['grid-gutter-width']; ?>px; }

    ul.products li.product-col .product-loop-title,
    ul.products li.product-col h3,
    ul.products li.product-col.show-outimage-q-onimage .product-loop-title:hover,
    ul.products li.product-col.show-outimage-q-onimage .product-loop-title:focus,
    ul.products li.product-col.show-outimage-q-onimage .product-loop-title:active,
    ul.products li.product-col.show-outimage-q-onimage .product-loop-title:hover > *,
    ul.products li.product-col.show-outimage-q-onimage .product-loop-title:focus > *,
    ul.products li.product-col.show-outimage-q-onimage .product-loop-title:active > *,
    #yith-wcwl-popup-message,
    .widget_product_categories ul li > a,
    .widget_price_filter ul li > a,
    .widget_layered_nav ul li > a,
    .widget_layered_nav_filters ul li > a,
    .widget_rating_filter ul li > a,
    .widget_price_filter ol li > a,
    .widget_layered_nav_filters ol li > a,
    .widget_rating_filter ol li > a,
    .woocommerce .widget_layered_nav ul.yith-wcan-label li a,
    .woocommerce-page .widget_layered_nav ul.yith-wcan-label li a,
    ul.product_list_widget li .product-details a,
    .widget ul.product_list_widget li .product-details a,
    .widget_recent_reviews .product_list_widget li a,
    .widget.widget_recent_reviews .product_list_widget li a,
    .widget_shopping_cart .product-details .remove-product,
    .shop_table dl.variation,
    .select2-container .select2-choice,
    .select2-drop,
    .select2-drop-active,
    .form-row input[type="email"],
    .form-row input[type="number"],
    .form-row input[type="password"],
    .form-row input[type="search"],
    .form-row input[type="tel"],
    .form-row input[type="text"],
    .form-row input[type="url"],
    .form-row input[type="color"],
    .form-row input[type="date"],
    .form-row input[type="datetime"],
    .form-row input[type="datetime-local"],
    .form-row input[type="month"],
    .form-row input[type="time"],
    .form-row input[type="week"],
    .form-row select,
    .form-row textarea { color: <?php echo $bodyColor; ?>; }

    .quantity .qty,
    .quantity .minus:hover,
    .quantity .plus:hover,
    .stock,
    .product-image .viewcart,
    .widget_product_categories ul li > a:hover,
    .widget_price_filter ul li > a:hover,
    .widget_layered_nav ul li > a:hover,
    .widget_layered_nav_filters ul li > a:hover,
    .widget_rating_filter ul li > a:hover,
    .widget_price_filter ol li > a:hover,
    .widget_layered_nav_filters ol li > a:hover,
    .widget_rating_filter ol li > a:hover,
    .widget_product_categories ul li > a:focus,
    .widget_price_filter ul li > a:focus,
    .widget_layered_nav ul li > a:focus,
    .widget_layered_nav_filters ul li > a:focus,
    .widget_rating_filter ul li > a:focus,
    .widget_price_filter ol li > a:focus,
    .widget_layered_nav_filters ol li > a:focus,
    .widget_rating_filter ol li > a:focus,
    .widget_product_categories ul li .toggle,
    .widget_price_filter ul li .toggle,
    .widget_layered_nav ul li .toggle,
    .widget_layered_nav_filters ul li .toggle,
    .widget_rating_filter ul li .toggle,
    .widget_price_filter ol li .toggle,
    .widget_layered_nav_filters ol li .toggle,
    .widget_rating_filter ol li .toggle,
    .widget_product_categories ul li.current > a,
    .widget_price_filter ul li.current > a,
    .widget_layered_nav ul li.current > a,
    .widget_layered_nav_filters ul li.current > a,
    .widget_rating_filter ul li.current > a,
    .widget_price_filter ol li.current > a,
    .widget_layered_nav_filters ol li.current > a,
    .widget_rating_filter ol li.current > a,
    .widget_product_categories ul li.chosen > a,
    .widget_price_filter ul li.chosen > a,
    .widget_layered_nav ul:not(.yith-wcan) li.chosen > a,
    .widget_layered_nav_filters ul li.chosen > a,
    .widget_rating_filter ul li.chosen > a,
    .widget_price_filter ol li.chosen > a,
    .widget_layered_nav_filters ol li.chosen > a,
    .widget_rating_filter ol li.chosen > a,
    .widget_layered_nav_filters ul li a:before,
    .widget_layered_nav .yith-wcan-select-wrapper ul.yith-wcan-select.yith-wcan li:hover a,
    .widget_layered_nav .yith-wcan-select-wrapper ul.yith-wcan-select.yith-wcan li.chosen a,
    ul.cart_list li .product-details a:hover,
    ul.product_list_widget li .product-details a:hover,
    ul.cart_list li a:hover,
    ul.product_list_widget li a:hover,
    .widget_shopping_cart .total .amount,
    .shipping_calculator h2,
    .cart_totals h2,
    .review-order.shop_table h2,
    .shipping_calculator h2 a,
    .cart_totals h2 a,
    .review-order.shop_table h2 a,
    .shop_table td.product-name,
    .product-subtotal .woocommerce-Price-amount { color: <?php echo $skinColor; ?>; }

    ul.products li.product .links-on-image .add-links .add_to_cart_button:hover,
    ul.products li.product .links-on-image .add-links .add_to_cart_read_more:hover,
    .product-image .viewcart:hover,
    .widget_price_filter .ui-slider .ui-slider-handle { background-color: <?php echo $skinColor; ?>; }

    .sidebar #yith-ajaxsearchform .btn { background: <?php echo $skinColor; ?>; }

    #yith-wcwl-popup-message,
    .woocommerce-cart .cart-form form,
    .product-layout-full_width .product-thumbnails-inner .img-thumbnail.selected,
    .product-layout-centered_vertical_zoom .product-thumbnails-inner .img-thumbnail.selected { border-color: <?php echo $skinColor; ?>; }

    .loader-container i.porto-ajax-loader { border-top-color: <?php echo $skinColor; ?>; }

    .yith-wcwl-add-to-wishlist a,
    .yith-wcwl-add-to-wishlist span { border-color: <?php echo $b['wishlist-border-color']; ?>; color: <?php echo $b['wishlist-color'] ?>; }
    .show-outimage-q-onimage .yith-wcwl-add-to-wishlist a,
    .show-outimage-q-onimage .yith-wcwl-add-to-wishlist span,
    .show-outimage-q-onimage-alt .yith-wcwl-add-to-wishlist a,
    .show-outimage-q-onimage-alt .yith-wcwl-add-to-wishlist span,
    .show-outimage-q-onimage .yith-wcwl-add-to-wishlist a:hover,
    .show-outimage-q-onimage .yith-wcwl-add-to-wishlist span:hover,
    .show-outimage-q-onimage-alt .yith-wcwl-add-to-wishlist a:hover,
    .show-outimage-q-onimage-alt .yith-wcwl-add-to-wishlist span:hover,
    .show-outimage-q-onimage .yith-wcwl-add-to-wishlist a:hover:before,
    .show-outimage-q-onimage .yith-wcwl-add-to-wishlist span:hover:before,
    .show-outimage-q-onimage-alt .yith-wcwl-add-to-wishlist a:hover:before,
    .show-outimage-q-onimage-alt .yith-wcwl-add-to-wishlist span:hover:before { color: <?php echo $b['wishlist-color'] ?>; }
    /*.yith-wcwl-add-to-wishlist span.ajax-loading { border-color: <?php echo $b['wishlist-color'] ?>; }*/
    .yith-wcwl-add-to-wishlist span.ajax-loading:hover { background-color: <?php echo $b['wishlist-color'] ?>; }
    .add-links .quickview { border-color: <?php echo $b['quickview-border-color'] ?>; color: <?php echo $b['quickview-color'] ?>; }
    .summary-before .ms-lightbox-btn,
    .product-images .zoom { background-color: <?php echo $skinColor; ?>; }
    .summary-before .ms-lightbox-btn:hover { background-color: <?php echo $portoColorLib->lighten($skinColor, 5); ?>; }
    .summary-before .ms-nav-next:before,
    .summary-before .ms-nav-prev:before,
    .summary-before .ms-thumblist-fwd:before,
    .summary-before .ms-thumblist-bwd:before,
    .product-summary-wrap .price { color: <?php echo $skinColor; ?>; }
    .product-summary-wrap .yith-wcwl-add-to-wishlist a,
    .product-summary-wrap .yith-wcwl-add-to-wishlist span { color: inherit; }
    .product-summary-wrap .yith-wcwl-add-to-wishlist a:before,
    .product-summary-wrap .yith-wcwl-add-to-wishlist span:before { border-color: <?php echo $b['wishlist-color'] ?>; color: <?php echo $b['wishlist-color'] ?>; }
    .product-summary-wrap .yith-wcwl-add-to-wishlist a:hover,
    .product-summary-wrap .yith-wcwl-add-to-wishlist span:hover,
    .product-summary-wrap .yith-wcwl-add-to-wishlist a:focus,
    .product-summary-wrap .yith-wcwl-add-to-wishlist span:focus { background: transparent; color: <?php echo $b['wishlist-color'] ?>; }
    .product-summary-wrap .yith-wcwl-add-to-wishlist a:hover:before,
    .product-summary-wrap .yith-wcwl-add-to-wishlist span:hover:before,
    .product-summary-wrap .yith-wcwl-add-to-wishlist a:focus:before,
    .product-summary-wrap .yith-wcwl-add-to-wishlist span:focus:before { color: <?php echo $b['wishlist-color-inverse']; ?>; /*background-color: <?php echo $b['wishlist-color'] ?>;*/ }

    .woocommerce-pagination a:hover,
    .woocommerce-pagination a:focus,
    .woocommerce-pagination span.current { border-color: <?php echo $skinColor; ?>; background-color: transparent; }

    ul.products li.product-col .product-loop-title:hover,
    ul.products li.product-col .product-loop-title:focus,
    ul.products li.product-col .product-loop-title:hover h3,
    ul.products li.product-col .product-loop-title:focus h3 { color: <?php echo $skinColor; ?>; }
    ul.products li.product-col .links-on-image .add-links-wrap .add-links .quickview { background-color: <?php echo $b['quickview-color'] ?>; color: <?php echo $b['quickview-color-inverse'] ?>; }
    ul.products li.product-col .links-on-image .add-links-wrap .add-links .quickview:hover { background-color: <?php echo $portoColorLib->lighten($b['quickview-color'], 5); ?>; }
    ul.products li.product-col.show-outimage-q-onimage .links-on-image .add-links-wrap .add-links .quickview { background-color: rgba(0, 0, 0, 0.6); }

    ul.products.list li.product-col.show-outimage-q-onimage .add_to_cart_button,
    ul.products.list li.product-col.show-outimage-q-onimage .add_to_cart_read_more,
    ul.products.list li.product-col.show-outimage-q-onimage-alt .add_to_cart_button,
    ul.products.list li.product-col.show-outimage-q-onimage-alt .add_to_cart_read_more,
    .woocommerce .widget_layered_nav ul.yith-wcan-label li a:hover,
    .woocommerce-page .widget_layered_nav ul.yith-wcan-label li a:hover,
    .woocommerce .widget_layered_nav ul.yith-wcan-label li.chosen a,
    .woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a,
    .filter-item-list .filter-item:not(.disabled):hover { background-color: <?php echo $skinColor; ?>; border-color: <?php echo $skinColor; ?>; }
    ul.products.list li.product-col.show-outimage-q-onimage .add-links-wrap .add-links .quickview:hover,
    ul.products.list li.product-col.show-outimage-q-onimage-alt .add-links-wrap .add-links .quickview:hover { color: <?php echo $b['quickview-color'] ?>; }

    .product_title a:hover,
    .product_title a:focus { color: <?php echo $skinColor; ?>; }
    .add_to_cart_button:hover,
    .add_to_cart_read_more:hover,
    .add_to_cart_button:focus,
    .add_to_cart_read_more:focus,
    .add-links .add_to_cart_button:hover,
    .add-links .add_to_cart_read_more:hover,
    .add-links .add_to_cart_button:focus,
    .add-links .add_to_cart_read_more:focus,
    ul.products li.product:hover .add_to_cart_button,
    ul.products li.product:hover .add_to_cart_read_more,
    ul.list li.product .add_to_cart_button,
    ul.list li.product .add_to_cart_read_more { background-color: <?php echo $skinColor; ?>; border-color: <?php echo $skinColor; ?>; color: #fff; }
    ul.products li.product .links-on-image .add-links .add_to_cart_button,
    ul.products li.product .links-on-image .add-links .add_to_cart_read_more { border-color: <?php echo $skinColor; ?>; color: <?php echo $skinColor; ?>; }

    .widget_product_categories ul li .toggle:hover,
    .widget_price_filter ul li .toggle:hover,
    .widget_layered_nav ul li .toggle:hover,
    .widget_layered_nav_filters ul li .toggle:hover,
    .widget_rating_filter ul li .toggle:hover,
    .widget_price_filter ol li .toggle:hover,
    .widget_layered_nav_filters ol li .toggle:hover,
    .widget_rating_filter ol li .toggle:hover { color: <?php echo $portoColorLib->lighten($skinColor, 5); ?>; }
    .widget_layered_nav ul li .count,
    .widget_product_categories ul li .count,
    .widget_rating_filter .wc-layered-nav-rating a { color: <?php echo if_light($portoColorLib->lighten($bodyColor, 5), $portoColorLib->darken($bodyColor, 5)); ?>; }
    .widget_layered_nav_filters ul li a:hover:before { color: <?php echo $portoColorLib->lighten($skinColor, 5); ?>; }
    .woocommerce .widget_layered_nav ul.yith-wcan-label li.chosen a:hover,
    .woocommerce-page .widget_layered_nav ul.yith-wcan-label li.chosen a:hover { background-color: <?php echo $portoColorLib->lighten($skinColor, 5); ?>; border-color: <?php echo $portoColorLib->lighten($skinColor, 5); ?>; }
    .woocommerce #content table.shop_table.wishlist_table.cart a.remove { color: <?php echo $skinColor; ?>; }
    .woocommerce #content table.shop_table.wishlist_table.cart a.remove:hover { color: <?php echo $portoColorLib->lighten($skinColor, 5); ?>; }
    .woocommerce #content table.shop_table.wishlist_table.cart a.remove:active { color: <?php echo $portoColorLib->darken($skinColor, 5); ?>; }

    .product-image .labels .onhot,
    .summary-before .labels .onhot { background: <?php echo $b['hot-color'] ?>; color: <?php echo $b['hot-color-inverse'] ?>; }
    .product-image .labels .onsale,
    .summary-before .labels .onsale { background: <?php echo $b['sale-color'] ?>; color: <?php echo $b['sale-color-inverse'] ?>; }
    .success-message-container { border-top: 4px solid <?php echo $skinColor; ?>; }

    .woocommerce-tabs .resp-tabs-list li.resp-tab-active,
    .woocommerce-tabs .resp-tabs-list li:hover { border-color: <?php echo $skinColor; ?> !important; }
    .woocommerce-tabs h2.resp-tab-active { border-bottom-color: <?php echo $skinColor; ?> !important; }
    .resp-vtabs.style-2 .resp-tabs-list li.resp-tab-active { border-bottom: 2px solid <?php echo $skinColor; ?> !important; }
    .product-categories li a:hover { color: #7b858a !important; text-decoration: underline; }
    .featured-box.porto-user-box { border-top-color: <?php echo $skinColor; ?>; }
    .woocommerce-widget-layered-nav-list .chosen a:not(.filter-color),
    .filter-item-list .active .filter-item { background-color: <?php echo $skinColor; ?>; color: #fff; border-color: <?php echo $skinColor; ?>; }
    .porto-related-products { background: #f4f4f4; }


    /* WC Marketplace Compatibility */
    <?php if ( class_exists( 'WC_Dependencies_Product_Vendor' ) ) : ?>
        .wcmp-wrapper .nav { display: block; }
        .wcmp-wrapper .top-user-nav>li a.dropdown-toggle:after { display: none; }
        .wcmp-wrapper select,
        .wcmp-wrapper select.form-control { background-size: auto; }
        .wcmp-wrapper .row-actions span.divider { margin: 0; background-image: none; }
        .wcmp-wrapper .content-padding:not(.dashboard) .dataTables_wrapper.dt-bootstrap .dataTables_paginate li { text-indent: 0; width: auto; }
        .wcmp-wrapper .input-group-addon { width: auto; line-height: inherit; }
    <?php endif; ?>


    <?php if ( is_singular( 'product' ) ) : ?>
        .sidebar-content { padding-bottom: 15px; }
    <?php endif; ?>

    /* daily sale */
    .sale-product-daily-deal .daily-deal-title,
    .sale-product-daily-deal .porto_countdown { font-family: 'Oswald', <?php echo $b['h2-font']['font-family']; ?>; text-transform: uppercase; }
    .entry-summary .sale-product-daily-deal { margin-top: 10px; }
    .entry-summary .sale-product-daily-deal .porto_countdown { margin-bottom: 5px; }
    .entry-summary .sale-product-daily-deal .porto_countdown-section { background-color: <?php echo $b['skin-color']; ?>; color: #fff; margin-left: 1px; margin-right: 1px; display: block; float: <?php echo $left; ?>; max-width: calc(25% - 2px); min-width: 64px; padding: 12px 10px; }
    .entry-summary .sale-product-daily-deal .porto_countdown .porto_countdown-amount { display: block; font-size: 18px; font-weight: 700; }
    .entry-summary .sale-product-daily-deal .porto_countdown-period { font-size: 10px; }
    .entry-summary .sale-product-daily-deal:after { content: ''; display: table; clear: both; }
    .entry-summary .sale-product-daily-deal .daily-deal-title { text-transform: uppercase; }

    .products .sale-product-daily-deal { position: absolute; left: 10px; right: 10px; bottom: 10px; color: #fff; padding: 5px 0; text-align: center; }
    .products .sale-product-daily-deal:before { content: ''; position: absolute; left: 0; width: 100%; top: 0; height: 100%; background: <?php echo $b['skin-color']; ?>; opacity: 0.7; }
    .products .sale-product-daily-deal > h5,
    .products .sale-product-daily-deal > div { position: relative; z-index: 1; }
    .products .sale-product-daily-deal .daily-deal-title { display: inline-block; color: #fff; font-size: 11px; font-weight: 400; margin-bottom: 0; margin-<?php  echo $right; ?>: 1px; }
    .products .sale-product-daily-deal .porto_countdown { float: none; display: inline-block; text-transform: uppercase; margin-bottom: 0; width: auto; }
    .products .sale-product-daily-deal .porto_countdown-section { padding: 0; margin-bottom: 0; }
    .products .sale-product-daily-deal .porto_countdown-section:first-child:after { content: ','; margin-<?php echo $right; ?>: 2px; }
    .products .sale-product-daily-deal .porto_countdown-amount,
    .products .sale-product-daily-deal .porto_countdown-period { font-size: 13px; font-weight: 500; padding: 0 1px; }
    .products .sale-product-daily-deal .porto_countdown-section:last-child .porto_countdown-period { padding: 0; }
    .products .sale-product-daily-deal:after { content: ''; display: table; clear: both; }

<?php endif; ?>
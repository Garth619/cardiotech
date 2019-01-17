<?php
    global $porto_settings_optimize;
    $unusedShortcodeList = !isset( $porto_settings_optimize['shortcodes_to_remove'] ) || !$porto_settings_optimize['shortcodes_to_remove'] ? array() : $porto_settings_optimize['shortcodes_to_remove'];
?>
@import "theme/shortcodes/common";
<?php if ( !in_array( 'vc_tabs', $unusedShortcodeList ) || !in_array( 'vc_tour', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/tabs";
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) || !in_array( 'vc_accordion', $unusedShortcodeList ) || !in_array( 'vc_accordion_tab', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/accordion";
<?php endif; ?>

@import "theme/shortcodes/toggles";

<?php if ( !in_array( 'porto_grid_container', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/grid_container";
<?php endif; ?>
<?php if ( !in_array( 'porto_carousel_logo', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/carousel_logo";
<?php endif; ?>

@import "theme/shortcodes/testimonials";

<?php if ( !in_array( 'porto_preview_image', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/preview_image";
<?php endif; ?>
<?php if ( !in_array( 'porto_buttons', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/buttons";
<?php endif; ?>
<?php if ( !in_array( 'porto_concept', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/concept";
<?php endif; ?>

@import "theme/shortcodes/countdown";

<?php if ( !in_array( 'porto_diamonds', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/diamond";
<?php endif; ?>
<?php if ( !in_array( 'porto_experience_timeline_item', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/experience_timeline";
<?php endif; ?>
<?php if ( !in_array( 'porto_fancytext', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/fancytext";
<?php endif; ?>
<?php if ( !in_array( 'porto_floating_menu_item', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/float_menu";
<?php endif; ?>
<?php if ( !in_array( 'porto_google_map', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/google_map";
<?php endif; ?>
<?php if ( !in_array( 'porto_ultimate_heading', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/heading";
<?php endif; ?>
<?php if ( !in_array( 'porto_history', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/history";
<?php endif; ?>
<?php if ( !in_array( 'porto_icon', $unusedShortcodeList ) || !in_array( 'porto_info_box', $unusedShortcodeList ) || !in_array( 'porto_stat_counter', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/icon";
<?php endif; ?>
<?php if ( !in_array( 'porto_icons', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/icons";
<?php endif; ?>
<?php if ( !in_array( 'porto_info_box', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/info_box";
<?php endif; ?>
<?php if ( !in_array( 'porto_info_list', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/info_list";
<?php endif; ?>
<?php if ( !in_array( 'porto_interactive_banner', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/interactive_banner";
<?php endif; ?>
<?php if ( !in_array( 'porto_links_block', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/links_block";
<?php endif; ?>
<?php if ( !in_array( 'porto_map_section', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/map_section";
<?php endif; ?>
<?php if ( !in_array( 'porto_price_box', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/pricing_tables";
<?php endif; ?>
<?php if ( !in_array( 'porto_schedule_timeline_item', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/schedule_timeline";
<?php endif; ?>
<?php if ( !in_array( 'porto_stat_counter', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/stat_counter";
<?php endif; ?>
<?php if ( class_exists( 'Woocommerce' ) || !in_array( 'porto_ultimate_carousel', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/ultimate_carousel";
<?php endif; ?>
<?php if ( !in_array( 'porto_ultimate_content_box', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/ultimate_content_box";
<?php endif; ?>
<?php if ( !in_array( 'vc_btn', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_button";
<?php endif; ?>
<?php if ( !in_array( 'vc_cta', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_cta3";
<?php endif; ?>
<?php if ( !in_array( 'vc_custom_heading', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_custom_heading";
<?php endif; ?>
<?php if ( !in_array( 'vc_pie', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_pie";
<?php endif; ?>
<?php if ( !in_array( 'vc_progress_bar', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_progress_bar";
<?php endif; ?>
<?php if ( !in_array( 'vc_row', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_row";
<?php endif; ?>
<?php if ( !in_array( 'vc_separator', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_separator";
<?php endif; ?>
<?php if ( !in_array( 'vc_single_image', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_single_image";
<?php endif; ?>
<?php if ( !in_array( 'vc_column_text', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/vc_text_column";
<?php endif; ?>
<?php if ( !in_array( 'porto_one_page_category_products', $unusedShortcodeList ) ) : ?>
@import "theme/shortcodes/woo_one_page_category_products";
<?php endif; ?>

.inline-block { display: inline-block; }
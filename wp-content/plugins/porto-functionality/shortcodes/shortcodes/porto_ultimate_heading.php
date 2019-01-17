<?php
// Porto Ultimate Heading
if ( function_exists( 'register_block_type' ) ) {
    register_block_type( 'porto/porto-ultimate-heading', array(
        'editor_script'   => 'porto_blocks',
        'render_callback' => 'porto_shortcode_ultimate_heading',
    ) );
}
add_shortcode('porto_ultimate_heading', 'porto_shortcode_ultimate_heading');
add_action('vc_after_init', 'porto_load_ultimate_heading_shortcode');

function porto_shortcode_ultimate_heading( $atts, $content = null ) {

    ob_start();
    if  ($template = porto_shortcode_template( 'porto_ultimate_heading' ) ) {
        include $template;
    }
    return ob_get_clean();
}

function porto_load_ultimate_heading_shortcode() {

    $animation_type = porto_vc_animation_type();
    $animation_duration = porto_vc_animation_duration();
    $animation_delay = porto_vc_animation_delay();
    $custom_class = porto_vc_custom_class();

    vc_map(
        array(
           'name' => __('Porto Headings','porto-shortcodes'),
           'base' => 'porto_ultimate_heading',
           'class' => 'porto_ultimate_heading',
           'icon' => 'porto4_vc_ultimate_heading',
           'category' => __('Porto', 'porto-shortcodes'),
           'description' => __('Awesome heading styles.','porto-shortcodes'),
           'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __( 'Title', 'porto-shortcodes' ),
                    'param_name' => 'main_heading',
                    'holder' => 'div',
                    'value' => ''
                ),
                array(
                    'type' => 'porto_param_heading',
                    'text' => __('Heading Settings','porto-shortcodes'),
                    'param_name' => 'main_heading_typograpy',
                    'group' => 'Typography',
                    'class' => ''
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __('Use theme default font family?','porto-shortcodes'),
                    'param_name' => 'main_heading_use_theme_fonts',
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
                    'std' => 'yes',
                    'group' => 'Typography',
                    'class' => ''
                ),
                array(
                    'type' => 'google_fonts',
                    'param_name' => 'main_heading_font',
                    'settings' => array(
                        'fields' => array(
                            'font_family_description' => __( 'Select Font Family.', 'porto-shortcodes' ),
                            'font_style_description' => __( 'Select Font Style.', 'porto-shortcodes' ),
                        ),
                    ),
                    'dependency' => array('element' => 'main_heading_use_theme_fonts', 'value_not_equal_to' => 'yes'),
                    'group' => 'Typography'
                ),
                array(
                    'type' => 'textfield',
                    'class' => 'font-size',
                    'heading' => __('Font size', 'porto-shortcodes'),
                    'param_name' => 'main_heading_font_size',
                    'group' => 'Typography'
                ),
                array(
                    'type' => 'textfield',
                    'class' => '',
                    'heading' => __('Font Weight', 'porto-shortcodes'),
                    'param_name' => 'main_heading_font_weight',
                    'group' => 'Typography'
                ),
                array(
                    'type' => 'colorpicker',
                    'class' => '',
                    'heading' => __('Font Color', 'porto-shortcodes'),
                    'param_name' => 'main_heading_color',
                    'value' => '',
                    'group' => 'Typography'
                ),

                array(
                    'type' => 'textfield',
                    'class' => 'font-size',
                    'heading' => __('Line Height', 'porto-shortcodes'),
                    'param_name' => 'main_heading_line_height',
                    'group' => 'Typography'
                ),

                array(
                    'type' => 'number',
                    'heading' => __('Heading Margin Bottom','porto-shortcodes'),
                    'param_name' => 'main_heading_margin_bottom',
                    'suffix' => 'px',
                    'group' => 'Design'
                ),
                array(
                    'type' => 'textarea_html',
                    'edit_field_class' => 'vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
                    'heading' => __('Sub Heading (Optional)', 'porto-shortcodes'),
                    'param_name' => 'content',
                    'value' => '',
                ),
                array(
                    'type' => 'dropdown',
                    'heading' => __('Tag','porto-shortcodes'),
                    'param_name' => 'heading_tag',
                    'value' => array(
                        __('Default','porto-shortcodes') => 'h2',
                        __('H1','porto-shortcodes') => 'h1',
                        __('H3','porto-shortcodes') => 'h3',
                        __('H4','porto-shortcodes') => 'h4',
                        __('H5','porto-shortcodes') => 'h5',
                        __('H6','porto-shortcodes') => 'h6',
                    ),
                    'description' => __('Default is H2', 'porto-shortcodes'),
                ),
                array(
                    'type' => 'porto_param_heading',
                    'text' => __('Sub Heading Settings','porto-shortcodes'),
                    'param_name' => 'sub_heading_typograpy',
                    'group' => 'Typography',
                    'class' => '',
                    'edit_field_class' => 'vc_column vc_col-sm-12',
                ),
                
                array(
                    'type' => 'checkbox',
                    'heading' => __('Use theme default font family?','porto-shortcodes'),
                    'param_name' => 'sub_heading_use_theme_fonts',
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
                    'std' => 'yes',
                    'group' => 'Typography',
                    'class' => ''
                ),
                array(
                    'type' => 'google_fonts',
                    'param_name' => 'sub_heading_font',
                    'settings' => array(
                        'fields' => array(
                            'font_family_description' => __( 'Select Font Family.', 'porto-shortcodes' ),
                            'font_style_description' => __( 'Select Font Style.', 'porto-shortcodes' ),
                        ),
                    ),
                    'dependency' => array('element' => 'sub_heading_use_theme_fonts', 'value_not_equal_to' => 'yes'),
                    'group' => 'Typography'
                ),
                array(
                    'type' => 'textfield',
                    'class' => '',
                    'heading' => __('Font Size', 'porto-shortcodes'),
                    'param_name' => 'sub_heading_font_size',
                    'group' => 'Typography'
                ),
                array(
                    'type' => 'textfield',
                    'class' => '',
                    'heading' => __('Font Weight', 'porto-shortcodes'),
                    'param_name' => 'sub_heading_font_weight',
                    'group' => 'Typography'
                ),
                array(
                    'type' => 'colorpicker',
                    'class' => '',
                    'heading' => __('Font Color', 'porto-shortcodes'),
                    'param_name' => 'sub_heading_color',
                    'value' => '',
                    'group' => 'Typography',
                ),

                array(
                    'type' => 'textfield',
                    'class' => '',
                    'heading' => __('Line Height', 'porto-shortcodes'),
                    'param_name' => 'sub_heading_line_height',
                    'group' => 'Typography'
                ),
                array(
                    'type' => 'number',
                    'heading' => 'Sub Heading Margin Bottom',
                    'param_name' => 'sub_heading_margin_bottom',
                    'suffix' => 'px',
                    'group' => 'Design'
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Alignment', 'porto-shortcodes'),
                    'param_name' => 'alignment',
                    'value' => array(
                        __('Center','porto-shortcodes')  =>  'center',
                        __('Left','porto-shortcodes')    =>  'left',
                        __('Right','porto-shortcodes')   =>  'right'
                    ),
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Seperator', 'porto-shortcodes'),
                    'param_name' => 'spacer',
                    'value' => array(
                        __('No Seperator','porto-shortcodes')    =>  'no_spacer',
                        __('Line','porto-shortcodes')            =>  'line_only',
                        __('Image','porto-shortcodes')           => 'image_only',
                    ),
                    'description' => __('Horizontal line, icon or image to divide sections', 'porto-shortcodes'),
                ),
                array(
                    'type' => 'dropdown',
                    'class' => '',
                    'heading' => __('Seperator Position', 'porto-shortcodes'),
                    'param_name' => 'spacer_position',
                    'value' => array(
                        __('Top','porto-shortcodes')     =>  'top',
                        __('Between Heading & Sub-Heading','porto-shortcodes')   =>  'middle',
                        __('Bottom','porto-shortcodes')  =>  'bottom'
                    ),
                    'dependency' => array('element' => 'spacer', 'value' => array('line_only')),
                ),
                array(
                    'type' => 'number',
                    'class' => '',
                    'heading' => __('Line Width (optional)', 'porto-shortcodes'),
                    'param_name' => 'line_width',
                    'suffix' => 'px',
                    'dependency' => array('element' => 'spacer', 'value' => array('line_only')),
                ),
                array(
                    'type' => 'number',
                    'class' => '',
                    'heading' => __('Line Height', 'porto-shortcodes'),
                    'param_name' => 'line_height',
                    'value' => 1,
                    'min' => 1,
                    'max' => 500,
                    'suffix' => 'px',
                    'dependency' => array('element' => 'spacer', 'value' => array('line_only')),
                ),
                array(
                    'type' => 'colorpicker',
                    'class' => '',
                    'heading' => __('Line Color', 'porto-shortcodes'),
                    'param_name' => 'line_color',
                    'value' => '#333333',
                    'dependency' => array('element' => 'spacer', 'value' => array('line_only')),
                ),
                array(
                    'type' => 'number',
                    'heading' => 'Seperator Margin Bottom',
                    'param_name' => 'spacer_margin_bottom',
                    'suffix' => 'px',
                    'dependency' => array('element' => 'spacer', 'value' => array('line_only')),
                    'group' => 'Design'
                ),
                $animation_type,
                $animation_duration,
                $animation_delay,
                $custom_class
            )
        )
    );

    if ( !class_exists( 'WPBakeryShortCode_Porto_Ultimate_Headings' ) ) {
        class WPBakeryShortCode_Porto_Ultimate_Headings extends WPBakeryShortCodesContainer { }
    }
}
<?php
// Porto Buttons

add_shortcode('porto_buttons', 'porto_shortcode_buttons');
add_action('vc_after_init', 'porto_load_buttons_shortcode');

function porto_shortcode_buttons( $atts, $content = null ) {

    ob_start();
    if  ($template = porto_shortcode_template( 'porto_buttons' ) ) {
        include $template;
    }
    return ob_get_clean();
}

function porto_load_buttons_shortcode() {

    $custom_class = porto_vc_custom_class();
    $custom_class['group'] = "General";

    vc_map(
        array(
            "name" => __("Porto Advanced Button", "porto-shortcodes"),
            "base" => "porto_buttons",
            "icon" => "porto4_vc_buttons",
            "class" => "porto_buttons",
            "content_element" => true,
            "controls" => "full",
            "category" => __("Porto", 'porto-shortcodes'),
            "description" => __("Create creative buttons.","porto-shortcodes"),
            "params" => array(
                array(
                    "type" => "textfield",
                    "heading" => __("Button Title","porto-shortcodes"),
                    "param_name" => "btn_title",
                    "value" => "",
                    "description" => "",
                    "group" => "General",
                    "admin_label" => true
                ),
                array(
                    "type" => "vc_link",
                    "heading" => __("Button Link","porto-shortcodes"),
                    "param_name" => "btn_link",
                    "value" => "",
                    "description" => "",
                    "group" => "General"
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Button Alignment","porto-shortcodes"),
                    "param_name" => "btn_align",
                    "value" => array(
                            __("Left Align","porto-shortcodes") => "porto-btn-left",
                            __("Center Align","porto-shortcodes") => "porto-btn-center",
                            __("Right Align","porto-shortcodes") => "porto-btn-right",
                            __("Inline","porto-shortcodes") => "porto-btn-inline",
                        ),
                    "description" => "",
                    "group" => "General"
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Button Size","porto-shortcodes"),
                    "param_name" => "btn_size",
                    "value" => array(
                            __("Normal Button","porto-shortcodes") => "porto-btn-normal",
                            __("Mini Button","porto-shortcodes") => "porto-btn-mini",
                            __("Small Button","porto-shortcodes") => "porto-btn-small",
                            __("Large Button","porto-shortcodes") => "porto-btn-large",
                            __("Button Block","porto-shortcodes") => "porto-btn-block",
                            __("Custom Size","porto-shortcodes") => "porto-btn-custom",
                        ),
                    "group" => "General"
                ),
                array(
                    "type" => "number",
                    "heading" => __("Button Width","porto-shortcodes"),
                    "param_name" => "btn_width",
                    "value" => "",
                    "min" => 10,
                    "max" => 1000,
                    "suffix" => "px",
                    "description" => "",
                    "dependency" => array("element" => "btn_size", "value" => "porto-btn-custom" ),
                    "group" => "General"
                ),
                array(
                    "type" => "number",
                    "heading" => __("Button Height","porto-shortcodes"),
                    "param_name" => "btn_height",
                    "value" => "",
                    "min" => 10,
                    "max" => 1000,
                    "suffix" => "px",
                    "description" => "",
                    "dependency" => array("element" => "btn_size", "value" => "porto-btn-custom" ),
                    "group" => "General"
                ),
                array(
                    "type" => "number",
                    "heading" => __("Button Left / Right Padding","porto-shortcodes"),
                    "param_name" => "btn_padding_left",
                    "value" => "",
                    "min" => 10,
                    "max" => 1000,
                    "suffix" => "px",
                    "description" => "",
                    "dependency" => array("element" => "btn_size", "value" => "porto-btn-custom" ),
                    "group" => "General"
                ),
                array(
                    "type" => "number",
                    "heading" => __("Button Top / Bottom Padding","porto-shortcodes"),
                    "param_name" => "btn_padding_top",
                    "value" => "",
                    "min" => 10,
                    "max" => 1000,
                    "suffix" => "px",
                    "description" => "",
                    "dependency" => array("element" => "btn_size", "value" => "porto-btn-custom" ),
                    "group" => "General"
                ),
                array(
                    "type" => "colorpicker",
                    "heading" => __("Button Title Color","porto-shortcodes"),
                    "param_name" => "btn_title_color",
                    "value" => "#000000",
                    "description" => "",
                    "group" => "General"
                ),
                array(
                    "type" => "colorpicker",
                    "heading" => __("Background Color","porto-shortcodes"),
                    "param_name" => "btn_bg_color",
                    "value" => "#e0e0e0",
                    "description" => "",
                    "group" => "General"
                ),
                $custom_class,
                array(
                    "type" => "textfield",
                    "heading" => __("Rel Attribute", "porto-shortcodes"),
                    "param_name" => "rel",
                    "description" => __("This is useful when you want to trigger third party features. Example- prettyPhoto, thickbox etc", "porto-shortcodes"),
                    "group" => "General",
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Button Hover Background Effect","porto-shortcodes"),
                    "param_name" => "btn_hover",
                    "value" => array(
                            __("No Effect","porto-shortcodes") => "porto-btn-no-hover-bg",
                            __("Fade Background","porto-shortcodes") => "porto-btn-fade-bg",
                            __("Fill Background from Top","porto-shortcodes") => "porto-btn-top-bg",
                            __("Fill Background from Bottom","porto-shortcodes") => "porto-btn-bottom-bg",
                            __("Fill Background from Left","porto-shortcodes") => "porto-btn-left-bg",
                            __("Fill Background from Right","porto-shortcodes") => "porto-btn-right-bg",
                            __("Fill Background from Center Horizontally","porto-shortcodes") => "porto-btn-center-hz-bg",
                            __("Fill Background from Center Vertically","porto-shortcodes") => "porto-btn-center-vt-bg",
                            __("Fill Background from Center Diagonal","porto-shortcodes") => "porto-btn-center-dg-bg",
                        ),
                    "group" => "Background"
                ),
                array(
                    "type" => "colorpicker",
                    "heading" => __("Hover Background Color","porto-shortcodes"),
                    "param_name" => "btn_bg_color_hover",
                    "value" => "",
                    "description" => "",
                    "group" => "Background"
                ),
                array(
                    "type" => "colorpicker",
                    "heading" => __("Hover Text Color","porto-shortcodes"),
                    "param_name" => "btn_title_color_hover",
                    "value" => "",
                    "description" => "",
                    "group" => "Background"
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Button Border Style", "porto-shortcodes"),
                    "param_name" => "btn_border_style",
                    "value" => array(
                        "None"=> "",
                        "Solid"=> "solid",
                        "Dashed" => "dashed",
                        "Dotted" => "dotted",
                        "Double" => "double",
                        "Inset" => "inset",
                        "Outset" => "outset",
                    ),
                    "description" => "",
                    "group" => "Styling"
                ),
                array(
                    "type" => "colorpicker",
                    "heading" => __("Border Color", "porto-shortcodes"),
                    "param_name" => "btn_color_border",
                    "value" => "",
                    "description" => "",
                    "dependency" => array("element" => "btn_border_style", "not_empty" => true),
                    "group" => "Styling"
                ),
                array(
                    "type" => "colorpicker",
                    "heading" => __("Border Color on Hover", "porto-shortcodes"),
                    "param_name" => "btn_color_border_hover",
                    "value" => "",
                    "description" => "",
                    "dependency" => array("element" => "btn_border_style", "not_empty" => true),
                    "group" => "Styling"
                ),
                array(
                    "type" => "number",
                    "heading" => __("Border Width", "porto-shortcodes"),
                    "param_name" => "btn_border_size",
                    "value" => 1,
                    "min" => 1,
                    "max" => 10,
                    "suffix" => "px",
                    "description" => "",
                    "dependency" => array("element" => "btn_border_style", "not_empty" => true),
                    "group" => "Styling"
                ),
                array(
                    "type" => "number",
                    "heading" => __("Border Radius","porto-shortcodes"),
                    "param_name" => "btn_radius",
                    "value" => 3,
                    "min" => 0,
                    "max" => 500,
                    "suffix" => "px",
                    "description" => "",
                    "dependency" => array("element" => "btn_border_style", "not_empty" => true),
                    "group" => "Styling"
                ),
                array(
                    'type' => 'css_editor',
                    'heading' => __( 'Css', 'porto-shortcodes' ),
                    'param_name' => 'css_adv_btn',
                    'group' => __( 'Styling', 'porto-shortcodes' ),
                    'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
                ),
                array(
                    'type' => 'checkbox',
                    'heading' => __('Use theme default font family?','porto-shortcodes'),
                    'param_name' => 'btn_font_use_theme_fonts',
                    'value' => array( __( 'Yes', 'js_composer' ) => 'yes' ),
                    'std' => 'yes',
                    'group' => 'Typography',
                    'class' => ''
                ),
                array(
                    'type' => 'google_fonts',
                    'param_name' => 'btn_font',
                    'settings' => array(
                        'fields' => array(
                            'font_family_description' => __( 'Select Font Family.', 'porto-shortcodes' ),
                            'font_style_description' => __( 'Select Font Style.', 'porto-shortcodes' ),
                        ),
                    ),
                    'dependency' => array('element' => 'btn_font_use_theme_fonts', 'value_not_equal_to' => 'yes'),
                    'group' => 'Typography'
                ),
                array(
                    "type" => "textfield",
                    "heading"       =>  __("Font Weight", "porto-shortcodes"),
                    "param_name"    =>  "btn_font_style",
                    "group" => "Typography"
                ),
                array(
                    "type" => "textfield",
                    "heading" => __("Font size", 'porto-shortcodes'),
                    "param_name" => "btn_font_size",
                    "group" => "Typography"
                ),
                array(
                    "type" => "textfield",
                    "heading" => __("Line Height", 'porto-shortcodes'),
                    "param_name" => "btn_line_height",
                    "group" => "Typography"
                ),
            )
        )
    );

    if(class_exists('WPBakeryShortCode'))
    {
        class WPBakeryShortCode_porto_buttons extends WPBakeryShortCode {
        }
    }
}
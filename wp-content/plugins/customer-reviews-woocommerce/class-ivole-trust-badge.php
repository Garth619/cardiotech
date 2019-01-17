<?php

if (! defined('ABSPATH')) {
    exit;
}

if (! class_exists('Ivole_Trust_Badge')) :

    class Ivole_Trust_Badge
    {

        /**
         * @var array holds the current shorcode attributes
         */
        public $shortcode_atts;

        public function __construct( $shortcode_atts )
        {
            $defaults = array(
                'type' => 'sl',
                'border' => 'yes'
            );
            if ( isset( $shortcode_atts['type'] ) ) {
                $type = str_replace( ' ', '', $shortcode_atts['type'] );
                $type = strtolower( $type );
                $allowed_types = array( 'sl', 'slp', 'sd', 'sdp', 'wl', 'wlp', 'wd', 'wdp' );
                if( in_array( $type, $allowed_types ) ) {
                  $shortcode_atts['type'] = $type;
                } else {
                  $shortcode_atts['type'] = null;
                }
            }
            if ( isset( $shortcode_atts['border'] ) ) {
                $border = str_replace( ' ', '', $shortcode_atts['border'] );
                $border = strtolower( $border );
                $allowed_borders = array( 'yes', 'no' );
                if( in_array( $border, $allowed_borders ) ) {
                  $shortcode_atts['border'] = $border;
                } else {
                  $shortcode_atts['border'] = 'yes';
                }
            }
            $this->shortcode_atts = shortcode_atts($defaults, $shortcode_atts);
            // load styles and js
            $this->ivole_style();
        }

        public function show_trust_badge()
        {
            $class_img = 'ivole-trustbadgefi-' . $this->shortcode_atts['type'] . ' ivole-trustbadgefi-b' . $this->shortcode_atts['border'];
            $return = '<div id="ivole_trustbadgef_' . $this->shortcode_atts['type'] . '" class="ivole-trustbadgef-' . $this->shortcode_atts['type'] . '">';
            $return .= '<a href="https://www.cusrev.com/reviews/' . get_option( 'ivole_reviews_verified_page', Ivole_Email::get_blogdomain() ) . '" rel="nofollow" target="_blank"><img id="ivole_trustbadgefi_' . $this->shortcode_atts['type'] . '" class="' . $class_img . '" src="' . add_query_arg( 't', time(), 'https://www.cusrev.com/badges/' . Ivole_Email::get_blogurl() . '-' . $this->shortcode_atts['type'] . '.png' ) . '"></a>';
            $return .= '</div>';
            return $return;
        }

        public function ivole_style()
        {
            wp_register_style('ivole-frontend-css', plugins_url('/css/frontend.css', __FILE__), array(), null, 'all');
            wp_enqueue_style('ivole-frontend-css');
        }
    }

endif;

<?php
/**
 * Default header type for header builder
 *
 * @since 4.8
 */

global $porto_settings;

if ( is_customize_preview() ) {
    $header_layout = get_option( 'porto_header_builder', '' );
    $header_builder_positions = get_option( 'porto_header_builder_layouts', array() );
    if ( isset( $header_layout['selected_layout'] ) && $header_layout['selected_layout'] && isset( $header_builder_positions[$header_layout['selected_layout']] ) ) {
        $header_elements = get_option( 'porto_header_builder_elements', array() );
    } else {
        $header_elements = array();
    }
} else {
    $current_layout = porto_header_builder_layout();
    $header_elements = isset( $current_layout['elements'] ) ? $current_layout['elements'] : array();
}

if ( is_customize_preview() && porto_get_wrapper_type() != 'boxed' && $porto_settings['header-wrapper'] == 'boxed') : ?>
    <div id="header-boxed">
<?php endif; ?>
    <header id="header" class="header-builder<?php echo porto_header_type_is_side() ? ' header-side sticky-menu-header' : ''; ?><?php echo ($porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url']) ? ' logo-overlay-header' : '' ?>">
    <?php
        $header_rows = array( 'top', 'main', 'bottom' );
        $header_columns = array( 'left', 'center', 'right' );
        $mobile_use_same = true;
        foreach( $header_rows as $row ) {
            foreach( $header_columns as $column ) {
                if ( isset( $header_elements['mobile_'. $row. '_' .$column] ) && $header_elements['mobile_'. $row. '_' .$column] && !empty( json_decode( $header_elements['mobile_'. $row. '_' .$column] ) ) ) {
                    $mobile_use_same = false;
                    break 2;
                }
            }
        }
        foreach( $header_rows as $row ) {
            $header_row_used = false;
            $mobile_header_row_used = false;
            $header_has_center = isset( $header_elements[$row. '_center'] ) && $header_elements[$row. '_center'] && !empty( json_decode( $header_elements[$row. '_center'] ) );
            $mobile_header_has_center = false ? $header_has_center : ( isset( $header_elements['mobile_'. $row. '_center'] ) && $header_elements['mobile_'. $row. '_center'] && !empty( json_decode( $header_elements['mobile_'. $row. '_center'] ) ) );
            foreach( $header_columns as $column ) {
                if ( isset( $header_elements[$row. '_' .$column] ) && $header_elements[$row. '_' .$column] && !empty( json_decode( $header_elements[$row. '_' .$column] ) ) ) {
                    $header_row_used = true;
                    if ( $mobile_use_same ) {
                        break;
                    }
                }
                if ( !$mobile_use_same && isset( $header_elements['mobile_'. $row. '_' .$column] ) && $header_elements['mobile_'. $row. '_' .$column] && !empty( json_decode( $header_elements['mobile_'. $row. '_' .$column] ) ) ) {
                    $mobile_header_row_used = true;
                }
            }
            if ( $header_row_used || $mobile_header_row_used ) {
                echo '<div class="header-'. $row . ( $header_has_center ? ' header-has-center' : '' ) . ( $mobile_header_has_center ? ' header-has-center-sm' : '' ) . ( $header_has_center && !$mobile_use_same && !$mobile_header_has_center ? ' header-has-not-center-sm' : '' ) . ( 'top' == $row && $header_row_used && !$mobile_header_row_used ? ' hidden-for-sm' : '' ) .'">';
                    if ( porto_header_type_is_side() ) {
                        echo '<div class="header-row">';
                    } else {
                        echo '<div class="header-row container">';
                    }
                    foreach( $header_columns as $column ) {
                        $elements = isset( $header_elements[$row. '_' .$column] ) ? json_decode( $header_elements[$row. '_' .$column] ) : array();
                        $mobile_elements = isset( $header_elements['mobile_'. $row. '_' .$column] ) ? json_decode( $header_elements['mobile_'. $row. '_' .$column] ) : array();

                        $mobile_col_use_same = $mobile_use_same;
                        if ( !$mobile_col_use_same ) {
                            $mobile_col_use_same = empty( $elements ) && empty( $mobile_elements ) ? true : ( isset( $header_elements[$row. '_' .$column] ) && isset( $header_elements['mobile_'. $row .'_' .$column] ) && $header_elements[$row. '_' .$column] == $header_elements['mobile_'. $row .'_' .$column] ? true : false );
                        }
                        if ( !empty( $elements ) ) {
                            echo '<div class="header-col header-'. $column . ( !$mobile_col_use_same ? ' hidden-for-sm' : '' ) .'">';
                                porto_header_elements( $elements );
                            echo '</div>';
                        }
                        if ( !empty( $mobile_elements ) && !$mobile_col_use_same ) {
                            echo '<div class="header-col visible-for-sm header-'. $column .'">';
                                porto_header_elements( $mobile_elements );
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                echo '</div>';
            }
        }

        get_template_part('header/mobile_menu');
    ?>
    </header>
<?php if ( is_customize_preview() && porto_get_wrapper_type() != 'boxed' && $porto_settings['header-wrapper'] == 'boxed' ) : ?>
    </div>
<?php endif; ?>
<?php
	if( ! defined( 'ABSPATH' ) ){
		exit; // Exit if accessed directly
	}
?>
<div class="wrap about-wrap porto-wrap">
    <h1><?php _e( 'Welcome to Porto!', 'porto' ); ?></h1>
    <div class="about-text"><?php echo esc_html__( 'Porto is now installed and ready to use! Read below for additional information. We hope you enjoy it!', 'porto' ); ?></div>
    <div class="porto-logo"><span class="porto-version"><?php _e( 'Version', 'porto' ); ?> <?php echo porto_version; ?></span></div>
    <h2 class="nav-tab-wrapper">
        <?php
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=porto' ), __( "Theme License", 'porto' ) );
        printf( '<a href="#" class="nav-tab nav-tab-active">%s</a>', __( "Change Log", 'porto' ) );
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'customize.php' ), __( "Theme Options", 'porto' ) );
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'themes.php?page=porto_settings' ), __( "Advanced", 'porto' ) );
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=porto-setup-wizard' ), __( "Setup Wizard", 'porto' ) );
        printf( '<a href="%s" class="nav-tab">%s</a>', admin_url( 'admin.php?page=porto-speed-optimize-wizard' ), __( "Speed Optimize Wizard", 'porto' ) );
        ?>
    </h2>
    <div class="porto-section porto-changelog">
        <?php
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'http://www.portotheme.com/activation/porto_wp/download/changelog.txt' );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            $result = curl_exec( $ch );
            echo preg_replace( "/<script.*?\/script>/s", "", $result );
        ?>
    </div>
    <div class="porto-thanks">
        <p class="description"><?php _e( 'Thank you, we hope you to enjoy using Porto!', 'porto' ); ?></p>
    </div>
</div>
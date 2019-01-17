<?php
global $porto_settings, $porto_layout;
?>
<header id="header" class="header-6<?php echo ($porto_settings['logo-overlay'] && $porto_settings['logo-overlay']['url']) ? ' logo-overlay-header' : '' ?>">
    <?php if ($porto_settings['show-header-top']) : ?>
    <div class="header-top">
        <div class="container">
            <div class="header-left">
                <?php
                // show social links
                echo porto_header_socials();
                ?>
            </div>
            <div class="header-right">
                <?php
                // show welcome message
                if ($porto_settings['welcome-msg'])
                    echo '<span class="welcome-msg">' . do_shortcode($porto_settings['welcome-msg']) . '</span>';
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="header-main">
        <div class="container">
            <div class="header-left">
                <?php
                // show logo
                $logo = porto_logo();
                echo $logo;
                ?>
            </div>
            <div class="header-center">
                <div class="header-center-top">
                    <?php
                        // show top navigation
                        $top_nav = porto_top_navigation();
                        echo $top_nav;
                    ?>
                </div>

                <div class="header-center-bottom">
                    <div id="main-menu">
                        <?php
                        // show main menu
                        echo porto_main_menu();
                        ?>
                    </div>
                    <?php // show mobile toggle ?>
                    <div class="mobile-toggle-section inline-block d-lg-none d-xl-none">
                        <a class="mobile-toggle"><i class="fa fa-reorder"></i></a>
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="header-right-top">
                    <?php
                        // show search form
                        echo porto_search_form();
                        $minicart = porto_minicart();
                    ?>
                </div>
                <div class="header-right-bottom">
                    <div class="header-minicart">
                        <?php
                        // show currency and view switcher
                        $currency_switcher = porto_currency_switcher();
                        $view_switcher = porto_view_switcher();

                        if ($currency_switcher || $view_switcher)
                            echo '<div class="switcher-wrap">';

                        echo $view_switcher;

                        echo $currency_switcher;

                        if ($currency_switcher || $view_switcher)
                            echo '</div>';

                        // show mini cart
                        echo $minicart;
                        ?>
                    </div>

                    <?php
                    get_template_part('header/header_tooltip');
                    ?>
                </div>

            </div>
        </div>
        <?php
            get_template_part('header/mobile_menu');
        ?>
    </div>
</header>
<?php get_header() ?>

<?php
global $porto_settings, $wp_query, $porto_layout;

$term = $wp_query->queried_object;
$term_id = $term->term_id;

$member_options = get_metadata($term->taxonomy, $term->term_id, 'member_options', true) == 'member_options' ? true : false;

?>

<div id="content" role="main" class="<?php if ($porto_layout === 'widewidth' || $porto_layout === 'wide-left-sidebar' || $porto_layout === 'wide-right-sidebar') { echo 'm-t-lg m-b-xl'; if (porto_get_wrapper_type() !=='boxed') echo ' m-r-md m-l-md'; } ?>">

    <?php if (category_description()) : ?>
        <div class="page-content">
            <?php echo category_description() ?>
        </div>
    <?php endif; ?>

    <?php if (have_posts()) : ?>

        <div class="page-members clearfix">

            <?php if ($porto_settings['member-archive-ajax']) : ?>
                <div id="memberAjaxBox" class="ajax-box">
                    <div class="bounce-loader">
                        <div class="bounce1"></div>
                        <div class="bounce2"></div>
                        <div class="bounce3"></div>
                    </div>
                    <div class="ajax-box-content" id="memberAjaxBoxContent"></div>
                    <?php if (function_exists('porto_title_archive_name') && porto_title_archive_name('member')) : ?>
                        <div class="hide ajax-content-append"><h4 class="m-t-sm m-b-lg"><?php echo sprintf( __( 'More %s:', 'porto' ), porto_title_archive_name('member') ); ?></h4></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="member-row members-container">
                <?php
                while (have_posts()) {
                    the_post();

                    get_template_part('content', 'archive-member');
                }
                ?>
            </div>

            <?php porto_pagination(); ?>

        </div>

        <?php wp_reset_postdata(); ?>

    <?php else : ?>

        <p><?php _e('Apologies, but no results were found for the requested archive.', 'porto'); ?></p>

    <?php endif; ?>
</div>

<?php get_footer() ?>
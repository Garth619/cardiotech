<?php
global $porto_settings, $post;

$archive_image = (int)get_post_meta($post->ID, 'portfolio_archive_image', true);
if ($archive_image) {
    $featured_images = array();
    $featured_image         = array(
        'thumb'         => wp_get_attachment_thumb_url( $archive_image ),
        'full'          => wp_get_attachment_url( $archive_image ),
        'attachment_id' => $archive_image
    );
    $featured_images[] = $featured_image;
} else {
    $featured_images = porto_get_featured_images();
}
$portfolio_link = get_post_meta($post->ID, 'portfolio_link', true);
$show_external_link = $porto_settings['portfolio-external-link'];

if (count($featured_images)) :
    $attachment_id = $featured_images[0]['attachment_id'];
    $attachment_thumb = porto_get_attachment($attachment_id, 'widget-thumb-medium');
    ?>
    <div class="portfolio-item-small">
        <div class="portfolio-image img-thumbnail">
            <a href="<?php if ($show_external_link && $portfolio_link) echo $portfolio_link; else the_permalink() ?>">
                <img width="<?php echo $attachment_thumb['width'] ?>" height="<?php echo $attachment_thumb['height'] ?>" src="<?php echo $attachment_thumb['src'] ?>" alt="<?php echo $attachment_thumb['alt'] ?>" />
            </a>
        </div>
    </div>
<?php
endif;

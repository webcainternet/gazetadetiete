<?php
/**
 * Template for testimonial post type display.
 * @package themify
 * @since 1.0.0
 */
?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php

$link = themify_get_featured_image_link('no_permalink=true');
$before = '';
$after = '';
if ( $link != '' ) {
	$before = '<a href="' . $link . '" title="' . get_the_title() . '">';
	$zoom_icon = themify_zoom_icon( false );
	$after = $zoom_icon . '</a>' . $after;
	$zoom_icon = '';
}

// Check if user wants to use a common dimension or those defined in each highlight
if ('yes' == $themify->use_original_dimensions) {
	// Save post id
	$post_id = get_the_ID();

	// Set image width
	$themify->width = get_post_meta($post_id, 'image_width', true);

	// Set image height
	$themify->height = get_post_meta($post_id, 'image_height', true);
}

$testimonial_image = themify_get_image('ignore=true&w='.$themify->width.'&h='.$themify->height);

// Get image url from returned image
$img_doc = new DOMDocument();
$img_doc->loadHTML($testimonial_image);
$img_tag = $img_doc->getElementsByTagName('img');
foreach( $img_tag as $tag ) {
	$testimonial_thumb = $tag->getAttribute('src');
}

?>

<?php if ( 'slider' == $themify->post_layout ) :
	wp_enqueue_script('themify-carousel-js');

?>
	<li data-image="">
<?php endif; ?>

<article id="testimonial-<?php the_ID(); ?>" <?php post_class('post clearfix testimonial-post'); ?> data-thumb="<?php echo $testimonial_thumb; ?>" data-thumbw="<?php echo $themify->width; ?>" data-thumbh="<?php echo $themify->height; ?>">

	<div class="testimonial-content">
		<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
			<?php the_excerpt(); ?>
		<?php elseif($themify->display_content == 'content'): ?>
			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
		<?php endif; //display content ?>
	</div>

	<?php if ( ( 'slider' != $themify->post_layout ) && ( 'no' != $themify->hide_image ) ): ?>
		<figure class="post-image">
			<?php echo $before; ?>
			<?php echo $testimonial_image; ?>
			<?php echo $after; ?>
		</figure>
	<?php endif; // hide image ?>

	<?php
	$testimonial_author = themify_get('testimonial_name');
	$testimonial_title = themify_get('testimonial_title');
	if( $testimonial_author || $testimonial_title ) : ?>
		<p class="testimonial-author">
			<?php echo $before.$testimonial_author.$after; ?><?php if( $testimonial_title ) : ?><span class="testimonial-title"><?php echo $testimonial_title; ?></span><?php endif; ?>
		</p>
	<?php endif; ?>

	<?php edit_post_link(__('Edit Testimonial', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

</article>
<!-- / .post -->

<?php if ( 'slider' == $themify->post_layout ) : ?>
	</li>
<?php endif; ?>
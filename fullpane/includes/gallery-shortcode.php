<?php
/**
 * Partial template to render the gallery shortcode section type
 * Created by themify
 * @since 1.0.0
 */

global $themify, $themify_gallery;

$images = $themify_gallery->get_gallery_images();

if ( $images ) :

	/**
	 * @var array $gsops Slider parameters.
	 */
	$gsops = $themify_gallery->get_slider_params( get_the_ID() );

	// Count valid items
	$returned_items = count( $images );
	?>

<div class="gallery-shortcode-wrap twg-wrap twg-gallery-shortcode" data-bgmode="<?php echo $gsops['bgmode']; ?>">
	<div class="gallery-image-holder twg-holder">

		<div class="twg-loading themify-loading"></div>

		<div class="gallery-info twg-info">
			<div class="gallery-caption twg-caption">

			</div>
			<!-- /gallery-caption -->
		</div>
	</div>

	<div id="gallery-shortcode-slider-<?php echo $themify->gallery_shortcode_slider_id; ?>" class="gallery-slider-wrap twg-controls">

		<?php if ( 'yes' != themify_get( 'hide_timer' ) && 'off' != $gsops['autoplay'] ) : ?>
			<div class="gallery-slider-timer">
				<div class="timer-bar"></div>
			</div>
			<!-- /gallery-slider-timer -->
		<?php endif; ?>

		<ul class="gallery-slider-thumbs slideshow twg-list" data-id="gallery-shortcode-slider-<?php echo $themify->gallery_shortcode_slider_id; ?>" data-autoplay="<?php echo $gsops['autoplay']; ?>" data-effect="scroll" data-speed="<?php echo $gsops['transition']; ?>" data-visible="<?php echo $returned_items <= 12 ? $returned_items - 1: '12' ?>" data-width="100" data-wrap="yes" data-slidernav="yes" data-pager="no">
		<?php foreach ( $images as $image ) :
			$thumb = wp_get_attachment_image_src( $image->ID, 'thumbnail' );
			$full = wp_get_attachment_image_src( $image->ID, apply_filters( 'themify_gallery_shortcode_full_size', 'large' ) );
			$caption = $themify_gallery->get_caption( $image );
			$description = $themify_gallery->get_description( $image );
			?>
			<li class="twg-item">
				<a href="#" data-image="<?php echo $full[0]; ?>" data-caption="<?php echo $caption; ?>" data-description="<?php echo $description; ?>" class="twg-link">
					<img src="<?php echo $thumb[0]; ?>" alt="<?php echo $caption; ?>"/>
				</a>
			</li>
		<?php endforeach; // images as image ?>
		</ul>
	</div>
</div>
<!-- /.twg-wrap -->

<?php
// Increment instance for next gallery
$themify->gallery_shortcode_slider_id++;
endif; // images ?>
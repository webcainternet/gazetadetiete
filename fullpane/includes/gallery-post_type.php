<?php
/**
 * Partial template to render the gallery post_type section type
 * Created by themify
 * @since 1.0.0
 */

global $themify, $themify_gallery;

$galleries = $themify_gallery->get_gallery_posts();

if ( $galleries ) :

	/**
	 * @var array $gsops Slider parameters.
	 */
	$gsops = $themify_gallery->get_slider_params( get_the_ID() );

	// Count valid items
	$returned_items = 0;
	foreach ( $galleries as $gallery ) {
		if ( has_post_thumbnail( $gallery->ID ) ) {
			$returned_items ++;
		}
	}
	?>

	<!-- start gallery post type entry -->

	<div class="gallery-post_type-wrap twg-wrap twg-gallery-post_type" data-bgmode="<?php echo $gsops['bgmode']; ?>">
		<div class="gallery-image-holder twg-holder">

			<div class="twg-loading themify-loading"></div>

			<div class="gallery-info twg-info">
				<span class="post-category twg-terms"></span>

				<h2 class="gallery-title">
					<a href="#" class="twg-title"></a>
				</h2>

				<div class="separator clearfix"><div class="line"></div></div>

				<time class="gallery-date twg-date"></time>

				<div class="twg-caption"></div>

				<div class="twg-actions">
					<a href="#" class="shortcode button outline twg-primary-action"><?php _e( 'Launch Gallery', 'themify' ); ?></a>
				</div>
			</div>

		</div>
		<!-- / .gallery-image-holder -->

		<div id="gallery-post_type-slider-<?php echo $themify->gallery_post_type_slider_id; ?>" class="gallery-slider-wrap twg-controls">

			<?php if ( 'yes' != themify_get( 'hide_timer' ) && 'off' != $gsops['autoplay'] ) : ?>
				<div class="gallery-slider-timer">
					<div class="timer-bar"></div>
				</div>
				<!-- /gallery-slider-timer -->
			<?php endif; ?>

			<ul class="gallery-slider-thumbs slideshow twg-list" data-id="gallery-post_type-slider-<?php echo $themify->gallery_post_type_slider_id; ?>" data-autoplay="<?php echo $gsops['autoplay']; ?>" data-effect="scroll" data-speed="<?php echo $gsops['transition']; ?>" data-visible="<?php echo $returned_items <= 12 ? $returned_items - 1 : '12' ?>" data-width="100" data-wrap="yes" data-slidernav="yes" data-pager="no">

				<?php foreach ( $galleries as $gallery ) :
					$thumb = wp_get_attachment_image_src( get_post_thumbnail_id( $gallery->ID ), 'thumbnail' );
					$full = wp_get_attachment_image_src( get_post_thumbnail_id( $gallery->ID ), apply_filters( 'themify_gallery_post_type_full_size', 'large' ) );
					if ( isset( $thumb[0] ) && '' != $thumb[0] ) : ?>

					<li class="twg-item">
						<a href="#" data-entry_id="<?php echo $gallery->ID; ?>" data-image="<?php echo $full[0]; ?>" class="twg-link">
							<img src="<?php echo $thumb[0]; ?>" alt="<?php echo esc_attr( $gallery->post_title ); ?>"/>
						</a>
					</li>
					<?php endif; // image URL exists ?>
				<?php endforeach; // galleries as gallery ?>
			</ul>

		</div>
		<!-- /gallery-slider-wrap -->

	</div>
	<!-- /.twg-wrap -->

	<!-- end gallery post type entry -->

<?php
// Increment instance for next gallery
$themify->gallery_post_type_slider_id++;
endif; // galleries ?>
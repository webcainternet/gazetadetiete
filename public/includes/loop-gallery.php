<?php
/**
 * Template for gallery post type display.
 * @package themify
 * @since 1.0.0
 */

/** Themify Default Variables
 *  @var object */
global $themify, $themify_gallery;

?>

<?php themify_post_before(); // hook ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'gallery-post' ); ?>>

	<?php themify_post_start(); // hook ?>

	<?php themify_before_post_title(); // Hook ?>

		<h1 class="post-title"><?php the_title(); ?></h1>

	<?php themify_after_post_title(); // Hook ?>

	<?php the_terms( get_the_ID(), 'gallery-category', '<span class="post-category">', ', ', '</span>' ); ?>

	<div class="gallery-wrapper clearfix gallery-type-gallery">

		<?php
		/**
		 * GALLERY TYPE: GALLERY
		 */
		if ( themify_get( 'gallery_shortcode' ) != '' ) : ?>

			<?php
			$images = $themify_gallery->get_gallery_images();
			if ( $images ) : $counter = 0; ?>

				<?php foreach ( $images as $image ) :
					$counter++;
					$full = wp_get_attachment_image_src( $image->ID, apply_filters( 'themify_gallery_post_type_single', 'large' ) );
					$caption = $themify_gallery->get_caption( $image );
					$description = $themify_gallery->get_description( $image );
					$featured = get_post_meta( $image->ID, 'themify_gallery_featured', true );
					if ( $featured && ( '' != $featured ) ) {
						$img_size = array(
							'width' => 500,
							'height' => 536,
						);
					} else {
						$img_size = array(
							'width' => 250,
							'height' => 268,
						);
					}
					?>
					<div class="item gallery-icon <?php echo $featured; ?>">
						<a href="<?php echo $full[0]; ?>" class="" data-image="<?php echo $full[0]; ?>" data-caption="<?php echo $caption; ?>" data-description="<?php echo $description; ?>">
							<?php themify_image( "src={$full[0]}&w={$img_size['width']}&h={$img_size['height']}&ignore=true&alt=" ); ?>
							<span><?php echo $caption; ?></span>
						</a>
					</div>
				<?php endforeach; // images as image ?>

			<?php endif; // images ?>

		<?php endif; // video section type ?>


	</div>

	<div class="gallery-content pagewidth">
		<div class="post clearfix">
			<div class="post-meta">
				<p class="post-author">
					<?php echo get_avatar( get_the_author_meta('user_email'), $themify->avatar_size, '' ); ?>
					<br/>
					<small><?php printf( __('<a href="%s">%s</a>', 'themify'),  get_author_posts_url( get_the_author_meta( 'ID' ) ), get_the_author_meta('display_name') ); ?></small>
				</p>
				<time class="post-date" datetime="<?php the_time('o-m-d') ?>"><?php the_time(apply_filters('themify_loop_date', 'M j, Y')) ?></time><br>
				<span class="post-comment"><?php comments_popup_link( __( '0 comments', 'themify' ), __( '1 comment', 'themify' ), __( '% comments', 'themify' ) ); ?></span><br/>

			</div>
			<div class="post-content">

				<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

			</div>
			<!-- /.post-content -->
		</div>
		<!-- / .post -->

		<?php wp_link_pages(array('before' => '<p><strong>' . __('Pages:', 'themify') . ' </strong>', 'after' => '</p>', 'next_or_number' => 'number')); ?>

		<?php get_template_part( 'includes/author-box', 'single'); ?>

		<?php get_template_part( 'includes/post-nav'); ?>

		<?php if(!themify_check('setting-comments_posts')): ?>
			<?php comments_template(); ?>
		<?php endif; ?>

	</div>

	<?php themify_post_end(); // hook ?>

</article>
<?php themify_post_after(); // hook ?>

<!-- /.post -->
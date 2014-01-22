<?php
/**
 * Template for team post type display.
 * @package themify
 * @since 1.0.0
 */
?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<?php

$link = themify_get_featured_image_link();
$before = '';
$after = '';
if ( $link != '' ) {
	$before = '<a href="' . $link . '" title="' . get_the_title() . '">';
	$zoom_icon = themify_zoom_icon(false);
	$after = $zoom_icon . '</a>' . $after;
	$zoom_icon = '';
}
// Save post id
$post_id = get_the_ID();

// Get team member skills
$skills = get_post_meta($post_id, 'skills', true );

// Get social links
$social = get_post_meta($post_id, 'social', true );

?>

<?php if ( 'slider' == $themify->post_layout ) :
	wp_enqueue_script('themify-carousel-js'); ?>
	<li>
<?php endif; ?>

<article id="team-<?php the_ID(); ?>" <?php post_class('post clearfix team-post ' . $themify->col_class); ?>>

	<div class="team-content-wrap clearfix">

		<div class="clearfix">

			<?php if ( 'yes' != $themify->hide_image ) : ?>
				<?php
				// Check if user wants to use a common dimension or those defined in each highlight
				if ( 'yes' == $themify->use_original_dimensions ) {
					// Save post id
					$post_id = get_the_ID();

					// Set image width
					$themify->width = get_post_meta($post_id, 'image_width', true);

					// Set image height
					$themify->height = get_post_meta($post_id, 'image_height', true);
				}
				?>
				<figure class="post-image">
					<?php if ( 'yes' != $themify->unlink_image ) : ?>
						<?php echo $before; ?>
						<?php themify_image('ignore=true&w='.$themify->width.'&h='.$themify->height); ?>
						<?php echo $after; ?>
					<?php else : ?>
						<?php themify_image('ignore=true&w='.$themify->width.'&h='.$themify->height); ?>
					<?php endif; // unlink image ?>
				</figure>
			<?php endif; // hide image ?>

			<div class="team-title-wrapper">
				<?php if('yes' != $themify->hide_title): ?>
					<h2 class="post-title">
						<?php if ( 'yes' != $themify->unlink_title ) : ?>
							<?php echo $before; ?>
							<?php the_title(); ?>
							<?php echo $after; ?>
						<?php else : ?>
							<?php the_title(); ?>
						<?php endif; ?>

						<?php if(themify_check('team_title')): ?>
							<span class="team-title"><?php echo themify_get('team_title'); ?></span>
						<?php endif; ?>
					</h2>
				<?php endif; // hide title ?>

				<?php if( $social ): ?>
					<p class="team-social">
						<?php echo do_shortcode( $social ); ?>
					</p>
					<!-- /.team-social -->
				<?php endif; ?>
			</div>

		</div>

		<div class="post-content">

			<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
				<?php the_excerpt(); ?>
			<?php elseif($themify->display_content == 'content'): ?>
				<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
			<?php endif; //display content ?>

		</div>
		<!-- /.post-content -->

	</div>
	<!-- /.team-content-wrap -->
	<div classs="clearfix"></div>

	<?php if( $skills ): ?>
		<div class="skillset-wrap">
			<h4><?php _e( 'Skills', 'themify' ); ?></h4>
			<div class="progress-bar-wrap">
				<?php echo do_shortcode( $skills ); ?>
			</div>
		</div>
		<!-- /.skillset -->
	<?php endif; ?>

	<?php edit_post_link(__('Edit Team', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

</article>
<!-- / .post -->

<?php if ( 'slider' == $themify->post_layout ) : ?>
	</li>
<?php endif; ?>
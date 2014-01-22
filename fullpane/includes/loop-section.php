<?php
/**
 * Template for section post type display.
 * @package themify
 * @since 1.0.0
 */
?>

<?php if(!is_single()){ global $more; $more = 0; } //enable more link ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify, $post, $themify_section;

$section_type = themify_check( 'section_type' ) ? themify_get( 'section_type' ) : 'standard';
$section_width = themify_check( 'section_width' ) ? themify_get( 'section_width' ) : '';
$section_type_class = 'gallery_posts' == $section_type ? 'gallery' : $section_type;
?>

<?php themify_post_before(); // hook ?>

<section id="<?php echo apply_filters('editable_slug', $post->post_name); ?>" <?php $themify_section->section_background('clearfix section-post ' . $section_type_class . ' ' . $section_width); ?>>
	
	<div class="section-inner">
		<?php themify_post_start(); // hook ?>
	
		<?php if ( ( themify_is_query_page() && $themify->hide_title != 'yes' ) && ( 'yes' != themify_get( 'hide_section_title' ) ) ): ?>
			<?php themify_before_post_title(); // Hook ?>

			<h2 class="section-title"><?php the_title(); ?></h2>

			<?php themify_after_post_title(); // Hook ?>
		<?php endif; //section title ?>
	
		<div class="section-content">

			<?php
			/**
			 * SECTION TYPE: VIDEO
			 */
			if ( 'video' == $section_type && themify_get( 'video_url' ) != '' ) : ?>

				<?php
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
				?>

			<?php endif; // video section type ?>

			<?php
			/**
			 * SECTION TYPE: GALLERY SHORTCODE
			 */
			if ( 'gallery' == $section_type && themify_get( 'gallery_shortcode' ) != '' ) : ?>

				<?php get_template_part( 'includes/gallery', 'shortcode' ); ?>

			<?php endif; // gallery section type ?>

			<?php
			/**
			 * SECTION TYPE: GALLERY POST TYPE
			 */
			if ( 'gallery_posts' == $section_type && themify_get( 'gallery_posts' ) != '' ) : ?>

				<?php get_template_part( 'includes/gallery', 'post_type' ); ?>

			<?php endif; // gallery post section type ?>

			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

			<?php edit_post_link(__('Edit Section', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

		</div>
		<!-- /.section-content -->

		<?php themify_post_end(); // hook ?>
	</div> <!-- /.section-inner -->
	
</section>
<?php themify_post_after(); // hook ?>
<!-- /.section-post -->
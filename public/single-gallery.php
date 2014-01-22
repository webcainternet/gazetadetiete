<?php
/**
 * Template for single gallery post view
 * @package themify
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<!-- layout -->
<div id="layout" class="clearfix">

	<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<?php themify_content_before(); // hook ?>

		<!-- content -->
		<div id="content" class="list-post">

			<?php themify_content_start(); // hook ?>

			<?php get_template_part( 'includes/loop-gallery' , 'single'); ?>

			<?php themify_content_end(); // hook ?>

		</div>
		<!-- /content -->

		<?php themify_content_after(); // hook ?>

	<?php endwhile; ?>

</div>
<!-- /layout -->

<?php get_footer(); ?>
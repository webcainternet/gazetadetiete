<?php
/**
 * Template for generic post display.
 * @package themify
 * @since 1.0.0
 */
?>
<?php if(!is_single()){ global $more; $more = 0; } //enable more link ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify;
?>

<?php themify_post_before(); // hook ?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post clearfix' ); ?>>
	
	<?php themify_post_start(); // hook ?>

	<?php if ( $themify->hide_image != 'yes' ) : ?>

		<?php themify_before_post_image(); // Hook ?>

		<?php if ( themify_get( 'video_url' ) != '' ) : ?>

			<?php
				global $wp_embed;
				echo $wp_embed->run_shortcode('[embed]' . themify_get('video_url') . '[/embed]');
			?>

		<?php elseif( $post_image = themify_get_image($themify->auto_featured_image . $themify->image_setting . "w=".$themify->width."&h=".$themify->height) ) : ?>

			<figure class="post-image <?php echo $themify->image_align; ?>">
				<?php if( 'yes' == $themify->unlink_image): ?>
					<?php echo $post_image; ?>
				<?php else: ?>
					<a href="<?php echo themify_get_featured_image_link(); ?>"><?php echo $post_image; ?><?php themify_zoom_icon(); ?></a>
				<?php endif; // unlink image ?>
			</figure>

		<?php endif; // video else image ?>

		<?php themify_after_post_image(); // Hook ?>

	<?php endif; // hide image ?>

	<div class="post-content">

		<?php if($themify->hide_title != 'yes'): ?>
			<?php themify_before_post_title(); // Hook ?>

			<h2 class="post-title">
				<?php if($themify->unlink_title == 'yes'): ?>
					<?php the_title(); ?>
				<?php else: ?>
					<a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
				<?php endif; //unlink post title ?>
			</h2>

			<?php themify_after_post_title(); // Hook ?>
		<?php endif; //post title ?>

		<?php if($themify->hide_meta != 'yes'): ?>
			<div class="post-meta">
	
				<?php if($themify->hide_meta_author != 'yes'): ?>
					<p class="post-author">
						<?php echo get_avatar( get_the_author_meta('user_email'), $themify->avatar_size, '' ); ?>
						<small><?php printf( __('<a href="%s">%s</a>', 'themify'),  get_author_posts_url( get_the_author_meta( 'ID' ) ), get_the_author_meta('display_name') ); ?></small>
					</p>
				<?php endif; ?>
	
				<?php if($themify->hide_date != 'yes'): ?>
					<time datetime="<?php the_time('o-m-d') ?>" class="post-date"><?php the_time(apply_filters('themify_loop_date', 'M j, Y')) ?></time>
				<?php endif; //post date ?>
	
				<?php  if( !themify_get('setting-comments_posts') && comments_open() && $themify->hide_meta_comment != 'yes' ) : ?>
					<span class="post-comment"><?php comments_popup_link( __( '0 comments', 'themify' ), __( '1 comment', 'themify' ), __( '% comments', 'themify' ) ); ?></span>
				<?php endif; ?>
	
				<?php if($themify->hide_meta_category != 'yes'): ?>
					<?php echo get_the_term_list( get_the_ID(), 'category', ' <span class="post-category">', ', ', '</span>' ); ?>
				<?php endif; ?>

				<?php if($themify->hide_meta_tag != 'yes'): ?>
					<?php the_tags('<span class="post-tag">', ', ', '</span>'); ?>
				<?php endif; ?>
	
			</div>
		<?php endif; //post meta ?>
		
		<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
	
			<?php the_excerpt(); ?>

			<?php if( themify_check('setting-excerpt_more') ) : ?>
				<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>" class="more-link"><?php echo themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify') ?></a><p>
			<?php endif; ?>
	
		<?php elseif ( 'none' == $themify->display_content && ! is_attachment() ) : ?>
	
		<?php else: ?>

			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
		
		<?php endif; //display content ?>
		
		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>
		
	</div>
	<!-- /.post-content -->
	<?php themify_post_end(); // hook ?>
	
</article>
<?php themify_post_after(); // hook ?>

<!-- /.post -->
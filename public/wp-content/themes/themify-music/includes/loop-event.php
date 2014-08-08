<?php
/**
 * Template for event post type display.
 * @package themify
 * @since 1.0.0
 */
?>
<?php if(!is_single()){ global $more; $more = 0; } //enable more link ?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify, $themify_event; ?>

<?php themify_post_before(); // hook ?>

<article itemscope itemtype="http://schema.org/Article" id="post-<?php the_ID(); ?>" <?php post_class('post clearfix event-post'); ?>>

	<?php themify_post_start(); // hook ?>

	<?php if ( ! is_singular( 'event' ) || ( isset( $themify->is_event_widget ) && $themify->is_event_widget ) ) : ?>

		<?php get_template_part( 'includes/post-media',	'event' ); ?>

		<div class="post-meta entry-meta clearfix">

			<?php if ( themify_check( 'start_date' ) && ( isset( $themify->hide_event_date ) && $themify->hide_event_date != 'yes' ) ) : ?>
				<time class="post-date entry-date updated" itemprop="datePublished">
					<?php $start_date = themify_get( 'start_date' ); ?>
					<span class="day"><?php echo $themify_event->get_datetime( $start_date, 'day' ); ?></span>
					<span class="month"><?php echo $themify_event->get_datetime( $start_date, 'month' ); ?></span>
					<span class="year"><?php echo $themify_event->get_datetime( $start_date, 'year' ); ?></span>
				</time>
				<!-- / .post-date -->
			<?php endif; ?>

		</div>
		<!-- /post-meta -->

	<?php endif; // is not single ?>

	<div class="post-content">

		<?php if ( ! is_singular( 'event' ) || ( isset( $themify->is_event_widget ) && $themify->is_event_widget ) ) : ?>
			<?php if ( $themify->hide_title != 'yes' ): ?>
				<?php themify_before_post_title(); // Hook ?>
				<?php if ( $themify->unlink_title == 'yes' ): ?>
					<h2 class="post-title entry-title" itemprop="name"><?php the_title(); ?></h2>
				<?php else: ?>
					<h2 class="post-title entry-title" itemprop="name">
						<a href="<?php echo themify_get_featured_image_link(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
					</h2>
				<?php endif; //unlink post title ?>
				<?php themify_after_post_title(); // Hook ?>
			<?php endif; //post title ?>

			<div class="<?php echo is_singular( 'event' ) ? 'event-single-wrap clearfix' : 'event-info'; ?>">

				<?php if ( themify_check( 'location' ) && ( isset( $themify->hide_event_location ) && $themify->hide_event_location != 'yes' ) ) : ?>
					<span class="location">
						<?php echo themify_get( 'location' ); ?>
					</span>
				<?php endif; ?>

				<?php if ( themify_check( 'start_date' ) && ( isset( $themify->hide_event_date ) && $themify->hide_event_date != 'yes' ) ) : ?>
					<span class="event-time"><?php echo $themify_event->get_datetime( $start_date, 'time' ); ?></span>
				<?php endif; ?>

			</div><!-- /.event-info -->

			<div class="event-cta-wrapper">
				<?php get_template_part( 'includes/social-share' ); ?>
				<?php if ( themify_check( 'buy_tickets' ) ) : ?>
					<a href="<?php echo themify_get( 'buy_tickets' ); ?>" class="buy-button">
						<span class="ticket"></span> <?php _e( 'Buy Tickets', 'themify' ); ?>
					</a>
				<?php endif; ?>
			</div>
			<!-- event-cta-wrapper -->

		<?php endif; // is not single ?>

		<div class="entry-content" itemprop="articleBody">

			<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>

				<?php the_excerpt(); ?>

				<?php if( themify_check('setting-excerpt_more') ) : ?>
					<p><a href="<?php the_permalink(); ?>" title="<?php the_title_attribute('echo=0'); ?>" class="more-link"><?php echo themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify') ?></a></p>
				<?php endif; ?>

			<?php elseif($themify->display_content == 'none'): ?>

			<?php else: ?>

				<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>

			<?php endif; //display content ?>

		</div><!-- /.entry-content -->

		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>

	</div>
	<!-- /.post-content -->
	<?php themify_post_end(); // hook ?>

</article>
<?php themify_post_after(); // hook ?>

<!-- /.post -->
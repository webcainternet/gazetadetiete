<?php
/**
 * Template for single post view
 * @package themify
 * @since 1.0.0
 */
?>

<?php get_header(); ?>

<?php 
/** Themify Default Variables
 *  @var object */
global $themify, $themify_event;
?>

<?php if( have_posts() ) while ( have_posts() ) : the_post(); ?>

	<!-- layout-container -->
	<div id="layout" class="pagewidth clearfix">

		<?php if ( is_single() ) : ?>
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
		<?php endif; // is single ?>

		<div class="event-single-wrap clearfix">

			<div class="event-map">
				<?php echo $themify_event->get_map( themify_get( 'map_address' ) ); ?>
			</div><!-- /.event-map -->

			<div class="event-single-details">

				<?php get_template_part( 'includes/post-media',	'event' ); ?>

				<div class="event-info-wrap">

					<?php if ( themify_check( 'start_date' ) ) : ?>
						<time class="post-date entry-date updated" itemprop="datePublished">
							<?php $start_date = themify_get( 'start_date' ); ?>
							<span class="day"><?php echo $themify_event->get_datetime( $start_date, 'day' ); ?></span>
							<span class="month"><?php echo $themify_event->get_datetime( $start_date, 'month' ); ?></span>
							<span class="time"><?php echo $themify_event->get_datetime( $start_date, 'time' ); ?></span>
						</time>
						<!-- / .post-date -->
					<?php endif; ?>

					<?php if ( themify_check( 'location' ) ) : ?>
						<span class="location">
								<?php echo themify_get( 'location' ); ?>
							</span>
					<?php endif; ?>

					<?php if ( is_singular( 'event' ) && themify_check( 'map_address' ) ) : ?>
						<div class="address">
							<?php echo wpautop( themify_get( 'map_address' ) ); ?>
						</div>
						<!-- /address -->
					<?php endif; ?>

				</div>
				<!-- / .event-info-wrap -->

				<div class="event-cta-wrapper clearfix">

					<?php get_template_part( 'includes/social-share' ); ?>
					<?php if ( themify_check( 'buy_tickets' ) ) : ?>
						<a href="<?php echo themify_get( 'buy_tickets' ); ?>" class="buy-button">
							<span class="ticket"></span> <?php _e( 'Buy Tickets', 'themify' ); ?>
						</a>
					<?php endif; ?>

				</div><!-- event-cta-wrapper -->

			</div><!-- / .event-single-details -->
		</div>

		<?php themify_content_before(); // hook ?>
		<!-- content -->
		<div id="content" class="list-post">
			<?php themify_content_start(); // hook ?>

			<?php get_template_part( 'includes/loop', get_post_type()); ?>

			<?php wp_link_pages( array( 'before' => '<p><strong>' . __( 'Pages:', 'themify' ) . ' </strong>', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>

			<?php get_template_part( 'includes/author-box', 'single' ); ?>

			<?php get_template_part( 'includes/post-nav' ); ?>

			<?php comments_template(); ?>

			<?php themify_content_end(); // hook ?>
		</div>
		<!-- /content -->
		<?php themify_content_after(); // hook ?>

		<?php
		/////////////////////////////////////////////
		// Sidebar
		/////////////////////////////////////////////
		if ($themify->layout != "sidebar-none"): get_sidebar(); endif; ?>

	</div>
	<!-- /layout-container -->

<?php endwhile; ?>

<?php get_footer(); ?>
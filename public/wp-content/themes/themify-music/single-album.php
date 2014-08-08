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
global $themify;
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

		<div class="album-container clearfix">

			<?php if ( isset( $_GET ) && isset( $_GET['post_in_lightbox'] ) && '1' == $_GET['post_in_lightbox'] ) : ?>
				<i class="close-lightbox ti-close"></i>
			<?php endif; ?>

			<div class="album-cover">
				<?php get_template_part( 'includes/post-media', get_post_type() ); ?>

				<?php get_template_part( 'includes/social-share' ); ?>

				<?php if ( $buy_album = themify_get( 'buy_album' ) ) : ?>
					<a href="<?php echo esc_url( $buy_album ); ?>" class="buy-button"><?php _e( 'Buy Album', 'themify' ); ?></a>
				<?php endif; // buy album ?>

			</div>
			<!-- /album cover -->

			<div class="album-info">

				<ul class="record-details">
					<?php if ( $artist = themify_get( 'artist' ) ) : ?>
						<li>
							<h6 class="record-artist"><?php _e( 'Artist', 'themify' ); ?></h6>
							<p class="record-artist" itemprop="byArtist"><?php echo $artist; ?></p>
						</li>
					<?php endif; // artist?>
					<?php if ( $released = themify_get( 'released' ) ) : ?>
					<li>
						<h6 class="record-release"><?php _e( 'Released', 'themify' ); ?></h6>
						<p class="record-release" itemprop="dateCreated"><?php echo $released; ?></p>
					</li>
					<?php endif; // released ?>
					<?php if ( $genre = themify_get( 'genre' ) ) : ?>
					<li>
						<h6 class="record-genre"><?php _e( 'Genre', 'themify' ); ?></h6>
						<p class="record-genre" itemprop="genre"><?php echo $genre; ?></p>
					</li>
					<?php endif; // genre ?>
				</ul>

			</div>
			<!-- /album-info -->

			<div class="album-playlist">
				<div class="jukebox">
					<ol class="tracklist">
						<?php
						$playlist = '';
						for ( $track = 1; $track <= apply_filters( 'themify_theme_number_of_tracks', 18 ); $track++ ) {
							$this_track = '';
							$this_track_name = '';
							if ( $track_name = themify_get( 'track_name_' . $track ) ) {
								$this_track .= 'title="' . $track_name . '" ';
								$this_track_name = $track_name;
							}

							$track_src = themify_get( 'track_file_' . $track );
							if ( '' != $track_src ) {
								$this_track .= 'src="' . $track_src . '"';
							}

							if ( '' != $this_track ) {
								$playlist .= '<li class="track is-playable" itemprop="track" itemscope="" itemtype="http://schema.org/MusicRecording"><a class="track-title" href="#" itemprop="url"><span itemprop="name">' . $this_track_name . '</span></a>' . '[audio ' . $this_track . ']</li>';
							}
						}
						echo do_shortcode( $playlist );
						?>
					</ol>
				</div>
				<!-- /jukebox -->
			</div>
			<!-- /album-playlist -->

		</div>

		<?php themify_content_before(); // hook ?>
		<!-- content -->
		<div id="content">
			<?php themify_content_start(); // hook ?>

			<?php get_template_part( 'includes/loop', get_post_type() ); ?>

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
		if ( $themify->layout != 'sidebar-none' ): get_sidebar(); endif; ?>

	</div>
	<!-- /layout-container -->

<?php endwhile; ?>

<?php get_footer(); ?>
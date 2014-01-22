<?php
/**
 * Template for highlight post type display.
 * @package themify
 * @since 1.0.0
 */
?>

<?php
/** Themify Default Variables
 *  @var object */
global $themify; ?>

<article id="highlight-<?php the_ID(); ?>" <?php post_class('post clearfix highlight-post'); ?>>

	<?php
	$link = themify_get_featured_image_link('no_permalink=true');
	$before = '';
	$after = '';
	if ($link != '') {
		$before = '<a href="' . $link . '" title="' . get_the_title() . '">';
		$zoom_icon = themify_zoom_icon(false);
		$after = $zoom_icon . '</a>' . $after;
		$zoom_icon = '';
	}

	// Chart color

	// Save post id
	$post_id = get_the_ID();

	$bar_percentage = get_post_meta($post_id, 'bar_percentage', true);
	if ( $bar_percentage ) {
		// Key in theme settings
		$bar_color_key = 'styling-backgrounds-chart_bar_color-background_color-value-value';
		$bar_color = get_post_meta($post_id, 'bar_color', true);
		if( ! $bar_color ) {
			$bar_color = themify_check($bar_color_key)? apply_filters('themify_chart_bar_color', themify_get($bar_color_key)): '#22d9e5';
		}
		$chart_before = sprintf( '<div class="chart chart-%s" data-percent="%s" data-color="%s">',
			$post_id, $bar_percentage, $bar_color
		);
		$chart_after = '</div><!-- /.chart -->';
		
		// echo the same color same as highlight bar for the icon
		echo sprintf( '<style>.post-%s .chart .fa {color:%s;}</style>', $post_id, '#' . $bar_color );
	} else {
		$chart_before = '';
		$chart_after = '';
	}
	?>
	<?php if('no' != $themify->hide_image): ?>
		<figure class="post-image">
			<?php echo $before . $chart_before; ?>
			<?php
			$fa_icon = get_post_meta($post_id, 'icon', true);
			// If there's an icon set, show it instead of the featured image
			if ( $fa_icon ) : ?>
				<i class="fa <?php echo $fa_icon; ?>"></i>
			<?php else:
				// Check if user wants to use a common dimension or those defined in each highlight
				if ('yes' == $themify->use_original_dimensions) {
					// Save post id
					$post_id = get_the_ID();

					// Set image width
					$themify->width = get_post_meta($post_id, 'image_width', true);

					// Set image height
					$themify->height = get_post_meta($post_id, 'image_height', true);
				}

				themify_image('ignore=true&w='.$themify->width.'&h='.$themify->height);
			endif;
			?>
			<?php echo $after . $chart_after; ?>
		</figure>
	<?php endif; // hide image ?>

	<div class="post-content">
		<?php if('no' != $themify->hide_title): ?>
			<h2 class="post-title"><?php echo $before; ?><?php the_title(); ?><?php echo $after; ?></h2>
		<?php endif; // hide title ?>
		<?php if ( 'excerpt' == $themify->display_content && ! is_attachment() ) : ?>
			<?php the_excerpt(); ?>
		<?php elseif($themify->display_content == 'content'): ?>
			<?php the_content(themify_check('setting-default_more_text')? themify_get('setting-default_more_text') : __('More &rarr;', 'themify')); ?>
		<?php endif; //display content ?>
		<?php edit_post_link(__('Edit', 'themify'), '<span class="edit-button">[', ']</span>'); ?>
	</div>

</article>
<!-- / .post -->
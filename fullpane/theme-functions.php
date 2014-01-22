<?php
/***************************************************************************
 *  					Theme Functions
 * 	----------------------------------------------------------------------
 * 						DO NOT EDIT THIS FILE
 *	----------------------------------------------------------------------
 * 
 *  					Copyright (C) Themify
 * 						http://themify.me
 *
 *  To add custom PHP functions to the theme, create a new 'custom-functions.php' file in the theme folder.
 *  They will be added to the theme automatically.
 * 
 ***************************************************************************/

/////// Actions ////////
// Enqueue scripts and styles required by theme
add_action( 'wp_enqueue_scripts', 'themify_theme_enqueue_scripts');

// Browser compatibility
add_action( 'wp_head', 'themify_ie_enhancements' );
add_action( 'wp_head', 'themify_viewport_tag' );
add_action( 'wp_head', 'themify_ie_standards_compliant');

// Register custom menu
add_action( 'init', 'themify_register_custom_nav');

// Register sidebars
add_action( 'widgets_init', 'themify_theme_register_sidebars' );

/**
 * Enqueue Stylesheets and Scripts
 * @since 1.0.0
 */
function themify_theme_enqueue_scripts(){

	// Get theme version for Themify theme scripts and styles
	$theme_version = wp_get_theme()->display('Version');

	// Check scrolling effect
	$scrollingEffect = themify_is_transition_active();

	// Check fullpage scrolling
	$is_fullpage_scroll = 'no' !== themify_get( 'section_full_scrolling' ) && '' != themify_get('section_query_category') ? true : false;
	
	///////////////////
	//Enqueue styles
	///////////////////
	
	// Themify base styling
	wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array(), $theme_version);

	if ( themify_is_woocommerce_active() ) {
		//Themify shop stylesheet
		wp_enqueue_style( 'themify-shop', THEME_URI . '/shop.css');
	}

	// Themify Media Queries CSS
	wp_enqueue_style( 'themify-media-queries', THEME_URI . '/media-queries.css', array(), $theme_version);
	
	// User stylesheet
	if(is_file(TEMPLATEPATH . '/custom_style.css'))
		wp_enqueue_style( 'custom-style', THEME_URI . '/custom_style.css');
		
	// Google Web Fonts embedding
	wp_enqueue_style( 'google-fonts', themify_https_esc('http://fonts.googleapis.com/css'). '?family=Roboto:400,100,300,700|Roboto+Condensed:400,300,700');

	// Font Awesome
	wp_enqueue_style( 'themify-fontawesome', THEMIFY_URI . '/fontawesome/css/font-awesome.min.css', array(), $theme_version);

	// Fontello
	wp_enqueue_style( 'themify-fontello', THEME_URI . '/fontello/css/fontello.css', array(), $theme_version);

	///////////////////
	//Enqueue scripts
	///////////////////

	// Slide mobile navigation menu
	wp_enqueue_script( 'slide-nav', THEME_URI . '/js/jquery.sidr.js', array('jquery'), $theme_version, true );

    // Excanvas
	wp_enqueue_script( 'themify-excanvas', THEME_URI . '/js/excanvas.js', array(), $theme_version, true );

	// Easy pie chart
	wp_enqueue_script( 'themify-easy-pie-chart', THEME_URI . '/js/jquery.easy-pie-chart.js', array('jquery'), $theme_version, true );

	// Backstretch
	wp_enqueue_script( 'themify-backstretch', THEME_URI.'/js/backstretch.js', array('jquery'), $theme_version, true );

	// Smart Resize for events debouncedresize and throttledresize
	wp_enqueue_script( 'themify-smartresize', THEME_URI.'/js/jquery.smartresize.js', array('jquery'), $theme_version, true );

	// Gallery plugin
	wp_enqueue_script( 'themify-widegallery', THEME_URI . '/js/themify.widegallery.js', array('jquery', 'themify-smartresize'), $theme_version, true );

	// Modernizr
	wp_enqueue_script( 'themify-modernizr', THEME_URI . '/js/modernizr-2.5.3.min.js', array(), $theme_version, true );

	// Masonry
	wp_enqueue_script( 'themify-masonry', THEME_URI . '/js/jquery.masonry.min.js', array(), $theme_version, true );

	// One Page Scroll
	wp_enqueue_script( 'jquery-effects-core' );
	if ( $is_fullpage_scroll ) {
		wp_enqueue_script( 'themify-slimscroll', THEME_URI . '/js/jquery.slimscroll.min.js', array(), $theme_version, true );
		wp_enqueue_script( 'themify-fullpage', THEME_URI . '/js/jquery.fullPage.js', array( 'jquery', 'jquery-effects-core' ), $theme_version, true );
	} else if ( $scrollingEffect ) {
		wp_enqueue_script( 'themify-waypoints', THEME_URI . '/js/waypoints.min.js', array('jquery'), $theme_version, true );	
	}

	// Portfolio Expander
	wp_enqueue_script( 'themify-portfolio-expander', THEME_URI . '/js/themify.portfolio-expander.js', array( 'jquery', 'jquery-effects-core' ), $theme_version, true );

	// Themify internal scripts
	wp_enqueue_script( 'theme-script', THEME_URI . '/js/themify.script.js', array('jquery'), $theme_version, true );

	// Themify gallery
	wp_enqueue_script( 'themify-gallery', THEMIFY_URI . '/js/themify.gallery.js', array('jquery'), $theme_version, true );
	
	// Inject variable values in gallery script
	wp_localize_script( 'theme-script', 'themifyScript', array(
		'lightbox' => themify_lightbox_vars_init(),
		'lightboxContext' => apply_filters('themify_lightbox_context', '#pagewrap'),
		'chart' => apply_filters('themify_chart_init_vars', array(
			'trackColor' => 'rgba(0,0,0,.1)',
			'scaleColor' => false,
			'lineCap' => 'butt',
			'rotate' => 0,
			'size' => 175,
			'lineWidth' => 3,
			'animate' => 2000
		)),
		'scrollingEffectOn' => $scrollingEffect,
		'ajax_nonce'	=> wp_create_nonce('ajax_nonce'),
		'ajax_url'		=> admin_url( 'admin-ajax.php' ),
		'networkError'	=> __('Unknown network error. Please try again later.', 'themify'),
		'termSeparator'	=> ', ',
		'galleryFadeSpeed' => '300',
		'galleryEvent' => 'click',
		'fullPageScroll' => $is_fullpage_scroll,
		'transition' => apply_filters( 'themify_transition_localize_script', array(
			'effect' => themify_check( 'setting-transition_effect_fadein' ) ? 'fade-in' : 'fly-in',
			'selectors' => array(
				'columns' => themify_remove_rn_chars( '.col4-1, .col4-2, .col4-3, .col3-1, .col3-2, .col2-1, .col-full' ),
				'postItems' => themify_remove_rn_chars( '.shortcode.portfolio:not(.slider), .shortcode.team:not(.slider), .shortcode.highlight:not(.slider), .shortcode.testimonial:not(.slider), .shortcode.list-posts:not(.slider), .module-testimonial, .module-post, .module-portfolio, .module-highlight')
			)
		))
	));
	
	// WordPress internal script to move the comment box to the right place when replying to a user
	if ( is_single() || is_page() ) wp_enqueue_script( 'comment-reply' );
}

/**
 * Add JavaScript files if IE version is lower than 9
 * @since 1.0.0
 */
function themify_ie_enhancements(){
	echo "\n".'
	<!-- media-queries.js -->
	<!--[if lt IE 9]>
		<script src="' . THEME_URI . '/js/respond.js"></script>
	<![endif]-->
	
	<!-- html5.js -->
	<!--[if lt IE 9]>
		<script src="'.themify_https_esc('http://html5shim.googlecode.com/svn/trunk/html5.js').'"></script>
	<![endif]-->
	'."\n";
}

/**
 * Add viewport tag for responsive layouts
 * @since 1.0.0
 */
function themify_viewport_tag(){
	echo "\n".'<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no">'."\n";
}

/**
 * Make IE behave like a standards-compliant browser
 * @since 1.0.0 
 */
function themify_ie_standards_compliant() {
	echo "\n".'
	<!--[if lt IE 9]>
	<script src="'.themify_https_esc('http://s3.amazonaws.com/nwapi/nwmatcher/nwmatcher-1.2.5-min.js').'"></script>
	<script type="text/javascript" src="'.themify_https_esc('http://cdnjs.cloudflare.com/ajax/libs/selectivizr/1.0.2/selectivizr-min.js').'"></script> 
	<![endif]-->
	'."\n";
}

/* Custom Write Panels
/***************************************************************************/

///////////////////////////////////////
// Build Write Panels
///////////////////////////////////////

if ( ! function_exists( 'themify_theme_init_types' ) ) {
	/**
	 * Initialize custom panel with its definitions
	 * Custom panel definitions are located in admin/post-type-TYPE.php
	 * @since 1.0.7
	 */
	function themify_theme_init_types() {
		// Load required files for post, page and custom post types where it applies
		foreach ( array( 'post', 'page', 'highlight', 'team', 'portfolio', 'section', 'testimonial', 'gallery' ) as $type ) {
			require_once( "admin/post-type-$type.php" );
		}
		/**
		 * Navigation menus used in page custom panel to specify a custom menu for the page.
		 * @var array
		 * @since 1.2.7
		 */
		$nav_menus = array(	array( 'name' => '', 'value' => '', 'selected' => true ) );
		foreach ( get_terms( 'nav_menu' ) as $menu ) {
			$nav_menus[] = array( 'name' => $menu->name, 'value' => $menu->term_id );
		}

		themify_build_write_panels( apply_filters('themify_theme_meta_boxes',
			array(
				array(
					'name'		=> __('Post Options', 'themify'),
					'id' 		=> 'post-options',
					'options'	=> themify_theme_post_meta_box(),
					'pages'		=> 'post'
				),
				array(
					'name'		=> __('Page Options', 'themify'),
					'id' 		=> 'page-options',
					'options'	=> themify_theme_page_meta_box( array( 'nav_menus' => $nav_menus ) ),
					'pages'		=> 'page'
				),
				array(
					"name"		=> __('Query Posts', 'themify'),
					'id'		=> 'query-posts',
					"options"	=> themify_theme_query_post_meta_box(),
					"pages"		=> "page"
				),
				array(
					"name"		=> __('Query Sections', 'themify'),
					'id' 		=> 'query-section',
					"options"	=> themify_theme_query_section_meta_box(),
					"pages"		=> "page"
				),
				array(
					"name"		=> __('Query Portfolios', 'themify'),
					'id' 		=> 'query-portfolio',
					"options"	=> themify_theme_query_portfolio_meta_box(),
					"pages"		=> "page"
				),
				array(
					'name'		=> __('Section Options', 'themify'),
					'id' 		=> 'section-options',
					'options'	=> themify_theme_section_meta_box(),
					'pages'		=> 'section'
				),
				array(
					'name'		=> __('Portfolio Options', 'themify'),
					'id' 		=> 'portfolio-options',
					'options' 	=> themify_theme_portfolio_meta_box(),
					'pages'		=> 'portfolio'
				),
				array(
					'name'		=> __('Team Options', 'themify'),
					'id' 		=> 'team-options',
					'options' 	=> themify_theme_team_meta_box(),
					'pages'		=> 'team'
				),
				array(
					'name'		=> __('Highlight Options', 'themify'),
					'id' 		=> 'highlight-options',
					'options' 	=> themify_theme_highlight_meta_box(),
					'pages'		=> 'highlight'
				),
				array(
					'name'		=> __('Testimonial Options', 'themify'),
					'id' 		=> 'testimonial-options',
					'options' 	=> themify_theme_testimonial_meta_box(),
					'pages'		=> 'testimonial'
				),
				array(
					'name'		=> __('Gallery Options', 'themify'),
					'id' 		=> 'gallery-options',
					'options' 	=> themify_theme_gallery_meta_box(),
					'pages'		=> 'gallery'
				),
			)
		));
	}
}
add_action( 'after_setup_theme', 'themify_theme_init_types' );

///////////////////////////////////////
// Enable WordPress feature image
///////////////////////////////////////
add_theme_support( 'post-thumbnails' );
remove_post_type_support( 'page', 'thumbnail' );
	
/**
 * Register Custom Menu Function
 * @since 1.0.0
 */
function themify_register_custom_nav() {
	register_nav_menus( array(
		'main-nav' => __( 'Main Navigation', 'themify' )
	));
}

/**
 * Default Main Nav Function
 * @since 1.0.0
 */
function themify_default_main_nav() {
	echo '<ul id="main-nav" class="main-nav clearfix">';
		wp_list_pages('title_li=');
	echo '</ul>';
}

/**
 * Sets custom menu selected in page custom panel as navigation, otherwise sets the default.
 * @since 1.2.7
 */
function themify_theme_menu_nav() {
	$custom_menu = themify_get( 'custom_menu' );
	if ( isset( $custom_menu ) && '' != $custom_menu ) {
		wp_nav_menu( array( 'menu' => $custom_menu, 'fallback_cb' => 'themify_default_main_nav' , 'container'  => '' , 'menu_id' => 'main-nav' , 'menu_class' => 'main-nav' ) );
	} else {
		wp_nav_menu( array( 'theme_location' => 'main-nav' , 'fallback_cb' => 'themify_default_main_nav' , 'container'  => '' , 'menu_id' => 'main-nav' , 'menu_class' => 'main-nav' ) );
	}
}

/**
 * Checks if the browser is a mobile device
 * @return boolean 
 */
function themify_is_mobile(){
	return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

/**
 * Check whether element transition effect is ON or Off
 * @return boolean
 */
function themify_is_transition_active() {
	// check if mobile exclude disabled OR disabled all transition
	if ( ( themify_check('setting-transition_effect_mobile_exclude') && themify_is_mobile() ) 
		|| themify_check('setting-transition_effect_all_disabled') ) {
		return false;
	} else {
		return true;
	}
}

/**
 * Add class and data attribute to portfolio section
 * @param sting $link
 * @return string
 */
add_filter( 'themify_get_featured_image_link', 'themify_theme_porto_expand_link' );
function themify_theme_porto_expand_link( $link ) {
	global $post;
	if( ! is_singular( 'portfolio' ) && get_post_type() == 'portfolio' 
		&& themify_get('external_link') == '' && themify_get('lightbox_link') == '' ) {
		$link .= '" class="porto-expand-js';
	}
	return $link;
}

/**
 * Expand portfolio content with ajax
 * @return void
 */
add_action( 'template_redirect', 'themify_theme_porto_expand_content', 20 );
function themify_theme_porto_expand_content() {
	if( isset($_GET['porto_expand']) && $_GET['porto_expand'] == 1 ) {
		header("HTTP/1.1 200 OK");
		$out = '';
			ob_start();
			get_template_part('single', 'portfolio-expanded');
		$out .= ob_get_clean();
		echo $out;
		exit;
	}
}

add_filter( 'body_class', 'add_transition_effect_body_class' );
function add_transition_effect_body_class( $classes ) {
	$scrollingEffect = themify_is_transition_active();
	if ( $scrollingEffect ) $classes[] = 'transition-active';
	return $classes;
}

/**
 * Remove new line character in strings
 * @param $string
 * @return string
 */
function themify_remove_rn_chars($string) {
	return preg_replace('/^\s+|\n|\r|\s+$/m', '', $string);
}

/**
 * Register sidebars
 * @since 1.0.0
 */
function themify_theme_register_sidebars() {
	$sidebars = array(
		array(
			'name' => __('Sidebar', 'themify'),
			'id' => 'sidebar-main',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		),
		array(
			'name' => __('Social Widget', 'themify'),
			'id' => 'social-widget',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<strong>',
			'after_title' => '</strong>',
		)
	);
	foreach( $sidebars as $sidebar ) {
		register_sidebar( $sidebar );
	}

	// Footer Sidebars
	themify_register_grouped_widgets();
}

if ( ! function_exists('themify_theme_gallery_plugins_args') ) {
	/**
	 * Enable Timeline entries to launch lightbox/fullscreen galleries
	 * @param $args
	 * @return mixed
	 * @since 1.0.4
	 */
	function themify_theme_gallery_plugins_args($args) {
		$args['contentImagesAreas'] .= ', .section-post';
		return $args;
	}
	add_filter('themify_gallery_plugins_args', 'themify_theme_gallery_plugins_args');
}

if ( ! function_exists( 'themify_theme_default_social_links' ) ) {
	/**
	 * Replace default squared social link icons with circular versions
	 * @param $data
	 * @return mixed
	 * @since 1.0.0
	 */
	function themify_theme_default_social_links( $data ) {
		$pre = 'setting-link_img_themify-link-';
		$data[$pre.'0'] = THEME_URI . '/images/twitter.png';
		$data[$pre.'1'] = THEME_URI . '/images/facebook.png';
		$data[$pre.'2'] = THEME_URI . '/images/google-plus.png';
		$data[$pre.'3'] = THEME_URI . '/images/youtube.png';
		$data[$pre.'4'] = THEME_URI . '/images/pinterest.png';
		return $data;
	}
	add_filter( 'themify_default_social_links', 'themify_theme_default_social_links' );
}

if ( ! function_exists( 'themify_theme_custom_post_css' ) ) {
	/**
	 * Outputs custom post CSS at the end of a post
	 * @since 1.0.0
	 */
	function themify_theme_custom_post_css() {
		global $themify;
		$post_id = '.section-post.post-'.get_the_ID();
		$css = array();
		$style = '';
		if ( ! isset( $themify->google_fonts ) ) {
			$themify->google_fonts = '';
		}
		$rules = array(
			"$post_id" => array(
				array(	'prop' => 'font-size',
						'key' => array('font_size', 'font_size_unit')
				),
				array(	'prop' => 'font-family',
						'key' => 'font_family'
				),
				array(	'prop' => 'color',
						'key' => 'font_color'
				),
				array(	'prop' => 'background-color',
						'key' => 'background_color'
				),
				array(	'prop' => 'background-image',
						'key' => 'background_image'
				),
				array(	'prop' => 'background-repeat',
						'key' => 'background_repeat'
				)
			),
			"$post_id h1, $post_id h2, $post_id h3, $post_id h4, $post_id h5, $post_id h6" => array(
				array(	'prop' => 'font-family',
						'key' => 'font_family'
				),
				array(	'prop' => 'color',
						'key' => 'font_color'
				)
			),
			"$post_id .section-title" => array(
				array(	'prop' => 'font-size',
						'key' => array('title_font_size', 'font_size_unit')
				),
				array(	'prop' => 'font-family',
						'key' => 'title_font_family'
				),
				array(	'prop' => 'color',
						'key' => 'title_font_color'
				)
			),
			"$post_id a" => array(
				array(	'prop' => 'color',
						'key' => 'link_color'
				)
			)
		);
		foreach ( $rules as $selector => $property ) {
			foreach ( $property as $val ) {
				$prop = $val['prop'];
				$key = $val['key'];
				if ( is_array( $key ) ) {
					if ( $prop == 'font-size' && themify_check( $key[0] ) ) {
						$css[$selector][$prop] = $prop . ': ' . themify_get( $key[0] ) . themify_get( $key[1] );
					}
				} elseif ( themify_check( $key ) && 'default' != themify_get( $key ) ) {
					if ( $prop == 'color' || stripos( $prop, 'color' ) ) {
						$css[$selector][$prop] = $prop . ': #' . themify_get( $key );
					}
					elseif ( $prop == 'background-image' && 'default' != themify_get( $key ) ) {
						$css[$selector][$prop] = $prop .': url(' . themify_get( $key ) . ')';
					}
					elseif ( $prop == 'background-repeat' && 'fullcover' == themify_get( $key ) ) {
						continue;
					}
					elseif ( $prop == 'font-family' ) {
						$font = themify_get( $key );
						$css[$selector][$prop] = $prop .': '. $font;
						if ( ! in_array( $font, themify_get_web_safe_font_list( true ) ) ) {
							$themify->google_fonts .= str_replace( ' ', '+', $font.'|' );
						}
					}
					else {
						$css[$selector][$prop] = $prop .': '. themify_get( $key );
					}
				}
			}
			if ( ! empty( $css[$selector] ) ) {
				$style .= "$selector {\n\t" . implode( ";\n\t", $css[$selector] ) . "\n}\n";
			}
		}

		if ( '' != $style ) {
			echo "\n<!-- $post_id Style -->\n<style>\n$style</style>\n<!-- End $post_id Style -->\n";
		}
	}
	add_action( 'themify_post_end', 'themify_theme_custom_post_css' );
}

if ( ! function_exists( 'themify_theme_enqueue_google_fonts' ) ) {
	/**
	 * Enqueue Google Fonts
	 * @since 1.0.0
	 */
	function themify_theme_enqueue_google_fonts() {
		global $themify;
		if ( ! isset( $themify->google_fonts ) || '' == $themify->google_fonts ) return;
		$themify->google_fonts = substr( $themify->google_fonts, 0, -1 );
		wp_enqueue_style( 'section-styling-google-fonts', themify_https_esc( 'http://fonts.googleapis.com/css' ). '?family='.$themify->google_fonts );
	}
	add_action( 'wp_footer', 'themify_theme_enqueue_google_fonts' );
}

if ( ! function_exists( 'themify_theme_get_gallery_category_terms' ) ) {
	/**
	 * Displays a list of current gallery categories
	 * @return array of name/value arrays
	 * @since 1.0.0
	 */
	function themify_theme_get_gallery_category_terms() {
		$backgrounds = array();
		$backgrounds[] = array( 'name' => __( 'All Categories', 'themify' ), 'value' => 0 );
		$bgs = get_terms( 'gallery-category' );
		if ( ! empty( $bgs ) ) {
			foreach ( $bgs as $index => $gallery ) {
				$backgrounds[] = array(
					'name' => $gallery->name,
					'value' => $gallery->slug
				);
			}
		}
		return $backgrounds;
	}
}

if ( ! function_exists( 'themify_post_count_markup' ) ) {
	/**
	 * Remove parentheses, wrap in span and put inside link
	 * @param $output
	 * @param $args
	 * @return mixed
	 * @since 1.0.0
	 */
	function themify_post_count_markup( $output, $args ) {
		if ( stripos( $args['id'], 'themify-list-categories' ) !== false ) {
			$search = array( '</a>', '(', ')' );
			$replace = array( '', '<span class="badge pull-right">', '</span></a>' );
			$output = str_replace( $search, $replace, $output );
		}
		return $output;
	}
	add_filter( 'wp_list_categories', 'themify_post_count_markup', 10, 2 );
}

if ( ! function_exists( 'themify_comment_reply_link' ) ) {
	/**
	 * Set class for reply link
	 * @param $before_link_after
	 * @return mixed
	 * @since 1.0.0
	 */
	function themify_comment_reply_link( $before_link_after ) {
		$before_link_after = str_replace( "class='", "class='shortcode button outline small ", $before_link_after );
		return $before_link_after;
	}
	add_filter( 'comment_reply_link', 'themify_comment_reply_link' );
}

if ( ! function_exists('themify_theme_comment') ) {
	/**
	 * Custom Theme Comment
	 * @param object $comment Current comment.
	 * @param array $args Parameters for comment reply link.
	 * @param int $depth Maximum comment nesting depth.
	 * @since 1.0.0
	 */
	function themify_theme_comment( $comment, $args, $depth ) {
	   $GLOBALS['comment'] = $comment; ?>

		<li id="comment-<?php comment_ID() ?>" <?php comment_class(); ?>>
			<p class="comment-author">
				<?php printf('%s <cite>%s</cite>', get_avatar( $comment, $size = '70' ), get_comment_author_link()); ?>
				<br />
				<small class="comment-time">
					<?php comment_date( apply_filters('themify_comment_date', 'M d, Y') ); ?>
					@
					<?php comment_time( apply_filters('themify_comment_time', 'H:i a') ); ?>
					<?php edit_comment_link( __('Edit', 'themify'),' [',']'); ?>
				</small>
			</p>
			<div class="commententry">
				<?php if ($comment->comment_approved == '0') : ?>
					<p><em><?php _e('Your comment is awaiting moderation.', 'themify') ?></em></p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>
			<p class="reply">
				<?php comment_reply_link(array_merge( $args, array('add_below' => 'comment', 'depth' => $depth, 'reply_text' => __( 'Reply', 'themify' ), 'max_depth' => $args['max_depth']))) ?>
			</p>
		<?php
	}
}

///////////////////////////////////////
// Themify Theme Key
///////////////////////////////////////
add_filter('themify_theme_key', create_function('$k', "return 'v8a63p0f93lrpelrvg1qkc07aidic2ycv';"));
?>
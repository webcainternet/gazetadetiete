<?php
/**
 * Portfolio Meta Box Options
 * @param array $args
 * @return array
 * @since 1.0.7
 */
function themify_theme_portfolio_meta_box( $args = array() ) {
	extract( $args );
	return array(
		// Post Image
		array(
			'name' 		=> 'post_image',
			'title' 		=> __('Featured Image', 'themify'),
			'description' => '',
			'type' 		=> 'image',
			'meta'		=> array()
		),
		// Gallery Shortcode
		array(
			'name' 		=> 'gallery_shortcode',
			'title' 	=> __('Slider Gallery', 'themify'),
			'description' => '',
			'type' 		=> 'gallery_shortcode'
		),
		// Media Type
		array(
			'name'		=> 'media_type',
			'title'		=> __('Show Media', 'themify'),
			'description' => __('Show whether the featured image or gallery slider on the index view.', 'themify'),
			'type' 		=> 'dropdown',
			'meta'		=> array(
				array(
					'value' => 'slider',
					'name' => __('Slider', 'themify'),
					'selected' => true
				),
				array(
					'value' => 'image',
					'name' => __('Image', 'themify')
				)
			)
		),
		// Featured Image Size
		array(
			'name'	=>	'feature_size',
			'title'	=>	__('Image Size', 'themify'),
			'description' => sprintf(__('Image sizes can be set at <a href="%s">Media Settings</a> and <a href="%s">Regenerated</a>', 'themify'), 'options-media.php', 'admin.php?page=themify_regenerate-thumbnails'),
			'type'		 =>	'featimgdropdown'
		),
		// Multi field: Image Dimension
		themify_image_dimensions_field(),
		// Hide Title
		array(
			"name" 		=> "hide_post_title",
			"title"		=> __('Hide Post Title', 'themify'),
			"description"	=> '',
			"type" 		=> 'dropdown',
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			)
		),
		// Unlink Post Title
		array(
			"name" 		=> "unlink_post_title",
			"title" 		=> __('Unlink Post Title', 'themify'),
			"description" => __('Unlink post title (it will display the post title without link)', 'themify'),
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			)
		),
		// Hide Post Date
		array(
			"name" 		=> "hide_post_date",
			"title"		=> __('Hide Post Date', 'themify'),
			"description"	=> "",
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			)
		),
		// Hide Post Meta
		array(
			"name" 		=> "hide_post_meta",
			"title"		=> __('Hide Post Meta', 'themify'),
			"description"	=> "",
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			)
		),
		// Hide Post Image
		array(
			"name" 		=> "hide_post_image",
			"title" 		=> __('Hide Featured Image', 'themify'),
			"description" => "",
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			)
		),
		// Unlink Post Image
		array(
			"name" 		=> "unlink_post_image",
			"title" 		=> __('Unlink Featured Image', 'themify'),
			"description" => __('Display the Featured Image without link', 'themify'),
			"type" 		=> "dropdown",
			"meta"		=> array(
				array("value" => "default", "name" => "", "selected" => true),
				array("value" => "yes", 'name' => __('Yes', 'themify')),
				array("value" => "no",	'name' => __('No', 'themify'))
			)
		),
		// External Link
		array(
			'name' 		=> 'external_link',
			'title' 		=> __('External Link', 'themify'),
			'description' => __('Link Featured Image to external URL', 'themify'),
			'type' 		=> 'textbox',
			'meta'		=> array()
		),
		// Lightbox Link
		themify_lightbox_link_field(),
		// Shortcode ID
		array(
			'name' 		=> 'post_id_info',
			'title' 	=> __('Shortcode ID', 'themify'),
			'description' => __('To show this use [portfolio id="%s"]'),
			'type' 		=> 'post_id_info'
		)
	);
}

/**************************************************************************************************
 * Portfolio Class - Shortcode
 **************************************************************************************************/

if ( ! class_exists( 'Themify_Portfolio' ) ) {

	class Themify_Portfolio {

		var $instance = 0;
		var $atts = array();
		var $post_type = 'portfolio';
		var $tax = 'portfolio-category';
		var $taxonomies;
		var $slideLoader = '<div class="slideshow-slider-loader"><div class="themify-loader"><div class="themify-loader_1 themify-loader_blockG"></div><div class="themify-loader_2 themify-loader_blockG"></div><div class="themify-loader_3 themify-loader_blockG"></div></div></div>';

		function __construct( $args = array() ) {
			$this->atts = array(
				'id' => '',
				'title' => '',
				'unlink_title' => '',
				'image' => 'yes', // no
				'image_w' => 221,
				'image_h' => 221,
				'display' => 'none', // excerpt, content
				'post_meta' => '', // yes
				'post_date' => '', // yes
				'more_link' => false, // true goes to post type archive, and admits custom link
				'more_text' => __('More &rarr;', 'themify'),
				'limit' => 4,
				'category' => 'all', // integer category ID
				'order' => 'DESC', // ASC
				'orderby' => 'date', // title, rand
				'style' => '', // grid4, grid3, grid2, list-post, slider
				'auto' => 'default', // autoplay pause length
				'effect' => 'default', // transition effect
				'speed' => 'default', // transition speed
				'visible' => 'default', // number of items visible at the same time
				'scroll' => 'default', // number of items to scroll at once
				'wrap' => 'default',
				'slider_nav' => 'default',
				'pager' => 'default',
				'sorting' => 'no', // yes
				'page_nav' => 'no', // yes
				'paged' => '0', // internal use for pagination, dev: previously was 1
				'use_original_dimensions' => 'no' // yes
			);
			$this->register();
			add_shortcode( $this->post_type, array( $this, 'init_shortcode' ) );

			add_filter('themify_default_post_layout_condition', array( $this, 'post_layout_condition' ), 12);
			add_filter('themify_default_post_layout', array( $this, 'post_layout' ), 12);
			add_filter('themify_default_layout_condition', array( $this, 'sidebar_condition' ), 12);
			add_filter('themify_default_layout', array( $this, 'sidebar' ), 12);
		}

		/**
		 * Register post type and taxonomy
		 */
		function register() {
			$cpt = array(
				'plural' => __('Portfolios', 'themify'),
				'singular' => __('Portfolio', 'themify'),
				'rewrite' => themify_check('themify_portfolio_slug')? themify_get('themify_portfolio_slug') : apply_filters('themify_portfolio_rewrite', 'project')
			);
			register_post_type( $this->post_type, array(
				'labels' => array(
					'name' => $cpt['plural'],
					'singular_name' => $cpt['singular'],
					'add_new' => __( 'Add New', 'themify' ),
					'add_new_item' => __( 'Add New Portfolio', 'themify' ),
					'edit_item' => __( 'Edit Portfolio', 'themify' ),
					'new_item' => __( 'New Portfolio', 'themify' ),
					'view_item' => __( 'View Portfolio', 'themify' ),
					'search_items' => __( 'Search Portfolios', 'themify' ),
					'not_found' => __( 'No Portfolios found', 'themify' ),
					'not_found_in_trash' => __( 'No Portfolios found in Trash', 'themify' ),
					'menu_name' => __( 'Portfolios', 'themify' ),
				),
				'supports' => isset( $cpt['supports'] )? $cpt['supports'] : array( 'title', 'editor', 'thumbnail', 'custom-fields', 'excerpt' ),

				'hierarchical' => false,
				'public' => true,
				'rewrite' => array( 'slug' => $cpt['rewrite'] ),
				'query_var' => true,
				'can_export' => true,
				'capability_type' => 'post'
			));
			register_taxonomy( $this->tax, array( $this->post_type ), array(
				'labels' => array(
					'name' => sprintf( __( '%s Categories', 'themify' ), $cpt['singular'] ),
					'singular_name' => sprintf( __( '%s Category', 'themify' ), $cpt['singular'] )
				),
				'public' => true,
				'show_in_nav_menus' => true,
				'show_ui' => true,
				'show_tagcloud' => true,
				'hierarchical' => true,
				'rewrite' => true,
				'query_var' => true
			));
			if ( is_admin() ) {
				add_filter('manage_edit-'.$this->tax.'_columns', array(&$this, 'taxonomy_header'), 10, 2);
				add_filter('manage_'.$this->tax.'_custom_column', array(&$this, 'taxonomy_column_id'), 10, 3);
			}
		}

		/**
		 * Set default term for custom taxonomy and assign to post
		 * @param number
		 * @param object
		 */
		function set_default_term( $post_id, $post ) {
			if ( 'publish' === $post->post_status ) {
				$terms = wp_get_post_terms( $post_id, $this->tax );
				if ( empty( $terms ) ) {
					wp_set_object_terms( $post_id, __( 'Uncategorized', 'themify' ), $this->tax );
				}
			}
		}

		/**
		 * Display an additional column in categories list
		 * @since 1.0.0
		 */
		function taxonomy_header($cat_columns) {
			$cat_columns['cat_id'] = 'ID';
			return $cat_columns;
		}
		/**
		 * Display ID in additional column in categories list
		 * @since 1.0.0
		 */
		function taxonomy_column_id($null, $column, $termid) {
			return $termid;
		}

		/**
		 * Includes new post types registered in theme to array of post types managed by Themify
		 * @param array
		 * @return array
		 */
		function extend_post_types( $types ) {
			return array_merge( $types, array( $this->post_type ) );
		}

		/**
		 * Add shortcode to WP
		 * @param $atts Array shortcode attributes
		 * @return String
		 * @since 1.0.0
		 */
		function init_shortcode( $atts ) {
			$this->instance++;
			return do_shortcode( $this->shortcode( shortcode_atts( $this->atts, $atts ), $this->post_type ) );
		}

		/**
		 * Trigger at the end of __construct of this shortcode
		 */
		function manage_and_filter() {
			add_filter( "manage_edit-{$this->post_type}_columns", array( $this, 'type_column_header' ), 10, 2 );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'type_column' ), 10, 3 );
			add_action( 'load-edit.php', array( $this, 'filter_load' ) );
			add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 1 );
			add_filter( 'get_sample_permalink_html', array($this, 'hide_view_post'), '', 4 );
		}

		/**
		 * Remove quick edit action from entries list in admin
		 * @param $actions
		 * @return mixed
		 */
		function remove_quick_edit( $actions ) {
			global $post;
			if( $post->post_type == $this->post_type )
				unset($actions['inline hide-if-no-js']);
			return $actions;
		}

		/**
		 * Hides View Section/Team/Highlight/Testimonial/Timeline button in edit screen
		 * @param string $return
		 * @param string $id
		 * @param string $new_title
		 * @param string $new_slug
		 * @return string Markup without the button
		 */
		function hide_view_post($return, $id, $new_title, $new_slug){
			global $post;
			if( $post->post_type == $this->post_type ) {
				return preg_replace('/<span id=\'view-post-btn\'>.*<\/span>/i', '', $return);
			} else {
				return $return;
			}
		}

		/**
		 * Display an additional column in list
		 * @param array
		 * @return array
		 */
		function type_column_header( $columns ) {
			unset( $columns['date'] );
			$columns['shortcode'] = __('Shortcode', 'themify');
			return $columns;
		}

		/**
		 * Display shortcode, type, size and color in columns in tiles list
		 * @param string $column key
		 * @param number $post_id
		 * @return string
		 */
		function type_column( $column, $post_id ) {
			switch( $column ) {
				case 'shortcode' :
					echo '<code>[' . $this->post_type . ' id="' . $post_id . '"]</code>';
					break;
			}
		}

		/**
		 * Filter request to sort
		 */
		function filter_load() {
			global $typenow;
			if ( $typenow == $this->post_type ) {
				add_action( current_filter(), array( $this, 'setup_vars' ), 20 );
				add_action( 'restrict_manage_posts', array( $this, 'get_select' ) );
				add_filter( "manage_taxonomies_for_{$this->post_type}_columns", array( $this, 'add_columns' ) );
			}
		}

		/**
		 * Add columns when filtering posts in edit.php
		 */
		public function add_columns( $taxonomies ) {
			return array_merge( $taxonomies, $this->taxonomies );
		}

		/**
		 * Parses the arguments given as category to see if they are category IDs or slugs and returns a proper tax_query
		 * @param $category
		 * @param $post_type
		 * @return array
		 */
		function parse_category_args( $category, $post_type ) {
			if ( 'all' != $category ) {
				$tax_query_terms = explode(',', $category);
				if ( preg_match( '#[a-z]#', $category ) ) {
					return array( array( 'taxonomy' => $post_type . '-category', 'field' => 'slug', 'terms' => $tax_query_terms ) );
				} else {
					return array( array( 'taxonomy' => $post_type . '-category', 'field' => 'id', 'terms' => $tax_query_terms ) );
				}
			}
		}

		/**
		 * Select form element to filter the post list
		 * @return string HTML
		 */
		public function get_select() {
			$html = '';
			foreach ($this->taxonomies as $tax) {
				$options = sprintf('<option value="">%s %s</option>', __('View All', 'themify'),
				get_taxonomy($tax)->label);
				$class = is_taxonomy_hierarchical($tax) ? ' class="level-0"' : '';
				foreach (get_terms( $tax ) as $taxon) {
					$options .= sprintf('<option %s%s value="%s">%s%s</option>', isset($_GET[$tax]) ? selected($taxon->slug, $_GET[$tax], false) : '', '0' !== $taxon->parent ? ' class="level-1"' : $class, $taxon->slug, '0' !== $taxon->parent ? str_repeat('&nbsp;', 3) : '', "{$taxon->name} ({$taxon->count})");
				}
				$html .= sprintf('<select name="%s" id="%s" class="postform">%s</select>', $tax, $tax, $options);
			}
			return print $html;
		}

		/**
		 * Setup vars when filtering posts in edit.php
		 */
		function setup_vars() {
			$this->post_type =  get_current_screen()->post_type;
			$this->taxonomies = array_diff(get_object_taxonomies($this->post_type), get_taxonomies(array('show_admin_column' => 'false')));
		}

		/**
		 * Returns link wrapped in paragraph either to the post type archive page or a custom location
		 * @param bool|string $more_link False does nothing, true goes to archive page, custom string sets custom location
		 * @param string $more_text
		 * @param string $post_type
		 * @return string
		 */
		function section_link( $more_link = false, $more_text, $post_type ) {
			if ( $more_link ) {
				if ( 'true' == $more_link ) {
					$more_link = get_post_type_archive_link( $post_type );
				}
				return '<p class="more-link-wrap"><a href="' . esc_url( $more_link ) . '" class="more-link">' . $more_text . '</a></p>';
			}
			return '';
		}

		/**
		 * Returns class to add in columns when querying multiple entries
		 * @param string $style Entries layout
		 * @return string $col_class CSS class for column
		 */
		function column_class( $style ) {
			$col_class = '';
			switch ( $style ) {
				case 'grid4':
					$col_class = 'col4-1';
					break;
				case 'grid3':
					$col_class = 'col3-1';
					break;
				case 'grid2':
					$col_class = 'col2-1';
					break;
				default:
					$col_class = '';
					break;
			}
			return $col_class;
		}

		/**
		 * Main shortcode rendering
		 * @param array $atts
		 * @param $post_type
		 * @return string|void
		 */
		function shortcode($atts = array(), $post_type){
			extract($atts);
			// Pagination
			global $paged;
			$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
			// Parameters to get posts
			$args = array(
				'post_type' => $post_type,
				'posts_per_page' => $limit,
				'order' => $order,
				'orderby' => $orderby,
				'suppress_filters' => false,
				'paged' => $paged
			);
			// Category parameters
			$args['tax_query'] = $this->parse_category_args($category, $post_type);

			// Defines layout type
			$cpt_layout_class = $this->post_type.'-multiple clearfix type-multiple';
			$multiple = true;

			// Single post type or many single post types
			if( '' != $id ){
				if(strpos($id, ',')){
					$ids = explode(',', str_replace(' ', '', $id));
					foreach ($ids as $string_id) {
						$int_ids[] = intval($string_id);
					}
					$args['post__in'] = $int_ids;
					$args['orderby'] = 'post__in';
				} else {
					$args['p'] = intval($id);
					$cpt_layout_class = $this->post_type.'-single';
					$multiple = false;
				}
			}

			// Get posts according to parameters
			$portfolio_query = new WP_Query();
			$posts = $portfolio_query->query(apply_filters('themify_'.$post_type.'_shortcode_args', $args));

			// Grid Style
			if( '' == $style ){
				$style = themify_check('setting-default_portfolio_index_post_layout')?
							 themify_get('setting-default_portfolio_index_post_layout'):
							 'grid4';
			}

			if( is_singular('portfolio') ){
				if( '' == $image_w ){
					$image_w = themify_check('setting-default_portfolio_single_image_post_width')?
							themify_get('setting-default_portfolio_single_image_post_width'):
							'670';
				}
				if( '' == $image_h ){
					$image_h = themify_check('setting-default_portfolio_single_image_post_height')?
							themify_get('setting-default_portfolio_single_image_post_height'):
							'0';
				}
				if( '' == $post_date ){
					$post_date = themify_check('setting-default_portfolio_single_post_date')?
							themify_get('setting-default_portfolio_index_post_date'):
							'yes';
				}
				if( '' == $title ){
					$title = themify_check('setting-default_portfolio_single_title')?
							themify_get('setting-default_portfolio_single_title'):
							'yes';
				}
				if( '' == $unlink_title ){
					$unlink_title = themify_check('setting-default_portfolio_single_unlink_post_title')?
							themify_get('setting-default_portfolio_single_unlink_post_title'):
							'no';
				}
				if( '' == $post_meta ){
					$post_meta = themify_check('setting-default_portfolio_single_meta')?
							themify_get('setting-default_portfolio_single_meta'):
							'yes';
				}
			} else {
				if( '' == $image_w ){
					$image_w = themify_check('setting-default_portfolio_index_image_post_width')?
							themify_get('setting-default_portfolio_index_image_post_width'):
							'221';
				}
				if( '' == $image_h ){
					$image_h = themify_check('setting-default_portfolio_index_image_post_height')?
							themify_get('setting-default_portfolio_index_image_post_height'):
							'221';
				}
				if( '' == $title ){
					$title = themify_check('setting-default_portfolio_index_title')?
							themify_get('setting-default_portfolio_index_title'):
							'yes';
				}
				if( '' == $unlink_title ){
					$unlink_title = themify_check('setting-default_portfolio_index_unlink_post_title')?
							themify_get('setting-default_portfolio_index_unlink_post_title'):
							'no';
				}
				// Reverse logic
				if( '' == $post_date ){
					$post_date = themify_check('setting-default_portfolio_index_post_date')?
							'no' == themify_get('setting-default_portfolio_index_post_date')?
								'yes' : 'no':
							'no';
				}
				if( '' == $post_meta ){
					$post_meta = themify_check('setting-default_portfolio_index_post_meta_category')?
							'no' == themify_get('setting-default_portfolio_index_post_meta_category')? 'yes' : 'no' :
							'no';
				}
			}

			// Collect markup to be returned
			$out = '';

			if( $posts ) {
				global $themify;
				$themify_save = clone $themify; // save a copy

				// override $themify object
				$themify->hide_title = 'yes' == $title? 'no': 'yes';
				$themify->unlink_title =  ( '' == $unlink_title || 'no' == $unlink_title )? 'no' : 'yes';
				$themify->hide_image = 'yes' == $image? 'no': 'yes';
				$themify->hide_meta = 'yes' == $post_meta? 'no': 'yes';
				$themify->hide_date = 'yes' == $post_date? 'no': 'yes';
				if(!$multiple) {
					if( '' == $image_w || get_post_meta($args['p'], 'image_width', true ) ){
						$themify->width = get_post_meta($args['p'], 'image_width', true );
					}
					if( '' == $image_h || get_post_meta($args['p'], 'image_height', true ) ){
						$themify->height = get_post_meta($args['p'], 'image_height', true );
					}
				} else {
					$themify->width = $image_w;
					$themify->height = $image_h;
				}
				$themify->use_original_dimensions = 'yes' == $use_original_dimensions? 'yes': 'no';
				$themify->display_content = $display;
				$themify->more_link = $more_link;
				$themify->more_text = $more_text;
				$themify->post_layout = $style;
				$themify->portfolio_instance = $this->instance;

				$slider_id = ( 'slider' == $style )? 'id="portfolio-slider-' . $this->instance.'"': '';
				$out .= ( 'slider' == $style)? $this->slideLoader : '';
				$out .= '<div '.$slider_id.' class="loops-wrapper shortcode ' . $post_type  . ' ' . $style . ' '. $cpt_layout_class .'">';
					if ( 'slider' == $style ) {
						$option = 'setting-portfolio_slider';
						$auto = 'default' == $auto ? themify_check( $option.'_autoplay' ) ? themify_get( $option.'_autoplay' ) : '4000' : $auto;
						if ( is_numeric( $auto ) && intval( $auto ) < 99 ) {
							$auto = intval( $auto ) * 1000;
						}
						$out .= sprintf('<div class="slideshow-wrap"><ul class="slideshow portfolio-holder" data-id="portfolio-slider-%s" data-autoplay="%s" data-effect="%s" data-speed="%s" data-visible="%s" data-scroll="%s" data-wrap="%s" data-slidernav="%s" data-pager="%s">',
							$this->instance,
							$auto,
							'default' == $effect ? themify_check( $option.'_effect' ) ? themify_get( $option.'_effect' ) : 'scroll' : $effect,
							'default' == $speed ? themify_check( $option.'_transition_speed' ) ? themify_get( $option.'_transition_speed' ) : '500' : $speed,
							'default' == $visible ? themify_check( $option.'_visible' ) ? themify_get( $option.'_visible' ) : '1' : $visible,
							'default' == $scroll ? themify_check( $option.'_scroll' ) ? themify_get( $option.'_scroll' ) : '1' : $scroll,
							'default' == $wrap ? themify_check( $option.'_wrap' ) ? themify_get( $option.'_wrap' ) : 'yes' : $wrap,
							'default' == $slider_nav ? themify_check( $option.'_slider_nav' ) ? themify_get( $option.'_slider_nav' ) : 'yes' : $slider_nav,
							'default' == $pager ? themify_check( $option.'_pager' ) ? themify_get( $option.'_pager' ) : 'yes' : $pager
						);
					}
					$out .= themify_get_shortcode_template($posts, 'includes/loop-portfolio', 'index');
					if ( 'slider' == $style ) {
						$out .= '</ul></div>';
					}
					$out .= $this->section_link($more_link, $more_text, $post_type);

				$out .= '</div>';

				$themify = clone $themify_save; // revert to original $themify state
			}
			return $out;
		}

		/**************************************************************************************************
		 * Body Classes for index and single
		 **************************************************************************************************/

		/**
		 * Changes condition to filter post layout class
		 * @param $condition
		 * @return bool
		 */
		function post_layout_condition($condition) {
			return $condition || is_tax('portfolio-category');
		}
		/**
		 * Returns modified post layout class
		 * @return string
		 */
		function post_layout() {
			global $themify;
			// get default layout
			$class = $themify->post_layout;
			if('portfolio' == $themify->query_post_type) {
				$class = themify_check('portfolio_layout') ? themify_get('portfolio_layout') : themify_get('setting-default_post_layout');
			} elseif (is_tax('portfolio-category')) {
				$class = themify_check('setting-default_portfolio_index_post_layout')? themify_get('setting-default_portfolio_index_post_layout') : 'list-post';
			}
			return $class;
		}
		/**
		 * Changes condition to filter sidebar layout class
		 * @param bool $condition
		 * @return bool
		 */
		function sidebar_condition($condition) {
			global $themify;
			// if layout is not set or is the home page and front page displays is set to latest posts
			return $condition || (is_home() && 'posts' == get_option('show_on_front')) || '' != $themify->query_category || is_tax('portfolio-category') || is_singular('portfolio');
		}
		/**
		 * Returns modified sidebar layout class
		 * @param string $class Original body class
		 * @return string
		 */
		function sidebar($class) {
			global $themify;
			// get default layout
			$class = $themify->layout;
			if (is_tax('portfolio-category')) {
				$class = themify_check('setting-default_portfolio_index_layout')? themify_get('setting-default_portfolio_index_layout') : 'sidebar-none';
			}
			return $class;
		}
	}
}

if ( ! function_exists( 'themify_theme_portfolio_image' ) ) {
	/**
	 * Returns the image for the portfolio slider
	 * @param int $attachment_id Image attachment ID
	 * @param int $width Width of the returned image
	 * @param int $height Height of the returned image
	 * @param string $size Size of the returned image
	 * @return string
	 * @since 1.0.0
	 */
	function themify_theme_portfolio_image($attachment_id, $width, $height, $size = 'large') {
		$size = apply_filters( 'themify_portfolio_image_size', $size );
		if ( themify_check( 'setting-img_settings_use' ) ) {
			// Image Script is disabled, use WP image
			$html = wp_get_attachment_image( $attachment_id, $size );
		} else {
			// Image Script is enabled, use it to process image
			$img = wp_get_attachment_image_src($attachment_id, $size);
			$html = themify_get_image('ignore=true&src='.$img[0].'&w='.$width.'&h='.$height);
		}
		return apply_filters( 'themify_portfolio_image_html', $html, $attachment_id, $width, $height, $size );
	}
}

/**************************************************************************************************
 * Initialize Type Class
 **************************************************************************************************/
new Themify_Portfolio();

/**************************************************************************************************
 * Themify Theme Settings Module
 **************************************************************************************************/

if ( ! function_exists( 'themify_default_portfolio_single_layout' ) ) {
	/**
	 * Default Single Portfolio Layout
	 * @param array $data
	 * @return string
	 */
	function themify_default_portfolio_single_layout( $data=array() ) {
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();
		/**
		 * Variable prefix key
		 * @var string
		 */
		$prefix = 'setting-default_portfolio_single_';
		/**
		 * Basic default options '', 'yes', 'no'
		 * @var array
		 */
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);


		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Hide Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'title">' .
							themify_options_module($default_options, $prefix.'title') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Unlink Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'unlink_post_title">' .
							themify_options_module($default_options, $prefix.'unlink_post_title') . '
						</select>
					</p>';

		// Hide Post Meta /////////////////////////////////////////
		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Meta', 'themify') . '</span>
						<select name="'.$prefix.'post_meta_category">' .
							themify_options_module($default_options, $prefix.'post_meta_category', true, 'yes') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Date', 'themify') . '</span>
						<select name="'.$prefix.'post_date">' .
							themify_options_module($default_options, $prefix.'post_date') . '
						</select>
					</p>';
		/**
		 * Image Dimensions
		 */
		$output .= '
			<p>
				<span class="label">' . __('Image Size', 'themify') . '</span>
				<input type="text" class="width2" name="'.$prefix.'image_post_width" value="'.$data[$prefix.'image_post_width'].'" /> ' . __('width', 'themify') . ' <small>(px)</small>
				<input type="text" class="width2" name="'.$prefix.'image_post_height" value="'.$data[$prefix.'image_post_height'].'" /> ' . __('height', 'themify') . ' <small>(px)</small>
			</p>';

		return $output;
	}
}

if ( ! function_exists( 'themify_default_portfolio_index_layout' ) ) {
	/**
	 * Default Index Portfolio Layout
	 * @param array $data
	 * @return string
	 */
	function themify_default_portfolio_index_layout( $data=array() ) {
		/**
		 * Associative array containing theme settings
		 * @var array
		 */
		$data = themify_get_data();
		/**
		 * Variable prefix key
		 * @var string
		 */
		$prefix = 'setting-default_portfolio_index_';
		/**
		 * Basic default options '', 'yes', 'no'
		 * @var array
		 */
		$default_options = array(
			array('name'=>'','value'=>''),
			array('name'=>__('Yes', 'themify'),'value'=>'yes'),
			array('name'=>__('No', 'themify'),'value'=>'no')
		);
		/**
		 * Sidebar Layout
		 * @var string
		 */
		$layout = isset( $data[$prefix.'layout'] ) ? $data[$prefix.'layout'] : '';
		/**
		 * Sidebar Layout Options
		 * @var array
		 */
		$sidebar_options = array(
			array('value' => 'sidebar1', 'img' => 'images/layout-icons/sidebar1.png', 'title' => __('Sidebar Right', 'themify')),
			array('value' => 'sidebar1 sidebar-left', 'img' => 'images/layout-icons/sidebar1-left.png', 'title' => __('Sidebar Left', 'themify')),
			array('value' => 'sidebar-none', 'img' => 'images/layout-icons/sidebar-none.png', 'selected' => true, 'title' => __('No Sidebar', 'themify')),
		);
		/**
		 * Post Layout Options
		 * @var array
		 */
		$post_layout_options = array(
			array('value' => 'grid4', 'img' => 'images/layout-icons/grid4.png', 'title' => __('Grid 4', 'themify'), 'selected' => true),
			array('value' => 'grid3', 'img' => 'images/layout-icons/grid3.png', 'title' => __('Grid 3', 'themify')),
			array('value' => 'grid2', 'img' => 'images/layout-icons/grid2.png', 'title' => __('Grid 2', 'themify'))
		);
		/**
		 * HTML for settings panel
		 * @var string
		 */
		$output = '<p>
						<span class="label">' . __('Portfolio Sidebar Option', 'themify') . '</span>';
						foreach($sidebar_options as $option){
							if ( ( '' == $layout || !$layout || ! isset( $layout ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
								$layout = $option['value'];
							}
							if($layout == $option['value']){
								$class = 'selected';
							} else {
								$class = '';
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
						}
						$output .= '<input type="hidden" name="'.$prefix.'layout" class="val" value="'.$layout.'" />';
		$output .= '</p>';
		/**
		 * Post Layout
		 */
		$output .= '<p>
						<span class="label">' . __('Portfolio Layout', 'themify') . '</span>';

						$val = isset( $data[$prefix.'post_layout'] ) ? $data[$prefix.'post_layout'] : '';

						foreach($post_layout_options as $option){
							if ( ( '' == $val || ! $val || ! isset( $val ) ) && ( isset( $option['selected'] ) && $option['selected'] ) ) {
								$val = $option['value'];
							}
							if ( $val == $option['value'] ) {
								$class = "selected";
							} else {
								$class = "";
							}
							$output .= '<a href="#" class="preview-icon '.$class.'" title="'.$option['title'].'"><img src="'.THEME_URI.'/'.$option['img'].'" alt="'.$option['value'].'"  /></a>';
						}

		$output .= '	<input type="hidden" name="'.$prefix.'post_layout" class="val" value="'.$val.'" />
					</p>';
		/**
		 * Display Content
		 */
		$output .= '<p>
						<span class="label">' . __('Display Content', 'themify') . '</span>
						<select name="'.$prefix.'display">'.
							themify_options_module(array(
								array('name' => __('None', 'themify'),'value'=>'none'),
								array('name' => __('Full Content', 'themify'),'value'=>'content'),
								array('name' => __('Excerpt', 'themify'),'value'=>'excerpt')
							), $prefix.'display').'
						</select>
					</p>';

		$output .= '<p>
						<span class="label">' . __('Hide Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'title">' .
							themify_options_module($default_options, $prefix.'title') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Unlink Portfolio Title', 'themify') . '</span>
						<select name="'.$prefix.'unlink_post_title">' .
							themify_options_module($default_options, $prefix.'unlink_post_title') . '
						</select>
					</p>';

		// Hide Post Meta /////////////////////////////////////////
		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Meta', 'themify') . '</span>
						<select name="'.$prefix.'post_meta_category">' .
							themify_options_module($default_options, $prefix.'post_meta_category', true, 'yes') . '
						</select>
					</p>';

		$output .=	'<p>
						<span class="label">' . __('Hide Portfolio Date', 'themify') . '</span>
						<select name="'.$prefix.'post_date">' .
							themify_options_module($default_options, $prefix.'post_date', true, 'yes') . '
						</select>
					</p>';
		/**
		 * Image Dimensions
		 */
		$output .= '<p>
						<span class="label">' . __('Image Size', 'themify') . '</span>
						<input type="text" class="width2" name="'.$prefix.'image_post_width" value="'.$data[$prefix.'image_post_width'].'" /> ' . __('width', 'themify') . ' <small>(px)</small>
						<input type="text" class="width2" name="'.$prefix.'image_post_height" value="'.$data[$prefix.'image_post_height'].'" /> ' . __('height', 'themify') . ' <small>(px)</small>
					</p>';
		return $output;
	}
}

if ( ! function_exists( 'themify_portfolio_slug' ) ) {
	/**
	 * Portfolio Slug
	 * @param array $data
	 * @return string
	 */
	function themify_portfolio_slug($data=array()){
		$data = themify_get_data();
		$portfolio_slug = isset($data['themify_portfolio_slug'])? $data['themify_portfolio_slug']: apply_filters('themify_portfolio_rewrite', 'project');
		return '
			<p>
				<span class="label">' . __('Portfolio Base Slug', 'themify') . '</span>
				<input type="text" name="themify_portfolio_slug" value="'.$portfolio_slug.'" class="slug-rewrite">
				<br />
				<span class="pushlabel"><small>' . __('Use only lowercase letters, numbers, underscores and dashes.', 'themify') . '</small></span>
				<br />
				<span class="pushlabel"><small>' . sprintf(__('After changing this, go to <a href="%s">permalinks</a> and click "Save changes" to refresh them.', 'themify'), admin_url('options-permalink.php')) . '</small></span><br />
			</p>';
	}
}

if ( ! function_exists( 'themify_portfolio_slider' ) ) {
	/**
	 * Creates portfolio slider module
	 * @return string
	 */
	function themify_portfolio_slider() {

		$prefix = 'setting-portfolio_slider_';

		$auto_options = apply_filters( 'themify_generic_slider_auto',
			array(
				__('4 Secs (default)', 'themify') => 4000,
				__('Off', 'themify') => 'off',
				__('1 Sec', 'themify') => 1000,
				__('2 Secs', 'themify') => 2000,
				__('3 Secs', 'themify') => 3000,
				__('4 Secs', 'themify') => 4000,
				__('5 Secs', 'themify') => 5000,
				__('6 Secs', 'themify') => 6000,
				__('7 Secs', 'themify') => 7000,
				__('8 Secs', 'themify') => 8000,
				__('9 Secs', 'themify') => 9000,
				__('10 Secs', 'themify')=> 10000
			)
		);
		$speed_options = apply_filters( 'themify_generic_slider_speed',
			array(
				__('Fast', 'themify') => 500,
				__('Normal', 'themify') => 1000,
				__('Slow', 'themify') => 1500
			)
		);
		$effect_options = array(
			array('name' => __('Scroll', 'themify'), 'value' => 'scroll'),
			array('name' => __('Fade', 'themify'), 'value' =>'fade')
		);

		/**
		 * Auto Play
		 */
		$output = '<p>
						<span class="label">' . __('Auto Play', 'themify') . '</span>
						<select name="'.$prefix.'autoplay">';
						foreach ( $auto_options as $name => $val ) {
							$output .= '<option value="'.$val.'" ' . selected( themify_get( $prefix.'autoplay' ), themify_check( $prefix.'autoplay' ) ? $val: 4000, false ) . '>'.$name.'</option>';
						}
		$output .= '	</select>
					</p>';

		/**
		 * Effect
		 */
		$output .= '<p>
						<span class="label">' . __('Effect', 'themify') . '</span>
						<select name="'.$prefix.'effect">'.
						themify_options_module( $effect_options, $prefix.'effect' ) . '
						</select>
					</p>';

		/**
		 * Transition Speed
		 */
		$output .= '<p>
						<span class="label">' . __('Transition Speed', 'themify') . '</span>
						<select name="'.$prefix.'transition_speed">';
						foreach ( $speed_options as $name => $val ) {
							$output .= '<option value="'.$val.'" ' . selected( themify_get( $prefix.'transition_speed' ), themify_check( $prefix.'transition_speed' ) ? $val: 500, false ) . '>'.$name.'</option>';
						}
		$output .= '	</select>
					</p>';

		/**
		 * Visible Items
		 */
		$visible_items = themify_check( $prefix.'visible' ) ? themify_get( $prefix.'visible' ) : '1';
		$output .= '<p>
						<span class="label">' . __( 'Visible Items', 'themify' ) . '</span>
						<input type="text" name="'.$prefix.'visible" value="' . $visible_items . '">
						<span class="pushlabel"><small>' . __( 'Enter the number of visible items at the same time in the slider.', 'themify' ) . '</small></span>
					</p>';

		/**
		 * Scroll Items
		 */
		$scroll_items = themify_check( $prefix.'scroll' ) ? themify_get( $prefix.'scroll' ) : '1';
		$output .= '<p>
						<span class="label">' . __( 'Items to Scroll', 'themify' ) . '</span>
						<input type="text" name="'.$prefix.'scroll" value="' . $scroll_items . '">
						<span class="pushlabel"><small>' . __( 'Enter the number of items to scroll at the same time in the slider.', 'themify' ) . '</small></span>
					</p>';

		return apply_filters( 'themify_portfolio_slider', $output );
	}
}
<?php

/**
 * Gallery Meta Box Options
 * @param array $args
 * @return array
 * @since 1.0.7
 */
function themify_theme_gallery_meta_box( $args = array() ) {
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
		// Featured Image Size
		array(
			'name'	=>	'feature_size',
			'title'	=>	__('Image Size', 'themify'),
			'description' => sprintf(__('Image sizes can be set at <a href="%s">Media Settings</a> and <a href="%s">Regenerated</a>', 'themify'), 'options-media.php', 'admin.php?page=themify_regenerate-thumbnails'),
			'type'		 =>	'featimgdropdown'
		),
		// Multi field: Image Dimension
		themify_image_dimensions_field(),
		// Gallery Shortcode
		array(
			'name' 		=> 'gallery_shortcode',
			'title' 	=> __('Gallery', 'themify'),
			'description' => '',
			'type' 		=> 'gallery_shortcode',
		),
	);
}

/**************************************************************************************************
 * Highlight Class - Shortcode
 **************************************************************************************************/

if ( ! class_exists( 'Themify_Gallery' ) ) {

	class Themify_Gallery {

		var $instance = 0;
		var $atts = array();
		var $post_type = 'gallery';
		var $tax = 'gallery-category';
		var $taxonomies;

		function __construct( $args = array() ) {
			$this->atts = array(
				'id' => '',
				'title' => 'yes', // no
				'image' => 'yes', // no
				'image_w' => 144,
				'image_h' => 144,
				'display' => 'content', // excerpt, none
				'more_link' => false, // true goes to post type archive, and admits custom link
				'more_text' => __('More &rarr;', 'themify'),
				'limit' => 6,
				'category' => 'all', // integer category ID
				'order' => 'DESC', // ASC
				'orderby' => 'date', // title, rand
				'style' => 'grid3', // grid4, grid2, list-post
				'section_link' => false, // true goes to post type archive, and admits custom link
				'use_original_dimensions' => 'no' // yes
			);
			$this->register();
			add_action( 'admin_init', array( $this, 'manage_and_filter' ) );
			add_action( 'save_post', array($this, 'set_default_term'), 100, 2 );
			add_filter( 'themify_post_types', array($this, 'extend_post_types' ) );
			add_filter( 'themify_gallery_plugins_args', array($this, 'enable_gallery_area' ) );

			add_action( 'wp_ajax_themify_get_gallery_entry', array($this, 'themify_get_gallery_entry') );
			add_action( 'wp_ajax_nopriv_themify_get_gallery_entry', array($this, 'themify_get_gallery_entry') );

			add_filter('themify_default_layout_condition', array($this, 'sidebar_condition'), 12);
			add_filter('themify_default_layout', array($this, 'sidebar'), 12);
		}

		/**
		 * Initialize gallery content area for fullscreen gallery
		 * @param $args
		 * @return mixed
		 */
		function enable_gallery_area( $args ) {
			$args['contentImagesAreas'] .= ', .type-gallery';
			return $args;
		}

		/**
		 * AJAX hook to return gallery entry
		 */
		function themify_get_gallery_entry() {
			check_ajax_referer( 'ajax_nonce', 'nonce' );

			if ( ! isset( $_POST['entry_id'] ) ) {
				echo json_encode( array(
					'error' => __( 'Entry ID not set', 'themify' ),
				));
				die();
			}

			$entry = get_post( $_POST['entry_id'] );
			setup_postdata( $entry );

			$tax = 'gallery-category';

			$terms = array();

			$raw_terms = get_the_terms( $entry->ID, $tax );

			if ( $raw_terms && ! is_wp_error( $raw_terms ) ) {
				foreach ( $raw_terms as $term ) {
					$terms[] = array(
						'name' => $term->name,
						'link' => get_term_link( $term, $tax )
					);
				}
			}

			echo json_encode( array(
				'title' => apply_filters( 'the_title', $entry->post_title ),
				'date' => apply_filters( 'the_date', mysql2date( 'M j, Y', $entry->post_date ) ),
				'content' => apply_filters( 'the_content', $entry->post_content ),
				'excerpt' => apply_filters( 'the_excerpt', get_the_excerpt() ),
				'link' => get_permalink( $entry->ID ),
				'terms' => $terms,
			));

			die();
		}

		/**
		 * Register post type and taxonomy
		 */
		function register() {
			$cpt = array(
				'plural' => __('Galleries', 'themify'),
				'singular' => __('Gallery', 'themify'),
			);
			register_post_type( $this->post_type, array(
				'labels' => array(
					'name' => $cpt['plural'],
					'singular_name' => $cpt['singular'],
					'add_new' => __( 'Add New', 'themify' ),
					'add_new_item' => __( 'Add New Gallery', 'themify' ),
					'edit_item' => __( 'Edit Gallery', 'themify' ),
					'new_item' => __( 'New Gallery', 'themify' ),
					'view_item' => __( 'View Gallery', 'themify' ),
					'search_items' => __( 'Search Galleries', 'themify' ),
					'not_found' => __( 'No Galleries found', 'themify' ),
					'not_found_in_trash' => __( 'No Galleries found in Trash', 'themify' ),
					'menu_name' => __( 'Galleries', 'themify' ),
				),
				'supports' => array('title', 'editor', 'thumbnail', 'custom-fields'),
				'hierarchical' => false,
				'public' => true,
				'exclude_from_search' => true,
				'query_var' => true,
				'can_export' => true,
				'capability_type' => 'post'
			));
			register_taxonomy( $this->tax, array( $this->post_type ), array(
				'labels' => array(
					'name' => sprintf(__( '%s Categories', 'themify' ), $cpt['singular']),
					'singular_name' => sprintf(__( '%s Category', 'themify' ), $cpt['singular'])
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
				add_filter( 'attachment_fields_to_edit', array($this, 'attachment_fields_to_edit'), 10, 2 );
				add_action( 'edit_attachment', array($this, 'attachment_fields_to_save'), 10, 2 );

			}
		}

	function attachment_fields_to_edit( $form_fields, $post ) {

		if ( ! preg_match( '!^image/!', get_post_mime_type( $post->ID ) ) ) {
			return $form_fields;
		}

		$include = get_post_meta( $post->ID, 'themify_gallery_featured', true );

		$name = 'attachments[' . $post->ID . '][themify_gallery_featured]';

		$form_fields['themify_gallery_featured'] = array(
			'label' => __( 'Larger', 'themify' ),
			'input' => 'html',
			'helps' => __('Show larger image in the gallery.', 'themify'),
			'html'  => '<span class="setting"><label for="' . $name . '" class="setting"><input type="checkbox" name="' . $name . '" id="' . $name . '" value="featured" ' . checked( $include, 'featured', false ) . ' />' . '</label></span>',
		);

		return $form_fields;
	}

	function attachment_fields_to_save( $attachment_id ) {
		if( isset( $_REQUEST['attachments'][$attachment_id]['themify_gallery_featured'] ) && preg_match( '!^image/!', get_post_mime_type( $attachment_id ) ) ) {
			update_post_meta($attachment_id, 'themify_gallery_featured', 'featured');
		} else {
			update_post_meta($attachment_id, 'themify_gallery_featured', '');
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
		 * Trigger at the end of __construct
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
		 * Hides View Section/Team/Highlight/Testimonial/Gallery button in edit screen
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
			$columns['icon'] = __('Icon', 'themify');
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

				case 'icon' :
					the_post_thumbnail( array( 50, 50 ) );
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
		 * @param bool|string False does nothing, true goes to archive page, custom string sets custom location
		 * @param string Text to link
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
		 * Extract image IDs from gallery shortcode and try to return them as entries list
		 * @param string $field
		 * @return array|bool
		 * @since 1.0.0
		 */
		function get_gallery_images( $field = 'gallery_shortcode' ) {
			$gallery_shortcode = themify_get( $field );
			$image_ids = preg_replace( '#\[gallery(.*)ids="([0-9|,]*)"(.*)\]#i', '$2', $gallery_shortcode );

			$query_args = array(
				'post__in' => explode( ',', str_replace( ' ', '', $image_ids ) ),
				'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'numberposts' => -1,
				'orderby' => stripos( $gallery_shortcode, 'rand' ) ? 'rand': 'post__in',
				'order' => 'ASC'
			);
			$entries = get_posts( apply_filters( 'themify_theme_get_gallery_images', $query_args ) );

			if ( $entries ) {
				return $entries;
			}

			return false;
		}

		/**
		 * Return gallery post type entries
		 * @param string $field
		 * @return array|bool
		 * @since 1.0.0
		 */
		function get_gallery_posts( $field = 'gallery_posts' ) {
			$query_term = themify_get( $field );
			$query_args = array(
				'post_type' => 'gallery',
				'posts_per_page' => 15,
			);
			if ( '0' != $query_term ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'gallery-category',
						'field' => 'slug',
						'terms' => $query_term
					)
				);
			}
			$entries = get_posts( apply_filters( 'themify_theme_get_gallery_posts', $query_args ) );

			if ( $entries ) {
				return $entries;
			}

			return false;
		}

		/**
		 * Checks if there's a description and returns it, otherwise returns caption
		 * @param $image
		 * @return mixed
		 */
		function get_description( $image ) {
			if ( '' != $image->post_content ) {
				return $image->post_content;
			}
			return $image->post_excerpt;
		}

		/**
		 * Checks if there's a caption and returns it, otherwise returns description
		 * @param $image
		 * @return mixed
		 */
		function get_caption( $image ) {
			if ( '' != $image->post_excerpt ) {
				return $image->post_excerpt;
			}
			return $image->post_content;
		}

		/**
		 * Return slider parameters
		 * @param $post_id
		 * @return mixed
		 */
		function get_slider_params( $post_id ) {
			if ( $temp = get_post_meta( get_the_ID(), 'gallery_autoplay', true ) ) {
				$params['autoplay'] = $temp;
			} else {
				$params['autoplay'] = '4000';
			}
			if ( $temp = get_post_meta( get_the_ID(), 'gallery_transition', true ) ) {
				$params['transition'] = $temp;
			} else {
				$params['transition'] = '300';
			}
			if ( 'best-fit' == get_post_meta( $post_id, 'gallery_stretch', true ) ) {
				$params['bgmode'] = 'best-fit';
			} else {
				$params['bgmode'] = 'cover';
			}
			return $params;
		}

		/**************************************************************************************************
		 * Body Classes for Portfolio index and single
		 **************************************************************************************************/

		/**
		 * Changes condition to filter sidebar layout class
		 * @param bool $condition
		 * @return bool
		 */
		function sidebar_condition( $condition ) {
			return $condition || is_singular('gallery');
		}
		/**
		 * Returns modified sidebar layout class
		 * @param string $class Original body class
		 * @return string
		 */
		function sidebar( $class ) {
			global $themify;
			$class = $themify->layout;
			if ( is_singular( 'gallery' ) ) {
				$class = 'sidebar-none';
			}
			return $class;
		}
	}
}

/**************************************************************************************************
 * Initialize Type Class
 **************************************************************************************************/
$GLOBALS['themify_gallery'] = new Themify_Gallery();
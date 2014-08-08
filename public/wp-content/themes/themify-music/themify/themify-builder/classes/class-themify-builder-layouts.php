<?php
/**
 * Class Builder Layouts and Layout Parts
 * Handle Layouts and Parts logic
 * @package themifyBuilder
 */
class Themify_Builder_Layouts {
	public $layout;
	public $layout_part;
	public $post_types = array();

	function __construct() {
		$this->register_layout();

		// Builder write panel
		add_filter( 'themify_do_metaboxes', array( $this, 'layout_write_panels' ), 11 );
		add_filter( 'themify_post_types', array( $this, 'extend_post_types' ) );

		add_action( 'wp_ajax_tfb_load_layout', array( $this, 'load_layout_ajaxify' ), 10 );
		add_action( 'wp_ajax_tfb_set_layout', array( $this, 'set_layout_ajaxify' ), 10 );
		add_action( 'wp_ajax_tfb_custom_layout_form', array( $this, 'custom_layout_form_ajaxify' ), 10 );
		add_action( 'wp_ajax_tfb_save_custom_layout', array( $this, 'save_custom_layout_ajaxify' ), 10 );

		/* Layout Action */
		add_action( 'wp_ajax_tfb_duplicate_layout', array( $this, 'duplicate_layout_ajaxify' ), 10 );
		add_action( 'wp_ajax_tfb_delete_layout', array( $this, 'delete_layout_ajaxify' ), 10 );

		add_action( 'template_redirect', array( $this, 'template_singular_layout' ) );

		add_shortcode( 'themify_layout_part', array( $this, 'layout_part_shortcode' ) );

		// Restrict prebuilt Layouts from deletion and edit
		add_action( 'edit_post', array( $this, 'restrict_post_deletion' ), 10, 1);
		add_action( 'wp_trash_post', array( $this, 'restrict_post_deletion' ), 10, 1);
		add_action( 'before_delete_post', array( $this, 'restrict_post_deletion' ), 10, 1);
		add_action( 'admin_init', array( $this, 'block_layout_edit_screen' ) );

		// Quick Edit Links
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ) );
		add_action( 'admin_init', array( $this, 'duplicate_action' ) );

		// Silent Import Pre-built Layouts
		add_action( 'admin_init', array( $this, 'do_silent_import' ) );
		add_action( 'themify_builder_import_end', array( $this, 'import_layout_finished' ) );
	}

	/**
	 * Register CPT
	 */
	function register_layout() {
		if ( ! class_exists( 'CPT' ) ) {
			include_once THEMIFY_BUILDER_LIBRARIES_DIR . '/' . 'CPT.php';
		}

		// create a template custom post type
		$this->layout = new CPT( array(
			'post_type_name' => 'tbuilder_layout',
			'singular' => __('Layout', 'themify'),
			'plural' => __('Layouts', 'themify')
		), array(
			'supports' => array('title', 'thumbnail'),
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => false
		));

		// define the columns to appear on the admin edit screen
		$this->layout->columns(array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Title', 'themify'),
			'thumbnail' => __('Thumbnail', 'themify'),
			'author' => __('Author', 'themify'),
			'date' => __('Date', 'themify')
		));

		// populate the thumbnail column
		$this->layout->populate_column('thumbnail', array( $this, 'populate_column_layout_thumbnail' ) );

		// use "pages" icon for post type
		$this->layout->menu_icon('dashicons-admin-page');

		// create a template custom post type
		$this->layout_part = new CPT( array(
			'post_type_name' => 'tbuilder_layout_part',
			'singular' => __('Layout Part', 'themify'),
			'plural' => __('Layout Parts', 'themify'),
			'slug' => 'tbuilder-layout-part'
		), array(
			'supports' => array('title', 'thumbnail'),
			'exclude_from_search' => true,
			'show_in_nav_menus' => false,
			'show_in_menu' => false
		));

		// define the columns to appear on the admin edit screen
		$this->layout_part->columns(array(
			'cb' => '<input type="checkbox" />',
			'title' => __('Title', 'themify'),
			'shortcode' => __('Shortcode', 'themify'),
			'author' => __('Author', 'themify'),
			'date' => __('Date', 'themify')
		));

		// populate the thumbnail column
		$this->layout_part->populate_column('shortcode', array( $this, 'populate_column_layout_part_shortcode' ) );

		// use "pages" icon for post type
		$this->layout_part->menu_icon('dashicons-screenoptions');

		$this->set_post_type_var( $this->layout->post_type_name );
		$this->set_post_type_var( $this->layout_part->post_type_name );
	}

	function set_post_type_var( $name ) {
		array_push( $this->post_types, $name );
	}

	function populate_column_layout_thumbnail( $column, $post ) {
		echo get_the_post_thumbnail( $post->ID, 'thumbnail');
	}

	function populate_column_layout_part_shortcode( $column, $post ) {
		echo sprintf( '[themify_layout_part id=%d]', $post->ID );
		echo '<br/>';
		echo sprintf( '[themify_layout_part slug=%s]', $post->post_name );
	}

	/**
	 * Metabox Panel
	 * @param array $meta_boxes
	 */
	function layout_write_panels( $meta_boxes ) {
		global $post, $current_screen, $pagenow;

		if ( ! in_array( $pagenow, array( 'post-new.php', 'post.php' ) ) ) return $meta_boxes;

		$feature_img = array();
		$builder_meta = array(
				'name' 		=> 'page_builder',	
				'title' 		=> __( 'Themify Builder', 'themify' ),
				'description' => '',
				'type' 		=> 'page_builder',			
				'meta'		=> array()			
		);
		$current_page = isset( $_GET['post_type'] ) ? $_GET['post_type'] : get_post_type( (int) $_GET['post'] );
		if ( $this->layout->post_type_name == $current_page ) {
			$feature_img = array(
				  "name" 		=> "post_image",
				  "title" 		=> __('Layout Thumbnail', 'themify'),
				  "description" => '',
				  "type" 		=> "image",
				  "meta"		=> array()
			);
			$page_builder_options = array_merge( array( $feature_img, $builder_meta ) );
		} else {
			$page_builder_options = array( $builder_meta );
		}
		
		$types = array( $this->layout->post_type_name, $this->layout_part->post_type_name );
		$all_meta_boxes = array();
		foreach ( $types as $type ) {
			$all_meta_boxes[] = apply_filters( 'layout_write_panels_meta_boxes', array(
				'name'		=> __( 'Settings', 'themify' ),
				'id' 		=> 'layout-settings-builder',
				'options'	=> $page_builder_options,
				'pages'    	=> $type
			) );
		}
		return array_merge( $meta_boxes, $all_meta_boxes);
	}

	/**
	 * Includes this custom post to array of cpts managed by Themify
	 * @param Array
	 * @return Array
	 */
	function extend_post_types( $types ) {
		$cpts = array( $this->layout->post_type_name, $this->layout_part->post_type_name );
		return array_merge( $types, $cpts );
	}

	/**
	 * Load list of available Templates
	 */
	function load_layout_ajaxify() {
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );

		$posts = get_posts(array(
			'post_type' => $this->layout->post_type_name,
			'posts_per_page' => -1
		));

		include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-layout-lists.php';
		die();
	}

	/**
	 * Template redirect function
	 **/
	function do_theme_redirect( $url ) {
		global $post, $wp_query;

		if ( have_posts() ) {
			include( $url );
			die();
		} else {
			$wp_query->is_404 = true;
		}
	}

	/**
	 * Custom layout for Template / Template Part Builder Editor
	 */
	function template_singular_layout() {
		if ( is_singular( array( $this->layout->post_type_name, $this->layout_part->post_type_name ) ) ) {
			$templatefilename = 'template-builder-editor.php';
			
			$return_template = locate_template(
				array(
					trailingslashit( 'themify-builder/templates' ) . $templatefilename
				)
			);

			// Get default template
			if ( ! $return_template )
				$return_template = THEMIFY_BUILDER_TEMPLATES_DIR . '/' . $templatefilename;

			$this->do_theme_redirect( $return_template );
		}
	}

	/**
	 * Set template to current active builder
	 */
	function set_layout_ajaxify() {
		global $ThemifyBuilder;
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );
		$template_slug = $_POST['layout_slug'];
		$current_builder_id = (int) $_POST['current_builder_id'];
		$args = array(
			'name' => $template_slug,
			'post_type' => $this->layout->post_type_name,
			'post_status' => 'publish',
			'numberposts' => 1
		);
		$template = get_posts( $args );
		$response = array();

		if ( $template ) {
			$builder_data = get_post_meta( $template[0]->ID, $ThemifyBuilder->get_meta_key(), true );
			if ( ! empty( $builder_data ) ) {
				update_post_meta( $current_builder_id, $ThemifyBuilder->get_meta_key(), $builder_data );	
			}
			$response['status'] = 'success';
			$response['msg'] = '';
		} else {
			$response['status'] = 'failed';
			$response['msg'] = __('Something went wrong', 'themify');
		}

		wp_send_json( $response );
		die();
	}

	/**
	 * Layout Part Shortcode
	 * @param array $atts 
	 * @return string
	 */
	function layout_part_shortcode( $atts ) {
		global $ThemifyBuilder;
		extract( shortcode_atts( array(
			'id' => '',
			'slug' => ''
		), $atts ));

		$args = array(
			'post_type' => $this->layout_part->post_type_name,
			'post_status' => 'publish',
			'numberposts' => 1
		);
		if ( ! empty( $slug ) ) $args['name'] = $slug;
		if ( ! empty( $id ) ) $args['p'] = $id;
		$template = get_posts( $args );
		$output = '';

		if ( $template ) {
			$builder_data = get_post_meta( $template[0]->ID, $ThemifyBuilder->get_meta_key(), true );
			if ( ! empty( $builder_data ) ) {
				$output = $ThemifyBuilder->retrieve_template( 'builder-layout-part-output.php', array( 'builder_output' => $builder_data, 'builder_id' => $template[0]->ID ), '', '', false );
			}
		}

		return $output;
	}

	/**
	 * Render Layout Form in lightbox
	 */
	function custom_layout_form_ajaxify() {
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );
		$postid = (int) $_POST['postid'];

		$fields = array(
			array(
				'id' => 'layout_img_field',
				'type' => 'image',
				'label' => __('Image Preview', 'themify'),
				'class' => 'xlarge'
			),
			array(
				'id' => 'layout_title_field',
				'type' => 'text',
				'label' => __('Title', 'themify')
			)
		);
		
		include_once THEMIFY_BUILDER_INCLUDES_DIR . '/themify-builder-save-layout-form.php';
		die();
	}

	/**
	 * Save as Layout
	 */
	function save_custom_layout_ajaxify() {
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );
		global $ThemifyBuilder;
		$data = array();
		$response = array(
			'status' => 'failed',
			'msg' => __('Something went wrong', 'themify')
		);

		if ( isset( $_POST['form_data'] ) )
			parse_str( $_POST['form_data'], $data );

		if ( isset( $data['postid'] ) && ! empty( $data['postid'] ) ) {
			$template = get_post( $data['postid'] );
			$title = isset( $data['layout_title_field'] ) && ! empty( $data['layout_title_field'] ) ? sanitize_text_field( $data['layout_title_field'] ) : $template->post_title . ' Layout';
			$builder_data = get_post_meta( $template->ID, $ThemifyBuilder->get_meta_key(), true );
			if ( ! empty( $builder_data ) ) {
				$new_id = wp_insert_post(array(
					'post_status' => 'publish',
					'post_type' => $this->layout->post_type_name,
					'post_author' => $template->post_author,
					'post_title' => $title,
				));

				update_post_meta( $new_id, $ThemifyBuilder->get_meta_key(), $builder_data );

				// Set image as Featured Image
				if ( isset( $data['layout_img_field_attach_id'] ) && ! empty( $data['layout_img_field_attach_id'] ) )
					set_post_thumbnail( $new_id, $data['layout_img_field_attach_id'] );

				$response['status'] = 'success';
				$response['msg'] = '';
			}
		}

		wp_send_json( $response );
	}

	/**
	 * Duplicate Layout Ajax
	 * @return json
	 */
	function duplicate_layout_ajaxify() {
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );
		global $themifyBuilderDuplicate;
		$layout = (int) $_POST['layout_id'];
		$post = get_post( $layout );
		remove_action( 'edit_post', array( $this, 'restrict_post_deletion' ), 10, 1);
		$new_id = $themifyBuilderDuplicate->duplicate( $post );
		delete_post_meta( $new_id, '_themify_builder_prebuilt_layout' );
		add_action( 'edit_post', array( $this, 'restrict_post_deletion' ), 10, 1);
		wp_send_json_success();
	}

	/**
	 * Delete Layout Ajax
	 * @return json
	 */
	function delete_layout_ajaxify() {
		check_ajax_referer( 'tfb_load_nonce', 'nonce' );
		$layout = (int) $_POST['layout_id'];
		if ( Themify_Builder_Model::is_prebuilt_layout( $layout ) ) {
			wp_send_json_error( array( 'msg' => __('Cannot delete prebuilt layout', 'themify' ) ) );
		} else {
			wp_delete_post( $layout, true );
			wp_send_json_success();
		}
	}

	/**
	 * Restrict Pre-built Layouts from deletion
	 */
	function restrict_post_deletion( $postid ) {
		if ( Themify_Builder_Model::is_prebuilt_layout( $postid ) ) {
			wp_die( __('Pre-built Layouts and Parts can\'t be editable or delete-able. Please use duplicate feature to have same layout.', 'themify') );
			exit;
		}
	}

	/**
	 * Block users from accessing Pre-built Layouts Edit Screen
	 */
	function block_layout_edit_screen() {
		global $pagenow;

		if ( 'post.php' == $pagenow && ( isset( $_GET['action'] ) 
			&& 'edit' == $_GET['action'] ) 
			&& ( $this->layout->post_type_name == get_post_type( $_GET['post'] ) || $this->layout_part->post_type_name == get_post_type( $_GET['post'] ) ) ) {

			if ( Themify_Builder_Model::is_prebuilt_layout( $_GET['post'] ) ) {
				wp_die( __('Pre-built Layouts and Parts can\'t be editable or delete-able. Please use duplicate feature to have same layout.', 'themify') );
				exit;
			}

		}
	}

	/**
	 * Add custom link actions in post rows
	 * @param array $actions 
	 * @return array
	 */
	function post_row_actions( $actions ) {
		global $post;
		if ( ( $this->layout->post_type_name == get_post_type() ) || ( $this->layout_part->post_type_name == get_post_type() ) ) {
			$actions['themify-builder-duplicate'] = sprintf( '<a href="%s">%s</a>', wp_nonce_url( admin_url( 'post.php?post=' . $post->ID . '&action=duplicate_tbuilder' ), 'duplicate_themify_builder' ), __('Duplicate', 'themify') );
			$actions['themify-builder'] = sprintf( '<a href="%s" target="_blank">%s</a>', get_permalink( $post->ID ) . '#builder_active', __('Themify Builder', 'themify' ));
			if ( Themify_Builder_Model::is_prebuilt_layout( $post->ID ) ) {
				unset( $actions['edit'] );
				unset( $actions['inline hide-if-no-js'] );
				unset( $actions['trash'] );
				unset( $actions['themify-builder'] );
			}
		}
		return $actions;
	}

	/**
	 * Duplicate Post in Admin Edit page
	 */
	function duplicate_action() {
		if ( isset( $_GET['action'] ) && 'duplicate_tbuilder' == $_GET['action'] && wp_verify_nonce($_GET['_wpnonce'], 'duplicate_themify_builder') ) {
			global $themifyBuilderDuplicate;
			$postid = (int) $_GET['post'];
			$layout = get_post( $postid );

			// Disable post restrict edit
			remove_action( 'edit_post', array( $this, 'restrict_post_deletion' ), 10, 1);

			$new_id = $themifyBuilderDuplicate->duplicate( $layout );
			delete_post_meta( $new_id, '_themify_builder_prebuilt_layout' );

			// Enable post restrict edit
			add_action( 'edit_post', array( $this, 'restrict_post_deletion' ), 10, 1);

			wp_redirect( admin_url( 'edit.php?post_type=' . get_post_type( $postid ) ) );
			exit;
		}
	}

	/**
	 * Silent Import Layouts
	 */
	function do_silent_import() {
		if ( ! Themify_Builder_Model::find_layout_updates() ) return;

		require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
		require_once ABSPATH . 'wp-admin/includes/post.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		require_once THEMIFY_BUILDER_CLASSES_DIR . '/class-themify-builder-layouts-importer.php';
		$load_file = THEMIFY_BUILDER_INCLUDES_DIR . '/data/layouts.xml.gz';
		$cache_dir = self::get_cache_dir();
		$extract_file = $cache_dir['path'] . 'layouts.xml';

		$this->uncompress_gzip( $load_file, $extract_file );

		if ( file_exists( $extract_file ) ) {
			$import = new Themify_Builder_Layouts_Importer();
			$import->fetch_attachments = true;
			$import->import( $extract_file );
		}
	}

	/**
	 * Set Layout version after import finished
	 */
	function import_layout_finished() {
		Themify_Builder_Model::set_current_layouts_version( THEMIFY_BUILDER_LAYOUTS_VERSION );

		// Delete cache layouts
		$cache_dir = self::get_cache_dir();
		$file = $cache_dir['path'] . 'layouts.xml';
		if ( file_exists( $file ) ) {
			unlink( $file );
		}
	}

	/**
	 * Get layouts cache dir
	 * @return array
	 */
	static public function get_cache_dir() {
		$upload_dir = wp_upload_dir();

		$dir_info = array(
			'path'   => $upload_dir['basedir'] . '/themify-builder/',
			'url'    => $upload_dir['baseurl'] . '/themify-builder/'
		);

		if( ! file_exists( $dir_info['path'] ) ) {
			mkdir( $dir_info['path'] );
		}

		return $dir_info;
	}

	/**
	 * Uncompress GZIP file
	 * @param string $srcName 
	 * @param string $dstName 
	 */
	function uncompress_gzip($srcName, $dstName) {
		$sfp = gzopen( $srcName, "rb" );
		$fp = fopen($dstName, "w" );

		while ( ! gzeof( $sfp ) ) {
			$string = gzgets( $sfp, 8192 );
			fwrite( $fp, $string, strlen( $string ) );
		}
		gzclose($sfp);
		fclose($fp);
	}
}
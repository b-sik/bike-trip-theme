<?php

/**
 * Bootstrap on WordPress functions and definitions
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package     WordPress
 * @subpackage  Bootstrap 5.1.3
 * @autor       Babobski
 */

define( 'BOOTSTRAP_VERSION', '5.1.3' );
define( 'BOOTSTRAP_ICON_VERSION', '1.8.2' );

/*
	 ========================================================================================================================

	01. Add language support to theme

	======================================================================================================================== */

add_action( 'after_setup_theme', 'my_theme_setup' );

function my_theme_setup() {
	 load_theme_textdomain( 'wp_babobski', get_template_directory() . '/language' );
}

/*
	 ========================================================================================================================

	02. Required external files

	======================================================================================================================== */

require_once 'external/bootstrap-utilities.php';
require_once 'external/bs5navwalker.php';

/*
	 ========================================================================================================================

    03. Add html 5 support to WordPress elements

	======================================================================================================================== */

add_theme_support(
	'html5',
	array(
		'comment-list',
		'search-form',
		'comment-form',
		'gallery',
		'caption',
	)
);

/*
	 ========================================================================================================================

	04. Theme specific settings

	======================================================================================================================== */

add_theme_support( 'post-thumbnails' );

// add_image_size( 'name', width, height, crop true|false );

register_nav_menus(
	array(
		'primary' => 'Primary Navigation',
	)
);

/*
	 ========================================================================================================================

	05. Actions and Filters

	======================================================================================================================== */

add_action( 'wp_enqueue_scripts', 'bootstrap_script_init' );

$BsWp = new BsWp();
add_filter( 'body_class', array( $BsWp, 'add_slug_to_body_class' ) );

/*
	 ========================================================================================================================

	06. Custom Post Types - include custom post types and taxonomies here e.g.

	e.g. require_once( 'custom-post-types/your-custom-post-type.php' );

	======================================================================================================================== */



/*
	 ========================================================================================================================

	07. Scripts

	======================================================================================================================== */

/**
 * Add scripts via wp_head()
 *
 * @return void
 * @author Keir Whitaker
 */
if ( ! function_exists( 'bootstrap_script_init' ) ) {
	function bootstrap_script_init() {
		// Get theme version number (located in style.css)
		$theme = wp_get_theme();

		wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.bundle.min.js', array( 'jquery' ), BOOTSTRAP_VERSION, true );
		wp_enqueue_script( 'site', get_template_directory_uri() . '/js/site.js', array( 'jquery', 'bootstrap' ), $theme->get( 'Version' ), true );

		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), BOOTSTRAP_VERSION, 'all' );
		wp_enqueue_style( 'bootstrap_icons', get_template_directory_uri() . '/css/bootstrap-icons.css', array(), BOOTSTRAP_ICON_VERSION, 'all' );
		wp_enqueue_style( 'screen', get_template_directory_uri() . '/style.css', array(), $theme->get( 'Version' ), 'screen' );
	}
}

/*
	 ========================================================================================================================

	08. Security & cleanup wp admin

	======================================================================================================================== */

// remove wp version
function theme_remove_version() {
	return '';
}

add_filter( 'the_generator', 'theme_remove_version' );

// remove default footer text
function remove_footer_admin() {
	echo '';
}

add_filter( 'admin_footer_text', 'remove_footer_admin' );

// remove WordPress logo from adminbar
function wp_logo_admin_bar_remove() {
	global $wp_admin_bar;

	/* Remove their stuff */
	$wp_admin_bar->remove_menu( 'wp-logo' );
}

add_action( 'wp_before_admin_bar_render', 'wp_logo_admin_bar_remove', 0 );

// Remove default Dashboard widgets
if ( ! function_exists( 'disable_default_dashboard_widgets' ) ) {
	function disable_default_dashboard_widgets() {
		// remove_meta_box('dashboard_right_now', 'dashboard', 'core');
		remove_meta_box( 'dashboard_activity', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );

		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );
	}
}
add_action( 'admin_menu', 'disable_default_dashboard_widgets' );

remove_action( 'welcome_panel', 'wp_welcome_panel' );

// Disable the emoji's
if ( ! function_exists( 'disable_emojis' ) ) {
	function disable_emojis() {
		 remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		// Remove from TinyMCE
		add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	}
}
add_action( 'init', 'disable_emojis' );

// Filter out the tinymce emoji plugin.
function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

add_action( 'admin_head', 'custom_logo_guttenberg' );

if ( ! function_exists( 'custom_logo_guttenberg' ) ) {
	function custom_logo_guttenberg() {
		 echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo( 'stylesheet_directory' ) .
			'/css/admin-custom.css?v=1.0.0" />';
	}
}

/*
	 ========================================================================================================================

	09. Disabeling Guttenberg

	======================================================================================================================== */

// Optional disable guttenberg block editor
// add_filter( 'use_block_editor_for_post', '__return_false' );


// Remove Gutenberg Block Library CSS from loading on the frontend
// function smartwp_remove_wp_block_library_css() {
// wp_dequeue_style('wp-block-library');
// wp_dequeue_style('wp-block-library-theme');
// wp_dequeue_style('wc-block-style'); // Remove WooCommerce block CSS
// wp_dequeue_style( 'storefront-gutenberg-blocks' ); // Storefront
// }
// add_action('wp_enqueue_scripts', 'smartwp_remove_wp_block_library_css', 100);

/*
	 ========================================================================================================================

	10. Custom login

	======================================================================================================================== */

// Add custom css
if ( ! function_exists( 'my_custom_login' ) ) {
	function my_custom_login() {
		echo '<link rel="stylesheet" type="text/css" href="' . get_bloginfo( 'stylesheet_directory' ) . '/css/custom-login-style.css?v=1.0.0" />';
	}
}
add_action( 'login_head', 'my_custom_login' );

// Link the logo to the home of our website
if ( ! function_exists( 'my_login_logo_url' ) ) {
	function my_login_logo_url() {
		return get_bloginfo( 'url' );
	}
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

// Change the title text
if ( ! function_exists( 'my_login_logo_url_title' ) ) {
	function my_login_logo_url_title() {
		return get_bloginfo( 'name' );
	}
}
add_filter( 'login_headertext', 'my_login_logo_url_title' );


/*
	 ========================================================================================================================

	11. Comments

	======================================================================================================================== */

/**
 * Custom callback for outputting comments
 *
 * @return void
 * @author Keir Whitaker
 */
if ( ! function_exists( 'bootstrap_comment' ) ) {
	function bootstrap_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		?>
		<?php if ( $comment->comment_approved == '1' ) : ?>
			<li class="row">
				<div class="col-4 col-md-2">
					<?php echo get_avatar( $comment ); ?>
				</div>
				<div class="col-8 col-md-10">
					<h4><?php comment_author_link(); ?></h4>
					<time><a href="#comment-<?php comment_ID(); ?>" pubdate><?php comment_date(); ?> at <?php comment_time(); ?></a></time>
					<?php comment_text(); ?>
				</div>
			<?php
		endif;
	}
}

function osm_shortcode( $gpx_filename, $height, $all = false, $file_color_list = 'none' ) {
	$upload_dir  = wp_upload_dir();
	$gpx_dir_url = $upload_dir['baseurl'] . '/2022/GPX/';

	$file_list = '';
	if ( $all ) {
		$file_list = $gpx_filename;
	} else {
		$file_list = $gpx_dir_url . $gpx_filename;
	}

	return '[osm_map_v3 map_center="autolat,autolon" zoom="autozoom" width="100%" height="' . $height . '" file_list="' . $file_list . '" file_color_list="' . $file_color_list . '" file_title="' . $gpx_filename . '"]';
};

function allowed_block_types( $block_editor_context, $editor_context ) {
	return array( 'core/paragraph', 'core/heading', 'core/gallery', 'core/table', 'core/list', 'core/quote', 'core/video', 'core/separator' );
}

add_filter( 'allowed_block_types_all', 'allowed_block_types', 10, 2 );

if ( function_exists( 'acf_add_local_field_group' ) ) :

	$front_page_id = get_page_by_title( 'Front Page' )->ID;

	acf_add_local_field_group(
		array(
			'key'                   => 'group_62a550dfcfe36',
			'title'                 => 'Bike Stats',
			'fields'                => array(
				array(
					'key'               => 'field_62a550eff6bfe',
					'label'             => 'Date',
					'name'              => 'date',
					'type'              => 'date_picker',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'show_in_graphql'   => 1,
					'display_format'    => 'm/d/Y',
					'return_format'     => 'm/d/Y',
					'first_day'         => 1,
				),
				array(
					'key'               => 'field_62a5516cf6c00',
					'label'             => 'Arguments',
					'name'              => 'arguments',
					'type'              => 'number',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'show_in_graphql'   => 1,
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'min'               => '',
					'max'               => '',
					'step'              => '',
				),
				array(
					'key'               => 'field_62a63da7601d9',
					'label'             => 'Miles and Elevation',
					'name'              => 'miles_and_elevation',
					'type'              => 'group',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'layout'            => 'block',
					'sub_fields'        => array(
						array(
							'key'               => 'field_62a63db8601da',
							'label'             => 'Rest day',
							'name'              => 'rest_day',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'show_in_graphql'   => 1,
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 0,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
						array(
							'key'               => 'field_62a63de0601db',
							'label'             => 'Miles',
							'name'              => 'miles',
							'type'              => 'number',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_62a63db8601da',
										'operator' => '!=',
										'value'    => '1',
									),
								),
							),
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'show_in_graphql'   => 1,
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'min'               => '',
							'max'               => '',
							'step'              => '',
						),
						array(
							'key'               => 'field_62bf3eeeff244',
							'label'             => 'Elevation gain',
							'name'              => 'elevation_gain',
							'type'              => 'number',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_62a63db8601da',
										'operator' => '!=',
										'value'    => '1',
									),
								),
							),
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'min'               => '',
							'max'               => '',
							'step'              => '',
						),
						array(
							'key'               => 'field_62bf3f0fff245',
							'label'             => 'Elevation loss',
							'name'              => 'elevation_loss',
							'type'              => 'number',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_62a63db8601da',
										'operator' => '!=',
										'value'    => '1',
									),
								),
							),
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'min'               => '',
							'max'               => '',
							'step'              => '',
						),
						array(
							'key'               => 'field_62bf400f7bd0c',
							'label'             => 'Flats',
							'name'              => 'flats',
							'type'              => 'number',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_62a63db8601da',
										'operator' => '!=',
										'value'    => '1',
									),
								),
							),
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'min'               => '',
							'max'               => '',
							'step'              => '',
						),
					),
				),
				array(
					'key'               => 'field_62be7798ccc2a',
					'label'             => 'GPX FIlename',
					'name'              => 'gpx_filename',
					'type'              => 'text',
					'instructions'      => 'Include ".gpx"',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'placeholder'       => '',
					'prepend'           => '',
					'append'            => '',
					'maxlength'         => '',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'post',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		)
	);

	acf_add_local_field_group(
		array(
			'key'                   => 'group_62bdfc5bb8e06',
			'title'                 => 'Front Page',
			'fields'                => array(
				array(
					'key'               => 'field_62bdfc694df61',
					'label'             => 'Header Image',
					'name'              => 'header_image',
					'type'              => 'image',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'return_format'     => 'url',
					'preview_size'      => 'large',
					'library'           => 'all',
					'min_width'         => '',
					'min_height'        => '',
					'min_size'          => '',
					'max_width'         => '',
					'max_height'        => '',
					'max_size'          => '',
					'mime_types'        => '',
				),
				array(
					'key'               => 'field_62c1a4336cbc4',
					'label'             => 'Header Overlay Color',
					'name'              => 'header_overlay_color',
					'type'              => 'color_picker',
					'instructions'      => '',
					'required'          => 0,
					'conditional_logic' => 0,
					'wrapper'           => array(
						'width' => '',
						'class' => '',
						'id'    => '',
					),
					'default_value'     => '',
					'enable_opacity'    => 0,
					'return_format'     => 'array',
				),
			),
			'location'              => array(
				array(
					array(
						'param'    => 'page',
						'operator' => '==',
						'value'    => strval( $front_page_id ),
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'show_in_rest'          => 0,
		)
	);

endif;

<?php
/**
 * Config methods for Bike Trip Theme.
 *
 * @category Class
 * @package  BikeTripTheme
 * @author   Brian Siklinski
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class BTT_Config {
	/**
	 * Allowed block types.
	 *
	 * @var array @allowed_block_types
	 */
	public array $allowed_block_types = array( 'core/paragraph', 'core/heading', 'core/gallery', 'core/table', 'core/list', 'core/quote', 'core/video', 'core/separator' );

	/**
	 * Construct.
	 */
	public function __construct() {

	}

	/**
	 * Init.
	 */
	public function init() {
		// Manually make all image blocks media links to work with Lightbox plugin.
		$filter_image_block = new Post_Content();
		$filter_image_block->init();

		add_filter( 'allowed_block_types_all', array( $this, 'allowed_block_types' ), 10, 2 );
		add_filter( 'script_loader_tag', array( $this, 'add_type_attribute' ), 10, 3 );

		$this->add_acf_fields_groups();
	}

	/**
	 * Front Page ID.
	 * This is used as a config post for attaching meta.
	 *
	 * @return int
	 */
	public static function get_config_post_id() {
		$page = get_page_by_title( 'Front Page' );
		return $page->ID;
	}

	/**
	 * Callback to set allowed block types.
	 *
	 * @param mixed $block_editor_context Block editor context.
	 * @param mixed $editor_context Editor context.
	 *
	 * @return array.
	 */
	public function allowed_block_types( $block_editor_context, $editor_context ) {
		return $this->allowed_block_types;
	}

	/** phpcs:ignore
	 * Add module type to script.
	 *
	 * @link https://stackoverflow.com/questions/58931144/enqueue-javascript-with-type-module
	 */
	public function add_type_attribute( $tag, $handle, $src ) {
		// if not your script, do nothing and return original $tag.
		if ( 'site' !== $handle ) {
			return $tag;
		}
		// change the script tag by adding type="module" and return it.
        $tag = '<script type="module" src="' . esc_url( $src ) . '"></script>'; // phpcs:ignore
		return $tag;
	}

	/**
	 * ACF field groups.
	 */
	public function add_acf_fields_groups() {
		if ( function_exists( 'acf_add_local_field_group' ) ) :

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
							'display_format'    => 'm/d/Y',
							'return_format'     => 'm/d/Y',
							'first_day'         => 1,
						),
						array(
							'key'               => 'field_62cf7fa4cbb0a',
							'label'             => 'Day Number',
							'name'              => 'day_number',
							'type'              => 'number',
							'instructions'      => 'If this is for multiple days, enter the start date here',
							'required'          => 1,
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
							'min'               => '',
							'max'               => '',
							'step'              => '',
						),
						array(
							'key'               => 'field_62eecf6f68a92',
							'label'             => 'Multiple days',
							'name'              => 'multiple_days',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 0,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
						array(
							'key'               => 'field_62eecf8268a93',
							'label'             => 'End Day Number',
							'name'              => 'end_day_number',
							'type'              => 'number',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_62eecf6f68a92',
										'operator' => '==',
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
							'key'               => 'field_62d498a638c37',
							'label'             => 'Weather',
							'name'              => 'weather',
							'type'              => 'text',
							'instructions'      => '',
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
						array(
							'key'               => 'field_62d498b338c38',
							'label'             => 'Locations',
							'name'              => 'locations',
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
									'key'               => 'field_62d4996138c39',
									'label'             => 'Single Location',
									'name'              => 'single',
									'type'              => 'true_false',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'message'           => '',
									'default_value'     => 0,
									'ui'                => 0,
									'ui_on_text'        => '',
									'ui_off_text'       => '',
								),
								array(
									'key'               => 'field_62d499cd38c3a',
									'label'             => 'Starting location',
									'name'              => 'start',
									'type'              => 'text',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => '',
									'placeholder'       => 'Nowhere, OH',
									'prepend'           => '',
									'append'            => '',
									'maxlength'         => '',
								),
								array(
									'key'               => 'field_62d49a0138c3b',
									'label'             => 'Ending Location',
									'name'              => 'end',
									'type'              => 'text',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => array(
										array(
											array(
												'field'    => 'field_62d4996138c39',
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
									'placeholder'       => 'Somewhere, CA',
									'prepend'           => '',
									'append'            => '',
									'maxlength'         => '',
								),
							),
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
							'key'               => 'field_62c9e8be22957',
							'label'             => 'Post Excerpt Length',
							'name'              => 'post_excerpt_length',
							'type'              => 'number',
							'instructions'      => 'Number of characters before \'...\'',
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
							'min'               => '',
							'max'               => '',
							'step'              => '',
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'page',
								'operator' => '==',
								'value'    => strval( $this->get_config_post_id() ),
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
	}
}

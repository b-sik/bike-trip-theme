<?php
/**
 * Process and style post content blocks for Bike Trip Theme.
 *
 * @category Class
 * @package  BikeTripTheme
 * @author   Brian Siklinski
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Process and output block-based post content with custom classes, styles, and layouts.
 */
class Post_Content {
	/**
	 * Construct.
	 */
	public function __construct() {

	}

	/**
	 * Display content with layouts.
	 *
	 * @return void
	 */
	public function single_post_content() {

		global $post;

		$blocks      = $this->parse_blocks_ignore_empty_blocks( $post->post_content );
		$block_queue = array();

		$total_blocks = count( $blocks );
		$count        = 1;

		foreach ( $blocks as $block ) {
			$blocks_in_queue = array_column( $block_queue, 'blockName' );

			// make sure to echo last block.
			if ( $total_blocks === $count ) {
				array_push( $block_queue, $block );
				$this->output_blocks_with_layouts( $block_queue );
			} elseif ( 'core/paragraph' === $block['blockName'] && in_array( 'core/paragraph', $blocks_in_queue, true ) ) {
				$this->output_blocks_with_layouts( $block_queue );
				$block_queue = array( $block );
			} else {
				array_push( $block_queue, $block );
			}

			$count++;
		}
	}

	/**
	 * Arrange blocks for output.
	 *
	 * @param array $blocks Collection of blocks.
	 * @return array
	 */
	public function process_block_queue( $blocks ) {
		$text_blocks = array();
		$images      = array();
		$video       = array();

		foreach ( $blocks as $block ) {
			switch ( $block['blockName'] ) {
				case 'core/paragraph':
					$text_blocks[] = $block;
					break;
				case 'core/quote':
					$text_blocks[] = $block;
					break;
				case 'core/list':
					$text_blocks[] = $block;
					break;
				case 'core/table':
					$text_blocks[] = $block;
					break;
				case 'core/gallery':
					$images = $block['innerBlocks'];
					break;
				case 'core/video':
					$video = $block;
					break;
			}
		}

		return array(
			'text-blocks' => $text_blocks,
			'images'      => $images,
			'video'       => $video,
		);
	}

	/**
	 * Arrange blocks in different layouts and echo output.
	 *
	 * @param array $blocks Queue of blocks.
	 * @return void
	 */
	public function output_blocks_with_layouts( $blocks ) {
		$blocks = $this->process_block_queue( $blocks );

		$text_blocks = $blocks['text-blocks'];
		$images      = $blocks['images'];
		$video       = $blocks['video'];

		ob_start();

		echo '<div class="row my-4">';
		if ( count( $images ) === 1 ) {
			switch ( $this->get_image_orientation( $images[0] ) ) {
				case 'landscape':
					$this->echo_blocks( $text_blocks, 'col-12' );
					$this->echo_block( $images[0], 'col-12' );
					$this->echo_block( $video );
					break;
				case 'portrait':
					$this->echo_blocks( $text_blocks, 'col-12 col-md-6' );
					$this->echo_block( $images[0], 'col-12 col-md-6 my-1' );
					$this->echo_block( $video );
					break;
			}
		} elseif ( count( $images ) === 2 ) {
			if ( $this->all_images_same_orientation( $images ) ) {
				$this->echo_blocks( $text_blocks, 'col-12' );
				$this->echo_block( $images[0], 'col-12 col-md-6 my-1' );
				$this->echo_block( $images[1], 'col-12 col-md-6 my-1' );
				$this->echo_block( $video );

			} else {
				$images = $this->order_landscape_last( $images );
				$this->echo_blocks( $text_blocks, 'col-12 col-md-6 my-1' );
				$this->echo_block( $images[0], 'col-12 col-md-6 my-1' );
				$this->echo_block( $images[1], 'col-12 my-1 mt-md-5' );
				$this->echo_block( $video );
			}
		} elseif ( count( $images ) === 3 ) {
			$this->echo_blocks( $text_blocks, 'col-12' );
			$this->echo_blocks( $images, 'col-12 col-md-4 my-1' );
			$this->echo_block( $video );
		} elseif ( count( $images ) === 4 ) {
			$this->echo_blocks( $text_blocks, 'col-12' );
			$this->echo_blocks( $images, 'col-6 my-1' );
			$this->echo_block( $video );
		} else {
			$this->echo_blocks( $text_blocks, 'col-12' );
			$this->echo_blocks( $images, 'col-6 col-md-4 my-1' );
			$this->echo_block( $video );
		}
		echo '</div>'; // close row.

		echo ob_get_clean(); // phpcs:ignore
	}

	/**
	 * Output single block.
	 *
	 * @param object $block Block.
	 * @param string $wrapper_class (optional) Wrapper classes.
	 * @param string $wrapper_style (optional) Wrapper styles.
	 *
	 * @return void
	 */
	public function echo_block( $block, $wrapper_class = '', $wrapper_style = '' ) {
		if ( empty( $block ) ) {
			return;
		}

		$wrapper_class = $this->apply_block_specific_styles( $block['blockName'], $wrapper_class );

		echo '<div style="' . esc_attr( $wrapper_style ) . '" class="' . esc_attr( $wrapper_class ) . '">';
		echo wp_kses_post( apply_filters( 'the_content', render_block( $block ) ) );
		echo '</div>';
	}

	/**
	 * Universal styles for a block.
	 *
	 * @param string $block_name Block name.
	 * @param string $wrapper_class Classes already passed into `echo_block`.
	 *
	 * @return string Updated wrapper classes.
	 */
	public function apply_block_specific_styles( $block_name, $wrapper_class ) {
		if ( 'core/quote' === $block_name ) {
			$wrapper_class .= ' my-auto';
		} elseif ( 'core/video' === $block_name ) {
			$wrapper_class .= ' col-10 offset-1 mt-5';
		}

		return $wrapper_class;
	}

	/**
	 * Output multiple blocks.
	 *
	 * @param array  $blocks Blocks.
	 * @param string $wrapper_class (optional) Wrapper classes.
	 * @param string $wrapper_style (optional) Wrapper styles.
	 *
	 * @return void
	 */
	public function echo_blocks( $blocks, $wrapper_class = '', $wrapper_style = '' ) {
		foreach ( $blocks as $block ) {
			$this->echo_block( $block, $wrapper_class, $wrapper_style );
		}
	}

	/**
	 * Takes an array of image blocks and puts landscape photos at end.
	 *
	 * @param array $images Image blocks.
	 * @return array
	 */
	public function order_landscape_last( $images ) {
		$non_landscapes = array();
		$landscapes     = array();

		foreach ( $images as $image ) {
			if ( 'landscape' === $this->get_image_orientation( $image ) ) {
				$landscapes[] = $image;
			} else {
				$non_landscapes[] = $image;
			}
		}

		return array_merge( $non_landscapes, $landscapes );
	}

	/**
	 * Checks if an array of image blocks contains any landscape images.
	 *
	 * @param array $images Image blocks.
	 * @return bool
	 */
	public function has_landscape_image( $images ) {
		foreach ( $images as $image ) {
			if ( 'landscape' === $this->get_image_orientation( $image ) ) {
				return true;
			}
			return false;
		}
	}

	/**
	 * Returns an array of strings of image orientations.
	 *
	 * @param array $images Image blocks.
	 * @return array
	 */
	public function get_image_orientations( $images ) {
		$image_orientations = array();
		foreach ( $images as $image ) {
			array_push( $image_orientations, $this->get_image_orientation( $image ) );
		}
		return $image_orientations;
	}

	/**
	 * Checks if all images are different orientations.
	 * Mostly used to check if three different images are landscape, portrait, and square.
	 *
	 * @param array $images Image blocks.
	 * @return bool
	 */
	public function no_images_same_orientation( $images ) {
		$image_orientations = $this->get_image_orientations( $images );
		if ( count( $images ) === count( array_unique( $image_orientations ) ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Checks if all images are the same orientation.
	 *
	 * @param array $images Image blocks.
	 * @return bool
	 */
	public function all_images_same_orientation( $images ) {
		$image_orientations = $this->get_image_orientations( $images );
		return count( array_unique( $image_orientations ) ) === 1;
	}

	/**
	 * Checks orientation of single image block.
	 *
	 * @param object $image Image block.
	 * @return string
	 */
	public function get_image_orientation( $image ) {
		$dimensions = $this->get_image_dimensions( $image );

		if ( $dimensions['w'] > $dimensions['h'] ) {
			return 'landscape';
		} elseif ( $dimensions['w'] === $dimensions['h'] ) {
			return 'square';
		} else {
			return 'portrait';
		}
	}

	/**
	 * Get dimensions of single image block.
	 *
	 * @param object $image Image block.
	 * @return array
	 */
	public function get_image_dimensions( $image ) {
		$str = $this->get_string_between( $image['innerHTML'], '<img src="', '.jpeg' );

		$explode_x = explode( 'x', $str );

		$height = array_pop( $explode_x );
		$width  = array_pop( explode( '-', $explode_x[0] ) );

		return array(
			'w' => $width,
			'h' => $height,
		);
	}

	/**
	 * Get portion of string between a start and end.
	 * https://stackoverflow.com/a/21804537/17378938
	 *
	 * @param string $string Full string.
	 * @param string $start Beginning string match.
	 * @param string $end Ending string match.
	 *
	 * @return string
	 */
	public function get_string_between( $string, $start, $end ) {
		$string = ' ' . $string;
		$ini    = strpos( $string, $start );
		if ( 0 === $ini ) {
			return '';
		}
		$ini += strlen( $start );
		$len  = strpos( $string, $end, $ini ) - $ini;
		return substr( $string, $ini, $len );
	}

	/**
	 * Check if null block.
	 * https://github.com/WordPress/gutenberg/issues/15040#issuecomment-484627498
	 *
	 * @param object $block Block.
	 * @return bool
	 */
	public function is_non_empty_block( $block ) {
		return ! ( null === $block['blockName'] && empty( trim( $block['innerHTML'] ) ) );
	}

	/**
	 * Remove null blocks.
	 * https://github.com/WordPress/gutenberg/issues/15040#issuecomment-484627498
	 *
	 * @param string $content Post content.
	 * @return array Parsed blocks
	 */
	public function parse_blocks_ignore_empty_blocks( $content ) {
		return array_filter( parse_blocks( $content ), array( $this, 'is_non_empty_block' ) );
	}
}

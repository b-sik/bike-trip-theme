<?php
/**
 * Process and style post content blocks for Bike Trip Theme.
 *
 * @category Class
 * @package  BikeTripTheme
 * @author   Brian Siklinski
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class Post_Content {
	/**
	 * Construct.
	 */
	public function __construct() {
	}

	/**
	 * Display content with layout.
	 */
	public function echo_content() {

		global $post;

		$blocks      = $this->parse_blocks_ignore_empty_blocks( $post->post_content );
		$block_queue = array();

		foreach ( $blocks as $block ) {
			$blocks_in_queue = array_column( $block_queue, 'blockName' );

			if ( 'core/paragraph' === $block['blockName'] && in_array( 'core/paragraph', $blocks_in_queue, true ) ) {
				$this->echo_blocks( $block_queue );
				$block_queue = array( $block );
			} else {
				array_push( $block_queue, $block );
			}
		}
	}

	/**
	 * Echo blocks.
	 */
	public function echo_blocks( $blocks ) {
		// var_dump( $blocks );
		$text_blocks = array();
		$images      = array();
		$video       = array();
		$quote       = array();

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

		if ( count( $images ) === 1 ) {
			switch ( $this->get_image_orientation( $images[0] ) ) {
				case 'landscape':
					echo '<div class="row my-4">';
					$this->echo_block( $quote, 'col-12' );
					$this->echo_block_array( $text_blocks, 'col-12' );
					$this->echo_block( $images[0], 'col-12' );
					$this->echo_video( $video );
					echo '</div>';
					break;
				case 'portrait':
					echo '<div class="row my-4">';
					$this->echo_block( $quote, 'col-12' );
					$this->echo_block_array( $text_blocks, 'col-12 col-md-6' );
					$this->echo_block( $images[0], 'col-12 col-md-6 my-1' );
					$this->echo_video( $video );
					echo '</div>';
					break;
			}
		} elseif ( count( $images ) === 2 ) {
			if ( $this->all_images_same_orientation( $images ) ) {
				echo '<div class="row my-4">';
				$this->echo_block( $quote, 'col-12' );
				$this->echo_block_array( $text_blocks, 'col-12' );
				$this->echo_block( $images[0], 'col-12 col-md-6 my-1' );
				$this->echo_block( $images[1], 'col-12 col-md-6 my-1' );
				$this->echo_video( $video );
				echo '</div>';
			} else {
				$images = $this->order_landscape_last( $images );
				echo '<div class="row my-4">';
				$this->echo_block( $quote, 'col-12' );
				$this->echo_block_array( $text_blocks, 'col-12 col-md-6 my-1' );
				$this->echo_block( $images[0], 'col-12 col-md-6 my-1' );
				$this->echo_block( $images[1], 'col-12 my-1 mt-md-5' );
				$this->echo_video( $video );
				echo '</div>';
			}
		} elseif ( count( $images ) === 3 ) {
			echo '<div class="row my-4">';
			$this->echo_block( $quote, 'col-12' );
			$this->echo_block_array( $text_blocks, 'col-12' );
			$this->echo_block_array( $images, 'col-12 col-md-4 my-1' );
			$this->echo_video( $video );
			echo '</div>';
		} elseif ( count( $images ) === 4 ) {
			echo '<div class="row my-4">';
			$this->echo_block( $quote, 'col-12' );
			$this->echo_block_array( $text_blocks, 'col-12' );
			$this->echo_block_array( $images, 'col-6 my-1' );
			$this->echo_video( $video );
			echo '</div>';
		} else {
			echo '<div class="row my-4">';
			$this->echo_block( $quote, 'col-12' );
			$this->echo_block_array( $text_blocks, 'col-12' );
			$this->echo_block_array( $images, 'col-6 col-md-4 my-1' );
			$this->echo_video( $video );
			echo '</div>';
		}
	}


	/**
	 * Echo block.
	 */
	public function echo_block( $block, $wrapper_class = '', $wrapper_style = '' ) {
		if ( empty( $block ) ) {
			return;
		}

		if ( 'core/quote' === $block['blockName'] ) {
			$wrapper_class .= ' my-auto';
		}

		echo '<div style="' . esc_attr( $wrapper_style ) . '" class="' . esc_attr( $wrapper_class ) . '">';
		echo wp_kses_post( apply_filters( 'the_content', render_block( $block ) ) );
		echo '</div>';
	}

	public function echo_block_array( $blocks, $wrapper_class = '', $wrapper_style = '' ) {
		foreach ( $blocks as $block ) {
			$this->echo_block( $block, $wrapper_class, $wrapper_style );
		}
	}

	public function echo_video( $video ) {
		if ( ! empty( $video ) ) {
			$this->echo_block( $video, 'col-10 offset-1 mt-5' );
		}

	}

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

	public function has_landscape_image( $images ) {
		foreach ( $images as $image ) {
			if ( 'landscape' === $this->get_image_orientation( $image ) ) {
				return true;
			}
			return false;
		}
	}

	public function get_image_orientations( $images ) {
		$image_orientations = array();
		foreach ( $images as $image ) {
			array_push( $image_orientations, $this->get_image_orientation( $image ) );
		}
		return $image_orientations;
	}

	public function no_images_same_orientation( $images ) {
		$image_orientations = $this->get_image_orientations( $images );
		if ( count( $images ) === count( array_unique( $image_orientations ) ) ) {
			return true;
		}
		return false;
	}

	public function all_images_same_orientation( $images ) {
		$image_orientations = $this->get_image_orientations( $images );
		return count( array_unique( $image_orientations ) ) === 1;
	}

	public function get_image_orientation( $image_block ) {
		$dimensions = $this->get_image_dimensions( $image_block );

		if ( $dimensions['w'] > $dimensions['h'] ) {
			return 'landscape';
		} elseif ( $dimensions['w'] === $dimensions['h'] ) {
			return 'square';
		} else {
			return 'portrait';
		}
	}

	public function get_image_dimensions( $image_block ) {
		$str = $this->get_string_between( $image_block['innerHTML'], '<img src="', '.jpeg' );

		$explode_x = explode( 'x', $str );

		$height = array_pop( $explode_x );
		$width  = array_pop( explode( '-', $explode_x[0] ) );

		return array(
			'w' => $width,
			'h' => $height,
		);
	}

	/**
	 * https://stackoverflow.com/a/21804537/17378938
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
	 * @param object $block
	 * @return boolean
	 */
	public function is_non_empty_block( $block ) {
		return ! ( $block['blockName'] === null && empty( trim( $block['innerHTML'] ) ) );
	}

	/**
	 * Remove null blocks.
	 * https://github.com/WordPress/gutenberg/issues/15040#issuecomment-484627498
	 *
	 * @param string $content
	 * @return array Parsed blocks
	 */
	public function parse_blocks_ignore_empty_blocks( $content ) {
		return array_filter( parse_blocks( $content ), array( $this, 'is_non_empty_block' ) );
	}
}

<?php
/**
 * Custom methods for Bike Trip Theme.
 *
 * @category Class
 * @package  BikeTripTheme
 * @author   Brian Siklinski
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class BTT_Utilities {
	/**
	 * Construct.
	 */
	public function __construct() {

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
	 * Content excerpt.
	 *
	 * @param int                    $length Number of characters.
	 * @param string|null (optional) $content Supplied content.
	 *
	 * @return string
	 */
	public function content_excerpt( $length, $content = null ) {
		$excerpt = '';
		if ( $content ) {
			$excerpt = $content;
		} else {
			$excerpt = get_the_excerpt();
		}

		$excerpt = substr( $excerpt, 0, $length );
		$result  = substr( $excerpt, 0, strrpos( $excerpt, ' ' ) );

		return $result . '...';
	}
}

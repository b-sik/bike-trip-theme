<?php

/**
 * Custom methods for Open Street Maps plugin
 *
 * @category Class
 * @package  BikeTripTheme
 * @author   Brian Siklinski
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class OSM_Custom {
	/**
	 * GPX directory relative to uploads.
	 *
	 * @var string Location of directory with gpx files.
	 * */
	public $gpx_dir_url = '/2022/GPX/';

	/**
	 * Colors.
	 *
	 * @var array Possible track colors.
	 */
	public $colors = array( 'blue', 'green', 'red', 'grey', 'black', 'purple', 'yellow', 'orange' );

	/**
	 * Post meta key - gpx names.
	 *
	 * @var string GPX names post meta key.
	 */
	public $gpx_names_meta_key = '__gpx_meta_names';

	/**
	 * Construct.
	 */
	public function __construct() {

	}

	/**
	 * Check if file exists on server.
	 *
	 * @param string $filename Complete URL of file.
	 * @return bool
	 */
	public function file_url_exists( $filename ) {
		$file = @fopen( $filename, 'r' ); // phpcs:ignore
		return $file ? true : false;
	}

	/**
	 * Check if gpx file exists.
	 *
	 * @param string $name File name without directory or extension.
	 * @return bool
	 */
	public function gpx_file_exists( $name ) {
		return $this->file_url_exists( $this->make_gpx_filename( $name ) );
	}

	/**
	 * Location of GPX directory with uploads dir.
	 *
	 * @param bool $basedir Return base directory or url.
	 * @return string Directory url.
	 */
	public function gpx_uploads_dir_url( $basedir ) {
		if ( $basedir ) {
			return wp_upload_dir()['basedir'] . $this->gpx_dir_url;
		} else {
			return wp_upload_dir()['baseurl'] . $this->gpx_dir_url;
		}
	}

	/**
	 * Create entire filename within directory.
	 *
	 * @param string $name File name.
	 * @param bool   $basedir Return base directory instead of URL.
	 *
	 * @return string
	 */
	public function make_gpx_filename( $name, $basedir = false ) {
		return $this->gpx_uploads_dir_url( $basedir ) . $name . '.gpx';
	}

	/**
	 * Create both dir and url filenames.
	 *
	 * @param string $name File name.
	 * @return array
	 */
	public function make_gpx_filenames( $name ) {
		return array(
			'url' => $this->gpx_uploads_dir_url( false ) . $name . '.gpx',
			'dir' => $this->gpx_uploads_dir_url( true ) . $name . '.gpx',
		);
	}

	/**
	 * All GPX filenames and associated color list.
	 * Also localizes data for JS-related popup manipulation.
	 *
	 * @return array
	 */
	public function all_gpx() {
		$filenames_list = '';
		$color_list     = '';
		$count          = 0;

		$js_data = array();

		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$fields = get_fields();

				if ( ! empty( $fields['day_number'] && ! $fields['miles_and_elevation']['rest_day'] ) ) {
					$filename = $this->make_gpx_filenames( $fields['day_number'] );

					if ( $this->file_url_exists( $filename['url'] ) ) {
						array_push( $js_data, $this->get_gpx_post_data( $filename, $fields ) );

						$filenames_list = $filenames_list . $filename['url'] . ',';

						$color_list = $color_list . $this->colors[ $count ] . ',';

						$count++;
						if ( $count > count( $this->colors ) - 1 ) {
							$count = 0;
						}
					}
				}
				endwhile;
			endif;

		wp_reset_postdata();

		wp_localize_script( 'site', 'gpxData', $js_data );

		$filenames_list = rtrim( $filenames_list, ',' );
		$color_list     = rtrim( $color_list, ',' );

		return array(
			'filenames'  => $filenames_list,
			'color_list' => $color_list,
		);
	}

	/**
	 * Generate OSM plugin shortcode with single gpx filepath.
	 *
	 * @param string           $filename Full filename and URL path.
	 * @param int|string       $height Map height.
	 * @param string (optonal) $file_color_list Segment color.
	 *
	 * @return string OSM shortcode.
	 */
	public function shortcode( $filename, $height, $file_color_list = 'none' ) {
		return '[osm_map_v3 map_center="autolat,autolon" zoom="autozoom" width="100%" height="' . $height . '" file_list="' . $filename . '" file_color_list="' . $file_color_list . '" file_title="' . $filename . '"]';
	}

	/**
	 * Generate OSM plugin shortcode with all gpx files.
	 *
	 * @param int|string $height Map height.
	 *
	 * @return string OSM shortcode.
	 */
	public function shortcode_all( $height ) {
		$all_gpx = $this->all_gpx();

		return '[osm_map_v3 map_center="autolat,autolon" zoom="autozoom" width="100%" height="' . $height . '" file_list="' . $all_gpx['filenames'] . '" file_color_list="' . $all_gpx['color_list'] . '" file_title="' . $all_gpx['filenames'] . '" control="fullscreen,scaleline,mouseposition"]';
	}

	/**
	 * Get array of gpx waypoint names.
	 * Removes all waypoints except for first and last.
	 *
	 * @param array $filename Dir and url filenames.
	 * @return array
	 */
	public function get_gpx_waypoint_names( $filename ) {
		$names          = array();
		$wpts_to_remove = array();

		$gpx = new DOMDocument();
		$gpx->load( $filename['url'] );
		$root = $gpx->documentElement; // phpcs:ignore

		$waypoints = $root->getElementsByTagName( 'wpt' );

		foreach ( $waypoints as $i => $wpt ) {
			// add first and last waypoint names to array.
			if ( 0 === $i || count( $waypoints ) - 1 === $i ) {
				foreach ( $wpt->childNodes as $node ) { // phpcs:ignore
					if ( 'name' === $node->tagName ) { // phpcs:ignore
						$names[] = $node->nodeValue; // phpcs:ignore
					}
				}
				// remove all other waypoints.
			} else {
				$wpts_to_remove[] = $wpt;
			}
		}

		if ( ! empty( $wpts_to_remove ) ) {
			foreach ( $wpts_to_remove as $wpt ) {
				$wpt->parentNode->removeChild( $wpt ); // phpcs:ignore
			}

			$gpx->save( $filename['dir'] );
		}

		return $names;
	}

	/**
	 * Process GPX file and return data object to be localized.
	 *
	 * @param array $filename Dir and url filenames.
	 * @param array $fields ACF fields.
	 *
	 * @return object
	 */
	public function get_gpx_post_data( $filename, $fields ) {
		$names = array();

		if ( ! metadata_exists( 'post', get_the_ID(), $this->gpx_names_meta_key ) ) {
			$names = $this->get_gpx_waypoint_names( $filename );
			update_post_meta( get_the_ID(), $this->gpx_names_meta_key, $names );
		} else {
			$names = get_post_meta( get_the_ID(), $this->gpx_names_meta_key, true );
		}

		$data = (object) array(
			'post_id'   => get_the_ID(),
			'permalink' => get_the_permalink(),
			'fields'    => $fields,
			'names'     => $names,
		);

		return $data;
	}
}

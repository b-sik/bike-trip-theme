<?php


// @TODO interactive map
// use PHP to extract the name/description that would be displayed in the OSM hover popup
// from the .gpx file. That way the popup can be linked to a blog post
// -> php get gpx description
// -> use filename to retrieve post info
// -> create associative array with gpx desc., filename/day number, and post info
// -> make array available to JS
// -> when popup is opened, match description with possible choices and replace with post info/link

// seems far from ideal, but the first solution I've come up with that doesn't edit the plugin itself.
// I have yet to find a way to attach info like this serverside, if possible.

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
	 * Construct.
	 */
	public function __construct() {

	}

	/**
	 * Location of GPX directory with uploads dir.
	 *
	 * @return string Directory url.
	 */
	public function gpx_uploads_dir_url() {
		return wp_upload_dir()['baseurl'] . $this->gpx_dir_url;
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
	 * Create entire filename within directory.
	 *
	 * @param string $name File name.
	 * @return string
	 */
	public function make_gpx_filename( $name ) {
		return $this->gpx_uploads_dir_url() . $name . '.gpx';
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
	 * All GPX filenames and associated color list.
	 * Also localizes data for JS-related popup manipulation.
	 *
	 * @return array
	 */
	public function all_gpx() {
		$filenames  = '';
		$color_list = '';
		$count      = 0;

		$js_data = array();

		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$fields = get_fields();

				if ( ! empty( $fields['day_number'] && ! $fields['miles_and_elevation']['rest_day'] ) ) {
					$filename = $this->make_gpx_filename( $fields['day_number'] );

					if ( $this->file_url_exists( $filename ) ) {
						array_push( $js_data, $this->localize_gpx_data( $filename, $fields ) );

						$filenames = $filenames . $filename . ',';

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

		$filenames  = rtrim( $filenames, ',' );
		$color_list = rtrim( $color_list, ',' );

		return array(
			'filenames'  => $filenames,
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

	public function localize_gpx_data( $filename, $fields ) {
		$gpx = simplexml_load_file( $filename );

		$data = (object) array(
			'post_id'   => get_the_ID(),
			'permalink' => get_the_permalink(),
			'fields'    => $fields,
			'names'     => array(),
		);

		foreach ( $gpx->wpt as $wpt ) {
			$data->names[] = (string) $wpt->name;
		}

		return $data;
	}
}

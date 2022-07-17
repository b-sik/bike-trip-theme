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
	 * All GPX filenames.
	 *
	 * @return string Comma separated string of all gpx filenames with upload path.
	 */
	public function all_gpx_filenames() {
		$filenames = '';

		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$fields = get_fields();

				if ( ! empty( $fields['day_number'] && ! $fields['miles_and_elevation']['rest_day'] ) ) {
					$filenames = $filenames . $this->gpx_uploads_dir_url() . $fields['day_number'] . '.gpx,';
				}
			endwhile;
		endif;

		return rtrim( $filenames, ',' );
	}

	/**
	 * Color list for gpx segments.
	 *
	 * @return string Comma separated string of color list.
	 */
	public function all_gpx_files_color_list() {
		$color_list = '';
		$count      = '';

		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$fields = get_fields();

				if ( ! empty( $fields['day_number'] && ! $fields['miles_and_elevation']['rest_day'] ) ) {

					$color_list = $color_list . $this->colors[ $count ] . ',';

					$count++;
					if ( $count > 7 ) {
						$count = 0;
					}
				}
			endwhile;
		endif;

		return rtrim( $color_list, ',' );

	}

	/**
	 * Generate OSM plugin shortcode with single gpx filepath.
	 *
	 * @param string           $filename Filename, sans extension.
	 * @param int|string       $height Map height.
	 * @param string (optonal) $file_color_list Segment color.
	 *
	 * @return string OSM shortcode.
	 */
	public function shortcode( $filename, $height, $file_color_list = 'none' ) {
		return '[osm_map_v3 map_center="autolat,autolon" zoom="autozoom" width="100%" height="' . $height . '" file_list="' . $filename . '.gpx" file_color_list="' . $file_color_list . '" file_title="' . $filename . '"]';
	}


	/**
	 * Generate OSM plugin shortcode with all gpx files.
	 *
	 * @param int|string $height Map height.
	 *
	 * @return string OSM shortcode.
	 */
	public function shortcode_all( $height ) {
		return '[osm_map_v3 map_center="autolat,autolon" zoom="autozoom" width="100%" height="' . $height . '" file_list="' . $this->all_gpx_filenames() . '" file_color_list="' . $this->all_gpx_files_color_list() . '" file_title="' . $this->all_gpx_filenames() . '"]';
	}

}

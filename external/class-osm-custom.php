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
	 * All GPX filenames and associated color list.
	 *
	 * @return array
	 */
	public function all_gpx() {
		$filenames  = '';
		$color_list = '';
		$count      = 0;

		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$fields = get_fields();

				if ( ! empty( $fields['day_number'] && ! $fields['miles_and_elevation']['rest_day'] ) ) {
					$filenames = $filenames . $this->gpx_uploads_dir_url() . $fields['day_number'] . '.gpx,';

					$color_list = $color_list . $this->colors[ $count ] . ',';

					$count++;
					if ( $count > count( $this->colors ) ) {
						$count = 0;
					}
				}
			endwhile;
		endif;

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
	 * @param string           $filename Filename, sans extension.
	 * @param int|string       $height Map height.
	 * @param string (optonal) $file_color_list Segment color.
	 *
	 * @return string OSM shortcode.
	 */
	public function shortcode( $filename, $height, $file_color_list = 'none' ) {
		return '[osm_map_v3 map_center="autolat,autolon" zoom="autozoom" width="100%" height="' . $height . '" file_list="' . $this->gpx_uploads_dir_url() . $filename . '.gpx" file_color_list="' . $file_color_list . '" file_title="' . $filename . '"]';
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

		return '[osm_map_v3 map_center="autolat,autolon" zoom="autozoom" width="100%" height="' . $height . '" file_list="' . $all_gpx['filenames'] . '" file_color_list="' . $all_gpx['color_list'] . '" file_title="' . $all_gpx['filenames'] . '"]';
	}
}

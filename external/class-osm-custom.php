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
	 * ID of config page, to store meta.
	 *
	 * @var int ID of config page.
	 */
	public $config_page_id;

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
	 * Post meta key - gpx waypoint names.
	 *
	 * @var string GPX names post meta key.
	 */
	public $gpx_names_meta_key = '__gpx_meta_names';

	/**
	 * Post meta key - gpx waypoint description.
	 *
	 * @var string GPX description post meta key.
	 */
	public $gpx_descs_meta_key = '__gpx_meta_descs';

	/**
	 * Post meta key - filenames list.
	 *
	 * @var string OSM shortcode filenames list post meta key.
	 */
	public $gpx_filenames_meta_key = '__gpx_filenames_list';

	/**
	 * Post meta key - colors list.
	 *
	 * @var string OSM shortcode colors list post meta key.
	 */
	public $gpx_colors_meta_key = '__gpx_colors_list';

	/**
	 * Post meta key - gpx file count.
	 * Used to check whether the post loop needs to run to update the filenames/colors list.
	 *
	 * @var int Total gpx file count.
	 */
	public $gpx_file_count_meta_key = '__gpx_file_count';

	/**
	 * Post meta key - JS data to localize.
	 *
	 * @var array Data to localize for use on frontend.
	 */
	public $gpx_js_data_meta_key = '__gpx_js_data';

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->config_page_id = BTT_Utilities::get_config_post_id();
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
	 * @return array|void
	 */
	public function make_gpx_filenames( $name ) {
		return array(
			'url' => $this->gpx_uploads_dir_url( false ) . $name . '.gpx',
			'dir' => $this->gpx_uploads_dir_url( true ) . $name . '.gpx',
		);
	}

	/**
	 * Get count of files in GPX directory.
	 *
	 * @return int
	 */
	public function get_gpx_file_count() {
		$gpx_iterator = new FilesystemIterator( $this->gpx_uploads_dir_url( true ) );
		return iterator_count( $gpx_iterator );
	}

	/**
	 * Returns all GPX filenames and associated color list for use in OSM shortcode.
	 * Also localizes data for JS-related popup manipulation.
	 * Contains series of checks to determine if data can be grabbed from db to avoid post loop.
	 * Loop should only run when a new GPX file is added to the GPX uploads dir.
	 *
	 * @return array
	 */
	public function all_gpx() {
		$file_gpx_count = $this->get_gpx_file_count();

		// is the gpx file count set in post meta?
		if ( metadata_exists( 'post', $this->config_page_id, $this->gpx_file_count_meta_key ) ) {
			$meta_gpx_count = (int) get_post_meta( $this->config_page_id, $this->gpx_file_count_meta_key, true );

			// does the file count match the post meta count?
			// if yes, return data from db. if no, run the loop.
			if ( $meta_gpx_count === $file_gpx_count ) {

				// before we return data from db, we also need to check if the js data to localize is available.
				// if not, run the loop.
				if ( metadata_exists( 'post', $this->config_page_id, $this->gpx_js_data_meta_key ) ) {
					$js_data = get_post_meta( $this->config_page_id, $this->gpx_js_data_meta_key, true );
					wp_localize_script( 'site', 'gpxData', $js_data );

					return array(
						'filenames'  => get_post_meta( $this->config_page_id, $this->gpx_filenames_meta_key, true ),
						'color_list' => get_post_meta( $this->config_page_id, $this->gpx_colors_meta_key, true ),
					);
				} else {
					return $this->all_gpx_do_loop( $this->config_page_id, $file_gpx_count );
				}
			} else {
				return $this->all_gpx_do_loop( $this->config_page_id, $file_gpx_count );
			}
		} else {
			// set gpx file count in post meta if not set and run function again.
			update_post_meta( $this->config_page_id, $this->gpx_file_count_meta_key, $file_gpx_count );
			$this->all_gpx();
		}
	}

	/**
	 * Loop through posts to create GPX file data for OSM shortcode.
	 * Sets relevant post meta and localizes JS data.
	 *
	 * @param int $file_gpx_count Result of GPX dir file iterator count.
	 *
	 * @return array
	 */
	public function all_gpx_do_loop( $file_gpx_count ) {
		$filenames_list = '';
		$color_list     = '';
		$count          = 0;

		$js_data = array();

		$all_posts_query = BTT_Utilities::all_posts_query();

		if ( $all_posts_query->have_posts() ) :
			while ( $all_posts_query->have_posts() ) :
				$all_posts_query->the_post();
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

		$filenames_list = rtrim( $filenames_list, ',' );
		$color_list     = rtrim( $color_list, ',' );

		update_post_meta( $this->config_page_id, $this->gpx_filenames_meta_key, $filenames_list );
		update_post_meta( $this->config_page_id, $this->gpx_colors_meta_key, $color_list );
		update_post_meta( $this->config_page_id, $this->gpx_file_count_meta_key, $file_gpx_count );
		update_post_meta( $this->config_page_id, $this->gpx_js_data_meta_key, $js_data );

		wp_localize_script( 'site', 'gpxData', $js_data );

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
	public function get_gpx_wpt_data( $filename ) {
		$names          = array();
		$descs          = array();
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
					if ( 'desc' === $node->tagName ) { // phpcs:ignore
						$descs[] = $node->nodeValue; // phpcs:ignore
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

		return array(
			'names' => $names,
			'descs' => $descs,
		);
	}

	/**
	 * Process GPX file and return data object to be localized.
	 * Runs inside of a posts loop.
	 *
	 * @param array $filename Dir and url filenames.
	 * @param array $fields ACF fields.
	 *
	 * @return object
	 */
	public function get_gpx_post_data( $filename, $fields ) {
		$names = array();
		$descs = array();

		if ( ! metadata_exists( 'post', get_the_ID(), $this->gpx_names_meta_key ) ) {
			$wpt_data = $this->get_gpx_wpt_data( $filename );
			$names    = $wpt_data['names'];
			$descs    = $wpt_data['descs'];

			update_post_meta( get_the_ID(), $this->gpx_names_meta_key, $names );
			update_post_meta( get_the_ID(), $this->gpx_descs_meta_key, $descs );
		} else {
			$names = get_post_meta( get_the_ID(), $this->gpx_names_meta_key, true );
			$descs = get_post_meta( get_the_ID(), $this->gpx_descs_meta_key, true );
		}

		$next_page = get_next_post();
		$prev_page = get_previous_post();

		$data = (object) array(
			'post_id'   => get_the_ID(),
			'permalink' => get_the_permalink(),
			'fields'    => $fields,
			'names'     => $names,
			'descs'     => $descs,
		);

		if ( ! empty( $next_page ) ) {
			$data->next_page = array(
				'fields'    => get_fields( $next_page->ID ),
				'permalink' => get_the_permalink( $next_page->ID ),
			);
		}

		if ( ! empty( $prev_page ) ) {
			$data->prev_page = array(
				'fields'    => get_fields( $prev_page->ID ),
				'permalink' => get_the_permalink( $prev_page->ID ),
			);
		}

		return $data;
	}
}

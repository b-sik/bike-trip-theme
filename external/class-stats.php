<?php
/**
 * Riding stats.
 *
 * @category Class
 * @package  BikeTripTheme
 * @author   Brian Siklinski
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class Stats {
	/**
	 * OSM Custom Class.
	 *
	 * @var Class Instance of OSM_Custom Class.
	 */
	public $osm;

	/**
	 * ID of config page, to store meta.
	 *
	 * @var int ID of config page.
	 */
	public $config_page_id;

	/**
	 * Post meta key - total stats.
	 *
	 * @var string Total stats meta key.
	 */
	public $stats_meta_key = '__gpx_stats';

	/**
	 * Post meta key - last file count.
	 *
	 * @var string Last file count meta key.
	 */
	public $last_post_meta_key = '__gpx_stats_last_post';

	/**
	 * Construct.
	 */
	public function __construct() {
		$this->config_page_id = BTT_Utilities::get_config_post_id();
	}

	/**
	 * Return all combined stats either from db or loop.
	 *
	 * @return array
	 */
	public function total_stats() {
		$last_post_id = $this->get_last_post_id();

		if ( ! metadata_exists( 'post', $this->config_page_id, $this->last_post_meta_key ) ) {
			update_post_meta( $this->config_page_id, $this->last_post_meta_key, $last_post_id );
			$this->total_stats();
		}

		$last_post_meta = (int) get_post_meta( $this->config_page_id, $this->last_post_meta_key, true );

		if ( $last_post_id === $last_post_meta ) {
			if ( metadata_exists( 'post', $this->config_page_id, $this->stats_meta_key ) ) {
				return get_post_meta( $this->config_page_id, $this->stats_meta_key, true );
			} else {
				return $this->total_stats_do_loop( $this->config_page_id, $last_post_id );
			}
		} else {
			return $this->total_stats_do_loop( $this->config_page_id, $last_post_id );
		}
	}

	/**
	 * Get the ID of the most recent post.
	 * Used to determine if an update is needed.
	 *
	 * @link https://wordpress.stackexchange.com/a/280192
	 *
	 * @return int ID
	 */
	public function get_last_post_id() {
		global $wpdb;

		$query = "SELECT ID FROM $wpdb->posts ORDER BY ID DESC LIMIT 0,1";

		$result = $wpdb->get_results( $query ); // phpcs:ignore
		$row    = $result[0];
		$id     = $row->ID;

		return (int) $id;
	}

	/**
	 * Do the posts loop to get fresh stats.
	 *
	 * @param int $page_id Page ID to attach meta.
	 * @param int $last_post_id Count of files in GPX upload folder.
	 *
	 * @return array
	 */
	public function total_stats_do_loop( $page_id, $last_post_id ) {
		$riding_days = 0;
		$miles       = 0;
		$elev_gain   = 0;
		$elev_loss   = 0;
		$flats       = 0;

		$all_posts_query = BTT_Utilities::all_posts_query();

		if ( $all_posts_query->have_posts() ) {
			while ( $all_posts_query->have_posts() ) {
				$all_posts_query->the_post();
				$fields = get_fields();
				$stats  = $fields['miles_and_elevation'];

				if ( ! $stats['rest_day'] ) {
					$miles     += intval( $stats['miles'] );
					$elev_gain += intval( $stats['elevation_gain'] );
					$elev_loss += intval( $stats['elevation_loss'] );
					$flats     += intval( $stats['flats'] );

					++$riding_days;
				}
			}
		}

		$final_stats = array(
			'riding_days' => $riding_days,
			'miles'       => number_format( $miles ),
			'avg_miles'   => number_format( $miles / $riding_days, 2 ),
			'elev_gain'   => number_format( $elev_gain ),
			'elev_loss'   => number_format( $elev_loss ),
			'flats'       => $flats,
		);

		update_post_meta( $page_id, $this->stats_meta_key, $final_stats );
		update_post_meta( $page_id, $this->last_post_meta_key, $last_post_id );

		return $final_stats;
	}
}

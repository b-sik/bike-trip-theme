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
	 * Construct.
	 */
	public function __construct() {

	}

	/**
	 * Return all combined stats.
	 *
	 * @return array
	 */
	public function total_stats() {
		$riding_days = 0;
		$miles       = 0;
		$elev_gain   = 0;
		$elev_loss   = 0;
		$flats       = 0;

		$args = array(
			'post_type' => 'post',
		);

		$post_query = new WP_Query( $args );

		if ( $post_query->have_posts() ) {
			while ( $post_query->have_posts() ) {
				$post_query->the_post();
				$fields = get_fields();
				$stats  = $fields['miles_and_elevation'];

				if ( ! $stats['rest_day'] ) {
					$miles     += intval( $stats['miles'] );
					$elev_gain += intval( $stats['elevation_gain'] );
					$elev_loss += intval( $stats['elevation_loss'] );
					$flats     += intval( $stats['flats'] );

					$riding_days++;
				}
			}
		}

		return array(
			'riding_days' => $riding_days,
			'miles'       => $miles,
			'elev_gain'   => $elev_gain,
			'elev_loss'   => $elev_loss,
			'flats'       => $flats,
		);
	}
}

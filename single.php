<?php
/**
 * The Template for displaying all single posts
 *
 * Please see /external/bootstrap-utilities.php for info on bs_wp::get_template_parts()
 *
 * @package     WordPress
 * @subpackage  Bootstrap 5.1.3
 * @autor       Babobski
 */

$bs_wp = new BsWp();
$osm   = new OSM_Custom();

$bs_wp->get_template_parts(
	array(
		'parts/shared/html-header',
		'parts/shared/header',
	)
);
?>

<div class="content container mb-5" id="single-post">
	<?php
	if ( have_posts() ) :
		the_post();
		$fields        = get_fields();
		$day_number    = $fields['day_number'];
		$date          = $fields['date'];
		$weather       = $fields['weather'];
		$start_loc     = $fields['locations']['start'];
		$is_single_loc = $fields['locations']['single'];
		$end_loc       = $fields['locations']['end'];
		$rest_day      = $fields['miles_and_elevation']['rest_day'];
		$stats         = $fields['miles_and_elevation'];

		if ( ! empty( $day_number ) && ! $rest_day && $osm->gpx_file_exists( $day_number ) ) {
			?>
			<div class="row mt-5">
				<div class="osm-wrapper col-12 col-lg-10 offset-lg-1">
					<?php
					echo do_shortcode( $osm->shortcode( $osm->make_gpx_filename( $day_number ), 300 ) );
					?>
				</div>
			</div>
		<?php } ?>

		<div id="single-post-stats-bar" class="row mt-5 mb-3">
			<div class="col-12 col-lg-10 offset-lg-1">
				<hr />
				<ul class="d-flex justify-content-around list-unstyled"> 
					<li>
						<time datetime="<?php echo esc_attr( $date ); ?>" pubdate> <?php echo esc_html( $date ); ?> </time>
					</li>

					<?php
					if ( $rest_day ) {
						echo '<li>Rest Day</li>';
					} else {
						echo esc_html( $stats['miles'] . ' Miles' );
						echo '<li>↑ ' . number_format( $stats['elevation_gain'] ) . ' ft ↓ ' . number_format( $stats['elevation_loss'] ) . ' ft</li>';
					}
					?>
				</ul>
				<hr />
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-12 col-lg-10 offset-lg-1">
					<h1 class="mb-1">DAY <?php echo esc_html( $day_number ); ?> </h1>

					<?php if ( $is_single_loc ) { ?>
						<h3>REST DAY in <?php echo esc_html( strtoupper( $start_loc ) ); ?></h3>
					<?php } else { ?>
						<h3>
							<?php echo esc_html( strtoupper( $start_loc ) . ' to ' . strtoupper( $end_loc ) ); ?>
						</h3>
					<?php } ?>

					<h6 class="mt-4">WEATHER: <?php echo esc_html( strtoupper( $weather ) ); ?></h6>

					<div id="post-content-wrapper" class="mt-5">
						<?php
						the_content();
						?>
					</div>
				</div>
			</div>
		</div>
</div>
<?php endif;

	$bs_wp->get_template_parts(
		array(
			'parts/shared/footer',
			'parts/shared/html-footer',
		)
	);
	?>

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
// $osm          = new OSM_Custom();
$post_content = new Post_Content();

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
		$fields     = get_fields();
		$day_number = $fields['day_number'];
		$date       = $fields['date'];
		$weather    = $fields['weather'];
		$start_loc  = $fields['locations']['start'];
		$single_loc = $fields['locations']['single'];
		$rest_day   = $fields['miles_and_elevation']['rest_day'];
		$stats      = $fields['miles_and_elevation'];

		$multiple_days = false;
		if ( isset( $fields['multiple_days'] ) ) {
			$multiple_days = $fields['multiple_days'];
		}

		if ( ! $single_loc ) {
			$end_loc = $fields['locations']['end'];
		}

		if ( $multiple_days ) {
			$end_day_number = $fields['end_day_number'];
		}

		?>

		<div id="single-post-stats-bar" class="row mt-5 mb-3">
			<div class="col-12 col-lg-10 offset-lg-1">
				<hr />
				<ul class="d-flex justify-content-around list-unstyled"> 
					<li>
						<time datetime="<?php echo esc_attr( $date ); ?>" pubdate> <?php echo esc_html( $date ); ?> </time>
					</li>

					<?php
					if ( $rest_day ) {
						?>
						<li>Rest Day<?php echo $multiple_days ? 's' : ''; ?>
						<?php
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
					<?php if ( $multiple_days ) { ?>
						<h1 class="mb-1">DAYS <?php echo esc_html( $day_number . ' - ' . $end_day_number ); ?> </h1>
					<?php } else { ?>
						<h1 class="mb-1">DAY <?php echo esc_html( $day_number ); ?> </h1>
					<?php } ?>

					<?php if ( $single_loc ) { ?>
						<h3>REST DAY<?php echo $multiple_days ? 'S' : ''; ?> in <?php echo esc_html( strtoupper( $start_loc ) ); ?></h3>
					<?php } else { ?>
						<h3>
							<?php echo esc_html( strtoupper( $start_loc ) . ' to ' . strtoupper( $end_loc ) ); ?>
						</h3>
					<?php } ?>

					<h6 class="mt-4">WEATHER: <?php echo esc_html( strtoupper( $weather ) ); ?></h6>

					<div id="post-content-wrapper" class="mt-5">
						<?php
						$post_content->single_post_content();
						?>
					</div>
				</div>
			</div>
			<div id="post-page-nav" class="row">
				<div class="col-6">
				<?php
				if ( isset( get_next_post()->ID ) ) {
					?>
					<a href="<?php echo esc_url( get_permalink( get_next_post()->ID ) ); ?>">&larr; next post</a>
				<?php } ?>
				</div>
				<div class="col-6 text-end">
				<?php
				if ( isset( get_previous_post()->ID ) ) {
					?>
					<a href="<?php echo esc_url( get_permalink( get_previous_post()->ID ) ); ?>">previous post &rarr;</a>
				<?php } ?>
				</div>
			</div>
		</div>
</div>
<?php endif;
	wp_reset_postdata();

	$bs_wp->get_template_parts(
		array(
			'parts/shared/footer',
			'parts/shared/html-footer',
		)
	);
	?>

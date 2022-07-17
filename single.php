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
		$fields     = get_fields();
		$day_number = $fields['day_number'];
		$date       = $fields['date'];
		$rest_day   = $fields['miles_and_elevation']['rest_day'];
		$stats      = $fields['miles_and_elevation'];

		if ( ! empty( $day_number ) && ! $rest_day ) {
			?>
			<div class="row mt-5 justify-content-center">
				<div class="osm-wrapper w-100">
					<?php
					echo do_shortcode( $osm->shortcode( $day_number, 300 ) );
					?>
				</div>
			</div>
		<?php } ?>

		<div id="single-post-stats-bar" class="row mt-5 mb-3">
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
		<h2 class="mb-3"> <?php the_title(); ?> </h2>
		<div class="container">
			<?php
			the_content();
			?>
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

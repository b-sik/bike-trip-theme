<?php

/**
 * The Template for displaying all single posts
 *
 * Please see /external/bootstrap-utilities.php for info on BsWp::get_template_parts()
 *
 * @package     WordPress
 * @subpackage  Bootstrap 5.1.3
 * @autor       Babobski
 */
$BsWp = new BsWp();

$BsWp->get_template_parts(
	array(
		'parts/shared/html-header',
		'parts/shared/header',
	)
);
?><div class="content container mb-5" id="single-post">
	<?php
	if ( have_posts() ) :
		the_post();
		$fields = get_fields();

		if ( ! empty( $fields['day_number'] ) && $fields['miles_and_elevation']['rest_day'] ) :
			?>
			<div class="row mt-5 justify-content-center">
				<div class="osm-wrapper w-100">
					<?php
					echo do_shortcode( osm_shortcode( $fields['day_number'] . '.gpx', 300 ) );
					?>
				</div>
			</div>
		<?php endif; ?>

		<div id="single-post-stats-bar" class="row mt-5 mb-3">
			<hr />
			<ul class="d-flex justify-content-around list-unstyled">
				<li>
					<time datetime="<?php echo $fields['date']; ?>" pubdate> <?php echo $fields['date']; ?> </time>
				</li>

				<?php
				if ( $fields['miles_and_elevation']['rest_day'] ) :
					echo '<li>Rest Day</li>';
				else :
					echo $fields['miles_and_elevation']['miles'] . ' Miles';
					echo '<li>↑ ' . number_format( $fields['miles_and_elevation']['elevation_gain'] ) . ' ft ↓ ' . number_format( $fields['miles_and_elevation']['elevation_loss'] ) . ' ft</li>';
				endif;
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
<?php endif; ?>


<?php
$BsWp->get_template_parts(
	array(
		'parts/shared/footer',
		'parts/shared/html-footer',
	)
);
?>

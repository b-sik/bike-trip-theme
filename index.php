<?php

/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file
 *
 * Please see /external/bootstrap-utilities.php for info on Starkers_Utilities::get_template_parts()
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

function content_excerpt( $length ) {
	$excerpt = get_the_excerpt();
	$excerpt = substr( $excerpt, 0, $length ); // Only display first x characters of excerpt
	$result  = substr( $excerpt, 0, strrpos( $excerpt, ' ' ) );

	ob_start();
	?> <div class="px-2 pt-2 pb-1"> <?php echo $result . '...'; ?> </div>
	<?php
	return ob_get_clean();
}

$all_gpx_file_names = '';
$upload_dir         = wp_upload_dir();
$gpx_dir_url        = $upload_dir['baseurl'] . '/2022/GPX/';
$gpx_color_list     = '';

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		$fields = get_fields();
		if ( ! empty( $fields['gpx_filename'] ) ) {
			$all_gpx_file_names = $all_gpx_file_names . $gpx_dir_url . $fields['gpx_filename'] . ',';
			$gpx_color_list     = $gpx_color_list . 'none,';
		}
	endwhile;
endif;

$all_gpx_file_names = rtrim( $all_gpx_file_names, ',' );
$gpx_color_list     = rtrim( $gpx_color_list, ',' );
?>

<section id="osm_overview_map" class="container my-5">
	<div class="row">
		<div class="osm-wrapper">
			<?php
			echo do_shortcode( osm_shortcode( $all_gpx_file_names, 400, true, $gpx_color_list ) );
			?>
		</div>
	</div>
</section>
<section id="all_posts" class="container min-vh-100 my-2">
	<?php
	if ( have_posts() ) :
		?>
		<div class="row g-2">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<div class="col-12 col-md-6 col-lg-4">
					<div class="container border h-100">
						<h3>
							<a href="<?php esc_url( the_permalink() ); ?>" title="<?php the_title(); ?>" rel="bookmark">
								<?php the_title(); ?> </a>
						</h3>
						<div class="mt-1">
						</div> <?php echo content_excerpt( 70 ); ?>
					</div>
				</div>
				<?php
			endwhile;
		endif;
		?>
		</div>
</section>
<?php
$BsWp->get_template_parts(
	array(
		'parts/shared/footer',
		'parts/shared/html-footer',
	)
);
?>

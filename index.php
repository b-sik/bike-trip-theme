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

?>
<div class="container-fluid min-vh-100 my-2">
	<div class="row p-3"> <?php get_sidebar(); ?> <div class="col-12 col-md-9">
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
									<?php echo do_shortcode( osm_shortcode( get_field( 'gpx_filename', get_the_ID() ), 200 ) ); ?>
								</div> <?php echo content_excerpt( 70 ); ?>
							</div>
						</div>
						<?php
					endwhile;
				else :
					?>
					<h1> <?php echo __( 'Nothing to show yet.', 'wp_babobski' ); ?> </h1> <?php endif; ?>
				</div>
		</div>
	</div>
</div><?php
		$BsWp->get_template_parts(
			array(
				'parts/shared/footer',
				'parts/shared/html-footer',
			)
		);
		?>

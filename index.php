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

$bs_wp = new BsWp();
$osm   = new OSM_Custom();
$util  = new BTT_Utilities();

$bs_wp->get_template_parts(
	array(
		'parts/shared/html-header',
		'parts/shared/header',
	)
);

$fields = get_fields( get_page_by_title( 'Front Page' )->ID );
?>

<section id="osm-overview-map" class="container-fluid py-5">
	<div class="row justify-content-center">
		<div class="osm-wrapper col-10 col-lg-8 text-center">
			<h3 class="mb-4"><?php echo esc_html( strtoupper( 'Track Our Progress' ) ); ?></h3>
			<?php
			echo do_shortcode( $osm->shortcode_all( 400 ) );
			?>
		</div>
	</div>
</section>
<section id="all_posts" class="container min-vh-100 my-2">
	<div class="row g-2 justify-content-center mt-3">
		<div class="col-12 col-lg-10">	<h3 id="posts-header" class="text-center mt-5 mb-3"><?php echo esc_html( strtoupper( 'Keeping Up With The Wheelie Babes' ) ); ?></h3>
		<hr />
	</div>

		<?php
		if ( have_posts() ) :

			while ( have_posts() ) :
				the_post();
				?>
				<div class="col-12 col-md-6 col-lg-5">
					<div class="card bg-dark">
						<a href="<?php esc_url( the_permalink() ); ?>" class="text-white" title="<?php the_title(); ?>" rel="bookmark">
							<?php if ( ! empty( get_the_post_thumbnail_url() ) ) : ?>
								<img class="card-img" src="<?php echo esc_attr( get_the_post_thumbnail_url() ); ?>" alt="Card image">
							<?php endif; ?>
							<div class="card-img-overlay d-flex flex-column justify-content-between">
								<h5 class="card-title">
									<?php the_title(); ?> </h5>
								<p class="card-text"><?php echo esc_html( $util->content_excerpt( $fields['post_excerpt_length'] ) ); ?></p>
							</div>
						</a>
					</div>
				</div>
				<?php
			endwhile;
		endif;
		?>
	</div>
</section>

<?php
$bs_wp->get_template_parts(
	array(
		'parts/shared/footer',
		'parts/shared/html-footer',
	)
);
?>

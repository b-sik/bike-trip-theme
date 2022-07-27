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

$fields              = get_fields( get_page_by_title( 'Front Page' )->ID );
$post_excerpt_length = $fields['post_excerpt_length']
?>

<section id="osm-overview-map" class="container-fluid min-vh-100 d-flex align-items-center">
	<div class="row justify-content-center flex-grow-1">
		<div class="osm-wrapper col-11 col-md-10 text-center">
			<h3 class="mb-5 pb-3"><?php echo esc_html( strtoupper( 'Track Our Progress' ) ); ?></h3>
			<?php
			echo do_shortcode( $osm->shortcode_all( 450 ) );
			?>
		</div>
		<p class="venmo-text pt-4 text-center">Help the Wheelie Babes stay fueled by <a href="https://venmo.com/?txn=pay&audience=friends&recipients=@Eric-Svenson&amount=5.00&note=Wheelie%20Babe%20Breakfast%20Sandwich" target="_blank" rel="noopener noreferrer">buying them a breakfast sandwich</a>!</p>
	</div>
</section>
<section id="all_posts" class="container min-vh-100 mt-2 mb-5">
	<div class="row g-4 g-lg-5 justify-content-center mt-3">
		<div class="col-12 col-lg-10">	
			<h3 id="posts-header" class="text-center mt-5 mb-3"><?php echo esc_html( strtoupper( 'Keeping Up With The Wheelie Babes' ) ); ?></h3>
		<hr />
		</div>

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$post_fields   = get_fields();
				$day_number    = $post_fields['day_number'];
				$start_loc     = $post_fields['locations']['start'];
				$is_single_loc = $post_fields['locations']['single'];
				$end_loc       = $post_fields['locations']['end'];
				?>
				<div class="col-12 col-md-6 col-lg-5">
					<div class="card bg-dark text-white text-decoration-none" onclick="location.href='<?php the_permalink(); ?>';" style="cursor:pointer">
							<?php if ( ! empty( get_the_post_thumbnail_url() ) ) : ?>
								<img class="card-img" src="<?php echo esc_attr( get_the_post_thumbnail_url() ); ?>" alt="Card image" />
							<?php endif; ?>
							<div class="card-img-overlay d-flex flex-column justify-content-start">
								<h4 class="card-title">
									<span class="d-block">DAY <?php echo esc_html( $day_number ); ?>:</span>

									<?php if ( $is_single_loc ) { ?>
									<span>REST DAY in <?php echo esc_html( strtoupper( $start_loc ) ); ?></span>
									<?php } else { ?>
										<span>
											<?php echo esc_html( strtoupper( $start_loc ) . ' to ' . strtoupper( $end_loc ) ); ?>
										</span>
									<?php }; ?> 
								</h4>

								<p class="card-text mt-2">
									<?php echo esc_html( $util->content_excerpt( $post_excerpt_length ) ); ?>
								</p>

								<div class="d-block mb-0 mt-auto d-flex justify-content-end">
									<a id="read-more" href="<?php esc_url( the_permalink() ); ?>" class='text-decoration-none'>READ MORE</a>
								</div>
							</div>
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

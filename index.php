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
	<div class="row py-4 justify-content-center flex-grow-1">
		<div class="osm-wrapper col-11 col-md-10 text-center">
			<h3 class="pb-2"><?php echo esc_html( strtoupper( 'Track Our Progress' ) ); ?></h3>
			<small class="py-3 text-center d-block" style="font-weight:300;">Click on a route segment to see that day's blog post 📍</small>
			<?php
			echo do_shortcode( $osm->shortcode_all( 450 ) );
			?>
		</div>
		<p class="venmo-text text-center pt-3">Help the Wheelie Babes stay fueled by <a href="https://venmo.com/?txn=pay&audience=friends&recipients=svenericson@gmail.com&amount=5.00&note=Wheelie%20Babes%20Breakfast%20Sandwich" target="_blank" rel="noopener noreferrer">buying them a breakfast sandwich</a>!</p>
	</div>
</section>
<section id="all_posts" class="container min-vh-100 mt-2 mb-5">
	<div class="row g-4 g-lg-5 justify-content-center mt-3">
		<div class="col-12 col-lg-10">	
			<h3 id="posts-header" class="text-center mt-3 mb-3"><?php echo esc_html( strtoupper( 'Keeping Up With The Wheelie Babes' ) ); ?></h3>
		<hr />
		</div>

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				$post_fields   = get_fields();
				$day_number    = $post_fields['day_number'];
				$start_loc     = $post_fields['locations']['start'];
				$single_loc    = $post_fields['locations']['single'];
				$end_loc       = $post_fields['locations']['end'];

				$multiple_days = false; 
				if ( isset( $post_fields['multiple_days'] ) ) {
					$multiple_days = $post_fields['multiple_days'];
				}

				if ( ! $single_loc ) {
					$end_loc = $post_fields['locations']['end'];
				}

				if ( $multiple_days ) {
					$end_day_number = $post_fields['end_day_number'];
				}
				?>
				<div class="col-12 col-md-6 col-lg-5">
					<div class="card bg-dark text-white text-decoration-none" onclick="location.href='<?php the_permalink(); ?>';" style="cursor:pointer">
							<?php if ( ! empty( get_the_post_thumbnail_url() ) ) : ?>
								<img class="card-img" src="<?php echo esc_attr( get_the_post_thumbnail_url() ); ?>" alt="Card image" />
							<?php endif; ?>
							<div class="card-img-overlay d-flex flex-column justify-content-start">
								<h4 class="card-title">
									<?php if ( $multiple_days ) { ?>
										<span class="d-block">DAYS <?php echo esc_html( $day_number . ' - ' . $end_day_number ); ?> </span>
									<?php } else { ?>
										<span class="d-block">DAY <?php echo esc_html( $day_number ); ?> </span>
									<?php } ?>

									<?php if ( $single_loc ) { ?>
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
				echo '<div class="col-12 d-flex justify-content-center">';
				the_posts_pagination(
					array(
						'mid_size'  => 2,
						'prev_text' => '&larr; More Recent Posts',
						'next_text' => 'Relive the Journey &rarr;',
					)
				);
				echo '</div>';
		endif;
		?>
	</div>
</section>

<?php
wp_reset_postdata();

$bs_wp->get_template_parts(
	array(
		'parts/shared/footer',
		'parts/shared/html-footer',
	)
);
?>

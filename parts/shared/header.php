<?php
$header_image = get_field( 'header_image', get_page_by_title( 'Front Page' )->ID );
$h_color = get_field( 'header_overlay_color', get_page_by_title( 'Front Page' )->ID );

$total_riding_days = 0;
$total_miles       = 0;
$total_arguments   = 0;
$total_elev_gain   = 0;
$total_elev_loss   = 0;
$total_flats       = 0;

$args = array(
	'post_type' => 'post',
);

$post_query = new WP_Query( $args );

if ( $post_query->have_posts() ) :
	while ( $post_query->have_posts() ) :
		$post_query->the_post();
		$fields = get_fields();
		if ( ! empty( $fields['arguments'] ) ) {
			$total_arguments += intval( $fields['arguments'] );
		}

		if ( ! $fields['miles_and_elevation']['rest_day'] ) {
			$total_miles       += intval( $fields['miles_and_elevation']['miles'] );
			$total_riding_days += 1;
			$total_elev_gain   += intval( $fields['miles_and_elevation']['elevation_gain'] );
			$total_elev_loss   += intval( $fields['miles_and_elevation']['elevation_loss'] );
			$total_flats       += intval( $fields['miles_and_elevation']['flats'] );
		}
	endwhile;
endif;
?>

<nav class="navbar navbar-expand navbar-light bg-light" style="background-image:url(<?php echo $header_image; ?>);background-size:cover;background-position:center;box-shadow:inset 0 0 0 2000px rgba(<?php echo $h_color['red']; ?>, <?php echo $h_color['green']; ?>, <?php echo $h_color['blue']; ?>, 0.9);">
	<div class="container-fluid">
		<!-- <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#primaryNav"
			aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button> -->
		<div class="offcanvas offcanvas-start" tabindex="-1" id="primaryNav">
			<!-- <div class="offcanvas-header">
				<h5 class="offcanvas-title" id="offcanvasNavbarLabel"><?php echo get_bloginfo( 'name' ); ?></h5>
				<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
					aria-label="Close"></button>
			</div> -->
			<div class="offcanvas-body mx-4 py-3 flex-column flex-sm-row align-items-center">
				<div class="col-12 col-sm-6 d-flex flex-column">
					<div class="row text-center text-sm-start">
						<a class="navbar-brand" href="<?php echo get_site_url(); ?>/">
							<h1><?php echo get_bloginfo( 'name' ); ?></h1>
						</a>
					
					<div class=""><small><?php echo get_bloginfo( 'description' ); ?></small></div>
					</div>
				</div>
				<div class="col-12 col-sm-6 d-flex flex-column align-items-center align-items-sm-end justify-content-center text-center text-sm-start mt-4 mt-sm-0">
					<ul class="list-unstyled m-0">
						<li>🚴 <?php echo $total_riding_days; ?> Riding Days</li>
						<li>🛣 <?php echo $total_miles; ?> Miles</li>
						<li>🚵 &uarr; <?php echo number_format($total_elev_gain); ?> ft &darr; <?php echo number_format($total_elev_loss); ?> ft</li>
						<li>
						<?php
						$flats_string = intval( $total_flats ) !== 1 ? 'Flats' : 'Flat';
						echo '⚠️ ' . $total_flats . ' ' . $flats_string
						?>
						</li>
						<li>
						<?php
						$arg_string = intval( $total_arguments ) !== 1 ? 'Arguments' : 'Argument';
						echo '🗣 ' . $total_arguments . ' ' . $arg_string;
						?>
						</li>
					</ul>
				</div>
				<!-- 
				<?php
				wp_nav_menu(
					array(
						'menu'           => 'primary',
						'theme_location' => 'primary',
						'depth'          => 2,
						'container'      => false,
						'menu_class'     => 'navbar-nav justify-content-start flex-grow-1 pe-3',
						'fallback_cb'    => '__return_false',
						'walker'         => new bootstrap_5_wp_nav_menu_walker(),
					)
				);
				?>
				 -->

			</div>
		</div>
	</div>
</nav>

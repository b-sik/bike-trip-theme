<?php
$header_image = get_field( 'header_image', get_page_by_title( 'Front Page' )->ID );

$stats = new Stats();
$stats = $stats->total_stats();
?>

<nav class="navbar navbar-expand navbar-light bg-light" style="background-image:url(<?php echo esc_attr( $header_image ); ?>);background-size:cover;background-position:center;box-shadow:inset 0 0 0 2000px #584B53;">
	<div class="container">
		<div class="offcanvas offcanvas-start" tabindex="-1" id="primaryNav">
			<div class="offcanvas-body mx-3 pb-5 py-4 flex-column flex-lg-row align-items-center">
				<div class="col-12 col-lg-6 d-flex flex-column">
					<div class="row text-center text-lg-start align-items-start">
						<a class="navbar-brand" href="<?php echo esc_url( get_site_url() ); ?>/">
							<h1><?php echo esc_html( strtoupper( get_bloginfo( 'name' ) ) ); ?></h1>
						</a>
						<div id="description"><small><?php echo esc_html( get_bloginfo( 'description' ) ); ?></small></div>
					</div>
				</div>
				<div class="col-12 col-lg-6 d-flex flex-column align-items-center align-items-lg-end justify-content-center text-center text-lg-start mt-4">
					<ul id="total-stats" class="list-unstyled m-0 text-center text-lg-end">
						<li class="mb-4 mt-4 mt-lg-0 border-bottom stats-header">
							<?php echo esc_html( strtoupper( 'Current Riding Stats' ) ); ?>
						</li>
						<li><?php echo esc_html( $stats['riding_days'] ); ?> riding days</li>
						<li class="my-3"><?php echo esc_html( $stats['miles'] ); ?> miles</li>
						<li class="my-3">&uarr; <?php echo number_format( $stats['elev_gain'] ); ?> ft &darr; <?php echo number_format( $stats['elev_loss'] ); ?> ft</li>
						<li>
							<?php
							$flats_string = intval( $stats['flats'] ) !== 1 ? 'flats' : 'flat';
							echo esc_html( $stats['flats'] . ' ' . $flats_string );
							?>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</nav>

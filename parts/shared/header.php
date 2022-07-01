<?php
$header_image = get_field('header_image', get_page_by_title('Front Page')->ID);
?>

<nav class="navbar navbar-expand-sm navbar-light bg-light" style="background-image:url(<?php echo $header_image ?>);background-size:cover;background-position:center;box-shadow:inset 0 0 0 2000px rgba(255, 0, 150, 0.9);">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#primaryNav"
            aria-controls="primaryNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="offcanvas offcanvas-start" tabindex="-1" id="primaryNav">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasNavbarLabel"><?php echo get_bloginfo( 'name' ); ?></h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                    aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-4 py-3">
                <div class="col-6 d-flex flex-column justify-content-center">
                    <div class="">
                        <a class="navbar-brand d-none d-sm-inline-flex" href="<?php echo get_site_url(); ?>/">
                            <h1><?php echo get_bloginfo( 'name' ); ?></h1>
                        </a>
                    </div>
                    <div class=""><small><?php echo get_bloginfo( 'description' ); ?></small></div>
                </div>
                <div class="col-6 d-flex flex-column align-items-end justify-content-center">
                    <ul class="list-unstyled m-0">
                        <li>X Riding Days</li>
                        <li>X Miles</li>
                        <li>&uarr; X ft &darr; X ft</li>
                        <li>X Flats</li>
                        <li>X Arguments</li>
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
                <!-- <?php get_search_form(); ?> -->
            </div>
        </div>
    </div>
</nav>
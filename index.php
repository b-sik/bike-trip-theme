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

function content_excerpt($length, $content = null)
{
	$excerpt = '';
	if ($content) {
		$excerpt = $content;
	} else {
		$excerpt = get_the_excerpt();
	}

	$excerpt = substr($excerpt, 0, $length); // Only display first x characters of excerpt
	$result  = substr($excerpt, 0, strrpos($excerpt, ' '));

	return $result . '...';
}

$all_gpx_file_names = '';
$upload_dir         = wp_upload_dir();
$gpx_dir_url        = $upload_dir['baseurl'] . '/2022/GPX/';
$gpx_color_list     = '';
$gpx_color_options 	= array('blue', 'green', 'red', 'grey', 'black', 'purple', 'yellow', 'orange');
$gpx_color_count 	= 0;

if (have_posts()) :
	while (have_posts()) :
		the_post();
		$fields = get_fields();
		if (!empty($fields['day_number'] && filter_var($fields['miles_and_elevation']['rest_day'], FILTER_VALIDATE_BOOLEAN) === false)) {
			$all_gpx_file_names = $all_gpx_file_names . $gpx_dir_url . $fields['day_number'] . '.gpx,';

			$gpx_color_list     = $gpx_color_list . $gpx_color_options[$gpx_color_count] . ',';

			$gpx_color_count++;
			if ($gpx_color_count > 7) {
				$gpx_color_count = 0;
			}
		}
	endwhile;
endif;

$all_gpx_file_names = rtrim($all_gpx_file_names, ',');
$gpx_color_list     = rtrim($gpx_color_list, ',');

$fields = get_fields(get_page_by_title('Front Page')->ID);

$intro_post    = get_page_by_title('Intro');
$intro_content = apply_filters('the_content', $intro_post->post_content);
?>

<section id="osm_overview_map" class="container my-5">
	<div class="row">
		<div class="osm-wrapper">
			<?php
			echo do_shortcode(osm_shortcode($all_gpx_file_names, 400, true, $gpx_color_list));
			?>
		</div>
	</div>
</section>
<section id="all_posts" class="container min-vh-100 my-2">
	<div class="row g-2">

		<?php if ($intro_post) : ?>
			<div id="intro-post" class="col-12">
				<div class="card intro-card bg-dark">
					<a href="<?php esc_url(the_permalink($intro_post->ID)); ?>" class="text-white text-center" title="<?php echo $intro_post->post_title; ?>" rel="bookmark">
						<?php if (!empty(get_the_post_thumbnail_url($intro_post->ID))) : ?>
							<img class="card-img img-responsive" src="<?php echo get_the_post_thumbnail_url($intro_post->ID); ?>" alt="Card image">
						<?php endif; ?>
						<div class="card-img-overlay d-flex flex-column justify-content-between">
							<?php echo content_excerpt($fields['intro_excerpt_length'], $intro_content); ?>
						</div>
					</a>
				</div>
			</div>
		<?php endif; ?>

		<?php
		if (have_posts()) :

			while (have_posts()) :
				the_post();
		?>
				<div class="col-12 col-md-6">
					<div class="card bg-dark">
						<a href="<?php esc_url(the_permalink()); ?>" class="text-white" title="<?php the_title(); ?>" rel="bookmark">
							<?php if (!empty(get_the_post_thumbnail_url())) : ?>
								<img class="card-img" src="<?php echo get_the_post_thumbnail_url(); ?>" alt="Card image">
							<?php endif; ?>
							<div class="card-img-overlay d-flex flex-column justify-content-between">
								<h5 class="card-title">
									<?php the_title(); ?> </h5>
								<p class="card-text"><?php echo content_excerpt($fields['post_excerpt_length']); ?></p>
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
$BsWp->get_template_parts(
	array(
		'parts/shared/footer',
		'parts/shared/html-footer',
	)
);
?>
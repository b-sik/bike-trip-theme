<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * Please see /external/bootsrap-utilities.php for info on BsWp::get_template_parts()
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
?>

<div class="container my-5">
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) :
			the_post();
			?>

			<?php the_content(); ?>

			<?php
	endwhile;
	};
	?>
</div>

<?php
$BsWp->get_template_parts(
	array(
		'parts/shared/footer',
		'parts/shared/html-footer',
	)
);
?>

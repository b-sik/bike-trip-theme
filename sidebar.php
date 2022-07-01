<?php
/**
 * Default sidebar
 *
 * Please see /external/bootstrap-utilities.php for info on BsWp::get_template_parts()
 *
 * @package     WordPress
 * @subpackage  Bootstrap 5.1.3
 * @autor       Babobski
 */
?> <div class="d-none d-md-block col-3"> <?php get_search_form(); ?> <ul class="mt-2 ms-2 list-unstyled"> 
<?php
	$args = array(
		'post_type' => 'post',
	);

	$post_query = new WP_Query( $args );
	if ( $post_query->have_posts() ) :
		while ( $post_query->have_posts() ) :
			$post_query->the_post();
			?>
			<li>
			<a href=" <?php esc_url( the_permalink() ); ?>" title="<?php the_title(); ?>" rel="bookmark">
			<?php the_title(); ?> </a>
		</li> <?php endwhile; ?> <?php endif; ?> </ul>
</div>

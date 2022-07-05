<?php
/**
 * The Template for displaying all single posts
 *
 * Please see /external/bootstrap-utilities.php for info on BsWp::get_template_parts()
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
?><div class="content container-fluid my-2"> 
<?php
if ( have_posts() ) :

	?>
	 <div class="row p-3"> <?php get_sidebar(); ?> <div class="col-12 col-md-9"> 
	<?php
												the_post();
												$fields = get_fields();
	?>
												 <h2 class="mb-3"> <?php the_title(); ?> </h2>
			<div class="row">
				<hr />
				<ul class="d-flex justify-content-around list-unstyled">
					<li>
						<time datetime="<?php echo $fields['date']; ?>" pubdate> <?php echo $fields['date']; ?> </time>
					</li>
					<li> 
					<?php
					if ( $fields['miles_and_elevation']['rest_day'] ) :
						echo 'Rest Day';
				else :
					echo $fields['miles_and_elevation']['miles'] . ' Miles';
					echo '</li><li>';
					echo number_format($fields['miles_and_elevation']['elevation_gain']) . ' ft Elevation Gain';
					echo '</li>';
						endif;
				?>
				</ul>
				<hr />
			</div> 
			<?php
					the_content();
			?>
			 <div class="mt-4"> 
			<?php
					echo do_shortcode( osm_shortcode( get_field( 'gpx_filename', get_the_ID() ), 400 ) );
			?>
					 </div>
		</div>
	</div> 
	<?php

endif;
?>
 </div><?php
					$BsWp->get_template_parts(
						array(
							'parts/shared/footer',
							'parts/shared/html-footer',
						)
					);
					?>

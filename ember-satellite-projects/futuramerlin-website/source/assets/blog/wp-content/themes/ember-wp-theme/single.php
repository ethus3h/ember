<?php
/**
 * The Template for displaying all single posts.
 *
 * @package raze
 */

get_header(); ?>
<?php get_sidebar(); ?>

	<div id="primary" class="content-area col-lg-8 col-md-8">
		<main id="main" class="site-main">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php raze_post_nav(); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) :
					comments_template();
				endif;
			?>

		<?php endwhile; // end of the loop. ?>

		</main><!-- #main -->
	</div><!-- #primary -->
		</div>
    </div>
<?php wp_footer(); ?></body>
</html>

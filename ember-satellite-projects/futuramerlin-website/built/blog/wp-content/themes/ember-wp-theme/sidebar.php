<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package raze
 */
?>
	<div id="secondary" class="widget-area col-lg-4 col-md-4" role="complementary">
		<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>

			<aside id="search" class="widget widget_search">
				<?php get_search_form(); ?>
			</aside>

			<aside id="archives" class="widget">
				<h1 class="widget-title"><?php _e( 'Archives', 'raze' ); ?></h1>
				<ul>
					<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>
				</ul>
			</aside>

			<aside id="meta" class="widget">
				<h1 class="widget-title"><?php _e( 'Meta', 'raze' ); ?></h1>
				<ul>
					<?php wp_register(); ?>
					<li><?php wp_loginout(); ?></li>
					<?php wp_meta(); ?>
				</ul>
			</aside>

		<?php endif; // end top of page widget area ?>
		</div><!-- #secondary -->
        <section class="top-of-page-widgets">
        <?php 
            if(!is_front_page() || !is_home())
                { 
                    if ( ! dynamic_sidebar( 'sidebar-5' )) {}
                }
        ?>
        </section>

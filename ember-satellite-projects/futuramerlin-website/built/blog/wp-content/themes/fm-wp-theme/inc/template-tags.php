<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package raze
 */

if ( ! function_exists( 'raze_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function raze_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	raze_nav();
}
endif;

if ( ! function_exists( 'raze_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function raze_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'raze' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&larr; Previous post: </span>%title', 'Previous post link', 'raze' ) );
				next_post_link(     '<div class="nav-next">%link</div>',     _x( '<span class="meta-nav">Next post: </span>%title<span class="meta-nav"> &rarr;</span>', 'Next post link',     'raze' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'raze_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function raze_posted_on() {
	$time_string = '&#x1f551;:&nbsp;<time class="entry-date published" datetime="%1$s">%2$s</time><br />';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= '&#9998;:&nbsp;<time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	printf( __( '<span class="posted-on">%1$s</span><span class="byline">%2$s</span>', 'raze' ),
		sprintf( '<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			$time_string
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s">%2$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		)
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function raze_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'raze_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'raze_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so raze_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so raze_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in raze_categorized_blog.
 */
function raze_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'raze_categories' );
}
add_action( 'edit_category', 'raze_category_transient_flusher' );
add_action( 'save_post',     'raze_category_transient_flusher' );

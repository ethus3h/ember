<?php
/**
 * @package raze
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<div class="entry-meta">
			<?php raze_posted_on(); ?>
		</div><!-- .entry-meta -->

		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content container">
	<div class="featured-image col-lg-4 col-md-12"> 
	<a href="<?php the_permalink(); ?>">
	<div class="thumb">
	<?php if ( has_post_thumbnail() ) {
	the_post_thumbnail('homepage-banner');
	} ?> 
	</div>
	</a>
	</div>
		<div class="article-rest col-lg-8 col-md-12">
		<header class="entry-header">
		<?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

		<?php if ( 'post' == get_post_type() ) : ?>
				<?php endif; ?>
	</header><!-- .entry-header -->
		<div class="entry-meta">
			<?php raze_posted_on(); ?>
		</div><!-- .entry-meta -->

		<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'raze' ) ); ?> 
		<footer class="entry-footer">
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'raze' ) );
				if ( $categories_list && raze_categorized_blog() ) :
			?>
			<span class="cat-links">
				<?php printf( __( '%1$s', 'raze' ), $categories_list ); ?>
			</span>
			<?php endif; // End if categories ?>

			<?php
				/* translators: used between list items, there is a space after the comma */
				$tags_list = get_the_tag_list( '', __( ', ', 'raze' ) );
				if ( $tags_list ) :
			?>
			<span class="tags-links">
				<?php printf( __( '%1$s', 'raze' ), $tags_list ); ?>
			</span>
			<?php endif; // End if $tags_list ?>
		<?php endif; // End if 'post' == get_post_type() ?>

		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'raze' ), __( '1 Comment', 'raze' ), __( '% Comments', 'raze' ) ); ?></span>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'raze' ), '<span class="edit-link">', '</span>' ); ?>
	</footer></div>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'raze' ),
				'after'  => '</div>',
			) );
		?>
	</div>
	<?php endif; ?>

	
</article>

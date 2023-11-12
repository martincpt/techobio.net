<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package cactus
 */
?>

<div class="single-page-content single-content"><!--Single Page Content-->
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="body-content">
            <!--Content-->
            <?php the_content(); ?>
			<?php
                wp_link_pages( array(
                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'videopro' ),
                    'after'  => '</div>',
                ) );
            ?>
            <!--Content-->
            <div class="entry-footer">
				<?php edit_post_link( esc_html__( 'Edit', 'videopro' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-footer -->
        </div>
    </article>

</div>
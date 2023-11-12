<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package cactus
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
$live_cm = get_post_meta($post->ID,'enable_live_video',true);
$videopro_post_video_layout = videopro_global_video_layout();
if($live_cm == 'on' && $videopro_post_video_layout != '2'){
	get_template_part( 'live-comments');
	return;
}
?>

<div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				echo esc_html__('Comment', 'videopro').' (<span id="tdt-f-number-calc">'.number_format_i18n( get_comments_number() ).'</span>)';
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above" class="comment-navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'videopro' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'videopro' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'videopro' ) ); ?></div>
		</nav><!-- #comment-nav-above -->
		<?php endif; // check for comment navigation 
		?>
		<ol class="comment-list">
        
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'       => 50,
			));
			?>
		</ol><!-- .comment-list -->
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below" class="comment-navigation">
			<h1 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', 'videopro' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'videopro' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'videopro' ) ); ?></div>
		</nav><!-- #comment-nav-below -->
		<?php endif; // check for comment navigation ?>

	<?php endif; // ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'videopro' ); ?></p>
	<?php endif; ?>

	<?php 
	$arrFixCommentPlaceholder = array(
								'comment_field' => '<textarea id="comment" name="comment" aria-required="true" required="required" placeholder="'.esc_html__('Your comment *','videopro').'"></textarea>',
								'title_reply'       => esc_html__( 'LEAVE YOUR COMMENT', 'videopro' ),
								'title_reply_to'    => esc_html__( 'Leave a Reply to %s', 'videopro' ),
								'format' 			=> 'xhtml'
								);
	comment_form($arrFixCommentPlaceholder); 
	?>

</div><!-- #comments -->

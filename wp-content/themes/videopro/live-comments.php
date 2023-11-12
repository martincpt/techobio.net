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
?>

<div id="comments" class="live-comment dark-div">
	<div id="comment-status" ></div>
	<?php 
	$arrFixCommentPlaceholder = array(
								'comment_field' => '<input id="comment" name="comment" aria-required="true" placeholder="'.esc_html__('Your comment  *','videopro').'">',
								'title_reply'       => esc_html__( 'YOUR COMMENTS', 'videopro' ),
								'title_reply_to'    => esc_html__( 'Leave a Reply to %s', 'videopro' ),
								'label_submit'    => esc_html__( 'SEND', 'videopro' ),
								);
	comment_form($arrFixCommentPlaceholder); 
    ?>
	<div class="comment-content-wrap">
    <div id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
				echo esc_html__('Comment', 'videopro').' (<span id="tdt-f-number-calc">'.number_format_i18n( get_comments_number() ).'</span>)';
			?>
		</h2>

		<?php
		$order ='DESC';
		$cm_auto_refresh='10';
		?>
        <input type="hidden" name="videopro_livecm_post_id" value="<?php echo esc_attr($post->ID); ?>">
        <input type="hidden" name="videopro_livecm_crtime" value="<?php echo esc_attr(current_time( 'timestamp' )); ?>">
        <input type="hidden" name="videopro_livecm_refre" value="<?php echo esc_attr($cm_auto_refresh); ?>">
        <input type="hidden" name="videopro_livecm_nuurl" value="<?php echo esc_url(wp_nonce_url(home_url('/').'?id='.$post->ID,'idn'.$post->ID,'ct_comment_wpnonce')); ?>">
        <input type="hidden" name="videopro_livecm_url_more" value="<?php echo esc_url(wp_nonce_url(home_url('/').'?id='.$post->ID.'&ids='.$post->ID,'idn'.$post->ID,'cactus_load_cm')); ?>">
        <input type="hidden" name="videopro_text_plst" value="<?php esc_html_e('Please type a comment.','videopro'); ?>">
        <input type="hidden" name="videopro_text_dlc" value="<?php esc_html_e('Duplicate comment detected; it looks as though you\'ve already said that!','videopro'); ?>">
        <input type="hidden" name="videopro_text_tfy" value="<?php esc_html_e('Thanks for your comment','videopro'); ?>">
        <input type="hidden" name="videopro_text_plwa" value="<?php esc_html_e('Please wait a while before posting your next comment','videopro'); ?>">
		<?php 
		$arr_all = array(
			'comment__not_in' => '',
			'post_id' => $post->ID,
			'order' => $order,
			'number' => '',   
		);
		$cm_curent = array();
		foreach(get_comments($arr_all) as $comment) :
			$cm_curent[] = $comment->comment_ID;
		endforeach;
		$arr = array(
			'comment__not_in' => '',
			'post_id' => $post->ID,
			'number' => get_option( 'comments_per_page' ),
		);
		$cm = get_comments($arr);
		$show_cm_it = array();
		foreach(get_comments($arr) as $it_comment) :
			$show_cm_it[] = $it_comment->comment_ID;
		endforeach;
		?>
		<ol class="comment-list">
        	<input type="hidden" id="list_cm" name="list_cm" value="<?php echo implode(",",$show_cm_it);?>">
            <input type="hidden" id="page_cm" name="page_cm" value="1">
			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
				'avatar_size'       => 50,
			),$cm);
				
			?>
		</ol><!-- .comment-list -->
		<?php if(count($cm_curent) > get_option( 'comments_per_page' )){?>
            <div class="page-navigation">	
            	<nav class="navigation-ajax" role="navigation">
                    <div class="wp-pagenavi">
                        <a id="load-comment-<?php echo esc_attr($post->ID); ?>" href="javascript:;" class="loadmore-comment load-more">
                        	<div class="load-title"><?php esc_html_e('More Comments','videopro'); ?></div>
                            <i class="fas fa-sync hidden-loading fa-pulse" id="load-spin"></i>
                        </a>
                    </div>
                </nav>	
			</div>
        <?php }?>
	<?php endif; // ?>

	<?php
		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'videopro' ); ?></p>
	<?php endif; ?>
	</div>
    </div>
</div><!-- #comments -->

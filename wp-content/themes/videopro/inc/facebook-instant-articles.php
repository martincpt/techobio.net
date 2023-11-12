<?php
add_action( 'instant_articles_before_transform_post', 'videopro_fbia_before_transform_post' );
add_action( 'instant_articles_after_transform_post', 'videopro_fbia_after_transform_post' );

/**
 * Run actions before content is processed.
 */
function videopro_fbia_before_transform_post() {
    add_filter( 'the_content', 'videopro_fia_add_video_before_content' );
}

/**
 * Run actions after content is processed.
 */
function videopro_fbia_after_transform_post() {
    remove_filter( 'the_content', 'videopro_fia_add_video_before_content' );
}

/**
 * Add video to the Instant Article's content
 *
 * @param str $content  The Content.
 * @return str
 */
function videopro_fia_add_video_before_content( $content ) {
    $post_id = get_the_ID();
    $post_format = get_post_format();
    if ($post_format == 'video') {
        ob_start();
        echo do_shortcode('[cactus_player_fia]');
        $html_for_video = ob_get_clean();
        $content = $html_for_video . $content;
        return $content;
    }
}
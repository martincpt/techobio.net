<?php

/**
 * get video-membership options
 */
function videopro_video_membership_get_options(){
    $options = get_option('videopro_video_membership');
    
    if(!is_array($options)) {$options = array();}
    
    $opts = array_merge(array(
                'channel-limit' => 0,
                'channel-limited-memberships' => array(),
                'channel-unlimited-memberships' => array(),
                'video-limit' => 0,
                'video-limited-memberships' => array(),
                'video-unlimited-memberships' => array(),
                'playlist-limit' => 0,
                'playlist-limited-memberships' => array(),
                'playlist-unlimited-memberships' => array(),
                'video-ads-memberships' => array(),
                'days-before-warning' => 7,
                'expired-warning' => esc_html__('Your subscription: {name_tag} is about to expired in <strong>{days}</strong> days', 'videopro'),
                'expired-error' => esc_html__('Your subscription: {name_tag} is expired', 'videopro'),
                'default-channel-status' => 'publish',
                'default-playlist-status' => 'publish',
                'default-video-status' => 'publish'
            ), $options);
    
    return $opts;
}
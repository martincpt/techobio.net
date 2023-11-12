<?php  
        
add_action( 'widgets_init', 'videopro_buddypress_widgets_init' );
function videopro_buddypress_widgets_init(){
    //buddyPress
    register_sidebar( array(
        'name' => __( 'BuddyPress Sidebar', 'videopro' ),
        'id' => 'bp_sidebar',
        'description' => __( 'Sidebar in BuddyPress Page.', 'videopro' ),
        'before_widget' => '<aside id="%1$s" class="widget %2$s"><div class="widget-inner">',
		'after_widget'  => '</div></aside>',
		'before_title'  => '<h2 class="widget-title h4">',
		'after_title'   => '</h2>',
    ));
}

/**
 * add more options in Theme Options for BuddyPress
 */
add_filter('option_tree_settings_args', 'videpro_buddypress_theme_options');
function videpro_buddypress_theme_options($settings){
    $settings['sections'][] = array(
                'id' => 'buddypress',
                'title' => esc_html__('BuddyPress', 'videopro')
            );
    
    $settings['settings'][] = array(
            'id'          => 'bp_sidebar',
            'label'       => esc_html__('Main Sidebar', 'videopro'),
            'desc'        => esc_html__('This Sidebar appears in all BuddyPress pages', 'videopro' ),
            'std'         => 'full',
            'type'        => 'select',
            'section'     => 'buddypress',
            'choices'     => array(
                        array(
                            'value'       => 'both',
                            'label'       => esc_html__('Both sidebar','videopro')
                          ),    
                          array(
                            'value'       => 'right',
                            'label'       => esc_html__( 'Right', 'videopro' )
                          ),
                          array(
                            'value'       => 'left',
                            'label'       => esc_html__( 'Left', 'videopro' )
                          ),
                          array(
                            'value'       => 'full',
                            'label'       => esc_html__( 'Hidden', 'videopro' )
                          )
            )
          );
          
    $settings['settings'][] = array(
            'id'          => 'bp_layout',
            'label'       => esc_html__('Layout', 'videopro'),
            'desc'        => esc_html__('This Layout setting applies for all BuddyPress pages', 'videopro' ),
            'std'         => 'fullwidth',
            'type'        => 'select',
            'type'        => 'radio-image',
            'section'     => 'buddypress',
            'choices'     => array(
                array(
                  'value'       => 'fullwidth',
                  'label'       => esc_html__('Full-width', 'videopro' ),
                  'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-01-fullwidth.jpg'
                ),
                array(
                  'value'       => 'boxed',
                  'label'       => esc_html__('Inbox', 'videopro' ),
                  'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-02-boxed.jpg'
                ),
                array(
                  'value'       => 'wide',
                  'label'       => esc_html__('Wide', 'videopro' ),
                  'src'         => get_template_directory_uri() . '/images/theme-options/theme-layout-03-wide.jpg'
                ),
            ));
            
    $settings['settings'][] = array(
            'id'          => 'bp_videos_nav_pos',
            'label'       => esc_html__('Videos Tab Position In Member Profile', 'videopro'),
            'desc'        => esc_html__('Enter priority number for the Videos nav item. The lower the number is, the closer the nav item to the first is', 'videopro' ),
            'std'         => '10',
            'type'        => 'text',
            'section'     => 'buddypress');
            
    $settings['settings'][] = array(
            'id'          => 'bp_activity_items',
            'label'       => esc_html__('Activity Items','videopro'),
            'desc'        => esc_html__('Choose which actions to be included in the Activity. Once an user does the action, an activity log is recorded', 'videopro' ),
            'std'         => '',
            'type'        => 'checkbox',
            'section'     => 'buddypress',
            'choices'     => array(
                array(
                  'value'       => 'upload_video',
                  'label'       => esc_html__('Upload Video', 'videopro' )
                ),
                array(
                  'value'       => 'edit_video',
                  'label'       => esc_html__('Edit Video', 'videopro' )
                ),
                array(
                  'value'       => 'create_channel',
                  'label'       => esc_html__('Create Channel', 'videopro' )
                ),
                array(
                  'value'       => 'edit_channel',
                  'label'       => esc_html__('Edit Channel', 'videopro' )
                ),
                array(
                  'value'       => 'create_playlist',
                  'label'       => esc_html__('Create Playlist', 'videopro' )
                ),
                array(
                  'value'       => 'edit_playlist',
                  'label'       => esc_html__('Edit Playlist', 'videopro' )
                ),
				array(
                  'value'       => 'subscribe_channel',
                  'label'       => esc_html__('Subscribe Channel', 'videopro' )
                ),
                array(
                  'value'       => 'subscribe_author',
                  'label'       => esc_html__('Subscribe Author', 'videopro' )
                ),
				array(
                  'value'       => 'unsubscribe_channel',
                  'label'       => esc_html__('Unsubscribe Channel', 'videopro' )
                ),
                array(
                  'value'       => 'unsubscribe_author',
                  'label'       => esc_html__('Unsubscribe Author', 'videopro' )
                )
				
            ));
			
	$settings['settings'][] = array(
            'id'          => 'bp_notifications',
            'label'       => esc_html__('Show Notification Bubble', 'videopro'),
            'desc'        => esc_html__('Show Notification Bubble on Top Header', 'videopro' ),
            'std'         => 'off',
            'type'        => 'on-off',
            'section'     => 'buddypress'
          );
            
    
    return $settings;
}

add_filter('videopro-get-page-sidebar-setting', 'videopro_buddypress_sidebar_settings');
function videopro_buddypress_sidebar_settings($sidebar_setting){
    if(function_exists('bp_current_component') && bp_current_component()){ //buddypress
        $sidebar_setting = ot_get_option('bp_sidebar', 'full');
    }
    
    return $sidebar_setting;
}

/**
 * change layout settings for BuddyPress pages
 */
add_filter('videopro-global-layout-setting', 'videopro_global_layout_setting');
function videopro_global_layout_setting($layout_setting){
    if(function_exists('bp_current_component') && bp_current_component()){ //buddypress
        $layout_setting = ot_get_option('bp_layout', 'fullwidth');
    }

    return $layout_setting;
}

/**
 * filter page title of BuddyPress pages
 */
add_filter('videopro-page-title', 'videopro_buddypress_page_title');
function videopro_buddypress_page_title($title){
    if(function_exists('bp_is_member') && bp_is_member()){ //buddypress single member page
        $title = '';
    }
    
    return $title;
}


/**
 * Add a Videos tab to the profile page
 */

add_action( 'bp_setup_nav', 'videopro_buddypress_setup_nav' );
add_action( 'bp_setup_nav', 'videopro_setup_subnav', 302 );

function videopro_buddypress_setup_nav() {    
	  global $bp;
      bp_core_new_nav_item( array( 
            'name' => apply_filters('videopro_buddypress_videos_nav_text', esc_html__( 'Videos', 'videopro' )), 
            'slug' => 'videos', 
            'position' => ot_get_option('bp_videos_nav_pos', 10),
            'screen_function' => 'videopro_buddypress_videos_screen',
			'default_subnav_slug' => 'myvideos',
      ) );
}

/**
 * Add sub nav items for Videos tab 
 */
function videopro_setup_subnav() {
	global $bp;
	bp_core_new_subnav_item( array( 
		'name' => esc_html__( 'My Videos','videopro'),
		'slug' => 'myvideos', 
		'parent_url' => trailingslashit( bp_displayed_user_domain() . 'videos' ) ,
		'parent_slug' => 'videos',
		'position' => apply_filters('videopro_bp_subnav_videos_pos', 1),
		'screen_function' => 'videopro_buddypress_videos_screen'
		) 
	);
    
    bp_core_new_subnav_item( array( 
		'name' => esc_html__( 'My Channels','videopro'),
		'slug' => 'channels', 
		'parent_url' => trailingslashit( bp_displayed_user_domain() . 'videos' ) ,
		'parent_slug' => 'videos',
		'position' => apply_filters('videopro_bp_subnav_channels_pos', 2),
		'screen_function' => 'videopro_buddypress_channels_screen'
		) 
	);
    
    bp_core_new_subnav_item( array( 
		'name' => esc_html__( 'My Playlists','videopro'),
		'slug' => 'playlists', 
		'parent_url' => trailingslashit( bp_displayed_user_domain() . 'videos' ) ,
		'parent_slug' => 'videos',
		'position' => apply_filters('videopro_bp_subnav_playlists_pos', 3),
		'screen_function' => 'videopro_buddypress_playlists_screen'
		) 
	);
    
    if(get_current_user_id() && get_current_user_id() == bp_displayed_user_id() && function_exists('osp_get')){
        // only logged in user can see this. He can see his own subscribed channels & authors
        $enable_author_sub = osp_get('ct_video_settings', 'author_subscription');
        
        if($enable_author_sub == 'on'){
            bp_core_new_subnav_item( array( 
                'name' => esc_html__( 'Subscribed Authors','videopro'),
                'slug' => 'subscribed-authors', 
                'parent_url' => trailingslashit( bp_displayed_user_domain() . 'videos' ) ,
                'parent_slug' => 'videos',
                'position' => apply_filters('videopro_bp_subnav_subscribed_authors_pos', 4),
                'screen_function' => 'videopro_buddypress_subscribed_authors_screen'
                ) 
            );
        }
        
        $enable_video_channels = unserialize(get_option('ct_channel_settings'));
		$enable_video_channels = isset($enable_video_channels['enable_video_channels']) ? $enable_video_channels['enable_video_channels'] : '';
        
        if($enable_video_channels != false){
            bp_core_new_subnav_item( array( 
                'name' => esc_html__( 'Subscribed Channels','videopro'),
                'slug' => 'subscribed-channels', 
                'parent_url' => trailingslashit( bp_displayed_user_domain() . 'videos' ) ,
                'parent_slug' => 'videos',
                'position' => apply_filters('videopro_bp_subnav_subscribed_channels_pos', 5),
                'screen_function' => 'videopro_buddypress_subscribed_channels_screen'
                ) 
            );
        }
    }
}

/**
 * register tabs
 */
function videopro_buddypress_channels_screen(){
    add_action( 'bp_template_content', 'videopro_buddypress_channels_tab' );
    bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function videopro_buddypress_videos_screen(){	
	add_action( 'bp_template_content', 'videopro_buddypress_videos_tab' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function videopro_buddypress_playlists_screen(){	
	add_action( 'bp_template_content', 'videopro_buddypress_playlists_tab' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function videopro_buddypress_subscribed_authors_screen(){	
	add_action( 'bp_template_content', 'videopro_buddypress_subscribed_authors_tab' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function videopro_buddypress_subscribed_channels_screen(){	
	add_action( 'bp_template_content', 'videopro_buddypress_subscribed_channels_tab' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

/**
 * print out Subscribed Channels tab 
 */
function videopro_buddypress_subscribed_channels_tab(){
    $meta_user = get_user_meta(get_current_user_id(), 'subscribe_channel_id',true);
    
    if(!is_array($meta_user) && $meta_user!=''){
        $meta_user = explode(" ", $meta_user );
    }
    
    if(empty($meta_user)){$meta_user = array(-1);}
    
    $paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page') : 1);
    
    $items_per_page = videopro_get_posts_per_page_subscribed_channels();
    
    $query = new WP_Query( array( 'post_type' => 'ct_channel', 
                            'post__in' => $meta_user , 
                            'paged' => $paged,
                            'posts_per_page' => $items_per_page
                            ) );
    
    if($query->have_posts()){        
        $it = $query->post_count;
        ?>
        <div class="cactus-listing-wrap subscribe-listing" data-page="1" data-nonce="<?php echo wp_create_nonce('subscribed-channels');?>" data-more="<?php echo $it < $items_per_page ? '0' : '1';?>" data-template="html/loop/subscribed-channel.php">
            <?php	
            
            $file = locate_template('html/loop/subscribed-channel.php');
                
            while ( $query->have_posts() ) : $query->the_post(); 
                include $file;
            endwhile;

            wp_reset_postdata();
            ?>
        </div>
        <div id="ajax-anchor" class=""><img class="ajax-loader" src="<?php echo get_template_directory_uri();?>/images/ajax-loader.gif" alt="Sending ..."></div>
        <?php
    } else {?>
        <div class="no-post">
            <h2 class="h4"><?php echo wp_kses(__('You do not have any subscriptions.<br>Browse Channels to subscribe.','videopro'),array('br'=>array()));?></h2>
            <?php
            $query = new WP_Query( array('post_type'  => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'cactus-video/includes/page-templates/channel-listing.php' ) );
            if ( $query->have_posts() ){
                while ( $query->have_posts() ) : $query->the_post();?>
                <a href="<?php echo esc_url(get_permalink());?>" class="btn btn-default"><?php esc_html_e('Browse Channels','videopro');?></a>
                <?php 
            endwhile; 
            wp_reset_postdata();
            } else{ ?>
                <a href="<?php echo get_post_type_archive_link('ct_channel');?>" class="btn btn-default"><?php esc_html_e('Browse Channels','videopro');?></a>
            <?php } ?>
        </div>
    <?php }
}

/**
 * print out Subscribed Authors tab 
 */
function videopro_buddypress_subscribed_authors_tab(){
	do_action('videopro_before_buddypress_subscribed_authors_tab_content');
	
    $subscribed_authors = get_user_meta(get_current_user_id(), 'subscribe_authors',true);
    if(!is_array($subscribed_authors) && $subscribed_authors!=''){
        $subscribed_authors = explode(" ", $subscribed_authors );
    }
    if(empty($subscribed_authors)){$subscribed_authors = array(-1);}
    
    $items_per_page = videopro_get_posts_per_page_subscribed_authors();
    
    $users = get_users(array(
                    'include' => $subscribed_authors,
                    'paged' => 1,
                    'number' => $items_per_page
                ));
                
    if(count($users) > 0){
        ?>
        <div class="page-template-subscribed-authors">
            <div class="cactus-listing-wrap subscribe-listing" data-page="1" data-nonce="<?php echo wp_create_nonce('subscribed-authors');?>" data-more="<?php echo count($users) < $items_per_page ? '0' : '1';?>" data-template="html/loop/subscribed-author.php">
                <?php	
                
                $file = locate_template('html/loop/subscribed-author.php');

                foreach($users as $user){
                    include $file;
                }
                    
                ?>
            </div>
        </div>
        <div id="ajax-anchor" class=""><img class="ajax-loader" src="<?php echo get_template_directory_uri();?>/images/ajax-loader.gif" alt="Sending ..."></div>
        <?php
    } else { ?>
        <div class="no-post">
            <h2 class="h4"><?php echo wp_kses(__('You do not have any subscriptions.<br>Browse Authors to subscribe.','videopro'),array('br'=>array()));?></h2>
            <?php
            $query = new WP_Query( array('post_type'  => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'cactus-video/includes/page-templates/authors-listing.php' ) );
            if ( $query->have_posts() ){
                while ( $query->have_posts() ) : $query->the_post();?>
                <a href="<?php echo esc_url(get_permalink());?>" class="btn btn-default"><?php esc_html_e('Browse Authors','videopro');?></a>
                <?php 
            endwhile; 
            wp_reset_postdata();
            }else{?>
                <a href="#" class="btn btn-default"><?php esc_html_e('Browse Authors','videopro');?></a>
            <?php }?>
        </div>
    <?php }
}

function videopro_buddypress_playlists_tab(){
    $paged = isset($_GET['p']) ? intval($_GET['p']) : 1;
    
    $args = array(
                'post_type' => 'ct_playlist',
                'post_status' => 'publish',
                'author' => bp_displayed_user_id(),
                'paged' => $paged
                );
    
    $query = new WP_Query($args);
	
	do_action('videopro_before_buddypress_playlists_tab_content');
    ?>
    <div class="cactus-listing-wrap">
        <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
            <div class="cactus-sub-wrap" id="bp_playlists">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <!--item listing-->                                                
                        <?php get_template_part( 'html/loop/content', get_post_format() ); ?>
                    <?php endwhile; 
                    
                    wp_reset_postdata();
                    ?>
                <?php else : ?>
                <section class="no-results not-found">
                    <div class="page-content">
                        <p>
                    <?php 
                    echo wp_kses_post(__('This author hasn\'t got any playlists yet','videopro'));
                    ?>
                        </p>
                    </div>
                </section>
                <?php endif; ?>
                <!--item listing-->
            </div>
            <?php videopro_paging_nav_ajax('#bp_playlists','html/loop/content', esc_html__( 'Load More', 'videopro' ), $query); ?>
        </div>
    </div>
    <?php
}

function videopro_buddypress_channels_tab(){
	do_action('videopro_before_buddypress_subscribed_channels_tab_content');
	
    $paged = isset($_GET['p']) ? intval($_GET['p']) : 1;
    
    $args = array(
                'post_type' => 'ct_channel',
                'post_status' => 'publish',
                'author' => bp_displayed_user_id(),
                'paged' => $paged
                );
    
    $query = new WP_Query($args);
    ?>
    <div class="cactus-listing-wrap">
        <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
            <div class="cactus-sub-wrap" id="bp_channels">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <!--item listing-->                                                
                        <?php get_template_part( 'html/loop/content', get_post_format() ); ?>
                    <?php endwhile; 
                    
                    wp_reset_postdata();
                    ?>
                <?php else : ?>
                <section class="no-results not-found">
                    <div class="page-content">
                        <p>
                    <?php 
                    esc_html_e('This author hasn\'t got any channels yet','videopro');
                    ?>
                        </p>
                    </div>
                </section>
                <?php endif; ?>
                <!--item listing-->
            </div>
            <?php videopro_paging_nav_ajax('#bp_channels','html/loop/content', esc_html__( 'Load More', 'videopro' ), $query); ?>
        </div>
    </div>
    <?php
}

/**
 * Videos tab in BuddyPress profile
 */
function videopro_buddypress_videos_tab() {
	do_action('videopro_before_buddypress_subscribed_videos_tab_content');
	
    $paged = isset($_GET['p']) ? intval($_GET['p']) : 1;
    
    $args = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'author' => bp_displayed_user_id(),
                'paged' => $paged
                );
    
    $query = new WP_Query($args);
    ?>
    <div class="cactus-listing-wrap">
        <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
            <div class="cactus-sub-wrap" id="bp_videos">
                <?php if ( $query->have_posts() ) : ?>
                    <?php while ( $query->have_posts() ) : $query->the_post(); ?>
                    <!--item listing-->                                                
                        <?php get_template_part( 'html/loop/content', get_post_format() ); ?>
                    <?php endwhile; 
                    
                    wp_reset_postdata();
                    ?>
                <?php else : ?>
                <section class="no-results not-found">
                    <div class="page-content">
                        <p>
                    <?php 
                    echo wp_kses_post(__('This author hasn\'t got any videos yet','videopro'));
                    ?>
                        </p>
                    </div>
                </section>
                <?php endif; ?>
                <!--item listing-->
            </div>
            <?php videopro_paging_nav_ajax('#bp_videos','html/loop/content', esc_html__( 'Load More', 'videopro' ), $query); ?>
        </div>
    </div>
    <?php
}

/**
 * Add an activity in BuddyPress when users upload a video
 */
add_action('videopro_after_post_submission', 'videopro_buddypress_add_new_video_activity', 10, 4);
function videopro_buddypress_add_new_video_activity($new_ID, $posted_data, $is_uploaded_in_channel, $is_uploaded_in_playlist){
    $activity_items = ot_get_option('bp_activity_items', array());
    if(in_array('upload_video', $activity_items)){
        if(function_exists('bp_activity_add')){
            $user_id = get_current_user_id();
            $post = get_post($new_ID);
            
            if($user_id && $post->post_status == 'publish'){
                $userlink = bp_core_get_userlink( $user_id );
                
                $action = sprintf( esc_html__( '%s uploaded a new video', 'videopro' ), $userlink );
                if($is_uploaded_in_channel){
                    $channel_id = $posted_data['current_channel'];
                    $channel = get_post($channel_id);
                    $channel_link = '<a href="' . get_permalink($channel_id) . '" title="' . esc_attr($channel->post_title) . '">' . esc_html($channel->post_title) . '</a>';
                    
                    $action = sprintf( esc_html__( '%s uploaded a new video in channel: %s ', 'videopro' ), $userlink, $channel_link );
                } else if($is_uploaded_in_playlist){
                    $playlist_id = $posted_data['current_playlist'];
                    $playlist = get_post($playlist_id);
                    $playlist_link = '<a href="' . get_permalink($playlist_id) . '" title="' . esc_attr($playlist->post_title) . '">' . esc_html($playlist->post_title) . '</a>';
                    
                    $action = sprintf( esc_html__( '%s uploaded a new video in playlist: %s', 'videopro' ), $userlink, $playlist_link );
                }
                
                $videolink = '<a href="' . get_permalink($new_ID) . '" title="' . esc_attr($post->post_title) . '">' . esc_html($post->post_title) . '</a>';
                
                $thumbnail = '<a href="' . get_permalink($new_ID) . '" title="' . esc_attr($post->post_title) . '">' . get_the_post_thumbnail($new_ID, 'videopro_misc_thumb_9') . '</a>';

                $content = sprintf(wp_kses_post(__('<p>Check out this video:</p><br/> %s <br/> %s', 'videopro')), $thumbnail, $videolink);
                
                bp_activity_add( array(
                            'user_id' => $user_id,
                            'action' => apply_filters( 'videopro_bp_new_video_activity_action', $action, $user_id, $new_ID ),
                            'content' => apply_filters('videopro_bp_new_video_activity_content', $content, $user_id, $new_ID),
                            'component' => 'profile',
                            'type' => 'new_video'
                            ) );
            }
        }
    }
}

/**
 * Add an activity in BuddyPress when users create new channel
 */
add_action('videopro_user_create_channel_submit', 'videopro_buddypress_add_new_channel_activity');
function videopro_buddypress_add_new_channel_activity($channel_id){
    $activity_items = ot_get_option('bp_activity_items', array());
    if(in_array('create_channel', $activity_items)){
        if(function_exists('bp_activity_add')){
            $user_id = get_current_user_id();
            
            if($user_id){
                $userlink = bp_core_get_userlink( $user_id );
                
                $channel = get_post($channel_id);
                
                $channel_link = '<a href="' . get_permalink($channel_id) . '" title="' . esc_attr($channel->post_title) . '">' . esc_html($channel->post_title) . '</a>';
                
                $content = sprintf(wp_kses_post('See channel: %s', 'videopro'), $channel_link);
                
                bp_activity_add( array(
                            'user_id' => $user_id,
                            'action' => apply_filters( 'videopro_bp_new_channel_activity_action', sprintf( esc_html__( '%s created a new channel', 'videopro' ), $userlink ), $user_id ),
                            'content' => apply_filters('videopro_bp_new_channel_activity_content', $content, $user_id, $channel_id),
                            'component' => 'profile',
                            'type' => 'new_channel'
                            ) );
            }
        }
    }
}

/**
 * Add an activity in BuddyPress when users create new playlist
 */
add_action('videopro_user_create_playlist_submit', 'videopro_buddypress_add_new_playlist_activity');
function videopro_buddypress_add_new_playlist_activity($playlist_id){
    $activity_items = ot_get_option('bp_activity_items', array());
    if(in_array('create_playlist', $activity_items)){
        if(function_exists('bp_activity_add')){
            $user_id = get_current_user_id();
            
            if($user_id){
                $userlink = bp_core_get_userlink( $user_id );
                
                $playlist = get_post($playlist_id);
                
                $playlist_link = '<a href="' . get_permalink($playlist_id) . '" title="' . esc_attr($playlist->post_title) . '">' . esc_html($playlist->post_title) . '</a>';
                
                $content = sprintf(wp_kses_post('See playlist: %s', 'videopro'), $playlist_link);
                
                bp_activity_add( array(
                            'user_id' => $user_id,
                            'action' => apply_filters( 'videopro_bp_new_playlist_activity_action', sprintf( esc_html__( '%s created a new playlist', 'videopro' ), $userlink ), $user_id ),
                            'content' => apply_filters('videopro_bp_new_playlist_activity_content', $content, $user_id, $playlist_id),
                            'component' => 'profile',
                            'type' => 'new_playlist'
                            ) );
            }
        }
    }
}

/**
 * Add an activity in BuddyPress when users edit a video
 */
add_action('videopro-after-edit-video', 'videopro_buddpress_edit_video_activity', 10, 2);
function videopro_buddpress_edit_video_activity($video_id, $error_upload){
    $activity_items = ot_get_option('bp_activity_items', array());
    if(in_array('edit_video', $activity_items)){
        if(function_exists('bp_activity_add')){
            $user_id = get_current_user_id();
            
            if($user_id && !($error_upload)){
                $userlink = bp_core_get_userlink( $user_id );
                
                $post = get_post($video_id);
                
                $action = sprintf( esc_html__( '%s made some changes on a video', 'videopro' ), $userlink );
                
                $videolink = '<a href="' . get_permalink($video_id) . '" title="' . esc_attr($post->post_title) . '">' . esc_html($post->post_title) . '</a>';
                
                $thumbnail = '<a href="' . get_permalink($video_id) . '" title="' . esc_attr($post->post_title) . '">' . get_the_post_thumbnail($video_id, 'videopro_misc_thumb_9') . '</a>';

                $content = sprintf(wp_kses_post(__('<span class="activity-title">Video has been updated:</span> <span class="activity-thumbnail">%s</span> <br> <span class="activity-link">%s</span>', 'videopro')), $thumbnail, $videolink);
                
                bp_activity_add( array(
                            'user_id' => $user_id,
                            'action' => apply_filters( 'videopro_bp_edit_video_activity_action', $action, $user_id, $video_id ),
                            'content' => apply_filters('videopro_bp_edit_video_activity_content', $content, $user_id, $video_id),
                            'component' => 'profile',
                            'type' => 'edit_video'
                            ) );
            }
        }
    }
}

/**
 * Add an activity in BuddyPress when users edit a channel
 */
add_action('videopro-after-edit-channel', 'videopro_buddypress_edit_channel_activity');
function videopro_buddypress_edit_channel_activity($channel_id){
    $activity_items = ot_get_option('bp_activity_items', array());
    if(in_array('edit_channel', $activity_items)){
        if(function_exists('bp_activity_add')){
            $user_id = get_current_user_id();
            
            if($user_id){
                $userlink = bp_core_get_userlink( $user_id );
                
                $channel = get_post($channel_id);
                
                $channel_link = '<a href="' . get_permalink($channel_id) . '" title="' . esc_attr($channel->post_title) . '">' . esc_html($channel->post_title) . '</a>';
                
                $content = sprintf(wp_kses_post('<p>Channel has been updated:</p> %s', 'videopro'), $channel_link);
                
                bp_activity_add( array(
                            'user_id' => $user_id,
                            'action' => apply_filters( 'videopro_bp_edit_channel_activity_action', sprintf( esc_html__( '%s made some changes on a channel', 'videopro' ), $userlink ), $user_id ),
                            'content' => apply_filters('videopro_bp_edit_channel_activity_content', $content, $user_id, $channel_id),
                            'component' => 'profile',
                            'type' => 'edit_channel'
                            ) );
            }
        }
    }
}

/**
 * Add an activity in BuddyPress when users edit a playlist
 */
add_action('videopro-after-edit-playlist', 'videopro_buddypress_edit_playlist_activity');
function videopro_buddypress_edit_playlist_activity($playlist_id){
    $activity_items = ot_get_option('bp_activity_items', array());
    if(in_array('edit_playlist', $activity_items)){
        if(function_exists('bp_activity_add')){
            $user_id = get_current_user_id();
            
            if($user_id){
                $userlink = bp_core_get_userlink( $user_id );
                
                $playlist = get_post($playlist_id);
                
                $playlist_link = '<a href="' . get_permalink($playlist_id) . '" title="' . esc_attr($playlist->post_title) . '">' . esc_html($playlist->post_title) . '</a>';
                
                $content = sprintf(wp_kses_post('<p>Playlist has been updated:</p> %s', 'videopro'), $playlist_link);
                
                bp_activity_add( array(
                            'user_id' => $user_id,
                            'action' => apply_filters( 'videopro_bp_edit_playlist_activity_action', sprintf( esc_html__( '%s made some changes on a playlist', 'videopro' ), $userlink ), $user_id ),
                            'content' => apply_filters('videopro_bp_edit_playlist_activity_content', $content, $user_id, $playlist_id),
                            'component' => 'profile',
                            'type' => 'edit_playlist'
                            ) );
            }
        }
    }
}

add_action('videopro_after_unsubscribe_channel', 'videopro_buddypress_add_unsubscribe_channel_activity', 10, 2);
function videopro_buddypress_add_unsubscribe_channel_activity($channel_id, $user_id){
	$activity_items = ot_get_option('bp_activity_items', array());
	if(in_array('unsubscribe_channel', $activity_items)) {
		$channel = get_post($channel_id);
		if($channel){
			$author_id = $channel->post_author;
			
			$user_name = get_the_author_meta('display_name', $user_id);
			
			$userlink = bp_core_get_userlink( $user_id );
			$channellink = '<a href="' . get_permalink($channel_id) . '" target="_blank" title="' . $channel->post_title . '">' . $channel->post_title . '</a>';
			
			$content = sprintf(__('%s has unsubscribed your channel %s', 'videopro'), $userlink, $channellink);
			
			if(function_exists('bp_activity_add')){
				bp_activity_add( array(
								'user_id' => $author_id,
								'action' => apply_filters( 'videopro_bp_unsubscribe_channel_activity_action', esc_html__( 'User unsubscribed!', 'videopro' ), $user_id, $channel_id ),
								'content' => apply_filters('videopro_bp_unsubscribe_channel_activity_content', $content, $user_id, $channel_id),
								'component' => 'profile',
								'type' => 'unsubscribe_channel'
								) );
			}
			
			if(function_exists('bp_notifications_add_notification')){
				bp_notifications_add_notification(array(
					'user_id' => $author_id,
					'item_id' => $user_id,
					'secondary_item_id' => $channel_id,
					'component_name' => 'videopro',
					'component_action' => 'unsubscribe_channel',
					'date_notified' => bp_core_current_time(),
					'is_new' => 1
				));
			}
		}
	}
}

add_action('videopro_after_subscribe_channel', 'videopro_buddypress_add_subscribe_channel_activity', 10, 2);
function videopro_buddypress_add_subscribe_channel_activity($channel_id, $user_id){
	$activity_items = ot_get_option('bp_activity_items', array());
	
	if(in_array('subscribe_channel', $activity_items)) {
		$channel = get_post($channel_id);
		if($channel){
			$author_id = $channel->post_author;
			
			$user_name = get_the_author_meta('display_name', $user_id);
			
			$userlink = bp_core_get_userlink( $user_id );
			$channellink = '<a href="' . get_permalink($channel_id) . '" target="_blank" title="' . $channel->post_title . '">' . $channel->post_title . '</a>';
			
			$content = sprintf(__('%s has subscribed your channel %s', 'videopro'), $userlink, $channellink);
			
			if(function_exists('bp_activity_add')){
				bp_activity_add( array(
								'user_id' => $author_id,
								'action' => apply_filters( 'videopro_bp_subscribe_channel_activity_action', esc_html__( 'New user subscription', 'videopro' ), $user_id, $channel_id ),
								'content' => apply_filters('videopro_bp_subscribe_channel_activity_content', $content, $user_id, $channel_id),
								'component' => 'profile',
								'type' => 'subscribe_channel'
								) );
			}
			
			if(function_exists('bp_notifications_add_notification')){
				bp_notifications_add_notification(array(
					'user_id' => intval($author_id),
					'item_id' => $user_id,
					'secondary_item_id' => $channel_id,
					'component_name' => 'videopro',
					'component_action' => 'subscribe_channel',
					'date_notified' => bp_core_current_time(),
					'is_new' => 1
				));
			}
		}
	}
}

add_action('videopro_after_subscribe_author', 'videopro_buddypress_add_subscribe_author_activity', 10, 2);
function videopro_buddypress_add_subscribe_author_activity($author_id, $user_id){
	$activity_items = ot_get_option('bp_activity_items', array());
	if(in_array('subscribe_author', $activity_items)) {
		$user_name = get_the_author_meta('display_name', $user_id);
		
		$userlink = bp_core_get_userlink( $user_id );
		
		$content = sprintf(__('%s has subscribed you', 'videopro'), $userlink);
		
		if(function_exists('bp_activity_add')){
			bp_activity_add( array(
							'user_id' => $author_id,
							'action' => apply_filters( 'videopro_bp_subscribe_author_activity_action', esc_html__( 'New user subscription', 'videopro' ), $user_id, $channel_id ),
							'content' => apply_filters('videopro_bp_subscribe_author_activity_content', $content, $user_id, $channel_id),
							'component' => 'profile',
							'type' => 'subscribe_author'
							) );
		}
		
		if(function_exists('bp_notifications_add_notification')){
			bp_notifications_add_notification(array(
				'user_id' => $author_id,
				'item_id' => $user_id,
				'secondary_item_id' => '',
				'component_name' => 'videopro',
				'component_action' => 'subscribe_author',
				'date_notified' => bp_core_current_time(),
				'is_new' => 1
			));
		}
	}
}

add_action('videopro_after_unsubscribe_author', 'videopro_buddypress_add_unsubscribe_author_activity', 10, 2);
function videopro_buddypress_add_unsubscribe_author_activity($author_id, $user_id){
	$activity_items = ot_get_option('bp_activity_items', array());
	if(in_array('unsubscribe_author', $activity_items)) {
		$user_name = get_the_author_meta('display_name', $user_id);
		
		$userlink = bp_core_get_userlink( $user_id );
		
		$content = sprintf(__('%s has unsubscribed you', 'videopro'), $userlink);
		
		if(function_exists('bp_activity_add')){
			bp_activity_add( array(
							'user_id' => $author_id,
							'action' => apply_filters( 'videopro_bp_unsubscribe_author_activity_action', esc_html__( 'User unsubscribed!', 'videopro' ), $user_id, $channel_id ),
							'content' => apply_filters('videopro_bp_unsubscribe_author_activity_content', $content, $user_id, $channel_id),
							'component' => 'profile',
							'type' => 'subscribe_author'
							) );
		}
		
		if(function_exists('bp_notifications_add_notification')){
			bp_notifications_add_notification(array(
				'user_id' => $author_id,
				'item_id' => $user_id,
				'secondary_item_id' => '',
				'component_name' => 'videopro',
				'component_action' => 'unsubscribe_author',
				'date_notified' => bp_core_current_time(),
				'is_new' => 1
			));
		}
	}
}

/**
 * BuddyPress notifications
 */
 
/**
 * register a custom component
 */ 
 function videopro_buddypress_add_custom_components( $component_names = array() ) {

	// Force $component_names to be an array
	if ( ! is_array( $component_names ) ) {
		$component_names = array();
	}

	// Add 'custom' component to registered components array
	array_push( $component_names, 'videopro' );

	// Return component's with 'custom' appended
	return $component_names;
}
add_filter( 'bp_notifications_get_registered_components', 'videopro_buddypress_add_custom_components' );

/**
 * format the notification text
 */
function videopro_buddypress_format_notifications( $description, $notification) {
	$action = $notification->component_action;
	$item_id = $notification->item_id;
	$secondary_item_id = $notification->secondary_item_id;
	$post_format = get_post_format($item_id);

	// New custom notifications
	if ( in_array($action, array('upload_video_channel', 'upload_video_playlist', 'upload_new_post', 'unsubscribe_author' , 'subscribe_author', 'subscribe_channel', 'unsubscribe_channel')) ) {
	
		$comment = get_comment( $item_id );
	
		$custom_title = esc_html__('New video uploaded', 'videopro');
        
        $param = '';
        $name = '';
		
		$custom_link = '#';
		
		switch($action){
			case 'upload_video_channel':
				$channel = get_post($secondary_item_id);
            
				if($channel){
					$param = '?channel=' . $channel->post_name;
					$name = $channel->post_title;
				}
				$custom_link = get_permalink($item_id) . $param;
				$custom_text = sprintf(esc_html__('New video uploaded in channel: %s', 'videopro'), $name);
				break;
			case 'upload_new_post':
				$post_author_id = get_post_field( 'post_author', $item_id );
				$author_name = get_the_author_meta('nicename', $post_author_id);
				
				$custom_link = get_permalink($item_id) . $param;
				
				if ($post_format == 'post-format-video' || $post_format == 'video') {
					$custom_text = sprintf(esc_html__('New video uploaded by %s', 'videopro'), $author_name);
				} else {
					$custom_title = esc_html__('New post created', 'videopro');
					$custom_text = sprintf(esc_html__('New post created by %s', 'videopro'), $author_name);
				}
				break;
			case 'upload_video_playlist':
				$param = '?list=' . $secondary_item_id;

				$playlist = get_post($secondary_item_id);
				if($playlist){
					$name = $playlist->post_title;
				}
				
				$custom_link = get_permalink($item_id) . $param;
				$custom_text = sprintf(esc_html__('New video uploaded in playlist: %s', 'videopro'), $name);
				break;
			case 'subscribe_author':
				$custom_link = bp_core_get_userlink($item_id);
				$user_name = get_the_author_meta('display_name', $item_id);
				$custom_text = sprintf(esc_html__('%s has subscribed you', 'videopro'), $user_name);
				$custom_title = $custom_text;
				break;
			case 'unsubscribe_author':
				$custom_link = bp_core_get_userlink($item_id);
				$user_name = get_the_author_meta('display_name', $item_id);
				$custom_text = sprintf(esc_html__('%s has unsubscribed you', 'videopro'), $user_name);
				$custom_title = $custom_text;
				break;
			case 'subscribe_channel':
				$custom_link = bp_core_get_userlink($item_id);
				$user_name = get_the_author_meta('display_name', $item_id);
				$channel = get_post($secondary_item_id);
				$channel_name = $channel ? $channel->post_title : esc_html__('Unknown', 'videopro');
				$custom_text = sprintf(esc_html__('%s has subscribed your channel %s', 'videopro'), $user_name, $channel_name);
				$custom_title = $custom_text;
				break;
			case 'unsubscribe_channel':
				$custom_link = bp_core_get_userlink($item_id);
				$user_name = get_the_author_meta('display_name', $item_id);
				$channel = get_post($secondary_item_id);
				$channel_name = $channel ? $channel->post_title : esc_html__('Unknown', 'videopro');
				$custom_text = sprintf(esc_html__('%s has unsubscribed your channel %s', 'videopro'), $user_name, $channel_name);
				$custom_title = $custom_text;
				break;
		}

		// WordPress Toolbar
		$format = 'string';

		if ( 'string' === $format ) {
			$return = apply_filters( 'videopro_bp_notification_filter', '<a href="' . esc_url( $custom_link ) . '" title="' . esc_attr( $custom_title ) . '">' . esc_html( $custom_text ) . '</a>', $custom_text, $custom_link );

		// Deprecated BuddyBar
		} else {
			$return = apply_filters( 'videopro_bp_notification_filter', array(
				'text' => $custom_text,
				'link' => $custom_link
			), $custom_link, 1, $custom_text, $custom_title );
		}
		
		return $return;
		
	} else {
        return $description;
    }
}
add_filter( 'bp_get_the_notification_description', 'videopro_buddypress_format_notifications', 10, 2 );

/**
 * send notifications to subscribers of channels and authors
 * Note: needs to be improved, as when there are many subscribers, this action is costly
 */
add_action('videopro_after_post_submission', 'videopro_buddypress_add_notification_new_video', 10, 4);
function videopro_buddypress_add_notification_new_video($post_id, $posted_data, $is_uploaded_in_channel, $is_uploaded_in_playlist, $from = 'front')
{
    if (function_exists('bp_is_active') && bp_is_active('notifications')) {
        $channels = array();
        if ($from == 'front') {
            // add notifications to subscribers of channels
            if ($is_uploaded_in_channel) {
                $channel_id = $posted_data['current_channel'];
                $channels[] = $channel_id;
            } elseif ($is_uploaded_in_playlist) {
                // get channels of containing playlist
                $playlist_id = $posted_data['current_playlist'];
                $channels = get_post_meta($playlist_id, 'playlist_channel_id', true);
            } else {
                if (isset($posted_data['channel'])) {
                    $channels = $posted_data['channel'];
                }
            }
        } elseif ($from == 'admin') {
            $format = get_post_format($post_id);
            if ($format == 'post-format-video' || $format == 'video') {
                $channels = get_post_meta($post_id, 'channel_id', true);
            }
        }
        $enable_channel_sub = osp_get('ct_channel_settings', 'channel_subscription');
        $all_channel_subscribers = array();
        if (is_array($channels) && count($channels) > 0 && $enable_channel_sub == '1') {
            foreach ($channels as $channel) {
                $channel_subscribers = videopro_get_channel_subscribers($channel);
                $all_channel_subscribers = array_merge($all_channel_subscribers, $channel_subscribers);
                if ($is_uploaded_in_playlist) {
                    $secondary_item_id = $posted_data['current_playlist'];
                    $component_action = 'upload_video_playlist';
                } else {
                    $secondary_item_id = $channel;
                    $component_action = 'upload_video_channel';
                }
                // send notifications to all subscribers
                if (count($channel_subscribers) > 0) {
                    foreach ($channel_subscribers as $subscriber) {
                        $id = bp_notifications_add_notification(array(
                            'user_id' => $subscriber,
                            'item_id' => $post_id,
                            'secondary_item_id' => $secondary_item_id,
                            'component_name' => 'videopro',
                            'component_action' => $component_action,
                            'date_notified' => bp_core_current_time(),
                            'is_new' => 1
                        ));
                    }
                }
            }
        }

        // get subscribers of authors
        $author_id = get_post_field('post_author', $post_id);
        $author_subscribers = videopro_get_author_subscribers($author_id);
        $author_subscribers = array_diff($author_subscribers, $all_channel_subscribers);
        // send notifications to all subscribers that do not subscribe any uploaded channel listed above
        $enable_author_sub = osp_get('ct_video_settings', 'author_subscription');
        if( wp_is_post_revision( $post_id) || wp_is_post_autosave( $post_id ) ) {

        } else {
            if (count($author_subscribers) > 0 && $enable_author_sub == 'on') {
                foreach ($author_subscribers as $subscriber) {
                    $id = bp_notifications_add_notification(array(
                        'user_id' => $subscriber,
                        'item_id' => $post_id,
                        'secondary_item_id' => '',
                        'component_name' => 'videopro',
                        'component_action' => 'upload_new_post',
                        'date_notified' => bp_core_current_time(),
                        'is_new' => 1
                    ));
                }
            }
        }
    }
}
/**
 * send notifications to subscribers of channels and authors if user upload video from admin
 */
function videopro_buddypress_add_notification_on_all_status_transitions( $new_status, $old_status, $post ) {
    if ( ( $old_status == 'pending' || $old_status == 'draft' || $old_status == 'auto-draft' ) && $new_status == 'publish' ) {
        if ($post->post_type == 'post') {
            add_action( 'save_post', 'videopro_buddypress_add_notification_new_video_from_admin', 5, 3 );
        }
    }
}
add_action(  'transition_post_status',  'videopro_buddypress_add_notification_on_all_status_transitions', 10, 3 );

function videopro_buddypress_add_notification_new_video_from_admin($post_id, $post, $update ) {
    videopro_buddypress_add_notification_new_video($post_id, $post, false, false, 'admin');
}

/**
 * Add <br/> tag to the allowed tags list
 */
add_filter('bp_activity_allowed_tags', 'videopro_bp_activity_allowed_tags');
function videopro_bp_activity_allowed_tags($activity_allowedtags){
    $activity_allowedtags['br'] = array();
    
    return $activity_allowedtags;
}

/**
 * wrap any iframe in the activity content by a DIV
 */
add_filter('bp_get_activity_content_body', 'videopro_bp_get_activity_content_body');
function videopro_bp_get_activity_content_body($content){
    $newoutput = preg_replace("/(<iframe.*?>.*?<\/iframe>)/", "<div class=\"iframe-wrapper\">$1</div>", $content); 
    return $newoutput;
}



// add notifications bubble
add_action('videopro_button_user_submit_video', 'videopro_buddypress_notification_bubble', 9);
function videopro_buddypress_notification_bubble( $context ){
	if($context != 'mobile' && is_user_logged_in() && ot_get_option('bp_notifications', 'off') == 'on' && function_exists('bp_has_notifications')){
		if(bp_has_notifications( array('user_id' => bp_loggedin_user_id()) )){
			$query_loop = buddypress()->notifications->query_loop;
			?>
			<div class="notification-bell">
				<i class="fas fa-bell" aria-hidden="true"></i>
				<div class="number-notification active"><?php echo $query_loop->total_notification_count;?></div>
			</div>
			<?php
		} else {
			?>
			<div class="notification-bell">
				<i class="fas fa-bell" aria-hidden="true"></i>
				<div class="number-notification">0</div>
			</div>
			<?php
		}
	}
}

add_action('wp_head','videopro_buddypress_cover_image_css');
function videopro_buddypress_cover_image_css(){
	$css = bp_add_cover_image_inline_css(true);
	$css = isset($css['css_rules']) ? $css['css_rules'] : '';
	
	echo $css;
}

add_action('videopro_before_end_body', 'videopro_buddpress_notification_board');
function videopro_buddpress_notification_board(){
	if(is_user_logged_in() && ot_get_option('bp_notifications', 'off') == 'on'){
		if(bp_has_notifications( array('user_id' => bp_loggedin_user_id()) )){
			$query_loop = buddypress()->notifications->query_loop;
		?>
		<!-- notification board -->
		<div class="notification-board">
			<div class="notifications">
				<div class="title">
					<span><?php esc_html_e('Your Notifications', 'videopro');?></span>
				</div>
				<div class="content">
					<?php while ( bp_the_notifications() ) : 
						bp_the_notification();
						
						$notification = $query_loop->notification;
						
						$user_id = $notification->user_id;
						$item_id = $notification->item_id;
						$component_name = $notification->component_name;
						$component_action = $notification->component_action;
						

						$user_avatar = bp_core_fetch_avatar(array('item_id' => $user_id, 'object' => 'user'));
						$item_avatar = '';
						if($component_name == 'videopro'){
							$item_avatar = get_the_post_thumbnail( $item_id );
						}
					
					?>
					<div class="notification-content">
						<?php if(!in_array($component_action, array('subscribe_author', 'unsubscribe_author', 'unsubscribe_channel', 'subscribe_channel'))){?>
						<div class="ava">
							<?php 
							$member_profile = bp_core_get_user_domain( $user_id );
							?>
							<a href="<?php echo esc_url($member_profile);?>" title="">
								<?php echo $user_avatar;?>
							</a>
						</div>
						<div class="notification-title">
							<span><?php bp_the_notification_description();  ?></span>
							<span><?php bp_the_notification_time_since();   ?><span>
						</div>
						<div class="video <?php echo $item_avatar != '' ? '' : 'no-ava';?>">
							<span class="video-title"><?php echo get_the_title($item_id);?></span>
							<?php
							if($item_avatar != ''){
								?>
							<div class="video-ava">
								<?php echo $item_avatar; ?>
							</div>
							<?php }?>

							<p class="actions"> <?php bp_the_notification_action_links(); ?> </p>
						</div>
						<?php } else { ?>
							<div class="ava">
								<?php 
								$member_profile = bp_core_get_user_domain( $item_id );
								$user_avatar = bp_core_fetch_avatar(array('item_id' => $item_id, 'object' => 'user'));
								?>
								<a href="<?php echo esc_url($member_profile);?>" title="">
									<?php echo $user_avatar;?>
								</a>
							</div>
							<div class="notification-title">
								<span><?php bp_the_notification_description(); ?></span>
								<span><?php bp_the_notification_time_since(); ?><span>
							</div>
							<div class="video">
								<p class="actions"> <?php videopro_bp_the_notification_action_links( ); ?> </p>
							</div>
						<?php } ?>
					</div>
					<?php endwhile; ?>
				</div>
			</div>
		</div>

		<?php
		} else { ?>
		
		<div class="notification-board" style="margin-right: 511px;">
			<div class="notifications">
				<div class="title">
						 <span><?php esc_html_e('Your Notifications', 'videopro');?></span>
				</div>
				<div class="content empty">
					<i class="fas fa-bell fa-5x" aria-hidden="true"></i>
					<p><?php esc_html_e('Empty Empty World...', 'videopro');?></p>
				</div>
			</div>
		</div>
		
		<?php
		}
	}
}

// temp fix for BuddyPress 3.0.0
function videopro_bp_the_notification_action_links( $args = ''){
	// Set default user ID to use.
	$user_id = isset( $args['user_id'] ) ? $args['user_id'] : bp_displayed_user_id();

	// Parse.
	$r = wp_parse_args( $args, array(
		'before' => '',
		'after'  => '',
		'sep'    => ' | ',
		'links'  => array(
			bp_get_the_notification_mark_link( $user_id ),
			videopro_bp_get_the_notification_delete_link( $user_id )
		)
	) );

	// Build the links.
	$retval = $r['before'] . implode( $r['links'], $r['sep'] ) . $r['after'];
	
	echo apply_filters( 'bp_get_the_notification_action_links', $retval, $r );
}

function videopro_bp_get_the_notification_delete_link( $user_id ){
	// Set default user ID to use.
	$user_id = 0 === $user_id ? bp_displayed_user_id() : $user_id;
	
	$delete_url = '';

	// URL to add nonce to.
	if ( bp_is_current_action( 'unread' ) ) {
		$link = bp_get_notifications_unread_permalink( $user_id );
	} else {
		$link = bp_get_notifications_read_permalink( $user_id );
	}

	// Get the ID.
	$id = bp_get_the_notification_id();

	// Get the args to add to the URL.
	$args = array(
		'action'          => 'delete',
		'notification_id' => $id
	);

	// Add the args.
	$delete_url = add_query_arg( $args, $link );

	// Add the nonce.
	$delete_url = wp_nonce_url( $delete_url, 'bp_notification_delete_' . $id );

	$retval = sprintf( '<a href="%1$s" class="delete secondary confirm bp-tooltip">%2$s</a>', esc_url( $delete_url ), __( 'Delete', 'videopro' ) );
	
	return apply_filters( 'bp_get_the_notification_delete_link', $retval, $user_id );
}

add_action( 'wp_enqueue_scripts', 'videopro_buddypress_scripts' );
function videopro_buddypress_scripts(){
	if(ot_get_option('bp_notifications', 'off') == 'on'){
		$inline_script = '
		if (jQuery(".notification-bell")[0]){
			(function($){
				var notificationBoard = function () {
					$(".notification-bell").toggleClass("arrow");
					$(".notification-board").toggle();
				}
				
				$(".notification-bell").click(function() {
					var left = $(".notification-bell").offset().left;
					var top = $(".notification-bell").offset().top;
					var bellTopOffset =  35;

					if( $(".sticky-menu.active").length > 0){
						left = $(".sticky-menu .notification-bell").offset().left;
						bellTopOffset =  16;
					}
				
					$(".notification-board").css("top", top + bellTopOffset);
					$(".notification-board").css("left", left - 311);
					notificationBoard();
				})
				$(document).click(function(e) {
					if (!$(".notification-bell").is(e.target) && $(".notification-bell").has(e.target).length === 0 && !$(".notification-board").is(e.target) && $(".notification-board").has(e.target).length === 0 && $(".notification-board").css("display") === "block") {
						notificationBoard();
				   }
				   })
				$(window).scroll(function() {
					if($(".notification-board").css("display") === "block") {
						notificationBoard();
					}
				})
				$(window).resize(function() {
					var bellLeft = $(".notification-bell").offset().left;
					var bellTop = $(".notification-bell").offset().top;
					var bellTopOffset =  35;

					if( $(".sticky-menu.active").length > 0){
						bellLeft = $(".sticky-menu .notification-bell").offset().left;
						bellTopOffset =  16;
					}
				
					var boardLeft = $(".notification-board").offset().left;
						$(".notification-board").css("top", bellTop + bellTopOffset);
						$(".notification-board").css("left", bellLeft - 311);
				})
			}(jQuery)) }';
		
		wp_add_inline_script('videopro-theme-js', $inline_script);
	}	
}

add_action('init','videopro_buddypress_delete_notification');

function videopro_buddypress_delete_notification()
{
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['notification_id']) && $_GET['notification_id'] != '') {
        $notification_id = $_GET['notification_id'];
        if (!bp_is_current_action( 'unread' ) && !bp_is_current_action( 'read' )) {
            BP_Notifications_Notification::delete( array( 'id' => $notification_id ) );
        }
    }
}
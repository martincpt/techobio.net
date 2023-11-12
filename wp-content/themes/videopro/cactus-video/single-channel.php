<?php
/**
 * The Template for displaying single channel.
 *
 * @package videopro
 */
get_header();
$sidebar = get_post_meta(get_the_ID(),'channel_sidebar',true);
if(($sidebar == '') && function_exists('osp_get')){
	$sidebar = osp_get('ct_channel_settings','channel_sidebar') ? osp_get('ct_channel_settings','channel_sidebar') : 'right';
}
$videopro_cpage = videopro_global_c_page();
$videopro_layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);


?>
<div id="cactus-body-container">

    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
    
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            <?php if($videopro_layout == 'boxed' && $sidebar == 'both'){?>
                <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
            <?php }?>
            <?php if($sidebar!='full' && $sidebar!='right'){ get_sidebar('left'); } ?>
            <?php if(is_active_sidebar('content-top-sidebar')){
                echo '<div class="content-top-sidebar-wrap">';
                dynamic_sidebar( 'content-top-sidebar' );
                echo '</div>';
            } ?>
            
            <div class="main-content-col single-channel">
                <div class="main-content-col-body">
                
                    <?php if(function_exists('videopro_breadcrumbs')){
						 videopro_breadcrumbs();
					}
                    
                    $videopro_view = isset($_GET['view']) ? $_GET['view'] : '';
                    
                    while ( have_posts() ) : the_post();
						$channel_id = get_the_ID();

						$thumbnail_id = get_post_thumbnail_id( $channel_id );
						$thumb_url = wp_get_attachment_url( $thumbnail_id );
						?>
						<div class="channel-banner">
							<div class="channel-banner-content" style="background-image:url(<?php echo esc_url($thumb_url);?>);">
                                <?php if(videopro_current_user_can('channel.edit', array('id' => get_the_ID()))){ ?>
                                    <a href="#" data-toggle="modal" data-target="#videopro_edit_channel_form" class="btn-edit-channel-thumbnail" data-channel="<?php echo get_the_ID();?>"><?php videopro_edit_button_icon();?></a>
                                <?php }?>
								<div class="thumb-gradient"></div>                                            
								<div class="channel-title dark-div">
									<h1 class="h4"><?php the_title(); ?>
                                    <?php do_action('videopro_after_title', get_the_ID() );?>
                                    </h1>
								</div>
								
								<div class="channel-picture">
                                    <?php
                                    
                                    $channel_avatar = osp_get('ct_channel_settings', 'channel_avatar_layout');
                                    
                                    if($channel_avatar == 1){
                                        $videopro_channel_thumbnail = get_post_meta( get_the_ID(), 'channel_thumb', true );
                                        if($videopro_channel_thumbnail != ''){
                                            $videopro_channel_thumbnail = wp_get_attachment_image( $videopro_channel_thumbnail, array(100, 100) );
                                            echo $videopro_channel_thumbnail;
                                        }
                                    } else {
                                        ?>
                                        <a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>">
                                            <?php echo get_avatar( get_the_author_meta('email'), 110 ); ?>
                                        </a>
                                        <?php
                                    }
                                    
                                    ?>                                	
								</div>
								<?php do_action('cactus-video-subscribe-button', $channel_id);?>
							</div>
							<div class="channel-author-content">
								<div class="group-social-channel">
									<?php videopro_print_channel_social_accounts(); ?>
								</div>
							</div>
						</div>        
						
						<div class="channel-menu">
							<div class="channel-menu-content">
								<div class="channel-menu-item heading-font item-videos font-size-3 <?php if( (!isset($videopro_view) &&  ($videopro_cpage =='')) || ( ($videopro_view == 'videos') &&  ($videopro_cpage =='')) || (($videopro_view == '')&&  ($videopro_cpage ==''))){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'videos'), get_the_permalink() ); ?>" title=""><?php esc_html_e('Videos','videopro');?></a>
								</div>
                              
								<div class="channel-menu-item heading-font item-playlists font-size-3 <?php if(($videopro_view == 'playlists')){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'playlists'), get_the_permalink() ); ?>" title=""><?php esc_html_e('Playlists','videopro');?></a>
								</div>
								<div class="channel-menu-item heading-font item-discussion font-size-3 <?php if(($videopro_view == 'discussion') || ($videopro_cpage != '')){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'discussion'), get_the_permalink() ); ?>" title=""><?php esc_html_e('Discussion','videopro');?></a>
								</div>
								<div class="channel-menu-item heading-font item-about font-size-3 <?php if($videopro_view == 'about'){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'about'), get_the_permalink() ); ?>" title=""><?php esc_html_e('About','videopro');?></a>
								</div>
                                <?php
                                if(($videopro_view == 'videos' || $videopro_view == '') && get_post_field('post_author', get_the_ID()) == get_current_user_id()){
                                    if(videopro_current_user_can('video.upload')){
                                        ?>
                                        <div class="cactus-upload-video">
                                            <a href="#" data-toggle="modal" data-target="#videopro_upload_videos_form" class="btn ct-gradient btn-user-submit btn-default bt-style-1 padding-small white font-size-1" data-type="1">        	
                                                <span><?php echo esc_html__('Upload Video','videopro');?></span>
                                            </a>
                                        </div>
                                        <?php
                                    } else {
                                        do_action('videopro_membership_check_limited_action', get_current_user_id(), 'video.upload');
                                    }
                                }
                                
                                if($videopro_view == 'playlists' && get_post_field('post_author', get_the_ID()) == get_current_user_id()){
                                    if(videopro_current_user_can('playlist.create')){
                                    ?>
                                    <a href="#" data-toggle="modal" data-target="#videopro_user_create_playlist_popup" class="btn-user-create-playlist btn btn-default ct-gradient bt-action metadata-font font-size-1 elms-right"><?php echo esc_html__('Create Playlist','videopro');?></a>
                                    <?php
                                    } else {
                                        do_action('videopro_membership_check_limited_action', get_current_user_id(), 'playlist.create');
                                    }
                                }
                                ?>
							</div> 
						</div>                        	
						<?php 
						if(isset($_GET['view']) && $_GET['view'] == 'playlists'){
							get_template_part( 'cactus-video/ct-channel-playlist' );
						}elseif(isset($_GET['view']) && $_GET['view'] == 'discussion' || ($videopro_cpage !='')){

						}elseif(isset($_GET['view']) && $_GET['view'] =='about'){
							get_template_part( 'cactus-video/ct-channel-about' );
						}else{
							get_template_part( 'cactus-video/ct-channel-video' );
						}
						?>
                        <div <?php if(!isset($_GET['view']) || $_GET['view'] !='discussion'){?> class="discus-none" style="display:none" <?php }?>>
							<?php get_template_part( 'cactus-video/ct-channel-discus' ); ?>
                        </div>
                    <?php endwhile; // end of the loop. ?>
                </div>
            </div>
            <?php 
			$sidebar_style = 'ct-medium';
			videopro_global_sidebar_style($sidebar_style);
			if($sidebar!='full'&& $sidebar!='left'){ get_sidebar(); } ?>
            </div>
          </div>
      </div>
  </div><!--body content-->

<?php get_footer();
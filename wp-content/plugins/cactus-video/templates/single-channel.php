<?php
/**
 * The Template for displaying single channel.
 *
 * @package videopro
 */
get_header();

$sidebar = get_post_meta(get_the_ID(),'channel_sidebar',true);
if(!$sidebar && function_exists('osp_get')){
	$sidebar = osp_get('ct_channel_settings','channel_sidebar');
}
$cpage = videopro_global_c_page();
$layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
?>
<div id="cactus-body-container">

    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
    
        <div class="cactus-container <?php if($layout=='wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            <?php if($layout=='boxed'&& $sidebar=='both'){?>
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
					}?>
                    <?php while ( have_posts() ) : the_post();
						$thumbnail_id = get_post_thumbnail_id( get_the_ID() );
						$thumb_url = wp_get_attachment_url( $thumbnail_id );
						?>
						<div class="channel-banner">
							<div class="channel-banner-content" style="background-image:url(<?php echo esc_url($thumb_url);?>);">
								<div class="thumb-gradient"></div>                                            
								<div class="channel-title dark-div">
									<h1 class="h4">
                                        <?php the_title(); ?>
                                        <?php do_action( 'videopro_after_title', get_the_ID() );?>
                                    </h1>
								</div>
								
								<div class="channel-picture">
                                	<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );?>">
										<?php echo get_avatar( get_the_author_meta('email'), 110 ); ?>
                                    </a>
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
								<div class="channel-menu-item heading-font font-size-3 <?php if( (!isset($_GET['view']) &&  ($cpage =='')) || ( (isset($_GET['view']) && $_GET['view'] =='videos') &&  ($cpage =='')) || ((isset($_GET['view']) && $_GET['view'] =='')&&  ($cpage ==''))){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'videos'), get_the_permalink() ); ?>" title=""><?php esc_html_e('Videos','videopro');?></a>
								</div>
								<div class="channel-menu-item heading-font font-size-3 <?php if((isset($_GET['view']) && $_GET['view'] =='playlists')){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'playlists'), get_the_permalink() ); ?>" title=""><?php esc_html_e('Playlists','videopro');?></a>
								</div>
								<div class="channel-menu-item heading-font font-size-3 <?php if((isset($_GET['view']) && $_GET['view'] =='discussion') || ($cpage !='')){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'discussion'), get_the_permalink() ); ?>" title=""><?php esc_html_e('Discussion','videopro');?></a>
								</div>
								<div class="channel-menu-item heading-font font-size-3 <?php if((isset($_GET['view']) && $_GET['view'] =='about')){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'about'), get_the_permalink() ); ?>" title=""><?php esc_html_e('About','videopro');?></a>
								</div>
							</div> 
						</div>                        	
						<?php 
						if(isset($_GET['view']) && $_GET['view'] =='playlists'){
							get_template_part( 'cactus-video/ct-channel-playlist' );
						}elseif(isset($_GET['view']) && $_GET['view'] =='discussion' || ($cpage !='')){

						}elseif(isset($_GET['view']) && $_GET['view'] =='about'){
							get_template_part( 'cactus-video/ct-channel-about' );
						}else{
							//include( '.php');
							get_template_part( 'cactus-video/ct-channel-video' );
						}
						?>
                        <div <?php if(!isset($_GET['view']) || $_GET['view'] !='discussion'){?> class="discus-none" style="display:none" <?php }?>>
							<?php get_template_part( 'cactus-video/ct-channel-discus' ); ?>
                        </div>
                        <script>
                            var locationHashComment = window.location.hash;
                            var showElementstag = jQuery('.main-content-col.single-channel .combo-change, .main-content-col.single-channel .cactus-sub-wrap, .main-content-col.single-channel .category-tools, .cactus-listing-wrap .style-channel .page-navigation');
                            if(locationHashComment!='' && locationHashComment!=null && typeof(locationHashComment)!='undefined' && locationHashComment.toString().split("-").length == 2){
                                showElementstag.css({'display':'none'});
                                jQuery('.main-content-col.single-channel .discus-none').show();
                                jQuery('.main-content-col.single-channel .channel-menu-item').removeClass('active');
                                jQuery('.main-content-col.single-channel .channel-menu-item').eq(2).addClass('active');
                            };
                        </script>
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

<?php get_footer(); ?>
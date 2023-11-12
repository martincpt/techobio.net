<?php
/**
 * Template Name: Subscribed Authors
 *
 * @package cactus
 */
if( !is_user_logged_in()){
	header('Location: ' . wp_login_url( get_permalink() ));
	exit();
}
get_header();

$sidebar = get_post_meta(get_the_ID(),'page_sidebar',true);
if(!$sidebar){
	$sidebar = ot_get_option('page_sidebar','both');
}
if($sidebar == 'hidden') $sidebar = 'full';
$layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
?>
<!--body content-->
    <div id="cactus-body-container">
    
        <div class="cactus-sidebar-control <?php if($sidebar=='right' || $sidebar=='both'){?>sb-ct-medium <?php }?>  <?php if($sidebar!='full' && $sidebar!='right'){?>sb-ct-small <?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        
            <div class="cactus-container <?php if($layout=='wide'){ echo 'ct-default';}?>">                        	
                <div class="cactus-row">
                    <?php if($layout=='boxed' && ($sidebar=='both')){?>
                        <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                    <?php }?>
                    <?php if($sidebar=='left' || $sidebar=='both'){ get_sidebar('left'); } ?>
                    
                    <div class="main-content-col">
                        <div class="main-content-col-body">
							<?php videopro_breadcrumbs();?>                        
                            <h1 class="single-title entry-title"><?php the_title();?></h1>
                                <?php
                                if(is_active_sidebar('content-top-sidebar')){
                                    echo '<div class="content-top-sidebar-wrap">';
                                    dynamic_sidebar( 'content-top-sidebar' );
                                    echo '</div>';
                                } ?>
        
                                    <?php 
                                    $subscribed_authors = get_user_meta(get_current_user_id(), 'subscribe_authors',true);
                                    
                                    if( !is_array($subscribed_authors) && $subscribed_authors != ''){
                                        $subscribed_authors = explode(" ", $subscribed_authors );
                                    }
                                    
									if(empty($subscribed_authors)){ $subscribed_authors = array(0); }
                                    
                                    // remove current user id in the list, just in case...
                                    if(($key = array_search(get_current_user_id(), $subscribed_authors)) !== false) {
                                        unset($subscribed_authors[$key]);
                                    }
                                                                                                            
                                    $items_per_page = videopro_get_posts_per_page_subscribed_authors();
                                    
                                    $users = get_users(array(
                                                    'include' => $subscribed_authors,
                                                    'paged' => 1,
                                                    'number' => $items_per_page,
                                                    'orderby' => 'meta_value',
                                                    'meta_key' => '_videopro_lasted_update',
                                                    'order' => 'DESC'
                                                ));
                                                
                                    if(count($users) > 0){
                                        ?>
                                        <div class="cactus-listing-wrap subscribe-listing" data-page="1" data-nonce="<?php echo wp_create_nonce('subscribed-authors');?>" data-more="<?php echo count($users) < $items_per_page ? '0' : '1';?>">
                                            <?php	
                                            
                                            $file = locate_template('cactus-video/loop/author-feed.php');
                                                if(!$file)
                                                    $file = ct_video_get_plugin_url() . 'templates/loop/author-feed.php';

                                            foreach($users as $user){
                                                include $file;
                                            }
                                                
                                            ?>
                                        </div>
                                        <div id="ajax-anchor" class=""><img class="ajax-loader" src="<?php echo get_template_directory_uri();?>/images/ajax-loader.gif" alt="Sending ..."></div>
                                        <?php
                                    } else { ?>
                                        <div class="no-post">
                                            <h2 class="h4"><?php echo wp_kses(__('You do not have any subscriptions.<br>Browse Authors to subscribe.','videopro'),array('br'=>array()));?></h2>
                                            <?php
											$query = new WP_Query( array('post_type'  => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'page-templates/authors-listing.php' ) );
											if ( $query->have_posts() ){
												while ( $query->have_posts() ) : $query->the_post();?>
                                                <a href="<?php echo esc_url(get_permalink());?>" class="btn btn-default"><?php esc_html_e('Browse Authors','videopro');?></a>
                                                <?php 
											endwhile; 
                                            wp_reset_postdata();
											}else{
                                                $btn = '<a href="#" class="btn btn-default">' . esc_html__('Browse Authors','videopro') . '</a>';
                                                $btn = apply_filters('videopro-subscribed-authors-browse_authors-button', $btn);
                                                echo $btn;
                                            }?>
                                        </div>
                                    <?php }?>
                        </div>
                    </div>
                    
                    <?php 
					$sidebar_style = 'ct-medium';
					videopro_global_sidebar_style($sidebar_style);
					if($sidebar=='right' || $sidebar=='both'){ get_sidebar(); } ?>
                    
                </div>
            </div>
            
        </div>                
        
        
    </div><!--body content-->

<?php get_footer(); ?>
<?php
/**
 * The template for displaying Archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package cactus
 */

get_header();

$sidebar = ot_get_option('page_sidebar','right');
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
$videopro_layout = videopro_global_layout();
?>

    <div id="cactus-body-container"> <!--Add class cactus-body-container for single page-->
        <div class="cactus-sidebar-control <?php if($sidebar == 'right' || $sidebar == 'both'){?>sb-ct-medium <?php }?>  <?php if($sidebar != 'full' && $sidebar != 'right'){?>sb-ct-small <?php }?>">
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            	<?php if($videopro_layout == 'boxed' && ($videopro_sidebar=='both')){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($sidebar != 'full'){ get_sidebar('left'); } ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
						<?php if(function_exists('videopro_breadcrumbs')){
                             videopro_breadcrumbs();
                        }?>  
                        <?php if(is_active_sidebar('content-top-sidebar')){
							echo '<div class="content-top-sidebar-wrap">';
							dynamic_sidebar( 'content-top-sidebar' );
							echo '</div>';
						} ?>
                        <?php
							$videopro_author = videopro_global_author();
							$userdata               = get_userdata($videopro_author);
                            
							$videopro_author_description     = (is_object($userdata) && get_the_author_meta('description',$userdata->ID)) != '' ? get_the_author_meta('description',$userdata->ID) : '';
						?>
                        <div class="cactus-author-post single-actor">
                          <div class="cactus-author-pic">
                            <div class="img-content">
                            	<?php
								
								echo get_avatar( $userdata->user_email, 400, esc_url(get_template_directory_uri() . '/images/avatar-2x-retina.jpg') );
                                
                                ?>
                            </div>
                          </div>
                          <div class="cactus-author-content">
                            <div class="author-content"> <h3 class="author-name h1"><?php echo esc_html($userdata->display_name);?><?php do_action('videopro_after_title', $userdata->ID, 'author' );?></h3> 

                                <?php 
                                    $target = ot_get_option('social_link_target','off');
                                    $target = $target == 'on' ? '_blank' : '_self';
                                ?>

							  <?php if($videopro_author_description){?>
                                  <span class="author-body"><?php echo wp_kses_post($videopro_author_description);?></span>
                              <?php }?>                                
                              <ul class="social-listing list-inline">
							  
                                  <?php
								  if($email = $userdata->user_email && ot_get_option('author_page_email_contact','on') == 'on'){ 
                                    $email = $userdata->user_email;
                                  ?>
									  <li class="email"><a rel="nofollow" href="mailto:<?php echo esc_attr($email); ?>" target="<?php echo $target;?>" title="<?php esc_html_e('Email', 'videopro');?>"><i class="fas fa-envelope"></i></a></li>
								  <?php }
								  
								  if(ot_get_option('author_page_social_accounts','on') == 'on'){
									  if($facebook = get_the_author_meta('facebook',$userdata->ID)){ ?>
										  <li class="facebook"><a rel="nofollow" href="<?php echo esc_url($facebook); ?>" target="<?php echo $target;?>" title="<?php esc_html_e('Facebook', 'videopro');?>"><i class="fab fa-facebook"></i></a></li>
									  <?php }
									  if($twitter = get_the_author_meta('twitter',$userdata->ID)){ ?>
										  <li class="twitter"><a rel="nofollow" href="<?php echo esc_url($twitter); ?>" target="<?php echo $target;?>" title="<?php esc_html_e('Twitter', 'videopro');?>"><i class="fab fa-twitter"></i></a></li>
									  <?php }
									  if($linkedin = get_the_author_meta('linkedin',$userdata->ID)){ ?>
										  <li class="linkedin"><a rel="nofollow" href="<?php echo esc_url($linkedin); ?>" target="<?php echo $target;?>" title="<?php esc_html_e('Linkedin', 'videopro');?>"><i class="fab fa-linkedin"></i></a></li>
									  <?php }
									  if($tumblr = get_the_author_meta('tumblr',$userdata->ID)){ ?>
										  <li class="tumblr"><a rel="nofollow" href="<?php echo esc_url($tumblr); ?>" target="<?php echo $target;?>" title="<?php esc_html_e('Tumblr', 'videopro');?>"><i class="fab fa-tumblr"></i></a></li>
									  <?php }
									  if($google = get_the_author_meta('google',$userdata->ID)){ ?>
										 <li class="google-plus"> <a rel="nofollow" href="<?php echo esc_url($google); ?>" target="<?php echo $target;?>" title="<?php esc_html_e('Google Plus', 'videopro');?>"><i class="fab fa-google-plus"></i></a></li>
									  <?php }
									  if($pinterest = get_the_author_meta('pinterest',$userdata->ID)){ ?>
										 <li class="pinterest"> <a rel="nofollow" href="<?php echo esc_url($pinterest); ?>" target="<?php echo $target;?>" title="<?php esc_html_e('Pinterest', 'videopro');?>"><i class="fab fa-pinterest"></i></a></li>
									  <?php }
									  
									  if($custom_acc = get_the_author_meta('cactus_account',$userdata->ID)){
										  foreach($custom_acc as $acc){
											  if($acc['icon'] || $acc['url']){
										  ?>
										  <li class="cactus_account custom-account-<?php echo sanitize_title(@$acc['title']);?>"><a rel="nofollow" href="<?php echo esc_attr(@$acc['url']); ?>" target="<?php echo $target;?>" title="<?php echo esc_attr(@$acc['title']);?>"><i class="fab <?php echo esc_attr(@$acc['icon']);?>"></i></a></li>
									  <?php 	}
										  }
									  }
								  }
								  ?>
                              </ul>
                                <?php 
                                do_action('videopro-author-page-after-content', $videopro_author);
                                ?>
                            </div>
                          </div>
                        </div>
                        
                        <?php 

                        $has_channels_tab = $has_channels_tab = true;
                        
                        // current view
                        if(isset($_GET['view']) && in_array($_GET['view'], array('videos','channels'))){
                            $current_view = $_GET['view'];
                        } else {
                            $current_view = 'videos';
                        }
                        
                        $videopro_author = videopro_global_author();
                        $page_url = get_author_posts_url($videopro_author);
                        $query_string = $_SERVER['QUERY_STRING'];
                        $page_url = $page_url . '?' . remove_query_arg(array('paged', 'view'), $query_string);
                        
                        if(!$has_channels_tab){
                        
                        ?>
                        <div class="single-divider"></div>
                        <?php }?>
                        <div class="channel-menu">
							<div class="channel-menu-content">
								<div class="channel-menu-item heading-font item-videos font-size-3 <?php if($current_view == 'videos'){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'videos'), $page_url); ?>" title=""><?php esc_html_e('Videos','videopro');?></a>
								</div>
                                
                                <?php if($has_channels_tab){?>
								<div class="channel-menu-item heading-font item-channels font-size-3 <?php if($current_view == 'channels'){?> active <?php }?>">
									<a href="<?php echo add_query_arg( array('view' => 'channels'), $page_url ); ?>" title=""><?php esc_html_e('Channels','videopro');?></a>
								</div>
                                <?php }?>
                                
                                <?php
                                if($current_view == 'channels' && $videopro_author == get_current_user_id()){
                                    if( videopro_current_user_can('channel.create') ){
                                    ?>
                                <a href="#" data-toggle="modal" data-target="#videopro_user_create_channel_popup" class="btn-user-create-channel btn btn-default ct-gradient bt-action metadata-font font-size-1 elms-right"><?php echo esc_html__('Create Channel','videopro');?></a>
                                    <?php
                                    } else {
                                        do_action('videopro_membership_check_limited_action', get_current_user_id(), 'channel.create');
                                    }
                                }
                                ?> 
							</div> 
						</div>
                        
                        <div class="cactus-listing-wrap">
                            <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                <?php
                                    if($current_view == 'channels'){
                                        get_template_part('html/single/author-channels');
                                    } else { ?>
                                <div class="cactus-sub-wrap">
                                    <?php if ( have_posts() ) : ?>
										<?php while ( have_posts() ) : the_post(); ?>
                                        <!--item listing-->                                                
                                            <?php get_template_part( 'html/loop/content', get_post_format() ); ?>
                                        <?php endwhile; ?>
                                    <?php else : ?>
                                    <section class="no-results not-found">
                                        <div class="page-content">
                                            <p>
                                        <?php 
                                        esc_html_e('This author hasn\'t got any videos yet','videopro');
                                        ?>
                                            </p>
                                        </div>
                                    </section>
                                    <?php endif; ?>
                                    <!--item listing-->
                                </div>
                                
                                <?php videopro_paging_nav('.cactus-listing-config .cactus-sub-wrap','html/loop/content'); ?>
                                
                                <?php }?>
                                <?php if(is_active_sidebar('content-bottom-sidebar')){
									echo '<div class="content-bottom-sidebar-wrap">';
									dynamic_sidebar( 'content-bottom-sidebar' );
									echo '</div>';
								} ?>
                            </div>
                        </div>
                        
                    </div>
                </div>
				<?php 
                $sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($sidebar_style);
                if($sidebar!='full'){ get_sidebar(); } ?>
        
            </div>
        </div>
        
    </div>                
    
    
</div>
<?php get_footer();
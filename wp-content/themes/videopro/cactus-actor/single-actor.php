<?php

get_header();
$videopro_sidebar = ot_get_option('post_sidebar','right');

$videopro_sidebar = apply_filters('videopro_single_actor_sidebar', $videopro_sidebar);

$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
$videopro_layout = videopro_global_layout();

?>

    <div id="cactus-body-container"> <!--Add class cactus-body-container for single page-->
        <div class="cactus-sidebar-control <?php if($videopro_sidebar == 'right' || $videopro_sidebar == 'both'){?>sb-ct-medium <?php }?>  <?php if($videopro_sidebar != 'full' && $videopro_sidebar != 'right'){?>sb-ct-small <?php }?>">
        <div class="cactus-container <?php if($videopro_layout== 'wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
            	<?php if($videopro_layout == 'boxed'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }?>
                <?php if($videopro_sidebar != 'full'){ get_sidebar('left'); } ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
						<?php if(function_exists('videopro_breadcrumbs')){
                             videopro_breadcrumbs();
                        }
						
						if(is_active_sidebar('content-top-sidebar')){
							echo '<div class="content-top-sidebar-wrap">';
							dynamic_sidebar( 'content-top-sidebar' );
							echo '</div>';
						} 
						
						if ( have_posts() ) : 
							while ( have_posts() ) : the_post(); ?>
                        <div class="cactus-author-post single-actor">
                        	<?php if(has_post_thumbnail()){ ?>
                            <div class="cactus-author-pic">
                              <div class="img-content">
                                  <?php echo videopro_thumbnail( array(298,298)); ?> 
                              </div>
                            </div>
                            <?php } ?>
                          <div class="cactus-author-content">
                            <div class="author-content"> <h3 class="author-name h1"><?php the_title();?><?php do_action('videopro_after_title', get_the_ID());?></h3>
                              <?php if(get_post_meta(get_the_ID(), 'actor-pos', true)!=''){?>
                              	<span class="author-position"><?php echo esc_html(get_post_meta(get_the_ID(), 'actor-pos', true));?></span> 
                              <?php }?>
                              <div class="author-body">
								<?php the_content();?>
								<?php
								  if(osp_get('ct_actor_settings','actor-birthday') == 1){
								  $day = get_post_meta(get_the_ID(), 'actor_bt_day', true);
								  $month = get_post_meta(get_the_ID(), 'actor_bt_month', true);
								  $year = get_post_meta(get_the_ID(), 'actor_bt_year', true);
								  if($year != '') {?>
								  <div class="actor-birthday">
									  <span><?php echo ($day != '' && $month != '') ? esc_html__('Born on:','videopro') : esc_html__('Born in:','videopro');?></span>
									  <?php
									  
									  if($day != '' && $month != '') {
										$date = date_create_from_format('Y-m-d',$year . '-' . $month . '-' . $day);
										echo $date->format(get_option('date_format'));
									  } else {
										  echo esc_html($year);
									  }
									  ?>
								  </div>
							      <?php }
								  }?>
							  </div>
							  
							      
                              <ul class="social-listing list-inline">
							  
                                  <?php

                                  $target = ot_get_option('social_link_target','off');
                                  $target = $target == 'on' ? '_blank' : '_self';
								  
                                  if($website = get_post_meta(get_the_ID(),'website',true)){ ?>
									  <li class="website"><a target="<?php echo $target;?>" rel="nofollow" href="<?php echo esc_url($website); ?>" title="<?php esc_html_e('Website', 'videopro');?>"><i class="fas fa-globe"></i></a></li>
								  <?php }
                                  
                                  if($imdb = get_post_meta(get_the_ID(),'imdb',true)){ ?>
									  <li class="imdb"><a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($imdb); ?>" title="<?php esc_html_e('IMDB', 'videopro');?>"><i class="fab fa-imdb"></i></a></li>
								  <?php }
                                  
								  if($facebook = get_post_meta(get_the_ID(),'facebook',true)){ ?>
									  <li class="facebook"><a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($facebook); ?>" title="<?php esc_html_e('Facebook', 'videopro');?>"><i class="fab fa-facebook"></i></a></li>
								  <?php }
								  if($twitter = get_post_meta(get_the_ID(),'twitter',true)){ ?>
									  <li class="twitter"><a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($twitter); ?>" title="<?php esc_html_e('Twitter', 'videopro');?>"><i class="fab fa-twitter"></i></a></li>
								  <?php }
								  if($linkedin = get_post_meta(get_the_ID(),'linkedin',true)){ ?>
									  <li class="linkedin"><a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($linkedin); ?>" title="<?php esc_html_e('Linkedin', 'videopro');?>"><i class="fab fa-linkedin"></i></a></li>
								  <?php }
								  if($tumblr = get_post_meta(get_the_ID(),'tumblr',true)){ ?>
									  <li class="tumblr"><a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($tumblr); ?>" title="<?php esc_html_e('Tumblr', 'videopro');?>"><i class="fab fa-tumblr"></i></a></li>
								  <?php }
								  if($google = get_post_meta(get_the_ID(),'google-plus',true)){ ?>
									 <li class="google-plus"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($google); ?>" title="<?php esc_html_e('Google Plus', 'videopro');?>"><i class="fab fa-google-plus"></i></a></li>
								  <?php }
								  if($pinterest = get_post_meta(get_the_ID(),'pinterest',true)){ ?>
									 <li class="pinterest"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($pinterest); ?>" title="<?php esc_html_e('Pinterest', 'videopro');?>"><i class="fab fa-pinterest"></i></a></li>
								  <?php }
								  if($instagram = get_post_meta(get_the_ID(),'instagram',true)){ ?>
									 <li class="instagram"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($instagram); ?>" title="<?php esc_html_e('Instagram', 'videopro');?>"><i class="fab fa-instagram"></i></a></li>
								  <?php }
								  if($flickr = get_post_meta(get_the_ID(),'flickr',true)){ ?>
									 <li class="flickr"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($flickr); ?>" title="<?php esc_html_e('Flickr', 'videopro');?>"><i class="fab fa-flickr"></i></a></li>
								  <?php }
								  if($youtube = get_post_meta(get_the_ID(),'youtube',true)){ ?>
									 <li class="youtube"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($youtube); ?>" title="<?php esc_html_e('Youtube', 'videopro');?>"><i class="fab fa-youtube"></i></a></li>
								  <?php }
                                  
                                  if($email = get_post_meta(get_the_ID(),'envelope',true)){ ?>
									  <li class="email"><a target="<?php echo $target;?>"  rel="nofollow" href="mailto:<?php echo esc_attr($email); ?>" title="<?php esc_html_e('Email', 'videopro');?>"><i class="far fa-envelope"></i></a></li>
								  <?php }
                                  
								  if($github = get_post_meta(get_the_ID(),'github',true)){ ?>
									 <li class="github"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($github); ?>" title="<?php esc_html_e('Github', 'videopro');?>"><i class="fab fa-github"></i></a></li>
								  <?php }
								   if($dribbble = get_post_meta(get_the_ID(),'dribbble',true)){ ?>
									 <li class="dribbble"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($dribbble); ?>" title="<?php esc_html_e('Dribbble', 'videopro');?>"><i class="fab fa-dribbble"></i></a></li>
								  <?php }
								  if($vk = get_post_meta(get_the_ID(),'vk',true)){ ?>
									 <li class="vk"> <a target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($vk); ?>" title="<?php esc_html_e('vk', 'videopro');?>"><i class="fab fa-vk"></i></a></li>
								  <?php }
                                  
                                  $custom_accounts = get_post_meta(get_the_ID(), 'actor-custom_account');
                                  foreach($custom_accounts as $custom_account){
                                      ?>
                                      <li class="<?php echo esc_attr(sanitize_title($custom_account['actor-custom_account-name']));?>"> <a  target="<?php echo $target;?>"  rel="nofollow" href="<?php echo esc_url($custom_account['actor-custom_account-link']);?>" title="<?php echo esc_attr($custom_account['actor-custom_account-name']);?>"><i class="<?php echo $custom_account['actor-custom_account-css_class'];?>"></i></a></li>
                                      <?php
                                  }
								  ?>
                              </ul>
                            </div>
                          </div>
                        </div>
                        
                        <div class="single-divider"></div>
                        <?php 
						$paged = get_query_var('paged') ? get_query_var('paged') : (get_query_var('page') ? get_query_var('page'):1);
						$args = array(
							'post_type' => 'post',
							'paged' => $paged,
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
                            'posts_per_page' => apply_filters('single_actor_videos_count', get_option('posts_per_page')),
							'orderby' => 'date',
							'order' => apply_filters('videopro_actor_videos_listing_order', 'DESC'),
							'meta_query' => videopro_get_meta_query_args('actor_id', get_the_ID())
						);

						$the_query = new WP_Query( $args );
						$it = $the_query->post_count;
                        
						if($the_query->have_posts()){
							$i = 0;
							?>                  
                            <h1 class="h4 category-title entry-title single-actor"><?php esc_html_e('Videos','videopro');?></h1>
                            <div class="cactus-listing-wrap single-actor">
                                <div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    <div class="cactus-sub-wrap">
                                        <?php
                                        while($the_query->have_posts()){ 
                                            $the_query->the_post();
                                            get_template_part( 'cactus-video/content-video' );
                                        }
                                        wp_reset_postdata();
                                        ?>                                                
                                    </div>
                                    
                                    <?php
                                    
                                    if(is_active_sidebar('content-bottom-sidebar')){
                                        echo '<div class="content-bottom-sidebar-wrap">';
                                            dynamic_sidebar( 'content-bottom-sidebar' );
                                        echo '</div>';
                                    } ?>
                                </div>
                            </div>
                            <?php videopro_paging_nav('.cactus-listing-wrap.single-actor .cactus-sub-wrap','cactus-video/content-video', false, $the_query); ?>
						<?php 
                        
                        
                        } else {
							?>
                            <p><?php esc_html_e('No videos found','videopro');?></p>
                            <?php
						}
						
                        ?>
                        
                        <?php endwhile;
						
						else : 
							get_template_part( 'html/loop/content', 'none' );							
						endif; ?>
                        
                        <?php
                        if(ot_get_option('show_comment', 'on') != 'off'){
                            
                            if ( comments_open() ){
                                comments_template();
                            }
						}?>
                    </div>
                </div>
				<?php 
                
				$sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($sidebar_style);
                if($videopro_sidebar != 'full'){ get_sidebar(); } 
				
				?>
        
            </div>
        </div>
        
    </div>                
    
    
</div>
<?php get_footer(); ?>
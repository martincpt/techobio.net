<h4 class="single-post-heading"><?php 
	$heading = osp_get('ct_channel_settings', 'containing_channels_heading'); 
	echo $heading;
	?></h4>
		<div class="post-list-in-single">
			<div class="cactus-listing-wrap">
				<div class="cactus-listing-config style-2"> <!--addClass: style-1 + (style-2 -> style-n)-->
					<div class="cactus-sub-wrap">
					
						<?php
						if($the_query->have_posts()){
							foreach ( $the_query->posts as $post) :
								$post_data = videopro_get_post_viewlikeduration($post->ID);
								extract($post_data);
							?>
							<!--item listing-->                                                
							<article class="cactus-post-item hentry">
								<div class="entry-content">                                        
									<?php if(has_post_thumbnail($post->ID)){?>
									<!--picture (remove)-->
									<div class="picture">
										<a href="<?php echo esc_url(get_the_permalink($post->ID));?>" title="<?php echo esc_attr(get_the_title($post->ID));?>">
											<?php echo videopro_thumbnail( array(360,202), $post->ID); ?>   
											<div class="ct-icon-video"></div>
										</a>
									</div><!--picture-->
									<?php }?>
									<div class="content">                                       
										<!--Title (no title remove)-->
										<h3 class="cactus-post-title entry-title h4"> 
											<a href="<?php echo get_the_permalink($post->ID);?>" title="<?php the_title_attribute(array('post' => $post->ID));?>"><?php echo get_the_title($post->ID); ?></a>
										</h3><!--Title-->
																											
										<div class="posted-on metadata-font">
											<a href="<?php echo get_author_posts_url( get_the_author_meta('ID', $post->post_author) ); ?>" class="vcard author cactus-info font-size-1">
												<span class="fn"><?php echo get_userdata($post->post_author)->display_name ;?></span>
										    </a>
											<div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime($post->ID);?></div>
										</div>                                                                        
										
										<div class="channel-button">
											<?php do_action('cactus-video-subscribe-button', $post->ID);?>
										</div>
									</div>
									
								</div>
								
							</article><!--item listing-->
							<?php
							endforeach;
						}
						?>
					</div>
					
				</div>
			</div>
		</div>
<div class="single-divider"></div>
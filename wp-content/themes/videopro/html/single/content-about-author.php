    <!--author-->
    <h4 class="single-post-heading"><?php echo esc_html__('About The Author', 'videopro');?></h4>                                            
    <div class="cactus-author-post">
      <div class="cactus-author-pic">
        <div class="img-content">
        	<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" class="avatar">
				<?php echo get_avatar( get_the_author_meta('email'), 110 ); ?>
            </a>
        </div>
      </div>
      <div class="cactus-author-content">
        <div class="author-content"> <span class="author-name"><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta( 'display_name' ); ?></a></span> <span class="author-body"><?php the_author_meta('description'); ?></span>
          <ul class="social-listing list-inline">                	                 
					<?php if(get_the_author_meta( 'facebook')!=''){ ?>
                    <li class="facebook">                                                
                        <a title="Facebook" href="<?php echo get_the_author_meta( 'facebook' ); ?>" target="_blank" rel="nofollow"><i class="fab fa-facebook"></i></a>
                    </li>
                    <?php }?>
                    <?php if(get_the_author_meta( 'twitter' )!=''){ ?>
                    <li class="twitter">                                                
                        <a href="<?php echo get_the_author_meta( 'twitter'); ?>" title="Twitter" rel="nofollow" target="_blank" ><i class="fas fa-twitter"></i></a>
                    </li>
                    <?php }?>
                    <?php if(get_the_author_meta( 'linkedin' )!=''){ ?>
                    <li class="linkedin">                                                
                        <a href="<?php echo get_the_author_meta( 'linkedin'); ?>" title="LinkedIn" rel="nofollow" target="_blank"><i class="fab fa-linkedin"></i></a>
                    </li>
                    <?php }?>
                    <?php if(get_the_author_meta( 'tumblr' )!=''){ ?>
                    <li class="tumblr">                                                
                        <a href="<?php echo get_the_author_meta( 'tumblr'); ?>" title="Tumblr" rel="nofollow" target="_blank"><i class="fab fa-tumblr"></i></a>
                    </li>
                    <?php }?>
                    <?php if(get_the_author_meta( 'google' )!=''){ ?>
                    <li class="google-plus">                                                
                        <a href="<?php echo get_the_author_meta( 'google' ); ?>" title="Google Plus" rel="nofollow" target="_blank"><i class="fab fa-google-plus"></i></a>
                    </li>
                    <?php }?>
                    <?php if(get_the_author_meta( 'pinterest' )!=''){ ?>
                    <li class="pinterest">                                                
                        <a href="<?php echo get_the_author_meta( 'pinterest' ); ?>" title="Pinterest" rel="nofollow" target="_blank"><i class="fab fa-pinterest"></i></a>
                    </li>
                    <?php }?>
                    <?php if(get_the_author_meta( 'email' )!=''){ ?>
                    <li class="email">                                                
                        <a href="mailto:<?php echo get_the_author_meta( 'email' ); ?>" title="Email"><i class="fas fa-envelope"></i></a>
                    </li>
                    <?php }
					if($custom_acc = get_the_author_meta('cactus_account')){
						foreach($custom_acc as $acc){
							if($acc['icon'] || $acc['url']){
						?>
						<li class="cactus_account custom-account-<?php echo sanitize_title(@$acc['title']);?>"><a rel="nofollow" href="<?php echo esc_attr(@$acc['url']); ?>" title="<?php echo esc_attr(@$acc['title']);?>"><i class="fab <?php echo esc_attr(@$acc['icon']);?>"></i></a></li>
					<?php 	}
						}
					}?>
                </ul>
        </div>
      </div>
    </div>											
    <div class="single-divider"></div>
    <!--author-->
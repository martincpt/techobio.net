<?php
	$channel_ID =  get_the_ID();
	$has_no_picture_class = !has_post_thumbnail() ? ' no-picture' : '';
	?>
	<!--item listing-->
	<div class="cactus-post-item">

	    <!--content-->
	    <div class="entry-content">
	        <div class="primary-post-content<?php echo esc_attr($has_no_picture_class);?>"> <!--addClass: related-post, no-picture -->

				<?php if(has_post_thumbnail()): ?>
		            <!--picture-->
		            <div class="picture">
		                <div class="picture-content">
		                    <a href="<?php esc_url(the_permalink());?>" class="post-link" title="<?php esc_attr(the_title_attribute());?>">
		                        <?php echo videopro_thumbnail('thumb_390x215');?>
		                        <div class="thumb-overlay"></div>
		                    </a>
		                </div>

		            </div>
		            <!--picture-->
	        	<?php endif;?>

	            <div class="content">

	                <!--Title-->
	                <h3 class="h2 cactus-post-title entry-title">
	                    <a href="<?php esc_url(the_permalink());?>" title="<?php esc_attr(the_title_attribute());?>"><?php the_title();?></a>
	                </h3><!--Title-->
	                <!--info-->
	                <?php
		                $args = array(
		                	'post_type' => 'post',
		                	'post_status' => 'publish',
		                	'ignore_sticky_posts' => 1,
		                	'posts_per_page' => -1,
		                	'orderby' => 'latest',
		                	'meta_query' => videopro_get_meta_query_args('channel_id', get_the_ID())
		                );
		                $video_query = new WP_Query( $args );
		                $n_video = $video_query->post_count;

		                $view_channel = (int)get_post_meta( get_the_ID(), 'view_channel', true );
		                $args_pl = array(
		                	'post_type' => 'ct_playlist',
		                	'post_status' => 'publish',
		                	'ignore_sticky_posts' => 1,
		                	'posts_per_page' => -1,
		                	'orderby' => 'modified',
		                	'meta_query' => videopro_get_meta_query_args('playlist_channel_id', get_the_ID())
		                );
		                $playlist_query = new WP_Query( $args_pl );
		                if($playlist_query->have_posts()){
		                	while($playlist_query->have_posts()){$playlist_query->the_post();
		                		$view_playlist = (int)get_post_meta( get_the_ID(), 'view_playlist', true );
		                		$view_channel = $view_channel + $view_playlist;
		                	}
		                }
		                 wp_reset_postdata();
	                ?>
	                <div class="posted-on">
	                    <div class="videos cactus-info"><?php echo esc_html($n_video); esc_html_e(' videos', 'videopro');?></div>
	                    <div class="view cactus-info"><?php echo esc_html($view_channel); esc_html_e(' views', 'videopro');?></div>
	                </div><!--info-->

	                <div class="subs-button">
	                    <?php videopro_subcribe_button($channel_ID); ?>
	                </div>

	                <div class="cactus-last-child"></div> <!--fix pixel no remove-->
	            </div>
	        </div>

	    </div><!--content-->

	</div>
	<!--item listing-->
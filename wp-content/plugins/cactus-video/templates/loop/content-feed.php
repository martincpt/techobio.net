<div class="cactus-listing-config style-3 style-widget-popular-post subscribe-header">
<div class="cactus-sub-wrap">
  <article class="cactus-post-item">
    <div class="entry-content">
    	<?php  if(has_post_thumbnail()){ ?>
        <div class="picture">
          <div class="picture-content">
              <a href="<?php the_permalink();?>" class="post-link" title="<?php the_title_attribute();?>">
              	<?php echo videopro_thumbnail(array(298,298));?>
              </a>
          </div>
        </div>
        <?php };?>
        <div class="content">
          	<h3 class="cactus-post-title entry-title h6 sub-lineheight"> <a href="<?php the_permalink();?>" title="<?php the_title_attribute();?>"><?php the_title(); ?></a> </h3>
			<?php do_action('cactus-video-subscribe-button', $channel_ID);?>
        </div>
    </div>
  </article>
</div>
</div>

<div class="cactus-listing-config style-2 style-channel-listing"> <!--addClass: style-1 + (style-2 -> style-n)-->

    <div class="cactus-sub-wrap">                                            
      <?php 
	  $posts_per_channel = 3;
	  $layout = videopro_global_layout();
	  if($layout == 'fullwidth'){ $posts_per_channel = 4;}
      
      $posts_per_page = apply_filters('videopro-subscribed-channels-posts_per_channel', $posts_per_channel, $layout);
      
	  $args = array(
		  'post_type' => 'post',
		  'post_status' => 'publish',
		  'ignore_sticky_posts' => 1,
		  'posts_per_page' => $posts_per_page,
		  'orderby' => 'latest',
		  'meta_query' => videopro_get_meta_query_args('channel_id', get_the_ID())
          
	  );
	  
      
	  $list_query = new WP_Query( $args );
	  $it = $list_query->post_count;
	  if($list_query->have_posts()){
	  while($list_query->have_posts()){$list_query->the_post();
		get_template_part( 'html/loop/content', get_post_format()  );
	  }
	  wp_reset_postdata();
    }?>                                           
    </div>
</div>
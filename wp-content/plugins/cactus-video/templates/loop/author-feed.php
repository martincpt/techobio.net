<div class="cactus-listing-config style-3 style-widget-popular-post subscribe-header">
<div class="cactus-sub-wrap">
  <article class="cactus-post-item">
    <div class="entry-content">
        <div class="picture">
          <div class="picture-content">
              <a href="<?php echo get_author_posts_url($user->ID);?>" class="post-link" title="<?php echo $user->display_name;?>">
              	<?php echo get_avatar($user->ID);?>
              </a>
          </div>
        </div>
        <div class="content">
          	<h3 class="cactus-post-title entry-title h6 sub-lineheight"> <a href="<?php echo get_author_posts_url($user->ID);?>" title="<?php echo $user->display_name;?>"><?php echo $user->display_name; ?></a> </h3>
			<?php videopro_author_subcribe_button($user->ID);?>
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
      
        $posts_per_page = apply_filters('videopro-subscribed-authors-posts_per_channel', $posts_per_channel, $layout);
      
        $args = array(
		  'post_type' => 'post',
		  'post_status' => 'publish',
		  'ignore_sticky_posts' => 1,
		  'posts_per_page' => $posts_per_page,
		  'orderby' => 'latest',
		  'author' => $user->ID
		  );
      
        $list_query = new WP_Query( $args );
	  
        if($list_query->have_posts()){
          while($list_query->have_posts()){
                $list_query->the_post();
                get_template_part( 'html/loop/content', get_post_format()  );
          }
        }
        
        wp_reset_postdata();
        
        ?>
    </div>
</div>
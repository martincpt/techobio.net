<article class="cactus-post-item">

    <div class="entry-content">                                        
        <?php 
		$videopro_id_cr_pos = videopro_global_id_cr_pos();
        
		if($videopro_id_cr_pos == ''){
			if(isset($_POST['id_playlist'])){
				$videopro_id_cr_pos = intval($_POST['id_playlist']);
			} else{ $videopro_id_cr_pos = ''; }
		}
        

		$id = get_the_ID();
		$post_data = videopro_get_post_viewlikeduration($id);
		
		// add specific data to determine where this post is
		$post_data['playlist'] = $videopro_id_cr_pos;

		extract($post_data);
		?>
        <?php if(has_post_thumbnail()): ?>
        <!--picture (remove)-->
        <div class="picture">
			<?php 
                videopro_loop_item_thumbnail($id, get_post_format(), array(360,202), $post_data);
            ?>
        </div><!--picture-->
        <?php endif ?>
        <div class="content">
                                                                        
            <!--Title (no title remove)-->
            <h3 class="cactus-post-title entry-title h4"> 
                <a href="<?php echo add_query_arg( array('list' => $videopro_id_cr_pos), get_the_permalink() );?>" title="<?php echo esc_attr(get_the_title(get_the_ID()));?>"><?php echo esc_attr(get_the_title(get_the_ID()));?></a>
            </h3><!--Title-->
            
            <div class="posted-on metadata-font">
                <?php if(ot_get_option('enable_archive_date', 'on') != 'off') { ?>
                    <div class="date-time cactus-info font-size-1"><?php echo videopro_get_datetime();?></div>
                <?php };?>
                 <?php if(function_exists( 'get_tptn_post_count_only' )){ ?>
                 <div class="view cactus-info font-size-1"><?php echo  videopro_get_formatted_string_number($viewed).esc_html__(' Views','videopro'); ?></div>
                 <?php }?>
            </div>
            
        </div>
        
    </div>
    
</article><!--item listing-->
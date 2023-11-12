<?php
/**
 * @package cactus
 */
global $thumb_url;
global $post_standard_layout;
global $post_gallery_layout;
$tags = ot_get_option('show_tags_single_post');
	global $post;
	$checkSmartListPost = 0;
	if(strpos($post->post_content, '<!--nextpage-->')!=false &&( !isset($_GET['view-all']) || $_GET['view-all']!=1 )){
		$checkSmartListPost = 1;
	};
	$paged = get_query_var('page')?get_query_var('page'):1;
	?>
    <div class="heading-post">                                            
        <!--info-->
        <div class="posted-on">
            <?php show_cat() ?>
            <div class="fix-responsive"></div>
            <?php echo videopro_get_datetime();?>
            <span class="vcard author"> 
                <span class="fn"><?php the_author_posts_link(); ?></span>
            </span>
            <a href="<?php comments_link(); ?>" class="comment cactus-info"><?php echo number_format_i18n(get_comments_number(get_the_ID()));?></a>                                               
        </div><!--info-->
        
        <!--Title-->
        <?php the_title( '<h1 class="title">', videopro_post_rating(get_the_ID(), true) . '</h1>' ); ?>
        <!--Title-->
        
    </div>
    <?php
	$csscl ='';
	$more = 1;
	cactus_toolbar($id_curr = get_the_ID(),1, $more, $csscl);?>    
	<div class="body-content " <?php if((isset($_GET['view-all'])) && ($_GET['view-all']==1)) {?>data-scroll-page-viewall="1"<?php }?>>
    	
		<?php 
		if( $checkSmartListPost == 1){
			preg_match('/\<h2(.*)\<\/h2\>/isU',get_the_content(), $h2_tag);
			?>
        	<div class="smart-list-post-wrap">
            	
                
                <h2 class="h1 title-page-post"><span class="post-static-page" <?php if($paged>1){?> data-scroll-page="1"<?php } ?>><?php echo $paged;?></span><span><?php if(isset($h2_tag[0])){echo strip_tags($h2_tag[0]);}?></span></h2>
                
                <?php
				wp_link_pages( 
					array(
						'before' => '<div class="page-links">',
						'after'  => '</div>',
						'nextpagelink'     => __( '<i class="fas fa-angle-right"></i>' ),
						'previouspagelink' => __( '<i class="fas fa-angle-left"></i>' ),
						'next_or_number'   => 'next',
					) 
				);
				?>
            </div>
        <?php    
		};
		if(isset($_GET['view-all']) && $_GET['view-all'] == 1){
			$ct =  $post->post_content = str_replace( "<!--nextpage-->", "<br/>", $post->post_content );
			echo apply_filters('the_content',$ct);
		}else{
			if(isset($h2_tag[0])){
				$content =  preg_replace ('/\<h2(.*)\<\/h2\>/isU', ' ', get_the_content());
				echo apply_filters('the_content',$content);
			}else{
			the_content();} ?>
		<?php
			
			if(strpos($post->post_content, '<!--nextpage-->')!=false){?>
            	<div class="viewallpost-wrap">
                    <div class="cactus-view-all-pages">
                        <span><span></span></span>
                        <a href="<?php echo add_query_arg( array('view-all' => 1), ct_get_curent_url() ); ?>" title=""><span><?php echo __( 'View as one page', 'videopro' )?></span></a>
                        <span><span></span></span>
                    </div>
                </div>
                <?php
			}
		}
		?>
	</div><!-- .entry-content -->
    
    <?php
	$tag_list = get_the_tag_list(' ', ' ');
	if($tags!='off' && str_replace(' ', '', $tag_list) !=''){?>
    <div class="tag-group">
        <span><?php _e('tags:','videopro') ?></span>
        <?php echo $tag_list; ?>
    </div>
    <?php }
		cactus_toolbar($id_curr = get_the_ID(), 1, $show_more=0, $css_class='fix-bottom');
	?> 
    <!--navigation post-->
    <div class="cactus-navigation-post">
    	<?php 
		$next_previous_same = ot_get_option('next_previous_same');
		if($next_previous_same!='all'){
			$p = get_adjacent_post(true, '', true);
			$n = get_adjacent_post(true, '', false);
		}else{
			$p = get_adjacent_post(false, '', true);
			$n = get_adjacent_post(false, '', false);
		}
		if(!empty($p)){ 
		?>
        <div class="prev-post">
            <a href="<?php echo get_permalink($p->ID) ?>" title="<?php echo esc_attr($p->post_title) ?>">
                <span><?php echo __( 'previous', 'videopro' )?></span>
                <?php echo esc_attr($p->post_title) ?>
            </a>
        </div>
        <?php }
		if(!empty($n)){ 
		?>
        <div class="next-post">
            <a href="<?php echo get_permalink($n->ID) ?>" title="<?php echo esc_attr($n->post_title) ?>">
                <span><?php echo __( 'next', 'videopro' )?></span>
                <?php echo esc_attr($n->post_title) ?>
            </a>
        </div>
        <?php }?>
    </div>
    <!--navigation post-->
    
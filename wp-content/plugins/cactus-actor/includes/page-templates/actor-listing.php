<?php
/**
 * Template Name: Actor Listing
 *
 * @package videopro
 */

get_header();
$sidebar = ot_get_option('blog_sidebar','right');
$sidebar = $sidebar == 'hidden' ? 'full' : $sidebar;

$videopro_page_layout = get_post_meta(get_the_ID(), 'page_sidebar', true);

if($videopro_page_layout != '0'){
    $sidebar = $videopro_page_layout;
}
$layout = videopro_global_layout();
$sidebar_style = 'ct-small';
videopro_global_sidebar_style($sidebar_style);
?>
<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($sidebar!='full' && $sidebar!='left'){?>sb-ct-medium<?php }if($sidebar!='full' && $sidebar!='right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        <div class="cactus-container <?php if($layout=='wide'){ echo 'ct-default';}?>">                        	
            <div class="cactus-row">
				<?php if($layout=='boxed'&& $sidebar=='both'){?>
                    <div class="open-sidebar-small open-box-menu"><i class="fa fa-bars"></i></div>
                <?php }?>
                <?php if($sidebar!='full' && $sidebar!='right'){ get_sidebar('left'); } ?>
                <?php if(is_active_sidebar('content-top-sidebar')){
                    echo '<div class="content-top-sidebar-wrap">';
                    dynamic_sidebar( 'content-top-sidebar' );
                    echo '</div>';
                } ?>
                <div class="main-content-col">
                    <div class="main-content-col-body">
                    
                        <?php if(function_exists('videopro_breadcrumbs')){
							 videopro_breadcrumbs();
						}?>
                		<h1 class="castings-title category-title entry-title"><?php if(is_page()){the_title();} else{ esc_html_e('Actors','videopro');}?></h1>
                        <?php 
                        $query_string = '';
                        global $wp;
						if(get_option('permalink_structure') != ''){
							$current_url = get_permalink();
							if(function_exists('qtrans_getLanguage') && qtrans_getLanguage()!=''){
								$current_url = '//'.$_SERVER["HTTP_HOST"] . $_SERVER['REDIRECT_URL'];
							}
						} else{
							$query_string = remove_query_arg(array('paged', 'pagename'), $wp->query_string);
						}
                        
                        if(isset($_GET['lang'])){
                            $query_string = add_query_arg('lang', $_GET['lang'], $query_string);
                        }
                        
                        $current_url = add_query_arg( $query_string, '', get_permalink() );
                        
                        ?>
                        <div class="alphabet-filter">
                            <a class="font-size-1 ct-gradient metadata-font <?php echo isset($_GET['orderby'])?'':'active' ?>" href="<?php echo esc_url(remove_query_arg('orderby', $current_url)); ?>"><?php esc_html_e('All','videopro'); ?></a>
                                <?php 
								
								$startCapital = 65;
                                
                                for( $i = 0; $i < apply_filters('videopro_language_characters_count', 26); $i++ ){
                                    $char = apply_filters('videopro_language_character', chr($startCapital + $i), $i);
                                ?>
                            	<a class="font-size-1 ct-gradient metadata-font <?php echo (isset($_GET['orderby']) && $char == $_GET['orderby']) ? 'active' : ''; ?>" href="<?php echo esc_url(add_query_arg( array('orderby' => $char), $current_url )); ?>"><?php echo $char; ?></a>
                              	<?php }?>
                        </div>
                        <?php 
						$paged = get_query_var('paged') ? get_query_var('paged') : ( get_query_var('page') ? get_query_var('page') : 1 );
						$args = array(
							'post_type' => 'ct_actor',
							'posts_per_page' => apply_filters('videopro_actors_listing_posts_per_page', get_option('posts_per_page')),
							'post_status' => 'publish',
							'ignore_sticky_posts' => 1,
							'paged' => $paged,
							'orderby' => 'title',
							'order' => 'ASC'
						);
						if(is_tax('actor_cat')){
							$args['tax_query'] = array(
								array(
								'taxonomy' => 'actor_cat',
								'field' => 'id',
								'terms' => get_queried_object()->term_id,
								 )
							  );
						}
						if(isset($_GET['orderby'])){
							$wpdb = videopro_global_wpdb(); 
							$character = $_GET['orderby']; // could be any letter you want
							$results = $wpdb->get_results($wpdb->prepare(
								  "
								  SELECT ID FROM $wpdb->posts
								  WHERE post_title LIKE %s
								  AND post_type = 'ct_actor'
								  AND post_status = 'publish'
								  ORDER BY post_title ASC
								  ",$character.'%')
							); 
							if(!empty($results)){
								$ar_id = array();
								foreach ($results as $item){
									$ar_id[] = $item->ID;
								}
								$args['post__in'] = $ar_id;
							}else{
								$args['post__in'] = array(-1);
							}
							
							$args['orderby'] = 'post__in';
						}
						
						//print_r($args);exit;
						$list_query = new WP_Query( $args );
						$it = $list_query->post_count;
						if($list_query->have_posts()){
							
							$wp_query = videopro_global_wp_query();
							$wp = videopro_global_wp();
							$main_query = $wp_query;
							$wp_query = $list_query;
						?>
						
						<script type="text/javascript">
						 var cactus = {"ajaxurl":"<?php echo admin_url( 'admin-ajax.php' );?>","query_vars":<?php echo str_replace('\/', '/', json_encode($args)) ?>,"current_url":"<?php echo home_url($wp->request);?>" }
						</script> 
                            <div class="cactus-listing-wrap actor-listing">
                                <div class="cactus-listing-config style-2 style-castings"> <!--addClass: style-1 + (style-2 -> style-n)-->
                                    <div class="cactus-sub-wrap">
                                    <?php
										if ( have_posts() ) :
											while ( have_posts() ) : the_post();
												include actor_get_plugin_url() . 'templates/loop/content-actor.php';
											endwhile;
										endif;
									?>                                          
                                    </div>
                                </div>
                            </div>
                            <?php videopro_paging_nav('.cactus-listing-wrap.actor-listing .cactus-sub-wrap', videopro_get_template_slug(actor_get_plugin_url() . 'templates/loop/content-actor.php')); ?>
                        <?php } else{
							?>
                            <p><?php esc_html_e('There isn\'t any Actors in this category', 'videopro');?></p>
                            <?php
						}
						wp_reset_postdata();
                        if($it>0){
                            $wp_query = $main_query;
                        }
                        ?>
                    </div>
                </div>
                 <?php 
                $sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($sidebar_style);
                if($sidebar!='full'&& $sidebar!='left'){ get_sidebar(); } ?>
            </div>
        </div>
    </div>
</div>

<?php
get_footer();

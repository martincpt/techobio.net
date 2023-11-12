<?php
/**
 * The Template for displaying all single posts.
 *
 * @package cactus
 */
//

$post_format = get_post_format();
if($post_format == 'video') {
	get_template_part('single-video');
	return;
}


get_header();
$videopro_sidebar = get_post_meta(get_the_ID(),'post_sidebar',true);
if(!$videopro_sidebar){
	$videopro_sidebar = ot_get_option('post_sidebar','right');
}
if($videopro_sidebar == 'hidden') $videopro_sidebar = 'full';
$videopro_page_title = videopro_global_page_title();
$videopro_layout = videopro_global_layout();
$videopro_sidebar_style = 'ct-small';
videopro_global_sidebar_style($videopro_sidebar_style);
$videopro_post_layout = videopro_global_post_layout();
?>
<div id="cactus-body-container">
    <div class="cactus-sidebar-control <?php if($videopro_sidebar != 'full' && $videopro_sidebar != 'left'){?>sb-ct-medium<?php }if($videopro_sidebar != 'full' && $videopro_sidebar != 'right'){?> sb-ct-small<?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        <div class="cactus-container <?php if($videopro_layout == 'wide'){ echo 'ct-default';}?>">
            <div class="cactus-row">
            	<?php if($videopro_post_layout == 2 && $post_format != 'video'){?>
				<div class="videov2-style dark-div">
                	<?php
					videopro_echo_breadcrumbs(get_the_ID(), $videopro_post_layout, $post_format);

                    videopro_print_advertising('ads_top_ct');

					get_template_part( 'html/single/header-content');
					?>
                </div>
                <?php
				}

				if($videopro_layout == 'boxed' && $videopro_sidebar == 'both'){ ?>
                    <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                <?php }

				if($videopro_sidebar!='full' && $videopro_sidebar!='right'){ get_sidebar('left'); } ?>
                <div class="main-content-col">
                      <div class="main-content-col-body">
						  <?php if(is_active_sidebar('content-top-sidebar')){
                              echo '<div class="content-top-sidebar-wrap">';
                              dynamic_sidebar( 'content-top-sidebar' );
                              echo '</div>';
                          } ?>
                          <div class="single-post-content">
                              <article class="cactus-single-content hentry">
									<?php
									while ( have_posts() ) : the_post();

									if(($videopro_post_layout != 2 && $post_format != 'video') || $post_format == 'video') {

									  videopro_echo_breadcrumbs(get_the_ID(), $videopro_post_layout, $post_format);

                                      videopro_print_advertising('ads_top_ct');

									  get_template_part( 'html/single/header-content');
									}

									videopro_post_toolbar(get_the_ID(), $post_format );

									?>

									<h1 class="single-title entry-title"><?php the_title();?></h1>

									<?php
                                        get_template_part( 'html/single/content');

                                    videopro_print_advertising('ads_single_2');

									if(ot_get_option('show_post_navi','on')!='off'){
										echo videopro_post_nav();
									}

									if(ot_get_option('show_about_the_author','on')!='off'){
										get_template_part( 'html/single/content-about-author');
									}

									if(ot_get_option('show_related_post','on')!='off'){
										get_template_part( 'html/single/single-related');
									}

									if(ot_get_option('show_comment', 'on')!='off'){
										if ( comments_open() || '0' != get_comments_number() ){
											comments_template();
										}
									}

									endwhile; // end of the loop.

									?>
                              </article>
							</div>
							<?php
							if(is_active_sidebar('content-bottom-sidebar')){
								echo '<div class="content-bottom-sidebar-wrap">';
									dynamic_sidebar( 'content-bottom-sidebar' );
								echo '</div>';
							}
							?>
                      </div>
                  </div>
                 <?php
                $videopro_sidebar_style = 'ct-medium';
				videopro_global_sidebar_style($videopro_sidebar_style);
                if($videopro_sidebar!='full' && $videopro_sidebar!='left'){
					get_sidebar();
				} ?>
            </div>
        </div>
    </div>
</div>
<?php get_footer(); ?>
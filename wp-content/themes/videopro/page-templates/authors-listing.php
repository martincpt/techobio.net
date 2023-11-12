<?php
/*
Template Name: Authors Listing
*/

// Get all users order by amount of posts
$paged = 1;

if(isset($_GET['paged'])){
    $paged = intval($_GET['paged']);
}

$posts_per_page = get_post_meta(get_the_ID(), 'authors_per_page', true);

if(!isset($posts_per_page) || $posts_per_page <= 0){
    $posts_per_page = 0;
}

$membership_id = get_post_meta(get_the_ID(), 'membership_id', true);
if($membership_id != '' && class_exists('MS_Factory')){
    // get users by membership
    $args = array(
            'posts_per_page' => $posts_per_page == 0 ? -1 : $posts_per_page,
            'offset' => $posts_per_page * ( $paged - 1 )
        );
        
    $membership = MS_Factory::load( 'MS_Model_Membership', $membership_id );
    
    $members = $membership->get_members($args);
    $totalCount = $membership->get_members_count();
} else {
    // get users by user role
    $user_role = get_post_meta(get_the_ID(), 'authors_role', true);
    if(!isset($user_role) || $user_role == '') $user_role = 'author';

    $roles = array();
    switch($user_role){
        case 'author':
        case 'subscriber':
            $roles = array($user_role);
            break;
        case 'author_subscriber':
            $roles = array('author', 'subscriber');
            break;
        default:
            break;
    }

    // allow to filter the roles
    $roles = apply_filters('videopro_authors_listing_roles', $roles);

    $orderby = get_post_meta(get_the_ID(), 'authors_orderby', true);
    if(!isset($orderby) || $orderby == '') $orderby = 'display_name';

    $order = 'ASC';

    if($orderby == 'post_count') $order = 'DESC';

    $args = array(
                        'orderby' => $orderby,
                        'order' => $order,
                        'role__in'  => $roles);
                        
    if($posts_per_page != 0){
        $args['number'] = $posts_per_page;
        $args['paged']  = $paged;
    }

    $members = get_users($args);

    // count total users
    $user_query = new WP_User_Query( array( 'role__in'  => $roles, 'count_total' => true ) );
    $totalCount = $user_query->get_total();
}

// allow result to be filtered
$members = apply_filters('videopro_authors_listing_list', $members, array(
                                                                            'membership_id' => $membership_id,
                                                                            'user_roles' => $user_role,
                                                                            'posts_per_page' => $posts_per_page,
                                                                            'paged' => $paged
                                                                        ));
                                                                        
$totalCount = apply_filters('videopro_authors_listing_total', $totalCount, array(
                                                                            'membership_id' => $membership_id,
                                                                            'user_roles' => $user_role
                                                                        ));

$layout = get_post_meta(get_the_ID(),'sidebar',true);
if(!$layout){
	$layout = isset( $global_page_layout ) && $global_page_layout ? $global_page_layout : ot_get_option('page_layout','right');
}

?>

<?php
get_header();

$videopro_sidebar = get_post_meta(get_the_ID(),'page_sidebar',true);
if(!$videopro_sidebar){
	$videopro_sidebar = ot_get_option('page_sidebar','both');
}
if($videopro_sidebar == 'hidden') $videopro_sidebar = 'full';
$videopro_page_title = videopro_global_page_title();
$videopro_layout = videopro_global_layout();
$videopro_sidebar_style = 'ct-small';
videopro_global_sidebar_style($videopro_sidebar_style);
?>
    <!--body content-->
    <div id="cactus-body-container">
    
        <div class="cactus-sidebar-control <?php if($videopro_sidebar=='right' || $videopro_sidebar=='both'){?>sb-ct-medium <?php }?>  <?php if($videopro_sidebar!='full' && $videopro_sidebar!='right'){?>sb-ct-small <?php }?>"> <!--sb-ct-medium, sb-ct-small-->
        
            <div class="cactus-container <?php if($videopro_layout=='wide'){ echo 'ct-default';}?>">                        	
                <div class="cactus-row">
                    <?php if($videopro_layout == 'boxed' && ($videopro_sidebar == 'both')){?>
                        <div class="open-sidebar-small open-box-menu"><i class="fas fa-bars"></i></div>
                    <?php }?>
                    <?php if($videopro_sidebar == 'left' || $videopro_sidebar == 'both'){ get_sidebar('left'); } ?>
                    
                    <div class="main-content-col">
                        <div class="main-content-col-body">
                        	<div class="single-page-content">
                                <article class="cactus-single-content">                                	
									<?php 	
									if(!is_page_template('page-templates/front-page.php')){								
										videopro_breadcrumbs();
										?>                        
										<h1 class="single-title entry-title"><?php echo esc_html($videopro_page_title);?></h1>
										<?php 
									}else{
										echo '<h2 class="hidden-title">'.esc_html($videopro_page_title).'</h2>';
									}?>
                                    <?php
									if(is_active_sidebar('content-top-sidebar')){
                                        echo '<div class="content-top-sidebar-wrap">';
                                        dynamic_sidebar( 'content-top-sidebar' );
                                        echo '</div>';
                                    } 
                                    
                                    $column_width = apply_filters('videopro-author-listing-columns', 4);
                                    if(in_array($column_width, array(3,4,6,12))){
                                        
                                    } else {
                                        $column_width = 4; // default 
                                    }
                                    
                                    $columns = 12 / $column_width;
                                    ?>
                
                                    <section class="authors-listing" id="authors-list">
                                        <div class="authors-listing-content">
                                            <div class="vp-row">
                                            <?php
                                            $i = 0;
                                            foreach($members as $member)
                                            {
                                                if(get_class($member) == 'MS_Model_Member'){
                                                    $user = $member->get_user();
                                                } else {
                                                    $user = $member;
                                                }
                                                
                                                $user = apply_filters('videopro_authors_listing_convert_item_to_WP_User', $user);
                                                
                                                $name = $user->display_name;
                                                if($name == ''){
                                                    $name = $user->user_nicename;
                                                }
                                                if($name != ''){
                                                    $i++;
                                                    
                                                    $count = count_user_posts($user->ID);
                                                ?>
                                                <div class="vp-col vp-col-<?php echo esc_attr($column_width);?>">
                                                    <div class="vp-col-inner "><div class="wpb_wrapper">
                                                        <div class="user with-name">
                                                            <div class="user-data">
                                                                <a href="<?php echo get_author_posts_url( $user->ID ); ?>" class="thumbnail" title="<?php echo esc_attr($name); ?>">
                                                                    <span class="avatar" title="<?php echo esc_html($name); ?>"><?php echo get_avatar( $user->user_email, '60' ); ?></span>
                                                                </a>
                                                                <h3 class="author-name name data"><a href="<?php echo get_author_posts_url( $user->ID ); ?>" class="" title="<?php echo esc_attr($name); ?>"><?php echo esc_html($name); ?><?php do_action('videopro_after_title', $user->ID, 'author' );?></a></h3>
                                                                <span class="posts_count data"><?php echo $count < 2 ? sprintf(__('%d post','videopro'), $count) : sprintf(__('%d posts','videopro'), $count);?></span>
                                                                <?php 
                                                                
                                                                $description = get_user_meta($user->ID, 'description', true);
                                                                if($description != ''){
                                                                ?>
                                                                <span class="description data"><?php echo esc_html($description); ?><br><br></span>
                                                                <?php
                                                                }
                                                                
                                                                $accounts = '';
                                                                
                                                                if($user->user_url != ''){
                                                                    $accounts .= '<li class="website"><a rel="nofollow" href="' . esc_url($user->user_url) . '" title="' . esc_html__('Website', 'videopro') . '"><i class="fas fa-globe"></i></a></li>';
                                                                }
                                                                
                                                                if($email = get_the_author_meta('author_email',$user->ID) && ot_get_option('author_page_email_contact','on') == 'on'){
                                                                    $accounts .= '<li class="email"><a rel="nofollow" href="mailto:' . esc_attr($email) . '" title="' . esc_html__('Email', 'videopro') . '"><i class="fas fa-envelope-o"></i></a></li>';
                                                                }
                                                                  
                                                                if(ot_get_option('author_page_social_accounts','on') == 'on'){
                                                                    if($facebook = get_the_author_meta('facebook',$user->ID)){
                                                                        $accounts .= '<li class="facebook"><a rel="nofollow" href="' . esc_url($facebook) . '" title="' . esc_html__('Facebook', 'videopro') . '"><i class="fab fa-facebook"></i></a></li>';
                                                                    }
                                                                    
                                                                    if($twitter = get_the_author_meta('twitter',$user->ID)){
                                                                        $accounts .= '<li class="twitter"><a rel="nofollow" href="' . esc_url($twitter) . '" title="' . esc_html__('Twitter', 'videopro') . '"><i class="fab fa-twitter"></i></a></li>';
                                                                    }
                                                                    
                                                                    if($linkedin = get_the_author_meta('linkedin',$user->ID)){
                                                                        $accounts .= '<li class="linkedin"><a rel="nofollow" href="' . esc_url($linkedin) . '" title="' . esc_html__('Linkedin', 'videopro') . '"><i class="fab fa-linkedin"></i></a></li>';
                                                                    }
                                                                    
                                                                    if($tumblr = get_the_author_meta('tumblr',$user->ID)){
                                                                        $accounts .= '<li class="tumblr"><a rel="nofollow" href="' . esc_url($tumblr) . '" title="' . esc_html__('Tumblr', 'videopro') . '"><i class="fab fa-tumblr"></i></a></li>';
                                                                    }
                                                                    
                                                                    if($google = get_the_author_meta('google',$user->ID)){
                                                                        $accounts .= '<li class="google-plus"> <a rel="nofollow" href="' . esc_url($google) . '" title="' . esc_html__('Google Plus', 'videopro') . '"><i class="fab fa-google-plus"></i></a></li>';
                                                                    }
                                                                    
                                                                    if($pinterest = get_the_author_meta('pinterest',$user->ID)){
                                                                        $accounts .= '<li class="pinterest"> <a rel="nofollow" href="' . esc_url($pinterest) . '" title="' .  esc_html__('Pinterest', 'videopro') . '"><i class="fab fa-pinterest"></i></a></li>';
                                                                    }
                                                                      
                                                                    if($custom_acc = get_the_author_meta('cactus_account',$user->ID)){
                                                                          foreach($custom_acc as $acc){
                                                                              if($acc['icon'] || $acc['url']){
                                                                                  
                                                                                  $accounts .= '<li class="cactus_account custom-account-' . sanitize_title(@$acc['title']) . '"><a rel="nofollow" href="' . esc_attr(@$acc['url']) . '" title="' . esc_attr(@$acc['title']) . '"><i class="fab ' . esc_attr(@$acc['icon']) . '"></i></a></li>';
                                                                              }
                                                                          }
                                                                    }
                                                                }
                                                            
                                                            if($accounts != ''){
                                                                $accounts = '<ul class="social-listing data list-inline">' . $accounts . '</ul>';
                                                                echo $accounts;
                                                            }
                                                            
                                                            ?>
                                                                
                                                              </div>
                                                              <div class="clearer"><!-- --></div>
                                                        </div>
                                                    </div></div>
                                                </div>
                                                    <?php
                                                    if($i % $columns == 0){
                                                        echo '<div class="clearer"><!-- --></div>';
                                                    }
                                                }
                                            }
                                            ?>
                                            </div>
                                        </div><!--/video-listing-content(blog-listing-content)-->
                                    <?php
                                    
                                    
                                    
                                    if($posts_per_page > 0){
                                        $pages = ceil($totalCount / $posts_per_page);
                                        if($pages > 1){
                                            $baseurl = videopro_get_current_url();
                                            
                                            $baseurl = remove_query_arg('pagename', $baseurl);

                                            videopro_paginate($baseurl,'paged', $pages, $paged, 5);
                                        }
                                    }
                                    
                                    ?>
                                    </section>
                                    
                                    <?php
									
									if(is_active_sidebar('content-bottom-sidebar')){
                                        echo '<div class="content-bottom-sidebar-wrap">';
                                        dynamic_sidebar( 'content-bottom-sidebar' );
                                        echo '</div>';
                                    } ?>
                                </article>
                            </div>
                        </div>
                    </div>
                    
                    <?php 
					$videopro_sidebar_style = 'ct-medium';
					videopro_global_sidebar_style($videopro_sidebar_style);
					if($videopro_sidebar=='right' || $videopro_sidebar=='both'){ get_sidebar(); } ?>
                    
                </div>
            </div>
            
        </div>                
        
        
    </div><!--body content-->

<?php get_footer();
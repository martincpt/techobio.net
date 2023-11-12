<?php 

do_action( 'videopro_before_nav' ); 

$videopro_layout = videopro_global_layout();
$header_background = '';
if(is_page_template('page-templates/front-page.php')){
	$header_schema = get_post_meta(get_the_ID(),'header_schema',true);
	$main_navi_layout = get_post_meta(get_the_ID(),'main_navi_layout',true);
	$main_navi_schema = get_post_meta(get_the_ID(),'main_navi_schema',true);
	$header_background = get_post_meta(get_the_ID(),'header_background',true);
	$front_page_logo = get_post_meta(get_the_ID(),'front_page_logo',true);
	$front_page_logo_sticky = get_post_meta(get_the_ID(),'front_page_logo_sticky',true);
	$main_navi_width = get_post_meta(get_the_ID(),'main_navi_width',true);
}else{
	$header_schema = ot_get_option('header_schema', 'dark');
	$main_navi_layout = ot_get_option('main_navi_layout', 'separeted');
	$main_navi_width = ot_get_option('main_navi_width', 'full');
	$main_navi_schema = ot_get_option('main_navi_schema', 'dark');
	if(is_singular( 'post' ) && get_post_meta(get_the_ID(),'main_navi_layout',true) !=''){
		$main_navi_layout = get_post_meta(get_the_ID(),'main_navi_layout',true);
	}
	if(is_singular( 'post' ) && get_post_meta(get_the_ID(),'main_navi_width',true) !=''){
		$main_navi_width = get_post_meta(get_the_ID(),'main_navi_width',true);
	}
}
?>
<!--Navigation style-->
<div class="cactus-nav-control <?php if(($main_navi_layout=='inline' && $header_schema == 'light')){?> style-1-inline <?php } if($main_navi_layout != 'inline'){?> cactus-nav-style-3<?php } if($header_schema == 'light'){?> cactus-nav-style-5<?php } if(($main_navi_layout=='inline' && $header_schema == 'light') || ($main_navi_layout!='inline' && $main_navi_schema == 'light')){?> cactus-nav-style-7<?php }?>">
    
		 <?php 
		if($header_background==''){ 
			$header_background = ot_get_option('header_background');
		}
		$style = '';
		if(isset($header_background['background-color']) && $header_background['background-color'] != '') {
			$style = 'background-color: ' . esc_attr($header_background['background-color']) . ';';
		}
		if(isset($header_background['background-image']) && $header_background['background-image'] != ''){
			$style .= 'background-image: url(' . esc_url($header_background['background-image']) . ');';
		}
		
		if($style != '') $style = 'style="' . $style . '"';
		 
		 ?>
    <div class="cactus-nav-main dark-div <?php if($header_schema != 'light'){?> dark-bg-color-1<?php }?>" <?php echo ($style != '' ? $style : '');?>>
        
        <div class="cactus-container padding-30px <?php if(($videopro_layout=='wide' && $main_navi_width !='full') || ($videopro_layout=='fullwidth' && $main_navi_width =='inbox')){ echo 'medium';}?>">
            
            <div class="cactus-row magin-30px">
                
                <!--nav left-->
                <div class="cactus-nav-left">
                    <!--logo-->
                    <div class="cactus-logo navigation-font">
                    	<?php $logo = ot_get_option('logo_image','') == '' ? esc_url(get_template_directory_uri()) . '/images/logo.png' : ot_get_option('logo_image','');
						if(is_page_template('page-templates/front-page.php') && $front_page_logo!=''){
							$logo = $front_page_logo;
						}
						 ?>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                        	<img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?>" class="cactus-img-logo">
                            
                            <?php $logo_image_sticky = ot_get_option('logo_image_sticky','') == '' ? esc_url(get_template_directory_uri()) . '/images/logo.png' : ot_get_option('logo_image_sticky',''); 
							if(is_page_template('page-templates/front-page.php') && $front_page_logo_sticky!=''){
								$logo_image_sticky = $front_page_logo_sticky;
							}
							if(ot_get_option('sticky_navigation') == 'on'){?>
                            <img src="<?php echo esc_url($logo_image_sticky); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" title="<?php echo esc_attr(get_bloginfo('name')); ?>" class="cactus-img-logo cactus-img-sticky">
                            <?php }?>
                        </a>
                    </div><!--logo-->
                    
                    <div class="cactus-main-menu cactus-open-left-sidebar right-logo navigation-font">
                        <ul>
                          <li><a href="javascript:;"><span></span><span></span><span></span></a></li>
                        </ul>
                    </div>
                    <?php if(ot_get_option('enable_search') != 'off'){ ?>
                    <!--header search-->
                    <div class="cactus-header-search-form search-box-expandable">
                    	<?php 
						if(is_active_sidebar('searchbox-sidebar')){
							dynamic_sidebar( 'searchbox-sidebar' );
						} else {
							?>
							<div class="cactus-main-menu cactus-open-search-mobile navigation-font">
								<ul>
								  <li><a href="javascript:;"><i class="fas fa-search"></i></a></li>
								</ul>
							</div>
							<form action="<?php echo esc_url(home_url('/'));?>" method="get">								
								<input type="text" placeholder="<?php echo esc_html_e('Search for videos...','videopro');?>" name="s" value="<?php echo esc_attr(get_search_query());?>">
                                <i class="fas fa-search" aria-hidden="true"></i>
								<input type="submit" value="<?php echo esc_html_e('SEARCH','videopro');?>"  id="searchsubmit" class="padding-small">                                
							</form>
                        <?php }?>
                    </div><!--header search-->
                    <?php }?>
					<?php
							do_action('videopro_button_user_submit_video', 'mobile');
							 ?>
                </div> <!--nav left-->
                
                <!--nav right-->
                <div class="cactus-nav-right">
                    <div class="cactus-main-menu cactus-open-menu-mobile navigation-font">
                        <ul>
                          <li><a href="javascript:;"><i class="fas fa-bars"></i><?php echo esc_html__('MENU', 'videopro');?></a></li>
                        </ul>
                    </div>
                    <?php
					do_action('videopro_button_user_submit_video');
					 ?>
                     
                     <!-- CUSTOM WIDGET AREA -->
					 
                     <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar("Navigation right") ) : ?>
					 <?php endif;?>

                    <!-- CUSTOM WIDGET AREA END -->

					<?php 
					$user_show_info = ot_get_option('mebership_login');
					if($user_show_info!='off'){
					$mebership_login_text = ot_get_option('mebership_login_text');
					
					$mebership_logout = ot_get_option('mebership_logout');
                    $membership_profile_menu_item = ot_get_option('membership_profile_menu_item', 'on');
                    $membership_edit_profile_menu_item = ot_get_option('membership_edit_profile_menu_item', 'on');

					$mebership_login_link = wp_login_url(videopro_get_current_url());
                    
                    $membership_register_link = ot_get_option('membership_register_link', 'off');
					$membership_register_text = ot_get_option('membership_register_text');
					$videopro_register_url = ot_get_option('membership_register_url');
                    
                    if($videopro_register_url == ''){
                        $videopro_register_url = wp_registration_url();
                    }
					?>
                    <div class="cactus-main-menu cactus-user-login navigation-font">
                        <ul>                	                 
                            <li>   
                                <?php if ( !is_user_logged_in() ) { ?>                                      
                                    <a href="<?php echo esc_url($mebership_login_link);?>"><i class="fas fa-user"></i>&nbsp;<?php if($mebership_login_text!=''){ echo esc_html($mebership_login_text);}else{esc_html_e('Login','videopro');}?></a>
                                    <?php
                                    if($membership_register_link == 'on'){?>
                                    <ul>
                                        <li><a href="<?php echo esc_url($videopro_register_url);?>"><?php if($membership_register_text != '') { echo esc_html($membership_register_text); } else esc_html_e('Register','videopro'); ?></a></li>
                                    </ul>
                                    <?php }?>
                                <?php } else {
                                $current_user = wp_get_current_user();
								$mebership_logged_display = ot_get_option('mebership_logged_display');
								if($mebership_logged_display =='2' && $current_user->first_name!=''){
									$name = $current_user->first_name;
								}elseif($mebership_logged_display =='3' && ($current_user->first_name!='' || $current_user->last_name!='')){
									$name = $current_user->first_name.' '.$current_user->last_name;
								}else{
									$name = $current_user->nickname;
								}
                                $user_avatar = get_avatar( $current_user->ID );
								$is_user_set_avatar = false;
                                if ($user_avatar && strpos($user_avatar, 'd=blank') !== false) {
                                    $is_user_set_avatar = false;
                                } else {
                                    $is_user_set_avatar = true;
                                } ?>
                                    <a class="user-info <?php echo $is_user_set_avatar ? 'has-avatar' : '';?>" href="<?php echo apply_filters('videopro_user_login_name_url', 'javascript:;');?>">
                                        <?php
                                        if ($is_user_set_avatar) {
                                            echo $user_avatar;
                                        } else { ?>
                                            <i class="fas fa-user"></i>&nbsp;
                                        <?php } ?>
                                        <?php echo esc_html($name);?>
                                    </a>
                                    <?php
                                    if(has_nav_menu( 'user-menu' )){
                                        ?>
                                        <ul>
                                            <?php
											
											do_action('videopro_before_usermenu', $current_user);
											
                                            wp_nav_menu(array(
                                                'theme_location'  => 'user-menu',
                                                'container' => false,
                                                'items_wrap' => '%3$s',
                                            ));
                                            ?>
                                            <?php if ($membership_profile_menu_item == 'on') { ?>
                                                <li><a href="<?php echo get_author_posts_url(get_current_user_id()); ?>"><?php esc_html_e('Public Profile', 'videopro') ?></a></li>
                                            <?php } ?>

                                            <?php if ($membership_edit_profile_menu_item == 'on') { ?>
                                                <li><a href="<?php echo get_edit_user_link( get_current_user_id() ); ?>"><?php esc_html_e('Edit Profile','videopro') ?></a></li>
                                            <?php } ?>

                                            <?php if ($mebership_logout != 'off') { ?>
                                                <li><a href="<?php echo wp_logout_url(videopro_get_current_url()); ?>"><?php esc_html_e('Logout', 'videopro') ?></a></li>
                                            <?php } ?>
                                        </ul>
                                        <?php 
                                    }else{
                                        ?>
                                        <ul>
                                            <?php
                                            $query = new WP_Query( array('post_type'  => 'page', 'posts_per_page' => 1, 'meta_key' => '_wp_page_template', 'meta_value' => 'cactus-video/includes/page-templates/channel-listing.php' ) );
                                            if ( $query->have_posts() ) while ( $query->have_posts() ) : $query->the_post();?>
                                                <li><a href="<?php echo get_permalink(); ?>"><?php the_title(); ?></a></li>
                                            <?php endwhile; 
                                            wp_reset_postdata();
                                            ?>
                                            <?php if($membership_profile_menu_item == 'on'){?>
                                            <li><a href="<?php echo get_author_posts_url(get_current_user_id()); ?>"><?php esc_html_e('Public Profile','videopro') ?></a></li>
                                            <?php }?>
                                            <li><a href="<?php echo get_edit_user_link( $current_user->ID ); ?>"><?php esc_html_e('Edit Profile','videopro') ?></a></li>
                                            <li><a href="<?php echo wp_logout_url( videopro_get_current_url() ); ?>"><?php esc_html_e('Logout','videopro') ?></a></li>
                                        </ul>
                                    <?php }?>
                                <?php }?>
                            </li>                                       
                        </ul>
                    </div>
					<?php }?>	
                </div><!--nav right-->
                <?php if($main_navi_layout == 'inline'){ ?>
                    <!--nav left-->
                    <div class="cactus-nav-left cactus-only-main-menu">
                         <!--main menu / megamenu / Basic dropdown-->                                  
                        <div class="cactus-main-menu navigation-font">
                        	<?php 
							if(is_active_sidebar('mainmenu-sidebar')){
								dynamic_sidebar( 'mainmenu-sidebar' );
							}else{
							?>
                            <ul class="nav navbar-nav">
                                <?php
                                    $megamenu = ot_get_option('megamenu', 'off');
                                    if($megamenu == 'on' && function_exists('mashmenu_load') && has_nav_menu( 'primary' )){
                                        mashmenu_load();
                                    }elseif(has_nav_menu( 'primary' )){
                                        wp_nav_menu(array(
                                            'theme_location'  => 'primary',
                                            'container' => false,
                                            'items_wrap' => '%3$s',
                                            'walker'=> new videopro_custom_walker_nav_menu()
                                        )); 
                                    }else{?>
                                        <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home','videopro') ?></a></li>
                                        <?php wp_list_pages('depth=1&title_li=' ); ?>
                                <?php } ?>
                                <?php if (class_exists('Woocommerce')) {
                                    global $woocommerce, $wp;
                                    $cart_url = wc_get_cart_url();
                                    $checkout_url = wc_get_checkout_url();
                                    $array_links = array(rtrim($cart_url, '/'), rtrim($checkout_url, '/'));
                                    if ($woocommerce->cart->get_cart_contents_count()) {
                                        ?>
                                        <li class="main-menu-item menu-item-depth-0 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children parent dropdown <?php if (in_array(home_url($wp->request), $array_links)) echo "current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor"?>">
                                            <a href="javascript:void(0)" class="menu-link dropdown-toggle disabled main-menu-link cactus-hasIcon" data-toggle="dropdown">
                                                <i class="fa fa-shopping-cart"></i><?php echo ' (' . $woocommerce->cart->get_cart_contents_count(), ')'; ?>
                                                <?php esc_html_e('Cart','videopro') ?>
                                            </a>
                                            <ul class="dropdown-menu menu-depth-1">
                                                <li class="sub-menu-item  menu-item-depth-1 menu-item menu-item-type-post_type">
                                                    <a href="<?php echo $cart_url; ?>" class="menu-link  sub-menu-link"><?php _e('View Cart', 'videopro') ?></a>
                                                </li>
                                                <li class="sub-menu-item menu-item-depth-1 menu-item menu-item-type-post_type">
                                                    <a href="<?php echo $checkout_url; ?>" class="menu-link  sub-menu-link"><?php _e('Check Out', 'videopro') ?></a>
                                                </li>
                                            </ul>
                                        </li>
                                    <?php }
                                } ?>
                           </ul>
                           <?php }?>
                        </div><!--main menu-->
                    </div><!--nav left-->                                
                <?php }?>
            </div>
            
        </div>
        
    </div>
    
</div>
<?php if($main_navi_layout !='inline'){ ?>
<div class="cactus-nav-control <?php if($main_navi_layout != 'inline'){?> cactus-nav-style-3<?php }if($videopro_layout=='wide'){ echo ' cactus-nav-style-4 ';}if($main_navi_schema == 'light'){?> cactus-nav-style-7<?php }?>">  <!--add Class: cactus-nav-style-3-->
    <div class="cactus-nav-main dark-div dark-bg-color-1">
        
        <div class="cactus-container padding-30px <?php if(($videopro_layout=='wide' && $main_navi_width !='full') || ($videopro_layout=='fullwidth' && $main_navi_width =='inbox')){ echo 'medium';}?>">
            
            <!--Menu Down-->
            <div class="cactus-row magin-30px">
                <!--nav left-->
                <div class="cactus-nav-left cactus-only-main-menu">
                    <!--main menu / megamenu / Basic dropdown-->                                  
                    <div class="cactus-main-menu navigation-font">
                    <?php 
						if(is_active_sidebar('mainmenu-sidebar')){
							dynamic_sidebar( 'mainmenu-sidebar' );
						}else{
						?>
                        <ul class="nav navbar-nav">
                            <?php
                                $megamenu = ot_get_option('megamenu', 'off');
                                if($megamenu == 'on' && function_exists('mashmenu_load') && has_nav_menu( 'primary' )){
                                    mashmenu_load();
                                }elseif(has_nav_menu( 'primary' )){
                                    wp_nav_menu(array(
                                        'theme_location'  => 'primary',
                                        'container' => false,
                                        'items_wrap' => '%3$s',
                                        'walker'=> new videopro_custom_walker_nav_menu()
                                    )); 
                                }else{?>
                                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home','videopro') ?></a></li>
                                    <?php wp_list_pages('depth=1&title_li=' ); ?>
                            <?php } ?>
                            <?php if (class_exists('Woocommerce')) {
                                global $woocommerce, $wp;
                                $cart_url = wc_get_cart_url();
                                $checkout_url = wc_get_checkout_url();
                                $array_links = array(rtrim($cart_url, '/'), rtrim($checkout_url, '/'));
                                if ($woocommerce->cart->get_cart_contents_count()) {
                                    ?>
                                    <li class="main-menu-item menu-item-depth-0 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children parent dropdown <?php if (in_array(home_url($wp->request), $array_links)) echo "current-menu-ancestor current-menu-parent current_page_parent current_page_ancestor"?>">
                                        <a href="javascript:void(0)" class="menu-link dropdown-toggle disabled main-menu-link cactus-hasIcon" data-toggle="dropdown">
                                            <i class="fa fa-shopping-cart"></i><?php echo ' (' . $woocommerce->cart->get_cart_contents_count(), ')'; ?>
                                            <?php esc_html_e('Cart','videopro') ?>
                                        </a>
                                        <ul class="dropdown-menu menu-depth-1">
                                            <li class="sub-menu-item  menu-item-depth-1 menu-item menu-item-type-post_type">
                                                <a href="<?php echo $cart_url; ?>" class="menu-link  sub-menu-link"><?php _e('View Cart', 'videopro') ?></a>
                                            </li>
                                            <li class="sub-menu-item menu-item-depth-1 menu-item menu-item-type-post_type">
                                                <a href="<?php echo $checkout_url; ?>" class="menu-link  sub-menu-link"><?php _e('Check Out', 'videopro') ?></a>
                                            </li>
                                        </ul>
                                    </li>
                                <?php }
                            } ?>
                       </ul>
                       <?php }?>
                    </div><!--main menu-->
                </div><!--nav left-->  
                
            </div>
            <!--Menu Down-->
            
        </div>
    </div>
</div>
<?php } ?>
<!--Navigation style-->
<?php do_action( 'videopro_after_nav' ); ?>
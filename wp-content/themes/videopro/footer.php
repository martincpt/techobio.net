<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package cactus
 */
if(is_active_sidebar('main-bottom-sidebar')){
	$_GET['mbt_c']=1;
	echo '<div class="main-bottom-sidebar-wrap">';
		dynamic_sidebar( apply_filters('videopro_main_bottom_sidebar', 'main-bottom-sidebar' ) );
	echo '</div>';	
	$_GET['mbt_c']=0;
}

$footer_schema = apply_filters('videopro-footer-schema', 'dark-div');
?>
            <!--footer-->
            <footer>
            	<div class="footer-inner <?php echo $footer_schema;?>">
                	<?php if ( is_active_sidebar( 'footer-sidebar' ) ) :
						videopro_width_widget(1); ?>
                        <div class="footer-sidebar cactus-sidebar">
                            <div class="cactus-container padding-20px">                        	
                                <div class="cactus-row magin-20px">
                                    <?php dynamic_sidebar( 'footer-sidebar' ); ?>
                                </div>
                            </div>
                        </div>
                    <?php 
					videopro_width_widget(0);
					endif; //if active sidebar ?>
                </div>
                
                <div class="footer-info <?php echo $footer_schema;?>">
                	<div class="cactus-container padding-20px">
                    	<div class="cactus-row magin-20px">
                        	<div class="copyright font-1"><?php echo wp_kses_post(ot_get_option('copyright', 'WordPress Theme by CactusThemes'));?></div>
                            <div class="link font-1">
                            	<div class="menu-footer-menu-container <?php if(ot_get_option('gototop', 'on') == 'on'){ echo 'has-gototop'; }?>">
                                	<ul id="menu-footer-menu" class="menu">
                                    	<?php
											if(has_nav_menu( 'footer-menu' )){
												wp_nav_menu(array(
													'theme_location'  => 'footer-menu',
													'container' => false,
													'items_wrap' => '%3$s',
												));	
											}
										?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </footer><!--footer-->
            <?php 
			$adsense_slot_ads_bottom_page = ot_get_option('adsense_slot_ads_bottom_page');
			$ads_bottom_page = ot_get_option('ads_bottom_page');
			if($adsense_slot_ads_bottom_page != '' || $ads_bottom_page != ''){?>
				<div class="ads-system page-wrap">
					<div class="ads-content">
					<?php
					if($adsense_slot_ads_bottom_page != ''){ 
						echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_bottom_page . '"]');
					}else if($ads_bottom_page != ''){
						echo do_shortcode($ads_bottom_page);
					}
					?>
					</div>
				</div>
				<?php
			}
			?>
    	</div>
        
        <?php 
		$adsense_slot_ads_wall_left = ot_get_option('adsense_slot_ads_wall_left');
		$ads_wall_left = ot_get_option('ads_wall_left');
		if($adsense_slot_ads_wall_left != '' || $ads_wall_left != ''){?>
			<div class="wall-ads-control wall-ads-left">
                <?php
				if($adsense_slot_ads_wall_left != ''){ 
					echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_wall_left . '"]');
				}else if($ads_wall_left != ''){
					echo do_shortcode($ads_wall_left);
				}
				?>
            </div>
			<?php
		}
		?>
        <?php 
		$adsense_slot_ads_wall_right = ot_get_option('adsense_slot_ads_wall_right');
		$ads_wall_right = ot_get_option('ads_wall_right');
		if($adsense_slot_ads_wall_right != '' || $ads_wall_right != ''){?>
			<div class="wall-ads-control wall-ads-right">
                <?php
				if($adsense_slot_ads_wall_right != ''){ 
					echo do_shortcode('[adsense pub="' . ot_get_option('adsense_id') . '" slot="' . $adsense_slot_ads_wall_right . '"]');
				}else if($ads_wall_right != ''){
					echo do_shortcode($ads_wall_right);
				}
				?>
            </div>
			<?php
		}
		?>
        
        <!--Menu moblie-->
        <div class="canvas-ovelay"></div>
        <div id="off-canvas" class="off-canvas-default dark-div">
          <div class="off-canvas-inner navigation-font">
            <div class="close-canvas-menu"> <i class="fas fa-times"></i> <?php echo __('CLOSE', 'videopro')?> </div>
            <nav class="off-menu">
              <ul>                        	
				  <?php
                  if(has_nav_menu( 'primary' )){
                      wp_nav_menu(array(
                          'theme_location'  => 'primary',
                          'container' => false,
                          'items_wrap' => '%3$s',
						  'walker'=> new videopro_custom_walker_nav_menu()
                      ));	
                  }else{ ?>
                      <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home','videopro') ?></a></li>
                      <?php wp_list_pages('depth=1&number=4&title_li=' );
                  } ?>
                  <?php if (class_exists('Woocommerce')) {
                      global $woocommerce;
                      $cart_url = wc_get_cart_url();
                      $checkout_url = wc_get_checkout_url();
                      if ($woocommerce->cart->get_cart_contents_count()) {
                          ?>
                          <li class="main-menu-item menu-item-depth-0 menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children parent dropdown">
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
            </nav>
          </div>
        </div>
        <!--Menu moblie-->
    </div>
    
    <?php
    
    if(ot_get_option('gototop', 'on') == 'on'){
    ?>
    <div id="gototop" class="hidden">
        <a href="#top" title="<?php echo esc_html__('To Top', 'videopro');?>"><i class="fas fa-angle-up"></i></a>
    </div>
    <?php    
    }
    
		/**
		 * to print out additional HTML at the end of the page 
		 */
		do_action('videopro_before_end_body');
		
		wp_footer(); ?>     
</body>
</html>
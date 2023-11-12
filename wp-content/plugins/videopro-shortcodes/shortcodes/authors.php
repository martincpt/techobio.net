<?php
class CactusShortcodeAuthors extends CactusShortcode {
	public function __construct( $attrs = null, $content = '' ) {
		parent::__construct('c_authors', $attrs , $content);
	}

	public function parse_shortcode($atts, $content){
		$id 		= isset($atts['id']) ? $atts['id'] : 'cactus-authors-' . rand(1, 9990);
        $count      = isset($atts['count']) ? $atts['count'] : 5;
        $column_width      = isset($atts['width']) ? $atts['width'] : 12;
		$view_all_url 	= isset($atts['url']) ? $atts['url'] : '';
        $ids = isset($atts['authors']) ? $atts['authors'] : '';
        $orderby = isset($atts['orderby']) ? $atts['orderby'] : 'display_name';
        
        $args = array(
                    'orderby' => $orderby,
                    'order' => 'ASC',
                    'number' => $count,
                    'role'  => 'author');
                    
        if($ids != ''){
            $args['include'] = explode(',', $ids);
        }

        $allUsers = get_users($args);
?>
    <div class="c_authors authors-listing-content">
        <div class="vc_row wpb_row vc_row-fluid">
        <?php
        $i = 0;
        //$column_width = apply_filters('videopro-author-listing-columns', 4);
        if(in_array($column_width, array(1,2,3,4,6,12))){
            
        } else {
            $column_width = 12; // default 
        }
        
        $columns = 12 / $column_width;
        
        foreach($allUsers as $user)
        {
            $name = $user->display_name;
            if($name == ''){
                $name = $user->user_nicename;
            }
            if($name != ''){
                $i++;
                
                
                
                $count = count_user_posts($user->ID);
            ?>
            <div class="wpb_column vc_column_container vc_col-sm-<?php echo esc_attr($column_width);?>">
                <div class="vc_column-inner "><div class="wpb_wrapper">
                    <div class="user with-name">
                        <div class="user-data">
                            <a href="<?php echo get_author_posts_url( $user->ID ); ?>" class="thumbnail" title="<?php echo esc_attr($name); ?>">
                                <span class="avatar" title="<?php echo esc_html($name); ?>"><?php echo get_avatar( $user->user_email, '60' ); ?></span>
                            </a>
                            <h3 class="author-name name data"><a href="<?php echo get_author_posts_url( $user->ID ); ?>" class="" title="<?php echo esc_attr($name); ?>"><?php echo esc_html($name); ?><?php do_action('videopro_after_title', $user->ID, 'author' );?></a></h3>
                            <span class="posts_count data"><?php echo $count < 2 ? sprintf(__('%d post','videopro'), $count) : sprintf(__('%d posts','videopro'), $count);?></span>
                            <span class="description data"><?php echo get_user_meta($user->ID, 'description', true); ?><br><br></span>
                            <ul class="social-listing data list-inline">
                          
                              <?php
                              if($email = get_the_author_meta('author_email',$user->ID) && ot_get_option('author_page_email_contact','on') == 'on'){ ?>
                                  <li class="email"><a rel="nofollow" href="mailto:<?php echo esc_attr($email); ?>" title="<?php esc_html_e('Email', 'videopro');?>"><i class="fa fa-envelope-o"></i></a></li>
                              <?php }
                              
                              if(ot_get_option('author_page_social_accounts','on') == 'on'){
                                  if($facebook = get_the_author_meta('facebook',$user->ID)){ ?>
                                      <li class="facebook"><a rel="nofollow" href="<?php echo esc_url($facebook); ?>" title="<?php esc_html_e('Facebook', 'videopro');?>"><i class="fa fa-facebook"></i></a></li>
                                  <?php }
                                  if($twitter = get_the_author_meta('twitter',$user->ID)){ ?>
                                      <li class="twitter"><a rel="nofollow" href="<?php echo esc_url($twitter); ?>" title="<?php esc_html_e('Twitter', 'videopro');?>"><i class="fa fa-twitter"></i></a></li>
                                  <?php }
                                  if($linkedin = get_the_author_meta('linkedin',$user->ID)){ ?>
                                      <li class="linkedin"><a rel="nofollow" href="<?php echo esc_url($linkedin); ?>" title="<?php esc_html_e('Linkedin', 'videopro');?>"><i class="fa fa-linkedin"></i></a></li>
                                  <?php }
                                  if($tumblr = get_the_author_meta('tumblr',$user->ID)){ ?>
                                      <li class="tumblr"><a rel="nofollow" href="<?php echo esc_url($tumblr); ?>" title="<?php esc_html_e('Tumblr', 'videopro');?>"><i class="fa fa-tumblr"></i></a></li>
                                  <?php }
                                  if($google = get_the_author_meta('google',$user->ID)){ ?>
                                     <li class="google-plus"> <a rel="nofollow" href="<?php echo esc_url($google); ?>" title="<?php esc_html_e('Google Plus', 'videopro');?>"><i class="fa fa-google-plus"></i></a></li>
                                  <?php }
                                  if($pinterest = get_the_author_meta('pinterest',$user->ID)){ ?>
                                     <li class="pinterest"> <a rel="nofollow" href="<?php echo esc_url($pinterest); ?>" title="<?php esc_html_e('Pinterest', 'videopro');?>"><i class="fa fa-pinterest"></i></a></li>
                                  <?php }
                                  
                                  if($custom_acc = get_the_author_meta('cactus_account',$user->ID)){
                                      foreach($custom_acc as $acc){
                                          if($acc['icon'] || $acc['url']){
                                      ?>
                                      <li class="cactus_account custom-account-<?php echo sanitize_title(@$acc['title']);?>"><a rel="nofollow" href="<?php echo esc_attr(@$acc['url']); ?>" title="<?php echo esc_attr(@$acc['title']);?>"><i class="fa <?php echo esc_attr(@$acc['icon']);?>"></i></a></li>
                                  <?php 	}
                                      }
                                  }
                              }
                              ?>
                          </ul>
                          <?php if($user->user_url != ''){?>
                          <span class="web data"><a href="<?php echo $user->user_url; ?>" target="_blank"><?php echo $user->user_url; ?></a></span>
                          <?php }?>
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
        }?>
        </div>
        <?php if($view_all_url != ''){?>
        <p class="viewall"><a href="<?php echo esc_url($view_all_url);?>" class="btn btn-default ct-gradient bt-action metadata-font font-size-1"><?php echo esc_html__('See All Authors...', 'videopro');?></a></p>
        <?php
        }
    ?>
    </div><!-- end .c_authors /-->
    <?php
    }
}

$shortcode_authors = new CactusShortcodeAuthors();

add_action( 'after_setup_theme', 'videopro_reg_ct_authors' );
function videopro_reg_ct_authors(){
    if(function_exists('vc_map')){
    vc_map( 	array(
			   "name" => esc_html__("VideoPro Authors",'videopro'),
			   "base" => "c_authors",
			   "class" => "",
			   "icon" => "icon-authors",
			   "controls" => "full",
			   "category" => esc_html__('VideoPro', 'videopro'),
			   "params" => 	array(
					array(
					  "type" => "textfield",
					  "heading" => esc_html__("Number of Authors", "videopro"),
					  "param_name" => "count",
					  "value" => "5",
					  "description" => "Number of authors to return. If leave empty, this shortcode only lists 5 authors",
					),
					array(
					  "type" => "textfield",
					  "heading" => esc_html__("View All URL", "videopro"),
					  "param_name" => "url",
					  "value" => "",
					  "description" => esc_html__("An URL to View All link", "videopro"),
					),
					array(
					   "type" => "dropdown",
					   "class" => "",
					   "heading" => esc_html__("Column Width", 'videopro'),
					   "param_name" => "width",
					   "value" => array(
                                '12/12' => 12,
                                '6/12' => 6,
                                '4/12' => 4,
                                '3/12' => 3,
                                '2/12' => 2,
                                '1/12' => 1
                            ),
					   "description" => esc_html__('Choose column width', 'videopro'),
					),
					array(
					   "type" => "textfield",
					   "class" => "",
					   "heading" => esc_html__("Author IDs", 'videopro'),
					   "param_name" => "authors",
					   "value" => '',
					   "description" => esc_html__('Enter list of Author IDs (separated by a comma) if you want to list only those authors', 'videopro'),
					)
				)
			));
    }
}
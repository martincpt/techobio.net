<?php

add_action('videopro_before_search_results', 'asf_hook_videopro_before_search_results', 11, 2);
function asf_hook_videopro_before_search_results($search_query, $filter_args){
	$enable_switcher_toolbar = function_exists('ot_get_option') ? ot_get_option('enable_switcher_toolbar', 'on') : 'off';
    if($enable_switcher_toolbar != 'off'){
		global $wp_query;
		
		// copy query to query all posts
		$args = $wp_query->query_vars;

		$args['posts_per_page'] = -1;
		$args['nopaging'] = true;
		
		$is_search_video = isset($filter_args['video_only']) ? $filter_args['video_only'] : 0;
		if(!$is_search_video){
			// if theme does not set this setting, check if it is passed on URL
			if(isset($_GET['v']) && $_GET['v'] == 1){
				$is_search_video = 1;
			} else {
				// or it is set in Plugin Settings
				$is_search_video = get_option('asf-search-video-only', 0);
			}
		}	
	?>

		<script type="text/javascript">
			asf.search = <?php echo json_encode($args);?>;
			asf.video_only = <?php echo ($is_search_video ? 1 : 0);?>;
			asf.cat = '<?php echo (isset($_GET['cat']) ? esc_js($_GET['cat']) : '');?>';
			asf.tags = '<?php echo (isset($_GET['tags']) ? esc_js($_GET['tags']) : '');?>';
			asf.sort = '<?php echo (isset($_GET['sort']) ? esc_js($_GET['sort']) : '');?>';
			asf.order = '<?php echo (isset($_GET['order']) ? esc_js($_GET['order']) : '');?>';
			asf.orderby = '<?php echo (isset($_GET['orderby']) ? esc_js($_GET['orderby']) : '');?>';
			asf.length = <?php echo (isset($_GET['length']) ? intval($_GET['length']) : 0);?>;
		</script>
		<?php 
			$urlParams = array();
			if(isset($_GET['cat'])){$urlParams['cat']=esc_js($_GET['cat']);}
			if(isset($_GET['orderby'])){$urlParams['orderby']=esc_js($_GET['orderby']);}
			if(isset($_GET['order'])){$urlParams['order']=esc_js($_GET['order']);}
			if(isset($_GET['length'])){$urlParams['length']=intval($_GET['length']);}else{$urlParams['length']=0;}
			if(isset($_GET['tags'])){$urlParams['tags']=esc_js($_GET['tags']);}
			$urlSearch = add_query_arg( 
				$urlParams, 
				get_search_link() 
			);
			$search_layout = ot_get_option('search_layout'); 
		?>
		<div id="asf-next-page"></div>
		<div id="filter-wrapper" class="asf-loading hidden-filter" data-search-url="<?php echo esc_url($urlSearch);?>">
		
			<div class="category-tools">
				<div id="asf-open-filters" class="view-sortby metadata-font font-size-1 ct-gradient">
					<?php echo esc_html__('FILTER', 'videopro')?> <i class="fa fa-angle-down"></i>
				</div>
				
				<?php
				if(function_exists('videopro_switcher_toolbar')){
					videopro_switcher_toolbar($search_layout);
				} else {?>
			
				<div class="view-mode">
					<div class="view-mode-switch ct-gradient">
						<div data-style="" class="view-mode-style-1 <?php if($search_layout=='layout_1' || $search_layout==''){?>active<?php }?>"><img src="<?php echo plugins_url('images/2X-layout1.png', __FILE__);?>" alt=""></div>
						<div data-style="style-2" class="view-mode-style-2 <?php if($search_layout=='layout_3'){?>active<?php }?>"><img src="<?php echo plugins_url('images/2X-layout2.png', __FILE__);?>" alt=""></div>
						<div data-style="style-3" class="view-mode-style-3 <?php if($search_layout=='layout_2'){?>active<?php }?>"><img src="<?php echo plugins_url('images/2X-layout3.png', __FILE__);?>" alt=""></div>
					</div>
				</div>
				<?php }?>
			</div>
			
			<div id="asf-search-filters"></div>
		
		</div>
	<?php
	}
}

/**
 * rewrite search filter form in Video Pro
 */
function asf_filter_videopro_default_search_form($html, $search_query){
	?>
	<div class="search-form-listing">                                    	 
		<form action="<?php echo esc_url(home_url());?>" method="get">
			<input type="text" placeholder="<?php echo esc_html_e('Search...','videopro');?>" name="s" value="<?php echo esc_attr($search_query);?>">
			<input type="submit" value="<?php echo esc_html_e('SEARCH','videopro');?>" class="padding-small">
		</form>
	</div>
<?php
}
add_filter('videopro_default_search_form', 'asf_filter_videopro_default_search_form', 10, 2);
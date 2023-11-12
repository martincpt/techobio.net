<form role="search" class="advance-search-form" onsubmit="if(jQuery('.ss',jQuery(this)).val() == '<?php echo esc_attr($default_text);?>' || jQuery('.ss',jQuery(this)).val() == '') return false;" method="get" id="form-ss" action="<?php echo esc_url(home_url( '/' )); ?>">
	<div>		
		<?php if($label != ''){?>
		<span class="screen-reader-text" for="s"><?php echo esc_html($label);?></span>
		<?php }?>
		<span class="searchtext">
		<input type="text" class="ss" autocomplete="off" value="<?php echo esc_attr($search_word);?>" onfocus="if(this.value == '<?php echo esc_attr($default_text);?>') this.value = '';" onblur="if(this.value == '') this.value='<?php echo esc_attr($default_text);?>'" name="s" autocomplete="off" />
		<?php if($suggestion){?>
		<span class="suggestion">
		</span>
		<?php }?>
			<?php if($button_text != ''){?>
				<input type="submit" id="ss_submit" value="<?php echo esc_attr($button_text);?>" />
			<?php }?>
		</span>
		<?php if($show_categories){ ?>
		<span class="lookin-cleaner"><!-- --></span>
		<span class="lookin">
		<label for="cat"><?php echo esc_html__('Look in: ','videopro');?><select id="s-cat" name="cat">
			<option value=""><?php echo " ".esc_html__('All categories','videopro')." ";?></option>
			<?php
				
				if(!is_array($select_categories) || (count($select_categories) == 0)){
					$select_categories = get_terms(array('category'));// get all categories
					foreach($select_categories as $cat){
						$cat_id = $cat->term_id;
						
						$taxonomy = $cat->name;
						if($taxonomy != 'Uncategorized'){
						?>
						<option value="<?php echo esc_attr($cat_id);?>" <?php if($cat_id == $search_cat) echo "selected='selected'";?>>  <?php echo esc_html($taxonomy);?> </option>
						<?php
						}
					}
				} else {
					foreach($select_categories as $cat){
						$cat_id = $cat;
						
						if(is_array($cat)){
							//print_r($cat);
							$cat_id = implode($cat);
						}
						
						
						$taxonomy = get_term_by('id',$cat_id,'category');
						
						
						?>
						<option value="<?php echo esc_attr($cat_id);?>" <?php if($cat_id == $search_cat) echo "selected='selected'";?>> <?php echo esc_html($taxonomy->name);?> </option>
						<?php
					}
				}

				?>
		</select>
		</label>
		</span>
		<?php } else {
		if(is_array($select_categories) && (count($select_categories) > 0)){
			?>
			<input type="hidden" name="cat" value="<?php echo esc_attr($post_type);
			foreach($select_categories as $cat){
				echo esc_attr($cat.",");
			}
			?>" />			
				
				<?php
			}
		}
		?>
		<?php 
					// show ordering
				?>
				<span class="orderby-cleaner"><!-- --></span>
				<span class="orderby">
					<label><?php echo esc_html__('Order by','videopro');?>
					<select name="orderby">
						<option value="post_title"> <?php echo esc_html__('Alphabetic','videopro');?> </option>
						<option value="post_date"> <?php echo esc_html__('Updated date','videopro');?> </option>
					</select>
					</label>
				</span>
				<span class="order-cleaner"><!-- --></span>
				<span class="order">
					<label><?php echo esc_html__('Order','videopro');?>
					<select name="order">
						<option value="asc"> <?php echo esc_html__('Asc','videopro');?> </option>
						<option value="desc"> <?php echo esc_html__('Desc','videopro');?> </option>
					</select>
					</label>
				</span>
				<?php if($show_tag_filter){
					if($search_cat == '') $search_cat = 0 ; $tags = asf_get_all_tags_in_category($search_cat); if(count($tags > 0)) {
					?>
					<span class="search_tags">
						<a href="javascript:void(0)" <?php if(isset($_GET['tag']) && $_GET['tag'] == '') echo 'class="filtered"';?> onclick="jQuery('#ss_tag_name').val('');jQuery('#form-ss').submit()"><?php echo esc_html__('Any tag','videopro');?></a>
					<?php
						foreach($tags as $tag){
					?>
						<a href="javascript:void(0)" <?php if(isset($_GET['tag']) && $_GET['tag'] == $tag->tag_slug) echo 'class="filtered"';?> onclick="jQuery('#ss_tag_name').val('<?php echo esc_attr($tag->tag_slug);?>');jQuery('#form-ss').submit()" data-slug='<?php echo esc_attr($tag->tag_slug);?>'><?php echo esc_attr($tag->tag_name);?></a>
					<?php
						}
					?>
					</span>
					<?php
					}
				}?>
				<input type="hidden" name="tag" id="ss_tag_name" value="<?php echo esc_attr(isset($_GET['tag'])?$_GET['tag']:'');?>"/>
		<?php if(is_array($search_for)){
			foreach($search_for as $post_type){
		?>
				<input type="hidden" name="post_type[]" value="<?php echo esc_attr($post_type);?>" />
		<?php }
		}
		$query = new WP_Query(array('s'=>$s,'posts_per_page'=>20));
	$html = '';
	if($query->have_posts()){
		while($query->have_posts()){
			$query->next_post();
			$query->post->post_title;
		}
	}
	
	wp_reset_postdata();
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				$('html, body').animate({
					 scrollTop: $("#form-ss").offset().top
				 }, 500);
				 <?php if($highlight_results){?>
					highlight_searchquery('<?php echo esc_js($s); ?>');
				 <?php }?>
			});
		</script>
	</div>
</form>
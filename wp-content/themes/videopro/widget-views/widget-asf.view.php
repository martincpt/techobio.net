<div class="cactus-main-menu cactus-open-search-mobile navigation-font">
    <ul>
      <li><a href="javascript:;"><i class="fas fa-search"></i></a></li>
    </ul>
</div>
                            <form role="search" onsubmit="if(jQuery('.ss',jQuery(this)).val() == '<?php echo esc_attr($default_text);?>' || jQuery('.ss',jQuery(this)).val() == '') return false;" method="get" id="searchform" action="<?php echo esc_url(home_url( '/' )); ?>">
	<div>
		<?php if($label != ''){?>
		<label class="screen-reader-text" for="s"><?php echo esc_attr($label);?></label>
		<?php }?>
		
		<?php if($show_categories){?>
            <span class="lookin">
            <label class="screen-reader-text lookin" for="cat"><?php echo esc_html__('Look in: ','videopro');?></label>
            <select id="s-cat" name="cat">
                <option value=""><?php echo esc_html__('All categories','videopro');?></option>
                <?php
                    if(!is_array($select_categories) || (count($select_categories) == 0)){
                        $select_categories = get_terms(array('category'));// get all categories
                        
                        foreach($select_categories as $cat){
                            $cat_id = $cat->term_id;
                            
                            $taxonomy = $cat->name;
                            
                            if($taxonomy != 'Uncategorized'){
                            ?>
                            <option value="<?php echo esc_attr($cat_id);?>" <?php if($cat_id == $search_cat) echo "selected='selected'";?>><?php echo esc_html($taxonomy);?></option>
                            <?php
                            }
                        }
                    } else {
                        foreach($select_categories as $cat){
                            $cat_id = $cat;
                            
                            if(is_array($cat)){
                                $cat_id = implode($cat);
                            }
                            
                                $taxonomy = get_term_by('id',$cat_id,'category');
                            
                            
                            ?>
                            <option value="<?php echo esc_attr($cat_id);?>" <?php if($cat_id == $search_cat) echo "selected='selected'";?>><?php echo esc_html($taxonomy->name);?></option>
                            <?php
                        }
                    }
                    ?>
            </select>
            </span>
		<?php } else {
			if(is_array($select_categories) && (count($select_categories) > 0)){
				?>
				<input type="hidden" name="cat" value="<?php echo esc_attr($post_type);
				foreach($select_categories as $cat){
					echo esc_attr($cat).",";
				}
				?>" />		
					
					<?php
				}
				}?>
			<?php if(is_array($search_for)){
				foreach($search_for as $post_type){
			?>
				<input type="hidden" name="post_type[]" value="<?php echo esc_attr($post_type);?>" />
			<?php } 
		}?>
        
        <span class="searchtext">
            <input type="text" value="<?php echo esc_attr($search_word);?>" onfocus="if(this.value == '<?php echo esc_attr($default_text);?>') this.value = '';" onblur="if(this.value == '') this.value='<?php echo esc_attr($default_text);?>'" name="s" class="ss"  autocomplete="off" placeholder="<?php echo esc_attr__('Enter Keyword', 'videopro')?>"/>
            <?php if($suggestion){?>
            <span class="suggestion"><!-- --></span>
            <?php }?>
            <i class="fas fa-search" aria-hidden="true"></i>
		</span>
		
		<?php 
		if($search_video_only){?>
			<input type="hidden" name="video_only" value="1" />
		<?php
		}
		?>
		<?php if($button_text != ''){?>
		<input type="submit" id="searchsubmit" value="<?php echo esc_attr($button_text);?>" />
		<?php }?>
	</div>
</form>
<?php 	
echo $after_widget;
<?php
/* Category custom field */
add_action( 'category_add_form_fields', 'videopro_extra_category_fields', 10 );
add_action ( 'edit_category_form_fields', 'videopro_extra_category_fields');
function videopro_extra_category_fields( $tag ) {    //check for existing featured ID
    $t_id 					= isset($tag->term_id) ? $tag->term_id : '';
    $cat_sidebar 			= get_option( "cat_sidebar_$t_id")?get_option( "cat_sidebar_$t_id"):'';
	$cat_layout 			= get_option( "cat_layout_$t_id")?get_option( "cat_layout_$t_id"):'';
	$cat_icon 			    = get_option( "cat_icon_$t_id")?get_option( "cat_icon_$t_id"):'';
    $cat_item_name          = get_option( "cat_item_name_$t_id") ? get_option( "cat_item_name_$t_id") : '';
	$cat_small_thumb          = get_option( "cat_small_thumb_$t_id") ? get_option( "cat_small_thumb_$t_id") : '';
	
?>
    <tr class="form-field">
		<th scope="row" valign="top">
			<label for="cat-sidebar"><?php esc_html_e('Sidebar','videopro'); ?></label>
		</th>
		<td>
            <select name="cat-sidebar" id="cat-sidebar">
                <option value="" <?php echo esc_attr(!$cat_sidebar?'selected="selected"':'') ?>><?php esc_html_e('Default','videopro') ?></option>
				<option value="right" <?php echo esc_attr($cat_sidebar=='right'?'selected="selected"':'') ?>><?php esc_html_e('Right','videopro') ?></option>
				<option value="left" <?php echo esc_attr($cat_sidebar=='left'?'selected="selected"':'') ?>><?php esc_html_e('Left','videopro') ?></option>
                <option value="both" <?php echo esc_attr($cat_sidebar=='both'?'selected="selected"':'') ?>><?php esc_html_e('Left & Right','videopro') ?></option>
                <option value="full" <?php echo esc_attr($cat_sidebar=='full'?'selected="selected"':'') ?>><?php esc_html_e('Hidden','videopro') ?></option>
            </select>
			<p class="description"><?php esc_html_e('Choose "Default" to use setting in Theme Options > Archives > Sidebar','videopro'); ?></p>
		</td>
	</tr>
    
    <tr class="form-field">
		<th scope="row" valign="top">
			<label for="cat-layout"><?php esc_html_e('Layout','videopro'); ?></label>
		</th>
		<td>
            <select name="cat-layout" id="cat-layout">
                <option value="" <?php echo esc_attr(!$blog_layout?'selected="selected"':'') ?>><?php esc_html_e('Default','videopro') ?></option>
				<option value="layout_1" <?php echo esc_attr($cat_layout=='layout_1'?'selected="selected"':'') ?>><?php esc_html_e('One Column, Big Thumbnail','videopro') ?></option>
				<option value="layout_2" <?php echo esc_attr($cat_layout=='layout_2'?'selected="selected"':'') ?>><?php esc_html_e('One Column, Small Thumbnail','videopro') ?></option>
                <option value="layout_3" <?php echo esc_attr($cat_layout=='layout_3'?'selected="selected"':'') ?>><?php esc_html_e('Multiple Columns','videopro') ?></option>
            </select>
			<p class="description"><?php esc_html_e('Choose "Default" to use setting in Theme Options > Archives > Layout','videopro'); ?></p>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row" valign="top">
			<label for="cat-icon"><?php esc_html_e('Icon Class','videopro'); ?></label>
		</th>
		<td>
        	<input type="text" name="cat-icon" id="cat-icon" value="<?php echo esc_attr($cat_icon) ?>" />
			<p class="description"><?php esc_html_e('Enter CSS Class for this category\'s icon','videopro'); ?></p>
		</td>
	</tr>
    <tr class="form-field">
		<th scope="row" valign="top">
			<label for="cat-icon"><?php esc_html_e('Name for Item','videopro'); ?></label>
		</th>
		<td>
        	<input type="text" name="cat-item-name" id="cat-item-name" value="<?php echo esc_attr($cat_item_name) ?>" />
			<p class="description"><?php esc_html_e('By default, each item in category is called "video". You can change this word for each category','videopro'); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row" valign="top">
			<label for="cat-icon"><?php esc_html_e('Small Thumbnail (32x32px)','videopro'); ?></label>
		</th>
		<td>
        	<input type="text" name="cat-small-thumb" id="cat-small-thumb" value="<?php echo esc_attr($cat_small_thumb) ?>" />
			<p class="description"><?php esc_html_e('Small Thumbnail URL used for Categories Widget','videopro'); ?></p>
		</td>
	</tr>
<?php
}
//save extra category extra fields hook
add_action ( 'edited_category', 'videopro_save_extra_category_fileds');
add_action( 'created_category', 'videopro_save_extra_category_fileds', 10, 2 );
function videopro_save_extra_category_fileds( $term_id ) {
    if ( isset( $_POST[sanitize_key('cat-sidebar')] ) ) {
        $cat_sidebar = esc_html($_POST['cat-sidebar']);
        update_option( "cat_sidebar_$term_id", $cat_sidebar );
    }
	if ( isset( $_POST[sanitize_key('cat-layout')] ) ) {
        $cat_layout = esc_html($_POST['cat-layout']);
        update_option( "cat_layout_$term_id", $cat_layout );
    }
	if ( isset( $_POST[sanitize_key('cat-icon')] ) ) {
        $cat_icon = esc_html($_POST['cat-icon']);
        update_option( "cat_icon_$term_id", $cat_icon );
    }
    
    if ( isset( $_POST[sanitize_key('cat-item-name')] ) ) {
        $cat_item_name = esc_html($_POST['cat-item-name']);
        update_option( "cat_item_name_$term_id", $cat_item_name );
    }
	
	if ( isset( $_POST[sanitize_key('cat-small-thumb')] ) ) {
        $cat_small_thumb = esc_html($_POST['cat-small-thumb']);
        update_option( "cat_small_thumb_$term_id", $cat_small_thumb );
    }
}
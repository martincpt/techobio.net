<?php

$membership_enabled = function_exists('ot_get_option') && ot_get_option('membership_allow_creating_channel');
        
if($membership_enabled == 'on'){
    $submenu = add_submenu_page('ct_video_settings', esc_html__('Membership Settings', 'videopro'), esc_html__('Membership Settings', 'videopro'),'administrator', 'videopro_video_membership', 'videopro_video_membership_optionpage');
}

/**
 * save video-membership options
 */
function videopro_video_membership_save_options(){
   $channel_limit = intval($_POST['membership-channel-limit']);
   $channel_limited_memberships = isset($_POST['membership-channels-limited-roles']) ? $_POST['membership-channels-limited-roles'] : array();
   $channel_unlimited_memberships = isset($_POST['membership-channels-unlimited-roles']) ? $_POST['membership-channels-unlimited-roles'] : array();
   
   $video_limit = intval($_POST['membership-video-limit']);
   $video_limited_memberships = isset($_POST['membership-videos-limited-roles']) ? $_POST['membership-videos-limited-roles'] : array();
   $video_unlimited_memberships = isset($_POST['membership-videos-unlimited-roles']) ? $_POST['membership-videos-unlimited-roles'] : array();
   
   $playlist_limit = intval($_POST['membership-playlist-limit']);
   $playlist_limited_memberships = isset($_POST['membership-playlists-limited-roles']) ? $_POST['membership-playlists-limited-roles'] : array();
   $playlist_unlimited_memberships = isset($_POST['membership-playlists-unlimited-roles']) ? $_POST['membership-playlists-unlimited-roles'] : array();
   
   $video_ads_memberships = isset($_POST['membership-video-ads-roles']) ? $_POST['membership-video-ads-roles'] : array();
   
   $warning_message = wp_kses_post($_POST['membership-expired-warning-message']);
   $error_message = wp_kses_post($_POST['membership-expired-error-message']);
   $days_before_warning = intval($_POST['membership-days-before-warning']);
   $default_channel_status = $_POST['membership-default-channel-status'];
   $default_playlist_status = $_POST['membership-default-playlist-status'];
   $default_video_status = $_POST['membership-default-video-status'];
   
   $options = array(
                'channel-limit' => $channel_limit,
                'channel-limited-memberships' => $channel_limited_memberships,
                'channel-unlimited-memberships' => $channel_unlimited_memberships,
                'video-limit' => $video_limit,
                'video-limited-memberships' => $video_limited_memberships,
                'video-unlimited-memberships' => $video_unlimited_memberships,
                'video-ads-memberships' => $video_ads_memberships,
                'playlist-limit' => $playlist_limit,
                'playlist-limited-memberships' => $playlist_limited_memberships,
                'playlist-unlimited-memberships' => $playlist_unlimited_memberships,
                'days-before-warning' => $days_before_warning,
                'default-channel-status' => $default_channel_status,
                'default-playlist-status' => $default_playlist_status,
                'default-video-status' => $default_video_status
            );
    
    if($warning_message != ''){
        $options = array_merge($options, array('expired-warning' => $warning_message));
    }
    
    if($error_message != ''){
        $options = array_merge($options, array('expired-error' => $error_message));
    }
            
    update_option('videopro_video_membership', $options);
}

/**
 * build options page
 */
function videopro_video_membership_optionpage(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        videopro_video_membership_save_options();
        ?>
        <div class="updated">
            <p><?php echo esc_html__('Settings are saved', 'videopro');?></p>
        </div>
        <?php
    }
    
    $options = videopro_video_membership_get_options();  

    
    ?>
    <div id="video_membership_page" class="wrap">
        <form action="admin.php?page=videopro_video_membership" method="post" accept-charset="UTF-8" enctype="application/x-www-form-urlencoded">
            <h1><?php echo esc_html__('VideoPro Membership Settings', 'videopro');?></h1>
            <?php
            if(class_exists('MS_Model_Membership')){
                $memberships = MS_Model_Membership::get_memberships();
                ?>
                <table class="wp-list-table" cellspacing="0">
                    <tbody>
                        <!-- Create Channels roles -->
                        <tr class="section"><td colspan="2"><h3><?php echo esc_html__('Create Channels Roles', 'videopro');?></h3></td></tr>
                        <tr class="item"><td class="head"><?php echo esc_html__('Create Channels (Limited)', 'videopro');?></td><td><p><?php echo esc_html__('Number of channels allowed to create (when users are in a limited membership)', 'videopro');?></p><input type="number" name="membership-channel-limit" placeholder="0" value="<?php echo $options['channel-limit'];?>"> <span class="italic"><?php echo esc_html__('(enter 0 to disable this limited mode)', 'videopro');?></span></td></tr>
                        <tr class="item alternative"><td class="head"><?php echo esc_html__('Create Channels (Limited)', 'videopro');?></td><td>
                        <p><?php echo esc_html__('Choose Memberships of which users can create limited number of channels', 'videopro');?></p>
                        <?php
                foreach($memberships as $membership){
                    $url = admin_url('admin.php?page=membership2&step=overview&membership_id=' . $membership->id);
                    ?>
                    <label class="check"><input type="checkbox" <?php if(in_array($membership->id, isset($options['channel-limited-memberships']) ? $options['channel-limited-memberships'] : array())) echo 'checked="checked"';?> name="membership-channels-limited-roles[]" value="<?php echo $membership->id;?>"/> <?php echo $membership->name;?> - <a href="<?php echo $url;?>" target="_blank"><?php echo esc_html__('View', 'videopro');?></a></label>
                    <?php
                }?></td></tr>
                    <tr class="item "><td class="head"><?php echo esc_html__('Create Channels Unlimited', 'videopro');?></td><td>
                            <p><?php echo esc_html__('Choose Memberships of which users can create unlimited number of channels', 'videopro');?></p>
                            <?php
                    foreach($memberships as $membership){
                        $url = admin_url('admin.php?page=membership2&step=overview&membership_id=' . $membership->id);
                        ?>
                        <label class="check"><input type="checkbox" name="membership-channels-unlimited-roles[]" <?php if(in_array($membership->id, isset($options['channel-unlimited-memberships']) ? $options['channel-unlimited-memberships'] : array())) echo 'checked="checked"';?> value="<?php echo $membership->id;?>"/> <?php echo $membership->name;?> - <a href="<?php echo $url;?>" target="_blank"><?php echo esc_html__('View', 'videopro');?></a></label>
                        <?php
                    }?></td></tr>
                        <!-- /end Create Channel Roles -->
                        
                        <!-- Create Playlists roles -->
                        <tr class="section"><td colspan="2"><h3><?php echo esc_html__('Create Playlists Roles', 'videopro');?></h3></td></tr>
                        <tr class="item"><td class="head"><?php echo esc_html__('Create Playlists (Limited)', 'videopro');?></td><td><p><?php echo esc_html__('Number of playlists allowed to create (when users are in a limited membership)', 'videopro');?></p><input type="number" name="membership-playlist-limit" placeholder="0" value="<?php echo $options['playlist-limit'];?>"> <span class="italic"><?php echo esc_html__('(enter 0 to disable this limited mode)', 'videopro');?></span></td></tr>
                        <tr class="item alternative"><td class="head"><?php echo esc_html__('Create Playlists (Limited)', 'videopro');?></td><td>
                        <p><?php echo esc_html__('Choose Memberships of which users can create limited number of playlists', 'videopro');?></p>
                        <?php
                foreach($memberships as $membership){
                    $url = admin_url('admin.php?page=membership2&step=overview&membership_id=' . $membership->id);
                    ?>
                    <label class="check"><input type="checkbox" <?php if(in_array($membership->id, isset($options['playlist-limited-memberships']) ? $options['playlist-limited-memberships'] : array())) echo 'checked="checked"';?> name="membership-playlists-limited-roles[]" value="<?php echo $membership->id;?>"/> <?php echo $membership->name;?> - <a href="<?php echo $url;?>" target="_blank"><?php echo esc_html__('View', 'videopro');?></a></label>
                    <?php
                }?></td></tr>
                    <tr class="item "><td class="head"><?php echo esc_html__('Create Playlists Unlimited', 'videopro');?></td><td>
                            <p><?php echo esc_html__('Choose Memberships of which users can create unlimited number of playlists', 'videopro');?></p>
                            <?php
                    foreach($memberships as $membership){
                        $url = admin_url('admin.php?page=membership2&step=overview&membership_id=' . $membership->id);
                        ?>
                        <label class="check"><input type="checkbox" name="membership-playlists-unlimited-roles[]" <?php if(in_array($membership->id, isset($options['playlist-unlimited-memberships']) ? $options['playlist-unlimited-memberships'] : array())) echo 'checked="checked"';?> value="<?php echo $membership->id;?>"/> <?php echo $membership->name;?> - <a href="<?php echo $url;?>" target="_blank"><?php echo esc_html__('View', 'videopro');?></a></label>
                        <?php
                    }?></td></tr>
                        <!-- /end Create Playlists Roles -->
                        
                        <!-- Upload Videos Roles -->
                        <tr class="section"><td colspan="2"><h3><?php echo esc_html__('Upload Videos Roles', 'videopro');?></h3></td></tr>
                        <tr class="item alternative"><td class="head"><?php echo esc_html__('Upload Videos (Limited)', 'videopro');?></td><td><p><?php echo esc_html__('Number of videos allowed to upload (when users are in a limited membership)', 'videopro');?></p><input type="number" name="membership-video-limit" placeholder="0" value="<?php echo $options['video-limit'];?>"> <span class="italic"><?php echo esc_html__('(enter 0 to disable this limited mode)', 'videopro');?></span></td></tr>
                        <tr class=""><td class="head"><?php echo esc_html__('Upload Videos (Limited)', 'videopro');?></td><td><p><?php echo esc_html__('Choose Memberships of which users can upload limited number of videos', 'videopro');?></p>
                            <?php
                    foreach($memberships as $membership){
                        $url = admin_url('admin.php?page=membership2&step=overview&membership_id=' . $membership->id);
                        ?>
                        <label class="check"><input type="checkbox" name="membership-videos-limited-roles[]" <?php if(in_array($membership->id, isset($options['video-limited-memberships']) ? $options['video-limited-memberships'] : array())) echo 'checked="checked"';?> value="<?php echo $membership->id;?>" /> <?php echo $membership->name;?> - <a href="<?php echo $url;?>" target="_blank"><?php echo esc_html__('View', 'videopro');?></a></label>
                        <?php
                    }?></td></tr>
                        <tr class="alternative"><td class="head"><?php echo esc_html__('Upload Videos Unlimited', 'videopro');?></td><td><p><?php echo esc_html__('Choose Memberships of which users can upload unlimited number of videos', 'videopro');?></p>
                            <?php
                    foreach($memberships as $membership){
                        $url = admin_url('admin.php?page=membership2&step=overview&membership_id=' . $membership->id);
                        ?>
                        <label class="check"><input type="checkbox" name="membership-videos-unlimited-roles[]" <?php if(in_array($membership->id, isset($options['video-unlimited-memberships']) ? $options['video-unlimited-memberships'] : array())) echo 'checked="checked"';?> value="<?php echo $membership->id;?>"/> <?php echo $membership->name;?> - <a href="<?php echo $url;?>" target="_blank"><?php echo esc_html__('View', 'videopro');?></a></label>
                        <?php
                    }?></td></tr>
                        <!-- /end Upload Videos Roles -->
                        <tr class="section"><td colspan="2"><h3><?php echo esc_html__('Video Ads', 'videopro');?></h3></td></tr>
                        <tr class="alternative"><td class="head"><?php echo esc_html__('Disable Video Ads', 'videopro');?></td><td><p><?php echo esc_html__('Choose Memberships of which users will not see video ads', 'videopro');?></p>
                            <?php
                    foreach($memberships as $membership){
                        $url = admin_url('admin.php?page=membership2&step=overview&membership_id=' . $membership->id);
                        ?>
                        <label class="check"><input type="checkbox" name="membership-video-ads-roles[]" <?php if(in_array($membership->id, isset($options['video-ads-memberships']) ? $options['video-ads-memberships'] : array())) echo 'checked="checked"';?> value="<?php echo $membership->id;?>"/> <?php echo $membership->name;?> - <a href="<?php echo $url;?>" target="_blank"><?php echo esc_html__('View', 'videopro');?></a></label>
                        <?php
                    }?></td></tr>
                        <tr class="section"><td colspan="2"><h3><?php echo esc_html__('Other Settings', 'videopro');?></h3></td></tr>
                        <tr><td class="head"><?php esc_html_e('Default Channel Status', 'videopro');?></td><td><select name="membership-default-channel-status"><option value=
"publish" <?php if($options['default-channel-status'] == 'publish') echo 'selected="selected"';?>><?php echo esc_html__('Publish', 'videopro');?></option><option value=
"pending" <?php if($options['default-channel-status'] == 'pending') echo 'selected="selected"';?>><?php echo esc_html__('Pending', 'videopro');?></option></select><p><?php esc_html_e('Default status for channels created by members', 'videopro');?></p></td></tr>
<tr><td class="head"><?php esc_html_e('Default Playlist Status', 'videopro');?></td><td><select name="membership-default-playlist-status"><option value=
"publish" <?php if($options['default-channel-status'] == 'publish') echo 'selected="selected"';?>><?php echo esc_html__('Publish', 'videopro');?></option><option value=
"pending" <?php if($options['default-channel-status'] == 'pending') echo 'selected="selected"';?>><?php echo esc_html__('Pending', 'videopro');?></option></select><p><?php esc_html_e('Default status for playlists created by members', 'videopro');?></p></td></tr>
<tr><td class="head"><?php esc_html_e('Default Video Status', 'videopro');?></td><td><select name="membership-default-video-status"><option value=
"publish" <?php if($options['default-channel-status'] == 'publish') echo 'selected="selected"';?>><?php echo esc_html__('Publish', 'videopro');?></option><option value=
"pending" <?php if($options['default-channel-status'] == 'pending') echo 'selected="selected"';?>><?php echo esc_html__('Pending', 'videopro');?></option></select><p><?php esc_html_e('Default status for videos uploaded by members', 'videopro');?></p></td></tr>
                        <tr><td class="head"><?php esc_html_e('Number of days before Expired Date to show warning', 'videopro');?></td><td><input type="number" name="membership-days-before-warning" value="<?php echo $options['days-before-warning'];?>"/></td></tr>
                        <tr><td class="head"><?php esc_html_e('Warning Message', 'videopro');?></td><td><textarea cols="100" rows="5" name="membership-expired-warning-message"><?php echo $options['expired-warning'];?></textarea><p><?php echo wp_kses(__('Use this text to add Membership Name Tag: <b>{name_tag}</b> and this text to add number of days before expired date: <b>{days}</b>. HTML tags are allowed to use in this message. For example: <br/><br/><i>Your subscription: {name_tag} is about to expired in <strong>{days}</strong> days</i><br/><br/>You can also use this shortcode to display the message anywhere: <b>[vp_member_expired_message]</b>' ,'videopro'), array('b' => array(), 'br' => array(), 'i' => array()));?></p></td></tr>
                        <tr><td class="head"><?php esc_html_e('Expired Error Message', 'videopro');?></td><td><textarea cols="100" rows="5" name="membership-expired-error-message"><?php echo $options['expired-error'];?></textarea><p><?php echo wp_kses(__('Use this text to add Membership Name Tag: <b>{name_tag}</b> and this text to add number of days before expired date: <b>{days}</b>. HTML tags are allowed to use in this message. For example: <br/><br/><i>Your subscription: {name_tag} is expired</i><br/><br/>You can also use this shortcode to display the message anywhere: <b>[vp_member_expired_message]</b>' ,'videopro'), array('b' => array(), 'br' => array(), 'i' => array()));?></p></td></tr>
                        
                    </tbody>
                </table>
                <p><input type="submit" class="button button-primary right" value="<?php echo esc_html__('Save', 'videopro');?>"></p>
                <?php
            } else {
                ?>
                <p><?php echo wp_kses(__('<a href="https://wordpress.org/plugins/membership/" target="_blank">Membership plugin</a> is required to enabled this feature</p>', 'videopro'), array('a'=>array('href'=>array(), 'target'=>array())));?>
                <?php
            }
            ?>
        </form>
    </div>
    <?php
}
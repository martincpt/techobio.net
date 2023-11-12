<?php
/*
If you would like to edit this file, copy it to your current theme's directory and edit it there.
Theme My Login will always look in your theme's directory first, before using this default template.
*/
?>
<div class="main-content tml-profile-page row">
    <div class="col-md-3">
        <div class="col-inner">
            <div class="tml-profile-sidebar">
                <div class="menu">
                    <h3><?php echo esc_html__('Personal Options', 'videopro');?></h3>
                    <ul class="menu-items">
                        <li><a href="#tml-section-name" class="item active"><?php echo esc_html__('Name', 'videopro');?></a></li>
                        <li><a href="#tml-section-contact" class="item"><?php echo esc_html__('Contact Info', 'videopro');?></a></li>
                        <li><a href="#tml-section-about" class="item"><?php echo esc_html__('About Yourself', 'videopro');?></a></li>
                        <li><a href="#tml-section-account" class="item"><?php echo esc_html__('Account Management', 'videopro');?></a></li>
                    </ul>
                </div>
                <div class="buttons">
                    <?php
                    if(videopro_current_user_can('channel.create')){
                      ?>
                    <a href="#" data-toggle="modal" data-target="#videopro_user_create_channel_popup" class="btn-user-create-channel btn btn-default ct-gradient bt-action metadata-font font-size-1 elms-right"><?php echo esc_html__('Create Channel','videopro');?></a>
                    <?php
                    } else {
                        // show limit message if any
                        do_action('videopro_membership_check_limited_action', get_current_user_id(), 'channel.create');
                    }
                    ?> 
                </div>
                <!-- extra content here -->
                <?php do_action('videopro-tml-main-sidebar');?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="col-inner">
            <div class="tml tml-profile" id="theme-my-login<?php $template->the_instance(); ?>">
                <?php do_action('videopro_tml_profile_page_before_content');?>
                <form id="your-profile" action="<?php $template->the_action_url( 'profile', 'login_post' ); ?>" method="post">
                    <div id="tml-section-name" class="tml-section">
                        <div id="tml-profile-links" class="tml-user-panel">
                            <?php if ( $template->options['show_gravatar'] ) : ?>
                            <div class="tml-user-avatar"><?php $template->the_user_avatar(80); ?></div>
                            <?php endif; ?>

                            <?php $template->the_user_links(); ?>

                            <?php do_action( 'tml_user_panel' ); ?>
                        </div>
                        
                        <?php $template->the_action_template_message( 'profile' ); ?>
                        <?php $template->the_errors(); ?>
                    
                        <?php wp_nonce_field( 'update-user_' . $current_user->ID ); ?>
                        <p>
                            <input type="hidden" name="from" value="profile" />
                            <input type="hidden" name="checkuser_id" value="<?php echo esc_attr($current_user->ID); ?>" />
                        </p>
                        
                        <div class="profile-group">

                        <table class="tml-form-table">
                        <tr class="tml-user-admin-bar-front-wrap">
                            <th><label for="admin_bar_front"><?php esc_html_e( 'Toolbar', 'videopro' )?></label></th>
                            <td>
                                <label for="admin_bar_front"><input type="checkbox" name="admin_bar_front" id="admin_bar_front" value="1"<?php checked( _get_admin_bar_pref( 'front', $profileuser->ID ) ); ?> />
                                <?php esc_html_e( 'Show Toolbar when viewing site', 'videopro' ); ?></label>
                            </td>
                        </tr>
                        <?php do_action( 'personal_options', $profileuser ); ?>
                        </table>

                        <?php do_action( 'profile_personal_options', $profileuser ); ?>
                        
                        </div>
                        
                        <div class="profile-group">

                        <h3><?php esc_html_e( 'Name', 'videopro' ); ?></h3>

                        <table class="tml-form-table">
                        <tr class="tml-user-login-wrap">
                            <th><label for="user_login"><?php esc_html_e( 'Username', 'videopro' ); ?></label></th>
                            <td><input type="text" name="user_login" id="user_login" value="<?php echo esc_attr( $profileuser->user_login ); ?>" disabled="disabled" class="regular-text" /> <span class="description"><?php esc_html_e( 'Usernames cannot be changed.', 'videopro' ); ?></span></td>
                        </tr>

                        <tr class="tml-first-name-wrap">
                            <th><label for="first_name"><?php esc_html_e( 'First Name', 'videopro' ); ?></label></th>
                            <td><input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $profileuser->first_name ); ?>" class="regular-text" /></td>
                        </tr>

                        <tr class="tml-last-name-wrap">
                            <th><label for="last_name"><?php esc_html_e( 'Last Name', 'videopro' ); ?></label></th>
                            <td><input type="text" name="last_name" id="last_name" value="<?php echo esc_attr( $profileuser->last_name ); ?>" class="regular-text" /></td>
                        </tr>

                        <tr class="tml-nickname-wrap">
                            <th><label for="nickname"><?php esc_html_e( 'Nickname', 'videopro' ); ?> <span class="description"><?php esc_html_e( '(required)', 'videopro' ); ?></span></label></th>
                            <td><input type="text" name="nickname" id="nickname" value="<?php echo esc_attr( $profileuser->nickname ); ?>" class="regular-text" /></td>
                        </tr>

                        <tr class="tml-display-name-wrap">
                            <th><label for="display_name"><?php esc_html_e( 'Display name publicly as', 'videopro' ); ?></label></th>
                            <td>
                                <select name="display_name" id="display_name">
                                <?php
                                    $public_display = array();
                                    $public_display['display_nickname']  = $profileuser->nickname;
                                    $public_display['display_username']  = $profileuser->user_login;

                                    if ( ! empty( $profileuser->first_name ) )
                                        $public_display['display_firstname'] = $profileuser->first_name;

                                    if ( ! empty( $profileuser->last_name ) )
                                        $public_display['display_lastname'] = $profileuser->last_name;

                                    if ( ! empty( $profileuser->first_name ) && ! empty( $profileuser->last_name ) ) {
                                        $public_display['display_firstlast'] = $profileuser->first_name . ' ' . $profileuser->last_name;
                                        $public_display['display_lastfirst'] = $profileuser->last_name . ' ' . $profileuser->first_name;
                                    }

                                    if ( ! in_array( $profileuser->display_name, $public_display ) )// Only add this if it isn't duplicated elsewhere
                                        $public_display = array( 'display_displayname' => $profileuser->display_name ) + $public_display;

                                    $public_display = array_map( 'trim', $public_display );
                                    $public_display = array_unique( $public_display );

                                    foreach ( $public_display as $id => $item ) {
                                ?>
                                    <option <?php selected( $profileuser->display_name, $item ); ?>><?php echo esc_html($item); ?></option>
                                <?php
                                    }
                                ?>
                                </select>
                            </td>
                        </tr>
                        </table>
                        
                        </div>
                    </div>
                    <!-- end tml-section-name -->
                    <div class="tml-section hidden" id="tml-section-contact">
                    
                        <div class="profile-group">

                        <h3><?php esc_html_e( 'Contact Info', 'videopro' ); ?></h3>

                        <table class="tml-form-table">
                        <tr class="tml-user-email-wrap">
                            <th><label for="email"><?php esc_html_e( 'E-mail', 'videopro' ); ?> <span class="description"><?php esc_html_e( '(required)', 'videopro' ); ?></span></label></th>
                            <td><input type="text" name="email" id="email" value="<?php echo esc_attr( $profileuser->user_email ); ?>" class="regular-text" /></td>
                            <?php
                            $new_email = get_option( $current_user->ID . '_new_email' );
                            if ( $new_email && $new_email['newemail'] != $current_user->user_email ) : ?>
                            <div class="updated inline">
                            <p><?php
                                printf(
                                    __( 'There is a pending change of your e-mail to %1$s. <a href="%2$s">Cancel</a>', 'videopro' ),
                                    '<code>' . $new_email['newemail'] . '</code>',
                                    esc_url( self_admin_url( 'profile.php?dismiss=' . $current_user->ID . '_new_email' ) )
                            ); ?></p>
                            </div>
                            <?php endif; ?>
                        </tr>

                        <tr class="tml-user-url-wrap">
                            <th><label for="url"><?php esc_html_e( 'Website', 'videopro' ); ?></label></th>
                            <td><input type="text" name="url" id="url" value="<?php echo esc_attr( $profileuser->user_url ); ?>" class="regular-text code" /></td>
                        </tr>

                        <?php
                            foreach ( wp_get_user_contact_methods() as $name => $desc ) {
                        ?>
                        <tr class="tml-user-contact-method-<?php echo esc_attr($name); ?>-wrap">
                            <th><label for="<?php echo esc_attr($name); ?>"><?php echo apply_filters( 'user_'.$name.'_label', $desc ); ?></label></th>
                            <td><input type="text" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr( $profileuser->$name ); ?>" class="regular-text" /></td>
                        </tr>
                        <?php
                            }
                        ?>
                        </table>
                        
                        </div>
                    </div>
                    <!-- end tml-section-contact -->
                    <div class="tml-section hidden" id="tml-section-about">
                        <div class="profile-group">

                        <h3><?php _e( 'About Yourself', 'videopro' ); ?></h3>

                        <table class="tml-form-table">
                        <tr class="tml-user-description-wrap">
                            <th><label for="description"><?php _e( 'Biographical Info', 'videopro' ); ?></label></th>
                            <td><textarea name="description" id="description" rows="5" cols="30"><?php echo esc_html( $profileuser->description ); ?></textarea><br />
                            <span class="description"><?php _e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'videopro' ); ?></span></td>
                        </tr>

                        <?php
                        $show_password_fields = apply_filters( 'show_password_fields', true, $profileuser );
                        if ( $show_password_fields ) :
                        ?>
                        </table>
                        
                        </div>
                    </div>
                    <!-- end tml-section-about -->
                    <div class="tml-section hidden" id="tml-section-account">
                        <div class="profile-group">

                        <h3><?php esc_html_e( 'Account Management', 'videopro' ); ?></h3>
                        <table class="tml-form-table">
                        <tr id="password" class="user-pass1-wrap">
                            <th><label for="pass1"><?php esc_html_e( 'New Password', 'videopro' ); ?></label></th>
                            <td>
                                <input class="hidden" style="display:none !important" value=" " /><!-- #24364 workaround -->
                                <button type="button" class="button button-secondary wp-generate-pw hide-if-no-js"><?php esc_html_e( 'Generate Password', 'videopro' ); ?></button>
                                <div class="wp-pwd hide-if-js">
                                    <span class="password-input-wrapper">
                                        <input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
                                    </span>
                                    <div style="display:none" id="pass-strength-result" aria-live="polite"></div>
                                    <button type="button" class="button button-secondary wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password', 'videopro' ); ?>">
                                        <span class="dashicons dashicons-hidden"></span>
                                        <span class="text"><?php esc_html_e( 'Hide', 'videopro' ); ?></span>
                                    </button>
                                    <button type="button" class="button button-secondary wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change', 'videopro' ); ?>">
                                        <span class="text"><?php esc_html_e( 'Cancel', 'videopro' ); ?></span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr class="user-pass2-wrap hide-if-js">
                            <th scope="row"><label for="pass2"><?php esc_html_e( 'Repeat New Password', 'videopro' ); ?></label></th>
                            <td>
                            <input name="pass2" type="password" id="pass2" class="regular-text" value="" autocomplete="off" />
                            <p class="description"><?php esc_html_e( 'Type your new password again.', 'videopro' ); ?></p>
                            </td>
                        </tr>
                        <tr class="pw-weak">
                            <th><?php esc_html_e( 'Confirm Password', 'videopro' ); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="pw_weak" class="pw-checkbox" />
                                    <?php esc_html_e( 'Confirm use of weak password', 'videopro' ); ?>
                                </label>
                            </td>
                        </tr>
                        <?php endif; ?>

                        </table>

                        <?php do_action( 'show_user_profile', $profileuser ); ?>
                        
                        </div>
                    
                    </div>

                    <p class="tml-submit-wrap">
                        <input type="hidden" name="action" value="profile" />
                        <input type="hidden" name="instance" value="<?php $template->the_instance(); ?>" />
                        <input type="hidden" name="user_id" id="user_id" value="<?php echo esc_attr( $current_user->ID ); ?>" />
                        <input type="submit" class="button-primary" value="<?php esc_attr_e( 'Update Profile', 'videopro' ); ?>" name="submit" id="submit" />
                    </p>
                </form>
            </div>
        </div>
    </div>
    <div class="clearer"><!-- --></div>
</div>

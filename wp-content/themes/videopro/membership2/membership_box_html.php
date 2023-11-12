<?php 
$spl_price = get_ms_single_box_membership_price();
$spl_p = explode(" ",$spl_price);
if(isset($spl_p[1])){
	$spl_price = '<span class="currency-mb">'.$spl_p[0].'</span> '.$spl_p[1]; 
}
if($videopro_ms_box_layout == 'vertical'){
?>
<div id="ms-membership-wrapper-<?php echo get_ms_single_box_membership_id(); ?>" class="<?php echo get_ms_single_box_wrapper_classes(); ?>">
        <div class="ms-top-bar">
                <h4><span class="ms-title"><?php echo get_ms_single_box_membership_name(); ?></span></h4>
        </div>
        <div class="ms-price-details">
                <div class="ms-description"><?php echo get_ms_single_box_membership_description(); ?></div>
                <div class="ms-price price"><?php echo wp_kses($spl_price,array('span' => array('class' => array()))); ?></div>

                <?php if ( is_ms_single_box_msg() ) : ?>
                        <div class="ms-bottom-msg"><?php echo get_ms_single_box_msg(); ?></div>
                <?php endif; ?>
        </div>

        <div class="ms-bottom-bar">
                <?php
                echo get_ms_single_box_hidden_fields();

                /**
                 * It's possible to add custom fields to the signup box.
                 *
                 * @since  1.0.1.2
                 */
                do_action( 'ms_shortcode_signup_form_end', get_ms_single_box_membership_obj() );

                echo get_ms_single_box_btn();
                
                if ( is_ms_single_box_action_pay() ) {
                    echo get_ms_single_box_payment_btn();
                }
                ?>
        </div>
</div>
<?php } else {
?>
<div id="ms-membership-wrapper-<?php echo get_ms_single_box_membership_id(); ?>" class="horizontal <?php echo get_ms_single_box_wrapper_classes(); ?>">
        <div class="ms-price-details">
                <h4><span class="ms-title"><?php echo get_ms_single_box_membership_name(); ?></span></h4>
                <div class="ms-description"><?php echo get_ms_single_box_membership_description(); ?></div>
        </div>

        <div class="ms-bottom-bar">
                <div class="ms-price price"><?php echo wp_kses($spl_price,array('span' => array('class' => array()))); ?></div>
                <?php
                echo get_ms_single_box_hidden_fields();

                /**
                 * It's possible to add custom fields to the signup box.
                 *
                 * @since  1.0.1.2
                 */
                do_action( 'ms_shortcode_signup_form_end', get_ms_single_box_membership_obj() );

                echo get_ms_single_box_btn();
                ?>
                <?php if ( is_ms_single_box_msg() ) : ?>
                        <div class="ms-bottom-msg"><?php echo get_ms_single_box_msg(); ?></div>
                <?php endif; ?>
                <?php
                if ( is_ms_single_box_action_pay() ) {
                    echo get_ms_single_box_payment_btn();
                }
                ?>
        </div>
</div>
<?php
}
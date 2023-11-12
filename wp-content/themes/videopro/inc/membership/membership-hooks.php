<?php


add_filter('ms_shortcode_signup', 'videopro_ms_shortcup_signup', 10, 2);
function videopro_ms_shortcup_signup($html, $data){
    
    $settings = MS_Factory::load( 'MS_Model_Settings' );
		$member = $data['member'];
		$subscriptions = $data['subscriptions'];
		$memberships = $data['memberships'];

		ob_start();
		?>
		<div class="ms-membership-form-wrapper">
			<?php
			if ( count( $subscriptions ) > 0 ) {
                $data['box_layout'] = 'horizontal';

				foreach ( $subscriptions as $subscription ) {
					$msg = $subscription->get_status_description();

					$membership = MS_Factory::load(
						'MS_Model_Membership',
						$subscription->membership_id
					);
                                        
                                        $membership->_move_from = $member->cancel_ids_on_subscription(
                                                $membership->id
                                        );
                                        
					switch ( $subscription->status ) {
						case MS_Model_Relationship::STATUS_CANCELED:
							videopro_ms_membership_box_html(
								$membership,
								MS_Helper_Membership::MEMBERSHIP_ACTION_RENEW,
                                $data,
								$msg,
								$subscription
							);
							break;

						case MS_Model_Relationship::STATUS_EXPIRED:
							videopro_ms_membership_box_html(
								$membership,
								MS_Helper_Membership::MEMBERSHIP_ACTION_RENEW,
                                $data,
								$msg,
								$subscription
							);
							break;

						case MS_Model_Relationship::STATUS_TRIAL:
						case MS_Model_Relationship::STATUS_ACTIVE:
						case MS_Model_Relationship::STATUS_WAITING:
							videopro_ms_membership_box_html(
								$membership,
								MS_Helper_Membership::MEMBERSHIP_ACTION_CANCEL,
                                $data,
								$msg,
								$subscription
							);
							break;

						case MS_Model_Relationship::STATUS_PENDING:
							if ( $membership->is_free() ) {
								$memberships[] = $membership;
							} else {
                                                            
                                                                if ( ! empty( $membership->_move_from ) ) {
                                                                        $m_action = MS_Helper_Membership::MEMBERSHIP_ACTION_MOVE;
                                                                } else {
                                                                        $m_action = MS_Helper_Membership::MEMBERSHIP_ACTION_PAY;
                                                                }
                                                            
								videopro_ms_membership_box_html(
									$membership,
									$m_action,
                                    $data,
									$msg,
									$subscription
								);
							}
							break;

						default:
							videopro_ms_membership_box_html(
								$membership,
								MS_Helper_Membership::MEMBERSHIP_ACTION_CANCEL,
                                $data,
								$msg,
								$subscription
							);
							break;
					}
				}
			}

			if ( $member->has_membership() && ! empty( $memberships ) ) {
				?>
				<legend class="ms-move-from">
					<?php esc_html_e( 'Available Memberships', 'membership2' ); ?>
				</legend>
				<?php
			}
			?>
			<div class="ms-form-price-boxes">
				<?php
				do_action(
					'ms_view_shortcode_membershipsignup_form_before_memberships',
					$data
				);
                
                $data['box_layout'] = 'vertical';

				foreach ( $memberships as $membership ) {
					if ( ! empty( $membership->_move_from ) ) {
						$action = MS_Helper_Membership::MEMBERSHIP_ACTION_MOVE;
					} else {
						$action = MS_Helper_Membership::MEMBERSHIP_ACTION_SIGNUP;
					}

					videopro_ms_membership_box_html(
						$membership,
						$action,
						$data
					);
				}

				do_action(
					'ms_view_shortcode_membershipsignup_form_after_memberships',
					$data
				);

				do_action( 'ms_show_prices' );
				?>
			</div>
		</div>

		<div style="clear:both;"></div>
		<?php
		$html = ob_get_clean();
		$html = apply_filters( 'ms_compact_code', $html );
        
        return $html;
}

/**
 * Output the HTML content of a single membership box.
 * This includes the membership name, description, price and the action
 * button (Sign-up, Cancel, etc.)
 *
 * @since  1.0.0
 * @param  MS_Model_Membership $membership
 * @param  string $action
 * @param  string $msg
 * @param  MS_Model_Relationship $subscription
 */
function videopro_ms_membership_box_html( $membership, $action, $data, $msg = null, $subscription = null) {
    $fields = videopro_ms_prepare_fields(
        $membership->id,
        $action,
        $data['step'],
        $membership->_move_from
    );
    $settings = MS_Factory::load( 'MS_Model_Settings' );

    if ( 0 == $membership->price ) {
        $price = esc_html__( 'Free', 'membership2' );
    } else {
        $price = sprintf(
            '%s %s',
            $settings->currency,
            MS_Helper_Billing::format_price( $membership->total_price ) // Includes Tax
        );
    }
    $price = apply_filters( 'ms_membership_price', $price, $membership );

    if ( is_user_logged_in() ) {
        $current = MS_Model_Pages::MS_PAGE_MEMBERSHIPS;
    } else {
        $current = MS_Model_Pages::MS_PAGE_REGISTER;
    }

    $url = MS_Model_Pages::get_page_url( $current );

    $classes = array(
        'ms-membership-details-wrapper',
        'ms-signup',
        'ms-membership-' . $membership->id,
        'ms-type-' . $membership->type,
        'ms-payment-' . $membership->payment_type,
        $membership->trial_period_enabled ? 'ms-with-trial' : 'ms-no-trial',
        'ms-status-' . ( $subscription ? $subscription->status : 'none' ),
        'ms-subscription-' . ($subscription ? $subscription->id : 'none' ),
    );
            
            $action_url = esc_url( $url );
            $membership_id = esc_attr( $membership->id );
            $membership_wrapper_classes = esc_attr( implode( ' ', $classes ) );
            $membership_name = esc_html( $membership->name );
            $membership_description = $membership->get_description();
            $membership_price = esc_html( $price );
            
            $class = apply_filters(
                    'ms_view_shortcode_membershipsignup_form_button_class',
                    'ms-signup-button ' . esc_attr( $action )
            );

            $button = array(
                    'id' => 'submit',
                    'type' => MS_Helper_Html::INPUT_TYPE_SUBMIT,
                    'value' => esc_html( $data[ "{$action}_text" ] ),
                    'class' => $class,
            );

            /**
             * Allow customizing the Signup button.
             *
             * Either adjust the array properties or return a valid HTML
             * string that will be directly output.
             *
             * @since  1.0.1.2
             * @param  array|string $button
             * @param  MS_Model_Membership $membership
             * @param  MS_Model_Subscription $subscription
             */
            $button = apply_filters(
                    'ms_view_shortcode_membershipsignup_button',
                    $button,
                    $membership,
                    $subscription
            );
            
            if ( MS_Helper_Membership::MEMBERSHIP_ACTION_CANCEL === $action ) {
                    /**
                     * PayPal Standard Gateway uses a special Cancel button.
                     *
                     * @see MS_Controller_Gateway
                     */
                    $button = apply_filters(
                            'videopro_ms_view_shortcode_membershipsignup_cancel_button',
                            $button,
                            $subscription,
                            $data
                    );
            } elseif ( MS_Helper_Membership::MEMBERSHIP_ACTION_PAY === $action ) {
                    // Paid membership: Display a Cancel button

                    $cancel_action = MS_Helper_Membership::MEMBERSHIP_ACTION_CANCEL;
                    $url = videopro_ms_get_action_url(
                            $membership->id,
                            $cancel_action,
                            '', // step is not required for cancel
                            $data
                    );

                    $link = array(
                            'url' => $url,
                            'class' => 'ms-cancel-button button',
                            'value' => esc_html( $data[ "{$cancel_action}_text" ] ),
                    );
                    
            }
            
            /**
             * If membership is not active, we won't allow to renew
             */
            if( ! $membership->active ) {
                $button = '';
            }
            
            $template_data = array(
                                'membership_id' => $membership_id,
                                'membership_wrapper_classes' => $membership_wrapper_classes,
                                'membership_name' => $membership_name,
                                'membership_description' => $membership_description,
                                'membership_price' => $membership_price,
                                'msg' => $msg,
                                'action' => $action,
                                'link' => isset( $link ) ? $link : '',
                                'fields' => $fields,
                                'button' => $button,
                                'm2_obj' => null
                            );
            MS_Helper_Template::$ms_single_box = $template_data;
            
            ?>
            <form action="<?php echo esc_url($action_url); ?>" class="ms-membership-form" method="post">
                <?php
                    wp_nonce_field( $fields['action']['value'] );
                    
                    $videopro_ms_box_layout = $data['box_layout'];
                    
                    if( $path = MS_Helper_Template::template_exists( 'membership_box_html.php' ) ) {
                        require $path;
                    }
                ?>
            </form>
    <?php
    do_action( 'ms_show_prices' );
}

/**
 * Return an array with input field definitions used on the
 * membership-registration page.
 *
 * @since  1.0.0
 *
 * @param  int $membership_id
 * @param  string $action
 * @param  string $step
 * @param  string $move_from_id
 * @return array Field definitions
 */
function videopro_ms_prepare_fields( $membership_id, $action, $step, $move_from_id = null ) {
    $fields = array(
        'membership_id' => array(
            'id' => 'membership_id',
            'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
            'value' => $membership_id,
        ),
        'action' => array(
            'id' => 'action',
            'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
            'value' => $action,
        ),
        'step' => array(
            'id' => 'step',
            'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
            'value' => $step,
        ),
    );

    if ( $move_from_id ) {
        if ( is_array( $move_from_id ) ) {
            $move_from_id = implode( ',', $move_from_id );
        }

        $fields['move_from_id'] = array(
            'id' => 'move_from_id',
            'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
            'value' => $move_from_id,
        );
    }

    if ( MS_Helper_Membership::MEMBERSHIP_ACTION_CANCEL == $action ) {
        unset( $fields['step'] );
    }

    return $fields;
}

/**
 * Returns a URL to trigger the specified membership action.
 *
 * The URL can be used in a link or a form with only a submit button.
 *
 * @since  1.0.0
 * @param  string $action
 * @return string The URL.
 */
function videopro_ms_get_action_url( $membership, $action, $step, $data ) {
    if ( empty( $data['member'] ) ) {
        $member = MS_Model_Member::get_current_member();
    } else {
        $member = $data['member'];
    }

    if ( is_numeric( $membership ) ) {
        $membership = MS_Factory::load(
            'MS_Model_Membership',
            $membership
        );
    }

    $membership->_move_from = $member->cancel_ids_on_subscription(
        $membership->id
    );

    $fields = videopro_ms_prepare_fields(
        $membership->id,
        $action,
        $step,
        $membership->_move_from
    );

    if ( is_user_logged_in() ) {
        $current = MS_Model_Pages::MS_PAGE_MEMBERSHIPS;
    } else {
        $current = MS_Model_Pages::MS_PAGE_REGISTER;
    }

    $url = MS_Model_Pages::get_page_url( $current );

    if ( $action == MS_Helper_Membership::MEMBERSHIP_ACTION_SIGNUP ) {
        // Only add the membership_id to the URL.
        $url = esc_url_raw(
            add_query_arg(
                'membership_id',
                $membership->id,
                $url
            )
        );
    } else {
        $url = esc_url_raw(
            add_query_arg(
                '_wpnonce',
                wp_create_nonce( $action ),
                $url
            )
        );

        foreach ( $fields as $field ) {
            $url = esc_url_raw(
                add_query_arg(
                    $field['id'],
                    $field['value'],
                    $url
                )
            );
        }
    }

    return apply_filters(
        'videopro_ms_view_shortcode_membershipsignup_action_url',
        $url,
        $action,
        $membership,
        $data
    );
}
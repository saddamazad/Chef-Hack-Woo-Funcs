<?php
add_filter('woocommerce_get_item_data', 'display_extra_meal_options_cart', 10, 2);
function display_extra_meal_options_cart($item_data, $cart_item) {
    if (!empty($cart_item['extra_options'])) {
        foreach ($cart_item['extra_options'] as $extra) {
            $item_data[] = array(
                'name'  => $extra['label'],
                'value' => wc_price($extra['cost']),
            );
        }
    }
    return $item_data;
}

add_filter('woocommerce_add_cart_item_data', 'add_extra_meal_options_cart_item_data', 10, 2);
function add_extra_meal_options_cart_item_data($cart_item_data, $product_id) {
    if (!empty($_POST['extra_options'])) {
        $cart_item_data['extra_options'] = json_decode(stripslashes($_POST['extra_options']), true);
    }
    return $cart_item_data;
}

//add_filter('woocommerce_cart_item_price', 'add_extra_price_to_cart_item', 10, 3);
function add_extra_price_to_cart_item($price, $cart_item, $cart_item_key) {
    if (isset($cart_item['extra_options'])) {
        $extra_price = 0;
        foreach ($cart_item['extra_options'] as $extra) {
            $extra_price += $extra['cost'];
        }
        // Add the extra price to the product price
        $price = wc_price($cart_item['data']->get_price() + $extra_price);
    }
    return $price;
}

add_action('woocommerce_before_calculate_totals', 'add_extra_meal_options_price', 10, 1);
function add_extra_meal_options_price($cart) {
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $product_id = $cart_item['product_id'];

        if (isset($cart_item['extra_options'])) {
            $extra_price = 0;
            foreach ($cart_item['extra_options'] as $extra) {
                $extra_price += $extra['cost'];
            }

            //$price = $cart_item['data']->get_price() + $extra_price;
            $product = wc_get_product( $product_id );
            $original_price = $product->get_price();
            $price = $original_price + $extra_price;
            $cart_item['data']->set_price($price);
        }
    }
}

add_action('woocommerce_order_item_meta_end', 'display_custom_size_order_meta', 10, 4);
function display_custom_size_order_meta($item_id, $item, $order, $plain_text) {
    $extra_meal_options = $item->get_meta('Extra Meal Options', true);

    if (!empty($extra_meal_options)) {
            // Decode the extra meal options and display them
            $extra_meal_options_array = json_decode($extra_meal_options, true);
            if (is_array($extra_meal_options_array)) {
                //echo '<p><strong>' . __('Extra Meal Options') . ':</strong></p>';
                foreach ($extra_meal_options_array as $extra_option) {
                    echo '<div>' . esc_html($extra_option['label']) . ': ' . wc_price($extra_option['cost']) . '</div>';
                }
            }
        }
}

add_action('woocommerce_after_order_itemmeta', 'display_extra_meal_options_in_order_admin', 10, 3);
function display_extra_meal_options_in_order_admin($item_id, $item, $order) {
    // Retrieve the stored extra meal options for the item
    $extra_meal_options = wc_get_order_item_meta($item_id, 'Extra Meal Options', true);

    if (!empty($extra_meal_options)) {
        //echo '<p><strong>' . __('Extra Meal Options') . ':</strong></p>';
        $extra_options = json_decode($extra_meal_options, true);
        
        if (is_array($extra_options)) {
            foreach ($extra_options as $extra) {
                echo '<div>' . esc_html($extra['label']) . ': ' . wc_price($extra['cost']) . '</div>';
            }
        }
    }
}

add_action('woocommerce_checkout_create_order_line_item', 'add_extra_meal_options_to_order', 10, 4);
function add_extra_meal_options_to_order($item, $cart_item_key, $values, $order) {
    if (isset($values['extra_options'])) {
        $item->add_meta_data('Extra Meal Options', json_encode($values['extra_options']), true);
    }
}
add_filter('woocommerce_order_item_get_formatted_meta_data', 'hide_raw_json_custom_sizes', 10, 2);
function hide_raw_json_custom_sizes($formatted_meta, $item) {
    foreach ($formatted_meta as $key => $meta) {
        if ($meta->key === 'Extra Meal Options') {
            unset($formatted_meta[$key]); // Remove raw JSON output
        }
    }
    return $formatted_meta;
}

add_filter('woocommerce_cart_item_permalink','__return_false');

// Disable shipping calculation logic on the Cart page
function ch_disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
	
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'ch_disable_shipping_calc_on_cart', 99 );

/**
 * Hide shipping rates when free shipping is available.
 * Updated to support WooCommerce 2.6 Shipping Zones.
 *
 * @param array $rates Array of rates found for the package.
 * @return array
 */
function ch_hide_shipping_when_free_is_available( $rates ) {
	$chosen_method = array();
	
	if( WC()->session ) {
		$shipping_type = WC()->session->get('shipping_type');
		
		$free_shipping_applied = false;
		
		$applied_coupons = WC()->cart->get_applied_coupons();
		if( sizeof($applied_coupons) > 0 ) {
			foreach($applied_coupons as $coupon_code) {
				$coupon = new WC_Coupon( $coupon_code );
				
				// check if this couple allows free shipping
				if( $coupon->get_free_shipping() ) {
					$free_shipping_applied = true;
					break;
				}
			}
		}
		
		foreach( $rates as $rate_id => $rate ) {
			if( $free_shipping_applied ) {
				if( ($shipping_type == 'Delivery') && ('free_shipping' === $rate->method_id) ) {
					//$chosen_method[ $rate_id ] = $rate;
					WC()->session->set( 'chosen_shipping_methods', array( $rate_id ) );
					break;
				}
			} else {
				if( ($shipping_type == 'Delivery') && (strpos($rate->method_id, 'flat_rate') !== false) ) {
					//$chosen_method[ $rate_id ] = $rate;
					WC()->session->set( 'chosen_shipping_methods', array( $rate_id ) );
					break;
				} elseif( ($shipping_type == 'Pickup') && (strpos($rate->method_id, 'local_pickup') !== false) ) {
					//$chosen_method[ $rate_id ] = $rate;
					WC()->session->set( 'chosen_shipping_methods', array( $rate_id ) );
					break;
				}
				$chosen_method[ $rate_id ] = $rate;
			}
			
			/*if(strpos($rate_id, 'flat_rate') !== false) {
				unset($rates[$rate_id]);
			}*/
		}
	}
	
	//return ! empty( $chosen_method ) ? $chosen_method : $rates;
	return $rates;
}
add_filter( 'woocommerce_package_rates', 'ch_hide_shipping_when_free_is_available', 100 );

add_action( 'woocommerce_review_order_before_payment', 'ch_add_custom_checkout_radio_options', 5 );
function ch_add_custom_checkout_radio_options() {
	$chosen = '';
	$shipping_type = '';
	$shipping_time = '';
	
	if( WC()->session->get('insulated_bag_fee') ) {
		$chosen = 1;
	}
	
	if( WC()->session->get('shipping_type') ) {
		$shipping_type = WC()->session->get('shipping_type');
		
		if($shipping_type == "Delivery") {
			$shipping_time = WC()->session->get('delivery_time');
		} elseif($shipping_type == "Pickup") {
			$shipping_time = WC()->session->get('pickup_time');
		}
	}
	
	woocommerce_form_field( 'insulated_bag_fee', array(
        'type'  => 'checkbox',
        'label' => __(' Insulated Bag with Ice $9 <span class="ch-info-circle" data-title="Please leave bag out for pickup in order to receive a credit on your account for the insulated bag with ice.">i</span>'),
        'class' => array( 'form-row-wide' ),
    ), $chosen );
	
	woocommerce_form_field( 'shipping_type', array(
        'type'  => 'text',
        'label' => __('Shipping Type'),
        'class' => array( 'wc-hidden-field' ),
		'required' => false,
    ), $shipping_type );
	
	woocommerce_form_field( 'shipping_time', array(
        'type'  => 'text',
        'label' => __('Shipping Time'),
        'class' => array( 'wc-hidden-field' ),
		'required' => false,
    ), $shipping_time );
}

add_action( 'woocommerce_review_order_before_payment', 'ch_add_home_delivery_options_field', 1 );
function ch_add_home_delivery_options_field() {
    echo '<p class="form-row form-row-wide validate-required" id="delivery_for_not_home_field">
			<label for="delivery_for_not_home_At the Front Door" class="required_field">If I am not home at time of delivery, please leave the package:&nbsp;<span class="required" aria-hidden="true">*</span></label>
			<span class="woocommerce-input-wrapper">
				<input type="radio" class="input-radio " value="At the Front Door" name="delivery_for_not_home" aria-required="true" autocomplete="off" id="delivery_for_not_home_At the Front Door"><label for="delivery_for_not_home_At the Front Door" class="radio required_field">At the Front Door&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="radio" class="input-radio " value="At the Back Door" name="delivery_for_not_home" aria-required="true" autocomplete="off" id="delivery_for_not_home_At the Back Door"><label for="delivery_for_not_home_At the Back Door" class="radio required_field">At the Back Door&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="radio" class="input-radio " value="At the Concierge" name="delivery_for_not_home" aria-required="true" autocomplete="off" id="delivery_for_not_home_At the Concierge"><label for="delivery_for_not_home_At the Concierge" class="radio required_field">At the Concierge&nbsp;<span class="required" aria-hidden="true">*</span></label>
				<input type="radio" class="input-radio " value="At my Unit" name="delivery_for_not_home" aria-required="true" autocomplete="off" id="delivery_for_not_home_At my Unit"><label for="delivery_for_not_home_At my Unit" class="radio required_field">At my Unit&nbsp;<span class="required" aria-hidden="true">*</span></label>
			</span>
	</p>';
}

// Remove "(optional)" label on "Installement checkbox" field
add_filter( 'woocommerce_form_field' , 'ch_remove_order_comments_optional_fields_label', 10, 4 );
function ch_remove_order_comments_optional_fields_label( $field, $key, $args, $value ) {
    // Only on checkout page for specific fields
    if( 'delivery_for_not_home' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
    if( 'insulated_bag_fee' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
	if( 'shipping_type' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
	if( 'shipping_time' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
	if( 'order_comments' === $key && is_checkout() ) {
        $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
        $field = str_replace( $optional, '', $field );
    }
    return $field;
}

add_action( 'woocommerce_checkout_process', 'ch_validate_custom_checkout_fields' );
function ch_validate_custom_checkout_fields() {
    // Grab chosen shipping method for the first package
    if ( isset( $_POST['shipping_method'][0] ) ) {
        $chosen_method = wc_clean( wp_unslash( $_POST['shipping_method'][0] ) );
    } else {
        $chosen_method = '';
    }

    // Only require this field if shipping != pickup
    if ( strpos($chosen_method, 'local_pickup') === false ) {
        if ( empty( $_POST['delivery_for_not_home'] ) && (WC()->session->get('shipping_type') === "Delivery") ) {
            wc_add_notice( __( 'Please select an option for if you are not home at the time of delivery.', 'woocommerce' ), 'error' );
        }
    }
}

add_action( 'wp_footer', 'ch_toggle_delivery_options_based_on_shipping' );
function ch_toggle_delivery_options_based_on_shipping() {
    if ( ! is_checkout() ) {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery( function($) {
        function update_delivery_options_visibility() {
            <?php if( WC()->session->get('shipping_type') === "Delivery" ) { ?>
                $('#delivery_for_not_home_field').show();
                $('#delivery_for_not_home_field input').prop('required', true);
            <?php } else { ?>
                $('#delivery_for_not_home_field').hide();
                $('#delivery_for_not_home_field input').prop('required', false);
            <?php } ?>
			$('.woocommerce-shipping-methods input[type="radio"]:not(:checked)').closest("li").hide();
			
			if( $(".shipping.recurring-total").length ) {
				let checkedMethod = $('.woocommerce-shipping-methods input[type="radio"]:checked').val();
				let checkedSubsMethod = $('.shipping.recurring-total input[type="radio"]:checked').val();
				if( checkedMethod != checkedSubsMethod ) {
					$('.shipping.recurring-total .shipping_method').removeAttr('checked');
					$('.shipping.recurring-total .shipping_method[value="'+checkedMethod+'"]').prop('checked', true);
					
					//$('body').trigger('update_checkout');
				}
				$('.shipping.recurring-total input[type="radio"]:not(:checked)').closest("li").hide();
			}
			
			<?php if( ! WC()->session->get('shipping_type') ) { ?>
			$('#insulated_bag_fee_field').hide();
			<?php } ?>
        }

        // On initial load:
        update_delivery_options_visibility();

        // When user changes shipping method:
        $('form.checkout').on( 'change', 'input[name="shipping_method[0]"]', function() {
            //update_delivery_options_visibility();
        });

        // After WC AJAX updates the checkout fragments:
        $( document.body ).on( 'updated_checkout', function() {
            update_delivery_options_visibility();
        });
    });
    </script>
    <?php
}

// Update the order custom meta with field value
add_action( 'woocommerce_checkout_update_order_meta', 'ch_custom_checkout_field_update_order_meta', 10, 1 );
function ch_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['delivery_for_not_home'] ) ) {
        update_post_meta( $order_id, '_delivery_for_not_home', sanitize_text_field( $_POST['delivery_for_not_home'] ) );
    }
	
	if ( isset( $_POST['insulated_bag_fee'] ) ) {
        update_post_meta( $order_id, '_insulated_bag_fee', sanitize_text_field( $_POST['insulated_bag_fee'] ) );
    }
	
	if ( isset( $_POST['shipping_type'] ) ) {
        update_post_meta( $order_id, '_shipping_type', sanitize_text_field( $_POST['shipping_type'] ) );
    }
	
	if ( isset( $_POST['shipping_time'] ) ) {
        update_post_meta( $order_id, '_shipping_time', sanitize_text_field( $_POST['shipping_time'] ) );
    }
}

add_action('woocommerce_payment_complete', 'ch_copy_custom_meta_to_subscription', 10, 1);
function ch_copy_custom_meta_to_subscription($order_id) {
	$order = wc_get_order( $order_id );
	
	// Now save the custom meta fields value to Subscriptions (if any)
	if (function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order)) {
		$subscriptions = wcs_get_subscriptions_for_order($order);
		foreach ($subscriptions as $subs_id => $subscription) {
			if( get_post_meta($order_id, '_delivery_for_not_home', true) ) {
				$parent_order_dl_home = get_post_meta($order_id, '_delivery_for_not_home', true);
				update_post_meta( $subs_id, '_delivery_for_not_home', $parent_order_dl_home );
			}
			if( get_post_meta($order_id, '_shipping_type', true) ) {
				$parent_order_shipping_type = get_post_meta($order_id, '_shipping_type', true);
				update_post_meta( $subs_id, '_shipping_type', $parent_order_shipping_type );
			}
			if( get_post_meta($order_id, '_shipping_time', true) ) {
				$parent_order_shipping_time = get_post_meta($order_id, '_shipping_time', true);
				update_post_meta( $subs_id, '_shipping_time', $parent_order_shipping_time );
			}
			if( get_post_meta($order_id, '_insulated_bag_fee', true) ) {
				$parent_order_insulated_bag_fee = get_post_meta($order_id, '_insulated_bag_fee', true);
				update_post_meta( $subs_id, '_insulated_bag_fee', $parent_order_insulated_bag_fee );
			}
		}
	}
}

// Save the custom meta fields value to renewal order
add_action( 'woocommerce_subscription_renewal_payment_complete', 'ch_copy_custom_meta_to_renewal_order', 10, 2 );
function ch_copy_custom_meta_to_renewal_order($subscription, $last_order) {
	$subscription_id = $subscription->get_id();
	$order_id = $last_order->get_id();
	
	if( get_post_meta($subscription_id, '_delivery_for_not_home', true) ) {
		$parent_order_dl_home = get_post_meta($subscription_id, '_delivery_for_not_home', true);
		update_post_meta( $order_id, '_delivery_for_not_home', $parent_order_dl_home );
	}
	if( get_post_meta($subscription_id, '_shipping_type', true) ) {
		$parent_order_shipping_type = get_post_meta($subscription_id, '_shipping_type', true);
		update_post_meta( $order_id, '_shipping_type', $parent_order_shipping_type );
	}
	if( get_post_meta($subscription_id, '_shipping_time', true) ) {
		$parent_order_shipping_time = get_post_meta($subscription_id, '_shipping_time', true);
		update_post_meta( $order_id, '_shipping_time', $parent_order_shipping_time );
	}
	if( get_post_meta($subscription_id, '_insulated_bag_fee', true) ) {
		$parent_order_insulated_bag_fee = get_post_meta($subscription_id, '_insulated_bag_fee', true);
		update_post_meta( $order_id, '_insulated_bag_fee', $parent_order_insulated_bag_fee );
	}
}

// Display the custom-field in orders view
add_action( 'woocommerce_order_details_after_order_table', 'ch_display_custom_field_in_orde_details', 10, 1 );
function ch_display_custom_field_in_orde_details( $order ) {
    $home_delivery_opts = get_post_meta( $order->get_id(), '_delivery_for_not_home',  true );
	$shipping_type = get_post_meta( $order->get_id(), '_shipping_type',  true );
	$shipping_time = get_post_meta( $order->get_id(), '_shipping_time',  true );
	//$insulated_bag_fee = get_post_meta( $order->get_id(), '_insulated_bag_fee',  true );

    if ( ! empty( $home_delivery_opts ) ) {
    ?>
        <table class="woocommerce-table woocommerce-table--delivery-details shop_table delivery_details">
            <tbody>
				<tr>
					<td>If I am not home at time of delivery, please leave the package: <?php echo $home_delivery_opts; ?></td>
				</tr>
			</tbody>
        </table>
    <?php
	}
	if ( ! empty( $shipping_time ) ) {
    ?>
        <table class="woocommerce-table woocommerce-table--delivery-details shop_table shipping_time_details">
            <tbody>
				<tr>
					<td><?php echo $shipping_time; ?></td>
				</tr>
			</tbody>
        </table>
    <?php
	}
	//if( $insulated_bag_fee ) {
    ?>
        <!--<table class="woocommerce-table woocommerce-table--ordertype-details shop_table ordertype_details">
            <tbody>
				<tr>
                	<td>Insulated Bag with Ice $9</td>
            	</tr>
			</tbody>
        </table>-->
    <?php
	//}
}

function ch_display_order_data_in_admin( $order ) {
	if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
		echo '<p style="position: relative; top: 10px;">If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
	}
}
add_action('woocommerce_admin_order_data_after_order_details', 'ch_display_order_data_in_admin');

// Get Ajax request and saving to WC session
add_action( 'wp_ajax_insulated_bag_fee', 'ch_get_insulated_bag_fee' );
add_action( 'wp_ajax_nopriv_insulated_bag_fee', 'ch_get_insulated_bag_fee' );
function ch_get_insulated_bag_fee() {
    if ( isset($_POST['insulated_bag_fee']) ) {
        WC()->session->set('insulated_bag_fee', $_POST['insulated_bag_fee'] );
    } else {
		if( WC()->session->get('insulated_bag_fee') ) {
			WC()->session->__unset('insulated_bag_fee');
		}
	}
    die();
}

// Add a custom fee
add_action( 'woocommerce_cart_calculate_fees', 'custom_insulated_bag_fee', 20, 1 );
function custom_insulated_bag_fee( $cart ) {
    // Only on checkout
    if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || ! is_checkout() ) {
        return;
	}

    //$percent = 3;

    if( WC()->session->get('insulated_bag_fee') ) {
		$bag_fee = intval(WC()->session->get('insulated_bag_fee'));
        $cart->add_fee( __( 'Insulated bag with ice', 'woocommerce'), $bag_fee );
	}
}

add_action('woocommerce_thankyou', 'clear_custom_session_data_after_order');
function clear_custom_session_data_after_order($order_id) {
    if (WC()->session) {
		if( WC()->session->get('shipping_type') ) {
        	WC()->session->__unset('shipping_type');
		}
		if( WC()->session->get('delivery_time') ) {
			WC()->session->__unset('delivery_time');
		}
		if( WC()->session->get('delivery_postal_code') ) {
			WC()->session->__unset('delivery_postal_code');
		}
		if( WC()->session->get('pickup_time') ) {
			WC()->session->__unset('pickup_time');
		}
		if( WC()->session->get('insulated_bag_fee') ) {
			WC()->session->__unset('insulated_bag_fee');
		}
    }
}

/*add_action('woocommerce_before_checkout_form', function () {
    $shipping_type = WC()->session->get('shipping_type');
	$delivery_time = WC()->session->get('pickup_time');
	$delivery_postal_code = WC()->session->get('delivery_postal_code');

    echo $delivery_time."<br>";
	echo $delivery_postal_code;
});*/

/*add_action("init", function() {
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        print_r( $cart_item );
        echo '<br><br><br>';
    }
});*/
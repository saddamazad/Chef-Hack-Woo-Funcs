<?php
add_shortcode( 'review_carousel', 'get_review_carousel_shortcode_init' );
function get_review_carousel_shortcode_init() {	
	global $post;

	$args_post = array(
		'post_type' => 'reviews',
		'posts_per_page' => '-1',	
	);
				
	$query_post = new WP_Query( $args_post );	
	
	$output = '';
    if ( $query_post->have_posts() ):
			$output .='<slick-carousel class="slick-carousel" data-desktop="4" data-tablate="3" data-mobile="1" data-autoplay="false" data-arrows="true" data-animation-speed="1000" data-item-speed="5000" data-dots="false" data-infinite-loop="true" data-slides-to-scroll="1" data-center-mode="false" data-des-center-padding="0px" data-tab-center-padding="0px" data-mob-center-padding="0px">';	
		       
			while ( $query_post->have_posts() ) : $query_post->the_post();		

			 	$output .='<div class="carousel_item">';
					$output .='<h4>'.get_the_title().'</h4>';
					$output .='<div class="entry_content">'.apply_filters( 'the_content', get_the_content() ).'</div>';
					$output .='<div class="author_box">';
						if( has_post_thumbnail() ) {
						$feature_img = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
						$output .= '<span><img src="'.$feature_img[0].'" alt="'.get_the_title().'" /></span>';
					  }
						$output .='<div class="author_title">';
							if(get_post_meta($post->ID, '_cmb2_author_name', true)){
							$output .='<h5>'.get_post_meta($post->ID, '_cmb2_author_name', true).'</h5>';
							$output .='<div class="star"><svg width="128" height="19" viewBox="0 0 128 19" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.825 19L5.45 11.975L0 7.25L7.2 6.625L10 0L12.8 6.625L20 7.25L14.55 11.975L16.175 19L10 15.275L3.825 19Z" fill="#FFA53F"/><path d="M30.825 19L32.45 11.975L27 7.25L34.2 6.625L37 0L39.8 6.625L47 7.25L41.55 11.975L43.175 19L37 15.275L30.825 19Z" fill="#FFA53F"/><path d="M57.825 19L59.45 11.975L54 7.25L61.2 6.625L64 0L66.8 6.625L74 7.25L68.55 11.975L70.175 19L64 15.275L57.825 19Z" fill="#FFA53F"/><path d="M84.825 19L86.45 11.975L81 7.25L88.2 6.625L91 0L93.8 6.625L101 7.25L95.55 11.975L97.175 19L91 15.275L84.825 19Z" fill="#FFA53F"/><path d="M111.825 19L113.45 11.975L108 7.25L115.2 6.625L118 0L120.8 6.625L128 7.25L122.55 11.975L124.175 19L118 15.275L111.825 19Z" fill="#FFA53F"/></svg></div>';
						}
						$output .='</div>';
						
					$output .='</div>';
				 $output .='</div>';

			endwhile;
			$output .='</slick-carousel>';
       	endif;
	wp_reset_query();
	return $output;				 

}// End review_carousel

function current_year_shortcode() {
    return date('Y');
}
add_shortcode('current_year', 'current_year_shortcode');


function header_logout_button_shortcode( $atts ) {
    if ( is_user_logged_in() ) {
        return '<a href="' .wp_logout_url(). '"><svg width="18" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 19L11.663 18.766C14.241 17.856 15.531 17.401 16.265 16.363C16.999 15.325 17 13.957 17 11.223L17 8.778C17 6.043 17 4.676 16.265 3.638C15.531 2.599 14.241 2.144 11.663 1.234L11 0.999999M11 10L1 10M11 10C11 10.7 9.006 12.008 8.5 12.5M11 10C11 9.3 9.006 7.992 8.5 7.5" stroke="#191919" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></a>';
    }
}
add_shortcode( 'logout_link', 'header_logout_button_shortcode' );


add_shortcode("get_meals_by_cat", "get_meals_by_cat_callback");
function get_meals_by_cat_callback($atts) {
    extract(shortcode_atts(array(
        'cat' => '',
		'show_extra_meal' =>'false'
    ), $atts));
    
    ob_start();
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'product_cat'    => $cat
    );

    $loop = new WP_Query( $args );

    if( $loop->have_posts() ) {        
        echo '<div class="cat_product_grid" data-category="'.esc_attr($cat).'">';
        $term = get_term_by('slug', $cat, 'product_cat');
        echo '<h3>'.esc_html($term->name).'</h3>';
        echo '<div class="cat-products-wrap-row">';
			while ( $loop->have_posts() ) : $loop->the_post();
				global $product;
				echo '<div class="meal-product" data-product-id="'.esc_attr($product->get_id()).'">';
				echo $product->get_price_html();
				
				if( has_post_thumbnail( $product->get_id() ) ) {
					$img_atts = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
					echo '<div class="image_wrap"><img src="'.esc_url($img_atts[0]).'" class="meal-image" alt="'.esc_attr(get_the_title()).'" /></div>';
				}
	
				echo '<div class="product_extra_content">';
					echo '<h4>'.esc_html(get_the_title()).'</h4>';
					
					?>
					<?php if ($show_extra_meal == 'true') : ?>
					<div class="meal-extra">
						<div class="extra-meal-opt">
							<input type="checkbox" class="extra-opts" id="extra_meat_<?php echo esc_attr($product->get_id()); ?>" value="Extra Meat" data-ext-cost="4.00" />
							<label for="extra_meat_<?php echo esc_attr($product->get_id()); ?>">Extra Meat ($4)</label>
						</div>
						<div class="extra-meal-opt">
							<input type="checkbox" class="extra-opts" id="extra_carbs_<?php echo esc_attr($product->get_id()); ?>" value="Extra Carbs" data-ext-cost="2.00" />
							<label for="extra_carbs_<?php echo esc_attr($product->get_id()); ?>">Extra Carbs ($2)</label>
						</div>
						<div class="extra-meal-opt">
							<input type="checkbox" class="extra-opts" id="double_veg_<?php echo esc_attr($product->get_id()); ?>" value="Double Veg" data-ext-cost="2.00" />
							<label for="double_veg_<?php echo esc_attr($product->get_id()); ?>">Double Veg ($2)</label>
						</div>
						<div class="extra-meal-opt">
							<input type="checkbox" class="extra-opts" id="no_carb_<?php echo esc_attr($product->get_id()); ?>" value="No Carb/Extra Veg" data-ext-cost="0.00" />
							<label for="no_carb_<?php echo esc_attr($product->get_id()); ?>">No Carb/Extra Veg</label>
						</div>
					</div>
					<?php endif; ?>
					<?php if ( $product->is_type('simple') && $product->is_purchasable() ) : ?>
						<div class="add_to_cart_wrap">
							<div class="quantity-inner">
                            	<spna type="button" class="qty-minus">-</spna>
								<?php
									woocommerce_quantity_input( array(
										'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
										'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
										'input_value' => 1,
									) );
								?>
                                <spna type="button" class="qty-plus">+</spna>
							</div>
							<a 
								href="?add-to-cart=<?php echo esc_attr( $product->get_id() );?>" 
								data-quantity="1"
								data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
								data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
								class="button add_to_cart_button meal_add_to_cart"
								data-extra-options=""
								aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
								rel="nofollow"
							>
								<?php echo esc_html( $product->single_add_to_cart_text() ); ?>
							</a>
                        </div>
					<?php endif; ?>
				<?php echo '</div>'; ?>
		
				<div class="product-info-popup" style="display: none;">
					<div class="product-info-wrap">
						<span class="close-product-lbx">X</span>
						<h3 class="otm-title"><?php echo esc_html(get_the_title()); ?></h3>
						<?php
							if( has_post_thumbnail( $product->get_id() ) ) {
								$img_atts = wp_get_attachment_image_src(get_post_thumbnail_id(), 'medium');
								echo '<div class="product-thumb"><img src="'.esc_url($img_atts[0]).'" class="otm-thumb" alt="'.esc_attr(get_the_title()).'" /></div>';
							}
						?>
						<div class="otm-description">
							<?php echo $product->get_short_description(); ?>
						</div>
					</div>
				</div>
				<?php
				echo '</div>';
			endwhile;
        echo '
		</div>
		</div>';
    }
    wp_reset_postdata();    
    
    return ob_get_clean();
}

add_shortcode("get_meals_cart", "render_meals_cart");
function render_meals_cart() {
	ob_start();
	?>
	<div id="flash-cart">
		<h3>Your Cart</h3>
		<div class="cart-content-wrap">
			<div class="cart-no-item">No item</div>
			<div class="cart-totals">
				<div class="cart-subtotal-line d-flex">
					<span>Subtotal</span>
					<strong class="cart-subtotal">$0.00</strong>
				</div>
				<div class="cart-total-line d-flex">
					<span>Total</span>
					<strong class="cart-total">$0.00</strong>
				</div>
			</div>
		</div>
	</div>
    <div class="cart_note">
    	<h5>Note</h5>
        <p>The order minimum is <strong>6 meals</strong></p>
        <p>One-time orders – minimum is  <strong>5 Meals <a href="#">(2 Meals Left)</a></strong></p>
        <p>There is no minimum order  <strong>for pickup orders</strong></p>
    </div>
	<?php
	return ob_get_clean();
}

function get_order_type_form_callback() {
	ob_start();
	?>
	<div class="order-type-forms-wrapper">
		<div class="order-type-toggle">
			<span data-order-type="delivery" class="active">Delivery</span><span data-order-type="pickup">Pickup</span>
		</div>
		<form action="" method="POST" id="delivery-form">
			<h3>Delivery Option</h3>
			<div class="input-radio">
				<input type="radio" name="delivery_time" value="Delivery Wednesday 2 - 8 pm" id="delv-opt1" required />
				<label for="delv-opt1">Delivery Wednesday 2 - 8 pm</label>
			</div>
			<!--<div class="input-radio">
				<input type="radio" name="delivery_time" value="Delivery Monday am 8am - 12 pm" id="delv-opt2" required />
				<label for="delv-opt2">Delivery Monday am 8am - 12 pm</label>
			</div>-->
			<div class="divider-line"></div>
			<input type="hidden" name="shipping_type" value="Delivery" />
			<input type="hidden" name="order_type" value="<?php echo $_GET["order_plan"] ?>" />
			<h3>Enter your postal code</h3>
			<div id="postcode-msg"></div>
			<div class="postal-code-input">
				<input type="text" name="delivery_postal_code" id="postal-code" placeholder="Enter your postal code" required />
				<input type="submit" name="delivery_form_submit" id="postal-code-submit" value="Submit" />
			</div>
		</form>
		<form action="" method="POST" id="pickup-form" style="display: none;">
			<h3>Pickup Option</h3>
			<div class="input-radio">
				<input type="radio" name="pickup_time" value="Pickup time Wednesday 2 - 8 pm" id="pkup-opt1" required />
				<label for="pkup-opt1">Pickup time Wednesday 2 - 8 pm</label>
			</div>
			<!--<div class="input-radio">
				<input type="radio" name="pickup_time" value="Pickup time Monday 11am-6pm" id="pkup-opt2" required />
				<label for="pkup-opt2">Pickup time Monday 11am-6pm</label>
			</div>-->
			<div class="divider-line"></div>
			<input type="hidden" name="shipping_type" value="Pickup" />
			<input type="hidden" name="order_type" value="<?php echo $_GET["order_plan"] ?>" />
			<input type="submit" name="pickup_form_submit" value="Next" />
		</form>
		<div class="min-order-info">
			<p>Minimum order for delivery is 6 meals.</p>
			<p>Minimum order for pickup - none.</p>
		</div>
		<div class="divider-line"></div>
		<h3>Pickup Location</h3>
		<p>4050 Bath Rd, Kingston, ON K7M 4Y4</p>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode("get_order_type_form", "get_order_type_form_callback");

function show_custom_woo_message_callback() {
	ob_start();
	if (isset($_GET['less_item_msg']) && $_GET["less_item_msg"] == 1) {
        echo '<div class="woocommerce-message" role="alert">';
        echo esc_html("Minimum order for delivery is 6 meals.");
        echo '</div>';
    }
	
	/*$shipping_type = WC()->session->get('shipping_type');
	$delivery_time = WC()->session->get('pickup_time');
	$delivery_postal_code = WC()->session->get('delivery_postal_code');

    echo $shipping_type."<br>";
	echo $delivery_postal_code;*/
	
	return ob_get_clean();
}
add_shortcode("show_custom_woo_message", "show_custom_woo_message_callback");

/**
 * Usage: [get_subscription_products type="meal type"]
 */
function subscription_meal_products_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(
        array(
            'type' => '', // Default empty
        ),
        $atts,
        'get_subscription_products'
    );

    // If no type specified, return empty
    if (empty($atts['type'])) {
        return '<p>Please specify a meal type.</p>';
    }

    // Query arguments
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
		'meta_key' => '_subsc_meals_plan',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query'     => array(
            array(
                'key'     => '_subsc_meals_type',
                'value'   => sanitize_text_field($atts['type']),
                'compare' => '=',
            ),
        ),
    );

    $products = new WP_Query($args);
	$counter = $products->post_count;

    ob_start(); // Start output buffering

    if ($products->have_posts()) {
        $count = 0;
		$meal_plan_opt = '';
		$init_price = 0;
		//$init_add_cart_url = '';
		//$init_product_id = 0;
        while ($products->have_posts()) {
            $products->the_post();
            global $product;
			$count++;
			
			if($count == $counter) {
				// Get product data
				$thumbnail = get_the_post_thumbnail(get_the_ID(), 'large');
				$title = get_the_title();
				$short_description = get_the_excerpt();
				//$short_description = $product->get_short_description();
				
				//$price = $product->get_price_html();
				//$product_url = $product->get_permalink();
			}
			
			$price = wc_get_price_to_display($product); // Get numeric price
			$add_to_cart_url = $product->add_to_cart_url();
			
			if($count == 1) {
				$init_price = '$'.$price;
				//$init_add_cart_url = $product->add_to_cart_url();
				//$init_product_id = get_the_ID();
			}
			
			$meal_plan = get_post_meta(get_the_ID(), '_subsc_meals_plan', true);
			if( $meal_plan ) {
				$meal_plan_opt .= '<option value="' . $meal_plan . '" data-product-id="' . get_the_ID() . '" data-price="$' . esc_html($price) . '">' . $meal_plan . ' Meals Plan</option>';
			}
			
			if($count == $counter) {
				echo '<div class="subsc-meal-product">
					<div class="product-image"><img src="'.site_url("/wp-content/uploads/2025/06/Logo-Icon.png").'" alt="Logo-Icon" class="subsc-logo-icon" />'.$thumbnail.'</div>

					<div class="product-info">
						<h3>'.esc_html($atts['type']).'</h3>
						<div class="product-description">'.wpautop($short_description).'</div>
					</div>

					<div class="product-plan-variations">
						<div class="product-options">
							<select class="meal-option-select">'.$meal_plan_opt.'</select>
						</div>
					</div>

					<div class="product-action">
						<a href="#" class="button buy-now">'.esc_html('Buy Now', 'elementor').'</a>
						<div class="product-price">'.$init_price.'</div>
					</div>
				</div>';
			}
        }
                
        // Reset post data
        wp_reset_postdata();
    } else {
        echo '<p>No products found for this meal type.</p>';
    }

    return ob_get_clean(); // Return the buffered output
}
add_shortcode('get_subscription_products', 'subscription_meal_products_shortcode');

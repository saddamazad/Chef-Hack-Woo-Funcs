<?php

/**
 * Enqueue styles
 */

function my_child_theme_enqueue_styles() {

    $parent_style = 'parent-style';
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'slick-style', get_stylesheet_directory_uri() .  '/inc/slick.css');
	wp_enqueue_script( 'slick-js', get_stylesheet_directory_uri() .  '/inc/slick.min.js', array(), '1.8.1', true );

	wp_enqueue_style( 'child-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( $parent_style ),
			wp_get_theme()->get('Version')
		);   
}
add_action( 'wp_enqueue_scripts', 'my_child_theme_enqueue_styles' );

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

// Include the WooCommerce functions
require_once dirname( __FILE__ ) . '/inc/woo-functions.php';

// Include the shortcodes
require_once dirname( __FILE__ ) . '/inc/shortcodes.php';

// Include the main TCPDF library
include_once dirname( __FILE__ ) . '/tcpdf/tcpdf.php';

// Include the admin reports
require_once dirname( __FILE__ ) . '/inc/admin-reports.php';

add_action('wp_head', 'get_custom_script_init');
function get_custom_script_init(){
	?>
    <script>
	document.addEventListener("DOMContentLoaded", function() {
	class SlickCarousel extends HTMLElement {
	  connectedCallback() {
		const desktop_items = parseInt(this.dataset.desktop);
		const tablate_items = parseInt(this.dataset.tablate);
		const mobile_items = parseInt(this.dataset.mobile);
		const slide_autoplay = this.dataset.autoplay === "true";
		const slider_arrows = this.dataset.arrows === "true";
		const animation_speed = parseInt(this.dataset.animationSpeed);
		const item_speed = parseInt(this.dataset.itemSpeed);
		const slider_dots = this.dataset.dots === "true";
		const slider_infinite_loop = this.dataset.infiniteLoop === "true";
		const slider_scroll = parseInt(this.dataset.slidesToScroll);
		const slider_vertical = this.dataset.vertical === "true";
		const slider_css = this.dataset.css;
		const slider_fade = this.dataset.fade === "true";
		const center_mode = this.dataset.centerMode === "true";
		const des_center_padding = this.dataset.desCenterPadding;
		const tab_center_padding = this.dataset.tabCenterPadding;
		const mob_center_padding = this.dataset.mobCenterPadding;
	  
		jQuery(this).slick({
		  slidesToShow: desktop_items,
		  arrows: slider_arrows,
		  vertical: slider_vertical,
		  autoplay: slide_autoplay,
		  speed: animation_speed,
		  slidesToScroll: slider_scroll,
		  dots: slider_dots,
		  fade: slider_fade,
		  adaptiveHeight: true,
		  centerPadding: des_center_padding,
		  centerMode: center_mode,
		  autoplaySpeed: item_speed,       
		  infinite: slider_infinite_loop,
		  prevArrow: '<button type="button" class="slick-prev" aria-label="Previous"><svg width="20" viewBox="0 0 30 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M0.999966 10H29M0.999966 10C0.999966 12.45 7.97897 17.028 9.74998 18.75M0.999966 10C0.999966 7.55001 7.97897 2.972 9.74998 1.25" stroke="white" stroke-width="1.88276" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
		  nextArrow: '<button type="button" class="slick-next" aria-label="Next"><svg width="20px" viewBox="0 0 30 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M29 10H1M29 10C29 12.45 22.021 17.028 20.25 18.75M29 10C29 7.55001 22.021 2.972 20.25 1.25" stroke="white" stroke-width="1.88276" stroke-linecap="round" stroke-linejoin="round"/></svg></button>',
		  cssEase: slider_css,
		  responsive: [
			{
			  breakpoint: 1024,
			  settings: {
				slidesToShow: tablate_items,
				centerPadding: tab_center_padding
			  }
			},
			{
			  breakpoint: 768,
			  settings: {
				slidesToShow: mobile_items,
				centerPadding: mob_center_padding
			  }
			},
			{
			  breakpoint: 410,
			  settings: {
				slidesToShow: 1,
				centerPadding: 0
			  }
			}
		  ]
		});
	  }
	}
	customElements.define('slick-carousel', SlickCarousel);
	});
	
	/*jQuery(function($){
		$(document).on('change', '.quantity input.qty', function(){
			var qty = $(this).val();
			$(this).closest('.meal-product').find('.add_to_cart_button').attr('data-quantity', qty);
		});
	});*/
	
	jQuery(document).ready(function($){
		$(document).on('click', '.meal_add_to_cart', function(e){
			var $thisbutton = $(this),
				product_id = $thisbutton.data('product_id'),
				quantity = $thisbutton.siblings(".quantity-inner").find('input.qty').val() || 1,
				extras = [];
	
			$thisbutton.closest('.meal-product').find('.extra-opts:checked').each(function(){
				extras.push({
					label: $(this).val(),
					cost: parseFloat($(this).data('ext-cost'))
				});
			});
	
			e.preventDefault();
	
			$.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
				data: {
					product_id: product_id,
					quantity: quantity,
					extra_options: JSON.stringify(extras)
				},
				beforeSend: function() {
					//$(this).prop('disabled', true).text('Adding...');
				},
				success: function(response){
					// Trigger a cart refresh
					$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
				}
			});
		});
		
		$(document).on('click', '.buy-now', function(e){
			var $thisbutton = $(this),
				product_id = $thisbutton.closest(".subsc-meal-product").find(".meal-option-select").find(':selected').attr("data-product-id"),
				quantity = 1;
	
			e.preventDefault();
	
			$.ajax({
				type: 'POST',
				url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
				data: {
					product_id: product_id,
					quantity: quantity
				},
				beforeSend: function() {
					//$(this).prop('disabled', true).text('Adding...');
				},
				success: function(response){
					// Trigger a cart refresh
					$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
				}
			});
		});
		
		$('form.checkout').on('change', '#insulated_bag_fee', function(e) {
			var fee = $(this).prop('checked') === true ? 9 : '';
			$.ajax({
				type: 'POST',
				url: wc_checkout_params.ajax_url,
				data: {
					'action': 'insulated_bag_fee',
					'insulated_bag_fee': fee,
				},
				success: function (result) {
					$('body').trigger('update_checkout');
				},
			});
		});
		
		/*$(".ch-info-circle").on("click", function(e) {
			e.preventDefault();
			$(this).toggleClass("actv");
		});*/
	
		// Refresh mini cart after adding
		/*$(document.body).on('added_to_cart', function(){
			$(document.body).trigger('wc_fragment_refresh');
		});*/
		
		$(".order-type-toggle span").on("click", function(e) {
			e.preventDefault();
			let selectedOption = $(this).attr("data-order-type");
			$(".order-type-toggle span").removeClass("active");
			$(this).addClass("active");
			$(".map-image").hide();
			$(".order-type-forms-wrapper form").hide();
			$("#"+selectedOption+"-map").show();
			$("#"+selectedOption+"-form").show();
		});
		$(".order-type-forms-wrapper .input-radio").on("click", function(e) {
			e.preventDefault();
			e.stopPropagation();
			$(this).closest("form").find(".input-radio").removeClass("checked-opt");
			
			let $radio = $(this).find('input[type="radio"]');
			let radioName = $radio.attr('name');

			// Uncheck all radios with the same name
			$('input[type="radio"][name="' + radioName + '"]').prop('checked', false);

			// Check the clicked one
			$radio.prop('checked', true);

			$(this).addClass("checked-opt");
		});
		
		$(document.body).on('update_checkout', function() {
			<?php if( WC()->session->get('shipping_type') === "Delivery" ) { ?>
			let postalCode = "<?php echo WC()->session->get('delivery_postal_code'); ?>";
			$("#billing_postcode").val(postalCode);
			$("#billing_postcode").prop("readonly", true);
			$("#shipping_postcode").val(postalCode);
			$("#shipping_postcode").prop("readonly", true);
			
			/*let preselectedProvince = 'ON'; // Ontario

			// Wait until the state field is available
			let interval = setInterval(function() {
				let $stateField = $('#billing_state');
				if ($stateField.length && $stateField.find('option').length > 1) {
					$stateField.val(preselectedProvince).trigger('change');
					clearInterval(interval);
				}
			}, 200);*/
			<?php } ?>
			
			<?php if( WC()->session->get('shipping_type') === "Pickup" ) { ?>
			$("#ship-to-different-address").css("pointer-events", "none");
			<?php } ?>
		});
		
		$(document).on('click', '.elementor-menu-cart__product-remove', function () {
			// Only run this on the checkout page
			<?php if( is_checkout() && !is_wc_endpoint_url() ) { ?>
			//location.reload();
			
			// Wait for WooCommerce's cart update to complete
			$(document.body).on('updated_wc_div removed_from_cart', function () {
				//if ($('body').hasClass('woocommerce-checkout')) {
				
					// Trigger update of order review via WooCommerce's event
					$('body').trigger('update_checkout');
				
				//}
			});
			<?php } ?>
		});
		
		$(".meal-product .image_wrap").on("click", function() {
			$(this).siblings(".product-info-popup").css("display", "flex");
		});
		$(".product_extra_content > h4").on("click", function() {
			$(this).parent().siblings(".product-info-popup").css("display", "flex");
		});
		$(".product-info-wrap").on("click", function(e) {
			e.stopPropagation();
		});
		$(".product-info-popup").on("click", function() {
			$(this).css("display", "none");
		});
		$(".close-product-lbx").on("click", function() {
			$(this).closest(".product-info-popup").css("display", "none");
		});
		
		$(".meal-option-select").on("change", function() {
			//let productAddUrl = $(this).find(':selected').attr("data-add-url");
			let productPrice = $(this).find(':selected').attr("data-price");
			
			//$(this).closest(".subsc-meal-product").find(".product-action .buy-now").attr("href", productAddUrl);
			$(this).closest(".subsc-meal-product").find(".product-action .product-price").text(productPrice);
		});
	});
	</script>
   <script>
		document.addEventListener('DOMContentLoaded', function() {
			document.querySelectorAll('.qty-minus').forEach(function(btn) {
				btn.addEventListener('click', function() {
					var input = this.parentNode.querySelector('input.qty');
					if (!input) return;
					var value = parseInt(input.value) || 1;
					if (value > (parseInt(input.min) || 1)) {
						input.value = value - 1;
						input.dispatchEvent(new Event('change'));
					}
				});
			});
		
			document.querySelectorAll('.qty-plus').forEach(function(btn) {
				btn.addEventListener('click', function() {
					var input = this.parentNode.querySelector('input.qty');
					if (!input) return;
					var value = parseInt(input.value) || 1;
					var max = parseInt(input.max) || 9999;
					if (value < max) {
						input.value = value + 1;
						input.dispatchEvent(new Event('change'));
					}
				});
			});
		});
	</script>
    
    <script>
		document.addEventListener('DOMContentLoaded', function() {
			if( document.querySelector('#loadMoreBtn') ) {
				var items = document.querySelectorAll('.all_category .cat_product_grid');
				var loadMoreBtn = document.getElementById('loadMoreBtn');
				var itemsToShow = 3;
				var currentIndex = 0;
				items.forEach(function(item) {
					item.style.display = 'none';
				});
				showNextItems();

				loadMoreBtn.addEventListener('click', function() {
					showNextItems();
				});

				function showNextItems() {
					for (var i = 0; i < itemsToShow; i++) {
						if (items[currentIndex]) {
							items[currentIndex].style.display = 'block';
							currentIndex++;
						}
					}
					if (currentIndex >= items.length) {
						loadMoreBtn.style.display = 'none';
					}
				}
			}
		});
		</script>
        
        <script>
		document.addEventListener('DOMContentLoaded', function () {
			var signupBtn = document.getElementById('open-registration-tab');
			var loginBtn = document.getElementById('open-login-tab');
		
			const loginForm = document.querySelector('form.login');
			const registrationForm = document.querySelector('form.register');
		
			if (registrationForm && loginForm) {
				registrationForm.style.display = 'none';
				loginForm.style.display = 'block';
				document.body.classList.add('active_login_form');
			}
		
			if (signupBtn) {
				signupBtn.addEventListener('click', function (e) {
					e.preventDefault();
					if (loginForm && registrationForm) {
						loginForm.style.display = 'none';
						registrationForm.style.display = 'block';
						document.body.classList.remove('active_login_form');
						document.body.classList.add('active_registration_form');
						registrationForm.scrollIntoView({ behavior: 'smooth' });
					}
				});
			}
		
			if (loginBtn) {
				loginBtn.addEventListener('click', function (e) {
					e.preventDefault();
					if (loginForm && registrationForm) {
						registrationForm.style.display = 'none';
						loginForm.style.display = 'block';
						document.body.classList.remove('active_registration_form');
						document.body.classList.add('active_login_form');
						loginForm.scrollIntoView({ behavior: 'smooth' });
					}
				});
			}
		});
		</script>
        <script>
			document.addEventListener("DOMContentLoaded", function () {
			  const infoCircles = document.querySelectorAll("#insulated_bag_fee_field .ch-info-circle");
			
			  infoCircles.forEach(function (circle) {
				circle.addEventListener("click", function (e) {
				  this.classList.toggle("active");
				  e.stopPropagation();
				});
			  });
			  
			});
			document.addEventListener("DOMContentLoaded", function () {
			  const variationsTable = document.querySelector(".variations_form table.variations");
			  const giftcardFormDiv = document.querySelector(".woocommerce_gc_giftcard_form");
			
			  if (variationsTable && giftcardFormDiv) {
				giftcardFormDiv.insertBefore(variationsTable, giftcardFormDiv.firstChild);
			  }
		   });
	</script>
    <script>
		document.addEventListener('DOMContentLoaded', function () {
		  function insertOrUpdateCartTitle() {
			const cartContainer = document.querySelector('.elementor-menu-cart__main');
			if (!cartContainer) return;
		
			let titleEl = cartContainer.querySelector('.mini-cart-title');
			if (!titleEl) {
			  titleEl = document.createElement('h4');
			  titleEl.className = 'mini-cart-title';
			  titleEl.textContent = 'Your cart (loading...)';
			  cartContainer.prepend(titleEl);
			}
			fetch('/wp-admin/admin-ajax.php?action=get_cart_item_count')
			  .then(response => response.text())
			  .then(count => {
				titleEl.textContent = `Your cart (${count} item${count === "1" ? '' : 's'})`;
			  });
		  }
		
		  insertOrUpdateCartTitle();
		  jQuery(document.body).on('updated_wc_div wc_fragments_loaded wc_fragment_refreshed added_to_cart', function () {
			insertOrUpdateCartTitle();
		  });
		});
		/*document.addEventListener('DOMContentLoaded', () => {
		  const header = document.querySelector('.default_header');
		  const scrollUp = 'sticky-up';
		  const scrollDown = 'sticky-down';
		  let lastScroll = 0;
		
		  window.addEventListener('scroll', () => {
			const currentScroll = window.pageYOffset;
		
			if (currentScroll <= 0) {
			  header.classList.remove(scrollUp);
			  return;
			}
		
			if (currentScroll > lastScroll && !header.classList.contains(scrollDown)) {
			  // down
			  header.classList.remove(scrollUp);
			  header.classList.add(scrollDown);
			} else if (currentScroll < lastScroll && header.classList.contains(scrollDown)) {
			  // up
			  header.classList.remove(scrollDown);
			  header.classList.add(scrollUp);
			}
		
			lastScroll = currentScroll;
		  });
		});*/
    </script>
    <?php
	$target_zone_id = 1; // Replace with your exact zone ID
    $target_postal_codes = [];

    $zones = WC_Shipping_Zones::get_zones();

    foreach ($zones as $zone) {
        if ($zone['zone_id'] === $target_zone_id) {
            foreach ($zone['zone_locations'] as $location) {
                if ($location->type === 'postcode') {
                    //$target_postal_codes[] = $location->code;
					$target_postal_codes[] = rtrim($location->code, "*");
                }
            }
            break; // Stop once we've found the zone
        }
    }

    // Convert to JSON for JS use
    $postal_codes_json = json_encode($target_postal_codes);
	
	// Script for the `Choose order type` page
	if( is_page(1507) ) {
	?>
	<script>
		// Postal codes from the shipping zone
        const wooPostalCodes = <?php echo $postal_codes_json; ?>;
		
		document.addEventListener("DOMContentLoaded", function () {
		
			document.getElementById('delivery-form').addEventListener('submit', function(e) {
				const form = e.target;

				if (form.checkValidity()) {
					// Form is valid
					let userPostalCode = document.getElementById('postal-code').value;
					let postalZone = userPostalCode.substring(0,3).toUpperCase();

					document.getElementById("postcode-msg").style.display = "none";

					if ( (userPostalCode.length > 5) && wooPostalCodes.includes(postalZone) ) {
						// Form will be submitted by default
					} else {
						document.getElementById("postcode-msg").innerHTML = '<div id="postal-error">Sorry, we don\'t deliver to this location!</div>';
						document.getElementById("postcode-msg").style.display = "block";
						
						e.preventDefault(); // Prevent default form submission
					}
				}
			});
			
		});
	</script>
	<?php
	}
}

//add_action( 'woocommerce_after_checkout_form', 'ch_disable_shipping_local_pickup' );
function ch_disable_shipping_local_pickup( $available_gateways ) {
   // Hide shipping based on the dynamic choice @ Checkout
   ?>
	<script type="text/javascript">
		jQuery('form.checkout').on('change','input[name^="shipping_method"]',function() {
			var val = jQuery( this ).val();
			if (val.match("^local_pickup")) {
				jQuery("#ship-to-different-address").fadeOut();
				jQuery(".shipping_address").fadeOut();
				jQuery("#delivery_for_not_home_field").hide();
			} else {
				jQuery('#ship-to-different-address').fadeIn();
				//jQuery("#ship-to-different-address").css("pointer-events", "auto");
				jQuery('#ship-to-different-address-checkbox').prop('checked', false);
				jQuery("#delivery_for_not_home_field").show();
			}
		});
	</script>
   <?php
}

add_action( 'cmb2_admin_init', 'register_cmb2_metabox_init' );
function register_cmb2_metabox_init() {
	$prefix = '_cmb2_';

	$cmb2 = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Review Metafield', 'cmb2' ),
		'object_types'  => array( 'reviews'), // Post type
	));
	
  	$cmb2->add_field( array(
		'name' => esc_html__( 'Author Name', 'cmb2' ),
		'id'   => $prefix . 'author_name',
		'type' => 'text',
	) );
	
	$cmb_subsc = new_cmb2_box( array(
		'id'            => $prefix . 'subscription_product_metabox',
		'title'         => esc_html__( 'Subscription Meals Plan Data', 'cmb2' ),
		'object_types'  => array( 'product'), // Post type
	));
	
	$cmb_subsc->add_field( array(
		'name'             => esc_html__( 'Meals Plan', 'cmb2' ),
		'id'               => '_subsc_meals_plan',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'6' => esc_html__( '6 Meals', 'cmb2' ),
			'8'   => esc_html__( '8 Meals', 'cmb2' ),
			'10'   => esc_html__( '10 Meals', 'cmb2' ),
			'15'   => esc_html__( '15 Meals', 'cmb2' ),
			'20'   => esc_html__( '20 Meals', 'cmb2' ),
		),
	) );
	
	$cmb_subsc->add_field( array(
		'name'             => esc_html__( 'Meals Type', 'cmb2' ),
		'id'               => '_subsc_meals_type',
		'type'             => 'select',
		'show_option_none' => true,
		'options'          => array(
			'The ChefHack Classic' => esc_html__( "The ChefHack Classic", "cmb2" ),
			'Meat Lover’s Hack'   => esc_html__( "Meat Lover’s Hack", "cmb2" ),
			'Comfort Carb Hack'   => esc_html__( "Comfort Carb Hack", "cmb2" ),
			'Veggie Boost Hack'   => esc_html__( "Veggie Boost Hack", "cmb2" ),
			'Low-Carb Hack'   => esc_html__( "Low-Carb Hack", "cmb2" ),
		),
	) );
}

add_action( 'woocommerce_admin_order_data_after_order_details', 'ch_order_shipping_fields' );
function ch_order_shipping_fields( $order ) {
	$shipping_type = get_post_meta( $order->get_id(), '_shipping_type', true );
	$shipping_time = get_post_meta( $order->get_id(), '_shipping_time', true );

    woocommerce_wp_select( array(
        'id'          => 'shipping_type',
        'label'       => __( 'Shipping Type', 'woocommerce' ),
        //'description' => __( 'Select a custom status for this order.', 'woocommerce' ),
        //'desc_tip'    => true,
        'value'       => $shipping_type,
        'options'     => array(
            'Delivery'     => __( 'Delivery', 'woocommerce' ),
            'Pickup'       => __( 'Pickup', 'woocommerce' ),
        ),
    ) );
	
	woocommerce_wp_select( array(
        'id'          => 'shipping_time',
        'label'       => __( 'Time', 'woocommerce' ),
        'value'       => $shipping_time,
        'options'     => array(
			'Delivery Wednesday 2 - 8 pm' => esc_html__( 'Delivery Wednesday 2 - 8 pm', 'woocommerce' ),
			'Pickup time Wednesday 2 - 8 pm' => esc_html__( 'Pickup time Wednesday 2 - 8 pm', 'woocommerce' ),
		),
    ) );
}

add_action( 'woocommerce_process_shop_order_meta', 'save_shipping_select_fields_to_order' );
function save_shipping_select_fields_to_order( $order_id ) {
    if ( isset( $_POST['shipping_type'] ) ) {
        update_post_meta( $order_id, '_shipping_type', sanitize_text_field( $_POST['shipping_type'] ) );
    }
	if ( isset( $_POST['shipping_time'] ) ) {
        update_post_meta( $order_id, '_shipping_time', sanitize_text_field( $_POST['shipping_time'] ) );
    }
}

add_action( 'init', 'reviews_custom_post_type');
function reviews_custom_post_type() {
    $label = array(
        'name'                => _x( 'Reviews', 'Post Type General Name', 'MemberProfile' ),
        'singular_name'       => _x( 'Review', 'Post Type Singular Name', 'MemberProfile' ),
        'menu_name'           => __( 'Reviews', 'MemberProfile' ),
        'parent_item_colon'   => __( 'Parent Review', 'MemberProfile' ),
        'all_items'           => __( 'All Reviews', 'MemberProfile' ),
        'view_item'           => __( 'View Review', 'MemberProfile' ),
        'add_new_item'        => __( 'Add New Review', 'MemberProfile' ),
        'add_new'             => __( 'Add New', 'MemberProfile' ),
        'edit_item'           => __( 'Edit Review', 'MemberProfile' ),
        'update_item'         => __( 'Update Review', 'MemberProfile' ),
        'search_items'        => __( 'Search Reviews', 'MemberProfile' ),
        'not_found'           => __( 'Not Found Review', 'MemberProfile' ),
        'not_found_in_trash'  => __( 'Not found in Review Trash', 'MemberProfile' ),
    );
	 
    $args = array(
        'labels'             => $label,
		'public'             => false, // Prevents the post type from being publicly visible
		'publicly_queryable' => false, // Disables queryable URLs for the post type
		'exclude_from_search' => true, // Excludes the post type from search results
		'show_ui'            => true,  // Allows access in the admin panel
		'show_in_menu'       => true,  // Makes it visible in the admin menu
		'capability_type'    => 'post',
		'menu_icon' => 'dashicons-star-filled',
		'rewrite'            => array( "slug" => "review", "with_front" => false ), // Disables permalinks entirely
		'has_archive'        => false,
		'hierarchical'       => false,
		'supports'           => array( 'title', 'editor', 'revisions', 'thumbnail'),
    );
      
    register_post_type( 'reviews', $args );
 
}

function ch_custom_admin_head() {
	$screen = get_current_screen();
?>
	<style>
		<?php if( "product" == $screen->id ) { ?>
		#edit-slug-box { display: none; }
		<?php } ?>
	</style>
<?php
}
add_action('admin_head', 'ch_custom_admin_head');

function ch_custom_login_head_scripts() {
	?>
	<style>
		.wp-login-register { display: none; }
		#login #nav { font-size: 0; }
		#login #nav .wp-login-lost-password { font-size: 13px; }
	</style>
	<?php
}
add_action("login_head", "ch_custom_login_head_scripts");

add_action("template_redirect", "ch_custom_template_redirects");
function ch_custom_template_redirects() {
	global $post;
	if( is_product() && $post->ID !== 999 ) { // 999 = Gift Card
		wp_redirect("/");
		exit;
	}
	
	/*if( isset($_GET["add_subs_items"]) ) {
		$cart_items = explode(",", $_GET["add_subs_items"]);
		foreach($cart_items as $product_id) {
			WC()->cart->add_to_cart( $product_id, 1 );
		}
		
		wp_redirect("/cart/");
		exit;
	}*/
}


add_action( 'woocommerce_login_form_end', 'custom_add_signup_text_link_no_reload' );
function custom_add_signup_text_link_no_reload() {
    echo '<p class="myaccount_custom_text">';
    echo 'Don’t have an account? <a href="#" id="open-registration-tab">Sign up</a>';
    echo '</p>';
}

add_action( 'woocommerce_register_form_end', 'custom_add_login_text_link' );
function custom_add_login_text_link() {
    echo '<p class="myaccount_custom_text">';
    echo 'Already have an account? <a href="#" id="open-login-tab">Log in now</a>';
    echo '</p>';
}

// Add name and confirm password fields
add_action( 'woocommerce_register_form_start', 'custom_add_name_field_to_registration' );
add_action( 'woocommerce_register_form', 'custom_add_confirm_password_field' );

function custom_add_name_field_to_registration() {
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_name"><?php _e( 'Name', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="name" id="reg_name" value="<?php if ( ! empty( $_POST['name'] ) ) echo esc_attr( $_POST['name'] ); ?>" />
    </p>
    <?php
}

function custom_add_confirm_password_field() {
    ?>
    <p class="form-row form-row-wide">
        <label for="reg_password2"><?php _e( 'Confirm Password', 'woocommerce' ); ?> <span class="required">*</span></label>
        <input type="password" class="input-text" name="password2" id="reg_password2" />
    </p>
    <?php
}

add_action( 'woocommerce_register_post', 'custom_validate_extra_registration_fields', 10, 3 );
function custom_validate_extra_registration_fields( $username, $email, $validation_errors ) {

    if ( empty( $_POST['name'] ) ) {
        $validation_errors->add( 'name_error', __( 'Full Name is required.', 'woocommerce' ) );
    }
    if ( isset( $_POST['password'] ) && $_POST['password'] !== $_POST['password2'] ) {
        $validation_errors->add( 'password_error', __( 'Passwords do not match.', 'woocommerce' ) );
    }

    return $validation_errors;
}


add_action( 'woocommerce_created_customer', 'custom_save_name_field' );
function custom_save_name_field( $customer_id ) {
    if ( isset( $_POST['name'] ) ) {
        update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['name'] ) );
        wp_update_user( array(
            'ID' => $customer_id,
            'display_name' => sanitize_text_field( $_POST['name'] )
        ) );
    }
}

add_action( 'wp_ajax_get_cart_item_count', 'get_cart_item_count_ajax' );
add_action( 'wp_ajax_nopriv_get_cart_item_count', 'get_cart_item_count_ajax' );
function get_cart_item_count_ajax() {
    echo WC()->cart->get_cart_contents_count();
    wp_die();
}

add_action('woocommerce_init', function () {
	// If session hasn't started yet, start it
    if (function_exists('WC') && WC()->session && !WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }
	
    if (isset($_POST['delivery_form_submit'])) {
        if (!isset(WC()->session)) {
            return;
        }

        // Save the values
        WC()->session->set('delivery_time', $_POST['delivery_time']);
        WC()->session->set('shipping_type', $_POST['shipping_type']);
		WC()->session->set('delivery_postal_code', $_POST['delivery_postal_code']);
		
		// remove pickup time from the session
		WC()->session->__unset('pickup_time');
		
		if( !empty($_POST["order_type"]) && $_POST["order_type"] === "one-time" ) {
			wp_redirect( home_url("/one-time-order/") );
			exit;
		}
		if( !empty($_POST["order_type"]) && $_POST["order_type"] === "subscription" ) {
			wp_redirect( home_url("/weekly-subscription/") );
			exit;
		}
    }
	if (isset($_POST['pickup_form_submit'])) {
        if (!isset(WC()->session)) {
            return;
        }

        // Save the values
        WC()->session->set('pickup_time', $_POST['pickup_time']);
        WC()->session->set('shipping_type', $_POST['shipping_type']);
		
		// remove delivery time and postal code from the session
		WC()->session->__unset('delivery_time');
		WC()->session->__unset('delivery_postal_code');
		
		if( !empty($_POST["order_type"]) && $_POST["order_type"] === "one-time" ) {
			wp_redirect( home_url("/one-time-order/") );
			exit;
		}
		if( !empty($_POST["order_type"]) && $_POST["order_type"] === "subscription" ) {
			wp_redirect( home_url("/weekly-subscription/") );
			exit;
		}
    }
	
	if( WC()->session ) {
		//echo WC()->session->get('shipping_type');
	}
});

/*add_action('template_redirect', 'redirect_checkout_if_less_than_6_items');
function redirect_checkout_if_less_than_6_items() {
    // Only run this on the checkout page
    if (is_checkout() && !is_wc_endpoint_url()) {
        // Get WooCommerce session
        $shipping_type = WC()->session->get('shipping_type');

		if( ! $shipping_type ) {
			// Perform redirect
            wp_safe_redirect( site_url('/select-meal-plan/') );
            exit;
		}
		
        // Check if shipping_type is 'Delivery'
        if ($shipping_type === 'Delivery') {
            // Get total quantity in the cart
            $cart_quantity = WC()->cart->get_cart_contents_count();

            // If less than 6 items, redirect to a specific page
            if ($cart_quantity < 6) {
                $redirect_url = site_url('/cart/?less_item_msg=1');

                // Perform redirect
                wp_safe_redirect($redirect_url);
                exit;
            }
        }
    }
}*/

add_action('template_redirect', 'ch_checkout_item_count_check_for_redirect');
function ch_checkout_item_count_check_for_redirect() {
	// Only run this on the checkout page
    if (is_checkout() && !is_wc_endpoint_url()) {
        $cart = WC()->cart->get_cart();
        $gift_card_product_id = 999;

        $non_gift_quantity = 0;
        $gift_card_quantity = 0;
		$has_subscription_product = false;
		
		if( class_exists('WC_Subscriptions_Cart') ) {
			// Check if cart contains a subscription product
    		if( WC_Subscriptions_Cart::cart_contains_subscription() ) {
				$has_subscription_product = true;
			}
		}

        foreach ($cart as $cart_item) {
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['quantity'];

            if ($product_id == $gift_card_product_id) {
                $gift_card_quantity += $quantity;
            } else {
                $non_gift_quantity += $quantity;
            }
			
			/*if( get_post_meta($product_id, '_gift_card', true) ) {
				// do stuff
			}*/
        }

        $shipping_type = WC()->session->get('shipping_type');
		
        // Allow checkout if only gift cards are in the cart
        if ($non_gift_quantity === 0) {
			
			if( $shipping_type ) {
				WC()->session->__unset('shipping_type');
			}
			if( $shipping_type === "Delivery" ) {
				if( WC()->session->get('delivery_time') ) {
					WC()->session->__unset('delivery_time');
				}
				if( WC()->session->get('delivery_postal_code') ) {
					WC()->session->__unset('delivery_postal_code');
				}
			} elseif( $shipping_type === "Pickup" ) {
				if( WC()->session->get('pickup_time') ) {
					WC()->session->__unset('pickup_time');
				}
			}
			
            return;
			
        }

        // Now check shipping_type only if there are non-gift items
        //$shipping_type = WC()->session->get('shipping_type');

		if( ! $shipping_type ) {
			// Perform redirect
            wp_safe_redirect( site_url('/select-meal-plan/') );
            exit;
		}
		
		if( ! $has_subscription_product ) {
			if ($shipping_type === 'Delivery' && $non_gift_quantity < 6) {
				// Redirect to cart page
				wp_safe_redirect( site_url('/cart/?less_item_msg=1') );
				exit;
			}
		}
    }
}

add_action('admin_init', 'ch_custom_gc_create_and_redeem_func');
function ch_custom_gc_create_and_redeem_func() {
	if( ! isset($_GET["gc_action"]) || empty($_GET["gc_action"]) || empty($_GET["user_id"]) ) {
		return;
	}
	
	if ( ! class_exists('WC_Gift_Cards') ) {
		//wc_add_notice( __( 'Gift Cards plugin not found.', 'woocommerce-gift-cards' ) );
        return;
    }
	
	$endPoint = site_url('/wp-json/wc/v3/gift-cards');
	$user = get_userdata( intval($_GET['user_id']) );
	$email = $user->user_email;
	
	// https://chefhack.mystagingwebsite.com/wp-admin/admin.php?page=wc-settings&tab=advanced&section=keys
	// Keys are generated from the user - Trevor
	$consumer_key = 'ck_75916083a245481f2945c44de234a7021681a262';
	$consumer_secret = 'cs_c2dbd85d8dfc6702ae8c6a15e8bbc56346180261';

	// Data to send
	$data = [
		'recipient' => $email,
		'sender'    => 'Chef Hack',
		'balance'   => 10,
	];

	// Encode the data as JSON
	$json_data = json_encode($data);

	// Initialize cURL session
	$ch = curl_init($endPoint);

	// Set cURL options
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, $consumer_key . ':' . $consumer_secret);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json'
	]);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	// Execute request
	$response = curl_exec($ch);
	$gc_info = json_decode($response, true);

	// Check for cURL error
	if (curl_errno($ch)) {
		echo 'Curl error: ' . curl_error($ch);
	} else {
		// Print the response
		/*echo 'Response: ' . $response;*/
	}

	// Close cURL session
	curl_close($ch);
	
	$gc_code = $gc_info["code"];
	
	$gc_results = WC_GC()->db->giftcards->query( array(
		'return' => 'objects',
		'code'   => $gc_code,
		'limit'  => 1
	) );

	if ( count( $gc_results ) > 0 ) {

		$gc_data = array_shift( $gc_results );
		$gc      = new WC_GC_Gift_Card( $gc_data );

		try {

			$gc->redeem( intval($_GET['user_id']) );
			// Re-init cart giftcards.
			/*WC_GC()->cart->destroy_cart_session();*/
			
			//wc_add_notice( __( 'The gift card has been added to your account.', 'woocommerce-gift-cards' ) );

		} catch ( Exception $e ) {
			//wc_add_notice( $e->getMessage(), 'error' );
		}

	} else {
		//wc_add_notice( __( 'Invalid gift card code.', 'woocommerce-gift-cards' ), 'error' );
	}
	
	wp_redirect( add_query_arg(['gc_10_credit' => 1, 'gc_user_id' => intval($_GET['user_id'])], admin_url('users.php')) );
    exit;
}

add_filter('manage_users_columns', 'ch_custom_user_column');
function ch_custom_user_column($columns) {
	unset($columns['posts']); // Remove Posts column
    $columns['gc_give_credit'] = 'Freezer Bag Credit';
    return $columns;
}

add_filter('manage_users_custom_column', 'ch_custom_user_column_content', 10, 3);
function ch_custom_user_column_content($value, $column_name, $user_id) {
	$user_obj = get_userdata( $user_id );
    if ( ($column_name === 'gc_give_credit') && in_array('customer', (array) $user_obj->roles) ) {
		$url = admin_url('users.php?gc_action=credit&user_id=' . $user_id);
		return '<a href="' . esc_url($url) . '" class="button button-primary" style="margin-top: 5px;">$10 Freezer Bag Credit</a>';
    }
    return $value;
}

add_action('admin_notices', 'ch_user_gc_action_notice');
function ch_user_gc_action_notice() {
    if ( ! empty($_GET['gc_10_credit']) ) {
		$user_obj = get_userdata( $_GET['gc_user_id'] );
        echo '<div class="notice notice-success is-dismissible"><p>$10 credit has been added to <strong>' . $user_obj->display_name . '</strong>\'s balance.</p></div>';
    }
}

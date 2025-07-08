<?php
function ch_admin_scripts( $hook_suffix ) {
	$screen = get_current_screen();
	
    if ( $hook_suffix === $screen->id ) {
		wp_register_script( "jquery-ui-timepicker", '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array( 'jquery', 'jquery-ui-datepicker' ) );
		wp_enqueue_script( 'jquery-ui-timepicker' );
		
		wp_register_style( 'jquery-ui-timepicker-style', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css' );
		wp_enqueue_style( 'jquery-ui-timepicker-style' );
		
		wp_enqueue_script( 'jquery-ui-datepicker' );
		
		wp_register_style( 'jquery-ui-datepicker-style', '//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui-datepicker-style' );
    }
}
add_action( 'admin_enqueue_scripts', 'ch_admin_scripts' );

function ch_custom_admin_scripts() {
	?>
	<style type="text/css">
		#order_line_items .item .display_meta tr th, .wc-order-bulk-actions .button.refund-items, #menu-posts-product .wp-submenu li:nth-last-child(3) {
			display: none;
		}
		.meal-type-table tbody tr:nth-child(2n) {
			background: #f2f2f2;
		}
		.date-selection-row, .orders-list-table {
			padding: 20px;
		}
		
		.post-type-shop_order .tablenav.top .actions, .post-type-shop_order .tablenav.bottom, .post-type-shop_order td#cb, .post-type-shop_order .check-column, .post-type-shop_order #subscription_relationship, .post-type-shop_order .column-subscription_relationship, .post-type-shop_order .order-preview, .post-type-shop_order #posts-filter .search-box, .post-type-shop_order .tablenav.top br.clear, .post-type-shop_subscription .search-box, .post-type-shop_subscription .tablenav .actions:not(.bulkactions) {
			display: none;
		}
		.post-type-shop_order .tablenav.top {
			position: relative;
			height: auto;
		}
		.post-type-shop_order .tablenav.top .tablenav-pages {
			margin-top: -35px;
		}
		.post-type-shop_order .manage-column.sortable a {
			color: #000;
		}
		.post-type-shop_order .manage-column.sortable a, .post-type-shop_order .manage-column.sortable a span {
			cursor: text;
		}
		.post-type-shop_order th.desc:hover span.sorting-indicator {
			visibility: hidden;
		}
		.filter-orders-type {
			margin-bottom: 10px;
			font-size: 14px;
		}
		.filter-orders-type a {
			text-decoration: none;
			border: 1px solid #acacac;
			padding: 3px 15px;
			display: inline-block;
			line-height: 25px;
		}
		.filter-orders-type a.active-sec {
			background: #2271b1;
			color: #fff;
		}
		.woocommerce_page_woo-cart-abandonment-recovery #addedit_template tr:nth-child(6), .woocommerce_page_woo-cart-abandonment-recovery #addedit_template tr:nth-last-child(2), .woocommerce_page_woo-cart-abandonment-recovery #addedit_template tr:last-child {
			display: none;
		}
		#order_data {
			position: relative;
		}
		.form-field.shipping_type_field {
			position: absolute;
			right: 290px;
			bottom: 15px;
			max-width: 100px;
		}
		.form-field.shipping_time_field {
			position: absolute;
			right: 25px;
			bottom: 15px;
			max-width: 250px;
		}
		.wp-list-table.users .column-user_jetpack {
			display: none;
		}
	</style>
	<?php
		$screen = get_current_screen();
		if ( ("product_page_orders-delivery-for-the-week" === $screen->id) || ("product_page_orders-meal-for-the-week" === $screen->id) ) {
	?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('.reporting-date').datetimepicker({
					dateFormat : 'yy-mm-dd',
					controlType: 'select',
					oneLine: true,
					timeFormat: 'hh:mm tt',
					hour: 17
				});
				
				jQuery(".filter-orders-type a").on("click", function(e) {
					e.preventDefault();
					jQuery(".filter-orders-type a").removeClass("active-sec");
					jQuery(this).addClass("active-sec");
					var delivery_type = jQuery(this).attr("data-order-type");
					if( delivery_type == "all" ) {
						jQuery('tr[data-delivery-type="Delivery"]').show();
						jQuery('tr[data-delivery-type="Pickup"]').show();
						var pdf_report_download_url = jQuery(".pdf-download-btn").attr("href");
						var csv_report_download_url = jQuery(".csv-download-btn").attr("href");
						if( pdf_report_download_url.includes("transportation") && csv_report_download_url.includes("transportation") ) {
							var pdf_report_url_arr = pdf_report_download_url.split("&transportation");
							var new_pdf_report_url = pdf_report_url_arr[0];
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							
							var csv_report_url_arr = csv_report_download_url.split("&transportation");
							var new_csv_report_url = csv_report_url_arr[0];
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						} else {
							// no changes
						}
					}
					if( delivery_type == "delivery" ) {
						jQuery('tr[data-delivery-type="Delivery"]').show();
						jQuery('tr[data-delivery-type="Pickup"]').hide();
						var pdf_report_download_url = jQuery(".pdf-download-btn").attr("href");
						var csv_report_download_url = jQuery(".csv-download-btn").attr("href");
						if( pdf_report_download_url.includes("transportation") && csv_report_download_url.includes("transportation") ) {
							var pdf_report_url_arr = pdf_report_download_url.split("transportation=");
							pdf_report_url_arr[1] = "delivery";
							var new_pdf_report_url = pdf_report_url_arr.join("transportation=");
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							
							var csv_report_url_arr = csv_report_download_url.split("transportation=");
							csv_report_url_arr[1] = "delivery";
							var new_csv_report_url = csv_report_url_arr.join("transportation=");
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						} else {
							var new_pdf_report_url = pdf_report_download_url+"&transportation=delivery";
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							var new_csv_report_url = csv_report_download_url+"&transportation=delivery";
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						}
					}
					if( delivery_type == "pickup" ) {
						jQuery('tr[data-delivery-type="Pickup"]').show();
						jQuery('tr[data-delivery-type="Delivery"]').hide();
						var pdf_report_download_url = jQuery(".pdf-download-btn").attr("href");
						var csv_report_download_url = jQuery(".csv-download-btn").attr("href");
						if( pdf_report_download_url.includes("transportation") && csv_report_download_url.includes("transportation") ) {
							var pdf_report_url_arr = pdf_report_download_url.split("transportation=");
							pdf_report_url_arr[1] = "pickup";
							var new_pdf_report_url = pdf_report_url_arr.join("transportation=");
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							
							var csv_report_url_arr = csv_report_download_url.split("transportation=");
							csv_report_url_arr[1] = "pickup";
							var new_csv_report_url = csv_report_url_arr.join("transportation=");
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						} else {
							var new_pdf_report_url = pdf_report_download_url+"&transportation=pickup";
							jQuery(".pdf-download-btn").attr("href", new_pdf_report_url);
							var new_csv_report_url = csv_report_download_url+"&transportation=pickup";
							jQuery(".csv-download-btn").attr("href", new_csv_report_url);
						}
					}
				});
			});
		</script>
	<?php
	}
	
	//if( "product_page_orders-delivery-for-the-week" == $screen->id ) {
		//if( isset($_GET["single_order"]) && isset($_GET["vmode"]) && $_GET["vmode"] == "print" ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(".flava-print-order").on("click", function() {
						window.frames["print_frame"].document.body.innerHTML = document.getElementById("order-print").innerHTML;
						window.frames["print_frame"].window.focus();
						window.frames["print_frame"].window.print();
					});
				});
			</script>
			<?php
		//}
	//}
	
	if( ($screen->id == "shop_order") || ($screen->id == "shop_subscription") ) {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				/*if( jQuery("#order_line_items").length ) {
					jQuery(".item").each(function() {
						var item_name = jQuery(this).find(".wc-order-item-name").text();
						if( item_name.includes("Meals Plan") ) {
							jQuery('<button type="button" class="subs-customize button-primary">Customize</button>').appendTo( jQuery(this).find(".name").find(".display_meta").find("td") );
						}
					});
				}*/
				
				jQuery("body").on("click", ".subs-customize", function() {
					jQuery(this).closest(".name").find(".edit").show();
					jQuery(this).closest(".name").find(".edit").find( "input[value=_child_products]" ).hide();
					jQuery(this).closest(".name").find(".edit").find( ".remove_order_item_meta" ).hide();
					jQuery(this).closest(".name").find(".edit").find( ".add_order_item_meta" ).hide();
					jQuery(this).closest(".name").find(".edit").find( "textarea" ).css("min-height", "150px");
				});
				
				jQuery("#woocommerce-order-items").on("click", "a.edit-order-item", function() {
					if( jQuery(this).parents('.item').length ) {
						var item_title = jQuery(this).closest(".item").find(".name").attr("data-sort-value");
						if( item_title.includes("Meals Plan") ) {
							jQuery(this).closest(".item").find(".name").find(".edit").find( "input[value=_child_products]" ).hide();
							jQuery(this).closest(".item").find(".name").find(".edit").find( ".remove_order_item_meta" ).hide();
							jQuery(this).closest(".item").find(".name").find(".edit").find( ".add_order_item_meta" ).hide();
							jQuery(this).closest(".item").find(".name").find(".edit").find( "textarea" ).css("min-height", "130px");
						}
					}
				});
			});
		</script>
		<?php
	}
	
}
add_action('admin_head', 'ch_custom_admin_scripts');

add_action('admin_menu', 'ch_register_weekly_report_submenu_page'); 
function ch_register_weekly_report_submenu_page() {
	add_submenu_page( 
		'edit.php?post_type=product',
        'Orders for the week',
        'View Weekly Orders',
        'manage_options',
        'orders-delivery-for-the-week',
        'orders_delivery_for_the_week_callback',
    );
    add_submenu_page( 
		'edit.php?post_type=product',
        'Orders for the week',
        'Orders Meal Type',
        'manage_options',
        'orders-meal-for-the-week',
        'orders_meal_for_the_week_callback',
    );
}

function orders_meal_for_the_week_callback() {
	$time_start = '17:00:00';
	$time_end   = '17:00:00';
	$prev_friday = date( 'Y-m-d', strtotime( 'previous friday' ) );
	$from_date = $prev_friday;
	$to_date = date("Y-m-d");
	$report_gen = false;
	$pdf_gen = false;
	$show_from_time = "05:00 pm";
	$show_to_time = "05:00 pm";

	$orders_list = '<div class="orders-list-table">';
	
	if( isset($_POST["order_date_range"]) || ( isset($_GET["sdate"]) && isset($_GET["edate"]) ) ) {
		if( isset($_POST["order_date_range"]) ) {
			$from_date_arr = explode(" ", $_POST["from_date"]);
			$from_date = $from_date_arr[0];
			$from_time = $from_date_arr[1];
			$time_start = $from_time.":00";
			$show_from_time = $from_time.' '.$from_date_arr[2];
			
			$to_date_arr = explode(" ", $_POST["to_date"]);
			$to_date = $to_date_arr[0];
			$to_time = $to_date_arr[1];
			$time_end = $to_time.":00";
			$show_to_time = $to_time.' '.$to_date_arr[2];
			
			$from_timestamp = strtotime($from_date);
			$show_from_date = date("M d, Y ", $from_timestamp);
			$to_timestamp = strtotime($to_date);
			$show_to_date = date("M d, Y ", $to_timestamp);
		}
		if( isset($_GET["sdate"]) && isset($_GET["edate"]) ) {
			$from_date_arr = explode(" ", $_GET["sdate"]);
			$from_date = $from_date_arr[0];
			$from_time = $from_date_arr[1];
			$time_start = $from_time.":00";
			$show_from_time = $from_time.' '.$from_date_arr[2];
			
			$to_date_arr = explode(" ", $_GET["edate"]);
			$to_date = $to_date_arr[0];
			$to_time = $to_date_arr[1];
			$time_end = $to_time.":00";
			$show_to_time = $to_time.' '.$to_date_arr[2];

			if( isset($_GET["or_ac"]) && $_GET["or_ac"] == "download" ) {
				$report_gen = true;
			}
			if( isset($_GET["or_dl_ac"]) && $_GET["or_dl_ac"] == "pdf" ) {
				$pdf_gen = true;
			}
			
			$from_timestamp = strtotime($from_date);
			$show_from_date = date("M d, Y ", $from_timestamp);
			$to_timestamp = strtotime($to_date);
			$show_to_date = date("M d, Y ", $to_timestamp);
		}		
	} else {
		$show_from_date = date("M d, Y ", strtotime( 'previous friday' ));
		$show_to_date = date("M d, Y ");
	}

	$q_time_start = date("H:i:s", strtotime($time_start));
	$q_time_end = date("H:i:s", strtotime($time_end));
	
	$start_date = date( $from_date.' '.$q_time_start );
	$end_date = date( $to_date.' '.$q_time_end );
	// 'status' => array( 'wc-processing','wc-completed','wc-on-hold' )
	$args = array(
		'orderby'       => 'id',
		'order'         => 'DESC',
		'posts_per_page' => -1,
		'status'        => array( 'wc-processing','wc-on-hold' ),
		'date_created'  => $start_date.'...'.$end_date
	);

	$orders = wc_get_orders( $args );

	if ( ! empty ( $orders ) ) {
		if( $report_gen ) {
			$data_rows = array();
		}

		$orders_list .= '<div style="display: flex; justify-content: space-between; align-items: center;"><div style="font-size: 14px;"><strong>From:</strong> '.$show_from_date.$show_from_time.' &nbsp;&nbsp;&nbsp; <strong>To:</strong> '.$show_to_date.$show_to_time.'</div>';
		//$orders_list .= '<div style="text-align: right; margin-bottom: 15px;"><a href="/wp-admin/edit.php?post_type=product&page=orders-for-the-week&or_ac=download&sdate='.date( "Y-m-d", strtotime($start_date) ).'&edate='.date( "Y-m-d", strtotime($end_date) ).'" class="button button-secondary button-large">Download CSV</a></div>';
		$orders_list .= '<div style="text-align: right; margin-bottom: 15px;"><a href="/wp-admin/edit.php?post_type=product&page=orders-meal-for-the-week&or_dl_ac=pdf&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large">Download PDF</a></div>';
		$orders_list .= '</div>';
		$orders_list .= '<br>';
		$orders_list .= '<table class="wp-list-table widefat fixed striped meal-type-table">';
		$orders_list .= '<thead>
								<tr>
									<td style="font-size: 15px;"><strong>Meal</strong></td>
									<td style="font-size: 15px;"><strong>Quantity</strong></td>
								</tr>
							</thead>';
		
		if( $pdf_gen ) {
			// create new PDF document
			$pdf = new MC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

			// set document information
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor('FlavaDaddy Team');
			$pdf->SetTitle('FlavaDaddy Team');
			$pdf->SetSubject('Order Info');
			$pdf->SetKeywords('Order, PDF, FlavaDaddy, Fully Prepared Meals');

			// set default header data
			$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 010', PDF_HEADER_STRING);

			// set header and footer fonts
			$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
			$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
			// remove default header/footer
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);

			// set default monospaced font
			$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

			// set margins
			$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
			$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
			$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

			// set auto page breaks
			$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

			// set image scale factor
			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

			// set font
			$pdf->SetFont('helvetica', '', 10);
			
			// add a page
			$pdf->AddPage();
		}
		
		$meal_items = array();
		//$user_subs = '';
		foreach( $orders as $order ) {
			foreach($order->get_items() as $item_id => $item ) {
				$product_id = $item->get_product_id();
				$product_name = $item->get_name();
				$quantity = $item->get_quantity();
				$item_key = $product_id;
				//$item_key = $product_name;
				
				/*if( $item->get_meta('_child_products', true) ) {
					$item_key_enc = md5($item_key);
					if( ! array_key_exists($item_key_enc, $meal_items) ) {
						$meal_items[$item_key_enc] = $product_name."||".$quantity;
					} else {
						$item_qty = explode("||", $meal_items[$item_key_enc])[1];
						$new_qty = (int) $item_qty + $quantity;
						$meal_items[$item_key_enc] = $product_name."||".$new_qty;
					}
					$child_products = $item->get_meta('_child_products', true);
					$child_products_arr = preg_split("/\r\n|\n|\r/", $child_products);
					foreach($child_products_arr as $child_product) {
						$cp_info_arr = explode(" X ", $child_product);
						if( count($cp_info_arr) > 1 ) {
							$cp_name = $cp_info_arr[0];
							$cp_qty = $cp_info_arr[1];
							//echo $cp_name.$cp_qty."??<br>";
							$item_key = html_entity_decode($cp_name);
							
							$item_key_enc = md5($item_key);
							if( ! array_key_exists($item_key_enc, $meal_items) ) {
								$meal_items[$item_key_enc] = $cp_name."||".$cp_qty;
							} else {
								$item_qty = explode("||", $meal_items[$item_key_enc])[1];
								$new_qty = (int) $item_qty + $cp_qty;
								$meal_items[$item_key_enc] = $cp_name."||".$new_qty;
							}
						}
					}
					
				/*} else {*/
				
					if( $item->get_meta('meal_extras', true) ) {
						$item_key .= $item->get_meta('meal_extras', true);
						$product_name .= " (".preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras', true)).")";
					}

					$item_key_enc = md5($item_key);
					if( ! array_key_exists($item_key_enc, $meal_items) ) {
						$meal_items[$item_key_enc] = $product_name."||".$quantity;
					} else {
						$item_qty = explode("||", $meal_items[$item_key_enc])[1];
						$new_qty = (int) $item_qty + $quantity;
						$meal_items[$item_key_enc] = $product_name."||".$new_qty;
					}
					
				/*}*/
				
				/*$product = wc_get_product( $product_id );
				if( $product->is_type( 'subscription' ) ) {
					
				}*/
			}
			/*Chili Lime Chicken Poke Bowl - x2
			Protein Ganache Rice Krispy (single) - x2
			Spicy Beef Noodz - x1
			Chimichurri Steak &#038; Potatoes - x1
			Teriyaki Turkey Bowl - x1
			Texas Smokey BBQ Chicken - x1*/
		}
		
		$pdf_table_rows = '';
		
		sort($meal_items);
		foreach($meal_items as $meal_item) {
			$meal_item_arr = explode("||", $meal_item);
			$orders_list .= '<tr>
								<td style="font-size: 15px;">'.$meal_item_arr[0].'</td>
								<td style="font-size: 15px;"><strong>'.$meal_item_arr[1].'</strong></td>
							</tr>';
			
			if( $pdf_gen ) {
				$pdf_table_rows .= '<tr>
										<td>'.$meal_item_arr[0].'</td>
										<td><strong>'.$meal_item_arr[1].'</strong></td>
									</tr>';
			}
			
			if( $report_gen ) {
				$row = array($meal_item_arr[0], $meal_item_arr[1]);
				$data_rows[] = $row;
			}
		}

		if( $pdf_gen ) {
			$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
						<tr>
							<td style="text-align: center;">
								<img src="'.home_url('/wp-content/uploads/2022/10/logo.png').'" />
								<br><br>
							</td>
						</tr>
						<tr>
							<td>
								<table border="1" cellpadding="10" cellspacing="0" style="width:100%;">
									<thead>
										<tr>
											<td style="font-size: 15px;"><strong>Meal</strong></td>
											<td style="font-size: 15px;"><strong>Quantity</strong></td>
										</tr>
									</thead>
									<tbody>
									'.$pdf_table_rows.'
									</tbody>
								</table>
							</td>
						</tr>
					</table>';
			
			$pdf->writeHTML($html, true, false, true, false, '');
			
			// reset pointer to the last page
			$pdf->lastPage();

			$upload_dir = wp_upload_dir();
			$pdf_dir = $upload_dir['basedir'].'/order-pdfs';
			$pdf_dir_url = $upload_dir['baseurl'].'/order-pdfs';
			if( ! file_exists( $pdf_dir ) ) {
				wp_mkdir_p( $pdf_dir );
			}

			// delete the current PDF if already exist
			if( file_exists($pdf_dir.'/Orders-meal-type-'.time().'.pdf') ) {
				unlink($pdf_dir.'/Orders-meal-type-'.time().'.pdf');
			}

			ob_end_clean();

			//Close and output PDF document
			$pdf->Output($pdf_dir.'/Orders-meal-type-'.time().'.pdf', 'D');
		}
		
		if( $report_gen ) {
			$domain = $_SERVER['SERVER_NAME'];
			$filename = 'orders-meals' . $domain . '-' . time() . '.csv';

			$header_columns = array('Meal','Quantity');
			header('Content-Type: application/csv');
			header('Content-Disposition: attachment; filename='.$filename);

			ob_end_clean();

			$fh = fopen( 'php://output', 'w' );

			fputcsv( $fh, $header_columns );

			foreach ( $data_rows as $data_row ) {
				fputcsv( $fh, $data_row );
			}

			exit();
		}

		$orders_list .= '</table>';
	}
	?>
	<div class="date-selection-row" style="padding-bottom: 0;">
		<h1 style="margin-bottom: 0;">Orders of this week</h1>
		<form action="" method="POST" style="margin-top: 20px;">
			<input type="text" name="from_date" class="reporting-date" placeholder="Start Date" value="<?php //echo $from_date; ?>" />
			<input type="text" name="to_date" class="reporting-date" placeholder="End Date" value="<?php //echo $to_date; ?>" />
			<br><br>
			<input type="submit" name="order_date_range" class="button button-primary button-large" value="Submit" />
		</form>
	</div>
	<?php
	$orders_list .= '</div>';
	echo $orders_list;
}

function orders_delivery_for_the_week_callback() {
	if( isset($_GET["single_order"]) && $_GET["single_order"] != "" ) {
		if( isset($_GET["vmode"]) && $_GET["vmode"] == "print" ) {
			$order_id = $_GET["single_order"];
			$order = wc_get_order($order_id);
			echo '<div id="order-print" style="display: inline-block; margin: 10px 0 0 0;"><div style="width: 384px; height: 576px; background: #ffffff; border: 1px solid #e6e6e6; padding: 15px 10px; font-size: 14px; line-height: 20px;">';
			$items_list = '';
			$plan_type = 'One Time';
			foreach ($order->get_items() as $item_id => $item ) {
				$product_id = $item->get_product_id();
				$product = wc_get_product( $product_id );
				if ( $product->is_type( 'subscription' ) ) {
					$plan_type = 'Subscription';
				}
				
				$item_ext = '';
				if( $item->get_meta('meal_extras') ) {
					$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
					$item_ext = ' <span>('.$item_extra_info.')</span>';
				}
				$item_name_qty = '   '.$item->get_name().' x '.$item->get_quantity().strip_tags($item_ext);
				$item_name = $item->get_name();
				if( strlen($item_name_qty) > 52 ) {
					$total_chars = strlen($item_name_qty);
					$del_chars = $total_chars - 52;
					$del_chars_with_dots = $del_chars + 3; // 3 dots
					$item_name = substr($item->get_name(), 0, -$del_chars_with_dots)."...";
				}
				$items_list .= ' &nbsp; '.$item_name.'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
				if( $product->is_type( 'subscription' ) && $item->get_meta('_child_products', true) ) {
					$items_list .= '<span style="display: inline-block; margin-left: 15px; font-size: 98%;">'.nl2br( $item->get_meta('_child_products', true) ).'</span><br>';
				}
			}
			echo '<span>Plan Type: '.$plan_type.'</span><br>';
			echo '<span>'.get_post_meta( $order->get_id(), '_shipping_time',  true ).'</span><br>';
			echo '<span>Name: '.$order->get_billing_first_name().' '.$order->get_billing_last_name().'</span><br><br>';
			echo '<span><strong>Address:</strong> <br>'.$order->get_formatted_shipping_address().'</span><br><br>';
			echo '<span><strong>Items:</strong> </span><br>';
			
			echo $items_list;
			
			if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
				echo '<br><span>Insulated bag: <strong>Yes</strong></span><br>';
			}
			echo '</div></div><span class="dashicons dashicons-printer flava-print-order" style="margin: 15px 0 0 10px; cursor: pointer; font-size: 30px;"></span>';
			echo '<br><iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>';
		} else {
			$order_id = $_GET["single_order"];
			echo '<h2>Order #'.$order_id.' <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&single_order='.$order_id.'&dl_pdf=1" style="text-decoration: none;"><span class="dashicons dashicons-pdf" style="margin-left: 10px;"></span></a></h2>';
			echo '<hr />';

			$order = wc_get_order($order_id);

			$customer_phone = '';
			if( $order->get_billing_phone() ) {
				$customer_phone = '('.$order->get_billing_phone().')';
			}
			echo '<p style="font-size: 15px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' '.$customer_phone.'</p>';
			//echo '<p style="font-size: 15px;"><strong>Phone:</strong> '.$order->get_billing_phone().'</p>';

			$order_items = '<p style="font-size: 15px;"><strong>Items:</strong><br>';
			foreach ($order->get_items() as $item_id => $item ) {
				$item_ext = '';
				if( $item->get_meta('meal_extras') ) {
					$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
					$item_ext = ' <span style="color: #c6731c;">('.$item_extra_info.')</span>';
				}
				$order_items .= ' &nbsp; '.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
			}
			echo $order_items.'</p>';

			echo '<p style="font-size: 15px;"><strong>Time:</strong><br>';
			echo get_post_meta( $order->get_id(), '_shipping_time',  true )."</p>";

			echo '<p style="font-size: 15px;"><strong>Address:</strong><br>';
			echo $order->get_formatted_shipping_address()."</p>";

			if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
				echo '<p style="font-size: 15px;">Insulated Bag with Ice: <strong>Yes</strong></p>';
			}

			if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
				echo '<p style="font-size: 15px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
			}

			if( $order->get_customer_note() ) {
				echo "<strong>Note:</strong> ".$order->get_customer_note();
			}
		}
	} else {
		$time_start = '17:00:00';
		$time_end   = '17:00:00';
		$prev_friday = date( 'Y-m-d', strtotime( 'previous friday' ) );
		$from_date = $prev_friday;
		$to_date = date("Y-m-d");
		$report_gen = false;
		$xcel_gen = false;
		$pdf_gen = false;
		$show_from_time = "05:00 pm";
		$show_to_time = "05:00 pm";
		$ord_type = false;
		$trns_type = false;

		$orders_list = '<div class="orders-list-table">';

		if( isset($_GET["transportation"]) && $_GET["transportation"] != "" ) {
			$ord_type = $_GET["transportation"];
		}
		
		if( isset($_POST["order_date_range"]) || ( isset($_GET["sdate"]) && isset($_GET["edate"]) ) ) {
			if( isset($_POST["order_date_range"]) ) {
				$from_date_arr = explode(" ", $_POST["from_date"]);
				$from_date = $from_date_arr[0];
				$from_time = $from_date_arr[1];
				$time_start = $from_time.":00";
				$show_from_time = $from_time.' '.$from_date_arr[2];

				$to_date_arr = explode(" ", $_POST["to_date"]);
				$to_date = $to_date_arr[0];
				$to_time = $to_date_arr[1];
				$time_end = $to_time.":00";
				$show_to_time = $to_time.' '.$to_date_arr[2];

				$from_timestamp = strtotime($from_date);
				$show_from_date = date("M d, Y ", $from_timestamp);
				$to_timestamp = strtotime($to_date);
				$show_to_date = date("M d, Y ", $to_timestamp);
			}
			if( isset($_GET["sdate"]) && isset($_GET["edate"]) ) {
				$from_date_arr = explode(" ", $_GET["sdate"]);
				$from_date = $from_date_arr[0];
				$from_time = $from_date_arr[1];
				$time_start = $from_time.":00";
				$show_from_time = $from_time.' '.$from_date_arr[2];

				$to_date_arr = explode(" ", $_GET["edate"]);
				$to_date = $to_date_arr[0];
				$to_time = $to_date_arr[1];
				$time_end = $to_time.":00";
				$show_to_time = $to_time.' '.$to_date_arr[2];

				if( isset($_GET["or_ac"]) && $_GET["or_ac"] == "download" ) {
					$report_gen = true;
				}
				/*if( isset($_GET["wtdo"]) && $_GET["wtdo"] == "exportdel" ) {
					$xcel_gen = true;
				}*/
				if( isset($_GET["or_dl_ac"]) && $_GET["or_dl_ac"] == "pdf" ) {
					$pdf_gen = true;
				}

				$from_timestamp = strtotime($from_date);
				$show_from_date = date("M d, Y ", $from_timestamp);
				$to_timestamp = strtotime($to_date);
				$show_to_date = date("M d, Y ", $to_timestamp);
			}		
		} else {
			$show_from_date = date("M d, Y ", strtotime( 'previous friday' ));
			$show_to_date = date("M d, Y ");
		}
		
		if( isset($_GET["wtdo"]) && $_GET["wtdo"] == "exportdel" ) {
			$xcel_gen = true;
			$trns_type = "delivery";
		}

		$q_time_start = date("H:i:s", strtotime($time_start));
		$q_time_end = date("H:i:s", strtotime($time_end));

		$start_date = date( $from_date.' '.$q_time_start );
		$end_date = date( $to_date.' '.$q_time_end );
		$args = array(
			'orderby'       => 'id',
			'order'         => 'DESC',
			'posts_per_page' => -1,
			'status'        => array( 'wc-processing','wc-on-hold' ),
			'date_created'  => $start_date.'...'.$end_date
		);

		$orders = wc_get_orders( $args );

		if ( ! empty ( $orders ) ) {
			if( $report_gen || $xcel_gen ) {
				$data_rows = array();
			}
			
			if( isset($_GET["vmode"]) && $_GET["vmode"] == "print" ) {
				echo '<div id="order-print" style="display: inline-block; margin: 10px 0 0 0;">';
				foreach ( $orders as $order ) {
					echo '<div style="width: 384px; height: 576px; background: #ffffff; border: 1px solid #e6e6e6; padding: 15px 10px; font-size: 14px; line-height: 20px;">';
					$items_list = '';
					$plan_type = 'One Time';
					foreach ($order->get_items() as $item_id => $item ) {
						$product_id = $item->get_product_id();
						$product = wc_get_product( $product_id );
						if ( $product->is_type( 'subscription' ) ) {
							$plan_type = 'Subscription';
						}

						$item_ext = '';
						if( $item->get_meta('meal_extras') ) {
							$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
							$item_ext = ' <span>('.$item_extra_info.')</span>';
						}
						$item_name_qty = '   '.$item->get_name().' x '.$item->get_quantity().strip_tags($item_ext);
						$item_name = $item->get_name();
						if( strlen($item_name_qty) > 52 ) {
							$total_chars = strlen($item_name_qty);
							$del_chars = $total_chars - 52;
							$del_chars_with_dots = $del_chars + 3; // 3 dots
							$item_name = substr($item->get_name(), 0, -$del_chars_with_dots)."...";
						}
						$items_list .= ' &nbsp; '.$item_name.'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
						if( $product->is_type( 'subscription' ) && $item->get_meta('_child_products', true) ) {
							$items_list .= '<span style="display: inline-block; margin-left: 15px; font-size: 98%;">'.nl2br( $item->get_meta('_child_products', true) ).'</span><br>';
						}
					}
					echo '<span>Plan Type: '.$plan_type.'</span><br>';
					echo '<span>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</span><br>';
					echo '<span>Name: '.$order->get_billing_first_name().' '.$order->get_billing_last_name().'</span><br><br>';
					echo '<span><strong>Address:</strong> <br>'.$order->get_formatted_shipping_address().'</span><br><br>';
					echo '<span><strong>Items:</strong> </span><br>';

					echo $items_list;
					if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
						echo '<br><span>Insulated bag: Yes</span><br>';
					}

					echo '</div>';
				}
				echo '</div><span class="dashicons dashicons-printer flava-print-order" style="margin: 15px 0 0 10px; cursor: pointer; font-size: 30px;"></span>';
				echo '<br><iframe name="print_frame" width="0" height="0" frameborder="0" src="about:blank"></iframe>';
			} else {

				$orders_list .= '<div style="display: flex; justify-content: space-between; align-items: center;"><div style="font-size: 14px;"><strong>From:</strong> '.$show_from_date.$show_from_time.' &nbsp;&nbsp;&nbsp; <strong>To:</strong> '.$show_to_date.$show_to_time.'</div>';
				$orders_list .= '<div style="text-align: right; margin-bottom: 5px;"><a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&vmode=print&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large csv-export-btn">Print Deliveries</a> <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&wtdo=exportdel&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large csv-export-btn">Export Deliveries</a> <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&or_ac=download&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large csv-download-btn">Download CSV</a> <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&or_dl_ac=pdf&sdate='.$from_date.' '.$show_from_time.'&edate='.$to_date.' '.$show_to_time.'" class="button button-secondary button-large pdf-download-btn">Download PDF</a></div></div>';

				$orders_list .= '<div class="filter-orders-type"><a href="#" class="active-sec" data-order-type="all">All</a> <a href="#" data-order-type="delivery">Delivery</a> <a href="#" data-order-type="pickup">Pickup</a></div>';

				$orders_list .= '<table class="wp-list-table widefat fixed striped table-view-list posts">';
				$orders_list .= '<thead>
										<tr>
											<td><strong>Order</strong></td>
											<td><strong>Date</strong></td>
											<td width="300px"><strong>Items</strong></td>
											<td><strong>Insulated Bag</strong></td>
											<td><strong>Status</strong></td>
											<td><strong>Type</strong></td>
											<td><strong>Time</strong></td>
											<td><strong>Address</strong></td>
											<td><strong>Total</strong></td>
										</tr>
									</thead>';

				if( $pdf_gen ) {
					// create new PDF document
					$pdf = new MC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

					// set document information
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('FlavaDaddy Team');
					$pdf->SetTitle('FlavaDaddy Team');
					$pdf->SetSubject('Order Info');
					$pdf->SetKeywords('Order, PDF, FlavaDaddy, Fully Prepared Meals');

					// set default header data
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 010', PDF_HEADER_STRING);

					// set header and footer fonts
					$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
					$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
					// remove default header/footer
					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);

					// set default monospaced font
					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

					// set margins
					$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

					// set auto page breaks
					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

					// set image scale factor
					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

					// set font
					$pdf->SetFont('helvetica', '', 10);
				}

				foreach ( $orders as $order ) {
					$order_date = date( "M d, Y", strtotime( $order->get_date_created() ) );

					$insulated_bag = 'No';
					if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
						$insulated_bag = 'Yes';
					}

					$order_items = '';
					foreach ($order->get_items() as $item_id => $item ) {
						$item_ext = '';
						if( $item->get_meta('meal_extras') ) {
							$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
							$item_ext = ' <span style="color: #c6731c;">('.$item_extra_info.')</span>';
						}
						$order_items .= ''.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
					}

					$orders_list .= '<tr data-delivery-type="'.get_post_meta( $order->get_id(), '_shipping_type',  true ).'">
											<td><a href="/wp-admin/post.php?post='.$order->get_id().'&action=edit" target="_blank"><strong>#'.$order->get_id().' '.$order->get_billing_first_name().'</strong></a><br><a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&single_order='.$order->get_id().'" target="_blank">View</a> | <a href="/wp-admin/edit.php?post_type=product&page=orders-delivery-for-the-week&single_order='.$order->get_id().'&vmode=print" target="_blank">Print</a></td>
											<td>'.$order_date.'</td>
											<td>'.$order_items.'</td>
											<td>'.$insulated_bag.'</td>
											<td class="order_status"><span class="order-status status-processing">'.ucwords($order->get_status()).'</span></td>
											<td>'.get_post_meta( $order->get_id(), '_shipping_type',  true ).'</td>
											<td>'.get_post_meta( $order->get_id(), '_shipping_time',  true ).'</td>
											<td>'.$order->get_formatted_shipping_address().'</td>
											<td>$'.$order->get_total().'</td>
										</tr>';

					// to be removed
					//echo "+".$ord_type."-".strtolower(get_post_meta( $order->get_id(), '_order_type',  true ))."+ &nbsp;&nbsp;";

					if( $pdf_gen ) {					
						if( ! $ord_type ) {
							// add a page
							$pdf->AddPage();

							$pdf_body = '<h2>Order #'.$order->get_id().'</h2>';
							//$order = wc_get_order($order_id);

							$pdf_body .= '<p style="font-size: 14px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' ('.$order->get_billing_phone().')</p>';

							$pdf_body .= '<p style="font-size: 14px;"><strong>Items:</strong><br>';
							foreach ($order->get_items() as $order_item_id => $order_item ) {
								$pdf_item_ext = '';
								if( $order_item->get_meta('meal_extras') ) {
									$order_item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $order_item->get_meta('meal_extras'));
									$pdf_item_ext = ' <span style="color: #c6731c;">('.$order_item_extra_info.')</span>';
								}
								$pdf_body .= ' &nbsp; '.$order_item->get_name().'<strong> x '.$order_item->get_quantity().'</strong>'.$pdf_item_ext.'<br>';
							}
							$pdf_body .= '</p>';

							$pdf_body .= '<p style="font-size: 14px;"><strong>Time:</strong><br>'.get_post_meta( $order->get_id(), '_shipping_time',  true ).'</p>';

							$pdf_body .= '<p style="font-size: 14px;"><strong>Address:</strong><br>'.$order->get_formatted_shipping_address().'</p>';

							if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
								$pdf_body .= '<p style="font-size: 14px;">Insulated Bag with Ice: <strong>Yes</strong></p>';
							}

							if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
								$pdf_body .= '<p style="font-size: 14px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
							}

							if( $order->get_customer_note() ) {
								$pdf_body .= '<p style="font-size: 14px;"><strong>Note:</strong> '. $order->get_customer_note() . '</p>';
							}

							$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
										<tr>
											<td style="text-align: center;">
												<img src="'.home_url('/wp-content/uploads/2025/04/Logo.png').'" />
												<br><br>
											</td>
										</tr>
										<tr>
											<td>
												'.$pdf_body.'
											</td>
										</tr>
									</table>';

							$pdf->writeHTML($html, true, false, true, false, '');						
						} else {
							if( $ord_type == strtolower(get_post_meta( $order->get_id(), '_order_type',  true )) ) {
								// add a page
								$pdf->AddPage();

								$pdf_body = '<h2>Order #'.$order->get_id().'</h2>';

								$pdf_body .= '<p style="font-size: 14px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' ('.$order->get_billing_phone().')</p>';

								$pdf_body .= '<p style="font-size: 14px;"><strong>Items:</strong><br>';
								foreach ($order->get_items() as $order_item_id => $order_item ) {
									$pdf_item_ext = '';
									if( $order_item->get_meta('meal_extras') ) {
										$order_item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $order_item->get_meta('meal_extras'));
										$pdf_item_ext = ' <span style="color: #c6731c;">('.$order_item_extra_info.')</span>';
									}
									$pdf_body .= ' &nbsp; '.$order_item->get_name().'<strong> x '.$order_item->get_quantity().'</strong>'.$pdf_item_ext.'<br>';
								}
								$pdf_body .= '</p>';

								$pdf_body .= '<p style="font-size: 14px;"><strong>Time:</strong><br>'.get_post_meta( $order->get_id(), '_shipping_time',  true ).'</p>';

								$pdf_body .= '<p style="font-size: 14px;"><strong>Address:</strong><br>'.$order->get_formatted_shipping_address().'</p>';

								if( get_post_meta( $order->get_id(), '_insulated_bag_fee',  true ) ) {
									$pdf_body .= '<p style="font-size: 14px;">Insulated Bag with Ice: <strong>Yes</strong></p>';
								}

								if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
									$pdf_body .= '<p style="font-size: 14px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
								}

								if( $order->get_customer_note() ) {
									$pdf_body .= '<p style="font-size: 14px;"><strong>Note:</strong> '. $order->get_customer_note() . '</p>';
								}

								$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
											<tr>
												<td style="text-align: center;">
													<img src="'.home_url('/wp-content/uploads/2025/04/Logo.png').'" />
													<br><br>
												</td>
											</tr>
											<tr>
												<td>
													'.$pdf_body.'
												</td>
											</tr>
										</table>';

								$pdf->writeHTML($html, true, false, true, false, '');
							}
						}
					}

					if( $report_gen ) {
						$row = array(
							'#'.$order->get_id(),
							$order->get_billing_first_name(),
							$order_date,
							wp_strip_all_tags(str_replace("<br>", "\n", $order_items)),
							ucwords($order->get_status()),
							get_post_meta( $order->get_id(), '_shipping_type',  true ),
							get_post_meta( $order->get_id(), '_shipping_time',  true ),
							str_replace("<br/>","\n",$order->get_formatted_shipping_address()),
							'$'.$order->get_total()
						);
						$data_rows[] = $row;
					}

					if( $xcel_gen ) {
						$row = array(
							$order->get_billing_first_name().' '.$order->get_billing_last_name(),
							str_replace("<br/>","\n",$order->get_formatted_shipping_address()),
							$order->get_shipping_address_1()."\n".$order->get_shipping_address_2(),
							$order->get_shipping_city(),
							$order->get_shipping_state(),
							$order->get_shipping_postcode(),
							$order->get_billing_phone(),
							$order->get_customer_note(),
							get_post_meta( $order->get_id(), '_shipping_type',  true ),
							get_post_meta( $order->get_id(), '_shipping_time',  true )
						);
						$data_rows[] = $row;
					}
				}

				if( $pdf_gen ) {
					// reset pointer to the last page
					$pdf->lastPage();

					$upload_dir = wp_upload_dir();
					$pdf_dir = $upload_dir['basedir'].'/order-pdfs';
					$pdf_dir_url = $upload_dir['baseurl'].'/order-pdfs';
					if( ! file_exists( $pdf_dir ) ) {
						wp_mkdir_p( $pdf_dir );
					}

					// delete the current PDF if already exist
					if( file_exists($pdf_dir.'/Orders-'.time().'.pdf') ) {
						unlink($pdf_dir.'/Orders-'.time().'.pdf');
					}

					ob_end_clean();

					//Close and output PDF document
					$pdf->Output($pdf_dir.'/Orders-'.time().'.pdf', 'D');
				}

				if( $report_gen ) {
					$domain = $_SERVER['SERVER_NAME'];
					$filename = 'orders-' . $domain . '-' . time() . '.csv';

					$header_columns = array('Order','Name','Date','Items','Status','Type','Time','Address','Total');
					header('Content-Type: application/csv');
					header('Content-Disposition: attachment; filename='.$filename);

					ob_end_clean();

					$fh = fopen( 'php://output', 'w' );

					fputcsv( $fh, $header_columns );

					foreach ( $data_rows as $data_row ) {
						fputcsv( $fh, $data_row );
					}

					exit();
				}

				if( $xcel_gen ) {
					$filename = 'Customer Input Template Complete.csv';

					$header_columns = array('Full Name','Address','Apt or Suite # and BUZZ #','City','Province','Postal Code','Phone #','Notes','Shipping Method','Time');
					header('Content-Type: application/csv');
					header('Content-Disposition: attachment; filename='.$filename);

					ob_end_clean();

					$fh = fopen( 'php://output', 'w' );

					fputcsv( $fh, $header_columns );

					foreach ( $data_rows as $data_row ) {
						fputcsv( $fh, $data_row );
					}

					exit();
				}

				$orders_list .= '</table>';
				
			}
		}
		
		if( !isset($_GET["vmode"]) ) {
		?>
		<div class="date-selection-row" style="padding-bottom: 0;">
			<h1 style="margin-bottom: 0;">Orders of this week</h1>
			<form action="" method="POST" style="margin-top: 20px;">
				<input type="text" name="from_date" class="reporting-date" placeholder="Start Date" value="<?php //echo $from_date; ?>" />
				<input type="text" name="to_date" class="reporting-date" placeholder="End Date" value="<?php //echo $to_date; ?>" />
				<br><br>
				<input type="submit" name="order_date_range" class="button button-primary button-large" value="Submit" />
			</form>
		</div>
		<?php
		}
		
		$orders_list .= '</div>';
		echo $orders_list;
	}
}

/**
  * Extend TCPDF to work with custom header
  */
class MC_TCPDF extends TCPDF {
	//Page header
	public function Header() {
		// Logo
		$image_file = home_url('/wp-content/uploads/2025/04/Logo.png'); //K_PATH_IMAGES.'logo_example.jpg'
		$this->Image($image_file, 10, 10, 50, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
		// Set font
		$this->SetFont('helvetica', 'B', 20);
		// Line break
		$this->Ln();
		// Title
		$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
	}
}

function ch_generate_order_pdf($order_id, $download = false) {
	if( isset($_GET["single_order"]) && isset($_GET["dl_pdf"]) && $_GET["dl_pdf"] == "1" ) {
		$order_id = $_GET["single_order"];
		
		// create new PDF document
		$pdf = new MC_TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('FlavaDaddy Team');
		$pdf->SetTitle('FlavaDaddy Team');
		$pdf->SetSubject('Order Info');
		$pdf->SetKeywords('Order, PDF, Chef Hack, Fully Prepared Meals');

		// set default header data
		$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 010', PDF_HEADER_STRING);

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		// remove default header/footer
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set font
		$pdf->SetFont('helvetica', '', 10);

		// add a page
		$pdf->AddPage();

		$pdf_body = '<h2>Order #'.$order_id.'</h2>';
		$order = wc_get_order($order_id);

		$pdf_body .= '<p style="font-size: 14px;"><strong>Contact:</strong> '.$order->get_billing_first_name().' '.$order->get_billing_last_name().' ('.$order->get_billing_phone().')</p>';

		$pdf_body .= '<p style="font-size: 14px;"><strong>Items:</strong><br>';
		foreach ($order->get_items() as $item_id => $item ) {
			$item_ext = '';
			if( $item->get_meta('meal_extras') ) {
				$item_extra_info = preg_replace('/([a-z]+ ?\/?)+(: \$\d)+/', '$1', $item->get_meta('meal_extras'));
				$item_ext = ' <span style="color: #c6731c;">('.$item_extra_info.')</span>';
			}
			$pdf_body .= ' &nbsp; '.$item->get_name().'<strong> x '.$item->get_quantity().'</strong>'.$item_ext.'<br>';
		}
		$pdf_body .= '</p>';

		//$pdf_body .= '<p style="font-size: 14px;"><strong>Time:</strong><br>'.get_post_meta( $order->get_id(), '_delivery_option',  true ).'</p>';

		$pdf_body .= '<p style="font-size: 14px;"><strong>Address:</strong><br>'.$order->get_formatted_shipping_address().'</p>';

		if( get_post_meta( $order->id, '_delivery_for_not_home', true ) ) {
			$pdf_body .= '<p style="font-size: 14px;">** If I am not home at time of delivery, please leave the package: <strong>' . get_post_meta( $order->id, '_delivery_for_not_home', true ) . '</strong></p>';
		}

		//$customer_note = $order->get_customer_note();
		if( $order->get_customer_note() ) {
			$pdf_body .= '<p style="font-size: 14px;"><strong>Note:</strong> '. $order->get_customer_note() . '</p>';
		}

		$html = '<table border="0" cellpadding="0" cellspacing="10" style="width:100%;">
					<tr>
						<td style="text-align: center;">
							<img src="'.home_url('/wp-content/uploads/2025/04/Logo.png').'" />
							<br><br>
						</td>
					</tr>
					<tr>
						<td>
							'.$pdf_body.'
						</td>
					</tr>
				</table>';

		$pdf->writeHTML($html, true, false, true, false, '');

		// reset pointer to the last page
		$pdf->lastPage();

		$upload_dir = wp_upload_dir();
		$pdf_dir = $upload_dir['basedir'].'/order-pdfs';
		$pdf_dir_url = $upload_dir['baseurl'].'/order-pdfs';
		if( ! file_exists( $pdf_dir ) ) {
			wp_mkdir_p( $pdf_dir );
		}

		// delete the current PDF if already exist
		if( file_exists($pdf_dir.'/Order-Details-'.$order_id.'.pdf') ) {
			unlink($pdf_dir.'/Order-Details-'.$order_id.'.pdf');
		}

		ob_end_clean();
		
		//Close and output PDF document
		$pdf->Output($pdf_dir.'/Order-Details-'.$order_id.'.pdf', 'D');
	}
}
add_action("admin_init", "ch_generate_order_pdf");

<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?php require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-header.php' ); ?>
<?php
$admin_object = new Woocommerce_Dynamic_Pricing_And_Discount_Pro_Admin( '', '' );
$allowed_tooltip_html = wp_kses_allowed_html( 'post' )['span'];
$submitFee = filter_input( INPUT_POST, 'submitFee', FILTER_SANITIZE_STRING );
if ( isset( $submitFee ) && ! empty( $submitFee ) ) {
	$post_data                  = filter_input_array( INPUT_POST,
		array(
			'post_type'                        			=> FILTER_SANITIZE_STRING,
			'dpad_post_id'                     			=> FILTER_SANITIZE_STRING,
			'dpad_settings_product_dpad_title' 			=> FILTER_SANITIZE_STRING,
			'dpad_settings_select_dpad_type'   			=> FILTER_SANITIZE_STRING,
			'dpad_settings_product_cost'       			=> FILTER_SANITIZE_STRING,
			'dpad_chk_qty_price'               			=> FILTER_SANITIZE_STRING,
			'dpad_per_qty'                     			=> FILTER_SANITIZE_STRING,
			'extra_product_cost'               			=> FILTER_SANITIZE_STRING,
			'dpad_settings_start_date'         			=> FILTER_SANITIZE_STRING,
			'dpad_settings_end_date'           			=> FILTER_SANITIZE_STRING,
			'dpad_time_from'           		   			=> FILTER_SANITIZE_STRING,
			'dpad_time_to'           		   			=> FILTER_SANITIZE_STRING,
			'dpad_settings_status'             			=> FILTER_SANITIZE_STRING,
			'total_row'                        			=> FILTER_SANITIZE_STRING,
			'submitFee'                        			=> FILTER_SANITIZE_STRING,
			'dpad_chk_discount_msg'			   			=> FILTER_SANITIZE_STRING,
			'dpad_chk_discount_msg_selected_product' 	=> FILTER_SANITIZE_STRING,
			'first_order_for_user'			   			=> FILTER_SANITIZE_STRING,
			'user_login_status'			   				=> FILTER_SANITIZE_STRING,
			'dpad_discount_msg_text'		   			=> FILTER_SANITIZE_STRING,
			'dpad_sale_product'		   		   			=> FILTER_SANITIZE_STRING,
		)
	);
	$post_data['dpad']                       = filter_input( INPUT_POST, 'dpad', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	$post_data['pdcv_selected_product_list'] = filter_input( INPUT_POST, 'pdcv_selected_product_list', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	$post_data['condition_key']              = filter_input( INPUT_POST, 'condition_key', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	$post_data['dpad_select_day_of_week']    = filter_input( INPUT_POST, 'dpad_select_day_of_week', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
	// $admin_object->wdpad_pro_dpad_conditions_save( $post_data );
}
$paction    = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );
$paction_id = filter_input( INPUT_GET, 'post', FILTER_SANITIZE_STRING );
if ( isset( $paction ) && $paction === 'edit' ) {
	$btnValue          = __( 'Update', 'woo-conditional-discount-rules-for-checkout' );
	$dpad_title        = __( get_the_title( $paction_id ), 'woo-conditional-discount-rules-for-checkout' );
	$getFeesCost       = __( get_post_meta( $paction_id, 'dpad_settings_product_cost', true ), 'woo-conditional-discount-rules-for-checkout' );
	$getFeesPerQtyFlag = get_post_meta( $paction_id, 'dpad_chk_qty_price', true );
	$getFeesPerQty     = get_post_meta( $paction_id, 'dpad_per_qty', true );
	$extraProductCost  = get_post_meta( $paction_id, 'extra_product_cost', true );
	$getFeesType       = __( get_post_meta( $paction_id, 'dpad_settings_select_dpad_type', true ), 'woo-conditional-discount-rules-for-checkout' );
	$getFeesStartDate  = get_post_meta( $paction_id, 'dpad_settings_start_date', true );
	$getFeesEndDate    = get_post_meta( $paction_id, 'dpad_settings_end_date', true );
	$dpad_time_from    = get_post_meta( $paction_id, 'dpad_time_from', true );
	$dpad_time_to      = get_post_meta( $paction_id, 'dpad_time_to', true );
	$getFeesStatus     = get_post_meta( $paction_id, 'dpad_settings_status', true );
	$productFeesArray  = get_post_meta( $paction_id, 'dynamic_pricing_metabox', true );
	$getFeesOptional   = __( get_post_meta( $paction_id, 'dpad_settings_optional_gift', true ), 'woo-conditional-discount-rules-for-checkout' );
	$byDefaultChecked  = get_post_meta( $paction_id, 'by_default_checkbox_checked', true );
	$getMsgChecked     = get_post_meta( $paction_id, 'dpad_chk_discount_msg', true );
	if ( wcdrfc_fs()->is__premium_only() ) {
		if ( wcdrfc_fs()->can_use_premium_code() ) {
			$getFirstOrderUser 			= get_post_meta( $paction_id, 'first_order_for_user', true );
			$getUserLoginStatus 		= get_post_meta( $paction_id, 'user_login_status', true );
			$ap_rule_status 			= get_post_meta( $paction_id, 'ap_rule_status', true );
			$cost_on_product_status 	= get_post_meta( $paction_id, 'cost_on_product_status', true );
			$cost_on_category_status    = get_post_meta( $paction_id, 'cost_on_category_status', true );
			$cost_rule_match 			= get_post_meta( $paction_id, 'cost_rule_match', true );

			$sm_metabox_ap_product      = get_post_meta( $paction_id, 'sm_metabox_ap_product', true );
			if ( is_serialized( $sm_metabox_ap_product ) ) {
				$sm_metabox_ap_product = maybe_unserialize( $sm_metabox_ap_product );
			} else {
				$sm_metabox_ap_product = $sm_metabox_ap_product;
			}

			$sm_metabox_ap_category = get_post_meta( $paction_id, 'sm_metabox_ap_category', true );
			if ( is_serialized( $sm_metabox_ap_category ) ) {
				$sm_metabox_ap_category = maybe_unserialize( $sm_metabox_ap_category );
			} else {
				$sm_metabox_ap_category = $sm_metabox_ap_category;
			}

			if ( ! empty( $cost_rule_match ) ) {
				if ( is_serialized( $cost_rule_match ) ) {
					$cost_rule_match = maybe_unserialize( $cost_rule_match );
				} else {
					$cost_rule_match = $cost_rule_match;
				}
				if ( array_key_exists( 'general_rule_match', $cost_rule_match ) ) {
					$general_rule_match = $cost_rule_match['general_rule_match'];
				} else {
					$general_rule_match = 'all';
				}
				if ( array_key_exists( 'cost_on_product_rule_match', $cost_rule_match ) ) {
					$cost_on_product_rule_match = $cost_rule_match['cost_on_product_rule_match'];
				} else {
					$cost_on_product_rule_match = 'any';
				}
				if ( array_key_exists( 'cost_on_category_rule_match', $cost_rule_match ) ) {
					$cost_on_category_rule_match = $cost_rule_match['cost_on_category_rule_match'];
				} else {
					$cost_on_category_rule_match = 'any';
				}
			} else {
				$general_rule_match				= 'all';
				$cost_on_product_rule_match 	= 'any';
				$cost_on_category_rule_match    = 'any';
			}
		} else {
			$getFirstOrderUser  = '';
			$getUserLoginStatus = '';
		}
	} else {
		$getFirstOrderUser  = '';
		$getUserLoginStatus = '';
	}
	$getDiscountMsg    	= __( get_post_meta( $paction_id, 'dpad_discount_msg_text', true ), 'woo-conditional-discount-rules-for-checkout' );
	$getSaleProduct    	= __( get_post_meta( $paction_id, 'dpad_sale_product', true ), 'woo-conditional-discount-rules-for-checkout' );
	$getSelectedflg    	= __( get_post_meta( $paction_id, 'dpad_chk_discount_msg_selected_product', true ), 'woo-conditional-discount-rules-for-checkout' );
	$getSelectedpd_lt  	= __( get_post_meta( $paction_id, 'pdcv_selected_product_list', true ), 'woo-conditional-discount-rules-for-checkout' );
	$get_select_dow    	= __( get_post_meta( $paction_id, 'dpad_select_day_of_week', true ), 'woo-conditional-discount-rules-for-checkout' );
} else {
	$paction_id         = '';
	$btnValue           = __( 'Submit', 'woo-conditional-discount-rules-for-checkout' );
	$dpad_title         = '';
	$getFeesCost        = '';
	$getFeesPerQtyFlag  = '';
	$getFeesPerQty      = '';
	$extraProductCost   = '';
	$getFeesType        = '';
	$getFeesStartDate   = '';
	$getFeesEndDate     = '';
	$dpad_time_from     = '';
	$dpad_time_to       = '';
	$getFeesStatus      = '';
	$getFeesOptional    = '';
	$byDefaultChecked   = '';
	$getMsgChecked      = '';
	$getFirstOrderUser  = '';
	$getUserLoginStatus = '';
	$getDiscountMsg     = '';
	$getSelectedflg     = '';
	$getSaleProduct     = '';
	$getSelectedpd_lt   = '';
	$get_select_dow		= '';
	$productFeesArray   = array();
	if ( wcdrfc_fs()->is__premium_only() ) {
		if ( wcdrfc_fs()->can_use_premium_code() ) {
			$ap_rule_status					= '';
			$cost_on_product_status         = '';
			$cost_on_category_status        = '';
			$sm_metabox_ap_product          = array();
			$sm_metabox_ap_category         = array();
			$general_rule_match 			= 'all';
			$cost_on_product_rule_match 	= 'any';
			$cost_on_category_rule_match    = 'any';
		}
	}
}
if ( $getFeesOptional === 'yes' ) {
	$style_display = 'display:block;';
} else {
	$style_display = 'display:none;';
}
if( $getSelectedflg === 'on' ) {
	$selected_style_display = 'display:block;';
} else {
	$selected_style_display = 'display:none;';
}
?>
<div class="text-condtion-is" style="display:none;">
	<select class="text-condition">
		<option value="is_equal_to"><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
		<option value="less_equal_to"><?php esc_html_e( 'Less or Equal to ( <= )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
		<option value="less_then"><?php esc_html_e( 'Less than ( < )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
		<option value="greater_equal_to"><?php esc_html_e( 'Greater or Equal to ( >= )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
		<option value="greater_then"><?php esc_html_e( 'Greater than ( > )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
		<option value="not_in"><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
	</select>
	<select class="select-condition">
		<option value="is_equal_to"><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
		<option value="not_in"><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
	</select>
</div>
<div class="default-country-box" style="display:none;">
	<?php echo wp_kses( $admin_object->wdpad_pro_get_country_list(), allowed_html_tags() ); ?>
</div>
<div class="wdpad-main-table res-cl">
	<h2><?php esc_html_e( 'Discount Configuration', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
	<form method="POST" name="dpadfrm" action="">
		<!-- <input type="hidden" name="post_type" value="wc_dynamic_pricing"> -->
		<input type="hidden" name="dpad_post_id" value="<?php echo esc_attr( $paction_id ) ?>">
		<table class="form-table table-outer product-fee-table">
			<tbody>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="onoffswitch"><?php esc_html_e( 'Status', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
				</th>
				<td class="forminp">
					<label class="switch">
						<input type="checkbox" name="dpad_settings_status" value="on" <?php echo ( isset( $getFeesStatus ) && $getFeesStatus === 'off' ) ? '' : 'checked'; ?>>
						<div class="slider round"></div>
					</label>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="dpad_settings_product_dpad_title"><?php esc_html_e( 'Discount rule title', 'woo-conditional-discount-rules-for-checkout' ); ?>
						<span class="required-star">*</span></label></th>
				<td class="forminp">
					<input type="text" name="dpad_settings_product_dpad_title" class="text-class" id="dpad_settings_product_dpad_title"
					       value="<?php echo isset( $dpad_title ) ? esc_attr( $dpad_title ) : ''; ?>" required="1"
					       placeholder="<?php esc_attr_e( 'Enter product discount title', 'woo-conditional-discount-rules-for-checkout' ); ?>">
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description"
					   style="display:none;"><?php esc_html_e( 'This discount rule title is visible to the customer at the time of checkout.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'This discount rule title is visible to the customer at the time of checkout.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>

			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="dpad_settings_select_dpad_type"><?php esc_html_e( 'Select discount type', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
				</th>
				<td class="forminp">
					<select name="dpad_settings_select_dpad_type" id="dpad_settings_select_dpad_type" class="">
						<option value="fixed" <?php echo isset( $getFeesType ) && $getFeesType === 'fixed' ? 'selected="selected"' : '' ?>><?php esc_html_e( 'Fixed', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
						<option value="percentage" <?php echo isset( $getFeesType ) && $getFeesType === 'percentage' ? 'selected="selected"' : '' ?>><?php esc_html_e( 'Percentage', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
					</select>
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description"
					   style="display:none;"><?php esc_html_e( 'You can give discount on fixed price or percentage based.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'You can give discount on fixed price or percentage based.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="dpad_settings_product_cost">
						<?php esc_html_e( 'Discount value', 'woo-conditional-discount-rules-for-checkout' ); ?>
						<span class="required-star">*</span>
					</label>
				</th>
				<td class="forminp">
					<div class="product_cost_left_div">
						<input type="text" id="dpad_settings_product_cost" name="dpad_settings_product_cost" required="1" class="text-class" id="dpad_settings_product_cost"
						       value="<?php echo isset
						       ( $getFeesCost ) ? esc_attr( $getFeesCost ) : ''; ?>" placeholder="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>">
						<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
						<p class="description" style="display:none;">
							<?php
							//esc_html_e( 'If you select fixed discount type then : you have add fixed discount value. (Eg. 10, 20)' );
							//echo '<br/>';
							//esc_html_e( 'If you select percentage based discount type then : you have add percentage of discount (Eg. 10, 15.20)', 'woo-conditional-discount-rules-for-checkout' ); ?>
						</p> -->
						<?php echo wp_kses( wc_help_tip( esc_html__( 'If you select fixed discount type then : you have add fixed discount value. (Eg. 10, 20)', 'woo-conditional-discount-rules-for-checkout' )
							.'<br/>'. esc_html__( 'If you select percentage based discount type then : you have add percentage of discount (Eg. 10, 15.20)', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
					</div>
					<?php
					if ( wcdrfc_fs()->is__premium_only() ) {
						if ( wcdrfc_fs()->can_use_premium_code() ) {
							?>
							<div class="product_cost_right_div">

								<div class="applyperqty-boxone">
									<label for="dpad_chk_qty_price"><?php esc_html_e( 'Apply Per Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
									<input type="checkbox" name="dpad_chk_qty_price" id="dpad_chk_qty_price" class="chk_qty_price_class"
									       value="on" <?php checked( $getFeesPerQtyFlag, 'on' ); ?>>
									<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
									<p class="description"
									   style="display:none;"><?php esc_html_e( 'Apply this discount per quantity of products.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
									<?php echo wp_kses( wc_help_tip( esc_html__( 'Apply this discount per quantity of products.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
								</div>

								<div class="applyperqty-boxtwo">
									<label for="apply_per_qty_type"><?php esc_html_e( 'Calculate Quantity Based On', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
									<select name="dpad_per_qty" id="price_cartqty_based" class="chk_qty_price_class" id="apply_per_qty_type">
										<option value="qty_cart_based" <?php selected( $getFeesPerQty, 'qty_cart_based' ); ?>><?php esc_html_e( 'Cart Based', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option value="qty_product_based" <?php selected( $getFeesPerQty, 'qty_product_based' ); ?>><?php esc_html_e( 'Product Based', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</select>
									<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
									<p class="description"
									   style="display:none;"><?php //esc_html_e( 'If you want to apply the discount for each quantity - where quantity should calculated based on product/category/tag conditions, then select the "Product Based" option.', 'woo-conditional-discount-rules-for-checkout' );
										//echo '<br>';
										//esc_html_e( 'If you want to apply the discount for each quantity in the customer\'s cart, then select the "Cart Based" option.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
									<?php echo wp_kses( wc_help_tip( esc_html__( 'If you want to apply the discount for each quantity - where quantity should calculated based on product/category/tag conditions, then select the "Product Based" option.', 'woo-conditional-discount-rules-for-checkout' ) . '<br/>' . esc_html__( 'If you want to apply the discount for each quantity in the customer\'s cart, then select the "Cart Based" option.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
								</div>
								
								<?php
								$additionaldpad_desc = esc_html( 'You can add discount here to be charged for each additional quantity. E.g. if user has added 3 quantities and you have set discount=$10 and discount per additional quantity=$5, then total extra discount=$10+$5+$5=$20' ) . '<br>' . esc_html( 'The quantity will be calculated based on the option chosen in the "Calculate Quantity Based On" above dropdown. That means, if you have chosen "Product Based" option - quantities will be calculated based on the products which are meeting the conditions set for this discount, and if they are more than 1, discount will be calculated considering only its additional quantities. e.g. 5 items in cart, and 3 are meeting the condition set, then additional discount of $5 will be charged on 2 quantities only, and not on 4 quantities.', 'woo-conditional-discount-rules-for-checkout' );
								?>
								<div class="applyperqty-boxthree">
									<label for="extra_product_cost"><?php esc_html_e( 'Discount per Additional Quantity&nbsp;(' . get_woocommerce_currency_symbol() . ') ', 'woo-conditional-discount-rules-for-checkout' ); ?>
										<span class="required-star">*</span></label>
									<input type="text" name="extra_product_cost" class="text-class" id="extra_product_cost" required
									       value="<?php echo isset( $extraProductCost ) ? esc_attr( $extraProductCost ) : ''; ?>" placeholder="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>">
									<span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
									<p class="description" style="display:none;"><?php echo wp_kses( $additionaldpad_desc, allowed_html_tags() ); ?></p>
								</div>

							</div>
							<?php
						}
					}
					?>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row"><label
						for="dpad_settings_start_date"><?php esc_html_e( 'Start date', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
				</th>
				<td class="forminp">
					<input type="text" name="dpad_settings_start_date" class="text-class" id="dpad_settings_start_date"
					       value="<?php echo isset( $getFeesStartDate ) ? esc_attr( $getFeesStartDate ) : ''; ?>"
					       placeholder="<?php esc_attr_e( 'Select start date', 'woo-conditional-discount-rules-for-checkout' ); ?>">
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description"
					   style="display:none;"><?php esc_html_e( 'Select start date which date product discount rules will enable on website.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'Select start date which date product discount rules will enable on website.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row"><label
						for="dpad_settings_end_date"><?php esc_html_e( 'End date', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
				</th>
				<td class="forminp">
					<input type="text" name="dpad_settings_end_date" class="text-class" id="dpad_settings_end_date"
					       value="<?php echo isset( $getFeesEndDate ) ? esc_attr( $getFeesEndDate ) : ''; ?>"
					       placeholder="<?php esc_attr_e( 'Select end date', 'woo-conditional-discount-rules-for-checkout' ); ?>">
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description"
					   style="display:none;"><?php esc_html_e( 'Select ending date which date product discount rules will disable on website', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'Select ending date which date product discount rules will disable on website', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="sm_time"><?php esc_html_e( 'Time', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
				</th>
				<td class="forminp">
					<span class="dpad_time_from"><?php esc_html_e( 'From:', 'woo-conditional-discount-rules-for-checkout' ); ?></span>
					<input type="text" class="dpad_time_input" name="dpad_time_from" class="text-class" id="dpad_time_from"
					       value="<?php echo esc_attr( $dpad_time_from ); ?>"
					       placeholder='<?php esc_attr_e( 'Select start time', 'woo-conditional-discount-rules-for-checkout' ); ?>'>
					<span class="dpad_time_to"><?php esc_html_e( 'To:', 'woo-conditional-discount-rules-for-checkout' ); ?></span>
					<input type="text" class="dpad_time_input" name="dpad_time_to" class="text-class" id="dpad_time_to"
					       value="<?php echo esc_attr( $dpad_time_to ); ?>"
					       placeholder='<?php esc_attr_e( 'Select end time', 'woo-conditional-discount-rules-for-checkout' ); ?>'>
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description" style="display:none;">
						<?php
						$html = sprintf( '%s<a href=%s target="_blank">%s</a>',
							esc_html__( 'Select time on which time product discount rules will enable on the website. This rule match with current time which is set by wordpress ', 'woo-conditional-discount-rules-for-checkout' ),
							esc_url( admin_url( 'options-general.php' ) ),
							esc_html__( 'Timezone', 'woo-conditional-discount-rules-for-checkout' )
						);
						//echo wp_kses_post( $html );
						?>
					</p> -->
					<?php echo wp_kses( wc_help_tip( esc_html( $html ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="dpad_chk_discount_msg">
						<?php esc_html_e( 'Enable discount message', 'woo-conditional-discount-rules-for-checkout' ); ?>
					</label>
				</th>
				<td class="forminp">
					<input type="checkbox" name="dpad_chk_discount_msg" id="dpad_chk_discount_msg" class="chk_qty_price_class" value="on" <?php checked( $getMsgChecked, 'on' ); ?>>
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description" style="display:none;"><?php esc_html_e( 'Display discount message on product details page above add to cart button', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'Display discount message on product details page above add to cart button', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			<tr valign="top" class="display_discount_message_text">
				<th class="titledesc" scope="row">
					<label for="dpad_discount_msg_text">
						<?php esc_html_e( 'Discount message', 'woo-conditional-discount-rules-for-checkout' ); ?>
					</label>
				</th>
				<td class="forminp">
					<textarea name="dpad_discount_msg_text" class="text-class" id="dpad_discount_msg_text"
					       placeholder="<?php esc_attr_e( 'Enter discount message', 'woo-conditional-discount-rules-for-checkout' ); ?>"><?php echo isset( $getDiscountMsg ) ? esc_html( $getDiscountMsg ) : ''; ?></textarea>
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description"
					   style="display:none;"><?php esc_html_e( 'This discount message will visible to the customer at the product details page below addd to cart button.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'This discount message will visible to the customer at the product details page below addd to cart button.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
					<div class="wdpad-selected-product-main">
						<input type="checkbox" name="dpad_chk_discount_msg_selected_product" id="dpad_chk_discount_msg_selected_product" class="chk_qty_price_class" value="on" <?php checked( $getSelectedflg, 'on' ); ?>>
						<span><?php esc_html_e('Only for specific products','woo-conditional-discount-rules-for-checkout'); ?></span>
						<div class="wdpad-selected-product-list" style="<?php echo esc_attr( $selected_style_display ); ?>">
							<?php echo wp_kses( $admin_object->wdpad_pro_get_selected_product_list( 'ofsp', $getSelectedpd_lt, 'edit' ), allowed_html_tags() ); ?>
						</div>
					</div>
				</td>
			</tr>
			<?php 
			if ( wcdrfc_fs()->is__premium_only() ) {
				if ( wcdrfc_fs()->can_use_premium_code() ) { ?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="first_order_for_user">
						<?php esc_html_e( 'Enable for first order', 'woo-conditional-discount-rules-for-checkout' ); ?>
					</label>
				</th>
				<td class="forminp">
					<input type="checkbox" name="first_order_for_user" id="first_order_for_user" value="on" <?php checked( $getFirstOrderUser, 'on' ); ?>>
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description" style="display:none;"><?php esc_html_e( 'Only apply when user will place first order.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'Only apply when user will place first order.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="user_login_status">
						<?php esc_html_e( 'Enable for logged in user', 'woo-conditional-discount-rules-for-checkout' ); ?>
					</label>
				</th>
				<td class="forminp">
					<input type="checkbox" name="user_login_status" id="user_login_status" value="on" <?php checked( $getUserLoginStatus, 'on' ); ?>>
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description" style="display:none;"><?php esc_html_e( 'Only apply when user is login into their account.', 'woo-conditional-discount-rules-for-checkout' ); ?></p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'Only apply when user is login into their account.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			<tr valign="top">
					<th class="titledesc" scope="row">
						<label for="dpad_select_day_of_week"><?php esc_html_e( 'Days of the Week', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
					</th>
					<td class="forminp">
						<?php
						$select_day_week_array = array(
							'sun' => esc_html__( 'Sunday', 'woo-conditional-discount-rules-for-checkout' ),
							'mon' => esc_html__( 'Monday', 'woo-conditional-discount-rules-for-checkout' ),
							'tue' => esc_html__( 'Tuesday', 'woo-conditional-discount-rules-for-checkout' ),
							'wed' => esc_html__( 'Wednesday', 'woo-conditional-discount-rules-for-checkout' ),
							'thu' => esc_html__( 'Thursday', 'woo-conditional-discount-rules-for-checkout' ),
							'fri' => esc_html__( 'Friday', 'woo-conditional-discount-rules-for-checkout' ),
							'sat' => esc_html__( 'Saturday', 'woo-conditional-discount-rules-for-checkout' ),
						);
						?>
						<select name="dpad_select_day_of_week[]" id="dpad_select_day_of_week"
								class="dpad_select_day_of_week multiselect2 dpad_select"
								multiple="multiple"
								placeholder='<?php echo esc_attr( 'Select days of the Week', 'woo-conditional-discount-rules-for-checkout' ); ?>'>
							<?php
							foreach ( $select_day_week_array as $value => $name ) {
								?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php echo ! empty( $get_select_dow ) && in_array( $value, $get_select_dow, true ) ? 'selected="selected"' : '' ?>><?php echo esc_html( $name ); ?></option>
								<?php
							}
							?>
						</select>
						<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
						<p class="description" style="display:none;">
							<?php
							$html = sprintf( '%s<a href=%s target="_blank">%s</a>',
								esc_html__( 'Select days on which day fee will enable on the website. This rule match with current day which is set by wordpress ', 'woo-conditional-discount-rules-for-checkout' ),
								esc_url( admin_url( 'options-general.php' ) ),
								esc_html__( 'Timezone', 'woo-conditional-discount-rules-for-checkout' )
							);
							echo wp_kses_post( $html );
							?>
						</p> -->
						<?php echo wp_kses( wc_help_tip( esc_html( $html ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
					</td>
				</tr>
			<?php } 
			} ?>
			<tr valign="top">
				<th class="titledesc" scope="row">
					<label for="dpad_sale_product"><?php esc_html_e( 'Sale products', 'woo-conditional-discount-rules-for-checkout' ); ?></label>
				</th>
				<td class="forminp">
					<select name="dpad_sale_product" id="dpad_sale_product" class="">
						<option value="include" <?php selected( $getSaleProduct, 'include' ); ?>><?php esc_html_e( 'Include', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
						<option value="exclude" <?php selected( $getSaleProduct, 'exclude' ); ?>><?php esc_html_e( 'Exclude', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
					</select>
					<!-- <span class="woocommerce_conditional_product_dpad_checkout_tab_descirtion"></span>
					<p class="description"
					   style="display:none;"><?php esc_html_e( 'You can include/exclude sale product from discount rules', 'woo-conditional-discount-rules-for-checkout' ); ?>
					</p> -->
					<?php echo wp_kses( wc_help_tip( esc_html__( 'You can include/exclude sale product from discount rules', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
				</td>
			</tr>
			</tbody>
		</table>
		<div class="sub-title section-title">
			<h2><?php esc_html_e( 'Discount Rules for checkout', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
			<div class="tap">
				<a id="fee-add-field" class="button button-large" href="javascript:void(0);"><?php esc_html_e( '+ Add Row', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
			</div>
			<?php
			if ( wcdrfc_fs()->is__premium_only() ) {
				if ( wcdrfc_fs()->can_use_premium_code() ) {
					?>
					<div class="dpad_match_type">
						<p class="switch_in_pricing_rules_description_left">
							<?php esc_html_e( 'below', 'woo-conditional-discount-rules-for-checkout' ); ?>
						
						<select name="cost_rule_match[general_rule_match]" id="general_rule_match"
								class="arcmt_select">
							<option
								value="any" <?php selected( $general_rule_match, 'any' ) ?>><?php esc_html_e( 'Any One', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
							<option
								value="all" <?php selected( $general_rule_match, 'all' ) ?>><?php esc_html_e( 'All', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
						</select>
						<p class="switch_in_pricing_rules_description">
							<?php esc_html_e( 'rule match', 'woo-conditional-discount-rules-for-checkout' ); ?>
						</p>
					</div>
					<?php
				}
			}
			?>
		</div>
		<div class="tap">
			<table id="tbl-product-fee" class="tbl_product_fee table-outer tap-cas form-table product-fee-table">
				<tbody>
				
				<?php
				if ( isset( $productFeesArray ) && ! empty( $productFeesArray ) ) {
					$i = 2;
					foreach ( $productFeesArray as $key => $productdpad ) {
						$dpad_conditions = isset( $productdpad['product_dpad_conditions_condition'] ) ? $productdpad['product_dpad_conditions_condition'] : '';
						$condition_is    = isset( $productdpad['product_dpad_conditions_is'] ) ? $productdpad['product_dpad_conditions_is'] : '';
						$condtion_value  = isset( $productdpad['product_dpad_conditions_values'] ) ? $productdpad['product_dpad_conditions_values'] : '';
						if ( wcdrfc_fs()->is__premium_only() ) {
							if ( wcdrfc_fs()->can_use_premium_code() ) {
								?>
								<tr id="row_<?php echo esc_attr( $i ); ?>" valign="top">
									<th class="titledesc th_product_dpad_conditions_condition" scope="row">
										<select rel-id="<?php echo esc_attr( $i ); ?>" id="product_dpad_conditions_condition_<?php echo esc_attr( $i ); ?>"
										        name="dpad[product_dpad_conditions_condition][]"
										        id="product_dpad_conditions_condition" class="product_dpad_conditions_condition">
											<optgroup label="<?php esc_attr_e( 'Location Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="country" <?php echo ( $dpad_conditions === 'country' ) ? 'selected' : '' ?>><?php esc_html_e( 'Country', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="city" <?php echo ( $dpad_conditions === 'city' ) ? 'selected' : '' ?>><?php esc_html_e( 'City', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="state" <?php echo ( $dpad_conditions === 'state' ) ? 'selected' : '' ?>><?php esc_html_e( 'State', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="postcode" <?php echo ( $dpad_conditions === 'postcode' ) ? 'selected' : '' ?>><?php esc_html_e( 'Postcode', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="zone" <?php echo ( $dpad_conditions === 'zone' ) ? 'selected' : '' ?>><?php esc_html_e( 'Zone', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Product Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="product" <?php echo ( $dpad_conditions === 'product' ) ? 'selected' : '' ?>><?php esc_html_e( 'Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="variableproduct" <?php echo ( $dpad_conditions === 'variableproduct' ) ? 'selected' : '' ?>><?php esc_html_e( 'Variable Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="category" <?php echo ( $dpad_conditions === 'category' ) ? 'selected' : '' ?>><?php esc_html_e( 'Category', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="tag" <?php echo ( $dpad_conditions === 'tag' ) ? 'selected' : '' ?>><?php esc_html_e( 'Tag', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="product_qty" <?php selected( $dpad_conditions, 'product_qty' ); ?>><?php esc_html_e( 'Product\'s quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="product_count" <?php selected( $dpad_conditions, 'product_count' ); ?>><?php esc_html_e( 'Product\'s count', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'User Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="user" <?php echo ( $dpad_conditions === 'user' ) ? 'selected' : '' ?>><?php esc_html_e( 'User', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="user_role" <?php echo ( $dpad_conditions === 'user_role' ) ? 'selected' : '' ?>><?php esc_html_e( 'User Role', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="user_mail" <?php echo ( $dpad_conditions === 'user_mail' ) ? 'selected' : '' ?>><?php esc_html_e( 'User Email', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Purchase History', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="total_spent_order" <?php selected( $dpad_conditions, 'total_spent_order' ); ?>><?php esc_html_e( 'Total order spent (all time)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="spent_order_count" <?php selected( $dpad_conditions, 'spent_order_count' ); ?>><?php esc_html_e( 'Number of orders (all time)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="last_spent_order" <?php selected( $dpad_conditions, 'last_spent_order' ); ?>><?php esc_html_e( 'Last order spent', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Cart Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<?php
												// $currency_symbol = get_woocommerce_currency_symbol();
												// $currency_symbol = ! empty( $currency_symbol ) ? '(' . $currency_symbol . ')' : '';
												$weight_unit = get_option( 'woocommerce_weight_unit' );
												$weight_unit = ! empty( $weight_unit ) ? '(' . $weight_unit . ')' : '';
												?>
												<option value="cart_total" <?php echo ( $dpad_conditions === 'cart_total' ) ? 'selected' : '' ?>><?php esc_html_e( 'Cart Subtotal (Before Discount) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="cart_totalafter" <?php echo ( $dpad_conditions === 'cart_totalafter' ) ? 'selected' : '' ?>><?php esc_html_e( 'Cart Subtotal (After Discount) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="quantity" <?php echo ( $dpad_conditions === 'quantity' ) ? 'selected' : '' ?>><?php esc_html_e( 'Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="weight" <?php echo ( $dpad_conditions === 'weight' ) ? 'selected' : '' ?>><?php esc_html_e( 'Weight ', 'woo-conditional-discount-rules-for-checkout' ); ?><?php echo esc_html( $weight_unit ); ?></option>
												<option value="coupon" <?php echo ( $dpad_conditions === 'coupon' ) ? 'selected' : '' ?>><?php esc_html_e( 'Coupon', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="shipping_class" <?php echo ( $dpad_conditions === 'shipping_class' ) ? 'selected' : '' ?>><?php esc_html_e( 'Shipping Class', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Payment Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="payment" <?php echo ( $dpad_conditions === 'payment' ) ? 'selected' : '' ?>><?php esc_html_e( 'Payment Gateway', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Shipping Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="shipping_method" <?php echo ( $dpad_conditions === 'shipping_method' ) ? 'selected' : '' ?>><?php esc_html_e( 'Shipping Method', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="shipping_total" <?php echo ( $dpad_conditions === 'shipping_total' ) ? 'selected' : '' ?>><?php esc_html_e( 'Shipping Total', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
										</select>
									</th>
									<td class="select_condition_for_in_notin">
										<?php //if ( $dpad_conditions === 'cart_total' || $dpad_conditions === 'cart_totalafter' || $dpad_conditions === 'quantity' || $dpad_conditions === 'weight' || $dpad_conditions === 'shipping_total' || $dpad_conditions === 'total_spent_order' || $dpad_conditions === 'spent_order_count' || $dpad_conditions === 'last_spent_order' || $dpad_conditions === 'product_qty' ) { ?>
										<?php if ( in_array( $dpad_conditions, array( 'cart_total', 'cart_totalafter', 'quantity', 'weight', 'shipping_total', 'total_spent_order', 'spent_order_count', 'last_spent_order', 'product_qty', 'product_count' ), true ) ) { ?>
											<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is_<?php echo esc_attr( $i ); ?>">
												<option value="is_equal_to" <?php echo ( $condition_is === 'is_equal_to' ) ? 'selected' : '' ?>><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="less_equal_to" <?php echo ( $condition_is === 'less_equal_to' ) ? 'selected' : '' ?>><?php esc_html_e( 'Less or Equal to ( <= )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="less_then" <?php echo ( $condition_is === 'less_then' ) ? 'selected' : '' ?>><?php esc_html_e( 'Less than ( < )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="greater_equal_to" <?php echo ( $condition_is === 'greater_equal_to' ) ? 'selected' : '' ?>><?php esc_html_e( 'Greater or Equal to ( >= )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="greater_then" <?php echo ( $condition_is === 'greater_then' ) ? 'selected' : '' ?>><?php esc_html_e( 'Greater than ( > )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="not_in" <?php echo ( $condition_is === 'not_in' ) ? 'selected' : '' ?>><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</select>
										<?php } else if( $dpad_conditions === 'user_mail' ){ ?>
											<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is_<?php echo esc_attr( $i ); ?>">
												<option value="user_name" <?php selected( $condition_is, 'user_name' ); ?>><?php esc_html_e( 'User Name ( john.doe )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="domain_name" <?php selected( $condition_is, 'domain_name' ); ?>><?php esc_html_e( 'Domain ( @gmail.com )', 'woo-conditional-discount-rules-for-checkout' ); ?> </option>
												<option value="full_mail" <?php selected( $condition_is, 'full_mail' ); ?>><?php esc_html_e( 'Email Address', 'woo-conditional-discount-rules-for-checkout' ); ?> </option>
											</select>
										<?php } else { ?>
											<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is_<?php echo esc_attr( $i ); ?>">
												<option value="is_equal_to" <?php echo ( $condition_is === 'is_equal_to' ) ? 'selected' : '' ?>><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="not_in" <?php echo ( $condition_is === 'not_in' ) ? 'selected' : '' ?>><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?> </option>
											</select>
										<?php } ?>
									</td>
									<td class="condition-value" id="column_<?php echo esc_attr( $i ); ?>" <?php if( $i <= 2 ) { echo 'colspan="2"'; } ?>>
										<?php
										$html = '';
										if ( $dpad_conditions === 'country' ) {
											$html .= $admin_object->wdpad_pro_get_country_list( $i, $condtion_value );
										} elseif ( $dpad_conditions === 'city' ) {
											$html .= '<textarea name = "dpad[product_dpad_conditions_values][value_' . $i . ']">' . $condtion_value . '</textarea>';
										} elseif ( $dpad_conditions === 'state' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_states_list__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'postcode' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= '<textarea name = "dpad[product_dpad_conditions_values][value_' . $i . ']">' . $condtion_value . '</textarea>';
												}
											}
										} elseif ( $dpad_conditions === 'zone' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_zones_list__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'product' ) {
											$html .= $admin_object->wdpad_pro_get_product_list( $i, $condtion_value, 'edit' );
										} elseif ( $dpad_conditions === 'variableproduct' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_varible_product_list__premium_only( $i, $condtion_value, 'edit' );
												}
											}
										} elseif ( $dpad_conditions === 'category' ) {
											$html .= $admin_object->wdpad_pro_get_category_list( $i, $condtion_value );
										} elseif ( $dpad_conditions === 'tag' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_tag_list__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'product_qty' ) {
											$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values price-class" value = "' . $condtion_value . '">';
											$html .= wp_kses_post( sprintf( '<p><b style="color: red;">%s</b>%s</p>',
													esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
													esc_html__( 'This rule will only work if you have selected any one Product Specific option.', 'woo-conditional-discount-rules-for-checkout' )
												) );
										} elseif ( $dpad_conditions === 'product_count' ) {
											$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
											$html .= wp_kses_post( sprintf( '<p><b style="color: red;">%s</b>%s</p>',
													esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
													esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' )
												) );
										} elseif ( $dpad_conditions === 'user' ) {
											$html .= $admin_object->wdpad_pro_get_user_list( $i, $condtion_value );
										} elseif ( $dpad_conditions === 'user_role' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_user_role_list__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'user_mail' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= '<textarea name = "dpad[product_dpad_conditions_values][value_' . $i . ']">' . $condtion_value . '</textarea>';
												}
											}
										} elseif ( $dpad_conditions === 'last_spent_order' ) {
											$html .= '<input type = "text"  name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
										} elseif ( $dpad_conditions === 'total_spent_order' ) {
											$html .= '<input type = "text"  name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
										} elseif ( $dpad_conditions === 'spent_order_count' ) {
											$html .= '<input type = "text"  name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
										} elseif ( $dpad_conditions === 'cart_total' ) {
											$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values price-class" value = "' . $condtion_value . '">';
										} elseif ( $dpad_conditions === 'cart_totalafter' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= '<input type="text" name="dpad[product_dpad_conditions_values][value_' . $i . ']" id="product_dpad_conditions_values" class="product_dpad_conditions_values price-class" value="' . $condtion_value . '">';
												}
											}
										} elseif ( $dpad_conditions === 'quantity' ) {
											$html .= '<input type = "text"  name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
										} elseif ( $dpad_conditions === 'weight' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values weight-class" value = "' . $condtion_value . '">';
												}
											}
										} elseif ( $dpad_conditions === 'coupon' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_coupon_list__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'shipping_class' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_advance_flat_rate_class__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'payment' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_payment_methods__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'shipping_method' ) {
											if ( wcdrfc_fs()->is__premium_only() ) {
												if ( wcdrfc_fs()->can_use_premium_code() ) {
													$html .= $admin_object->wdpad_pro_get_active_shipping_methods__premium_only( $i, $condtion_value );
												}
											}
										} elseif ( $dpad_conditions === 'shipping_total' ) {
											$html .= '<input type = "text"  name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
										}
										echo wp_kses( $html, allowed_html_tags() );
										?>
										<input type="hidden" name="condition_key[<?php echo 'value_' . esc_attr( $i ); ?>]" value="">
									</td>
									<?php if( $i > 2 ) { ?>
									<td>
										<a id="fee-delete-field" rel-id="<?php echo esc_attr( $i ); ?>" class="delete-row" href="javascript:;" title="Delete"><i
												class="fa fa-trash"></i></a>
									</td>
									<?php } ?>
								</tr>
								<?php
							} else { ?>
								<tr id="row_<?php echo esc_attr( $i ); ?>" valign="top">
									<th class="titledesc th_product_dpad_conditions_condition" scope="row">
										<select rel-id="<?php echo esc_attr( $i ); ?>" id="product_dpad_conditions_condition_<?php echo esc_attr( $i ); ?>" name="dpad[product_dpad_conditions_condition][]"
										        id="product_dpad_conditions_condition" class="product_dpad_conditions_condition">
											<optgroup label="<?php esc_attr_e( 'Location Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="country" <?php echo ( $dpad_conditions === 'country' ) ? 'selected' : '' ?>><?php esc_html_e( 'Country', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'City (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'State (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Postcode (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Zone (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Product Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="product" <?php echo ( $dpad_conditions === 'product' ) ? 'selected' : '' ?>><?php esc_html_e( 'Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Variable Product (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="category" <?php echo ( $dpad_conditions === 'category' ) ? 'selected' : '' ?>><?php esc_html_e( 'Category', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Tag (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Product\'s quantity (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="product_count" <?php selected( $dpad_conditions, 'product_count' ); ?>><?php esc_html_e( 'Product\'s count', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'User Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option value="user" <?php echo ( $dpad_conditions === 'user' ) ? 'selected' : '' ?>><?php esc_html_e( 'User', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'User Role (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'User Email (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Purchase History', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option disabled><?php esc_html_e( 'Last order spent (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Total order spent (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Number of orders (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Cart Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<?php
												// $currency_symbol = get_woocommerce_currency_symbol();
												// $currency_symbol = ! empty( $currency_symbol ) ? '(' . $currency_symbol . ')' : '';
												$weight_unit = get_option( 'woocommerce_weight_unit' );
												$weight_unit = ! empty( $weight_unit ) ? '(' . $weight_unit . ')' : '';
												?>
												<option value="cart_total" <?php echo ( $dpad_conditions === 'cart_total' ) ? 'selected' : '' ?>><?php esc_html_e( 'Cart Subtotal', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Cart Subtotal (After Discount) (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="quantity" <?php echo ( $dpad_conditions === 'quantity' ) ? 'selected' : '' ?>><?php esc_html_e( 'Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Weight (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?><?php echo esc_html( $weight_unit ); ?></option>
												<option disabled><?php esc_html_e( 'Coupon (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Shipping Class (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Payment Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option disabled><?php esc_html_e( 'Payment Gateway (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
											<optgroup label="<?php esc_attr_e( 'Shipping Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
												<option disabled><?php esc_html_e( 'Shipping Method (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option disabled><?php esc_html_e( 'Shipping Total (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</optgroup>
										</select>
									</th>
									<td class="select_condition_for_in_notin">
										<?php if ( 'cart_total' === $dpad_conditions || 'cart_totalafter' === $dpad_conditions || 'quantity' === $dpad_conditions || 'weight' === $dpad_conditions || 'product_count' === $dpad_conditions ) { ?>
											<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is_<?php echo esc_attr( $i ); ?>">
												<option value="is_equal_to" <?php echo ( 'is_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="less_equal_to" <?php echo ( 'less_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Less or Equal to ( <= )',
														'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="less_then" <?php echo ( 'less_then' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Less than ( < )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="greater_equal_to" <?php echo ( 'greater_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Greater or Equal to ( >= )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="greater_then" <?php echo ( 'greater_then' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Greater than ( > )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="not_in" <?php echo ( 'not_in' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</select>
										<?php } else { ?>
											<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is_<?php echo esc_attr( $i ); ?>">
												<option value="is_equal_to" <?php echo ( 'is_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option value="not_in" <?php echo ( 'not_in' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?> </option>
											</select>
										<?php } ?>
									</td>
									<td class="condition-value" id="column_<?php echo esc_attr( $i ); ?>" <?php if( $i <= 2 ) { echo 'colspan="2"'; } ?>>
										<?php
										$html = '';
										if ( 'country' === $dpad_conditions ) {
											$html .= $admin_object->wdpad_pro_get_country_list( $i, $condtion_value );
										} elseif ( 'product' === $dpad_conditions ) {
											$html .= $admin_object->wdpad_pro_get_product_list( $i, $condtion_value, 'edit' );
										} elseif ( 'category' === $dpad_conditions ) {
											$html .= $admin_object->wdpad_pro_get_category_list( $i, $condtion_value );
										} elseif ( 'user' === $dpad_conditions ) {
											$html .= $admin_object->wdpad_pro_get_user_list( $i, $condtion_value );
										} elseif ( 'cart_total' === $dpad_conditions ) {
											$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values price-class" value = "' . $condtion_value . '">';
										} elseif ( 'quantity' === $dpad_conditions ) {
											$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
										} elseif ( $dpad_conditions === 'product_count' ) {
											$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
											$html .= wp_kses_post( sprintf( '<p><b style="color: red;">%s</b>%s</p>',
													esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
													esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' )
												) );
										}
										echo wp_kses( $html, allowed_html_tags() );
										?>
										<input type="hidden" name="condition_key[<?php echo 'value_' . esc_attr( $i ); ?>]" value="">
									</td>
									<?php if( $i > 2 ) { ?>
									<td>
										<a id="fee-delete-field" rel-id="<?php echo esc_attr( $i ); ?>" class="delete-row" href="javascript:;" title="Delete"><i class="fa fa-trash"></i></a>
									</td>
									<?php } ?>
								</tr>
							<?php }
						} else { ?>
							<tr id="row_<?php echo esc_attr( $i ); ?>" valign="top">
								<th class="titledesc th_product_dpad_conditions_condition" scope="row">
									<select rel-id="<?php echo esc_attr( $i ); ?>" id="product_dpad_conditions_condition_<?php echo esc_attr( $i ); ?>" name="dpad[product_dpad_conditions_condition][]"
									        id="product_dpad_conditions_condition" class="product_dpad_conditions_condition">
										<optgroup label="<?php esc_attr_e( 'Location Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="country" <?php echo ( $dpad_conditions === 'country' ) ? 'selected' : '' ?>><?php esc_html_e( 'Country', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="" disabled><?php esc_html_e( 'City (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="" disabled><?php esc_html_e( 'State (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="" disabled><?php esc_html_e( 'Postcode (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="" disabled><?php esc_html_e( 'Zone (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Product Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="product" <?php echo ( $dpad_conditions === 'product' ) ? 'selected' : '' ?>><?php esc_html_e( 'Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Variable Product (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="category" <?php echo ( $dpad_conditions === 'category' ) ? 'selected' : '' ?>><?php esc_html_e( 'Category', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Tag (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Product\'s quantity (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="product_count" <?php selected( $dpad_conditions, 'product_count' ); ?>><?php esc_html_e( 'Product\'s count', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'User Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="user" <?php echo ( $dpad_conditions === 'user' ) ? 'selected' : '' ?>><?php esc_html_e( 'User', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'User Role (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'User Email (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Purchase History', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option disabled><?php esc_html_e( 'Last order spent (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Total order spent (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Number of orders (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Cart Specific ', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<?php
											// $currency_symbol = get_woocommerce_currency_symbol();
											// $currency_symbol = ! empty( $currency_symbol ) ? '(' . $currency_symbol . ')' : '';
											$weight_unit = get_option( 'woocommerce_weight_unit' );
											$weight_unit = ! empty( $weight_unit ) ? '(' . $weight_unit . ')' : '';
											?>
											<option value="cart_total" <?php echo ( $dpad_conditions === 'cart_total' ) ? 'selected' : '' ?>><?php esc_html_e( 'Cart Subtotal', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Cart Subtotal (After Discount) (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="quantity" <?php echo ( $dpad_conditions === 'quantity' ) ? 'selected' : '' ?>><?php esc_html_e( 'Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Weight (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?><?php echo esc_html( $weight_unit ); ?></option>
											<option disabled><?php esc_html_e( 'Coupon (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Shipping Class (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Payment Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option disabled><?php esc_html_e( 'Payment Gateway (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Shipping Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option disabled><?php esc_html_e( 'Shipping Method (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Shipping Total (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
									</select>
								</th>
								<td class="select_condition_for_in_notin">
									<?php if ( 'cart_total' === $dpad_conditions || 'cart_totalafter' === $dpad_conditions || 'quantity' === $dpad_conditions || 'weight' === $dpad_conditions || 'product_count' === $dpad_conditions ) { ?>
										<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is_<?php echo esc_attr( $i ); ?>">
											<option value="is_equal_to" <?php echo ( 'is_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="less_equal_to" <?php echo ( 'less_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Less or Equal to ( <= )',
													'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="less_then" <?php echo ( 'less_then' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Less than ( < )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="greater_equal_to" <?php echo ( 'greater_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Greater or Equal to ( >= )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="greater_then" <?php echo ( 'greater_then' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Greater than ( > )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="not_in" <?php echo ( 'not_in' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</select>
									<?php } else { ?>
										<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is_<?php echo esc_attr( $i ); ?>">
											<option value="is_equal_to" <?php echo ( 'is_equal_to' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="not_in" <?php echo ( 'not_in' === $condition_is ) ? 'selected' : '' ?>><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?> </option>
										</select>
									<?php } ?>
								</td>
								<td class="condition-value" id="column_<?php echo esc_attr( $i ); ?>" <?php if( $i <= 2 ) { echo 'colspan="2"'; } ?>>
									<?php
									$html = '';
									if ( 'country' === $dpad_conditions ) {
										$html .= $admin_object->wdpad_pro_get_country_list( $i, $condtion_value );
									} elseif ( 'product' === $dpad_conditions ) {
										$html .= $admin_object->wdpad_pro_get_product_list( $i, $condtion_value, 'edit' );
									} elseif ( 'category' === $dpad_conditions ) {
										$html .= $admin_object->wdpad_pro_get_category_list( $i, $condtion_value );
									} elseif ( 'user' === $dpad_conditions ) {
										$html .= $admin_object->wdpad_pro_get_user_list( $i, $condtion_value );
									} elseif ( 'cart_total' === $dpad_conditions ) {
										$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values price-class" value = "' . $condtion_value . '">';
									} elseif ( 'quantity' === $dpad_conditions ) {
										$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . esc_attr( $i ) . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
									} elseif ( $dpad_conditions === 'product_count' ) {
										$html .= '<input type = "text" name = "dpad[product_dpad_conditions_values][value_' . $i . ']" id = "product_dpad_conditions_values" class = "product_dpad_conditions_values qty-class" value = "' . $condtion_value . '">';
										$html .= wp_kses_post( sprintf( '<p><b style="color: red;">%s</b>%s</p>',
												esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
												esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' )
											) );
									}
									echo wp_kses( $html, allowed_html_tags() );
									?>
									<input type="hidden" name="condition_key[<?php echo 'value_' . esc_attr( $i ); ?>]" value="">
								</td>
								<?php if( $i > 2 ) { ?>
								<td>
									<a id="fee-delete-field" rel-id="<?php echo esc_attr( $i ); ?>" class="delete-row" href="javascript:;" title="Delete"><i class="fa fa-trash"></i></a>
								</td>
								<?php } ?>
							</tr>
						<?php }
						$i ++;
					}
					?>
					<?php
				} else {
					$i = 1;
					if ( wcdrfc_fs()->is__premium_only() ) {
						if ( wcdrfc_fs()->can_use_premium_code() ) {
							?>
							<tr id="row_1" valign="top">
								<th class="titledesc th_product_dpad_conditions_condition" scope="row">
									<select rel-id="1" id="product_dpad_conditions_condition_1" name="dpad[product_dpad_conditions_condition][]"
									        id="product_dpad_conditions_condition"
									        class="product_dpad_conditions_condition">
										<optgroup label="<?php esc_attr_e( 'Location Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="country"><?php esc_html_e( 'Country', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="city"><?php esc_html_e( 'City', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="state"><?php esc_html_e( 'State', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="postcode"><?php esc_html_e( 'Postcode', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="zone"><?php esc_html_e( 'Zone', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Product Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="product"><?php esc_html_e( 'Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="variableproduct"><?php esc_html_e( 'Variable Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="category"><?php esc_html_e( 'Category', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="tag"><?php esc_html_e( 'Tag', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="product_qty"><?php esc_html_e( 'Product\'s quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="product_count"><?php esc_html_e( 'Product\'s count', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'User Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="user"><?php esc_html_e( 'User', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="user_role"><?php esc_html_e( 'User Role', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="user_mail"><?php esc_html_e( 'User Email', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Purchase History', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="last_spent_order"><?php esc_html_e( 'Last order spent', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="total_spent_order"><?php esc_html_e( 'Total order spent (all time)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="spent_order_count"><?php esc_html_e( 'Number of orders (all time)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Cart Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<?php
											// $currency_symbol = get_woocommerce_currency_symbol();
											// $currency_symbol = ! empty( $currency_symbol ) ? '(' . $currency_symbol . ')' : '';
											$weight_unit = get_option( 'woocommerce_weight_unit' );
											$weight_unit = ! empty( $weight_unit ) ? '(' . $weight_unit . ')' : '';
											?>
											<option value="cart_total"><?php esc_html_e( 'Cart Subtotal (Before Discount) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="cart_totalafter"><?php esc_html_e( 'Cart Subtotal (After Discount) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="quantity"><?php esc_html_e( 'Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="weight"><?php esc_html_e( 'Weight', 'woo-conditional-discount-rules-for-checkout' ); ?><?php echo esc_html( $weight_unit ); ?></option>
											<option value="coupon"><?php esc_html_e( 'Coupon', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="shipping_class"><?php esc_html_e( 'Shipping Class', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Payment Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="payment"><?php esc_html_e( 'Payment Gateway', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Shipping Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="shipping_method"><?php esc_html_e( 'Shipping Method', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="shipping_total"><?php esc_html_e( 'Shipping Total', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
									</select>
								</td>
								<td class="select_condition_for_in_notin">
									<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is product_dpad_conditions_is_1">
										<option value="is_equal_to"><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option value="not_in"><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</select>
								</td>
								<td id="column_1" class="condition-value">
									<?php echo wp_kses( $admin_object->wdpad_pro_get_country_list( 1 ), allowed_html_tags() ); ?>
									<input type="hidden" name="condition_key[value_1][]" value="">
								</td>
							</tr>
							<?php
						} else { ?>
							<tr id="row_1" valign="top">
								<th class="titledesc th_product_dpad_conditions_condition" scope="row">
									<select rel-id="1" id="product_dpad_conditions_condition_1" name="dpad[product_dpad_conditions_condition][]"
									        id="product_dpad_conditions_condition"
									        class="product_dpad_conditions_condition">
										<optgroup label="<?php esc_attr_e( 'Location Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="country"><?php esc_html_e( 'Country', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'City (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'State (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Postcode (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Zone (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Product Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="product"><?php esc_html_e( 'Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Variable Product (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="category"><?php esc_html_e( 'Category', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Tag (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Product\'s quantity (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="product_count"><?php esc_html_e( 'Product\'s count', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'User Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option value="user"><?php esc_html_e( 'User', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'User Role (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'User Email (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Purchase History', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option disabled><?php esc_html_e( 'Last order spent (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Total order spent (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Number of orders (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Cart Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<?php
											// $currency_symbol = get_woocommerce_currency_symbol();
											// $currency_symbol = ! empty( $currency_symbol ) ? '(' . $currency_symbol . ')' : '';
											$weight_unit = get_option( 'woocommerce_weight_unit' );
											$weight_unit = ! empty( $weight_unit ) ? '(' . $weight_unit . ')' : '';
											?>
											<option value="cart_total"><?php esc_html_e( 'Cart Subtotal ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Cart Subtotal (After Discount) (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option value="quantity"><?php esc_html_e( 'Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Weight (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?><?php echo esc_html( $weight_unit ); ?></option>
											<option disabled><?php esc_html_e( 'Coupon (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Shipping Class (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Payment Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option disabled><?php esc_html_e( 'Payment Gateway (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
										<optgroup label="<?php esc_attr_e( 'Shipping Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
											<option disabled><?php esc_html_e( 'Shipping Method (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											<option disabled><?php esc_html_e( 'Shipping Total (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										</optgroup>
									</select>
								</td>
								<td class="select_condition_for_in_notin">
									<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is product_dpad_conditions_is_1">
										<option value="is_equal_to"><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option value="not_in"><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</select>
								</td>
								<td id="column_1" class="condition-value">
									<?php echo wp_kses( $admin_object->wdpad_pro_get_country_list( 1 ), allowed_html_tags() ); ?>
									<input type="hidden" name="condition_key[value_1][]" value="">
								</td>
							</tr>
						<?php }
					} else { ?>
						<tr id="row_1" valign="top">
							<th class="titledesc th_product_dpad_conditions_condition" scope="row">
								<select rel-id="1" id="product_dpad_conditions_condition_1" name="dpad[product_dpad_conditions_condition][]"
								        id="product_dpad_conditions_condition"
								        class="product_dpad_conditions_condition">
									<optgroup label="<?php esc_attr_e( 'Location Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
										<option value="country"><?php esc_html_e( 'Country', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'City (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'State (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Postcode (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Zone (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</optgroup>
									<optgroup label="<?php esc_attr_e( 'Product Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
										<option value="product"><?php esc_html_e( 'Product', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Variable Product (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option value="category"><?php esc_html_e( 'Category', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Tag (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Product\'s quantity (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option value="product_count"><?php esc_html_e( 'Product\'s count', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</optgroup>
									<optgroup label="<?php esc_attr_e( 'User Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
										<option value="user"><?php esc_html_e( 'User', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'User Role (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'User Email (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</optgroup>
									<optgroup label="<?php esc_attr_e( 'Purchase History', 'woo-conditional-discount-rules-for-checkout' ); ?>">
										<option disabled><?php esc_html_e( 'Last order spent (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Total order spent (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Number of orders (all time) (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</optgroup>
									<optgroup label="<?php esc_attr_e( 'Cart Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
										<?php
										// $currency_symbol = get_woocommerce_currency_symbol();
										// $currency_symbol = ! empty( $currency_symbol ) ? '(' . $currency_symbol . ')' : '';
										$weight_unit = get_option( 'woocommerce_weight_unit' );
										$weight_unit = ! empty( $weight_unit ) ? '(' . $weight_unit . ')' : '';
										?>
										<option value="cart_total"><?php esc_html_e( 'Cart Subtotal ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Cart Subtotal (After Discount) (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option value="quantity"><?php esc_html_e( 'Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Weight (Available in PRO) ', 'woo-conditional-discount-rules-for-checkout' ); ?><?php echo esc_html( $weight_unit ); ?></option>
										<option disabled><?php esc_html_e( 'Coupon (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Shipping Class (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</optgroup>
									<optgroup label="<?php esc_attr_e( 'Payment Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
										<option disabled><?php esc_html_e( 'Payment Gateway (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</optgroup>
									<optgroup label="<?php esc_attr_e( 'Shipping Specific', 'woo-conditional-discount-rules-for-checkout' ); ?>">
										<option disabled><?php esc_html_e( 'Shipping Method (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
										<option disabled><?php esc_html_e( 'Shipping Total (Available in PRO)', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									</optgroup>
								</select>
							</td>
							<td class="select_condition_for_in_notin">
								<select name="dpad[product_dpad_conditions_is][]" class="product_dpad_conditions_is product_dpad_conditions_is_1">
									<option value="is_equal_to"><?php esc_html_e( 'Equal to ( = )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
									<option value="not_in"><?php esc_html_e( 'Not Equal to ( != )', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
								</select>
							</td>
							<td id="column_1" class="condition-value">
								<?php echo wp_kses( $admin_object->wdpad_pro_get_country_list( 1 ), allowed_html_tags() ); ?>
								<input type="hidden" name="condition_key[value_1][]" value="">
							</td>
						</tr>
					<?php }
				} ?>
				</tbody>
			</table>
			<input type="hidden" name="total_row" id="total_row" value="<?php echo esc_attr( $i ); ?>">
		</div>
		<?php
		if ( wcdrfc_fs()->is__premium_only() ) {
			if ( wcdrfc_fs()->can_use_premium_code() ) {
				?>
				<?php // Advanced Pricing Section start  ?>
				<div id="apm_wrap" class="fees-pricing-rules">
					<div class="ap_title section-title">
						<h2><?php esc_html_e( 'Advanced Discount Price Rules', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
						<label class="switch">
							<input type="checkbox" name="ap_rule_status" value="on" <?php checked( $ap_rule_status, 'on' ); ?>>
							<div class="slider round"></div>
						</label>
						<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
						<p class="description"
							style="display:none;padding-left: 15px;"><?php //esc_html_e( 'If enabled this Advanced Pricing button only than below all rule\'s will go for apply to shipping method.', 'woo-conditional-discount-rules-for-checkout' ); ?>
						</p> -->
						<?php echo wp_kses( wc_help_tip( esc_html__( 'If enabled this Advanced Pricing button only than below all rule\'s will go for apply to discount rule', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
					</div>

					<div class="fees_pricing_rules">
						<div class="fees_pricing_rules_tab">
							<ul class="tabs">
								<?php
								$tab_array = array(
									'tab-1'  => esc_html__( 'Cost on Product', 'woo-conditional-discount-rules-for-checkout' ),
									'tab-4'  => esc_html__( 'Cost on Category', 'woo-conditional-discount-rules-for-checkout' ),
								);
								if ( ! empty( $tab_array ) ) {
									foreach ( $tab_array as $data_tab => $tab_title ) {
										if ( 'tab-1' === $data_tab ) {
											$class = ' current';
										} else {
											$class = '';
										}
										?>
										<li class="tab-link<?php echo esc_attr( $class ); ?>"
											data-tab="<?php echo esc_attr( $data_tab ); ?>">
											<?php echo esc_html( $tab_title ); ?>
										</li>
										<?php
									}
								}
								?>
							</ul>
						</div>
						<div class="fees_pricing_rules_tab_content">
							<?php // Advanced Pricing Product Section start here   ?>
							<div class="ap_product_container advance_pricing_rule_box tab-content current" id="tab-1"
									data-title="<?php esc_attr_e( 'Cost on Product', 'woo-conditional-discount-rules-for-checkout' ); ?>">
								<div class="tap-class">
									<div class="predefined_elements">
										<div id="all_product_list"></div>
									</div>
									<div class="sub-title">
										<h2 class="ap-title"><?php esc_html_e( 'Cost on Product', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
										<div class="tap">
											<a id="ap-product-add-field" class="button button-primary button-large"
												href="javascript:;"><?php esc_html_e( '+ Add Rule', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
											<div class="switch_status_div">
												<label class="switch switch_in_pricing_rules">
													<input type="checkbox" name="cost_on_product_status"
															value="on" <?php checked( $cost_on_product_status, 'on' ); ?>>
													<div class="slider round"></div>
												</label>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description switch_in_pricing_rules_description"
													style="display:none;">
													<?php //esc_html_e( 'You can turn off this button, if you do not need to apply this fee amount.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'You can turn off this button, if you do not need to apply this discount amount.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</div>
										</div>
										<div class="dpad_ap_match_type">
											<p class="switch_in_pricing_rules_description_left">
												<?php esc_html_e( 'below', 'woo-conditional-discount-rules-for-checkout' ); ?>
											</p>
											<select name="cost_rule_match[cost_on_product_rule_match]"
													id="cost_on_product_rule_match" class="arcmt_select">
												<option
													value="any" <?php selected( $cost_on_product_rule_match, 'any' ) ?>><?php esc_html_e( 'Any One', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option
													value="all" <?php selected( $cost_on_product_rule_match, 'all' ) ?>><?php esc_html_e( 'All', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</select>
											<p class="switch_in_pricing_rules_description">
												<?php esc_html_e( 'rule match', 'woo-conditional-discount-rules-for-checkout' ); ?>
											</p>
										</div>
									</div>
									<table id="tbl_ap_product_method"
											class="tbl_product_fee table-outer tap-cas form-table fees-table">
										<tbody>
										<tr class="heading">
											<th class="titledesc th_product_fees_conditions_condition" scope="row">
												<?php esc_html_e( 'Product', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php esc_html_e( 'Select a product to apply the discount amount to when the min/max quantity match.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'Select a product to apply the discount amount to when the min/max quantity match.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
											<th class="titledesc th_product_fees_conditions_condition" scope="row">
												<?php esc_html_e( 'Min Quantity ', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php //esc_html_e( 'You can set a minimum product quantity per row before the fee amount is applied.', 'woo-conditional-discount-rules-for-checkout' ); ?>
													<br/><?php //esc_html_e( 'Leave empty to not set a minimum.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'You can set a minimum product quantity per row before the discount amount is applied.<br/>Leave empty to not set a minimum.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
											<th class="titledesc th_product_fees_conditions_condition" scope="row">
												<?php esc_html_e( 'Max Quantity ', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php //esc_html_e( 'You can set a maximum product quantity per row before the fee amount is applied.', 'woo-conditional-discount-rules-for-checkout' ); ?>
													<br/><?php //esc_html_e( 'Leave empty to not set a maximum.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'You can set a maximum product quantity per row before the discount amount is applied.<br/>Leave empty to not set a maximum.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
											<th class="titledesc th_product_fees_conditions_condition" scope="row"
												colspan="2"><?php esc_html_e( 'Discount amount', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php //esc_html_e( 'A fixed amount (e.g. 5 / -5), percentage (e.g. 5% / -5%) to add as a fee. Percentage and minus amount will apply based on cart subtotal.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'A fixed amount (e.g. 5 / -5), percentage (e.g. 5% / -5%) to add as a discount. Percentage and minus amount will apply based on cart subtotal.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
										</tr>
										
										<?php
										//check advanced pricing value fill proper or unset if not
										$filled_arr = array();
										if ( ! empty( $sm_metabox_ap_product ) && is_array( $sm_metabox_ap_product ) ):
											foreach ( $sm_metabox_ap_product as $app_arr ) {
												//check that if required field fill or not once save the APR,  if match than fill in array
												if ( ! empty( $app_arr ) || '' !== $app_arr ) {
													if ( ( '' !== $app_arr['ap_fees_products'] && '' !== $app_arr['ap_fees_ap_price_product'] ) && ( '' !== $app_arr['ap_fees_ap_prd_min_qty'] || '' !== $app_arr['ap_fees_ap_prd_max_qty'] ) ) {
														//if condition match than fill in array
														$filled_arr[] = $app_arr;
													}
												}
											}
										endif;
										//check APR exist
										if ( isset( $filled_arr ) && ! empty( $filled_arr ) ) {
											$cnt_product = 2;
											foreach ( $filled_arr as $key => $productfees ) {
												$fees_ap_fees_products    = isset( $productfees['ap_fees_products'] ) ? $productfees['ap_fees_products'] : '';
												$ap_fees_ap_min_qty       = isset( $productfees['ap_fees_ap_prd_min_qty'] ) ? $productfees['ap_fees_ap_prd_min_qty'] : '';
												$ap_fees_ap_max_qty       = isset( $productfees['ap_fees_ap_prd_max_qty'] ) ? $productfees['ap_fees_ap_prd_max_qty'] : '';
												$ap_fees_ap_price_product = isset( $productfees['ap_fees_ap_price_product'] ) ? $productfees['ap_fees_ap_price_product'] : '';
												$ap_fees_ap_per_product   = isset( $productfees['ap_fees_ap_per_product'] ) && strpos($ap_fees_ap_price_product, '%') ? $productfees['ap_fees_ap_per_product'] : 'no';
												?>
												<tr id="ap_product_row_<?php echo esc_attr( $cnt_product ); ?>"
													valign="top" class="ap_product_row_tr">
													<td class="titledesc" scope="row">
														<select rel-id="<?php echo esc_attr( $cnt_product ); ?>"
																id="ap_product_fees_conditions_condition_<?php echo esc_attr( $cnt_product ); ?>"
																name="dpad[ap_product_fees_conditions_condition][<?php echo esc_attr( $cnt_product ); ?>][]"
																id="ap_product_fees_conditions_condition"
																class="wdpad_select ap_product product_fees_conditions_values multiselect2"
																multiple="multiple">
															<?php
															echo wp_kses( $admin_object->wdpad_get_product_options( $cnt_product, $fees_ap_fees_products ), allowed_html_tags() );
															?>
														</select>
													</td>
													<td class="column_<?php echo esc_attr( $cnt_product ); ?> condition-value">
														<input type="number" name="dpad[ap_fees_ap_prd_min_qty][]"
																class="text-class qty-class"
																id="ap_fees_ap_prd_min_qty[]"
																placeholder="<?php esc_attr_e( 'Min Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?>"
																value="<?php echo esc_attr( $ap_fees_ap_min_qty ); ?>"
																min="1">
													</td>
													<td class="column_<?php echo esc_attr( $cnt_product ); ?> condition-value">
														<input type="number" name="dpad[ap_fees_ap_prd_max_qty][]"
																class="text-class qty-class"
																id="ap_fees_ap_prd_max_qty[]"
																placeholder="<?php esc_attr_e( 'Max Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?>"
																value="<?php echo esc_attr( $ap_fees_ap_max_qty ); ?>"
																min="1">
													</td>
													<td class="column_<?php echo esc_attr( $cnt_product ); ?> condition-value">
														<input type="text" name="dpad[ap_fees_ap_price_product][]"
																class="text-class number-field"
																id="ap_fees_ap_price_product[]"
																placeholder="<?php esc_attr_e( 'Amount', 'woo-conditional-discount-rules-for-checkout' ); ?>"
																value="<?php echo esc_attr( $ap_fees_ap_price_product ); ?>">
														<?php
														$first_char = substr( $ap_fees_ap_price_product, 0, 1 );
														if ( '-' === $first_char ) {
															$html = sprintf(
																'<p><b style="color: red;">%s</b>%s',
																esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
																esc_html__( 'If entered fee amount is less than cart subtotal it will reflect with minus sign (EX: $ -10.00) OR If entered fee amount is more than cart subtotal then the total amount shown as zero (EX: Total: 0): ', 'woo-conditional-discount-rules-for-checkout' )
															);
															echo wp_kses_post( $html );
														}
														?>
													</td>
													<td class="column_<?php echo esc_attr( $cnt_product ); ?> condition-value">
														<a id="ap_product_delete_field"
															rel-id="<?php echo esc_attr( $cnt_product ); ?>"
															title="Delete" class="delete-row" href="javascript:;">
															<i class="fa fa-trash"></i>
														</a>
													</td>
												</tr>
												<?php
												$cnt_product ++;
											}
											?>
											<?php
										} else {
											$cnt_product = 1;
										}
										 
										?>
										</tbody>
									</table>
									<input type="hidden" name="total_row_product" id="total_row_product"
											value="<?php echo esc_attr( $cnt_product ); ?>">
								</div>
							</div>
							<?php // Advanced Pricing Product Section end here ?>
							
							<?php // Advanced Pricing Category Section start here ?>
							<div class="ap_category_container advance_pricing_rule_box tab-content" id="tab-4"
									data-title="<?php esc_attr_e( 'Cost on Category', 'woo-conditional-discount-rules-for-checkout' ); ?>">
								<div class="tap-class">
									<div class="predefined_elements">
										<div id="all_category_list">
											<?php
											echo wp_kses( $admin_object->wdpad_get_category_options__premium_only( "", $json = true ), allowed_html_tags() );
											?>
										</div>
									</div>
									<div class="sub-title">
										<h2 class="ap-title"><?php esc_html_e( 'Cost on Category', 'woo-conditional-discount-rules-for-checkout' ); ?></h2>
										<div class="tap">
											<a id="ap-category-add-field" class="button button-primary button-large"
												href="javascript:;"><?php esc_html_e( '+ Add Rule', 'woo-conditional-discount-rules-for-checkout' ); ?></a>
											<div class="switch_status_div">
												<label class="switch switch_in_pricing_rules">
													<input type="checkbox" name="cost_on_category_status"
															value="on" <?php checked( $cost_on_category_status, 'on' ); ?>>
													<div class="slider round"></div>
												</label>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description switch_in_pricing_rules_description"
													style="display:none;">
													<?php esc_html_e( 'You can turn off this button, if you do not need to apply this fee amount.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'You can turn off this button, if you do not need to apply this fee amount.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</div>
										</div>
										<div class="dpad_ap_match_type">
											<p class="switch_in_pricing_rules_description_left">
												<?php esc_html_e( 'below', 'woo-conditional-discount-rules-for-checkout' ); ?>
											</p>
											<select name="cost_rule_match[cost_on_category_rule_match]"
													id="cost_on_category_rule_match" class="arcmt_select">
												<option
													value="any" <?php selected( $cost_on_category_rule_match, 'any' ) ?>><?php esc_html_e( 'Any One', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
												<option
													value="all" <?php selected( $cost_on_category_rule_match, 'all' ) ?>><?php esc_html_e( 'All', 'woo-conditional-discount-rules-for-checkout' ); ?></option>
											</select>
											<p class="switch_in_pricing_rules_description">
												<?php esc_html_e( 'rule match', 'woo-conditional-discount-rules-for-checkout' ); ?>
											</p>
										</div>
									</div>
									<table id="tbl_ap_category_method"
											class="tbl_category_fee table-outer tap-cas form-table fees-table">
										<tbody>
										<tr class="heading">
											<th class="titledesc th_category_fees_conditions_condition"
												scope="row"><?php esc_html_e( 'Category', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php //esc_html_e( 'Select a category to apply the fee amount to when the min/max quantity match.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'Select a category to apply the fee amount to when the min/max quantity match.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
											<th class="titledesc th_category_fees_conditions_condition" scope="row">
												<?php esc_html_e( 'Min Quantity ', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php esc_html_e( 'You can set a minimum category quantity per row before the fee amount is applied.', 'woo-conditional-discount-rules-for-checkout' ); ?>
													<br/><?php esc_html_e( 'Leave empty to not set a minimum.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'You can set a minimum category quantity per row before the fee amount is applied.<br/>Leave empty to not set a minimum.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
											<th class="titledesc th_category_fees_conditions_condition" scope="row">
												<?php esc_html_e( 'Max Quantity ', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php //esc_html_e( 'You can set a maximum category quantity per row before the fee amount is applied.', 'woo-conditional-discount-rules-for-checkout' ); ?>
													<br/><?php //esc_html_e( 'Leave empty to not set a maximum.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'You can set a maximum category quantity per row before the fee amount is applied.<br/>Leave empty to not set a maximum.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
											<th class="titledesc th_category_fees_conditions_condition" scope="row"
												colspan="2"><?php esc_html_e( 'Fee amount', 'woo-conditional-discount-rules-for-checkout' ); ?>
												<!-- <span class="woocommerce_conditional_product_fees_checkout_tab_description"></span>
												<p class="description" style="display:none;">
													<?php //esc_html_e( 'A fixed amount (e.g. 5 / -5) percentage (e.g. 5% / -5%) to add as a fee. Percentage and minus amount will apply based on cart subtotal.', 'woo-conditional-discount-rules-for-checkout' ); ?>
												</p> -->
												<?php echo wp_kses( wc_help_tip( esc_html__( 'A fixed amount (e.g. 5 / -5) percentage (e.g. 5% / -5%) to add as a fee. Percentage and minus amount will apply based on cart subtotal.', 'woo-conditional-discount-rules-for-checkout' ) ), array( 'span' => $allowed_tooltip_html ) ); ?>
											</th>
										</tr>
										<?php
										//check advanced pricing value fill proper or unset if not
										$filled_arr = array();
										//check if category AP rules exist
										if ( ! empty( $sm_metabox_ap_category ) && is_array( $sm_metabox_ap_category ) ):
											foreach ( $sm_metabox_ap_category as $apcat_arr ):
												//check that if required field fill or not once save the APR,  if match than fill in array
												if ( ! empty( $apcat_arr ) || '' !== $apcat_arr ) {
													if ( ( '' !== $apcat_arr['ap_fees_categories'] && '' !== $apcat_arr['ap_fees_ap_price_category'] ) &&
															( '' !== $apcat_arr['ap_fees_ap_cat_min_qty'] || '' !== $apcat_arr['ap_fees_ap_cat_max_qty'] ) ) {
														//if condition match than fill in array
														$filled_arr[] = $apcat_arr;
													}
												}
											endforeach;
										endif;
										//check APR exist
										if ( isset( $filled_arr ) && ! empty( $filled_arr ) ) {
											$cnt_category = 2;
											foreach ( $filled_arr as $key => $productfees ) {
												$fees_ap_fees_categories   = isset( $productfees['ap_fees_categories'] ) ? $productfees['ap_fees_categories'] : '';
												$ap_fees_ap_cat_min_qty    = isset( $productfees['ap_fees_ap_cat_min_qty'] ) ? $productfees['ap_fees_ap_cat_min_qty'] : '';
												$ap_fees_ap_cat_max_qty    = isset( $productfees['ap_fees_ap_cat_max_qty'] ) ? $productfees['ap_fees_ap_cat_max_qty'] : '';
												$ap_fees_ap_price_category = isset( $productfees['ap_fees_ap_price_category'] ) ? $productfees['ap_fees_ap_price_category'] : '';
												?>
												<tr id="ap_category_row_<?php echo esc_attr( $cnt_category ); ?>"
													valign="top"
													class="ap_category_row_tr">
													<td class="titledesc" scope="row">
														<select rel-id="<?php echo esc_attr( $cnt_category ); ?>"
																id="ap_category_fees_conditions_condition_<?php echo esc_attr( $cnt_category ); ?>"
																name="dpad[ap_category_fees_conditions_condition][<?php echo esc_attr( $cnt_category ); ?>][]"
																id="ap_category_fees_conditions_condition"
																class="wdpad_select ap_category product_fees_conditions_values multiselect2"
																multiple="multiple">
															<?php
															echo wp_kses( $admin_object->wdpad_get_category_options__premium_only( $fees_ap_fees_categories, $json = false ), allowed_html_tags() );
															?>
														</select>
													</td>
													<td class="column_<?php echo esc_attr( $cnt_category ); ?> condition-value">
														<input type="number"
																name="dpad[ap_fees_ap_cat_min_qty][]"
																class="text-class qty-class"
																id="ap_fees_ap_cat_min_qty[]"
																placeholder="<?php esc_attr_e( 'Min Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?>"
																value="<?php echo esc_attr( $ap_fees_ap_cat_min_qty ); ?>"
																min="1">
													</td>
													<td class="column_<?php echo esc_attr( $cnt_category ); ?> condition-value">
														<input type="number"
																name="dpad[ap_fees_ap_cat_max_qty][]"
																class="text-class qty-class"
																id="ap_fees_ap_cat_max_qty[]"
																placeholder="<?php esc_attr_e( 'Max Quantity', 'woo-conditional-discount-rules-for-checkout' ); ?>"
																value="<?php echo esc_attr( $ap_fees_ap_cat_max_qty ); ?>"
																min="1">
													</td>
													<td class="column_<?php echo esc_attr( $cnt_category ); ?> condition-value">
														<input type="text"
																name="dpad[ap_fees_ap_price_category][]"
																class="text-class number-field"
																id="ap_fees_ap_price_category[]"
																placeholder="<?php esc_attr_e( 'Amount', 'woo-conditional-discount-rules-for-checkout' ); ?>"
																value="<?php echo esc_attr( $ap_fees_ap_price_category ); ?>">
														<?php
														//get first character for check is minus sign or not
														$first_char = substr( $ap_fees_ap_price_category, 0, 1 );
														if ( '-' === $first_char ) {
															$html = sprintf(
																'<p><b style="color: red;">%s</b>%s',
																esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
																esc_html__( 'If entered fee amount is less than cart subtotal it will reflect with minus sign (EX: $ -10.00) OR If entered fee amount is more than cart subtotal then the total amount shown as zero (EX: Total: 0): ', 'woo-conditional-discount-rules-for-checkout' )
															);
															echo wp_kses_post( $html );
														}
														?>
													</td>
													<td class="column_<?php echo esc_attr( $cnt_category ); ?> condition-value">
														<a id="ap_category_delete_field"
															rel-id="<?php echo esc_attr( $cnt_category ); ?>"
															title="Delete" class="delete-row" href="javascript:;">
															<i class="fa fa-trash"></i>
														</a>
													</td>
												</tr>
												<?php
												$cnt_category ++;
											}
											?>
											<?php
										} else {
											$cnt_category = 1;
										}
										?>
										</tbody>
									</table>
									<input type="hidden" name="total_row_category" id="total_row_category"
											value="<?php echo esc_attr( $cnt_category ); ?>">
									<!-- Advanced Pricing Category Section end here -->
								</div>
							</div>
							<?php // Advanced Pricing Category Section end here  ?>
						</div>
					</div>
				</div>
				<?php // Advanced Pricing Section end  ?>
				<?php
			}
		}
		?>
		<p class="submit">
			<input type="submit" name="submitFee" class="submitFee button button-primary button-large" value="<?php echo esc_attr( $btnValue ); ?>">
		</p>
		<?php wp_nonce_field( 'dpad_save_method', 'dpad_save_method_nonce' ); ?>
	</form>
</div>
<?php require_once( plugin_dir_path( __FILE__ ) . 'header/plugin-sidebar.php' );
?>

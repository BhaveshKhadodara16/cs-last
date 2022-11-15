<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woocommerce_Dynamic_Pricing_And_Discount_Pro
 * @subpackage Woocommerce_Dynamic_Pricing_And_Discount_Pro/public
 * @author     Multidots <inquiry@multidots.in>
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woocommerce-dynamic-pricing-and-discount-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woocommerce_Dynamic_Pricing_And_Discount_Pro_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
	}

	function woocommerce_locate_template_product_dpad_conditions( $template, $template_name, $template_path ) {

		global $woocommerce;

		$_template = $template;

		if ( ! $template_path ) {
			$template_path = $woocommerce->template_url;
		}

		$plugin_path = woocommerce_conditional_discount_rules_for_checkout_pro_path() . '/woocommerce/';

		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name,
			)
		);

		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) ) {
			$template = $plugin_path . $template_name;
		}

		if ( ! $template ) {
			$template = $_template;
		}

		// Return what we found
		return $template;
	}

	/**
	 * @param $package
	 */
	public function conditional_dpad_add_to_cart( $package ) {

		global $woocommerce, $woocommerce_wpml, $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		} else {
			$get_site_language = get_bloginfo( "language" );
			if ( false !== strpos( $get_site_language, '-' ) ) {
				$get_site_language_explode = explode( '-', $get_site_language );
				$default_lang              = $get_site_language_explode[0];
			} else {
				$default_lang = $get_site_language;
			}
		}

		$dpad_args = array(
			'post_type'      	=> 'wc_dynamic_pricing',
			'post_status'    	=> 'publish',
			'orderby'       	=> 'menu_order',
			'order'          	=> 'ASC',
			'posts_per_page' 	=> - 1,
			'suppress_filters'	=> false,
			'fields' 			=> 'ids'
		);

		$get_all_dpad_query = new WP_Query( $dpad_args );
		$get_all_dpad       = $get_all_dpad_query->get_posts();

		$cart_array                = $woocommerce->cart->get_cart();
		$cart_sub_total            = $woocommerce->cart->get_subtotal();
		$subtax                    = $woocommerce->cart->get_subtotal_tax();
		$wtdc                      = get_option( 'woocommerce_tax_display_cart' );
		if( isset( $subtax ) && !empty( $subtax ) && 'incl' === $wtdc ) {
			$cart_sub_total = $cart_sub_total + $subtax;	
		}
		//$cart_sub_total              = $woocommerce->cart->cart_contents_total; // cart total with incl tax.
		$cart_final_products_array = array();
		$cart_products_subtotal    = 0;

		if ( ! empty( $get_all_dpad ) ) {
			$ij=1;
			foreach ( $get_all_dpad as $dpad ) {

				if ( ! empty( $sitepress ) ) {
					$dpad_id = apply_filters( 'wpml_object_id', $dpad, 'wc_dynamic_pricing', true, $default_lang );
				} else {
					$dpad_id = $dpad;
				}
				
				//First order for user Start
				if ( wcdrfc_fs()->is__premium_only() ) {
					if ( wcdrfc_fs()->can_use_premium_code() ) {
						$getFirstOrderForUser   	= get_post_meta( $dpad, 'first_order_for_user', true );
						$firstOrderForUser   		= ( isset( $getFirstOrderForUser ) && ! empty( $getFirstOrderForUser ) && 'on' === $getFirstOrderForUser ) ? true : false;
						if( $firstOrderForUser && is_user_logged_in() ){
							$current_user_id = get_current_user_id();
							$check_for_user = $this->dpad_check_first_order_for_user__premium_only( $current_user_id );
							if( !$check_for_user ){
								continue;
							}
						}
						$getUserLoginStatus  = get_post_meta( $dpad, 'user_login_status', true );
						$userLoginStatus   = ( isset( $getUserLoginStatus ) && ! empty( $getUserLoginStatus ) && 'on' === $getUserLoginStatus ) ? true : false;
						if( $userLoginStatus && !is_user_logged_in() ){
							continue;
						}
						$today =  strtolower( gmdate( "D" ) );
						$dpad_select_day_of_week = get_post_meta( $dpad, 'dpad_select_day_of_week', true ) ? get_post_meta( $dpad, 'dpad_select_day_of_week', true ) : 
						array();
						if( !in_array($today, $dpad_select_day_of_week, true) && !empty($dpad_select_day_of_week) ) {
							continue;
						}
					}
				}
				//First order for user End

				$is_passed = array();
				$cart_based_qty = 0;

				foreach ( $cart_array as  $woo_cart_item_for_qty ) {
					$cart_based_qty += $woo_cart_item_for_qty['quantity'];
				}

				$dpad_title          = get_the_title( $dpad );
				$title               = ! empty( $dpad_title ) ? __( $dpad_title, 'woo-conditional-discount-rules-for-checkout' ) : __( 'Fee', 'woo-conditional-discount-rules-for-checkout' );
				$getFeesCostOriginal = get_post_meta( $dpad, 'dpad_settings_product_cost', true );
				$getFeeType          = get_post_meta( $dpad, 'dpad_settings_select_dpad_type', true );

				if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
					if ( isset( $getFeeType ) && ! empty( $getFeeType ) && $getFeeType === 'fixed' ) {
						$getFeesCost = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $getFeesCostOriginal );
					} else {
						$getFeesCost = $getFeesCostOriginal;
					}
				} else {
					$getFeesCost = $getFeesCostOriginal;
				}
				
				$getFeesPerQtyFlag = '';
				if ( wcdrfc_fs()->is__premium_only() ) {
					if ( wcdrfc_fs()->can_use_premium_code() ) {
						$getFeesPerQtyFlag        = get_post_meta( $dpad, 'dpad_chk_qty_price', true );
						$getFeesPerQty            = get_post_meta( $dpad, 'dpad_per_qty', true );
						$extraProductCostOriginal = get_post_meta( $dpad, 'extra_product_cost', true );

						if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
							$extraProductCost = $woocommerce_wpml->multi_currency->prices->convert_price_amount( $extraProductCostOriginal );
						} else {
							$extraProductCost = $extraProductCostOriginal;
						}
					}
				}

				$getFeetaxable   = get_post_meta( $dpad, 'dpad_settings_select_taxable', true );
				$getFeeStartDate = get_post_meta( $dpad, 'dpad_settings_start_date', true );
				$getFeeEndDate   = get_post_meta( $dpad, 'dpad_settings_end_date', true );
				$getFeeStartTime = get_post_meta( $dpad, 'dpad_time_from', true );
				$getFeeEndTime   = get_post_meta( $dpad, 'dpad_time_to', true );
				$getFeeStatus    = get_post_meta( $dpad, 'dpad_settings_status', true );

				if ( isset( $getFeeStatus ) && $getFeeStatus === 'off' ) {
					continue;
				}

				$get_condition_array 	= get_post_meta( $dpad, 'dynamic_pricing_metabox', true );
				$general_rule_match 	= 'all';
				if ( wcdrfc_fs()->is__premium_only() ) {
					if ( wcdrfc_fs()->can_use_premium_code() ) {
						$cost_rule_match = get_post_meta( $dpad, 'cost_rule_match', true );
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
							$cost_on_product_rule_match                 = 'any';
							$cost_on_category_rule_match                = 'any';
						}
					}
				}
				/* Percentage Logic Start */
				if ( isset( $getFeesCost ) && ! empty( $getFeesCost ) ) {

					if ( ! empty( $get_condition_array ) ) {

						$cart_products_subtotal     = 0;
						$cart_cat_products_subtotal = 0;
						$cart_tag_products_subtotal = 0;

						$product_based_percentage_subtotal = 0;
						$percentage_subtotal               = 0;

						$product_specific_flag = 0;
						$products_based_qty    = 0;

						foreach ( $get_condition_array as $key => $condition ) {
							if ( array_search( 'product', $condition,true ) ) {

								$site_product_id           = '';
								$cart_final_products_array = array();

								/* Product Condition Start */
								if ( $condition['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
											foreach ( $cart_array as $key => $value ) {

												if ( ! empty( $sitepress ) ) {
													$site_product_id = apply_filters( 'wpml_object_id', $value['product_id'], 'product', true, $default_lang );
												} else {
													$site_product_id = $value['product_id'];
												}

												if ( (int)$product_id === (int)$site_product_id ) {
													$cart_final_products_array[] = $value;
												}
											}
										}
									}
								} elseif ( $condition['product_dpad_conditions_is'] === 'not_in' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
											foreach ( $cart_array as $key => $value ) {

												if ( ! empty( $sitepress ) ) {
													$site_product_id = apply_filters( 'wpml_object_id', $value['product_id'], 'product', true, $default_lang );
												} else {
													$site_product_id = $value['product_id'];
												}

												if ( (int)$product_id !== (int)$site_product_id ) {
													$cart_final_products_array[] = $value;
												}
											}
										}
									}
								}

								if ( ! empty( $cart_final_products_array ) ) {
									$product_specific_flag = 1;
									foreach ( $cart_final_products_array as $cart_item ) {

										$products_based_qty += $cart_item['quantity'];

										$line_item_subtotal     = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
										$cart_products_subtotal += $line_item_subtotal;
									}
								}
								/* Product Condition End */
							}
							if ( array_search( 'variableproduct', $condition,true ) ) {

								$site_product_id           = '';
								$cart_final_products_array = array();
								/* Variable Product Condition Start */

								if ( $condition['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
											foreach ( $cart_array as $key => $value ) {

												if ( ! empty( $sitepress ) ) {
													$site_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
												} else {
													$site_product_id = $value['variation_id'];
												}

												if ( (int)$product_id === (int)$site_product_id ) {
													$cart_final_products_array[] = $value;
												}
											}
										}
									}
								} elseif ( $condition['product_dpad_conditions_is'] === 'not_in' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
											foreach ( $cart_array as $key => $value ) {

												if ( ! empty( $sitepress ) ) {
													$site_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
												} else {
													$site_product_id = $value['variation_id'];
												}
												if ( (int)$product_id !== (int)$site_product_id ) {
													$cart_final_products_array[] = $value;
												}
											}
										}
									}
								}


								if ( ! empty( $cart_final_products_array ) ) {
									$product_specific_flag = 1;
									foreach ( $cart_final_products_array as $cart_item ) {

										$products_based_qty += $cart_item['quantity'];

										$line_item_subtotal     = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
										$cart_products_subtotal += $line_item_subtotal;
									}
								}
								/* Variable Product Condition End */
							}
							if ( array_search( 'category', $condition,true ) ) {

								/* Category Condition Start */
								$final_cart_products_cats_ids  = array();
								$cart_final_cat_products_array = array();

								$all_cats = get_terms(
									array(
										'taxonomy' => 'product_cat',
										'fields'   => 'ids',
									)
								);

								if ( $condition['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										foreach ( $condition['product_dpad_conditions_values'] as $category_id ) {
											$final_cart_products_cats_ids[] = $category_id;
										}
									}
								} elseif ( $condition['product_dpad_conditions_is'] === 'not_in' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										$final_cart_products_cats_ids = array_diff( $all_cats, $condition['product_dpad_conditions_values'] );
									}
								}

								$cat_args         = array(
									'post_type'      => 'product',
									'posts_per_page' => - 1,
									'order'          => 'ASC',
									'fields'         => 'ids',
									'suppress_filters' => false,
									'tax_query'      => array(
										array(
											'taxonomy' => 'product_cat',
											'field'    => 'term_id',
											'terms'    => $final_cart_products_cats_ids,
										),
									),
								);
								$cat_products_ids = get_posts( $cat_args ); // phpcs:ignore

								foreach ( $cart_array as $key => $value ) {
									if ( in_array( (int)$value['product_id'], dpad_convert_array_to_int($cat_products_ids),true ) ) {
										$cart_final_cat_products_array[] = $value;
									}
								}

								if ( ! empty( $cart_final_cat_products_array ) ) {
									$product_specific_flag = 1;
									foreach ( $cart_final_cat_products_array as $cart_item ) {

										$products_based_qty += $cart_item['quantity'];

										$line_item_subtotal         = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
										$cart_cat_products_subtotal += $line_item_subtotal;
									}
								}
								/* Category Condition End */
							}
							if ( array_search( 'tag', $condition,true ) ) {

								/* Tag Condition Start */
								$final_cart_products_tag_ids   = array();
								$cart_final_tag_products_array = array();

								$all_tags = get_terms(
									array(
										'taxonomy' => 'product_tag',
										'fields'   => 'ids',
									)
								);

								if ( $condition['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										foreach ( $condition['product_dpad_conditions_values'] as $tag_id ) {
											$final_cart_products_tag_ids[] = $tag_id;
										}
									}
								} elseif ( $condition['product_dpad_conditions_is'] === 'not_in' ) {
									if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
										$final_cart_products_tag_ids = array_diff( $all_tags, $condition['product_dpad_conditions_values'] );
									}
								}

								$tag_args         = array(
									'post_type'      => 'product',
									'posts_per_page' => - 1,
									'order'          => 'ASC',
									'fields'         => 'ids',
									'suppress_filters' => false,
									'tax_query'      => array(
										array(
											'taxonomy' => 'product_tag',
											'field'    => 'term_id',
											'terms'    => $final_cart_products_tag_ids,
										),
									),
								);
								$tag_products_ids = get_posts( $tag_args ); // phpcs:ignore

								foreach ( $cart_array as $key => $value ) {
									if ( in_array( (int)$value['product_id'], dpad_convert_array_to_int($tag_products_ids),true ) ) {
										$cart_final_tag_products_array[] = $value;
									}
								}

								if ( ! empty( $cart_final_tag_products_array ) ) {
									$product_specific_flag = 1;
									foreach ( $cart_final_tag_products_array as $cart_item ) {

										$products_based_qty += $cart_item['quantity'];

										$line_item_subtotal         = $cart_item['line_subtotal'] + $cart_item['line_subtotal_tax'];
										$cart_tag_products_subtotal += $line_item_subtotal;
									}
								}
								/* Tag Condition End */
							}
							$product_based_percentage_subtotal = $cart_products_subtotal + $cart_cat_products_subtotal + $cart_tag_products_subtotal;
						}
						
						if ( (int)$product_specific_flag === 1 ) {
							$percentage_subtotal = $product_based_percentage_subtotal;
						} else {
							$products_based_qty  = $cart_based_qty;
							$percentage_subtotal = $cart_sub_total;
						}
					}

					if ( isset( $getFeeType ) && ! empty( $getFeeType ) && $getFeeType === 'percentage' ) {
						
						$percentage_fee = ( $percentage_subtotal * $getFeesCost ) / 100;

						if ( $getFeesPerQtyFlag === 'on' ) {
							if ( $getFeesPerQty === 'qty_cart_based' ) {
								$dpad_cost = $percentage_fee + ( ( $cart_based_qty - 1 ) * $extraProductCost );
							} else if ( $getFeesPerQty === 'qty_product_based' ) {
								$dpad_cost = $percentage_fee + ( ( $products_based_qty - 1 ) * $extraProductCost );
							}
						} else {
							$dpad_cost = $percentage_fee;
						}
					} else {
						$fixed_fee = $getFeesCost;
						if ( $getFeesPerQtyFlag === 'on' ) {
							if ( $getFeesPerQty === 'qty_cart_based' ) {
								$dpad_cost = $fixed_fee + ( ( $cart_based_qty - 1 ) * $extraProductCost );
							} else if ( $getFeesPerQty === 'qty_product_based' ) {
								$dpad_cost = $fixed_fee + ( ( $products_based_qty - 1 ) * $extraProductCost );
							}
						} else {
							$dpad_cost = $fixed_fee;
						}
					}
				} else {
					$dpad_cost = 0;
				}

				$sale_product_check = get_post_meta( $dpad, 'dpad_sale_product', true );
				$wc_curr_version = $this->dpad_get_woo_version_number();
				if ( ! empty( $get_condition_array ) ) {
					$country_array           = array();
					$city_array              = array();
					$state_array             = array();
					$postcode_array          = array();
					$zone_array              = array();
					$product_array           = array();
					$variableproduct_array   = array();
					$category_array          = array();
					$tag_array               = array();
					$product_qty_array     	 = array();
					$product_count_array   	 = array();
					$user_array              = array();
					$user_role_array         = array();
					$user_mail_array         = array();
					$cart_total_array        = array();
					$cart_totalafter_array   = array();
					$total_spent_order_array = array();
					$spent_order_count_array = array();
					$last_spent_order_array  = array();
					$quantity_array          = array();
					$weight_array            = array();
					$coupon_array            = array();
					$shipping_class_array    = array();
					$payment_gateway         = array();
					$shipping_methods        = array();
					$shipping_total_array    = array();
					foreach ( $get_condition_array as $key => $value ) {
						if ( array_search( 'country', $value,true ) ) {
							$country_array[ $key ] = $value;
						}
						if ( array_search( 'city', $value,true ) ) {
							$city_array[ $key ] = $value;
						}
						if ( array_search( 'state', $value,true ) ) {
							$state_array[ $key ] = $value;
						}
						if ( array_search( 'postcode', $value,true ) ) {
							$postcode_array[ $key ] = $value;
						}
						if ( array_search( 'zone', $value,true ) ) {
							$zone_array[ $key ] = $value;
						}
						if ( array_search( 'product', $value,true ) ) {
							$product_array[ $key ] = $value;
						}
						if ( array_search( 'variableproduct', $value,true ) ) {
							$variableproduct_array[ $key ] = $value;
						}
						if ( array_search( 'category', $value,true ) ) {
							$category_array[ $key ] = $value;
						}
						if ( array_search( 'tag', $value,true ) ) {
							$tag_array[ $key ] = $value;
						}
						if ( array_search( 'product_qty', $value, true ) ) {
							$product_qty_array[ $key ] = $value;
						}
						if ( array_search( 'product_count', $value, true ) ) {
							$product_count_array[ $key ] = $value;
						}
						if ( array_search( 'user', $value,true ) ) {
							$user_array[ $key ] = $value;
						}
						if ( array_search( 'user_role', $value,true ) ) {
							$user_role_array[ $key ] = $value;
						}
						if ( array_search( 'user_mail', $value,true ) ) {
							$user_mail_array[ $key ] = $value;
						}
						if ( array_search( 'cart_total', $value,true ) ) {
							$cart_total_array[ $key ] = $value;
						}
						if ( array_search( 'cart_totalafter', $value,true ) ) {
							$cart_totalafter_array[ $key ] = $value;
						}
						if ( array_search( 'total_spent_order', $value,true ) ) {
							$total_spent_order_array[ $key ] = $value;
						}
						if ( array_search( 'spent_order_count', $value,true ) ) {
							$spent_order_count_array[ $key ] = $value;
						}
						if ( array_search( 'last_spent_order', $value,true ) ) {
							$last_spent_order_array[ $key ] = $value;
						}
						if ( array_search( 'quantity', $value,true ) ) {
							$quantity_array[ $key ] = $value;
						}
						if ( array_search( 'weight', $value,true ) ) {
							$weight_array[ $key ] = $value;
						}
						if ( array_search( 'coupon', $value,true ) ) {
							$coupon_array[ $key ] = $value;
						}
						if ( array_search( 'shipping_class', $value,true ) ) {
							$shipping_class_array[ $key ] = $value;
						}
						if ( array_search( 'payment', $value,true ) ) {
							$payment_gateway[ $key ] = $value;
						}
						if ( array_search( 'shipping_method', $value,true ) ) {
							$shipping_methods[ $key ] = $value;
						}
						if ( array_search( 'shipping_total', $value,true ) ) {
							$shipping_total_array[ $key ] = $value;
						}
					}

					$ap_rule_status          = get_post_meta( $dpad, 'ap_rule_status', true );

					//Check if is country exist
					if ( is_array( $country_array ) && isset( $country_array ) && ! empty( $country_array ) && ! empty( $cart_array ) ) {
						$selected_country 	= $woocommerce->customer->get_shipping_country();
						$is_sub_passed 			= array();
						// $passed_country                         = array();
						foreach ( $country_array as $key => $country ) {
							if ( 'is_equal_to' === $country['product_dpad_conditions_is'] ) {
								if ( ! empty( $country['product_dpad_conditions_values'] ) ) {
									if ( in_array( $selected_country, $country['product_dpad_conditions_values'], true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_country'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_country'] = 'no';
									}
								}
								if ( empty( $country['product_dpad_conditions_values'] ) ) {
									$is_sub_passed[ $key ]['has_dpad_based_on_country'] = 'yes';
								}
							}
							if ( 'not_in' === $country['product_dpad_conditions_is'] ) {
								if ( ! empty( $country['product_dpad_conditions_values'] ) ) {
									if ( in_array( $selected_country, $country['product_dpad_conditions_values'], true ) || in_array( 'all', $country['product_dpad_conditions_values'], true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_country'] = 'no';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_country'] = 'yes';
									}
								}
							}
						}
						$country_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_country', $general_rule_match );
						if ( 'yes' === $country_passed ) {
							$is_passed['has_dpad_based_on_country'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_country'] = 'no';
						}
					}

					//Check if is city exist
					if ( is_array( $city_array ) && isset( $city_array ) && ! empty( $city_array ) && ! empty( $cart_array ) ) {
						$selected_city  = $woocommerce->customer->get_shipping_city();
						$is_sub_passed 		= array();
						foreach ( $city_array as $key => $city ) {
							if ( ! empty( $city['product_dpad_conditions_values'] ) ) {

								$citystr        = str_replace( PHP_EOL, "<br/>", $city['product_dpad_conditions_values'] );
								$city_val_array = explode( '<br/>', $citystr );
								$city_val_array = array_map( 'trim', $city_val_array );

								if ( 'is_equal_to' === $city['product_dpad_conditions_is'] ) {
									if ( in_array( $selected_city, $city_val_array, true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_city'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_city'] = 'no';
									}
								}
								if ( 'not_in' === $city['product_dpad_conditions_is'] ) {
									if ( in_array( $selected_city, $city_val_array, true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_city'] = 'no';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_city'] = 'yes';
									}
								}
							}
						}
						$city_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_city', $general_rule_match );
						if ( 'yes' === $city_passed ) {
							$is_passed['has_dpad_based_on_city'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_city'] = 'no';
						}
					}

					//Check if is state exist (Premium)
					if ( is_array( $state_array ) && isset( $state_array ) && ! empty( $state_array ) && ! empty( $cart_array ) ) {
						$country        = $woocommerce->customer->get_shipping_country();
						$state          = $woocommerce->customer->get_shipping_state();
						$selected_state	= $country . ':' . $state;
						$is_sub_passed 		= array();
						foreach ( $state_array as $key => $state ) {
							if ( ! empty( $state['product_dpad_conditions_values'] ) ) {
								if ( 'is_equal_to' === $state['product_dpad_conditions_is'] ) {
									if ( in_array( $selected_state, $state['product_dpad_conditions_values'], true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_state'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_state'] = 'no';
									}
								}
								if ( 'not_in' === $state['product_dpad_conditions_is'] ) {
									if ( in_array( $selected_state, $state['product_dpad_conditions_values'], true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_state'] = 'no';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_state'] = 'yes';
									}
								}
							}
						}
						$state_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_state', $general_rule_match );
						if ( 'yes' === $state_passed ) {
							$is_passed['has_dpad_based_on_state'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_state'] = 'no';
						}
					}

					//Check if is postcode exist (Premium)
					if ( is_array( $postcode_array ) && isset( $postcode_array ) && ! empty( $postcode_array ) && ! empty( $cart_array ) ) {
						$selected_postcode  = $woocommerce->customer->get_shipping_postcode();
						$is_sub_passed 			= array();
						foreach ( $postcode_array as $key => $postcode ) {
							if ( ! empty( $postcode['product_dpad_conditions_values'] ) ) {
								$postcodestr        = str_replace( PHP_EOL, "<br/>", $postcode['product_dpad_conditions_values'] );
								$postcode_val_array = explode( '<br/>', $postcodestr );
								$postcode_val_array = array_map( 'trim', $postcode_val_array );

								if ( 'is_equal_to' === $postcode['product_dpad_conditions_is'] ) {
									if ( in_array( $selected_postcode, $postcode_val_array, true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_postcode'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_postcode'] = 'no';
									}
								}
								if ( 'not_in' === $postcode['product_dpad_conditions_is'] ) {
									if ( in_array( $selected_postcode, $postcode_val_array, true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_postcode'] = 'no';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_postcode'] = 'yes';
									}
								}
							}
						}
						$postcode_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_postcode', $general_rule_match );
						if ( 'yes' === $postcode_passed ) {
							$is_passed['has_dpad_based_on_postcode'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_postcode'] = 'no';
						}
					}

					//Check if is zone exist (Premium)
					if ( is_array( $zone_array ) && isset( $zone_array ) && ! empty( $zone_array ) && ! empty( $cart_array ) ) {
						$get_zonelist    = $this->wc_get_shipping_zone();
						$is_sub_passed		 = array();
						foreach ( $zone_array as $key => $zone ) {
							if ( ! empty( $zone['product_dpad_conditions_values'] ) ) {
								if ( 'is_equal_to' === $zone['product_dpad_conditions_is'] ) {
									if ( in_array( $get_zonelist, $zone['product_dpad_conditions_values'],true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_zone'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_zone'] = 'no';
									}
								}
								if ( 'not_in' === $zone['product_dpad_conditions_is'] ) {
									if ( in_array( $get_zonelist, $zone['product_dpad_conditions_values'],true ) ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_zone'] = 'no';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_zone'] = 'yes';
									}
								}
							}
						}
						$zone_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_zone', $general_rule_match );
						if ( 'yes' === $zone_passed ) {
							$is_passed['has_dpad_based_on_zone'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_zone'] = 'no';
						}
					}
					
					//Check if is product exist
					if ( is_array( $product_array ) && isset( $product_array ) && ! empty( $product_array ) && ! empty( $cart_array ) ) {

						$cart_products_array = array();
						$cart_product        = $this->dpad_array_column( $cart_array, 'product_id' );
						$product_ids_on_sale = wc_get_product_ids_on_sale();

						if( "exclude" === $sale_product_check ){
							$cart_product = array_diff($cart_product, $product_ids_on_sale);
						}

						if ( isset( $cart_product ) && ! empty( $cart_product ) ) {
							foreach ( $cart_product as $key => $cart_product_id ) {
								if ( ! empty( $sitepress ) ) {
									$cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
								} else {
									$cart_products_array[] = $cart_product_id;
								}
							}
						}

						$is_sub_passed 		= array();
						foreach ( $product_array as $key => $product ) {
							if ( ! empty( $product['product_dpad_conditions_values'] ) ) {
								if ( 'is_equal_to' === $product['product_dpad_conditions_is'] ) {
									foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
										settype( $product_id, 'integer' );
										if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_product'] = 'yes';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_product'] = 'no';
										}
									}
								}
								if ( $product['product_dpad_conditions_is'] === 'not_in' ) {
									foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
										settype( $product_id, 'integer' );
										if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_product'] = 'no';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_product'] = 'yes';
										}
									}
								}
							}
						}
						$product_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_product', $general_rule_match );
						if ( 'yes' === $product_passed ) {
							$is_passed['has_dpad_based_on_product'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_product'] = 'no';
						}
					}

					//Check if is variable product exist
					if ( is_array( $variableproduct_array ) && isset( $variableproduct_array ) && ! empty( $variableproduct_array ) && ! empty( $cart_array ) ) {

						$cart_products_array = array();
						$cart_product        = $this->dpad_array_column( $cart_array, 'variation_id' );
						$product_ids_on_sale = wc_get_product_ids_on_sale();
						
						if( "exclude" === $sale_product_check ){
							$cart_product = array_diff($cart_product, $product_ids_on_sale);
						}
						
						if ( isset( $cart_product ) && ! empty( $cart_product ) ) {

							foreach ( $cart_product as $key => $cart_product_id ) {

								if ( ! empty( $sitepress ) ) {
									$cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
								} else {
									$cart_products_array[] = $cart_product_id;
								}
							}
						}
						$is_sub_passed = array();
						foreach ( $variableproduct_array as $key => $product ) {
							if ( ! empty( $product['product_dpad_conditions_values'] ) ) {
								if ( $product['product_dpad_conditions_is'] === 'is_equal_to' ) {
									foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
										settype( $product_id, 'integer' );
										if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_variable_product'] = 'yes';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_variable_product'] = 'no';
										}
									}
								}
								if ( $product['product_dpad_conditions_is'] === 'not_in' ) {
									foreach ( $product['product_dpad_conditions_values'] as $product_id ) {
										settype( $product_id, 'integer' );
										if ( in_array( $product_id, dpad_convert_array_to_int($cart_products_array), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_variable_product'] = 'no';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_variable_product'] = 'yes';
										}
									}
								}
							}
						}
						$variable_prd_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_variable_product', $general_rule_match );
						if ( 'yes' === $variable_prd_passed ) {
							$is_passed['has_dpad_based_on_variable_product'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_variable_product'] = 'no';
						}
					}

					//Check if is Category exist
					if ( is_array( $category_array ) && isset( $category_array ) && ! empty( $category_array ) && ! empty( $cart_array ) ) {
						$cart_product           = $this->dpad_array_column( $cart_array, 'product_id' );
						$cart_category_id_array = array();
						$is_sub_passed 				= array();
						$cart_products_array    = array();
						$product_ids_on_sale 	= wc_get_product_ids_on_sale();
						
						if( "exclude" === $sale_product_check ){
							$cart_product = array_diff($cart_product, $product_ids_on_sale);
						}
						
						if ( isset( $cart_product ) && ! empty( $cart_product ) ) {
							foreach ( $cart_product as $key => $cart_product_id ) {
								if ( ! empty( $sitepress ) ) {
									$cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
								} else {
									$cart_products_array[] = $cart_product_id;
								}
							}
						}

						if ( ! empty( $cart_products_array ) ) {
							foreach ( $cart_products_array as $product ) {
								$cart_product_category = wp_get_post_terms( $product, 'product_cat', array( 'fields' => 'ids' ) );
								if ( isset( $cart_product_category ) && ! empty( $cart_product_category ) && is_array( $cart_product_category ) ) {
									$cart_category_id_array[] = $cart_product_category;
								}
							}
							$get_cat_all = array_unique( $this->array_flatten( $cart_category_id_array ) );
							foreach ( $category_array as $key => $category ) {
								if ( ! empty( $category['product_dpad_conditions_values'] ) ) {
									if ( $category['product_dpad_conditions_is'] === 'is_equal_to' ) {
										foreach ( $category['product_dpad_conditions_values'] as $category_id ) {
											settype( $category_id, 'integer' );
											if ( in_array( $category_id, dpad_convert_array_to_int($get_cat_all), true ) ) {
												$is_sub_passed[ $key ]['has_dpad_based_on_category'] = 'yes';
												break;
											} else {
												$is_sub_passed[ $key ]['has_dpad_based_on_category'] = 'no';
											}
										}
									}
									if ( $category['product_dpad_conditions_is'] === 'not_in' ) {
										foreach ( $category['product_dpad_conditions_values'] as $category_id ) {
											settype( $category_id, 'integer' );
											if ( in_array( $category_id, dpad_convert_array_to_int($get_cat_all), true ) ) {
												$is_sub_passed[ $key ]['has_dpad_based_on_category'] = 'no';
												break;
											} else {
												$is_sub_passed[ $key ]['has_dpad_based_on_category'] = 'yes';
											}
										}
									}
								}
							}
							$category_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_category', $general_rule_match );
							if ( 'yes' === $category_passed ) {
								$is_passed['has_dpad_based_on_category'] = 'yes';
							} else {
								$is_passed['has_dpad_based_on_category'] = 'no';
							}
						}
					}

					//Check if is tag exist
					if ( is_array( $tag_array ) && isset( $tag_array ) && ! empty( $tag_array ) && ! empty( $cart_array ) ) {
						$cart_product                       = $this->dpad_array_column( $cart_array, 'product_id' );
						$tagid                              = array();
						$is_sub_passed							= array();
						$cart_products_array                = array();
						$product_ids_on_sale 				= wc_get_product_ids_on_sale();
						
						if( "exclude" === $sale_product_check ){
							$cart_product = array_diff($cart_product, $product_ids_on_sale);
						}
						
						if ( isset( $cart_product ) && ! empty( $cart_product ) ) {
							foreach ( $cart_product as $key => $cart_product_id ) {
								if ( ! empty( $sitepress ) ) {
									$cart_products_array[] = apply_filters( 'wpml_object_id', $cart_product_id, 'product', true, $default_lang );
								} else {
									$cart_products_array[] = $cart_product_id;
								}
							}
						}

						foreach ( $cart_products_array as $product ) {
							$cart_product_tag = wp_get_post_terms( $product, 'product_tag', array( 'fields' => 'ids' ) );
							if ( isset( $cart_product_tag ) && ! empty( $cart_product_tag ) && is_array( $cart_product_tag ) ) {
								$tagid[] = $cart_product_tag;
							}
						}
						$get_tag_all = array_unique( $this->array_flatten( $tagid ) );
						foreach ( $tag_array as $key => $tag ) {
							if ( ! empty( $tag['product_dpad_conditions_values'] ) ) {
								if ( $tag['product_dpad_conditions_is'] === 'is_equal_to' ) {
									foreach ( $tag['product_dpad_conditions_values'] as $tag_id ) {
										settype( $tag_id, 'integer' );
										if ( in_array( $tag_id, $get_tag_all, true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_tag'] = 'yes';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_tag'] = 'no';
										}
									}
								}
								if ( $tag['product_dpad_conditions_is'] === 'not_in' ) {
									foreach ( $tag['product_dpad_conditions_values'] as $tag_id ) {
										settype( $tag_id, 'integer' );
										if ( in_array( $tag_id, $get_tag_all, true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_tag'] = 'no';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_tag'] = 'yes';
										}
									}
								}
							}
						}
						$tag_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_tag', $general_rule_match );
						if ( 'yes' === $tag_passed ) {
							$is_passed['has_dpad_based_on_tag'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_tag'] = 'no';
						}
					}

					//Check if product quantity exist
					if ( is_array( $product_qty_array ) && isset( $product_qty_array ) && ! empty( $product_qty_array ) && ! empty( $cart_array ) ) {
						$products_based_qty = $this->dpad_product_qty_on_rules_ps( $dpad, $cart_array, 0, 0, $sitepress, $default_lang );
						
						$quantity_total = $products_based_qty[0] > 0 ? $products_based_qty[0] : 0;
						$is_sub_passed 		= array();
						settype( $quantity_total, 'float' );

						foreach ( $product_qty_array as $key => $quantity ) {
							settype( $quantity['product_dpad_conditions_values'], 'float' );
							if ( ! empty( $quantity['product_dpad_conditions_values'] ) ) {
								if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
									if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
									if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
									if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
									if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
									if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_qty'] = 'yes';
									}
								}
							}
						}
						$product_qty_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_product_qty', $general_rule_match );
						if ( 'yes' === $product_qty_passed ) {
							$is_passed['has_dpad_based_on_product_qty'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_product_qty'] = 'no';
						}
					}

					//Check if product quantity exist
					if ( is_array( $product_count_array ) && isset( $product_count_array ) && ! empty( $product_count_array ) && ! empty( $cart_array ) ) {
						$quantity_total = 0;
						$is_sub_passed = array();
						
						if ( array_search( 'product', $condition, true ) 
						|| array_search( 'variableproduct', $condition, true ) 
						|| array_search( 'category', $condition, true ) 
						|| array_search( 'tag', $condition, true ) ) {
							$products_based_count = $this->dpad_product_count_on_rules_ps( $dpad, $cart_array, 0, 0, $sitepress, $default_lang );
						
							$quantity_total = $products_based_count;
						} else {
							$quantity_total = count($cart_array);
						}						
						settype( $quantity_total, 'float' );
						foreach ( $product_count_array as $key => $quantity ) {
							settype( $quantity['product_dpad_conditions_values'], 'float' );
							if ( ! empty( $quantity['product_dpad_conditions_values'] ) ) {

								if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
									if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
									if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
									if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
									if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
									if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_product_count'] = 'yes';
									}
								}
							}
						}
						$product_count_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_product_count', $general_rule_match );
						if ( 'yes' === $product_count_passed ) {
							$is_passed['has_dpad_based_on_product_count'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_product_count'] = 'no';
						}
					}

					//Check if is user exist
					if ( is_array( $user_array ) && isset( $user_array ) && ! empty( $user_array ) && ! empty( $cart_array ) && is_user_logged_in()) {
						
						$current_user_id 	= get_current_user_id();
						$is_sub_passed			= array();
						settype( $current_user_id, 'integer' );
						foreach ( $user_array as $key => $user ) {
							if ( 'is_equal_to' === $user['product_dpad_conditions_is'] ) {
								if ( in_array( $current_user_id, dpad_convert_array_to_int($user['product_dpad_conditions_values']), true ) ) {
									$is_sub_passed[ $key ]['has_dpad_based_on_user'] = 'yes';
								} else {
									$is_sub_passed[ $key ]['has_dpad_based_on_user'] = 'no';
								}
							}
							if ( 'not_in' === $user['product_dpad_conditions_is'] ) {
								if ( in_array( $current_user_id, dpad_convert_array_to_int($user['product_dpad_conditions_values']), true ) ) {
									$is_sub_passed[ $key ]['has_dpad_based_on_user'] = 'no';
								} else {
									$is_sub_passed[ $key ]['has_dpad_based_on_user'] = 'yes';
								}
							}
						}
						$user_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_user', $general_rule_match );
						if ( 'yes' === $user_passed ) {
							$is_passed['has_dpad_based_on_user'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_user'] = 'no';
						}
					}

					//Check if is user role exist (Premium)
					if ( is_array( $user_role_array ) && !empty($user_role_array) && isset( $user_role_array ) && ! empty( $user_role_array ) && ! empty( $cart_array )  ) {
						$passed_user_role 	= array();
						$is_sub_passed 		= array();
						/**
						 * check user loggedin or not
						 */
						global $current_user;
						if ( is_user_logged_in() ) {
                            $current_user_role = $current_user->roles;
                        } else {
                            $current_user_role = array('guest');
                        }

						if ( is_array( $current_user_role ) && isset( $current_user_role ) && ! empty( $current_user_role ) ) {
							foreach ( $user_role_array as $key => $user_role ) {
								if( ! empty($user_role['product_dpad_conditions_values'] ) ) {
									foreach ( $current_user_role as $current_user_all_role ) {
										if ( 'is_equal_to' === $user_role['product_dpad_conditions_is'] ) {
											if ( in_array( $current_user_all_role, $user_role['product_dpad_conditions_values'], true ) ) {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_role'] = 'yes';
											} else {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_role'] = 'no';
											}
										}
										if ( 'not_in' === $user_role['product_dpad_conditions_is'] ) {
											if ( in_array( $current_user_all_role, $user_role['product_dpad_conditions_values'], true ) ) {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_role'] = 'no';
											} else {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_role'] = 'yes';
											}
										}
									}
								}
							}
						}
						$user_role_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_user_role', $general_rule_match );
						if ( 'yes' === $user_role_passed ) {
							$is_passed['has_dpad_based_on_user_role'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_user_role'] = 'no';
						}
					}

					//Check if is user mail exist (Premium)
					if ( is_array( $user_mail_array ) && !empty($user_mail_array) && isset( $user_mail_array ) && ! empty( $cart_array ) ) {

						global $current_user;
						if( isset($_POST['post_data']) && !empty($_POST['post_data']) ){ //phpcs:ignore
							parse_str($_POST['post_data'], $post_data); //phpcs:ignore
							$billing_email = sanitize_email($post_data['billing_email']);
						}
						if ( isset($billing_email) && !empty($billing_email) ) {
							$current_user_mail = $billing_email;
						} else {
							$current_user_mail = $current_user->user_email;
						}
						$is_sub_passed = array();

						if ( isset( $current_user_mail ) && ! empty( $current_user_mail ) && ! empty( $cart_array ) ) {
							
							$current_user_mail = explode("@",$current_user_mail);

							foreach ( $user_mail_array as $key => $user_mail ) {
								if( !empty($user_mail['product_dpad_conditions_values']) ){
									
									$usermailstr         = str_replace( PHP_EOL, "<br/>", $user_mail['product_dpad_conditions_values'] );
									$user_mail_val_array = explode( '<br/>', $usermailstr );
									foreach( $user_mail_val_array as $user_mail_val ){
										
										$user_mail_val_array = trim($user_mail_val);

										if ( $user_mail['product_dpad_conditions_is'] === 'user_name' ) {

											if($user_mail_val_array === $current_user_mail[0]) {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_mail'] = 'yes';
											} else {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_mail'] = 'no';
											}
										} else if( $user_mail['product_dpad_conditions_is'] === 'domain_name' ) {
											
											if($user_mail_val_array === $current_user_mail[1]) {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_mail'] = 'yes';
											} else {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_mail'] = 'no';
											}
										} else {
											$full_mail = implode("@",$current_user_mail);
											if($user_mail_val_array === $full_mail) {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_mail'] = 'yes';
											} else {
												$is_sub_passed[ $key ]['has_dpad_based_on_user_mail'] = 'no';
											}
										}
									}
								}
							}
						}
						$user_mail_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_user_mail', $general_rule_match );
						if ( 'yes' === $user_mail_passed ) {
							$is_passed['has_dpad_based_on_user_mail'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_user_mail'] = 'no';
						}
					}
					
					//Check if is coupon exist (Premium)
					if ( is_array( $coupon_array ) && isset( $coupon_array ) && ! empty( $coupon_array ) && ! empty( $cart_array ) ) {
						$couponId  = array();
						$is_sub_passed = array();

						if ( $wc_curr_version >= 3.0 ) {
							$cart_coupon = WC()->cart->get_coupons();
						} else {
							$cart_coupon = isset( $woocommerce->cart->coupons ) && ! empty( $woocommerce->cart->coupons ) ? $woocommerce->cart->coupons : array();
						}

						if ( ! empty( $cart_coupon ) ) {
							foreach ( $cart_coupon as $cartCoupon ) {
								if ( $cartCoupon->is_valid() && isset( $cartCoupon ) && ! empty( $cartCoupon ) ) {
									if ( $wc_curr_version >= 3.0 ) {
										$couponId[] = $cartCoupon->get_id();
									} else {
										$couponId[] = $cartCoupon->id;
									}
								}
							}
						}
						
						foreach ( $coupon_array as $key => $coupon ) {
							if ( ! empty( $coupon['product_dpad_conditions_values'] ) ) {
								$product_dpad_conditions_values = array_map( 'intval', $coupon['product_dpad_conditions_values'] );

								if ( 'is_equal_to' === $coupon['product_dpad_conditions_is'] ) {
									foreach ( $product_dpad_conditions_values as $coupon_id ) {
										settype( $coupon_id, 'integer' );
										if ( in_array( $coupon_id, dpad_convert_array_to_int($couponId), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_coupon'] = 'yes';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_coupon'] = 'no';
										}
									}
								}

								if ( 'not_in' === $coupon['product_dpad_conditions_is'] ) {
									foreach ( $product_dpad_conditions_values as $coupon_id ) {
										settype( $coupon_id, 'integer' );
										if ( in_array( $coupon_id, dpad_convert_array_to_int($couponId), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_coupon'] = 'no';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_coupon'] = 'yes';
										}
									}
								}
							}
						}
						$coupon_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_coupon', $general_rule_match );
						if ( 'yes' === $coupon_passed ) {
							$is_passed['has_dpad_based_on_coupon'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_coupon'] = 'no';
						}
					}

					//Check if is Cart Subtotal (Before Discount) exist
					if ( is_array( $cart_total_array ) && isset( $cart_total_array ) && ! empty( $cart_total_array ) && ! empty( $cart_array ) ) {

						$total = 0;
						$product_ids_on_sale = wc_get_product_ids_on_sale();

						if( "exclude" === $sale_product_check ){
							foreach($cart_array as $value){
								$product_id = $value['variation_id'] ? $value['variation_id'] : $value['product_id'];
								if( !in_array($product_id, $product_ids_on_sale) ){
									$total += $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $value['data'], $value['quantity'] ) );	
								}
							}
						} else {
							if ( $wc_curr_version >= 3.0 ) {
								$total = $this->dpad_remove_currency( $woocommerce->cart->get_cart_subtotal() );
							} else {
								$total = $woocommerce->cart->subtotal;
							}	
						}
						
						if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
							$new_total = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $total );
						} else {
							$new_total = $total;
						}
                        
						settype( $new_total, 'float' );
						$is_sub_passed = array();

						foreach ( $cart_total_array as $key => $cart_total ) {
							settype( $cart_total['product_dpad_conditions_values'], 'float' );

							if ( ! empty( $cart_total['product_dpad_conditions_values'] ) ) {
								if ( $cart_total['product_dpad_conditions_is'] === 'is_equal_to' ) {	
									if ( $cart_total['product_dpad_conditions_values'] === $new_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
										break;
									}
								}

								if ( $cart_total['product_dpad_conditions_is'] === 'less_equal_to' ) {
									if ( $cart_total['product_dpad_conditions_values'] >= $new_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
										break;
									}
								}

								if ( $cart_total['product_dpad_conditions_is'] === 'less_then' ) {
									if ( $cart_total['product_dpad_conditions_values'] > $new_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
										break;
									}
								}

								if ( $cart_total['product_dpad_conditions_is'] === 'greater_equal_to' ) {
									if ( $cart_total['product_dpad_conditions_values'] <= $new_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
										break;
									}
								}
							
								if ( $cart_total['product_dpad_conditions_is'] === 'greater_then' ) {
									if ( $cart_total['product_dpad_conditions_values'] < $new_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
										break;
									}
								}
							
								if ( $cart_total['product_dpad_conditions_is'] === 'not_in' ) {
									if ( $new_total === $cart_total['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_cart_total'] = 'yes';
									}
								}
							}
						}
						$cart_total_before_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_cart_total', $general_rule_match );
						if ( 'yes' === $cart_total_before_passed ) {
							$is_passed['has_dpad_based_on_cart_total'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_cart_total'] = 'no';
						}
					}

					//Check if is Cart Subtotal (After Discount) exist (Premium)
					if ( is_array( $cart_totalafter_array ) && isset( $cart_totalafter_array ) && ! empty( $cart_totalafter_array ) && ! empty( $cart_array ) ) {
						$totalprice = 0;
						$product_ids_on_sale = wc_get_product_ids_on_sale();

						if( "exclude" === $sale_product_check ){
							foreach($cart_array as $value){
								$product_id = $value['variation_id'] ? $value['variation_id'] : $value['product_id'];
								if( !in_array($product_id, $product_ids_on_sale) ){
									$totalprice += $this->dpad_remove_currency( WC()->cart->get_product_subtotal( $value['data'], $value['quantity'] ) );	
								}
							}
						} else {
							if ( $wc_curr_version >= 3.0 ) {
								$totalprice = $this->dpad_remove_currency( $woocommerce->cart->get_cart_subtotal() );
							} else {
								$totalprice = $woocommerce->cart->subtotal;
							}
						}
						$is_sub_passed = array();
						$totaldisc   = $this->dpad_remove_currency( $woocommerce->cart->get_total_discount() );
						if( '' !== $totaldisc && 0.0 !== $totaldisc ) {
							$resultprice = $totalprice - $totaldisc;
							if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
								$new_resultprice = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultprice );
							} else {
								$new_resultprice = $resultprice;
							}
							settype( $new_resultprice, 'float' );

							foreach ( $cart_totalafter_array as $key => $cart_totalafter ) {
								settype( $cart_totalafter['product_fees_conditions_values'], 'float' );

								if ( ! empty( $cart_totalafter['product_dpad_conditions_values'] ) ) {

									if ( $cart_totalafter['product_dpad_conditions_is'] === 'is_equal_to' ) {
										if ( $cart_totalafter['product_dpad_conditions_values'] === $new_resultprice ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
											break;
										}
									}
								
									if ( $cart_totalafter['product_dpad_conditions_is'] === 'less_equal_to' ) {
										if ( $cart_totalafter['product_dpad_conditions_values'] >= $new_resultprice ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
											break;
										}
									}
								
									if ( $cart_totalafter['product_dpad_conditions_is'] === 'less_then' ) {
										if ( $cart_totalafter['product_dpad_conditions_values'] > $new_resultprice ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
											break;
										}
									}
								
									if ( $cart_totalafter['product_dpad_conditions_is'] === 'greater_equal_to' ) {
										if ( $cart_totalafter['product_dpad_conditions_values'] <= $new_resultprice ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
											break;
										}
									}
								
									if ( $cart_totalafter['product_dpad_conditions_is'] === 'greater_then' ) {
										if ( $cart_totalafter['product_dpad_conditions_values'] < $new_resultprice ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
											break;
										}
									}
								
									if ( $cart_totalafter['product_dpad_conditions_is'] === 'not_in' ) {
										if ( $new_resultprice === $cart_totalafter['product_dpad_conditions_values'] ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'no';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_cart_totalafter'] = 'yes';
										}
									}
								}
							}
						}
						$cart_total_after_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_cart_totalafter', $general_rule_match );
						if ( 'yes' === $cart_total_after_passed ) {
							$is_passed['has_dpad_based_on_cart_totalafter'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_cart_totalafter'] = 'no';
						}
					}

					//Check if is Total order spent exist
					if ( is_array( $total_spent_order_array ) && isset( $total_spent_order_array ) && ! empty( $total_spent_order_array ) && ! empty( $cart_array ) && is_user_logged_in() ) {
						
						global $current_user;
						$totalprice 	= 0;
						$resultprice 	= wc_get_customer_total_spent( $current_user->ID );
						$is_sub_passed 	= array();
						if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
							$new_resultprice = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultprice );
						} else {
							$new_resultprice = $resultprice;
						}
						settype($new_resultprice, 'float');
						
						foreach ( $total_spent_order_array as $key => $total_spent_order ) {
							settype($total_spent_order['product_dpad_conditions_values'], 'float');
							if ( $total_spent_order['product_dpad_conditions_is'] === 'is_equal_to' ) {
								if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $total_spent_order['product_dpad_conditions_values'] === $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $total_spent_order['product_dpad_conditions_is'] === 'less_equal_to' ) {
								if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $total_spent_order['product_dpad_conditions_values'] >= $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $total_spent_order['product_dpad_conditions_is'] === 'less_then' ) {
								if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $total_spent_order['product_dpad_conditions_values'] > $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $total_spent_order['product_dpad_conditions_is'] === 'greater_equal_to' ) {
								if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $total_spent_order['product_dpad_conditions_values'] <= $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $total_spent_order['product_dpad_conditions_is'] === 'greater_then' ) {
								if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $total_spent_order['product_dpad_conditions_values'] < $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $total_spent_order['product_dpad_conditions_is'] === 'not_in' ) {
								if ( ! empty( $total_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $new_resultprice === $total_spent_order['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_total_spent_order'] = 'yes';
									}
								}
							}
						}
						$total_spent_order_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_total_spent_order', $general_rule_match );
						if ( 'yes' === $total_spent_order_passed ) {
							$is_passed['has_dpad_based_on_total_spent_order'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_total_spent_order'] = 'no';
						}
					}

					//Check if is Total order count exist
					if ( is_array( $spent_order_count_array ) && isset( $spent_order_count_array ) && ! empty( $spent_order_count_array ) && ! empty( $cart_array ) && is_user_logged_in() ) {
						
						global $current_user;
						$user_id 		= $current_user->ID;
						$resultcount 	= $this->dpad_check_order_for_user__premium_only( $user_id, true);
						$is_sub_passed 	= array();

						if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
							$new_resultcount = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultcount );
						} else {
							$new_resultcount = $resultcount;
						}
						settype($new_resultcount, 'float');
						
						foreach ( $spent_order_count_array as $key => $spent_order_count ) {
							settype($spent_order_count['product_dpad_conditions_values'], 'float');
							if ( $spent_order_count['product_dpad_conditions_is'] === 'is_equal_to' ) {
								if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
									if ( $spent_order_count['product_dpad_conditions_values'] === $new_resultcount ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
										break;
									}
								}
							}
							if ( $spent_order_count['product_dpad_conditions_is'] === 'less_equal_to' ) {
								if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
									if ( $spent_order_count['product_dpad_conditions_values'] >= $new_resultcount ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
										break;
									}
								}
							}
							if ( $spent_order_count['product_dpad_conditions_is'] === 'less_then' ) {
								if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
									if ( $spent_order_count['product_dpad_conditions_values'] > $new_resultcount ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
										break;
									}
								}
							}
							if ( $spent_order_count['product_dpad_conditions_is'] === 'greater_equal_to' ) {
								if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
									if ( $spent_order_count['product_dpad_conditions_values'] <= $new_resultcount ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
										break;
									}
								}
							}
							if ( $spent_order_count['product_dpad_conditions_is'] === 'greater_then' ) {
								if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
									if ( $spent_order_count['product_dpad_conditions_values'] < $new_resultcount ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
										break;
									}
								}
							}
							if ( $spent_order_count['product_dpad_conditions_is'] === 'not_in' ) {
								if ( ! empty( $spent_order_count['product_dpad_conditions_values'] ) ) {
									if ( $new_resultcount === $spent_order_count['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_spent_order_count'] = 'yes';
									}
								}
							}
						}
						$spent_order_count_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_spent_order_count', $general_rule_match );
						if ( 'yes' === $spent_order_count_passed ) {
							$is_passed['has_dpad_based_on_spent_order_count'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_spent_order_count'] = 'no';
						}
					}

					//Check if is Last order spent exist
					if ( is_array( $last_spent_order_array ) && isset( $last_spent_order_array ) && ! empty( $last_spent_order_array ) && ! empty( $cart_array ) && is_user_logged_in() ) {
						
						global $current_user;
						$user_id 		= $current_user->ID;
						$resultprice 	= $this->dpad_check_order_for_user__premium_only($user_id);
						$is_sub_passed 	= array();

						if ( isset( $woocommerce_wpml ) && ! empty( $woocommerce_wpml->multi_currency ) ) {
							$new_resultprice = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $resultprice );
						} else {
							$new_resultprice = $resultprice;
						}
						settype($new_resultprice, 'float');
						
						foreach ( $last_spent_order_array as $key => $last_spent_order ) {
							settype($last_spent_order['product_dpad_conditions_values'], 'float');
							if ( $last_spent_order['product_dpad_conditions_is'] === 'is_equal_to' ) {
								if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $last_spent_order['product_dpad_conditions_values'] === $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $last_spent_order['product_dpad_conditions_is'] === 'less_equal_to' ) {
								if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $last_spent_order['product_dpad_conditions_values'] >= $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $last_spent_order['product_dpad_conditions_is'] === 'less_then' ) {
								if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $last_spent_order['product_dpad_conditions_values'] > $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $last_spent_order['product_dpad_conditions_is'] === 'greater_equal_to' ) {
								if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $last_spent_order['product_dpad_conditions_values'] <= $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $last_spent_order['product_dpad_conditions_is'] === 'greater_then' ) {
								if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $last_spent_order['product_dpad_conditions_values'] < $new_resultprice ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
										break;
									}
								}
							}
							if ( $last_spent_order['product_dpad_conditions_is'] === 'not_in' ) {
								if ( ! empty( $last_spent_order['product_dpad_conditions_values'] ) ) {
									if ( $new_resultprice === $last_spent_order['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_last_spent_order'] = 'yes';
									}
								}
							}
						}
						$last_spent_order_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_last_spent_order', $general_rule_match );
						if ( 'yes' === $last_spent_order_passed ) {
							$is_passed['has_dpad_based_on_last_spent_order'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_last_spent_order'] = 'no';
						}
					}
					
					//Check if is quantity exist
					if ( is_array( $quantity_array ) && isset( $quantity_array ) && ! empty( $quantity_array ) && ! empty( $cart_array ) ) {
						
						$quantity_total 		= 0;
						$product_ids_on_sale 	= wc_get_product_ids_on_sale();
						$is_sub_passed 			= array();

						foreach ( $cart_array as  $woo_cart_item ) {
							$product_type = $woo_cart_item['data']->get_type();
							$product_id = $woo_cart_item['variation_id'] ? $woo_cart_item['variation_id'] : $woo_cart_item['product_id'];
							if( false === strpos( $product_type, 'bundle' ) ) {
								if( "exclude" === $sale_product_check ){
									if( !in_array($product_id, $product_ids_on_sale) ){
										$quantity_total += $woo_cart_item['quantity'];
									}
								} else {
									$quantity_total += $woo_cart_item['quantity'];
								}
							} 
						}
						settype( $quantity_total, 'integer' );

						foreach ( $quantity_array as $key => $quantity ) {
							settype( $quantity['product_dpad_conditions_values'], 'integer' );
							if ( ! empty( $quantity['product_dpad_conditions_values'] ) ) {

								if ( $quantity['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'less_equal_to' ) {
									if ( $quantity['product_dpad_conditions_values'] >= $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'less_then' ) {
									if ( $quantity['product_dpad_conditions_values'] > $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'greater_equal_to' ) {
									if ( $quantity['product_dpad_conditions_values'] <= $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'greater_then' ) {
									if ( $quantity['product_dpad_conditions_values'] < $quantity_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
										break;
									}
								}

								if ( $quantity['product_dpad_conditions_is'] === 'not_in' ) {
									if ( $quantity_total === $quantity['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_quantity'] = 'yes';
									}
								}
							}
						}
						$quantity_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_quantity', $general_rule_match );
						if ( 'yes' === $quantity_passed ) {
							$is_passed['has_dpad_based_on_quantity'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_quantity'] = 'no';
						}
					}

					//Check if is weight exist (Premium)
					if ( is_array( $weight_array ) && isset( $weight_array ) && ! empty( $weight_array ) && ! empty( $cart_array ) ) {
						$weight_total           = 0;
						$product_ids_on_sale 	= wc_get_product_ids_on_sale();
						foreach ( $cart_array as $woo_cart_item ) {
							$product_weight = $woo_cart_item['data']->get_weight();
							$product_type = $woo_cart_item['data']->get_type();
							$product_id = $woo_cart_item['variation_id'] ? $woo_cart_item['variation_id'] : $woo_cart_item['product_id'];
							if ( $product_weight > 0 && false === strpos( $product_type, 'bundle' ) ) {
								$woo_cart_item_quantity = $woo_cart_item['quantity'];
								if( "exclude" === $sale_product_check ){
									if( !in_array($product_id, $product_ids_on_sale) ){
										$weight_total += floatval( $product_weight ) * intval( $woo_cart_item_quantity );
									}
								} else {
									$weight_total += floatval( $product_weight ) * intval( $woo_cart_item_quantity );
								}
							}							
						}
						$is_sub_passed = array();
						settype( $weight_total, 'float' );
						foreach ( $weight_array as $weight ) {
							settype( $weight['product_dpad_conditions_values'], 'float' );
							if ( ! empty( $weight['product_dpad_conditions_values'] ) ) {

								if ( $weight['product_dpad_conditions_is'] === 'is_equal_to' ) {
									if ( $weight_total === $weight['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
										break;
									}
								}

								if ( $weight['product_dpad_conditions_is'] === 'less_equal_to' ) {
									if ( $weight['product_dpad_conditions_values'] >= $weight_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
										break;
									}
								}

								if ( $weight['product_dpad_conditions_is'] === 'less_then' ) {
									if ( $weight['product_dpad_conditions_values'] > $weight_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
										break;
									}
								}
							
								if ( $weight['product_dpad_conditions_is'] === 'greater_equal_to' ) {
									if ( $weight['product_dpad_conditions_values'] <= $weight_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
										break;
									}
								}

								if ( $weight['product_dpad_conditions_is'] === 'greater_then' ) {
									if ( $weight['product_dpad_conditions_values'] < $weight_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
										break;
									}
								}
								
								if ( $weight['product_dpad_conditions_is'] === 'not_in' ) {
									if ( $weight_total === $weight['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_weight'] = 'yes';
									}
								}
							}
						}
						$weight_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_weight', $general_rule_match );
						if ( 'yes' === $weight_passed ) {
							$is_passed['has_dpad_based_on_weight'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_weight'] = 'no';
						}
					}

					//Check if is shipping class exist (Premium)
					if ( is_array( $shipping_class_array ) && isset( $shipping_class_array ) && ! empty( $shipping_class_array ) && ! empty( $cart_array ) ) {
						$_shippingclass         = array();
						$product_ids_on_sale 	= wc_get_product_ids_on_sale();
						$is_sub_passed 			= array();
						foreach ( $woocommerce->cart->get_cart() as $values ) {						
							$product_type = $values['data']->get_type();
							if( false === strpos( $product_type, 'bundle' ) ) {
								$product_id = $values['variation_id'] ? $values['variation_id'] : $values['product_id'];
								$terms = array();
								if( "exclude" === $sale_product_check ){
									if( !in_array($product_id, $product_ids_on_sale) ){
										$terms = get_the_terms( $product_id, 'product_shipping_class' );
									}
								} else {
									$terms = get_the_terms( $product_id, 'product_shipping_class' );
								}
								if ( !empty( $terms ) ) {
									foreach ( $terms as $term ) {
										if ( ! empty( $sitepress ) ) {
											$_shippingclass[] = apply_filters( 'wpml_object_id', $term->term_id, 'product_shipping_class', true, $default_lang );
										} else {
											$_shippingclass[] = $term->term_id;
										}
									}
								}
							}
						}
						$get_shipping_class_all = array_unique( $this->array_flatten( $_shippingclass ) );
						foreach ( $shipping_class_array as $shipping_class ) {
							if ( ! empty( $shipping_class['product_dpad_conditions_values'] ) ) {
								if ( $shipping_class['product_dpad_conditions_is'] === 'is_equal_to' ) {
									foreach ( $shipping_class['product_dpad_conditions_values'] as $shipping_class_id ) {
										settype( $shipping_class_id, 'integer' );
										if ( in_array( $shipping_class_id, dpad_convert_array_to_int($get_shipping_class_all), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'yes';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'no';
										}
									}
								}
								if ( $shipping_class['product_dpad_conditions_is'] === 'not_in' ) {
									foreach ( $shipping_class['product_dpad_conditions_values'] as $shipping_class_id ) {
										settype( $shipping_class_id, 'integer' );
										if ( in_array( $shipping_class_id, dpad_convert_array_to_int($get_shipping_class_all), true ) ) {
											$is_sub_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'no';
											break;
										} else {
											$is_sub_passed[ $key ]['has_dpad_based_on_shipping_class'] = 'yes';
										}
									}
								}
							}
						}
						$shipping_class_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_shipping_class', $general_rule_match );
						if ( 'yes' === $shipping_class_passed ) {
							$is_passed['has_dpad_based_on_shipping_class'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_shipping_class'] = 'no';
						}
					}

					//Check if is payment gateway exist (Premium)
					if ( is_array( $payment_gateway ) && isset( $payment_gateway ) && ! empty( $payment_gateway ) && ! empty( $cart_array ) ) {

						$is_sub_passed = array();
						if( $wc_curr_version >= 3.0 ) {
							$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
						} else {
							$chosen_payment_method  = $woocommerce->session->chosen_payment_method;
						}

						foreach ( $payment_gateway as $key => $payment ) {
							if ( $payment['product_dpad_conditions_is'] === 'is_equal_to' ) {
								if ( in_array( $chosen_payment_method, $payment['product_dpad_conditions_values'], true ) ) {
									$is_sub_passed[ $key ]['has_dpad_based_on_payment'] = 'yes';
								} else {
									$is_sub_passed[ $key ]['has_dpad_based_on_payment'] = 'no';
								}
							}
							if ( $payment['product_dpad_conditions_is'] === 'not_in' ) {
								if ( in_array( $chosen_payment_method, $payment['product_dpad_conditions_values'], true ) ) {
									$is_sub_passed[ $key ]['has_dpad_based_on_payment'] = 'no';
								} else {
									$is_sub_passed[ $key ]['has_dpad_based_on_payment'] = 'yes';
								}
							}
						}
						$payment_gateway_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_payment', $general_rule_match );
						if ( 'yes' === $payment_gateway_passed ) {
							$is_passed['has_dpad_based_on_payment'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_payment'] = 'no';
						}
					}

					//Check if is shipping method exist (Premium)
					if ( is_array( $shipping_methods ) && isset( $shipping_methods ) && ! empty( $shipping_methods ) && ! empty( $cart_array ) ) {
						$is_sub_passed = array();
						if ( $wc_curr_version >= 3.0 ) {
							$chosen_shipping_methods = WC()->session->get( 'chosen_shipping_methods' );
						} else {
							$chosen_shipping_methods = $woocommerce->session->chosen_shipping_methods;
						}
						$chosen_shipping_methods_explode	= explode( ':', $chosen_shipping_methods[0] );

						foreach ( $shipping_methods as $key => $method ) {
							if ( $method['product_dpad_conditions_is'] === 'is_equal_to' ) {
								if ( in_array( $chosen_shipping_methods_explode[0], $method['product_dpad_conditions_values'], true ) ) {
									$is_sub_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'yes';
								} else {
									$is_sub_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'no';
								}
							}
							if ( $method['product_dpad_conditions_is'] === 'not_in' ) {
								if ( in_array( $chosen_shipping_methods_explode[0], $method['product_dpad_conditions_values'], true ) ) {
									$is_sub_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'no';
								} else {
									$is_sub_passed[ $key ]['has_dpad_based_on_shipping_method'] = 'yes';
								}
							}
						}
						$shipping_method_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_shipping_method', $general_rule_match );
						if ( 'yes' === $shipping_method_passed ) {
							$is_passed['has_dpad_based_on_shipping_method'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_shipping_method'] = 'no';
						}
					}
					
					//Check if is shipping total with confition
					if ( is_array( $shipping_total_array ) && isset( $shipping_total_array ) && ! empty( $shipping_total_array ) && ! empty( $cart_array ) ) {
						$is_sub_passed = array();
						$shipping_total = ( $woocommerce->cart->get_shipping_total() > 0 && !empty($woocommerce->cart->get_shipping_total()) ) ? $woocommerce->cart->get_shipping_total() : 0;
						$shipping_taxes = $woocommerce->cart->get_shipping_taxes();
						if( !empty($shipping_taxes) ){
							foreach($shipping_taxes as $shipping_tax){
								$shipping_total += $shipping_tax;
							}
						}
						settype( $shipping_total, 'float' );

						foreach ( $shipping_total_array as $key => $shipping ) {
							settype( $shipping['product_dpad_conditions_values'], 'float' );

							if ( $shipping['product_dpad_conditions_is'] === 'is_equal_to' ) {
								if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
									if ( $shipping_total === $shipping['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
										break;
									}
								}
							}
							if ( $shipping['product_dpad_conditions_is'] === 'less_equal_to' ) {
								if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
									if ( $shipping['product_dpad_conditions_values'] >= $shipping_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
										break;
									}
								}
							}
							if ( $shipping['product_dpad_conditions_is'] === 'less_then' ) {
								if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
									if ( $shipping['product_dpad_conditions_values'] > $shipping_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
										break;
									}
								}
							}
							if ( $shipping['product_dpad_conditions_is'] === 'greater_equal_to' ) {
								if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
									if ( $shipping['product_dpad_conditions_values'] <= $shipping_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
										break;
									}
								}
							}
							if ( $shipping['product_dpad_conditions_is'] === 'greater_then' ) {
								if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
									if ( $shipping['product_dpad_conditions_values'] < $shipping_total ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
										break;
									}
								}
							}
							if ( $shipping['product_dpad_conditions_is'] === 'not_in' ) {
								if ( ! empty( $shipping['product_dpad_conditions_values'] ) && $shipping['product_dpad_conditions_values'] >= 0 ) {
									if ( $shipping_total === $shipping['product_dpad_conditions_values'] ) {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'no';
										break;
									} else {
										$is_sub_passed[ $key ]['has_dpad_based_on_shipping_total'] = 'yes';
									}
								}
							}
						}
						$shipping_total_passed = $this->dpad_check_all_passed_general_rule( $is_sub_passed, 'has_dpad_based_on_shipping_total', $general_rule_match );
						if ( 'yes' === $shipping_total_passed ) {
							$is_passed['has_dpad_based_on_shipping_total'] = 'yes';
						} else {
							$is_passed['has_dpad_based_on_shipping_total'] = 'no';
						}
					}
					
					if ( isset( $is_passed ) && ! empty( $is_passed ) && is_array( $is_passed ) ) {
						$fnispassed = array();
						foreach ( $is_passed as $val ) {
							if ( '' !== $val ) {
								$fnispassed[] = $val;
							}
						}
						if ( 'all' === $general_rule_match ) {
							if ( in_array( 'no', $fnispassed, true ) ) {
								$final_is_passed_general_rule['passed'] = 'no';
							} else {
								$final_is_passed_general_rule['passed'] = 'yes';
							}
						} else {
							if ( in_array( 'yes', $fnispassed, true ) ) {
								$final_is_passed_general_rule['passed'] = 'yes';
							} else {
								$final_is_passed_general_rule['passed'] = 'no';
							}
						}
					}
				}
				if ( wcdrfc_fs()->is__premium_only() ) {
					if ( wcdrfc_fs()->can_use_premium_code() ) {
						/* Start Advance Pricing Rules */
						if ( 'on' === $ap_rule_status ) {
							$cost_on_product_status                         = get_post_meta( $dpad, 'cost_on_product_status', true );
							$cost_on_category_status                        = get_post_meta( $dpad, 'cost_on_category_status', true );
							
							$get_condition_array_ap_product                 = get_post_meta( $dpad, 'sm_metabox_ap_product', true );
							$get_condition_array_ap_category                = get_post_meta( $dpad, 'sm_metabox_ap_category', true );
							
							$match_advance_rule                             = array();
							if ( 'on' === $cost_on_product_status ) {
								$match_advance_rule['hfbopq'] = $this->wcpfc_pro_match_product_per_qty__premium_only( $get_condition_array_ap_product, $cart_array, $sitepress, $default_lang, $cost_on_product_rule_match );
							}							
							if ( 'on' === $cost_on_category_status ) {
								$match_advance_rule['hfbocs'] = $this->wcpfc_pro_match_category_per_qty__premium_only( $get_condition_array_ap_category, $cart_array, $sitepress, $default_lang, $cost_on_category_rule_match );
							}
							
							$advance_pricing_rule_cost = 0;
							if ( isset( $match_advance_rule ) && ! empty( $match_advance_rule ) && is_array( $match_advance_rule ) ) {
								foreach ( $match_advance_rule as $val ) {
									if ( '' !== $val['flag'] && 'yes' === $val['flag'] ) {
										$advance_pricing_rule_cost += $val['total_amount'];
									}
								}
							}
							$advance_pricing_rule_cost = $this->wdpad_pro_price_format( $advance_pricing_rule_cost );
							$dpad_cost                 += $advance_pricing_rule_cost;
						}
					}
				}
				if ( empty( $final_is_passed_general_rule ) || '' === $final_is_passed_general_rule || null === $final_is_passed_general_rule ) {
					$new_is_passed['passed'] = 'no';
				} else if ( ! empty( $final_is_passed_general_rule ) && in_array( 'no', $final_is_passed_general_rule, true ) ) {
					$new_is_passed['passed'] = 'no';
				} else if ( empty( $final_is_passed_general_rule ) && in_array( '', $final_is_passed_general_rule, true ) ) {
					$new_is_passed['passed'] = 'no';
				} else if ( ! empty( $final_is_passed_general_rule ) && in_array( 'yes', $final_is_passed_general_rule, true ) ) {
					$new_is_passed['passed'] = 'yes';
				}
				if ( in_array( 'no', $new_is_passed, true ) ) {
					$final_passed['passed'] = 'no';
				} else {
					$final_passed['passed'] = 'yes';
				}
				
				if ( isset( $final_passed ) && ! empty( $final_passed ) && is_array( $final_passed ) ) {
					if ( ! in_array( 'no', $final_passed, true ) ) {

						$local_nowtimestamp = current_time( 'timestamp' );
						$texable      = ( isset( $getFeetaxable ) && ! empty( $getFeetaxable ) && $getFeetaxable === 'yes' ) ? true : false;
						
						$currentDate  = strtotime( gmdate( 'd-m-Y' ) );
						$feeStartDate = isset( $getFeeStartDate ) && $getFeeStartDate !== '' ? strtotime( $getFeeStartDate ) : '';
						$feeEndDate   = isset( $getFeeEndDate ) && $getFeeEndDate !== '' ? strtotime( $getFeeEndDate ) : '';
						$feeStartTime = isset( $getFeeStartTime ) && $getFeeStartTime !== '' ? strtotime( $getFeeStartTime ) : '';
						$feeEndTime   = isset( $getFeeEndTime ) && $getFeeEndTime !== '' ? strtotime( $getFeeEndTime ) : '';
						if ( ( $currentDate >= $feeStartDate || $feeStartDate === '' ) && ( $currentDate <= $feeEndDate || $feeEndDate === '' ) && ( $local_nowtimestamp >= $feeStartTime || $feeStartTime === '' ) && ( $local_nowtimestamp <= $feeEndTime || $feeEndTime === '' ) ) {
							$woocommerce->cart->add_fee( $title, ( - 1 * $dpad_cost ), $texable, ''); //'Reduced rate',
							$ij++;
						}
					}
				}
			}
		}
	}

	/**
	 * Change shipping label when shipping method is taxable
	 *
	 * @param $label
	 * @param $method
	 * @return $label
	 */
	public function dpad_change_shipping_title( $label, $method){
		$total_tax = 0;
		$current_currency = get_woocommerce_currency_symbol();
		if( !empty($method->get_taxes()) ){
			foreach($method->get_taxes() as $shipping_tax ){
				$total_tax += $shipping_tax;
			}
		}
		if($total_tax > 0){
			$label .= sprintf( wp_kses_post( ' %1$s(Tax: %3$s)%2$s' ), '<strong>', '</strong>', $current_currency.$total_tax );
		}
		return $label;
	}
	
	/**
	 * Check user's have first order or not
	 *
	 * @return boolean $order_check
	 * @since 2.2.0
	 *
	 */
	public function dpad_check_first_order_for_user__premium_only( $user_id ) {

		$user_id = !empty($user_id) ? $user_id : get_current_user_id();

		// Get all customer orders
		$customer_orders = get_posts( array(
			'numberposts' => 1, // one order is enough
			'meta_key'    => '_customer_user',
			'meta_value'  => $user_id,
			'post_type'   => 'shop_order', // WC orders post type
			'post_status' => array( 'wc-completed', 'wc-processing' ), // Only orders with "completed" and "processing" status
			'fields'      => 'ids', // Return Ids "completed"
		) );

		// return "true" when customer has already at least one order (false if not)
	   	return count($customer_orders) > 0 ? false : true; 
	    
	}

	/**
	 * Add discount message on product details page after add to cart button
	 *
	 * @param $label
	 * @param $method
	 * @return $label
	 */
	public function dpad_content_after_addtocart_button(){
		global $product;
		$productid = $product->get_id();
		$dpad_args = array(
			'post_status'   => 'publish',
			'post_type'     => 'wc_dynamic_pricing',
			'post_per_page' => -1,
		);
		$get_all_discounts = new WP_Query( $dpad_args );
		if( $get_all_discounts->have_posts() ) {
			while ( $get_all_discounts->have_posts() ) {
				$get_all_discounts->the_post();
				$discount_id        = get_the_ID();
				$getMsgChecked      = get_post_meta( $discount_id, 'dpad_chk_discount_msg', true );
				$getrulestatus      = get_post_meta( $discount_id, 'dpad_settings_status', true );
				$forSpecificProduct = get_post_meta( $discount_id, 'dpad_chk_discount_msg_selected_product', true );
				if( 'on' === $getrulestatus ){
					if( !empty($getMsgChecked) && "on" === $getMsgChecked ){
						if( !empty( $forSpecificProduct ) && 'on' === $forSpecificProduct ){
							$selectedProductList = get_post_meta( $discount_id, 'pdcv_selected_product_list', true );
							if( in_array( $productid, $selectedProductList) ){
								$getDiscountMsg    = esc_html__( get_post_meta( $discount_id, 'dpad_discount_msg_text', true ), 'woo-conditional-discount-rules-for-checkout' );
								echo sprintf( wp_kses_post( '<div class="dpad_discount_message"><span>%s</span></div>' ), esc_html($getDiscountMsg) );		
							}
						}else{
							$getDiscountMsg    = esc_html__( get_post_meta( $discount_id, 'dpad_discount_msg_text', true ), 'woo-conditional-discount-rules-for-checkout' );
							echo sprintf( wp_kses_post( '<div class="dpad_discount_message"><span>%s</span></div>' ), esc_html($getDiscountMsg) );
						}
					}
				}
			}
		}
		/* Restore original Post Data */
		wp_reset_postdata();
	}

	/**
	 * Remove taxes from cart discount
	 *
	 * @param $package
	 */
	public function conditional_dpad_exclude_cart_fees_taxes($package)
	{
	    return [];
	}

	/**
	 * Find a matching zone for a given package.
	 *
	 * @since  2.6.0
	 * @uses   wc_make_numeric_postcode()
	 * @return WC_Shipping_Zone
	 */
    public function wc_get_shipping_zone()
    {
        global $wpdb, $woocommerce;

        $country = strtoupper(wc_clean($woocommerce->customer->get_shipping_country()));
        $state = strtoupper(wc_clean($woocommerce->customer->get_shipping_state()));
        $continent = strtoupper(wc_clean(WC()->countries->get_continent_code_for_country($country)));
        $postcode = wc_normalize_postcode(wc_clean($woocommerce->customer->get_shipping_postcode()));
        $cache_key = WC_Cache_Helper::get_cache_prefix('shipping_zones') . 'wc_shipping_zone_' . md5(sprintf('%s+%s+%s', $country, $state, $postcode));
        $matching_zone_id = wp_cache_get($cache_key, 'shipping_zones');

        if (false === $matching_zone_id) {


            // Postcode range and wildcard matching
            $postcode_locations=array();
            $zones = WC_Shipping_Zones::get_zones();
            if(!empty($zones)){
                foreach ($zones as  $zone) {
                    if(!empty($zone['zone_locations'])){
                        foreach ($zone['zone_locations'] as $zone_location) {
                            $location=new stdClass();
                            if('postcode' === $zone_location->type){
                                $location->zone_id=$zone['zone_id'];
                                $location->location_code=$zone_location->code;
                                $postcode_locations[]= $location;   
                            }                        
                        }
                    }
                }                    
            }

            if ($postcode_locations) {
                $zone_ids_with_postcode_rules = array_map('absint', wp_list_pluck($postcode_locations, 'zone_id'));
                $matches = wc_postcode_location_matcher($postcode, $postcode_locations, 'zone_id', 'location_code', $country);
                $do_not_match = array_unique(array_diff($zone_ids_with_postcode_rules, array_keys($matches)));

                if (!empty($do_not_match)) {
                    $criteria =$do_not_match;
                }
            }
            // Get matching zones
            if(!empty($criteria)){
                $matching_zone_id = $wpdb->get_var($wpdb->prepare("
                    SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones
                    LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'
                    WHERE ( ( location_type = 'country' AND location_code = %s )
                    OR ( location_type = 'state' AND location_code = %s )
                    OR ( location_type = 'continent' AND location_code = %s )
                    OR ( location_type IS NULL ) )
                    AND zones.zone_id NOT IN (%s)
                    ORDER BY zone_order ASC LIMIT 1
                ",$country,$country . ':' . $state,$continent,implode(',', $do_not_match)));                
            } else {
                $matching_zone_id = $wpdb->get_var($wpdb->prepare("
                    SELECT zones.zone_id FROM {$wpdb->prefix}woocommerce_shipping_zones as zones
                    LEFT OUTER JOIN {$wpdb->prefix}woocommerce_shipping_zone_locations as locations ON zones.zone_id = locations.zone_id AND location_type != 'postcode'
                    WHERE ( ( location_type = 'country' AND location_code = %s )
                    OR ( location_type = 'state' AND location_code = %s )
                    OR ( location_type = 'continent' AND location_code = %s )
                    OR ( location_type IS NULL ) )
                    ORDER BY zone_order ASC LIMIT 1
                   
                ",$country,$country . ':' . $state,$continent));
            }


            wp_cache_set($cache_key, $matching_zone_id, 'shipping_zones');
        }   

        return $matching_zone_id ? $matching_zone_id : 0;
    }


	public function dpad_array_column( array $input, $columnKey, $indexKey = null ) {
		$array = array();
		foreach ( $input as $value ) {
			if ( ! isset( $value[ $columnKey ] ) ) {

				return false;
			}
			if ( is_null( $indexKey ) ) {
				$array[] = $value[ $columnKey ];
			} else {
				if ( ! isset( $value[ $indexKey ] ) ) {
					
					return false;
				}
				if ( ! is_scalar( $value[ $indexKey ] ) ) {
					
					return false;
				}
				$array[ $value[ $indexKey ] ] = $value[ $columnKey ];
			}
		}

		return $array;
	}

	public function array_flatten( $array ) {
		if ( ! is_array( $array ) ) {
			return false;
		}
		$result = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$result = array_merge( $result, $this->array_flatten( $value ) );
			} else {
				$result[ $key ] = $value;
			}
		}

		return $result;
	}

	function dpad_get_woo_version_number() {
		// If get_plugins() isn't available, require it
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Create the plugins folder and file variables
		$plugin_folder = get_plugins( '/' . 'woocommerce' );
		$plugin_file   = 'woocommerce.php';

		// If the plugin version number is set, return it
		if ( isset( $plugin_folder[ $plugin_file ]['Version'] ) ) {
			return $plugin_folder[ $plugin_file ]['Version'];
		} else {
			return null;
		}
	}

	/*
     * Get WooCommerce version number
     */

	public function dpad_remove_currency( $price ) {
        $args  = array(
            'decimal_separator'  => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
        );

        $wc_currency_symbol = get_woocommerce_currency_symbol();
        $cleanText          = strip_tags($price);
		$new_price          = str_replace( $wc_currency_symbol, '', $cleanText );

        $tnew_price         = str_replace( $args['thousand_separator'], '', $new_price);
        $dnew_price         = str_replace( $args['decimal_separator'], '.', $tnew_price);
        $new_price2         = preg_replace( '/[^.\d]/', '', $dnew_price );
        
		return $new_price2;
	}

	/*
     * Enable ajax refresh for email field
     */
	function dpad_trigger_update_checkout_on_change( $fields ) {

		$fields['billing']['billing_email']['class'][] = 'update_totals_on_change';

		return $fields;
	}

	/**
	 * Check order condition for user
	 *
	 * @return boolean $order_check
	 * @since 2.2.0
	 *
	 */
	public function dpad_check_order_for_user__premium_only( $user_id, $count = false ) {

		$user_id = !empty($user_id) ? $user_id : get_current_user_id();

		$numberposts = (!$count) ? 1 : -1;
		// Get all customer orders
		$customer_orders = get_posts( array(
			'numberposts' => $numberposts, // one order is enough
			'meta_key'    => '_customer_user',
			'meta_value'  => $user_id,
			'post_type'   => 'shop_order', // WC orders post type
			'post_status' => array( 'wc-completed', 'wc-processing' ), // Only orders with "completed" and "processing" status
			'fields'      => 'ids', // Return Ids "completed"
		) );
	
		// return "true" when customer has already at least one order (false if not)
		$total = 0;
		if(!$count){
			foreach ( $customer_orders as $customer_order ) {
				$order = wc_get_order( $customer_order );
				$total += $order->get_total();
			}
			return $total; 
		} else {
			return count($customer_orders);
		}
	}

	/**
	 * Count qty for product based and cart based when apply per qty option is on. This rule will apply when advance pricing rule will disable
	 *
	 * @param int    $fees_id
	 * @param array  $cart_array
	 * @param int    $products_based_qty
	 * @param float  $products_based_subtotal
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $products_based_qty, $products_based_subtotal
	 * @since 2.2.0
	 *
	 * @uses  get_post_meta()
	 * @uses  get_post()
	 * @uses  get_terms()
	 *
	 */
	public function dpad_product_qty_on_rules_ps( $fees_id, $cart_array, $products_based_qty, $products_based_subtotal, $sitepress, $default_lang ) {
		$get_condition_array = get_post_meta( $fees_id, 'dynamic_pricing_metabox', true );
		$all_rule_check   = array();
		if ( ! empty( $get_condition_array ) ) {
			foreach ( $get_condition_array as $condition ) {
				if ( array_search( 'product', $condition, true ) ) {
					$site_product_id           = '';
					$cart_final_products_array = array();
					// Product Condition Start
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							// foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
								// settype( $product_id, 'integer' );
								foreach ( $cart_array as $value ) {
									if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
										$product_id_lan = $value['variation_id'];
									} else {
										$product_id_lan = $value['product_id'];
									}
									$_product = wc_get_product( $product_id_lan );
									$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
									if ( ! empty( $sitepress ) ) {
										$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
									} else {
										$site_product_id = $product_id_lan;
									}
									if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
										if ( in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
											$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
											if( array_key_exists($site_product_id, $cart_final_products_array) ){
												$product_data_explode   = explode( "||", $cart_final_products_array[ $site_product_id ] );
												$cart_product_qty   	= json_decode( $product_data_explode[0] );
												$prod_qty 				+= $cart_product_qty;
											}
											$cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
										}
									} else {
										if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
											$prod_qty = 0;
											$cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
										}
									}
								}
							// }
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							// foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
							// 	settype( $product_id, 'integer' );
								foreach ( $cart_array as $value ) {
									if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
										$product_id_lan = $value['variation_id'];
									} else {
										$product_id_lan = $value['product_id'];
									}
									$_product = wc_get_product( $product_id_lan );
									$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
									if ( ! empty( $sitepress ) ) {
										$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
									} else {
										$site_product_id = $product_id_lan;
									}
									if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
										if ( ! in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
											$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
											if( array_key_exists($site_product_id, $cart_final_products_array) ){
												$product_data_explode   = explode( "||", $cart_final_products_array[ $site_product_id ] );
												$cart_product_qty   	= json_decode( $product_data_explode[0] );
												$prod_qty 				+= $cart_product_qty;
											} 
											$cart_final_products_array[ $product_id_lan ] = $prod_qty . "||" . $line_item_subtotal;
										}
									} else {
										if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
											$prod_qty = 0;
											$cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
										}
									}
								}
							// }
						}
					}
					if ( ! empty( $cart_final_products_array ) ) {
						foreach ( $cart_final_products_array as $prd_id => $cart_item ) {
							$cart_item_explode                     = explode( "||", $cart_item );
							$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
							$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
							// $line_item_subtotal                    = (float) $cart_item['line_subtotal'] + (float) $cart_item['line_subtotal_tax'];
							// $all_rule_check[ $prd_id ]['qty']      = $cart_item['quantity'];
							// $all_rule_check[ $prd_id ]['subtotal'] = $line_item_subtotal;
						}
					}
					// Product Condition End
				}
				if ( array_search( 'variableproduct', $condition, true ) ) {
					$site_product_id               = '';
					$cart_final_var_products_array = array();
					// Variable Product Condition Start
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							// foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
							// 	settype( $product_id, 'integer' );
								foreach ( $cart_array as $value ) {
									if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
										$product_id_lan = $value['variation_id'];
									} else {
										$product_id_lan = $value['product_id'];
									}
									$_product = wc_get_product( $product_id_lan );
									$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
									if ( ! empty( $sitepress ) ) {
										$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
									} else {
										$site_product_id = $product_id_lan;
									}
									if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
										if ( in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
											$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
											$cart_final_var_products_array[] = $prod_qty . "||" . $line_item_subtotal;
										}
									} else {
										if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
											$prod_qty = 0;
											$cart_final_var_products_array[] = $prod_qty . "||" . $line_item_subtotal;
										}
									}
								}
							// }
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							// foreach ( $condition['product_dpad_conditions_values'] as $product_id ) {
							// 	settype( $product_id, 'integer' );
								foreach ( $cart_array as $value ) {
									if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
										$product_id_lan = $value['variation_id'];
									} else {
										$product_id_lan = $value['product_id'];
									}
									$_product = wc_get_product( $product_id_lan );
									$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
									if ( ! empty( $sitepress ) ) {
										$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
									} else {
										$site_product_id = $product_id_lan;
									}
									if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
										if ( ! in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
											$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
											$cart_final_var_products_array[] = $prod_qty . "||" . $line_item_subtotal;
										}
									} else {
										if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
											$prod_qty = 0;
											$cart_final_var_products_array[] = $prod_qty . "||" . $line_item_subtotal;
										}
									}
								}
							// }
						}
					}
					if ( ! empty( $cart_final_var_products_array ) ) {
						foreach ( $cart_final_var_products_array as $prd_id => $cart_item ) {
							$cart_item_explode                     = explode( "||", $cart_item );
							$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
							$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
							// $line_item_subtotal                    = (float) $cart_item['line_subtotal'] + (float) $cart_item['line_subtotal_tax'];
							// $all_rule_check[]['qty']      = $cart_item['quantity'];
							// $all_rule_check[]['subtotal'] = $line_item_subtotal;
						}
					}
					// Variable Product Condition End
				}
				// Category Condition Start
				if ( array_search( 'category', $condition, true ) ) {
					$final_cart_products_cats_ids  = array();
					$cart_final_cat_products_array = array();
					$all_cats                      = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $condition['product_dpad_conditions_values'] as $category_id ) {
								settype( $category_id, 'integer' );
								$final_cart_products_cats_ids[] = $category_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							$final_cart_products_cats_ids = array_diff( $all_cats, $condition['product_dpad_conditions_values'] );
						}
					}
					$final_cart_products_cats_ids = array_map( 'intval', $final_cart_products_cats_ids );
					$terms            = array();
					$cart_value_array = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
						$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
						$cart_value_array[] = $value;
						$term_ids           = wp_get_post_terms( $value['product_id'], 'product_cat', array( 'fields' => 'ids' ) );
						foreach ( $term_ids as $term_id ) {
							$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
							if( false !== strpos( $_product->get_type(), 'bundle' ) ){
								$prod_qty = 0;
							}
							$product_id                       = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $term_id, $final_cart_products_cats_ids, true ) ) {
								if( array_key_exists($product_id,$terms) && array_key_exists($term_id,$terms[$product_id]) ){
									$term_data_explode  = explode( "||", $terms[ $product_id ][ $term_id ] );
									$cart_term_qty      = json_decode( $term_data_explode[0] );
									$prod_qty += $cart_term_qty;
								}
								$terms[ $product_id ][ $term_id ] = $prod_qty . "||" . $line_item_subtotal;
							}
						}
					}
					foreach ( $terms as $cart_product_key => $main_term_data ) {
						foreach ( $main_term_data as $cart_term_id => $term_data ) {
							$term_data_explode  = explode( "||", $term_data );
							$cart_term_qty      = json_decode( $term_data_explode[0] );
							$cart_term_subtotal = json_decode( $term_data_explode[1] );
							if ( in_array( $cart_term_id, $final_cart_products_cats_ids, true ) ) {
								$cart_final_cat_products_array[ $cart_product_key ][ $cart_term_id ] = $cart_term_qty . "||" . $cart_term_subtotal;
							}
						}
					}
					if ( ! empty( $cart_final_cat_products_array ) ) {
						foreach ( $cart_final_cat_products_array as $prd_id => $main_cart_item ) {
							foreach ( $main_cart_item as $term_id => $cart_item ) {
								$cart_item_explode                     = explode( "||", $cart_item );
								$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
								$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
							}
						}
					}
				}
				// Category Condition End
				if ( array_search( 'tag', $condition, true ) ) {
					// Tag Condition Start
					$final_cart_products_tag_ids   = array();
					$cart_final_tag_products_array = array();
					$all_tags                      = get_terms(
						array(
							'taxonomy' => 'product_tag',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $condition['product_dpad_conditions_values'] as $tag_id ) {
								$final_cart_products_tag_ids[] = $tag_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							$final_cart_products_tag_ids = array_diff( $all_tags, $condition['product_dpad_conditions_values'] );
						}
					}
					$final_cart_products_tag_ids = array_map( 'intval', $final_cart_products_tag_ids );
					$tags                        = array();
					$cart_value_array            = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
						$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
						$cart_value_array[] = $value;
						$tag_ids            = wp_get_post_terms( $value['product_id'], 'product_tag', array( 'fields' => 'ids' ) );
						foreach ( $tag_ids as $tag_id ) {
							$prod_qty = $value['quantity'] ? $value['quantity'] : 0;
							if( false !== strpos( $_product->get_type(), 'bundle' ) ){
								$prod_qty = 0;
							}
							$product_id                       = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $tag_id, $final_cart_products_tag_ids, true ) ) {
								if( array_key_exists($product_id,$tags) && array_key_exists($tag_id,$tags[$product_id]) ){
									$term_data_explode  = explode( "||", $tags[ $product_id ][ $tag_id ] );
									$cart_term_qty      = json_decode( $term_data_explode[0] );
									$prod_qty += $cart_term_qty;
								}
								$tags[ $product_id ][ $tag_id ] = $prod_qty . "||" . $line_item_subtotal;
							}
						}
					}
					foreach ( $tags as $cart_product_key => $main_tag_data ) {
						foreach ( $main_tag_data as $cart_tag_id => $tag_data ) {
							$tag_data_explode  = explode( "||", $tag_data );
							$cart_tag_qty      = json_decode( $tag_data_explode[0] );
							$cart_tag_subtotal = json_decode( $tag_data_explode[1] );
							if ( ! empty( $final_cart_products_tag_ids ) ) {
								if ( in_array( $cart_tag_id, $final_cart_products_tag_ids, true ) ) {
									$cart_final_tag_products_array[ $cart_product_key ][ $cart_tag_id ] = $cart_tag_qty . "||" . $cart_tag_subtotal;
								}
							}
						}
					}
					if ( ! empty( $cart_final_tag_products_array ) ) {
						foreach ( $cart_final_tag_products_array as $prd_id => $main_cart_item ) {
							foreach ( $main_cart_item as $term_id => $cart_item ) {
								$cart_item_explode                     = explode( "||", $cart_item );
								$all_rule_check[ $prd_id ]['qty']      = $cart_item_explode[0];
								$all_rule_check[ $prd_id ]['subtotal'] = $cart_item_explode[1];
							}
						}
					}
				}
			}
		}
		if ( ! empty( $all_rule_check ) ) {
			foreach ( $all_rule_check as $cart_item ) {
				$products_based_qty      += isset($cart_item['qty'])?$cart_item['qty']:0;
				$products_based_subtotal += isset($cart_item['subtotal'])?$cart_item['subtotal']:0;
			}
		}
		if ( 0 === $products_based_qty ) {
			$products_based_qty = 1;
		}
		return array( $products_based_qty, $products_based_subtotal );
	}

	/**
	 * Count product based and cart based when apply per count option is on. This rule will apply when advance pricing rule will disable
	 *
	 * @param int    $fees_id
	 * @param array  $cart_array
	 * @param int    $products_based_qty
	 * @param float  $products_based_subtotal
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $products_based_qty, $products_based_subtotal
	 * @since 2.2.0
	 *
	 * @uses  get_post_meta()
	 * @uses  get_post()
	 * @uses  get_terms()
	 *
	 */
	public function dpad_product_count_on_rules_ps( $fees_id, $cart_array, $products_based_qty, $products_based_subtotal, $sitepress, $default_lang ) {
		$get_condition_array = get_post_meta( $fees_id, 'dynamic_pricing_metabox', true );
		$all_rule_check   = array();
		$final_count = 0;
		if ( ! empty( $get_condition_array ) ) {
			foreach ( $get_condition_array as $condition ) {
				if ( array_search( 'product', $condition, true ) ) {
					$site_product_id           = '';
					$cart_final_products_array = array();
					// Product Condition Start
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
										$final_count++;
									}
								} 
								// else {
								// 	if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
								// 		$prod_qty = 0;
								// 		$cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
								// 	}
								// }
								$cart_final_products_array[ $site_product_id ] = $final_count;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( ! in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
										$final_count++;
									}
								} 
								// else {
								// 	if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
								// 		$prod_qty = 0;
								// 		$cart_final_products_array[ $site_product_id ] = $prod_qty . "||" . $line_item_subtotal;
								// 	}
								// }
								// $cart_final_products_array[ $site_product_id ] = $final_count;
							}
						}
					}
				}
				if ( array_search( 'variableproduct', $condition, true ) ) {
					$site_product_id               = '';
					$cart_final_var_products_array = array();
					// Variable Product Condition Start
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
										$final_count++;
									}
								} 
								// else {
								// 	if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
								// 		$prod_qty = 0;
								// 		$cart_final_var_products_array[] = $prod_qty . "||" . $line_item_subtotal;
								// 	}
								// }
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $cart_array as $value ) {
								if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
									$product_id_lan = $value['variation_id'];
								} else {
									$product_id_lan = $value['product_id'];
								}
								$_product = wc_get_product( $product_id_lan );
								$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
								if ( ! empty( $sitepress ) ) {
									$site_product_id = apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang );
								} else {
									$site_product_id = $product_id_lan;
								}
								if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
									if ( ! in_array( $site_product_id, $condition['product_dpad_conditions_values'] ) ) {
										$final_count++;
									}
								} 
								// else {
								// 	if ( false !== strpos( $_product->get_type(), 'bundle' ) ){
								// 		$prod_qty = 0;
								// 		$cart_final_var_products_array[] = $prod_qty . "||" . $line_item_subtotal;
								// 	}
								// }
							}
						}
					}
					// Variable Product Condition End
				}
				if ( array_search( 'category', $condition, true ) ) {
					$final_cart_products_cats_ids  = array();
					$cart_final_cat_products_array = array();
					$all_cats                      = get_terms(
						array(
							'taxonomy' => 'product_cat',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $condition['product_dpad_conditions_values'] as $category_id ) {
								settype( $category_id, 'integer' );
								$final_cart_products_cats_ids[] = $category_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							$final_cart_products_cats_ids = array_diff( $all_cats, $condition['product_dpad_conditions_values'] );
						}
					}
					$final_cart_products_cats_ids = array_map( 'intval', $final_cart_products_cats_ids );
					
					$terms            = array();
					$cart_value_array = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
						$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
						$cart_value_array[] = $value;
						$term_ids           = wp_get_post_terms( $value['product_id'], 'product_cat', array( 'fields' => 'ids' ) );
						
						foreach ( $term_ids as $term_id ) {
							// $prod_qty = $value['quantity'] ? $value['quantity'] : 0;
							// if( false !== strpos( $_product->get_type(), 'bundle' ) ){
							// 	$prod_qty = 0;
							// }
							$product_id = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $term_id, $final_cart_products_cats_ids, true ) ) {
								// if( array_key_exists($product_id,$terms) && array_key_exists($term_id,$terms[$product_id]) ){
									$final_count++;
								// }
							}
						}
					}
				}
				if ( array_search( 'tag', $condition, true ) ) {
					// Tag Condition Start
					$final_cart_products_tag_ids   = array();
					$cart_final_tag_products_array = array();
					$all_tags                      = get_terms(
						array(
							'taxonomy' => 'product_tag',
							'fields'   => 'ids',
						)
					);
					if ( 'is_equal_to' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							foreach ( $condition['product_dpad_conditions_values'] as $tag_id ) {
								$final_cart_products_tag_ids[] = $tag_id;
							}
						}
					} elseif ( 'not_in' === $condition['product_dpad_conditions_is'] ) {
						if ( ! empty( $condition['product_dpad_conditions_values'] ) ) {
							$final_cart_products_tag_ids = array_diff( $all_tags, $condition['product_dpad_conditions_values'] );
						}
					}
					$final_cart_products_tag_ids = array_map( 'intval', $final_cart_products_tag_ids );
					$tags                        = array();
					$cart_value_array            = array();
					foreach ( $cart_array as $value ) {
						if ( ! empty( $value['variation_id'] ) && 0 !== $value['variation_id'] ) {
							$product_id = $value['variation_id'];
						} else {
							$product_id = $value['product_id'];
						}
						$_product = wc_get_product( $product_id );
						$line_item_subtotal = (float) $value['line_subtotal'] + (float) $value['line_subtotal_tax'];
						$cart_value_array[] = $value;
						$tag_ids            = wp_get_post_terms( $value['product_id'], 'product_tag', array( 'fields' => 'ids' ) );
						foreach ( $tag_ids as $tag_id ) {
							// $prod_qty = $value['quantity'] ? $value['quantity'] : 0;
							// if( false !== strpos( $_product->get_type(), 'bundle' ) ){
							// 	$prod_qty = 0;
							// }
							$product_id                       = ( $value['variation_id'] ) ? $value['variation_id'] : $product_id;
							if ( in_array( $tag_id, $final_cart_products_tag_ids, true ) ) {
								// if( array_key_exists($product_id,$tags) && array_key_exists($tag_id,$tags[$product_id]) ){
									$final_count++;
								// }
							}
						}
					}
				}
			}
		}
		// return array( $products_based_qty, $products_based_subtotal );
		return $final_count;
	}

	/**
	 * Find unique id based on given array
	 *
	 * @param array  $is_passed
	 * @param string $has_fee_based
	 * @param string $general_rule_match
	 *
	 * @return string $main_is_passed
	 * @since    3.6
	 *
	 */
	public function dpad_check_all_passed_general_rule( $is_passed, $has_fee_based, $general_rule_match ) {
		$main_is_passed = 'no';
		$flag           = array();
		if ( ! empty( $is_passed ) ) {
			foreach ( $is_passed as $key => $is_passed_value ) {
				if ( 'yes' === $is_passed_value[ $has_fee_based ] ) {
					$flag[ $key ] = true;
				} else {
					$flag[ $key ] = false;
				}
			}
			if ( 'any' === $general_rule_match ) {
				if ( in_array( true, $flag, true ) ) {
					$main_is_passed = 'yes';
				} else {
					$main_is_passed = 'no';
				}
			} else {
				if ( in_array( false, $flag, true ) ) {
					$main_is_passed = 'no';
				} else {
					$main_is_passed = 'yes';
				}
			}
		}

		return $main_is_passed;
	}

	/**
	 * Match product per qty rules
	 *
	 * @param array  $get_condition_array_ap_product
	 * @param array  $cart_products_array
	 * @param string $default_lang
	 *
	 * @return array $is_passed_advance_rule
	 * @since    1.3.3
	 *
	 * @uses     wcpfc_count_qty_for_product()
	 *
	 */
	public function wcpfc_pro_match_product_per_qty__premium_only( $get_condition_array_ap_product, $woo_cart_array, $sitepress, $default_lang, $cost_on_product_rule_match ) {
		$per_product_cost = 0;
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_prd = array();
			if ( ! empty( $get_condition_array_ap_product ) || '' !== $get_condition_array_ap_product ) {
				foreach ( $get_condition_array_ap_product as $key => $get_condition ) {
					if ( ! empty( $get_condition['ap_fees_products'] ) || '' !== $get_condition['ap_fees_products'] ) {
						$total_qws                 = $this->wcpfc_get_count_qty__premium_only(
							$get_condition['ap_fees_products'], $woo_cart_array, $sitepress, $default_lang, 'product', 'qty'
						);
						$get_min_max               = $this->wcpfc_check_min_max_qws__premium_only(
							$get_condition['ap_fees_ap_prd_min_qty'], $get_condition['ap_fees_ap_prd_max_qty'], $get_condition['ap_fees_ap_price_product'], 'qty'
						);
						$is_passed_from_here_prd[] = $this->wcpfc_check_passed_rule__premium_only(
							$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_cost_per_prd_qty', 'has_fee_based_on_cost_per_prd_price', $get_condition['ap_fees_ap_price_product'], $total_qws, 'qty'
						);
					}
				}
			}
			
			$main_is_passed = $this->wcpfc_pro_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_prd, 'has_fee_based_on_cost_per_prd_qty', 'has_fee_based_on_cost_per_prd_price', $cost_on_product_rule_match
			);
			
			return $main_is_passed;
		}
	}
	/**
	 * Match category per qty rules
	 *
	 * @param array  $get_condition_array_ap_category
	 * @param array  $cart_products_array
	 * @param string $default_lang
	 *
	 * @return array $is_passed_advance_rule
	 * @uses     wcpfc_pro_count_qty_for_category__premium_only()
	 *
	 * @since    1.3.3
	 *
	 * @uses     WC_Cart::get_cart()
	 * @uses     wp_get_post_terms()
	 * @uses     wcpfc_pro_array_flatten()
	 */
	public function wcpfc_pro_match_category_per_qty__premium_only( $get_condition_array_ap_category, $woo_cart_array, $sitepress, $default_lang, $cost_on_category_rule_match ) {
		if ( ! empty( $woo_cart_array ) ) {
			$is_passed_from_here_cat = array();
			if ( ! empty( $get_condition_array_ap_category ) || '' !== $get_condition_array_ap_category ) {
				foreach ( $get_condition_array_ap_category as $key => $get_condition ) {
					if ( ! empty( $get_condition['ap_fees_categories'] ) || '' !== $get_condition['ap_fees_categories'] ) {
						$total_qws                 = $this->wcpfc_get_count_qty__premium_only(
							$get_condition['ap_fees_categories'], $woo_cart_array, $sitepress, $default_lang, 'category', 'qty'
						);
						$get_min_max               = $this->wcpfc_check_min_max_qws__premium_only(
							$get_condition['ap_fees_ap_cat_min_qty'], $get_condition['ap_fees_ap_cat_max_qty'], $get_condition['ap_fees_ap_price_category'], 'qty'
						);
						$is_passed_from_here_cat[] = $this->wcpfc_check_passed_rule__premium_only(
							$key, $get_min_max['min'], $get_min_max['max'], 'has_fee_based_on_per_category', 'has_fee_based_on_cost_per_cat_price', $get_condition['ap_fees_ap_price_category'], $total_qws, 'qty'
						);
					}
				}
			}
			$main_is_passed = $this->wcpfc_pro_check_all_passed_advance_rule__premium_only(
				$is_passed_from_here_cat, 'has_fee_based_on_per_category', 'has_fee_based_on_cost_per_cat_price', $cost_on_category_rule_match
			);

			return $main_is_passed;
		}
	}
	/**
	 * Count qty for Product, Category and Total Cart
	 *
	 * @param array  $ap_selected_id
	 * @param array  $woo_cart_array
	 * @param string $sitepress
	 * @param string $default_lang
	 * @param string $type
	 * @param string $qws
	 *
	 * @return int $total
	 *
	 * @since 3.6
	 *
	 * @uses  wc_get_product()
	 * @uses  WC_Product::is_type()
	 * @uses  wp_get_post_terms()
	 * @uses  wcpfc_get_prd_category_from_cart__premium_only()
	 *
	 */
	public function wcpfc_get_count_qty__premium_only( $ap_selected_id, $woo_cart_array, $sitepress, $default_lang, $type, $qws ) {
		$total_qws = 0;
		if ( 'shipping_class' !== $type ) {
			$ap_selected_id = array_map( 'intval', $ap_selected_id );
		}
		foreach ( $woo_cart_array as $woo_cart_item ) {
			$main_product_id_lan = $woo_cart_item['product_id'];
			if ( ! empty( $woo_cart_item['variation_id'] ) || 0 !== $woo_cart_item['variation_id'] ) {
				$product_id_lan = $woo_cart_item['variation_id'];
			} else {
				$product_id_lan = $woo_cart_item['product_id'];
			}
			$_product = wc_get_product( $product_id_lan );
			if ( ! empty( $sitepress ) ) {
				$product_id_lan = intval( apply_filters( 'wpml_object_id', $product_id_lan, 'product', true, $default_lang ) );
			} else {
				$product_id_lan = intval( $product_id_lan );
			}
			if ( 'product' === $type ) {
				if ( in_array( $product_id_lan, $ap_selected_id, true ) ) {
					if ( 'qty' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] );
					}
					if ( 'weight' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] ) * floatval( $_product->get_weight() );
					}
					if ( 'subtotal' === $qws ) {
						if ( ! empty( $woo_cart_item['line_tax'] ) ) {
							$woo_cart_item['line_tax'] = $woo_cart_item['line_tax'];
						}
						$total_qws += $this->wcpfc_pro_get_specific_subtotal__premium_only( $woo_cart_item['line_subtotal'], $woo_cart_item['line_tax'] );
					}
				}
			}
			if ( 'category' === $type ) {
				$cat_id_list        = wp_get_post_terms( $main_product_id_lan, 'product_cat', array( 'fields' => 'ids' ) );
				$cat_id_list_origin = $this->wcpfc_get_prd_category_from_cart__premium_only( $cat_id_list, $sitepress, $default_lang );
				if ( ! empty( $cat_id_list_origin ) && is_array( $cat_id_list_origin ) ) {
					foreach ( $ap_selected_id as $ap_fees_categories_key_val ) {
						if ( in_array( $ap_fees_categories_key_val, $cat_id_list_origin, true ) ) {
							if ( 'qty' === $qws ) {
								$total_qws += intval( $woo_cart_item['quantity'] );
							}
							if ( 'weight' === $qws ) {
								$total_qws += intval( $woo_cart_item['quantity'] ) * floatval( $_product->get_weight() );
							}
							if ( 'subtotal' === $qws ) {
								if ( ! empty( $woo_cart_item['line_tax'] ) ) {
									$woo_cart_item['line_tax'] = $woo_cart_item['line_tax'];
								}
								$total_qws += $this->wcpfc_pro_get_specific_subtotal__premium_only( $woo_cart_item['line_subtotal'], $woo_cart_item['line_tax'] );
							}
							break;
						}
					}
				}
			}
			if ( 'shipping_class' === $type ) {
				$prd_shipping_class = $_product->get_shipping_class();
				if ( in_array( $prd_shipping_class, $ap_selected_id, true ) ) {
					if ( 'qty' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] );
					}
					if ( 'weight' === $qws ) {
						$total_qws += intval( $woo_cart_item['quantity'] ) * floatval( $_product->get_weight() );
					}
					if ( 'subtotal' === $qws ) {
						if ( ! empty( $woo_cart_item['line_tax'] ) ) {
							$woo_cart_item['line_tax'] = $woo_cart_item['line_tax'];
						}
						$total_qws += $this->wcpfc_pro_get_specific_subtotal__premium_only( $woo_cart_item['line_subtotal'], $woo_cart_item['line_tax'] );
					}
				}
			}
		}

		return $total_qws;
	}

	/**
	 * Check Min and max qty, weight and subtotal
	 *
	 * @param int|float $min
	 * @param int|float $max
	 * @param float     $price
	 * @param string    $qws
	 *
	 * @return array
	 *
	 * @since 3.4
	 *
	 */
	public function wcpfc_check_min_max_qws__premium_only( $min, $max, $price, $qws ) {
		$min_val = $min;
		if ( '' === $max || '0' === $max ) {
			$max_val = 2000000000;
		} else {
			$max_val = $max;
		}
		$price_val = $price;
		if ( 'qty' === $qws ) {
			settype( $min_val, 'integer' );
			settype( $max_val, 'integer' );
		} else {
			settype( $min_val, 'float' );
			settype( $max_val, 'float' );
		}

		return array(
			'min'   => $min_val,
			'max'   => $max_val,
			'price' => $price_val,
		);
	}
	/**
	 * Cgeck rule passed or not
	 *
	 * @param string    $key
	 * @param string    $min
	 * @param string    $max
	 * @param string    $hbc
	 * @param string    $hbp
	 * @param float     $price
	 * @param int|float $total_qws
	 * @param string    $qws
	 *
	 * @return array
	 * @since    3.6
	 *
	 */
	public function wcpfc_check_passed_rule__premium_only( $key, $min, $max, $hbc, $hbp, $price, $total_qws, $qws ) {
		$is_passed_from_here_prd = array();
		if ( ( $min <= $total_qws ) && ( $total_qws <= $max ) ) {
			$is_passed_from_here_prd[ $hbc ][ $key ] = 'yes';
			$is_passed_from_here_prd[ $hbp ][ $key ] = $price;
		} else {
			$is_passed_from_here_prd[ $hbc ][ $key ] = 'no';
			$is_passed_from_here_prd[ $hbp ][ $key ] = $price;
		}

		return $is_passed_from_here_prd;
	}
	/**
	 * Find unique id based on given array
	 *
	 * @param array  $is_passed
	 * @param string $has_fee_checked
	 * @param string $has_fee_based
	 * @param string $advance_inside_rule_match
	 *
	 * @return array
	 * @since    3.6
	 *
	 */
	public function wcpfc_pro_check_all_passed_advance_rule__premium_only( $is_passed, $has_fee_checked, $has_fee_based, $advance_inside_rule_match ) {
		$get_cart_total = WC()->cart->get_cart_contents_total();
		$main_is_passed = 'no';
		$flag           = array();
		$sum_ammount    = 0;
		if ( ! empty( $is_passed ) ) {
			
			foreach ( $is_passed as $main_is_passed ) {
				foreach ( $main_is_passed[ $has_fee_checked ] as $key => $is_passed_value ) {
					if ( 'yes' === $is_passed_value ) {
						
						foreach ( $main_is_passed[ $has_fee_based ] as $hfb_key => $hfb_is_passed_value ) {
							if ( $hfb_key === $key ) {
								$final_price = $this->wcpfc_check_percantage_price__premium_only( $hfb_is_passed_value, $get_cart_total );
								$sum_ammount += $final_price;
							}
						}
						$flag[ $key ] = true;
					} else {
						$flag[ $key ] = false;
					}
				}
			}
			if ( 'any' === $advance_inside_rule_match ) {
				if ( in_array( true, $flag, true ) ) {
					$main_is_passed = 'yes';
				} else {
					$main_is_passed = 'no';
				}
			} else {
				if ( in_array( false, $flag, true ) ) {
					$main_is_passed = 'no';
				} else {
					$main_is_passed = 'yes';
				}
			}
		}

		return array(
			'flag'         => $main_is_passed,
			'total_amount' => $sum_ammount,
		);
	}
	/**
	 * Add shipping rate
	 *
	 * @param int|float $min
	 * @param int|float $max
	 * @param float     $price
	 * @param int|float $count_total
	 * @param float     $get_cart_total
	 * @param float     $shipping_rate_cost
	 *
	 * @return float $shipping_rate_cost
	 *
	 * @since 3.4
	 *
	 */
	public function wcpfc_check_percantage_price__premium_only( $price, $get_cart_total ) {
		if ( ! empty( $price ) ) {
			$is_percent = substr( $price, - 1 );
			if ( '%' === $is_percent ) {
				$percent = substr( $price, 0, - 1 );
				$percent = number_format( $percent, 2, '.', '' );
				if ( ! empty( $percent ) ) {
					$percent_total = ( $percent / 100 ) * $get_cart_total;
					$price         = $percent_total;
				}
			} else {
				$price = $this->wdpad_pro_price_format( $price );
			}
		}

		return $price;
	}
	/**
	 * Price format
	 *
	 * @param string $price
	 *
	 * @return string $price
	 * @since  1.3.3
	 *
	 */
	public function wdpad_pro_price_format( $price ) {
		$price = floatval( $price );

		return $price;
	}
	/**
	 * Get Product category from cart
	 *
	 * @param array  $cat_id_list
	 * @param string $sitepress
	 * @param string $default_lang
	 *
	 * @return array $cat_id_list_origin
	 *
	 * @since 3.6
	 *
	 */
	public function wcpfc_get_prd_category_from_cart__premium_only( $cat_id_list, $sitepress, $default_lang ) {
		$cat_id_list_origin = array();
		if ( isset( $cat_id_list ) && ! empty( $cat_id_list ) ) {
			foreach ( $cat_id_list as $cat_id ) {
				if ( ! empty( $sitepress ) ) {
					$cat_id_list_origin[] = (int) apply_filters( 'wpml_object_id', $cat_id, 'product_cat', true, $default_lang );
				} else {
					$cat_id_list_origin[] = (int) $cat_id;
				}
			}
		}

		return $cat_id_list_origin;
	}
}

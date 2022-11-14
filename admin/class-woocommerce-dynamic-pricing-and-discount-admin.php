<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @link       http://www.multidots.com
 * @since      1.0.0
 * @package    Woocommerce_Dynamic_Pricing_And_Discount_Pro
 * @subpackage Woocommerce_Dynamic_Pricing_And_Discount_Pro/admin
 * @author     Multidots <inquiry@multidots.in>
 */
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Woocommerce_Dynamic_Pricing_And_Discount_Pro_Admin {
	const wdpad_post_type = 'wc_dynamic_pricing';
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
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 *
	 * @since    1.0.0
	 *
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		$menu_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
		if ( isset( $menu_page ) && ! empty( $menu_page ) && ( $menu_page === 'wcdrfc-rules-list' || $menu_page === 'wcdrfc-rule-add-new' || $menu_page === 'wcdrfc-page-get-started' || $menu_page === 'wcdrfc-page-information' || $menu_page === 'wcdrfc-pro-edit-fee' ) ) {
			wp_enqueue_style( $this->plugin_name . '-jquery-ui-css', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-jquery-timepicker-css', plugin_dir_url( __FILE__ ) . 'css/jquery.timepicker.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . '-webkit-css', plugin_dir_url( __FILE__ ) . 'css/webkit.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name . 'main-style', plugin_dir_url( __FILE__ ) . 'css/style.css', array(), 'all' );
			wp_enqueue_style( $this->plugin_name . 'media-css', plugin_dir_url( __FILE__ ) . 'css/media.css', array(), 'all' );
			wp_enqueue_style( $this->plugin_name . 'select2-min', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), 'all' );
		}
	}
	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		$menu_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		if ( isset( $menu_page ) && ! empty( $menu_page ) && ( $menu_page === 'wcdrfc-rules-list' || $menu_page === 'wcdrfc-rule-add-new' || $menu_page === 'wcdrfc-page-get-started' || $menu_page === 'wcdrfc-page-information' || $menu_page === 'wcdrfc-pro-edit-fee' ) ) {
			wp_enqueue_script( $this->plugin_name . '-tablesorter-js', plugin_dir_url( __FILE__ ) . 'js/jquery.tablesorter.js', array( 'jquery' ), $this->version, false );
			if ( wcdrfc_fs()->is__premium_only() ) {
				if ( wcdrfc_fs()->can_use_premium_code() ) {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-admin__premium_only.js', array(
						'jquery',
						'jquery-ui-dialog',
						'jquery-ui-accordion',
						'jquery-ui-sortable',
					), $this->version, false );
				} else {
					wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-admin.js', array(
						'jquery',
						'jquery-ui-dialog',
						'jquery-ui-accordion',
						'jquery-ui-sortable',
					), $this->version, false );
				}
			} else {
				wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woocommerce-dynamic-pricing-and-discount-admin.js', array(
					'jquery',
					'jquery-ui-dialog',
					'jquery-ui-accordion',
					'jquery-ui-sortable',
				), $this->version, false );
			}
			
			wp_enqueue_script( $this->plugin_name . '-select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array(
				'jquery',
				'jquery-ui-dialog',
				'jquery-ui-accordion',
				'jquery-ui-datepicker',
			) );

			wp_enqueue_script( $this->plugin_name . '-timepicker-js', plugin_dir_url( __FILE__ ) . 'js/jquery.timepicker.js', array( 'jquery' ), $this->version, false );
			
			wp_enqueue_script( 'jquery-tiptip' );

			if ( wcdrfc_fs()->is__premium_only() && wcdrfc_fs()->can_use_premium_code() ) {
				wp_localize_script( $this->plugin_name, 'coditional_vars', array( 
						'ajaxurl'                          	=> admin_url( 'admin-ajax.php' ),
						'plugin_url' 						=> plugin_dir_url( __FILE__ ),
						'delete'                           	=> esc_html__( 'Delete', 'woo-conditional-discount-rules-for-checkout' ),
						'cart_qty'                         	=> esc_html__( 'Cart Qty', 'woo-conditional-discount-rules-for-checkout' ),
						'min_quantity'                     	=> esc_html__( 'Min Quantity', 'woo-conditional-discount-rules-for-checkout' ),
						'max_quantity'                     	=> esc_html__( 'Max Quantity', 'woo-conditional-discount-rules-for-checkout' ),
						'cart_weight'                      	=> esc_html__( 'Cart Weight', 'woo-conditional-discount-rules-for-checkout' ),
						'min_weight'                       	=> esc_html__( 'Min Weight', 'woo-conditional-discount-rules-for-checkout' ),
						'max_weight'                       	=> esc_html__( 'Max Weight', 'woo-conditional-discount-rules-for-checkout' ),
						'cart_subtotal'                    	=> esc_html__( 'Cart Subtotal', 'woo-conditional-discount-rules-for-checkout' ),
						'min_subtotal'                     	=> esc_html__( 'Min Subtotal', 'woo-conditional-discount-rules-for-checkout' ),
						'max_subtotal'                     	=> esc_html__( 'Max Subtotal', 'woo-conditional-discount-rules-for-checkout' ),
						'amount'                           	=> esc_html__( 'Amount', 'woo-conditional-discount-rules-for-checkout' ),
						'product_qty_msg' 					=> esc_html__( 'This rule will only work if you have selected any one Product Specific option.', 'woo-conditional-discount-rules-for-checkout' ),
						'product_count_msg' 				=> esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' ),
						'note'              				=> esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
						'warning_msg6'      				=> esc_html__( 'You need to select product specific option in Discount Rules for product based option', 'woo-conditional-discount-rules-for-checkout' ),
						'error_msg' 	    				=> esc_html__( 'Please add Discount Rules value', 'woo-conditional-discount-rules-for-checkout' ),
						'per_product'						=> esc_html__( 'Apply on Products', 'woo-conditional-discount-rules-for-checkout' ),
					) 
				);
			} else {
				wp_localize_script( $this->plugin_name, 'coditional_vars', array( 
					'ajaxurl'                          	=> admin_url( 'admin-ajax.php' ),
					'plugin_url' 						=> plugin_dir_url( __FILE__ ),
					'product_qty_msg' 					=> esc_html__( 'This rule will only work if you have selected any one Product Specific option.', 'woo-conditional-discount-rules-for-checkout' ),
					'product_count_msg' 				=> esc_html__( 'This rule will work if you have selected any one Product Specific option or it will apply to all products.', 'woo-conditional-discount-rules-for-checkout' ),
					'note'              				=> esc_html__( 'Note: ', 'woo-conditional-discount-rules-for-checkout' ),
					'warning_msg6'      				=> esc_html__( 'You need to select product specific option in Discount Rules for product based option', 'woo-conditional-discount-rules-for-checkout' ),
					'error_msg' 	    				=> esc_html__( 'Please add Discount Rules value', 'woo-conditional-discount-rules-for-checkout' ),
				) 
			);
			}
		}
	}
	/**
	 * Set Active menu
	 */
	public function wdpad_pro_active_menu() {
		$screen = get_current_screen();
		if ( ! empty( $screen ) && ( $screen->id === 'dotstore-plugins_page_wcdrfc-rule-add-new' || $screen->id === 'dotstore-plugins_page_wcdrfc-pro-edit-fee' ||
		                             $screen->id === 'dotstore-plugins_page_wcdrfc-page-get-started' || $screen->id === 'dotstore-plugins_page_wcdrfc-page-information' ) ) {
			?>
			<script type="text/javascript">
              jQuery(document).ready(function ($) {
                $('a[href="admin.php?page=wcdrfc-rules-list"]').parent().addClass('current')
                $('a[href="admin.php?page=wcdrfc-rules-list"]').addClass('current')
              })
			</script>
			<?php
		}
	}
	public function dot_store_menu_conditional_dpad_pro() {
		if ( wcdrfc_fs()->is__premium_only() && wcdrfc_fs()->can_use_premium_code() ) {
			$plugin_name = WOOCOMMERCE_CONDITIONAL_DISCOUNT_FOR_CHECKOUT_PRO_PLUGIN_NAME;
		} else {
			$plugin_name = WOOCOMMERCE_CONDITIONAL_DISCOUNT_FOR_CHECKOUT_FREE_PLUGIN_NAME;
		}
		global $GLOBALS;
		if ( empty( $GLOBALS['admin_page_hooks']['dots_store'] ) ) {
			add_menu_page(
				'DotStore Plugins', __( 'DotStore Plugins', 'woo-conditional-discount-rules-for-checkout' ), 'null', 'dots_store', array(
				$this,
				'dot_store_menu_page',
			), WDPAD_PRO_PLUGIN_URL . 'admin/images/menu-icon.png', 25
			);
		}
		add_submenu_page( 'dots_store', 'Get Started', 'Get Started', 'manage_options', 'wcdrfc-page-get-started', array(
			$this,
			'wdpad_pro_get_started_page',
		) );
		add_submenu_page( 'dots_store', 'Introduction', 'Introduction', 'manage_options', 'wcdrfc-page-information', array(
			$this,
			'wdpad_pro_information_page',
		) );
		$get_hook = add_submenu_page( 'dots_store', $plugin_name, __( $plugin_name, 'woo-conditional-discount-rules-for-checkout' ), 'manage_options', 'wcdrfc-rules-list', array(
			$this,
			'wdpad_pro_dpad_list_page',
		) );
		add_action( "load-$get_hook", array( $this, "dpad_screen_options" ) );
		// add_submenu_page( 'dots_store', 'Add New', 'Add New', 'manage_options', 'wcdrfc-rule-add-new', array(
		// 	$this,
		// 	'wdpad_pro_add_new_dpad_page',
		// ) );
		// add_submenu_page( 'dots_store', 'Edit Discount', 'Edit Discount', 'manage_options', 'wcdrfc-pro-edit-fee', array(
		// 	$this,
		// 	'wdpad_pro_edit_dpad_page',
		// ) );
	}
	public function dot_store_menu_page() {
	}
	public function wdpad_pro_information_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-information-page.php' );
	}
	public function wdpad_pro_dpad_list_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-list-page.php' );
		$dpad_rule_lising_obj = new DPAD_Rule_Listing_Page();
		$dpad_rule_lising_obj->dpad_sj_output();
	}
	// public function wdpad_pro_add_new_dpad_page() {
	// 	require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-add-new-page.php' );
	// }
	// public function wdpad_pro_edit_dpad_page() {
	// 	require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-add-new-page.php' );
	// }
	public function wdpad_pro_get_started_page() {
		require_once( plugin_dir_path( __FILE__ ) . 'partials/wcdrfc-pro-get-started-page.php' );
	}
	/**
	 * Screen option for discount rule list
	 *
	 * @since    1.0.0
	 */
	public function dpad_screen_options() {
		$args = array(
			'label'   => esc_html__( 'List Per Page', 'woo-conditional-discount-rules-for-checkout' ),
			'default' => 1,
			'option'  => 'dpad_per_page',
		);
		add_screen_option( 'per_page', $args );
	}
	function pro_dpad_settings_get_meta( $value ) {
		global $post;
		$field = get_post_meta( $post->ID, $value, true );
		if ( ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}
	/**
	 * Add screen option for per page
	 *
	 * @param bool   $status
	 * @param string $option
	 * @param int    $value
	 *
	 * @return int $value
	 * @since 1.0.0
	 *
	 */
	public function wdpad_set_screen_options( $status, $option, $value ) {
		$dpad_screens = array(
			'dpad_per_page',
		);
		if( 'dpad_per_page' === $option ){
			$value = !empty($value) && $value > 0 ? $value : 1;
		}
		if ( in_array( $option, $dpad_screens, true ) ) {
			return $value;
		}
		return $status;
	}
	function wdpad_pro_dpad_conditions_save( $post ) {
		if ( empty( $post ) ) {
			return false;
		}
		if ( isset( $post['post_type'] ) && $post['post_type'] === self::wdpad_post_type ) {
			if ( $post['dpad_post_id'] === '' ) {
				$dpad_post = array(
					'post_title'  => $post['dpad_settings_product_dpad_title'],
					'post_status' => 'publish',
					'post_type'   => self::wdpad_post_type,
					'post_author' => 1,
				);
				$post_id   = wp_insert_post( $dpad_post );
			} else {
				$dpad_post = array(
					'ID'          => $post['dpad_post_id'],
					'post_title'  => $post['dpad_settings_product_dpad_title'],
					'post_status' => 'publish',
				);
				$post_id   = wp_update_post( $dpad_post );
			}
			if ( isset( $post['dpad_settings_product_cost'] ) ) {
				update_post_meta( $post_id, 'dpad_settings_product_cost', esc_attr( $post['dpad_settings_product_cost'] ) );
			}
			/* Apply per quantity postmeta start */
			if ( isset( $post['dpad_chk_qty_price'] ) ) {
				update_post_meta( $post_id, 'dpad_chk_qty_price', 'on' );
			} else {
				update_post_meta( $post_id, 'dpad_chk_qty_price', 'off' );
			}
			if ( isset( $post['dpad_per_qty'] ) ) {
				update_post_meta( $post_id, 'dpad_per_qty', esc_attr( $post['dpad_per_qty'] ) );
			}
			if ( isset( $post['extra_product_cost'] ) ) {
				update_post_meta( $post_id, 'extra_product_cost', esc_attr( $post['extra_product_cost'] ) );
			}
			/* Apply per quantity postmeta end */
			if ( isset( $post['dpad_settings_select_dpad_type'] ) ) {
				update_post_meta( $post_id, 'dpad_settings_select_dpad_type', esc_attr( $post['dpad_settings_select_dpad_type'] ) );
			}
			if ( isset( $post['dpad_settings_start_date'] ) ) {
				update_post_meta( $post_id, 'dpad_settings_start_date', esc_attr( $post['dpad_settings_start_date'] ) );
			}
			if ( isset( $post['dpad_settings_end_date'] ) ) {
				update_post_meta( $post_id, 'dpad_settings_end_date', esc_attr( $post['dpad_settings_end_date'] ) );
			}
			if ( isset( $post['dpad_time_from'] ) ) {
				update_post_meta( $post_id, 'dpad_time_from', esc_attr( $post['dpad_time_from'] ) );
			}
			if ( isset( $post['dpad_time_to'] ) ) {
				update_post_meta( $post_id, 'dpad_time_to', esc_attr( $post['dpad_time_to'] ) );
			}
			if ( isset( $post['dpad_settings_status'] ) ) {
				update_post_meta( $post_id, 'dpad_settings_status', 'on' );
			} else {
				update_post_meta( $post_id, 'dpad_settings_status', 'off' );
			}
			if ( isset( $post['dpad_settings_select_taxable'] ) ) {
				update_post_meta( $post_id, 'dpad_settings_select_taxable', esc_attr( $post['dpad_settings_select_taxable'] ) );
			}
			if ( isset( $post['dpad_settings_optional_gift'] ) ) {
				update_post_meta( $post_id, 'dpad_settings_optional_gift', esc_attr( $post['dpad_settings_optional_gift'] ) );
			}
			if ( isset( $post['by_default_checkbox_checked'] ) ) {
				update_post_meta( $post_id, 'by_default_checkbox_checked', 'on' );
			} else {
				update_post_meta( $post_id, 'by_default_checkbox_checked', 'off' );
			}
			/* Enable/Disable discount message */
			if ( isset( $post['dpad_chk_discount_msg'] ) ) {
				update_post_meta( $post_id, 'dpad_chk_discount_msg', 'on' );
			} else {
				update_post_meta( $post_id, 'dpad_chk_discount_msg', 'off' );
			}
			if ( isset( $post['dpad_chk_discount_msg_selected_product'] ) ) {
				update_post_meta( $post_id, 'dpad_chk_discount_msg_selected_product', 'on' );
			} else {
				update_post_meta( $post_id, 'dpad_chk_discount_msg_selected_product', 'off' );
			}
			if ( isset( $post['dpad_discount_msg_text'] ) ) {
				update_post_meta( $post_id, 'dpad_discount_msg_text', esc_attr( $post['dpad_discount_msg_text'] ) );
			}
			$pdcv_selected_product_list = isset( $post['pdcv_selected_product_list'] ) ? $post['pdcv_selected_product_list'] : array();
			if ( isset( $pdcv_selected_product_list ) ) {
				update_post_meta( $post_id, 'pdcv_selected_product_list',  $pdcv_selected_product_list );
			}
			$dpad_select_day_of_week = isset( $post['dpad_select_day_of_week'] ) ? array_map( 'sanitize_text_field', $post['dpad_select_day_of_week'] ) : array();
			if ( isset( $dpad_select_day_of_week ) ) {
				update_post_meta( $post_id, 'dpad_select_day_of_week',  $dpad_select_day_of_week );
			}
			
			if ( wcdrfc_fs()->is__premium_only() ) {
				if ( wcdrfc_fs()->can_use_premium_code() ) {
					/* Enable/Disable discount for first order */
					if ( isset( $post['first_order_for_user'] ) ) {
						update_post_meta( $post_id, 'first_order_for_user', 'on' );
					} else {
						update_post_meta( $post_id, 'first_order_for_user', 'off' );
					}
					/* Enable/Disable discount for login user */
					if ( isset( $post['user_login_status'] ) ) {
						update_post_meta( $post_id, 'user_login_status', 'on' );
					} else {
						update_post_meta( $post_id, 'user_login_status', 'off' );
					}
				}
			}
			if ( isset( $post['dpad_sale_product'] ) ) {
				update_post_meta( $post_id, 'dpad_sale_product', esc_attr( $post['dpad_sale_product'] ) );
			}
			$dpadArray         = array();
			$dpad              = isset( $post['dpad'] ) ? $post['dpad'] : array();
			$condition_key     = isset( $post['condition_key'] ) ? $post['condition_key'] : array();
			$dpad_conditions   = $dpad['product_dpad_conditions_condition'];
			$conditions_is     = $dpad['product_dpad_conditions_is'];
			$conditions_values = isset( $dpad['product_dpad_conditions_values'] ) && ! empty( $dpad['product_dpad_conditions_values'] ) ? $dpad['product_dpad_conditions_values'] : array();
			$size              = count( $dpad_conditions );
			foreach ( array_keys( $condition_key ) as $key ) {
				if ( ! array_key_exists( $key, $conditions_values ) ) {
					$conditions_values[ $key ] = array();
				}
			}
			uksort( $conditions_values, 'strnatcmp' );
			$conditionsValuesArray = [];
			foreach ( $conditions_values as $v ) {
				$conditionsValuesArray[] = $v;
			}
			$dpadArray = [];
			for ( $i = 0; $i < $size; $i ++ ) {
				$dpadArray[] = array(
					'product_dpad_conditions_condition' => $dpad_conditions[ $i ],
					'product_dpad_conditions_is'        => $conditions_is[ $i ],
					'product_dpad_conditions_values'    => $conditionsValuesArray[ $i ],
				);
			}
			update_post_meta( $post_id, 'dynamic_pricing_metabox', $dpadArray );
			if ( is_network_admin() ) {
				$admin_url = admin_url();
			} else {
				$admin_url = network_admin_url();
			}
			$admin_urls = $admin_url . 'admin.php?page=wcdrfc-rules-list';
			wp_safe_redirect( $admin_urls );
			exit();
		}
	}
	/**
	 * Product spesifict starts
	 */
	function wdpad_pro_product_dpad_conditions_get_meta( $value ) {
		global $post;
		$field = get_post_meta( $post->ID, $value, true );
		if ( isset( $field ) && ! empty( $field ) ) {
			return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		} else {
			return false;
		}
	}
	public function wdpad_pro_product_dpad_conditions_values_ajax() {
		$condition = filter_input( INPUT_POST, 'condition', FILTER_SANITIZE_STRING );
		$count     = filter_input( INPUT_POST, 'count', FILTER_SANITIZE_STRING );
		$condition = isset( $condition ) ? $condition : '';
		$count     = isset( $count ) ? $count : '';
		$html      = '';
		if ( wcdrfc_fs()->is__premium_only() ) {
			if ( wcdrfc_fs()->can_use_premium_code() ) {
				if ( $condition === 'country' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_country_list( $count, [], true ) );
				} elseif ( $condition === 'city' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'state' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_states_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'postcode' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'zone' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_zones_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'product' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_product_list( $count, [], '', true ) );
				} elseif ( $condition === 'variableproduct' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_varible_product_list__premium_only( $count, [], '', true ) );
				} elseif ( $condition === 'category' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_category_list( $count, [], true ) );
				} elseif ( $condition === 'tag' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_tag_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'product_qty' ) {
					$html .= 'input';
				} elseif ( $condition === 'product_count' ) {
					$html .= 'input';
				} elseif ( $condition === 'user' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_user_list( $count, [], true ) );
				} elseif ( $condition === 'user_role' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_user_role_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'user_mail' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'cart_total' ) {
					$html .= 'input';
				} elseif ( $condition === 'cart_totalafter' ) {
					$html .= 'input';
				} elseif ( $condition === 'quantity' ) {
					$html .= 'input';
				} elseif ( $condition === 'total_spent_order' ) {
					$html .= 'input';
				} elseif ( $condition === 'spent_order_count' ) {
					$html .= 'input';
				} elseif ( $condition === 'last_spent_order' ) {
					$html .= 'input';
				} elseif ( $condition === 'weight' ) {
					$html .= 'input';
				} elseif ( $condition === 'coupon' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_coupon_list__premium_only( $count, [], true ) );
				} elseif ( $condition === 'shipping_class' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_advance_flat_rate_class__premium_only( $count, [], true ) );
				} elseif ( $condition === 'payment' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_payment_methods__premium_only( $count, [], true ) );
				} elseif ( $condition === 'shipping_method' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_active_shipping_methods__premium_only( $count, [], true ) );
				} elseif ( $condition === 'shipping_total' ) {
					$html .= 'input';
				}
			} else {
				if ( $condition === 'country' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_country_list( $count, [], true ) );
				} elseif ( $condition === 'city' ) {
					$html .= 'textarea';
				} elseif ( $condition === 'product' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_product_list( $count, [], '', true ) );
				} elseif ( $condition === 'category' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_category_list( $count, [], true ) );
				} elseif ( $condition === 'user' ) {
					$html .= wp_json_encode( $this->wdpad_pro_get_user_list( $count, [], true ) );
				} elseif ( $condition === 'cart_total' ) {
					$html .= 'input';
				} elseif ( $condition === 'quantity' ) {
					$html .= 'input';
				} elseif ( $condition === 'product_count' ) {
					$html .= 'input';
				}
			}
		} else {
			if ( $condition === 'country' ) {
				$html .= wp_json_encode( $this->wdpad_pro_get_country_list( $count, [], true ) );
			} elseif ( $condition === 'city' ) {
				$html .= 'textarea';
			} elseif ( $condition === 'product' ) {
				$html .= wp_json_encode( $this->wdpad_pro_get_product_list( $count, [], '', true ) );
			} elseif ( $condition === 'category' ) {
				$html .= wp_json_encode( $this->wdpad_pro_get_category_list( $count, [], true ) );
			} elseif ( $condition === 'user' ) {
				$html .= wp_json_encode( $this->wdpad_pro_get_user_list( $count, [], true ) );
			} elseif ( $condition === 'cart_total' ) {
				$html .= 'input';
			} elseif ( $condition === 'quantity' ) {
				$html .= 'input';
			} elseif ( $condition === 'product_count' ) {
				$html .= 'input';
			}
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die(); // this is required to terminate immediately and return a proper response
	}
	/**
	 * Function for select country list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_pro_get_country_list( $count = '', $selected = array(), $json = false ) {
		$countries_obj = new WC_Countries();
		$getCountries  = $countries_obj->__get( 'countries' );
		if ( $json ) {
			return $this->convert_array_to_json( $getCountries );
		}
		$html = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2 product_fees_conditions_values_country" multiple="multiple">';
		if ( ! empty( $getCountries ) ) {
			foreach ( $getCountries as $code => $country ) {
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $code, $selected, true ) ? 'selected=selected' : '';
				$html        .= '<option value="' . $code . '" ' . $selectedVal . '>' . $country . '</option>';
			}
		}
		$html .= '</select>';
		return $html;
	}
	public function convert_array_to_json( $arr ) {
		$filter_data = [];
		foreach ( $arr as $key => $value ) {
			$option                        = [];
			$option['name']                = $value;
			$option['attributes']['value'] = $key;
			$filter_data[]                 = $option;
		}
		return $filter_data;
	}
	/**
	 * Get product list in advance pricing rules section
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return mixed $html
	 * @since 1.0.0
	 *
	 */
	public function wdpad_get_product_options( $count = '', $selected = array() ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		
		$all_selected_product_ids = array();
		if ( ! empty( $selected ) && is_array( $selected ) ) {
			foreach ( $selected as $product_id ) {
				$_product = wc_get_product( $product_id );

				if ( 'product_variation' === $_product->post_type ) {
					$all_selected_product_ids[] = $_product->get_parent_id(); //parent_id;
				} else {
					$all_selected_product_ids[] = $product_id;
				}
			}
		}
		$all_selected_product_count = 900;
		$get_all_products = new WP_Query( array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'posts_per_page' => $all_selected_product_count,
			'post__in'       => $all_selected_product_ids,
		) );
		
		$baselang_variation_product_ids = array();
		$defaultlang_simple_product_ids = array();
		$html                           = '';
		
		
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_variation_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_variation_product_id = $value['variation_id'];
						}
						$baselang_variation_product_ids[] = $defaultlang_variation_product_id;
					}
				}
				if ( $_product->is_type( 'simple' ) ) {
					if ( ! empty( $sitepress ) ) {
						$defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
					} else {
						$defaultlang_simple_product_id = $get_all_product->ID;
					}
					$defaultlang_simple_product_ids[] = $defaultlang_simple_product_id;
				}
			}
		}
		$baselang_product_ids = array_merge( $baselang_variation_product_ids, $defaultlang_simple_product_ids );
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $baselang_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( '' !== $selectedVal ) {
					$html .= '<option value="' . $baselang_product_id . '" ' . $selectedVal . '>' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				}
			}
		}
		return $html;
	}
	/**
	 * Get category list in advance pricing rules section
	 *
	 * @param array $selected
	 *
	 * @return mixed $html
	 * @since 1.0.0
	 *
	 */
	public function wdpad_get_category_options__premium_only( $selected = array(), $json = false ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$filter_category_list = [];
		$args                 = array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'name',
			'hierarchical' => 1,
			'hide_empty'   => 1,
		);
		$get_all_categories   = get_terms( 'product_cat', $args );
		$html                 = '';
		if ( isset( $get_all_categories ) && ! empty( $get_all_categories ) ) {
			foreach ( $get_all_categories as $get_all_category ) {
				if ( $get_all_category ) {
					if ( ! empty( $sitepress ) ) {
						$new_cat_id = apply_filters( 'wpml_object_id', $get_all_category->term_id, 'product_cat', true, $default_lang );
					} else {
						$new_cat_id = $get_all_category->term_id;
					}
					$category        = get_term_by( 'id', $new_cat_id, 'product_cat' );
					$parent_category = get_term_by( 'id', $category->parent, 'product_cat' );
					if ( ! empty( $selected ) ) {
						$selected    = array_map( 'intval', $selected );
						$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_cat_id, $selected, true ) ? 'selected=selected' : '';
						if ( $category->parent > 0 ) {
							$html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . '' . $parent_category->name . '->' . $category->name . '</option>';
						} else {
							$html .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . $category->name . '</option>';
						}
					} else {
						if ( $category->parent > 0 ) {
							$filter_category_list[ $category->term_id ] = $parent_category->name . '->' . $category->name;
						} else {
							$filter_category_list[ $category->term_id ] = $category->name;
						}
					}
				}
			}
		}
		if ( true === $json ) {
			return wp_json_encode( $this->convert_array_to_json( $filter_category_list ) );
		} else {
			return $html;
		}
	}
	/**
	 * Get the states for a country.
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string of states
	 */
	public function wdpad_pro_get_states_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$countries     = WC()->countries->get_allowed_countries();
		$filter_states = [];
		$html          = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2 product_fees_conditions_values_state" multiple="multiple">';
		foreach ( $countries as $key => $val ) {
			$states = WC()->countries->get_states( $key );
			if ( ! empty( $states ) ) {
				foreach ( $states as $state_key => $state_value ) {
					$selectedVal                              = is_array( $selected ) && ! empty( $selected ) && in_array( esc_attr( $key . ':' . $state_key ), $selected, true ) ? 'selected=selected' : '';
					$html                                     .= '<option value="' . esc_attr( $key . ':' . $state_key ) . '" ' . $selectedVal . '>' . esc_html( $val . ' -> ' . $state_value ) . '</option>';
					$filter_states[ $key . ':' . $state_key ] = $val . ' -> ' . $state_value;
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_states );
		}
		return $html;
	}
	public function wdpad_pro_get_zones_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_zone = [];
		$raw_zones   = WC_Shipping_Zones::get_zones();
		$html        = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $raw_zones ) && ! empty( $raw_zones ) ) {
			foreach ( $raw_zones as $zone ) {
				$selected                        = array_map( 'intval', $selected );
				$zone['zone_id']                 = (int) $zone['zone_id'];
				$selectedVal                     = is_array( $selected ) && ! empty( $selected ) && in_array( $zone['zone_id'], $selected, true ) ? 'selected=selected' : '';
				$html                            .= '<option value="' . $zone['zone_id'] . '" ' . $selectedVal . '>' . $zone['zone_name'] . '</option>';
				$filter_zone[ $zone['zone_id'] ] = $zone['zone_name'];
			}
		}
		if ( $json ) {
			return $this->convert_array_to_json( $filter_zone );
		}
		$html .= '</select>';
		return $html;
	}
	/**
	 * Function for select product list for selected product
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_pro_get_selected_product_list( $count = '', $selected = array(), $action = '', $json = false ) {
		
		if( empty($selected) ){ $selected = array(); }
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$post_in = '';
		if ( 'edit' === $action ) {
			$post_in        = $selected;
			$posts_per_page = - 1;
		} else {
			$post_in        = '';
			$posts_per_page = - 1;
		}
		$product_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post__in'       => $post_in,
			'posts_per_page' => $posts_per_page,
		);
		$get_all_products = new WP_Query( $product_args );
		$html             = '<select id="product-filter-' . $count . '" rel-id="' . $count . '" name="pdcv_selected_product_list[]" class="product_filter_select2 multiselect2" multiple="multiple">';
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$new_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$new_product_id = $get_all_product->ID;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( $selectedVal !== '' ) {
					$html .= '<option value="' . $new_product_id . '" ' . $selectedVal . '>' . '#' . $new_product_id . ' - ' . get_the_title( $new_product_id ) . '</option>';
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return [];
		}
		return $html;
	}
	/**
	 * Function for select product list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_pro_get_product_list( $count = '', $selected = array(), $action = '', $json = false ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$post_in = '';
		if ( 'edit' === $action ) {
			$post_in        = $selected;
			$posts_per_page = - 1;
		} else {
			$post_in        = '';
			$posts_per_page = - 1;
		}
		$product_args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post__in'       => $post_in,
			'posts_per_page' => $posts_per_page,
		);
		$get_all_products = new WP_Query( $product_args );
		$html             = '<select id="product-filter-' . $count . '" rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_filter_select2 product_discount_select product_dpad_conditions_values multiselect2" multiple="multiple">';
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$new_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$new_product_id = $get_all_product->ID;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( $selectedVal !== '' ) {
					$html .= '<option value="' . $new_product_id . '" ' . $selectedVal . '>' . '#' . $new_product_id . ' - ' . get_the_title( $new_product_id ) . '</option>';
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return [];
		}
		return $html;
	}
	/**
	 * Function for select product list
	 *
	 */
	public function wdpad_pro_get_varible_product_list__premium_only( $count = '', $selected = array(), $action = '', $json = false ) {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$post_in = '';
		if ( 'edit' === $action ) {
			$post_in        = $selected;
			$posts_per_page = - 1;
		} else {
			$post_in        = '';
			$posts_per_page = 10;
		}
		$product_args = array(
			'post_type'      => 'product_variation',
			'post_status'    => 'publish',
			'orderby'        => 'ID',
			'order'          => 'ASC',
			'post__in'       => $post_in,
			'posts_per_page' => $posts_per_page,
		);
		$get_all_products = new WP_Query( $product_args );
		$html = '<select id="var-product-filter-' . $count . '" rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_var_filter_select2 product_discount_select product_fees_conditions_values multiselect2" multiple="multiple">';
		if ( isset( $get_all_products->posts ) && ! empty( $get_all_products->posts ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$new_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$new_product_id = $get_all_product->ID;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_product_id, $selected, true ) ? 'selected=selected' : '';
				if ( $selectedVal !== '' ) {
					$html .= '<option value="' . $new_product_id . '" ' . $selectedVal . '>' . '#' . $new_product_id . ' - ' . get_the_title( $new_product_id ) . '</option>';
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return [];
		}
		return $html;
	}
	/**
	 * Function for select cat list
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_pro_get_category_list( $count = '', $selected = array(), $json = false ) {
		$filter_categories = [];
		global $sitepress;
		$taxonomy     = 'product_cat';
		$post_status  = 'publish';
		$orderby      = 'name';
		$hierarchical = 1;
		$empty        = 0;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$args               = array(
			'post_type'      => 'product',
			'post_status'    => $post_status,
			'taxonomy'       => $taxonomy,
			'orderby'        => $orderby,
			'hierarchical'   => $hierarchical,
			'hide_empty'     => $empty,
			'posts_per_page' => - 1,
		);
		$get_all_categories = get_categories( $args );
		$html               = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_categories ) && ! empty( $get_all_categories ) ) {
			foreach ( $get_all_categories as $get_all_category ) {
				if ( ! empty( $sitepress ) ) {
					$new_cat_id = apply_filters( 'wpml_object_id', $get_all_category->term_id, 'product_cat', true, $default_lang );
				} else {
					$new_cat_id = $get_all_category->term_id;
				}
				$selected        = array_map( 'intval', $selected );
				$selectedVal     = is_array( $selected ) && ! empty( $selected ) && in_array( $new_cat_id, $selected, true ) ? 'selected=selected' : '';
				$category        = get_term_by( 'id', $new_cat_id, 'product_cat' );
				$parent_category = get_term_by( 'id', $category->parent, 'product_cat' );
				if ( $category->parent > 0 ) {
					$html                                    .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . '#' . $parent_category->name . '->' . $category->name . '</option>';
					$filter_categories[ $category->term_id ] = '#' . $parent_category->name . '->' . $category->name;
				} else {
					$html                                    .= '<option value=' . $category->term_id . ' ' . $selectedVal . '>' . $category->name . '</option>';
					$filter_categories[ $category->term_id ] = $category->name;
				}
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_categories );
		}
		return $html;
	}
	/**
	 * Function for select tag list
	 *
	 */
	public function wdpad_pro_get_tag_list__premium_only( $count = '', $selected = array(), $json = false ) {
		global $sitepress;
		$filter_tags  = [];
		$taxonomy     = 'product_tag';
		$orderby      = 'name';
		$hierarchical = 1;
		$empty        = 0;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		$args = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'taxonomy'       => $taxonomy,
			'orderby'        => $orderby,
			'hierarchical'   => $hierarchical,
			'hide_empty'     => $empty,
			'posts_per_page' => - 1,
		);
		$get_all_tags = get_categories( $args );
		$html = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_tags ) && ! empty( $get_all_tags ) ) {
			foreach ( $get_all_tags as $get_all_tag ) {
				if ( ! empty( $sitepress ) ) {
					$new_tag_id = apply_filters( 'wpml_object_id', $get_all_tag->term_id, 'product_tag', true, $default_lang );
				} else {
					$new_tag_id = $get_all_tag->term_id;
				}
				$selected    = array_map( 'intval', $selected );
				$selectedVal = is_array( $selected ) && ! empty( $selected ) && in_array( $new_tag_id, $selected, true ) ? 'selected=selected' : '';
				$tag         = get_term_by( 'id', $new_tag_id, 'product_tag' );
				$html                         .= '<option value="' . $tag->term_id . '" ' . $selectedVal . '>' . $tag->name . '</option>';
				$filter_tags[ $tag->term_id ] = $tag->name;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_tags );
		}
		return $html;
	}
	/**
	 * Function for select user list
	 *
	 */
	public function wdpad_pro_get_user_list( $count = '', $selected = array(), $json = false ) {
		$filter_users  = [];
		$get_all_users = get_users();
		$html          = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_users ) && ! empty( $get_all_users ) ) {
			foreach ( $get_all_users as $get_all_user ) {
				$selected                                = array_map( 'intval', $selected );
				$selectedVal                             = is_array( $selected ) && ! empty( $selected ) && in_array( (int) $get_all_user->data->ID, $selected, true ) ? 'selected=selected' : '';
				$html                                    .= '<option value="' . $get_all_user->data->ID . '" ' . $selectedVal . '>' . $get_all_user->data->user_login . '</option>';
				$filter_users[ $get_all_user->data->ID ] = $get_all_user->data->user_login;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_users );
		}
		return $html;
	}
	/**
	 * Get User role list
	 *
	 * @return unknown
	 */
	public function wdpad_pro_get_user_role_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_user_roles = [];
		global $wp_roles;
		$html = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $wp_roles->roles ) && ! empty( $wp_roles->roles ) ) {
			$defaultSel                 = ! empty( $selected ) && in_array( 'guest', $selected, true ) ? 'selected=selected' : '';
			$html                       .= '<option value="guest" ' . $defaultSel . '>Guest</option>';
			$filter_user_roles["guest"] = 'Guest';
			foreach ( $wp_roles->roles as $user_role_key => $get_all_role ) {
				$selectedVal                         = is_array( $selected ) && ! empty( $selected ) && in_array( $user_role_key, $selected, true ) ? 'selected=selected' : '';
				$html                                .= '<option value="' . $user_role_key . '" ' . $selectedVal . '>' . $get_all_role['name'] . '</option>';
				$filter_user_roles[ $user_role_key ] = $get_all_role['name'];
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_user_roles );
		}
		return $html;
	}
	/**
	 * Function for get Coupon list
	 *
	 */
	public function wdpad_pro_get_coupon_list__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_coupon_list = [];
		$get_all_coupon     = new WP_Query( array(
			'post_type'      => 'shop_coupon',
			'post_status'    => 'publish',
			'posts_per_page' => - 1,
		) );
		$html               = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( isset( $get_all_coupon->posts ) && ! empty( $get_all_coupon->posts ) ) {
			foreach ( $get_all_coupon->posts as $get_all_coupon ) {
				$selected                                  = array_map( 'intval', $selected );
				$selectedVal                               = is_array( $selected ) && ! empty( $selected ) && in_array( $get_all_coupon->ID, $selected, true ) ? 'selected=selected' : '';
				$html                                      .= '<option value="' . $get_all_coupon->ID . '" ' . $selectedVal . '>' . $get_all_coupon->post_title . '</option>';
				$filter_coupon_list[ $get_all_coupon->ID ] = $get_all_coupon->post_title;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_coupon_list );
		}
		return $html;
	}
	/**
	 * get all shipping class name
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_pro_get_advance_flat_rate_class__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_rate_class = [];
		$shipping_classes  = WC()->shipping->get_shipping_classes();
		$html              = '<select rel-id="' . $count . '" name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		$html              .= '<option value="">Select Class</option>';
		if ( isset( $shipping_classes ) && ! empty( $shipping_classes ) ) {
			foreach ( $shipping_classes as $shipping_classes_key ) {
				$shipping_classes_old                                = get_term_by( 'slug', $shipping_classes_key->slug, 'product_shipping_class' );
				$selected                                            = array_map( 'intval', $selected );
				$selectedVal                                         = ! empty( $selected ) && in_array( $shipping_classes_old->term_id, $selected, true ) ? 'selected=selected' : '';
				$html                                                .= '<option value="' . $shipping_classes_old->term_id . '" ' . $selectedVal . '>' . $shipping_classes_key->name . '</option>';
				$filter_rate_class[ $shipping_classes_old->term_id ] = $shipping_classes_key->name;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_rate_class );
		}
		return $html;
	}
	/**
	 * Function for select payment gateways
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_pro_get_payment_methods__premium_only( $count = '', $selected = array(), $json = false ) {
		$filter_payment_methods     = [];
		$available_payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
		$html                       = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( ! empty( $available_payment_gateways ) ) {
			foreach ( $available_payment_gateways as $available_gateways_key => $available_gateways_val ) {
				$selectedVal                                           = is_array( $selected ) && ! empty( $selected ) && in_array( $available_gateways_key, $selected, true ) ? 'selected=selected' : '';
				$html                                                  .= '<option value="' . $available_gateways_val->id . '" ' . $selectedVal . '>' . $available_gateways_val->title . '</option>';
				$filter_payment_methods[ $available_gateways_val->id ] = $available_gateways_val->title;
			}
		}
		$html .= '</select>';
		if ( $json ) {
			return $this->convert_array_to_json( $filter_payment_methods );
		}
		return $html;
	}
	/**
	 * Function for select shipping methods
	 *
	 * @param string $count
	 * @param array  $selected
	 *
	 * @return string
	 */
	public function wdpad_pro_get_active_shipping_methods__premium_only( $count = '', $selected = array(), $json = false ) {
		$shipping_methods = [];
		$active_methods   = array();
		$shipping_methods = WC()->shipping->get_shipping_methods();
		foreach ( $shipping_methods as $id => $shipping_method ) {
			if ( isset( $shipping_method->enabled ) && 'yes' === $shipping_method->enabled ) {
				$method_args           = array(
					'id'           => $shipping_method->id,
					'method_title' => $shipping_method->method_title,
					'title'        => $shipping_method->title,
					'tax_status'   => $shipping_method->tax_status,
				);
				$active_methods[ $id ] = $method_args;
			}
		}
		$html = '<select name="dpad[product_dpad_conditions_values][value_' . $count . '][]" class="product_fees_conditions_values product_discount_select product_discount_select multiselect2" multiple="multiple">';
		if ( ! empty( $active_methods ) ) {
			foreach ( $active_methods as $method_key => $method_val ) {
				$selectedVal                           = is_array( $selected ) && ! empty( $selected ) && in_array( $method_key, $selected, true ) ? 'selected=selected' : '';
				$html                                  .= '<option value="' . $method_val['id'] . '" ' . $selectedVal . '>' . $method_val['method_title'] . '</option>';
				$shipping_methods[ $method_val['id'] ] = $method_val['method_title'];
			}
		}
		if ( $json ) {
			return $this->convert_array_to_json( $shipping_methods );
		}
		$html .= '</select>';
		return $html;
	}
	public function wdpad_pro_wc_multiple_delete_conditional_fee() {
		$allVals = filter_input( INPUT_POST, 'allVals', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$result  = 0;
		if ( ! empty( $allVals ) ) {
			foreach ( $allVals as $val ) {
				wp_delete_post( $val );
				$result = 1;
			}
		}
		echo esc_html( $result );
		wp_die();
	}
	public function multiple_disable_conditional_fee() {
		$allVals                                 = filter_input( INPUT_POST, 'allVals', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$multiple_disable_enable_conditional_fee = filter_input( INPUT_POST, 'do_action', FILTER_SANITIZE_STRING );
		if ( ! empty( $allVals ) && isset( $multiple_disable_enable_conditional_fee ) ) {
			foreach ( $allVals as $val ) {
				if ( $multiple_disable_enable_conditional_fee === 'disable-conditional-fee' ) {
					update_post_meta( $val, 'dpad_settings_status', 'off' );
				} else if ( $multiple_disable_enable_conditional_fee === 'enable-conditional-fee' ) {
					update_post_meta( $val, 'dpad_settings_status', 'on' );
				}
				$result = 1;
			}
		}
		echo esc_html( $result );
		wp_die();
	}
	public function wdpad_pro_wc_multiple_delete_conditional_discount() {
		$allVals = filter_input( INPUT_POST, 'allVals', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );
		$result  = 0;
		if ( ! empty( $allVals ) ) {
			foreach ( $allVals as $val ) {
				update_post_meta( $val, '' );
				$result = 1;
			}
		}
		echo esc_html( $result );
		wp_die();
	}
	public function wdpad_pro_welcome_conditional_dpad_screen_do_activation_redirect() {
		$this->wdpad_pro_register_post_type();
		// if no activation redirect
		if ( ! get_transient( '_welcome_screen_wdpad_pro_mode_activation_redirect_data' ) ) {
			return;
		}
		// Delete the redirect transient
		delete_transient( '_welcome_screen_wdpad_pro_mode_activation_redirect_data' );
		// if activating from network, or bulk
		$activate_multi = filter_input( INPUT_GET, 'activate-multi', FILTER_SANITIZE_STRING );
		if ( is_network_admin() || isset( $activate_multi ) ) {
			return;
		}
		// Redirect to extra cost welcome  page
		wp_safe_redirect( add_query_arg( array( 'page' => 'wcdrfc-page-get-started' ), admin_url( 'admin.php' ) ) );
		exit;
	}
	/**
	 * Register post type
	 *
	 * @since    2.3.0
	 */
	public function wdpad_pro_register_post_type() {
		register_post_type( self::wdpad_post_type, array(
			'labels'          => array(
				'name'          => __( 'Conditional Discount Rule', 'woo-conditional-discount-rules-for-checkout' ),
				'singular_name' => __( 'Conditional Discount Rule', 'woo-conditional-discount-rules-for-checkout' ),
			),
			'rewrite'         => false,
			'query_var'       => false,
			'public'          => false,
			'capability_type' => 'page',
		) );
	}
	public function wdpad_pro_remove_admin_submenus() {
		remove_submenu_page( 'dots_store', 'wcdrfc-page-information' );
		remove_submenu_page( 'dots_store', 'wcdrfc-rule-add-new' );
		remove_submenu_page( 'dots_store', 'wcdrfc-pro-edit-fee' );
		remove_submenu_page( 'dots_store', 'wcdrfc-page-get-started' );
	}
	/**
	 * When create fees based on advance pricing rule and add rule based onm product qty then all
	 * product will display using ajax
	 *
	 * @since 1.0.0
	 *
	 */
	public function wdpad_pro_simple_and_variation_product_list_ajax() {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang 				= $sitepress->get_default_language();
		}
		$json                           = true;
		$filter_product_list            = [];
		$request_value                  = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_STRING );
		$posts_per_page                 = filter_input( INPUT_GET, 'posts_per_page', FILTER_VALIDATE_INT );
		$offset                         = filter_input( INPUT_GET, 'offset', FILTER_VALIDATE_INT );
		$post_value                     = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';
		$posts_per_page                 = isset( $posts_per_page ) ? sanitize_text_field( $posts_per_page ) : '';
		$offset                         = isset( $offset ) ? sanitize_text_field( $offset ) : '';
		$baselang_simple_product_ids    = array();
		$baselang_variation_product_ids = array();
		function wcpfc_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( ! empty( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		$product_args = array(
			'post_type'        => 'product',
			'posts_per_page'   => - 1,
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
		);
		add_filter( 'posts_where', 'wcpfc_posts_where', 10, 2 );
		$get_wp_query = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'wcpfc_posts_where', 10, 2 );
		$get_all_products = $get_wp_query->posts;
		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_variation_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_variation_product_id = $value['variation_id'];
						}
						$baselang_variation_product_ids[] = $defaultlang_variation_product_id;
					}
				}
				if ( $_product->is_type( 'simple' ) ) {
					if ( ! empty( $sitepress ) ) {
						$defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
					} else {
						$defaultlang_simple_product_id = $get_all_product->ID;
					}
					$baselang_simple_product_ids[] = $defaultlang_simple_product_id;
				}
			}
		}
		$baselang_product_ids = array_merge( $baselang_variation_product_ids, $baselang_simple_product_ids );
		$html                 = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html                  .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				$filter_product_list[] = array( $baselang_product_id, get_the_title( $baselang_product_id ) );
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_product_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );;
		wp_die();
	}
	public function wdpad_pro_product_dpad_conditions_values_product_ajax() {
		$json = true;
		global $sitepress;
		$filter_product_list = [];
		$request_value       = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_STRING );
		$posts_per_page      = 10;
		$offset              = - 1;
		$post_value          = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';
		$posts_per_page      = isset( $posts_per_page ) ? sanitize_text_field( $posts_per_page ) : '';
		$offset              = isset( $offset ) ? sanitize_text_field( $offset ) : '';
		$baselang_product_ids = array();
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		function wcpfc_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( isset( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		$product_args = array(
			'post_type'      => 'product',
			'posts_per_page' => - 1,
			'offset'         => $offset,
			's'              => $post_value,
			'post_status'    => 'publish',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'show_posts'     => - 1,
		);
		add_filter( 'posts_where', 'wcpfc_posts_where', 10, 2 );
		$wp_query = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'wcpfc_posts_where', 10, 2 );
		$get_all_products = $wp_query->posts;
		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				if ( ! empty( $sitepress ) ) {
					$defaultlang_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
				} else {
					$defaultlang_product_id = $get_all_product->ID;
				}
				$baselang_product_ids[] = $defaultlang_product_id;
			}
		}
		$html = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$_product = wc_get_product( $baselang_product_id );
				$html     .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				if ( $_product->get_type() === 'simple' ) {
					if ( $_product->get_type() === 'variable' ) {
						$vari = "(All variation)";
					} else {
						$vari = "";
					}
					$filter_product = array();
					$filter_product['id']             = $baselang_product_id;
					$filter_product['text']           = '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . $vari;
					$filter_product_list['results'][] = $filter_product;
				}
			}
		}
		if ( $json ) {
			$filter_product_list['pagination']  = "more";
			$filter_product_list['placeholder'] = "Please enter 3 characters";
			echo wp_json_encode( $filter_product_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die();
	}
	public function wdpad_pro_product_dpad_conditions_varible_values_product_ajax() {
		$json = true;
		global $sitepress;
		$post_value     = filter_input( INPUT_GET, 'search', FILTER_SANITIZE_STRING );
		$posts_per_page = 10;
		$offset         = - 1;
		$post_value     = isset( $post_value ) ? $post_value : '';
		$posts_per_page = isset( $posts_per_page ) ? $posts_per_page : '';
		$offset         = isset( $offset ) ? $offset : '';
		$baselang_product_ids = array();
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		}
		function wcpfc_posts_wheres( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( isset( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		$product_args = array(
			'post_type'        => 'product',
			'posts_per_page'   => - 1,
			'offset'           => $offset,
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
		);
		add_filter( 'posts_where', 'wcpfc_posts_wheres', 10, 2 );
		$get_all_products = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'wcpfc_posts_wheres', 10, 2 );
		if ( ! empty( $get_all_products ) ) {
			foreach ( $get_all_products->posts as $get_all_product ) {
				$_product = wc_get_product( $get_all_product->ID );
				if ( $_product->is_type( 'variable' ) ) {
					$variations = $_product->get_available_variations();
					foreach ( $variations as $value ) {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
						} else {
							$defaultlang_product_id = $value['variation_id'];
						}
						$baselang_product_ids[] = $defaultlang_product_id;
					}
				}
			}
		}
		$html                         = '';
		$filter_variable_product_list = [];
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html .= '<option value="' . $baselang_product_id . '">' . '#' . $baselang_product_id . ' - ' . get_the_title( $baselang_product_id ) . '</option>';
				$filter_variable_product = array();
				$filter_variable_product['id']             = $baselang_product_id;
				$filter_variable_product['text']           = '#' . $baselang_product_id . ' - ' . str_replace( '&#8211;', '-', get_the_title( $baselang_product_id ) );
				$filter_variable_product_list['results'][] = $filter_variable_product;
			}
		}
		if ( $json ) {
			$filter_variable_product_list['pagination']  = "more";
			$filter_variable_product_list['placeholder'] = "Please enter 3 characters";
			echo wp_json_encode( $filter_variable_product_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die();
	}
	function wdpad_pro_admin_footer_review() {
		if ( wcdrfc_fs()->is__premium_only() && wcdrfc_fs()->can_use_premium_code() ) {
			echo sprintf( wp_kses( __( 'If you like <strong>Conditional Discount Rules For WooCommerce Checkout Pro</strong> plugin, please leave us ★★★★★ ratings on <a href="%1$s" target="_blank">DotStore</a>.', 'woo-conditional-discount-rules-for-checkout' ), array(
				'strong' => array(),
				'a'      => array(
					'href'   => array(),
					'target' => 'blank',
				),
			) ), esc_url( 'https://www.thedotstore.com/woocommerce-conditional-discount-rules-for-checkout#tab-reviews' ) );
		} else {
			echo sprintf( wp_kses( __( 'If you like <strong>Conditional Discount Rules For WooCommerce Checkout</strong> plugin, please leave us ★★★★★ ratings on <a href="%1$s" target="_blank">DotStore</a>.', 'woo-conditional-discount-rules-for-checkout' ), array(
				'strong' => array(),
				'a'      => array(
					'href'   => array(),
					'target' => 'blank',
				),
			) ), esc_url( 'https://wordpress.org/support/plugin/woo-conditional-discount-rules-for-checkout/reviews/#new-post' ) );
		}
	}
	function conditional_discount_sorting() {
		
		check_ajax_referer( 'sorting_conditional_fee_action', 'sorting_conditional_fee' );

		global $sitepress, $wpdb;

		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		} else {
			$get_site_language = get_bloginfo( 'language' );
			if ( false !== strpos( $get_site_language, '-' ) ) {
				$get_site_language_explode = explode( '-', $get_site_language );
				$default_lang              = $get_site_language_explode[0];
			} else {
				$default_lang = $get_site_language;
			}
		}
		$post_type 			= self::wdpad_post_type;
		$getPaged      		= filter_input( INPUT_POST, 'paged', FILTER_SANITIZE_NUMBER_INT);
		$getListingArray	= filter_input( INPUT_POST, 'listing', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
		
		$paged     			= !empty( $getPaged ) ? $getPaged : 1;
		$listinbgArray     	= !empty( $getListingArray ) ? array_map( 'intval', wp_unslash( $getListingArray ) ) : array();

		// $query_args = array(
		// 	'post_type'      => $post_type,
		// 	'post_status'    => array( 'publish', 'draft' ),
		// 	'posts_per_page' => -1,
		// 	'orderby'        => array('menu_order', 'post_date'),
		// 	'order'          => 'DESC',
		// 	'fields' 		 => 'ids'
		// );
		// $post_list = new WP_Query( $query_args );
		// $results = $post_list->posts; 
		$results        =   $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID 
				FROM {$wpdb->posts} 
				WHERE post_type = %s AND post_status IN ('publish', 'draft') 
				ORDER BY menu_order, post_date 
				DESC", 
				$post_type
			)
		);

		//Create the list of ID's
		$objects_ids = array();            
		foreach($results as $result) {
			$objects_ids[] = (int)$result->ID; 
		}
		//Here we switch order
		$per_page = get_user_option( 'dpad_per_page' );
		$per_page = ( !empty( $per_page ) || $per_page > 1 ) ? $per_page : 1;
		$edit_start_at = $paged * $per_page - $per_page;
		$index = 0;
		for( $i = $edit_start_at; $i < ($edit_start_at + $per_page); $i++ ) {

			if( !isset($objects_ids[$i]) )
				break;
				
			$objects_ids[$i] = (int)$listinbgArray[$index];
			$index++;
		}
		//Update the menu_order within database
		foreach( $objects_ids as $menu_order => $id ) {
			$data = array( 'menu_order' => $menu_order );
			$wpdb->update( $wpdb->posts, $data, array('ID' => $id) );
			clean_post_cache( $id );
		}		
		wp_send_json_success( array('message' => esc_html__( 'Discount rule has been updated.', 'woo-conditional-discount-rules-for-checkout' ) ) );
	}
	public function dpad_updated_message( $message, $validation_msg ){
		if ( empty( $message ) ) {
			return false;
		}

		if ( 'created' === $message ) {
			$updated_message = esc_html__( "Discount rule has been created.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'saved' === $message ) {
			$updated_message = esc_html__( "Discount rule has been updated.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'deleted' === $message ) {
			$updated_message = esc_html__( "Discount rule has been deleted.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'duplicated' === $message ) {
			$updated_message = esc_html__( "Discount rule has been duplicated.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'disabled' === $message ) {
			$updated_message = esc_html__( "Discount rule has been disabled.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'enabled' === $message ) {
			$updated_message = esc_html__( "Discount rule has been enabled.", 'woo-conditional-discount-rules-for-checkout' );
		}
		if ( 'failed' === $message ) {
			$failed_messsage = esc_html__( "There was an error with saving data.", 'woo-conditional-discount-rules-for-checkout' );
		} elseif ( 'nonce_check' === $message ) {
			$failed_messsage = esc_html__( "There was an error with security check.", 'woo-conditional-discount-rules-for-checkout' );
		}
		if ( 'validated' === $message ) {
			$validated_messsage = esc_html( $validation_msg );
		}
		
		if ( ! empty( $updated_message ) ) {
			echo sprintf( '<div id="message" class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $updated_message ) );
			return false;
		}
		if ( ! empty( $failed_messsage ) ) {
			echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $failed_messsage ) );
			return false;
		}
		if ( ! empty( $validated_messsage ) ) {
			echo sprintf( '<div id="message" class="notice notice-error is-dismissible"><p>%s</p></div>', esc_html( $validated_messsage ) );
			return false;
		}
	}

	/**
	 * Display simple and variable product list based product specific option in Advance Pricing Rules
	 *
	 * @return string $html
	 * @uses   afrsm_pro_get_default_langugae_with_sitpress()
	 * @uses   wc_get_product()
	 * @uses   WC_Product::is_type()
	 * @uses   get_available_variations()
	 * @uses   Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro::afrsm_pro_allowed_html_tags()
	 *
	 * @since  3.4
	 *
	 */
	public function dpad_simple_and_variation_product_list_ajax__premium_only() {
		global $sitepress;
		if ( ! empty( $sitepress ) ) {
			$default_lang = $sitepress->get_default_language();
		} else {
			$get_site_language = get_bloginfo( 'language' );
			if ( false !== strpos( $get_site_language, '-' ) ) {
				$get_site_language_explode = explode( '-', $get_site_language );
				$default_lang              = $get_site_language_explode[0];
			} else {
				$default_lang = $get_site_language;
			}
		}
		$json                           = true;
		$filter_product_list            = [];
		$request_value                  = filter_input( INPUT_GET, 'value', FILTER_SANITIZE_STRING );
		$post_value                     = isset( $request_value ) ? sanitize_text_field( $request_value ) : '';
		$baselang_simple_product_ids    = array();
		$baselang_variation_product_ids = array();
		function dpad_posts_where( $where, $wp_query ) {
			global $wpdb;
			$search_term = $wp_query->get( 'search_pro_title' );
			if ( ! empty( $search_term ) ) {
				$search_term_like = $wpdb->esc_like( $search_term );
				$where            .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql( $search_term_like ) . '%\'';
			}
			return $where;
		}
		$simple_and_variation_product_count = 900;
		$product_args = array(
			'post_type'        => 'product',
			'posts_per_page'   => $simple_and_variation_product_count,
			'search_pro_title' => $post_value,
			'post_status'      => 'publish',
			'orderby'          => 'title',
			'order'            => 'ASC',
		);
		add_filter( 'posts_where', 'dpad_posts_where', 10, 2 );
		$get_wp_query = new WP_Query( $product_args );
		remove_filter( 'posts_where', 'dpad_posts_where', 10, 2 );
		$get_all_products = $get_wp_query->posts;
		if ( isset( $get_all_products ) && ! empty( $get_all_products ) ) {
			foreach ( $get_all_products as $get_all_product ) {
				$_product      = wc_get_product( $get_all_product->ID );
				if ( ! ( $_product->is_virtual( 'yes' ) ) && false === strpos( $_product->get_type(), 'bundle' ) ) {
					if ( $_product->is_type( 'variable' ) ) {
						$variations = $_product->get_available_variations();
						foreach ( $variations as $value ) {
							if ( ! empty( $sitepress ) ) {
								$defaultlang_variation_product_id = apply_filters( 'wpml_object_id', $value['variation_id'], 'product', true, $default_lang );
							} else {
								$defaultlang_variation_product_id = $value['variation_id'];
							}
							$baselang_variation_product_ids[] = $defaultlang_variation_product_id;
						}
					} else {
						if ( ! empty( $sitepress ) ) {
							$defaultlang_simple_product_id = apply_filters( 'wpml_object_id', $get_all_product->ID, 'product', true, $default_lang );
						} else {
							$defaultlang_simple_product_id = $get_all_product->ID;
						}
						$baselang_simple_product_ids[] = $defaultlang_simple_product_id;
					}
				}
			}
		}
		$baselang_product_ids = array_merge( $baselang_variation_product_ids, $baselang_simple_product_ids );
		$html                 = '';
		if ( isset( $baselang_product_ids ) && ! empty( $baselang_product_ids ) ) {
			foreach ( $baselang_product_ids as $baselang_product_id ) {
				$html                  .= '<option value="' . esc_attr( $baselang_product_id ) . '">' . '#' . esc_html( $baselang_product_id ) . ' - ' . esc_html( get_the_title( $baselang_product_id ) ) . '</option>';
				$filter_product_list[] = array( $baselang_product_id, get_the_title( $baselang_product_id ) );
			}
		}
		if ( $json ) {
			echo wp_json_encode( $filter_product_list );
			wp_die();
		}
		echo wp_kses( $html, allowed_html_tags() );
		wp_die();
	}
}
<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * WC_Discount_Rules_Table class.
 *
 * @extends WP_List_Table
 */
if ( ! class_exists( 'WC_Discount_Rules_Table' ) ) {

	class WC_Discount_Rules_Table extends WP_List_Table {

		const post_type = 'wc_dynamic_pricing';
		private static $wc_dpad_found_items = 0;
		private static $admin_object = null;

		/**
		 * get_columns function.
		 *
		 * @return  array
		 * @since 1.0.0
		 *
		 */
		public function get_columns() {
			return array(
				'cb'                => '<input type="checkbox" />',
				'title'             => esc_html__( 'Title', 'woo-conditional-discount-rules-for-checkout' ),
				'amount'   			=> esc_html__( 'Amount', 'woo-conditional-discount-rules-for-checkout' ),
				'status'            => esc_html__( 'Status', 'woo-conditional-discount-rules-for-checkout' ),
				'date'              => esc_html__( 'Date', 'woo-conditional-discount-rules-for-checkout' ),
			);
		}

		/**
		 * get_sortable_columns function.
		 *
		 * @return array
		 * @since 1.0.0
		 *
		 */
		protected function get_sortable_columns() {
			$columns = array(
				'title'  	=> array( 'title', true ),
				'date'		=> array( 'date', false ),
				'amount'	=> array( 'amount', false ),
			);

			return $columns;
		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct( array(
				'singular' => 'post',
				'plural'   => 'posts',
				'ajax'     => false
			) );
			self::$admin_object = new Woocommerce_Dynamic_Pricing_And_Discount_Pro_Admin( '', '' );
		}

		/**
		 * Get Methods to display
		 *
		 * @since 1.0.0
		 */
		public function prepare_items() {
			$this->prepare_column_headers();
			$per_page = $this->get_items_per_page( 'dpad_per_page' );

			$get_search  = filter_input( INPUT_POST, 's', FILTER_SANITIZE_STRING );
			$get_orderby = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_STRING );
			$get_order   = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_STRING );

			$args = array(
				'posts_per_page' => $per_page,
				'order' 		 => 'ASC',
				'orderby' 		 => 'menu_order',
				'offset'         => ( $this->get_pagenum() - 1 ) * $per_page,
			);

			if ( isset( $get_search ) && ! empty( $get_search ) ) {
				$args['s'] = trim( wp_unslash( $get_search ) );
			}

			if ( isset( $get_orderby ) && ! empty( $get_orderby ) ) {
				if ( 'title' === $get_orderby ) {
					$args['orderby'] = 'title';
				} elseif ( 'amount' === $get_orderby ) {
					$args['meta_key'] = 'dpad_settings_product_cost';// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					$args['orderby']  = 'meta_value_num';
				} elseif ( 'date' === $get_orderby ) {
					$args['orderby'] = 'date';
				}
			}

			if ( isset( $get_order ) && ! empty( $get_order ) ) {
				if ( 'asc' === strtolower( $get_order ) ) {
					$args['order'] = 'ASC';
				} elseif ( 'desc' === strtolower( $get_order ) ) {
					$args['order'] = 'DESC';
				}
			}

			$this->items = $this->dpad_find( $args, $get_orderby);

			$total_items = $this->dpad_count();

			$total_pages = ceil( $total_items / $per_page );

			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'total_pages' => $total_pages,
				'per_page'    => $per_page,
			) );
		}

		/**
		 */
		public function no_items() {
			if ( isset( $this->error ) ) {
				echo esc_html($this->error->get_error_message());
			} else {
				esc_html_e( 'No rule found.', 'woo-conditional-discount-rules-for-checkout' );
			}
		}

		/**
		 * Checkbox column
		 *
		 * @param string
		 *
		 * @return mixed
		 * @since 1.0.0
		 *
		 */
		public function column_cb( $item ) {
			if ( ! $item->ID ) {
				return;
			}

			return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s" />', 'method_id_cb', esc_attr( $item->ID ) );
		}

		/**
		 * Output the shipping name column.
		 *
		 * @param object $item
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 */
		public function column_title( $item ) {
			$edit_method_url = add_query_arg( array(
				'page'   => 'wcdrfc-rules-list',
				'action' => 'edit',
				'post'   => $item->ID
			), admin_url( 'admin.php' ) );
			$editurl         = $edit_method_url;

			$method_name = '<strong>
                            <a href="' . wp_nonce_url( $editurl, 'edit_' . $item->ID, 'cust_nonce' ) . '" class="row-title">' . esc_html( $item->post_title ) . '</a>
                        </strong>';

			echo wp_kses( $method_name, allowed_html_tags() );
		}

		/**
		 * Generates and displays row action links.
		 *
		 * @param object $item Link being acted upon.
		 * @param string $column_name Current column name.
		 * @param string $primary Primary column name.
		 *
		 * @return string Row action output for links.
		 * @since 1.0.0
		 *
		 */
		protected function handle_row_actions( $item, $column_name, $primary ) {
			if ( $primary !== $column_name ) {
				return '';
			}

			$edit_method_url = add_query_arg( array(
				'page'   => 'wcdrfc-rules-list',
				'action' => 'edit',
				'post'   => $item->ID
			), admin_url( 'admin.php' ) );
			$editurl         = $edit_method_url;

			$delete_method_url = add_query_arg( array(
				'page'   => 'wcdrfc-rules-list',
				'action' => 'delete',
				'post'   => $item->ID
			), admin_url( 'admin.php' ) );
			$delurl            = $delete_method_url;

			$duplicate_method_url = add_query_arg( array(
				'page'   => 'wcdrfc-rules-list',
				'action' => 'duplicate',
				'post'   => $item->ID
			), admin_url( 'admin.php' ) );
			$duplicateurl         = $duplicate_method_url;

			$actions              = array();
			$actions['edit']      = '<a href="' . wp_nonce_url( $editurl, 'edit_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Edit', 'woo-conditional-discount-rules-for-checkout' ) . '</a>';
			$actions['delete']    = '<a href="' . wp_nonce_url( $delurl, 'del_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Delete', 'woo-conditional-discount-rules-for-checkout' ) . '</a>';
			$actions['duplicate'] = '<a href="' . wp_nonce_url( $duplicateurl, 'duplicate_' . $item->ID, 'cust_nonce' ) . '">' . __( 'Duplicate', 'woo-conditional-discount-rules-for-checkout' ) . '</a>';

			return $this->row_actions( $actions );
		}

		/**
		 * Output the method amount column.
		 *
		 * @param object $item
		 *
		 * @return int|float
		 * @since 1.0.0
		 *
		 */
		public function column_amount( $item ) {
			if ( 0 === $item->ID ) {
				return esc_html__( 'null', 'woo-conditional-discount-rules-for-checkout' );
			}
            $amount  = get_post_meta( $item->ID, 'dpad_settings_product_cost', true );
            if( !is_null($amount) && $amount >= 0 ) {
				$amount_type  = get_post_meta( $item->ID, 'dpad_settings_select_dpad_type', true );
				if( 'fixed' === $amount_type ) {
					return esc_html( get_woocommerce_currency_symbol() ) . ' ' . $amount;
				} else {
					return $amount . ' %';
				}
			} else {
				return esc_html__( 'N/As', 'woo-conditional-discount-rules-for-checkout' );
			}
		}

		/**
		 * Output the method enabled column.
		 *
		 * @param object $item
		 *
		 * @return string
		 */
		public function column_status( $item ) {
			if ( 0 === $item->ID ) {
				return esc_html__( 'Everywhere', 'woo-conditional-discount-rules-for-checkout' );
			}
			$item_status = get_post_meta( $item->ID, 'dpad_settings_status', true );
			if ( 'on' === $item_status ) {
				$status = 'Enable';
			} else {
				$status = 'Disable';
			}

			return $status;
		}

		/**
		 * Output the method amount column.
		 *
		 * @param object $item
		 *
		 * @return mixed $item->post_date;
		 * @since 1.0.0
		 *
		 */
		public function column_date( $item ) {
			if ( 0 === $item->ID ) {
				return esc_html__( 'Everywhere', 'woo-conditional-discount-rules-for-checkout' );
			}

			return $item->post_date;
		}

		/**
		 * Display bulk action in filter
		 *
		 * @return array $actions
		 * @since 1.0.0
		 *
		 */
		public function get_bulk_actions() {
			$actions = array(
				'disable' => esc_html__( 'Disable', 'woo-conditional-discount-rules-for-checkout' ),
				'enable'  => esc_html__( 'Enable', 'woo-conditional-discount-rules-for-checkout' ),
				'delete'  => esc_html__( 'Delete', 'woo-conditional-discount-rules-for-checkout' )
			);

			return $actions;
		}

		/**
		 * Process bulk actions
		 *
		 * @since 1.0.0
		 */
		public function process_bulk_action() {
			$delete_nonce     = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			$get_method_id_cb = filter_input( INPUT_POST, 'method_id_cb', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY );
			$method_id_cb     = ! empty( $get_method_id_cb ) ? array_map( 'sanitize_text_field', wp_unslash( $get_method_id_cb ) ) : array();

			$action = $this->current_action();

			if ( ! isset( $method_id_cb ) ) {
				return;
			}

			$deletenonce = wp_verify_nonce( $delete_nonce, 'bulk-shippingmethods' );

			if ( ! isset( $deletenonce ) && 1 !== $deletenonce ) {
				return;
			}

			$items = array_filter( array_map( 'absint', $method_id_cb ) );

			if ( ! $items ) {
				return;
			}

			if ( 'delete' === $action ) {
				foreach ( $items as $id ) {
					wp_delete_post( $id );
				}
				self::$admin_object->dpad_updated_message( 'deleted', '' );
			} elseif ( 'enable' === $action ) {

				foreach ( $items as $id ) {
					update_post_meta( $id, 'dpad_settings_status', 'on' );
				}
				self::$admin_object->dpad_updated_message( 'enabled', '' );
			} elseif ( 'disable' === $action ) {
				foreach ( $items as $id ) {
					update_post_meta( $id, 'dpad_settings_status', 'off' );
				}
				self::$admin_object->dpad_updated_message( 'disabled', '' );
			}
		}

		/**
		 * Find post data
		 *
		 * @param mixed $args
		 * @param string $get_orderby
		 *
		 * @return array $posts
		 * @since 1.0.0
		 *
		 */
		public static function dpad_find( $args = '' , $get_orderby) {
			$defaults = array(
				'post_status'    => 'any',
				'posts_per_page' => - 1,
				'offset'         => 0,
				'orderby'        => 'ID',
				'order'          => 'ASC',
			);

			$args = wp_parse_args( $args, $defaults );

			$args['post_type'] = self::post_type;

			$wc_dpad_query = new WP_Query( $args );
			$posts          = $wc_dpad_query->query( $args );

			self::$wc_dpad_found_items = $wc_dpad_query->found_posts;

			return $posts;
		}

		/**
		 * Count post data
		 *
		 * @return string
		 * @since 1.0.0
		 *
		 */
		public static function dpad_count() {
			return self::$wc_dpad_found_items;
		}

		/**
		 * Set column_headers property for table list
		 *
		 * @since 1.0.0
		 */
		protected function prepare_column_headers() {
			$this->_column_headers = array(
				$this->get_columns(),
				array(),
				$this->get_sortable_columns(),
			);
		}
	}
}
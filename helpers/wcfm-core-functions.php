<?php
if(!function_exists('wcfm_woocommerce_inactive_notice')) {
	function wcfm_woocommerce_inactive_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sWooCommerce Frontend Manager is inactive.%s The %sWooCommerce plugin%s must be active for the WooCommerce Frontend Manager to work. Please %sinstall & activate WooCommerce%s', 'wc-frontend-manager' ), '<strong>', '</strong>', '<a target="_blank" href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', '<a href="' . admin_url( 'plugin-install.php?tab=search&s=woocommerce' ) . '">', '&nbsp;&raquo;</a>' ); ?></p>
		</div>
		<?php
	}
}

if(!function_exists('wcfm_woocommerce_version_notice')) {
	function wcfm_woocommerce_version_notice() {
		?>
		<div id="message" class="error">
		<p><?php printf( __( '%sOpps ..!!!%s You are using %sWC %s. WCFM works only with %sWC 3.0+%s. PLease upgrade your WooCommerce version now to make your life easier and peaceful by using WCFM.', 'wc-frontend-manager' ), '<strong>', '</strong>', '<strong>', WC_VERSION . '</strong>', '<strong>', '</strong>' ); ?></p>
		</div>
		<?php
	}
}

if(!function_exists('wcfm_wcfmu_inactive_notice')) {
	function wcfm_wcfmu_inactive_notice() {
		?>
		<div id="message" class="notice notice-warning">
		<p><?php printf( __( 'You didn\'t get your %sWCFM Ultimate%s yet ..!!! Then quickly install %sWCFM Ultimate%s now to add more Power to your WooCommerce Frontend Manager.', 'wc-frontend-manager' ), '<strong>', '</strong>', '<a target="_blank" href="http://wclovers.com/product/woocommerce-frontend-manager-ultimate/">', '</a>' ); ?></p>
		</div>
		<?php
	}
}

if(!function_exists('wcfm_restriction_message_show')) {
	function wcfm_restriction_message_show( $feature = '', $text_only = false ) {
		?>
		<div class="collapse wcfm-collapse">
		  <div class="wcfm-container">
			  <div class="wcfm-content">
					<div id="wcfmu-feature-missing-message" class="wcfm-warn-message wcfm-wcfmu" style="display: block;">
						<p><span class="fa fa-warning"></span>
						<?php printf( __( '%s' . $feature . '%s: You don\'t have permission to access this page. Please contact your %sStore Admin%s for assistance.', 'wc-frontend-manager' ), '<strong>', '</strong>', '<strong>', '</strong>' ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

if(!function_exists('wcfmu_feature_help_text_show')) {
	function wcfmu_feature_help_text_show( $feature, $only_admin = false, $text_only = false ) {
		
		if( wcfm_is_vendor() ) {
			if( !$only_admin ) {
				if( $text_only ) {
					_e( $feature . ': Please contact your Store Admin to avail this feature.', 'wc-frontend-manager' );
				} else {
					?>
					<div id="wcfmu-feature-missing-message" class="wcfm-warn-message wcfm-wcfmu" style="display: block;">
						<p><span class="fa fa-warning"></span>
						<?php printf( __( '%s' . $feature . '%s: Please contact your %sStore Admin%s to avail this feature.', 'wc-frontend-manager' ), '<strong>', '</strong>', '<strong>', '</strong>' ); ?></p>
					</div>
					<?php
				}
			}
		} else {
			if( $text_only ) {
				_e( $feature . ': Upgrade your WCFM to WCFM Ultimate to avail this feature.', 'wc-frontend-manager' );
			} else {
				?>
				<div id="wcfmu-feature-missing-message" class="wcfm-warn-message wcfm-wcfmu" style="display: block;">
					<p><span class="fa fa-warning"></span><?php printf( __( '%s' . $feature . '%s: Upgrade your WCFM to %sWCFM Ultimate%s to avail this feature.', 'wc-frontend-manager' ), '<strong>', '</strong>', '<a target="_blank" href="http://wclovers.com/product/woocommerce-frontend-manager-ultimate/"><strong>', '</strong></a>' ); ?></p>
				</div>
				<?php
			}
		}
	}
}

if( !function_exists( 'wcfm_is_marketplace' ) ) {
	function wcfm_is_marketplace() {
		$active_plugins = (array) get_option( 'active_plugins', array() );
		
		// WC Vendors Check
		$is_marketplace = ( in_array( 'wc-vendors/class-wc-vendors.php', $active_plugins ) || array_key_exists( 'wc-vendors/class-wc-vendors.php', $active_plugins ) ) ? 'wcvendors' : false;
		
		// WC Marketplace Check
		if( !$is_marketplace )
			$is_marketplace = ( in_array( 'dc-woocommerce-multi-vendor/dc_product_vendor.php', $active_plugins ) || array_key_exists( 'dc-woocommerce-multi-vendor/dc_product_vendor.php', $active_plugins ) ) ? 'wcmarketplace' : false;
		
		// WC Product Vendors Check
		if( !$is_marketplace )
			$is_marketplace = ( in_array( 'woocommerce-product-vendors/woocommerce-product-vendors.php', $active_plugins ) || array_key_exists( 'woocommerce-product-vendors/woocommerce-product-vendors.php', $active_plugins ) ) ? 'wcpvendors' : false;
		
		return $is_marketplace;
	}
}

if( !function_exists( 'wcfm_is_vendor' ) ) {
	function wcfm_is_vendor() {
		if( !is_user_logged_in() ) return false;
		
		$is_marketplace = wcfm_is_marketplace();
		
		if( $is_marketplace ) {
			if( 'wcvendors' == $is_marketplace ) {
			  if ( WCV_Vendors::is_vendor( get_current_user_id() ) ) return true;
			} elseif( 'wcmarketplace' == $is_marketplace ) {
				if( is_user_wcmp_vendor( get_current_user_id() ) ) return true;
			} elseif( 'wcpvendors' == $is_marketplace ) {
				if( WC_Product_Vendors_Utils::is_vendor( get_current_user_id() ) ) return true;
			}
		}
		
		return false;
	}
}

if( !function_exists( 'wcfm_is_booking' ) ) {
	function wcfm_is_booking() {
		
		// WC Bookings Check
		$is_booking = ( WCFM_Dependencies::wcfm_bookings_plugin_active_check() ) ? 'wcbooking' : false;
		
		return $is_booking;
	}
}

if( !function_exists( 'wcfm_is_subscription' ) ) {
	function wcfm_is_subscription() {
		
		// WC Subscriptions Check
		$is_booking = ( WCFM_Dependencies::wcfm_subscriptions_plugin_active_check() ) ? 'wcsubscriptions' : false;
		
		return $is_booking;
	}
}

if(!function_exists('is_wcfm_page')) {
	function is_wcfm_page() {    
		$pages = get_option("wcfm_page_options");
		if(isset($pages['wc_frontend_manager_page_id'])) {
			return is_page( $pages['wc_frontend_manager_page_id'] ) ? true : false;
		}
		return false;
	}
}

if(!function_exists('get_wcfm_page')) {
	function get_wcfm_page() {
		$pages = get_option("wcfm_page_options");
		if(isset($pages['wc_frontend_manager_page_id'])) {
			return get_permalink( $pages['wc_frontend_manager_page_id'] );
		}
		return false;
	}
}

if(!function_exists('get_wcfm_url')) {
	function get_wcfm_url() {
		return get_wcfm_page();
	}
}

if ( ! function_exists( 'is_wc_endpoint_url' ) ) {

	/**
	 * is_wcfm_endpoint_url - Check if an endpoint is showing.
	 * @param  string $endpoint
	 * @return bool
	 */
	function is_wcfm_endpoint_url( $endpoint = false ) {
		global $WCFM, $WCFM_Query, $wp;

		$wcfm_endpoints = $WCFM_Query->get_query_vars();

		if ( $endpoint !== false ) {
			if ( ! isset( $wc_endpoints[ $endpoint ] ) ) {
				return false;
			} else {
				$endpoint_var = $wcfm_endpoints[ $endpoint ];
			}

			return isset( $wp->query_vars[ $endpoint_var ] );
		} else {
			foreach ( $wcfm_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}
	}
}

if(!function_exists('get_wcfm_products_url')) {
	function get_wcfm_products_url( $product_status = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_products_url = wcfm_get_endpoint_url( 'wcfm-products', '', $wcfm_page );
		if($product_status) $wcfm_products_url = add_query_arg( 'product_status', $product_status, $wcfm_products_url );
		return $wcfm_products_url;
	}
}

if(!function_exists('get_wcfm_edit_product_url')) {
	function get_wcfm_edit_product_url( $product_id = '', $the_product = array() ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_edit_product_url = wcfm_get_endpoint_url( 'wcfm-products-manage', $product_id, $wcfm_page );
		return $wcfm_edit_product_url;
	}
}

if(!function_exists('get_wcfm_import_product_url')) {
	function get_wcfm_import_product_url( $step = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_import_product_url = wcfm_get_endpoint_url( 'wcfm-products-import', '', $wcfm_page );
		if($step) $wcfm_import_product_url = add_query_arg( 'step', $step, $wcfm_import_product_url );
		return $wcfm_import_product_url;
	}
}

if(!function_exists('get_wcfm_export_product_url')) {
	function get_wcfm_export_product_url( ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_export_product_url = wcfm_get_endpoint_url( 'wcfm-products-export', '', $wcfm_page );
		return $wcfm_export_product_url;
	}
}

if(!function_exists('get_wcfm_coupons_url')) {
	function get_wcfm_coupons_url() {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_coupons_url = wcfm_get_endpoint_url( 'wcfm-coupons', '', $wcfm_page );
		return $wcfm_coupons_url;
	}
}

if(!function_exists('get_wcfm_coupons_manage_url')) {
	function get_wcfm_coupons_manage_url( $coupon_id = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_coupon_manage_url = wcfm_get_endpoint_url( 'wcfm-coupons-manage', $coupon_id, $wcfm_page );
		return $wcfm_coupon_manage_url;
	}
}

if(!function_exists('get_wcfm_orders_url')) {
	function get_wcfm_orders_url( $order_ststus = '') {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_orders_url = wcfm_get_endpoint_url( 'wcfm-orders', '', $wcfm_page );
		if( $order_ststus ) $wcfm_orders_url = add_query_arg( 'order_status', $order_ststus, $wcfm_orders_url );
		return $wcfm_orders_url;
	}
}

if(!function_exists('get_wcfm_view_order_url')) {
	function get_wcfm_view_order_url($order_id = '') {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_view_order_url = wcfm_get_endpoint_url( 'wcfm-orders-details', $order_id, $wcfm_page );
		return $wcfm_view_order_url;
	}
}

if(!function_exists('get_wcfm_reports_url')) {
	function get_wcfm_reports_url( $range = '', $report_type = 'wcfm-reports-sales-by-date' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_reports_url = wcfm_get_endpoint_url( $report_type, '', $wcfm_page );
		if( $range ) $get_wcfm_reports_url = add_query_arg( 'range', $range, $get_wcfm_reports_url );
		if( $report_type == 'wcfm-reports-sales-by-date' ) $get_wcfm_reports_url = apply_filters( 'wcfm_default_reports_url', $get_wcfm_reports_url );
		return $get_wcfm_reports_url;
	}
}

if(!function_exists('get_wcfm_profile_url')) {
	function get_wcfm_profile_url() {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_profile_url = wcfm_get_endpoint_url( 'wcfm-profile', '', $wcfm_page );
		return $get_wcfm_profile_url;
	}
}

if(!function_exists('get_wcfm_analytics_url')) {
	function get_wcfm_analytics_url() {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_analytics_url = wcfm_get_endpoint_url( 'wcfm-analytics', '', $wcfm_page );
		return $get_wcfm_analytics_url;
	}
}

if(!function_exists('get_wcfm_settings_url')) {
	function get_wcfm_settings_url() {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_settings_url = wcfm_get_endpoint_url( 'wcfm-settings', '', $wcfm_page );
		return $get_wcfm_settings_url;
	}
}

if(!function_exists('get_wcfm_knowledgebase_url')) {
	function get_wcfm_knowledgebase_url() {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_knowledgebase_url = wcfm_get_endpoint_url( 'wcfm-knowledgebase', '', $wcfm_page );
		return $get_wcfm_knowledgebase_url;
	}
}

if(!function_exists('get_wcfm_messages_url')) {
	function get_wcfm_messages_url( $type = '' ) {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$get_wcfm_messages_url = wcfm_get_endpoint_url( 'wcfm-messages', '', $wcfm_page );
		if( $type ) $get_wcfm_messages_url = add_query_arg( 'type', $type, $get_wcfm_messages_url );
		return $get_wcfm_messages_url;
	}
}

if(!function_exists('get_wcfm_bookings_dashboard_url')) {
	function get_wcfm_bookings_dashboard_url( $booking_ststus = '') {
		global $WCFM;
		$wcfm_page = get_wcfm_page();
		$wcfm_bookings_dashboard_url = wcfm_get_endpoint_url( 'wcfm-bookings-dashboard', '', $wcfm_page );
		return $wcfm_bookings_dashboard_url;
	}
}

if(!function_exists('get_wcfm_products_manager_messages')) {
	function get_wcfm_products_manager_messages() {
		global $WCFM;
		
		$messages = array(
											'no_title' => __('Please insert Product Title before submit.', 'wc-frontend-manager'),
											'sku_unique' => __('Product SKU must be unique.', 'wc-frontend-manager'),
											'variation_sku_unique' => __('Variation SKU must be unique.', 'wc-frontend-manager'),
											'product_saved' => __('Product Successfully Saved.', 'wc-frontend-manager'),
											'product_published' => __('Product Successfully Published.', 'wc-frontend-manager'),
											);
		
		return $messages;
	}
}

if(!function_exists('get_wcfm_coupons_manage_messages')) {
	function get_wcfm_coupons_manage_messages() {
		global $WCFM;
		
		$messages = array(
											'no_title' => __( 'Please insert atleast Coupon Title before submit.', 'wc-frontend-manager' ),
											'coupon_saved' => __( 'Coupon Successfully Saved.', 'wc-frontend-manager' ),
											'coupon_published' => __( 'Coupon Successfully Published.', 'wc-frontend-manager' ),
											);
		
		return $messages;
	}
}

/**
 * Get endpoint URL.
 *
 * Gets the URL for an endpoint, which varies depending on permalink settings.
 *
 * @param  string $endpoint
 * @param  string $value
 * @param  string $permalink
 *
 * @return string
 */
function wcfm_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	if ( ! $permalink ) {
		$permalink = get_permalink();
	}
	
	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
	$endpoint = ! empty( $wcfm_modified_endpoints[ $endpoint ] ) ? $wcfm_modified_endpoints[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}

	return apply_filters( 'wcfm_get_endpoint_url', $url, $endpoint, $value, $permalink );
}
?>
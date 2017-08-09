<?php

/**
 * WCFM plugin core
 *
 * Marketplace WC Marketplace Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.1.0
 */
 
class WCFM_WCMarketplace {
	
	private $vendor_id;
	private $vendor_term;
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_vendor() ) {
    	
    	$this->vendor_id   = get_current_user_id();
    	$this->vendor_term = get_user_meta( $this->vendor_id, '_vendor_term_id', true );
		
    	// Store Identity
    	add_filter( 'wcfm_store_logo', array( &$this, 'wcmarketplace_store_logo' ) );
    	add_filter( 'wcfm_store_name', array( &$this, 'wcmarketplace_store_name' ) );
    	
    	// WCFM Menu Filter
    	add_filter( 'wcfm_menus', array( &$this, 'wcmarketplace_wcfm_menus' ), 30 );
    	add_filter( 'wcfm_add_new_product_sub_menu', array( &$this, 'wcmarketplace_add_new_product_sub_menu' ) );
    	add_filter( 'wcfm_add_new_coupon_sub_menu', array( &$this, 'wcmarketplace_add_new_coupon_sub_menu' ) );
    	add_filter( 'wcmp_vendor_dashboard_nav', array( &$this, 'wcmarketplace_wcfm_vendor_dashboard_nav' ) );
    	
    	// WP Admin View
    	add_filter( 'wcfm_allow_wp_admin_view', array( &$this, 'wcmarketplace_allow_wp_admin_view' ) );
    	
			// Allow Vendor user to manage product from catalog
			add_filter( 'wcfm_allwoed_user_rols', array( &$this, 'allow_wcmarketplace_vendor_role' ) );
			
			// Filter Vendor Products
			add_filter( 'wcfm_products_args', array( &$this, 'wcmarketplace_products_args' ) );
			
			// Manage Vendor Product Permissions
			add_filter( 'wcfm_product_types', array( &$this, 'wcmarketplace_is_allow_product_types'), 100 );
			add_filter( 'wcfm_product_shipping_class', array( &$this, 'wcmarketplace_product_shipping_class'), 100 );
			add_filter( 'wcfm_product_manage_fields_general', array( &$this, 'wcmarketplace_is_allow_fields_general' ), 100 );
			add_filter( 'wcfm_is_allow_inventory', array( &$this, 'wcmarketplace_is_allow_inventory' ) );
			add_filter( 'wcfm_is_allow_shipping', array( &$this, 'wcmarketplace_is_allow_shipping' ) );
			add_filter( 'wcfm_is_allow_tax', array( &$this, 'wcmarketplace_is_allow_tax' ) );
			add_filter( 'wcfm_is_allow_attribute', array( &$this, 'wcmarketplace_is_allow_attribute' ) );
			add_filter( 'wcfm_is_allow_variable', array( &$this, 'wcmarketplace_is_allow_variable' ) );
			add_filter( 'wcfm_is_allow_linked', array( &$this, 'wcmarketplace_is_allow_linked' ) );
			add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcmarketplace_product_manage_vendor_association' ), 10, 2 ); 
			
			// Manage Vendor Product Export Permissions - 2.4.2
			add_filter( 'woocommerce_product_export_row_data', array( &$this, 'wcmarketplace_product_export_row_data' ), 100, 2 );
			
			// Filter Vendor Coupons
			add_filter( 'wcfm_coupons_args', array( &$this, 'wcmarketplace_coupons_args' ) );
			
			// Manage Vendor Coupon Permission
			add_filter( 'wcfm_coupon_types', array( &$this, 'wcmarketplace_coupon_types' ) );
			
			// Manage Order Details Permission
			add_filter( 'wcfm_allow_order_details', array( &$this, 'wcmarketplace_is_allow_order_details' ) );
			add_filter( 'wcfm_valid_line_items', array( &$this, 'wcmarketplace_valid_line_items' ), 10, 3 );
			add_filter( 'wcfm_order_details_shipping_line_item', array( &$this, 'wcmarketplace_is_allow_order_details_shipping_line_item' ) );
			add_filter( 'wcfm_order_details_tax_line_item', array( &$this, 'wcmarketplace_is_allow_order_details_tax_line_item' ) );
			add_filter( 'wcfm_order_details_line_total_head', array( &$this, 'wcmarketplace_is_allow_order_details_line_total_head' ) );
			add_filter( 'wcfm_order_details_line_total', array( &$this, 'wcmarketplace_is_allow_order_details_line_total' ) );
			add_filter( 'wcfm_order_details_tax_total', array( &$this, 'wcmarketplace_is_allow_order_details_tax_total' ) );
			add_filter( 'wcfm_order_details_fee_line_item', array( &$this, 'wcmarketplace_is_allow_order_details_fee_line_item' ) );
			add_filter( 'wcfm_order_details_refund_line_item', array( &$this, 'wcmarketplace_is_allow_order_details_refund_line_item' ) );
			add_filter( 'wcfm_order_details_coupon_line_item', array( &$this, 'wcmarketplace_is_allow_order_details_coupon_line_item' ) );
			add_filter( 'wcfm_order_details_total', array( &$this, 'wcmarketplace_is_allow_wcfm_order_details_total' ) );
			add_action( 'wcfm_order_details_after_line_total_head', array( &$this, 'wcmarketplace_after_line_total_head' ) );
			add_action( 'wcfm_after_order_details_line_total', array( &$this, 'wcmarketplace_after_line_total' ), 10, 2 );
			add_action( 'wcfm_order_totals_after_total', array( &$this, 'wcmarketplace_order_total_commission' ) );
			add_filter( 'wcfm_generate_csv_url', array( &$this, 'wcmarketplace_generate_csv_url' ), 10, 2 );
			
			// Report Filter
			add_filter( 'wcfm_report_out_of_stock_query_from', array( &$this, 'wcmarketplace_report_out_of_stock_query_from' ), 100, 2 );
			add_filter( 'woocommerce_reports_order_statuses', array( &$this, 'wcmarketplace_reports_order_statuses' ) );
			add_filter( 'woocommerce_dashboard_status_widget_top_seller_query', array( &$this, 'wcmarketplace_dashboard_status_widget_top_seller_query'), 100 );
			//add_filter( 'woocommerce_reports_get_order_report_data', array( &$this, 'wcmarketplace_reports_get_order_report_data'), 100 );
			
			// Knowledgebase
			add_action( 'before_wcfm_knowledgebase' , array( &$this, 'wcmarketplace_wcfm_knowledgebase' ) );
		}
  }
  
  // WCFM WCMp Store Logo
  function wcmarketplace_store_logo( $store_logo ) {
  	$vendor = get_wcmp_vendor($this->vendor_id);
  	if ( $vendor->image ) {
			$store_logo = $vendor->image;
		}
  	return $store_logo;
  }
  
  // WCFM WCMp Store Name
  function wcmarketplace_store_name( $store_name ) {
  	$vendor = get_wcmp_vendor( $this->vendor_id );
  	$shop_name = get_user_meta( $this->vendor_id , '_vendor_page_title', true);
  	if( $shop_name ) { $store_name = '<a target="_blank" href="' . apply_filters('wcmp_vendor_shop_permalink', $vendor->permalink) . '">' . $shop_name . '</a>'; }
  	else { $store_name = '<a target="_blank" href="' . apply_filters('wcmp_vendor_shop_permalink', $vendor->permalink) . '">' . __('Shop', 'wc-frontend-manager') . '</a>'; }
  	return $store_name;
  }
  
  // WCFM wcmarketplace Menu
  function wcmarketplace_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	if( !current_user_can( 'edit_products' ) ) unset( $menus['wcfm-products'] );
  	if( !current_user_can( 'edit_shop_coupons' ) ) unset( $menus['wcfm-coupons'] );
  	
  	return $menus;
  }
  
  // WCMp Add New Product Sub menu
  function wcmarketplace_add_new_product_sub_menu( $has_new ) {
  	if( !current_user_can( 'edit_products' ) ) $has_new = false;
  	return $has_new;
  }
  
  // WCMp Add New Coupon Sub menu
  function wcmarketplace_add_new_coupon_sub_menu( $has_new ) {
  	if( !current_user_can( 'edit_shop_coupons' ) ) $has_new = false;
  	return $has_new;
  }
  
  // WCMp menu
  function wcmarketplace_wcfm_vendor_dashboard_nav( $vendor_nav ) {
  	global $WCFM;
  	
  	// WCMp Dashboard Menu
  	$vendor_nav['dashboard']['url'] = '#';
		$vendor_nav['dashboard']['submenu'] = array(
																								'wcmp-dashboard' => array(
																										'label' => __('WCMp', 'wc-frontend-manager')
																										, 'url' => wcmp_get_vendor_dashboard_endpoint_url('dashboard')
																										, 'capability' => apply_filters('wcmp_vendor_dashboard_menu_dashboard_capability', true)
																										, 'position' => 10
																										, 'link_target' => '_self'
																								),
																								'wcfm-dashboard' => array(
																										'label' => __('WCFM', 'wc-frontend-manager')
																										, 'url' => get_wcfm_page()
																										, 'capability' => apply_filters('wcmp_vendor_dashboard_menu_dashboard_capability', true)
																										, 'position' => 20
																										, 'link_target' => '_self'
																								)
																						);
  	
  	// WCMp Products Menu
  	if( current_user_can( 'edit_products' ) ) {
  		$vendor_nav['vendor-products']['url'] = '#';
  		$vendor_nav['vendor-products']['submenu'] = array(
																												'add-new-product' => array(
																														'label' => __('Add Product', 'wc-frontend-manager')
																														, 'url' => get_wcfm_edit_product_url()
																														, 'capability' => apply_filters('wcmp_vendor_dashboard_menu_add_new_product_capability', 'edit_products')
																														, 'position' => 10
																														, 'link_target' => '_self'
																												),
																												'products' => array(
																														'label' => __('Products', 'wc-frontend-manager')
																														, 'url' => get_wcfm_products_url()
																														, 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_products_capability', 'edit_products')
																														, 'position' => 20
																														, 'link_target' => '_self'
																												)
																										);
  	} else {
  		unset( $vendor_nav['vendor-products'] );
  	}
  	
  	// WCMp Coupons Menu
  	if( current_user_can( 'edit_shop_coupons' ) ) {
  		$vendor_nav['vendor-promte']['url'] = '#';
  		$vendor_nav['vendor-promte']['submenu']['coupons']['url'] = get_wcfm_coupons_url();
  		$vendor_nav['vendor-promte']['submenu']['add-new-coupon']['url'] = get_wcfm_coupons_manage_url();
  	} else {
  		unset( $vendor_nav['vendor-promte'] );
  	}
  	
  	// WCMp Reports Menu
  	$vendor_nav['vendor-report']['submenu']['wcfm-reports-sales-by-date'] = array(
																																									'label' => __( 'by Date', 'wc-frontend-manager' )
																																									, 'url' => get_wcfm_reports_url( '', 'wcfm-reports-sales-by-date' )
																																									, 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_report_capability', true)
																																									, 'position' => 20
																																									, 'link_target' => '_self'
																																							);
  	$vendor_nav['vendor-report']['submenu']['wcfm-reports-out-of-stock'] = array(
																																									'label' => __( 'Out of stock', 'wc-frontend-manager' )
																																									, 'url' => get_wcfm_reports_url( '', 'wcfm-reports-out-of-stock' )
																																									, 'capability' => apply_filters('wcmp_vendor_dashboard_menu_vendor_report_capability', true)
																																									, 'position' => 50
																																									, 'link_target' => '_self'
																																							);
  	
  	return $vendor_nav;
  }
  
  // WCMp WP-admin view
  function wcmarketplace_allow_wp_admin_view( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // WCMp user roles
  function allow_wcmarketplace_vendor_role( $allowed_roles ) {
  	if( wcfm_is_vendor() ) $allowed_roles[] = 'dc_vendor';
  	return $allowed_roles;
  }
  
  // Product args
  function wcmarketplace_products_args( $args ) {
  	if( wcfm_is_vendor() ) $args['author'] = $this->vendor_id;
  	return $args;
  }
  
  // Product Types
  function wcmarketplace_is_allow_product_types( $product_types ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('simple') ) unset( $product_types[ 'simple' ] );
  	if( !$WCMp->vendor_caps->vendor_can('variable') ) unset( $product_types[ 'variable' ] );
  	if( !$WCMp->vendor_caps->vendor_can('grouped') ) unset( $product_types[ 'grouped' ] );
  	if( !$WCMp->vendor_caps->vendor_can('external') ) unset( $product_types[ 'external' ] );
  	
  	if( !$WCMp->vendor_caps->vendor_can('attribute') ) unset( $product_types['variable'] );
  	
  	$wcfm_capability_options = get_option( 'wcfm_capability_options' );
  	$wc_frontend_manager_manage_subscription = ( isset( $wcfm_capability_options['manage_subscription'] ) ) ? $wcfm_capability_options['manage_subscription'] : 'no';
  	if( $wc_frontend_manager_manage_subscription == 'yes' ) unset( $product_types[ 'subscription' ] );
  	if( $wc_frontend_manager_manage_subscription == 'yes' ) unset( $product_types[ 'variable-subscription' ] );
  	
		return $product_types;
  }
  
  // Shipping Class filtering as Per vendor
  function wcmarketplace_product_shipping_class( $product_shipping_class ) {
  	$vendor_shipping_class_id = get_user_meta( $this->vendor_id, 'shipping_class_id', true );
  	$filtered_product_shipping_class = array();
  	
  	foreach($product_shipping_class as $product_shipping) {
			if( $vendor_shipping_class_id != $product_shipping->term_id ) continue;
			$filtered_product_shipping_class[$product_shipping->term_id] = $product_shipping;
		}
  	
  	return $filtered_product_shipping_class;
  }
  
  // General Fields
  function wcmarketplace_is_allow_fields_general( $general_fields ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('sku') ) unset( $general_fields['sku'] );
  		
  	return $general_fields;
  }
  
  // Inventory
  function wcmarketplace_is_allow_inventory( $allow ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('inventory') ) return false;
  	return $allow;
  }
  
  // Shipping
  function wcmarketplace_is_allow_shipping( $allow ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('shipping') ) return false;
  	return $allow;
  }
  
  // Tax
  function wcmarketplace_is_allow_tax( $allow ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('taxes') ) return false;
  	return $allow;
  }
  
  // Attributes
  function wcmarketplace_is_allow_attribute( $allow ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('attribute') ) return false;
  	return $allow;
  }
  
  // Variable
  function wcmarketplace_is_allow_variable( $allow ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('attribute') ) return false;
  	if( !$WCMp->vendor_caps->vendor_can('variable') ) return false;
  	return $allow;
  }
  
  // Linked
  function wcmarketplace_is_allow_linked( $allow ) {
  	global $WCMp;
  	if( !$WCMp->vendor_caps->vendor_can('linked_products') ) return false;
  	return $allow;
  }
  
  // Product Vendor association on Product save
  function wcmarketplace_product_manage_vendor_association( $new_product_id, $wcfm_products_manage_form_data ) {
  	global $WCFM, $WCMp;
  	
  	$vendor_term = get_user_meta( $this->vendor_id, '_vendor_term_id', true );
		$term = get_term( $vendor_term , 'dc_vendor_shop' );
		wp_delete_object_term_relationships( $new_product_id, 'dc_vendor_shop' );
		wp_set_post_terms( $new_product_id, $term->name , 'dc_vendor_shop', true );
  }
  
  // Product Export Data Filter - 2.4.2
  function wcmarketplace_product_export_row_data( $row, $product ) {
  	global $WCFM, $WCMp;
  	
  	$user_id = $this->vendor_id;
  	
  	$vendor = get_wcmp_vendor($user_id);
    $vendor_products = $vendor->get_products();
  	$products = array();
		foreach ($vendor_products as $vendor_product) {
			$products[] = $vendor_product->ID;
			if( $vendor_product->post_type == 'product_variation' ) $products[] = $vendor_product->post_parent;
		}
		
		if( !in_array( $product->get_ID(), $products ) ) return array();
		
		return $row;
  }
  
  // Coupons Args
  function wcmarketplace_coupons_args( $args ) {
  	if( wcfm_is_vendor() ) $args['author'] = $this->vendor_id;
  	return $args;
  }
  
  // Coupon Types
  function wcmarketplace_coupon_types( $types ) {
  	$wcmp_coupon_types = array( 'percent', 'fixed_product' );
  	foreach( $types as $type => $label ) 
  		if( !in_array( $type, $wcmp_coupon_types ) ) unset( $types[$type] );
  	return $types;
  } 
  
  // Order Status details
  function wcmarketplace_is_allow_order_details( $allow ) {
  	return false;
  }
  
  // Filter Order Details Line Items as Per Vendor
  function wcmarketplace_valid_line_items( $items, $order_id ) {
  	global $WCFM, $wpdb;
  	
  	$sql = "SELECT `product_id` FROM {$wpdb->prefix}wcmp_vendor_orders WHERE `vendor_id` = {$this->vendor_id} AND `order_id` = {$order_id}";
  	$valid_products = $wpdb->get_results($sql);
  	$valid_items = array();
  	if( !empty($valid_products) ) {
  		foreach( $valid_products as $valid_product ) {
  			$valid_items[] = $valid_product->product_id;
  		}
  	}
  	
  	$valid = array();
  	foreach ($items as $key => $value) {
			if ( in_array( $value['variation_id'], $valid_items) || in_array( $value['product_id'], $valid_items ) ) {
				$valid[] = $value;
			}
		}
  	return $valid;
  }
  
  // Order Details Shipping Line Item
  function wcmarketplace_is_allow_order_details_shipping_line_item( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Line Item
  function wcmarketplace_is_allow_order_details_tax_line_item( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_tax' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Total Line Item Head
  function wcmarketplace_is_allow_order_details_line_total_head( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Total Line Item
  function wcmarketplace_is_allow_order_details_line_total( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Tax Total
  function wcmarketplace_is_allow_order_details_tax_total( $allow ) {
  	//if ( !WC_Vendors::$pv_options->get_option( 'give_tax' ) ) $allow = false;
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Fee Line Item
  function wcmarketplace_is_allow_order_details_fee_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Coupon Line Item
  function wcmarketplace_is_allow_order_details_coupon_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Refunded Line Item
  function wcmarketplace_is_allow_order_details_refund_line_item( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // Order Details Total
  function wcmarketplace_is_allow_wcfm_order_details_total( $allow ) {
  	$allow = false;
  	return $allow;
  }
  
  // wcmarketplace After Order Total Line Head
  function wcmarketplace_after_line_total_head( $order ) {
  	global $WCFM, $WCMp;
  	?>
		<th class="line_cost sortable" data-sort="float"><?php _e( 'Commission', 'wc-frontend-manager' ); ?></th>
  	<?php
  	if ( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) {
  		?>
  		<th class="line_cost sortable no_ipad no_mob" data-sort="float"><?php _e( 'Shipping', 'wc-frontend-manager' ); ?></th>
  		<?php
  	}
  	
  	if ( $WCMp->vendor_caps->vendor_payment_settings('give_tax') ) {
  		?>
  		<th class="line_cost sortable no_ipad no_mob" data-sort="float"><?php _e( 'Tax', 'wc-frontend-manager' ); ?></th>
  		<?php
  	}
  	?>
  	<th></th>
  	<?php
  }
  
  // wcmarketplace after Order total Line item
  function wcmarketplace_after_line_total( $item, $order ) {
  	global $WCFM, $wpdb, $WCMp;
  	$qty = ( isset( $item['qty'] ) ? esc_html( $item['qty'] ) : '1' );
		
		$sql = "
			SELECT commission_amount AS line_total, shipping AS total_shipping, tax 
			FROM {$wpdb->prefix}wcmp_vendor_orders
			WHERE (product_id = " . $item['product_id'] . " OR product_id = " . $item['variation_id'] . ")
			AND   order_id = " . $order->get_id() . "
			AND   `vendor_id` = " . $this->vendor_id;
		$order_line_due = $wpdb->get_results( $sql );
		
		if( !empty( $order_line_due ) ) {
		?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( $order_line_due[0]->line_total ); ?></div>
			</td>
			<?php if ( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) { ?>
			<td class="line_cost no_ipad no_mob" width="1%">
				<div class="view"><?php echo ($order_line_due[0]->total_shipping == 'NAN' ) ? wc_price( 0 ) : wc_price( $order_line_due[0]->total_shipping ); ?></div>
			</td>
			<?php } ?>
			<?php if ( $WCMp->vendor_caps->vendor_payment_settings('give_tax') ) { ?>
			<td class="line_cost no_ipad no_mob" width="1%">
				<div class="view"><?php echo ( $order_line_due[0]->tax == 'NAN' ) ? wc_price( 0 ) : wc_price( $order_line_due[0]->tax ); ?></div>
			</td>
			<?php } ?>
		<?php
		} else {
			?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0 ); ?></div>
			</td>
			<?php if ( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) { ?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0 ); ?></div>
			</td>
			<?php } ?>
			<?php if ( $WCMp->vendor_caps->vendor_payment_settings('give_tax') ) { ?>
			<td class="line_cost" width="1%">
				<div class="view"><?php echo wc_price( 0 ); ?></div>
			</td>
			<?php } ?>
			<?php
		}
		?>
		<td></td>
		<?php
  }
  
  // WC marketplace Order Total Commission
  function wcmarketplace_order_total_commission( $order_id ) {
  	global $WCFM, $wpdb, $WCMp;
  	$sql = "
  	SELECT SUM(commission_amount) as line_total,
	   SUM(shipping) as shipping,
       SUM(tax) as tax
       FROM {$wpdb->prefix}wcmp_vendor_orders
       WHERE order_id = " . $order_id . "
       AND `vendor_id` = " . $this->vendor_id;
    $order_due = $wpdb->get_results( $sql );
  	$total = $order_due[0]->line_total; 
  	if ( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) {
  		$total += ( $order_due[0]->shipping == 'NAN' ) ? 0 : $order_due[0]->shipping; 
  	}
  	if ( $WCMp->vendor_caps->vendor_payment_settings('give_tax') ) {
  		$total += ( $order_due[0]->tax == 'NAN' ) ? 0 : $order_due[0]->tax; 
  	}
		?>
		<tr>
			<td class="label"><?php _e( 'Line Commission', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $order_due[0]->line_total ); ?></div>
			</td>
		</tr>
		<?php if ( $WCMp->vendor_caps->vendor_payment_settings('give_shipping') ) { ?>
		<tr>
			<td class="label"><?php _e( 'Shipping', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo ( $order_due[0]->shipping == 'NAN' ) ? wc_price( 0 ) : wc_price( $order_due[0]->shipping ); ?></div>
			</td>
		</tr>
		<?php } ?>
		<?php if ( $WCMp->vendor_caps->vendor_payment_settings('give_tax') ) { ?>
		<tr>
			<td class="label"><?php _e( 'Tax', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo ( $order_due[0]->tax == 'NAN' ) ? wc_price( 0 ) : wc_price( $order_due[0]->tax ); ?></div>
			</td>
		</tr>
		<?php } ?>
		<tr>
			<td class="label"><?php _e( 'Total Commission', 'wc-frontend-manager' ); ?>:</td>
			<td>
				
			</td>
			<td class="total">
				<div class="view"><?php echo wc_price( $total ); ?></div>
			</td>
		</tr>
		<?php
  }
  
  // CSV Export URL
  function wcmarketplace_generate_csv_url( $url, $order_id ) {
  	$url = admin_url('admin-ajax.php?action=wcmp_vendor_csv_download_per_order&order_id=' . $order_id . '&nonce=' . wp_create_nonce('wcmp_vendor_csv_download_per_order'));
  	return $url;
  }
  
  // Report Vendor Filter
  function wcmarketplace_report_out_of_stock_query_from( $query_from, $stock ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = $this->vendor_id;
  	
  	$query_from = "FROM {$wpdb->posts} as posts
			INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
			INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
			WHERE 1=1
			AND posts.post_type IN ( 'product', 'product_variation' )
			AND posts.post_status = 'publish'
			AND posts.post_author = {$user_id}
			AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
			AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
		";
		
		return $query_from;
  }
  
  // Report Order Data Status
  function wcmarketplace_reports_order_statuses( $order_status ) {
  	$order_status = array( 'completed', 'processing' );
  	return $order_status;
  }
  
  // WCVendor dashboard top seller query
  function wcmarketplace_dashboard_status_widget_top_seller_query( $query ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = $this->vendor_id;
  	
    $vendor = get_wcmp_vendor($user_id);
    $vendor_products = $vendor->get_products();
  	$products = array(0);
		foreach ($vendor_products as $vendor_product) {
			$products[] = $vendor_product->ID;
			if( $vendor_product->post_type == 'product_variation' ) $products[] = $vendor_product->post_parent;
		}
		if( !empty($products) )
			$query['where'] .= "AND order_item_meta_2.meta_value in (" . implode( ',', $products ) . ")";
  	
  	return $query;
  }
  
  // Report Data Filter as per Vendor
  function wcmarketplace_reports_get_order_report_data( $result ) {
  	global $WCFM, $wpdb, $_POST;
  	
  	$user_id = $this->vendor_id;
  	
  	$vendor = get_wcmp_vendor($user_id);
    $vendor_products = $vendor->get_products();
  	$products = array();
		foreach ($vendor_products as $vendor_product) {
			$products[] = $vendor_product->ID;
			if( $vendor_product->post_type == 'product_variation' ) $products[] = $vendor_product->post_parent;
		}
  	
  	if( !empty( $result ) && is_array( $result ) ) {
  		foreach( $result as $result_key => $result_val ) {
  			if( !in_array( $result_val->product_id, $products ) ) unset( $result[$result_key] );
  		}
  	}
  	
  	return $result;
  }
  
  // Showing WCMp Knowledgebases
  function wcmarketplace_wcfm_knowledgebase() {
  	global $WCFM, $WCMp;
  	
  	$args = array(
							'posts_per_page'   => -1,
							'offset'           => 0,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'wcmp_university',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('publish'),
							'suppress_filters' => true 
						);
		$wcmp_knowledgebases = get_posts( $args );
		
		if( !empty( $wcmp_knowledgebases ) ) {
  	  foreach( $wcmp_knowledgebases as $wcmp_knowledgebase ) {
  	  	?>
  	  	<div class="wcfm-container">
					<div id="wcfm_knowledgebase_listing_expander-<?php echo $wcmp_knowledgebase->ID; ?>" class="wcmp_knowledgebase wcfm-content">
						<?php
						echo '<h4>' . $wcmp_knowledgebase->post_title . '</h4>';
						echo $wcmp_knowledgebase->post_content;
						?>
					</div>
				</div>
				<div class="wcfm-clearfix"></div><br />
  	  	<?php
  	  }
		}
  }
}
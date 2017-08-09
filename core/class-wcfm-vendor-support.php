<?php
/**
 * WCFM plugin core
 *
 * Plugin Vendor Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.0.0
 */
 
class WCFM_Vendor_Support {

	public function __construct() {
		global $WCFM;
		
		// Login Redirect
		add_filter( 'woocommerce_login_redirect', array($this, 'wcfm_wc_vendor_login_redirect'), 50, 2 );
		add_filter( 'login_redirect', array($this, 'wcfm_vendor_login_redirect'), 50, 3 );
		
		if( wcfm_is_vendor() ) {
			add_filter( 'wcfm_orders_total_heading', array( &$this, 'wcfm_vendors_orders_total_heading' ) );
		}
		
		if( !wcfm_is_vendor()) {
			if( $WCFM->is_marketplece == 'wcvendors' ) {
		  	add_action( 'end_wcfm_products_manage', array( &$this, 'wcvendors_product_commission' ), 500 );
		  	add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcvendors_product_commission_save' ), 500, 2 );
			}
		}
		
		// WC Vendor Capability update
		add_action( 'wcvendors_option_updates', array( &$this, 'vendors_capability_option_updates' ), 10, 2 );
		
		// Product Vendors Manage Vendor Product Permissions
		if( $WCFM->is_marketplece == 'wcpvendors' ) {
			add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcpvendors_product_manage_vendor_association' ), 10, 2 );
		}
	}
	
		/**
	 * WCFM WC Vendor Login redirect
	 */
	function wcfm_wc_vendor_login_redirect( $redirect_to, $user ) {
		if ( isset($user->roles) && is_array($user->roles) ) {
			if ( in_array( 'vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'dc_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_admin_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_manager_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			}
		}
		
		return $redirect_to;
	}
	
	/**
	 * WCFM Vendor Login redirect
	 */
	function wcfm_vendor_login_redirect( $redirect_to, $request, $user ) {
		if ( isset($user->roles) && is_array($user->roles) ) {
			if ( in_array( 'vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'dc_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_admin_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			} elseif ( in_array( 'wc_product_vendors_manager_vendor', $user->roles ) ) {
				$redirect_to = get_wcfm_url();
			}
		}
		
		return $redirect_to;
	}
	
	/**
	 * Orders total heading as commission for vendors
	 */
	function wcfm_vendors_orders_total_heading( $heading ) {
		global $WCFM;
		
		$heading = __( 'Commission', 'wc-frontend-manager');
		return $heading;
	}
	
	// WCV Vendor Commission
	function wcvendors_product_commission( $product_id ) {
		global $WCFM;
		
		$pv_commission_rate = '';
		if( $product_id  ) {
			$pv_commission_rate = get_post_meta( $product_id , 'pv_commission_rate', true );
		}
		?>
		<!-- collapsible 12 - WCV Commission Support -->
		<div class="page_collapsible products_manage_commission simple variable grouped external booking" id="wcfm_products_manage_form_commission_head"><label class="fa fa-percent"></label><?php _e('Commission', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container simple variable external grouped booking">
			<div id="wcfm_products_manage_form_commission_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'product_manage_fields_commission', array(  
																																														"pv_commission_rate" => array('label' => __('Commission(%)', 'wc-frontend-manager') , 'type' => 'number', 'attributes' => array( 'min' => '', 'steps' => 1 ), 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title wcfm_ele simple variable external grouped booking', 'value' => $pv_commission_rate ),
																																									)) );
				?>
			</div>
		</div>
		<!-- end collapsible -->
		<div class="wcfm_clearfix"></div>
		<?php
	}
	
	// WCV Vendor Commision Save
	function wcvendors_product_commission_save( $new_product_id, $wcfm_products_manage_form_data ) {
		
		if( isset( $wcfm_products_manage_form_data['pv_commission_rate'] ) && !empty( $wcfm_products_manage_form_data['pv_commission_rate'] ) ) {
			update_post_meta( $new_product_id, 'pv_commission_rate', $wcfm_products_manage_form_data['pv_commission_rate'] );
		}
	}
	
	// Vendors Capability Options update
  function vendors_capability_option_updates( $options = array(), $tabname = 'capabilities' ) {
  	
  	if( $tabname == 'capabilities' ) {
  		$options = get_option( 'wcfm_capability_options' );
  		$is_marketplece = wcfm_is_marketplace();
  		
  		if( $is_marketplece ) {
  		
				if( $is_marketplece == 'wcvendors' ) {
					$vendor_role = get_role( 'vendor' );
				} elseif( $is_marketplece == 'wcmarketplace' ) {
					$vendor_role = get_role( 'dc_vendor' );
				}  elseif( $is_marketplece == 'wcpvendors' ) {
					$vendor_role = get_role( 'wc_product_vendors_admin_vendor' );
				}
				
				// Booking Capability
				if( wcfm_is_booking() ) {
					if( isset( $options['manage_booking'] ) && $options[ 'manage_booking' ] == 'yes' ) {
						$vendor_role->remove_cap( 'manage_bookings' );
						if( $is_marketplece == 'wcmarketplace' ) remove_wcmp_users_caps('manage_bookings');
					} else {
						$vendor_role->add_cap( 'manage_bookings' );
						if( $is_marketplece == 'wcmarketplace' ) add_wcmp_users_caps('manage_bookings');
					}
				}
				
				// Appointment Capability
				if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
						if( isset( $options['manage_appointment'] ) && $options[ 'manage_appointment' ] == 'yes' ) {
							$vendor_role->remove_cap( 'manage_appointments' );
							if( $is_marketplece == 'wcmarketplace' ) remove_wcmp_users_caps('manage_appointments');
						} else {
							$vendor_role->add_cap( 'manage_appointments' );
							if( $is_marketplece == 'wcmarketplace' ) add_wcmp_users_caps('manage_appointments');
						}
					}
				}
				
				// Submit Products
				if( isset( $options[ 'submit_products' ] ) && $options[ 'submit_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_products' );
					$vendor_role->remove_cap( 'manage_products' );
					$vendor_role->remove_cap( 'read_products' );
				} else {
					$vendor_role->add_cap( 'edit_products' );
					$vendor_role->add_cap( 'manage_products' );
					$vendor_role->add_cap( 'read_products' );
				}
				
				// Publish Coupon
				if( isset( $options[ 'publish_products' ] ) && $options[ 'publish_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'publish_products' );
				} else {
					$vendor_role->add_cap( 'publish_products' );
				}
				
				// Live Products Edit
				if( isset( $options[ 'edit_live_products' ] ) && $options[ 'edit_live_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_published_products' );
				} else {
					$vendor_role->add_cap( 'edit_published_products' );
				}
				
				// Delete Products
				if( isset( $options[ 'delete_products' ] ) && $options[ 'delete_products' ] == 'yes' ) {
					$vendor_role->remove_cap( 'delete_published_products' );
					$vendor_role->remove_cap( 'delete_products' );
				} else {
					$vendor_role->add_cap( 'delete_published_products' );
					$vendor_role->add_cap( 'delete_products' );
				}
				
				// Submit Cuopon
				if( isset( $options[ 'submit_coupons' ] ) && $options[ 'submit_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_shop_coupons' );
					$vendor_role->remove_cap( 'manage_shop_coupons' );
					$vendor_role->remove_cap( 'read_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'edit_shop_coupons' );
					$vendor_role->add_cap( 'manage_shop_coupons' );
					$vendor_role->add_cap( 'read_shop_coupons' );
				}
				
				// Publish Coupon
				if( isset( $options[ 'publish_coupons' ] ) && $options[ 'publish_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'publish_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'publish_shop_coupons' );
				}
				
				// Live Coupon Edit
				if( isset( $options[ 'edit_live_coupons' ] ) && $options[ 'edit_live_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'edit_published_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'edit_published_shop_coupons' );
				}
				
				// Delete Coupon
				if( isset( $options[ 'delete_coupons' ] ) && $options[ 'delete_coupons' ] == 'yes' ) {
					$vendor_role->remove_cap( 'delete_published_shop_coupons' );
					$vendor_role->remove_cap( 'delete_shop_coupons' );
				} else {
					$vendor_role->add_cap( 'delete_published_shop_coupons' );
					$vendor_role->add_cap( 'delete_shop_coupons' );
				}
			}
		}
  }
  
  // Product Vendor association on Product save
  function wcpvendors_product_manage_vendor_association( $new_product_id, $wcfm_products_manage_form_data ) {
  	global $WCFM, $WCMp;
  	
  	
		// check post type to be product
		if ( 'product' === get_post_type( $new_product_id ) ) {
			
			$product_post = get_post( $new_product_id );
			
			if ( WC_Product_Vendors_Utils::is_vendor( $product_post->post_author ) ) {
				$vendor_data = WC_Product_Vendors_Utils::get_all_vendor_data( $product_post->post_author );
				if( $vendor_data && !empty( $vendor_data ) ) {
					$vendor_data_term = key( $vendor_data );
		
					// automatically set the vendor term for this product
					wp_set_object_terms( $new_product_id, $vendor_data_term, WC_PRODUCT_VENDORS_TAXONOMY );
				}
			}
		}
  }
  
  /**
   * Total commission paid by Admin
   */
  function get_total_commission() {
  	global $WCFM, $wpdb, $WCMp;
  	
  	$commission = 0;
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$commission_table = 'pv_commission'; 
  		$total_due = 'total_due';
  		$total_shipping = 'total_shipping';
  		$tax = 'tax';
  		$status = 'status';
  		$time = 'time';
		} elseif( $marketplece == 'wcmarketplace' ) {
			$commission_table = 'wcmp_vendor_orders'; 
  		$total_due = 'commission_amount';
  		$total_shipping = 'shipping';
  		$tax = 'tax';
  		$status = 'status';
  		$time = 'created';
		} elseif( $marketplece == 'wcpvendors' ) {
			$commission_table = 'wcpv_commissions'; 
  		$total_due = 'total_commission_amount';
  		$total_shipping = 'product_shipping_amount';
  		$tax = 'product_tax_amount';
  		$status = 'commission_status';
  		$time = 'paid_date';
		}
  	
  	$sql = "SELECT SUM( commission.{$total_due} ) AS total_due, SUM( commission.{$total_shipping} ) AS total_shipping, SUM( commission.{$tax} ) AS tax FROM {$wpdb->prefix}{$commission_table} AS commission";
		$sql .= " WHERE 1=1";
		if( $marketplece != 'wcmarketplace' ) $sql .= " AND commission.{$status} = 'paid'";
		$sql .= " AND DATE( commission.{$time} ) BETWEEN DATE_SUB( NOW(), INTERVAL 7 DAY ) AND NOW()";
		
		$total_commissions = $wpdb->get_results( $sql );
		if( !empty($total_commissions) ) {
			foreach( $total_commissions as $total_commission ) {
				$commission = $total_commission->total_due;
				if( $marketplece == 'wcvendors' ) {
					if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { $commission += $total_commission->total_shipping; } 
					if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { $commission += $total_commission->tax; }
				} elseif( $marketplece == 'wcmarketplace' ) {
					if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) { $commission += ( $total_commission->total_shipping == 'NAN' ) ? 0 : $total_commission->total_shipping; } 
					if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) { $commission += ( $total_commission->tax == 'NAN' ) ? 0 : $total_commission->tax; }
				}
			}
		}
		if( !$commission ) $commission = 0;
		
		return $commission;
  }
  
  /**
   * Total sales by vendor items
   */
  function get_total_total_sell() {
  	global $WCFM, $wpdb, $WCMp;
  	
  	$total_sell = 0;
  	
  	$marketplece = wcfm_is_marketplace();
  	if( $marketplece == 'wcvendors' ) {
  		$commission_table = 'pv_commission'; 
  		$qty = 'qty';
  		$time = 'time';
  		$func = 'SUM';
		} elseif( $marketplece == 'wcmarketplace' ) {
			$commission_table = 'wcmp_vendor_orders'; 
  		$qty = 'product_id';
  		$time = 'created';
  		$func = 'COUNT';
		} elseif( $marketplece == 'wcpvendors' ) {
			$commission_table = 'wcpv_commissions'; 
  		$qty = 'product_quantity';
  		$time = 'order_date';
  		$func = 'SUM';
		}
  	
  	$sql = "SELECT {$func}( commission.{$qty} ) AS qty FROM {$wpdb->prefix}{$commission_table} AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND DATE( commission.{$time} ) BETWEEN DATE_SUB( NOW(), INTERVAL 7 DAY ) AND NOW()";
		
		$total_sell = $wpdb->get_var( $sql );
		if( !$total_sell ) $total_sell = 0;
		
		return $total_sell;
  }
}
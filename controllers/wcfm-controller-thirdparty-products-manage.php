<?php
/**
 * WCFM plugin controllers
 *
 * Third Party Plugin Products Manage Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.2.2
 */

class WCFM_ThirdParty_Products_Manage_Controller {
	
	public function __construct() {
		global $WCFM;
		
		// WC Paid Listing Support - 2.3.4
    if( $wcfm_allow_job_package = apply_filters( 'wcfm_is_allow_job_package', true ) ) {
			if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
				// WC Paid Listing Product Meta Data Save
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wcpl_product_meta_save' ), 50, 2 );
			}
		}
		
		// WC Rental & Booking Support - 2.3.8
    if( $wcfm_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
				// WC Rental Product Meta Data Save
				add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_wcrental_product_meta_save' ), 80, 2 );
			}
		}
		
		// Third Party Product Meta Data Save
    add_action( 'after_wcfm_products_manage_meta_save', array( &$this, 'wcfm_thirdparty_products_manage_meta_save' ), 100, 2 );
	}
	
	/**
	 * WC Paid Listing Product Meta data save
	 */
	function wcfm_wcpl_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		if( $wcfm_products_manage_form_data['product_type'] == 'job_package' ) {
	
			$job_package_fields = array(
				'_job_listing_package_subscription_type',
				'_job_listing_limit',
				'_job_listing_duration'
			);
	
			foreach ( $job_package_fields as $field_name ) {
				if ( isset( $wcfm_products_manage_form_data[ $field_name ] ) ) {
					update_post_meta( $new_product_id, $field_name, stripslashes( $wcfm_products_manage_form_data[ $field_name ] ) );
				}
			}
			
			// Featured
			$is_featured = ( isset( $wcfm_products_manage_form_data['_job_listing_featured'] ) ) ? 'yes' : 'no';
	
			update_post_meta( $new_product_id, '_job_listing_featured', $is_featured );
		}
	}
	
	/**
	 * WC Rental Product Meta data save
	 */
	function wcfm_wcrental_product_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		if( $wcfm_products_manage_form_data['product_type'] == 'redq_rental' ) {
			$rental_fields = array(
				'pricing_type',
				'hourly_price',
				'general_price',
				'redq_rental_availability'
			);
	
			foreach ( $rental_fields as $field_name ) {
				if ( isset( $wcfm_products_manage_form_data[ $field_name ] ) ) {
					$rental_fields[ $field_name ] = $wcfm_products_manage_form_data[ $field_name ];
					update_post_meta( $new_product_id, $field_name, $wcfm_products_manage_form_data[ $field_name ] );
				}
			}
			
			update_post_meta( $new_product_id, '_price', $wcfm_products_manage_form_data[ 'general_price' ] );
			update_post_meta( $new_product_id, 'redq_all_data', $rental_fields );
		}
	}
	
	/**
	 * Third Party Product Meta data save
	 */
	function wcfm_thirdparty_products_manage_meta_save( $new_product_id, $wcfm_products_manage_form_data ) {
		global $wpdb, $WCFM, $_POST;
		
		// Yoast SEO Support
		if(WCFM_Dependencies::wcfm_yoast_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['yoast_wpseo_focuskw_text_input'])) {
				update_post_meta( $new_product_id, '_yoast_wpseo_focuskw_text_input', $wcfm_products_manage_form_data['yoast_wpseo_focuskw_text_input'] );
				update_post_meta( $new_product_id, '_yoast_wpseo_focuskw', $wcfm_products_manage_form_data['yoast_wpseo_focuskw_text_input'] );
			}
			if(isset($wcfm_products_manage_form_data['yoast_wpseo_metadesc'])) {
				update_post_meta( $new_product_id, '_yoast_wpseo_metadesc', strip_tags( $wcfm_products_manage_form_data['yoast_wpseo_metadesc'] ) );
			}
		}
		
		// WooCommerce Custom Product Tabs Lite Support
		if(WCFM_Dependencies::wcfm_wc_tabs_lite_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['product_tabs'])) {
				$frs_woo_product_tabs = array();
				if( !empty( $wcfm_products_manage_form_data['product_tabs'] ) ) {
					foreach( $wcfm_products_manage_form_data['product_tabs'] as $frs_woo_product_tab ) {
						if( $frs_woo_product_tab['title'] ) {
							// convert the tab title into an id string
							$tab_id = strtolower( wc_clean( $frs_woo_product_tab['title'] ) );
		
							// remove non-alphas, numbers, underscores or whitespace
							$tab_id = preg_replace( "/[^\w\s]/", '', $tab_id );
		
							// replace all underscores with single spaces
							$tab_id = preg_replace( "/_+/", ' ', $tab_id );
		
							// replace all multiple spaces with single dashes
							$tab_id = preg_replace( "/\s+/", '-', $tab_id );
		
							// prepend with 'tab-' string
							$tab_id = 'tab-' . $tab_id;
							
							$frs_woo_product_tabs[] = array(
																							'title'   => wc_clean( $frs_woo_product_tab['title'] ),
																							'id'      => $tab_id,
																							'content' => $frs_woo_product_tab['content']
																						);
						}
					}
					update_post_meta( $new_product_id, 'frs_woo_product_tabs', $frs_woo_product_tabs );
				} else {
					delete_post_meta( $new_product_id, 'frs_woo_product_tabs' );
				}
			}
		}
		
		// WooCommerce barcode & ISBN Support
		if(WCFM_Dependencies::wcfm_wc_barcode_isbn_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['barcode'])) {
				update_post_meta( $new_product_id, 'barcode', $wcfm_products_manage_form_data['barcode'] );
				update_post_meta( $new_product_id, 'ISBN', $wcfm_products_manage_form_data['ISBN'] );
			}
		}
		
		// WooCommerce MSRP Pricing Support
		if(WCFM_Dependencies::wcfm_wc_msrp_pricing_plugin_active_check()) {
			if(isset($wcfm_products_manage_form_data['_msrp_price'])) {
				update_post_meta( $new_product_id, '_msrp_price', strip_tags( $wcfm_products_manage_form_data['_msrp_price'] ) );
			}
		}
		
		// Quantities and Units for WooCommerce Support 
		if( $allow_quantities_units = apply_filters( 'wcfm_is_allow_quantities_units', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_quantities_units_plugin_active_check()) {
				if(isset($wcfm_products_manage_form_data['_wpbo_override'])) {
					update_post_meta( $new_product_id, '_wpbo_override', 'on' );
					update_post_meta( $new_product_id, '_wpbo_deactive', isset( $wcfm_products_manage_form_data['_wpbo_deactive'] ) ? 'on' : '' );
					update_post_meta( $new_product_id, '_wpbo_step', strip_tags( $wcfm_products_manage_form_data['_wpbo_step'] ) );
					update_post_meta( $new_product_id, '_wpbo_minimum', strip_tags( $wcfm_products_manage_form_data['_wpbo_minimum'] ) );
					update_post_meta( $new_product_id, '_wpbo_maximum', strip_tags( $wcfm_products_manage_form_data['_wpbo_maximum'] ) );
					update_post_meta( $new_product_id, '_wpbo_minimum_oos', strip_tags( $wcfm_products_manage_form_data['_wpbo_minimum_oos'] ) );
					update_post_meta( $new_product_id, '_wpbo_maximum_oos', strip_tags( $wcfm_products_manage_form_data['_wpbo_maximum_oos'] ) );
					update_post_meta( $new_product_id, 'unit', strip_tags( $wcfm_products_manage_form_data['unit'] ) );
				} else {
					update_post_meta( $new_product_id, '_wpbo_override', '' );
				}
			}
		}
		
		// WooCommerce Product Fees Support
		if( $allow_product_fees = apply_filters( 'wcfm_is_allow_product_fees', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_product_fees_plugin_active_check()) {
				update_post_meta( $new_product_id, 'product-fee-name', $wcfm_products_manage_form_data['product-fee-name'] );
				update_post_meta( $new_product_id, 'product-fee-amount', $wcfm_products_manage_form_data['product-fee-amount'] );
				$product_fee_multiplier = ( $wcfm_products_manage_form_data['product-fee-multiplier'] ) ? 'yes' : 'no';
				update_post_meta( $new_product_id, 'product-fee-multiplier', $product_fee_multiplier );
			}
		}
		
		// WooCommerce Bulk Discount Support
		if( $allow_bulk_discount = apply_filters( 'wcfm_is_allow_bulk_discount', true ) ) {
			if(WCFM_Dependencies::wcfm_wc_bulk_discount_plugin_active_check()) {
				$_bulkdiscount_enabled = ( $wcfm_products_manage_form_data['_bulkdiscount_enabled'] ) ? 'yes' : 'no';
				update_post_meta( $new_product_id, '_bulkdiscount_enabled', $_bulkdiscount_enabled );
				update_post_meta( $new_product_id, '_bulkdiscount_text_info', $wcfm_products_manage_form_data['_bulkdiscount_text_info'] );
				update_post_meta( $new_product_id, '_bulkdiscounts', $wcfm_products_manage_form_data['_bulkdiscounts'] );
				
				$bulk_discount_rule_counter = 0;
				foreach( $wcfm_products_manage_form_data['_bulkdiscounts'] as $bulkdiscount ) {
					$bulk_discount_rule_counter++;
					update_post_meta( $new_product_id, '_bulkdiscount_quantity_'.$bulk_discount_rule_counter, $bulkdiscount['quantity'] );
					update_post_meta( $new_product_id, '_bulkdiscount_discount_'.$bulk_discount_rule_counter, $bulkdiscount['discount'] );
				}
				
				if( $bulk_discount_rule_counter < 5 ) {
					for( $bdrc = ($bulk_discount_rule_counter+1); $bdrc <= 5; $bdrc++ ) {
						update_post_meta( $new_product_id, '_bulkdiscount_quantity_'.$bdrc, '' );
						update_post_meta( $new_product_id, '_bulkdiscount_discount_'.$bdrc, '' );
					}
				}
			}
		}
	}
}
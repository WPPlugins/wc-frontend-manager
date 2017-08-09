<?php
/**
 * WCFM plugin core
 *
 * Third Party Plugin Support Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.2.2
 */
 
class WCFM_ThirdParty_Support {

	public function __construct() {
		global $WCFM;
		
		// Product Manage Third Party Plugins View
    add_action( 'end_wcfm_products_manage', array( &$this, 'wcfm_thirdparty_products_manage_views' ), 100 );
    
    // WCFM Menu Filter
    add_filter( 'wcfm_menus', array( &$this, 'wcfm_thirdparty_menus' ), 100 );
    
    // WC Paid Listing Support - 2.3.4
    if( $wcfm_allow_job_package = apply_filters( 'wcfm_is_allow_job_package', true ) ) {
			if ( WCFM_Dependencies::wcfm_wc_paid_listing_active_check() ) {
				// WC Paid Listing Product Type
				add_filter( 'wcfm_product_types', array( &$this, 'wcfm_wcpl_product_types' ), 50 );
				
				// WC Paid Listing Product options
				add_filter( 'wcfm_product_manage_fields_pricing', array( &$this, 'wcfm_wcpl_product_manage_fields_pricing' ), 50, 2 );
			}
		}
		
		// WC Rental & Booking Support - 2.3.8
    if( $wcfm_allow_rental = apply_filters( 'wcfm_is_allow_rental', true ) ) {
			if( WCFM_Dependencies::wcfm_wc_rental_active_check() ) {
				// WC Rental Product Type
				add_filter( 'wcfm_product_types', array( &$this, 'wcfm_wcrental_product_types' ), 80 );
				
				// WC Rental Product options
				add_filter( 'after_wcfm_products_manage_general', array( &$this, 'wcfm_wcrental_product_manage_fields' ), 80, 2 );
			}
		}
	}
	
	/**
   * Product Manage Third Party Plugins views
   */
  function wcfm_thirdparty_products_manage_views( ) {
		global $WCFM;
	  
	 require_once( $WCFM->library->views_path . 'wcfm-view-thirdparty-products-manage.php' );
	}
	
	/**
	 * WCFM Third Party Plugins Menus
	 */
	function wcfm_thirdparty_menus( $menus ) {
  	global $WCFM;
  	
  	// WP Job Manager Menu Item
  	if( $wcfm_allow_job_package = apply_filters( 'wcfm_is_allow_job_package', true ) ) {
			if ( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
				$wcfm_options = get_option( 'wcfm_options' );
				$wc_frontend_manager_associate_listings = ( isset( $wcfm_options['wc_frontend_manager_associate_listings'] ) ) ? $wcfm_options['wc_frontend_manager_associate_listings'] : 'no';
				if( !wcfm_is_vendor() || ( wcfm_is_vendor() && 'no' == $wc_frontend_manager_associate_listings ) ) {
					$jobs_dashboard = get_permalink( get_option( 'job_manager_job_dashboard_page_id' ) );
					$post_a_job = get_permalink ( get_option( 'job_manager_submit_job_form_page_id' ) );
					if( $jobs_dashboard && $post_a_job ) {
						$menus = array_slice($menus, 0, 3, true) +
																array( 'listings' => array(  'label'  => __( 'Listings', 'wc-frontend-manager' ),
																												 'url'     => $jobs_dashboard,
																												 'icon'    => 'briefcase',
																												 'has_new'    => true,
																												 'new_class'  => 'wcfm_sub_menu_items_listings_manage',
																												 'new_url'    => $post_a_job,
																												) )	 +
																	array_slice($menus, 3, count($menus) - 3, true) ;
					}
				}
			}
		}
		
  	return $menus;
  }
  
  /**
   * WC Paid Listing Product Type
   */
  function wcfm_wcpl_product_types( $pro_types ) {
  	global $WCFM;
  	
  	$pro_types['job_package'] = __( 'Job Package', 'wp-job-manager-wc-paid-listings' );
  	
  	return $pro_types;
  }
  
  /**
	 * WC Paid Listing Product General options
	 */
	function wcfm_wcpl_product_manage_fields_pricing( $general_fields, $product_id ) {
		global $WCFM;
		
		$_job_listing_package_subscription_type        = '';
		$_job_listing_limit     = '';
		$_job_listing_duration       = '';
		$_job_listing_featured = 'no';
		
		if( $product_id ) {
			$_job_listing_package_subscription_type        = get_post_meta( $product_id, '_job_listing_package_subscription_type', true );
			$_job_listing_limit     = get_post_meta( $product_id, '_job_listing_limit', true );
			$_job_listing_duration       = get_post_meta( $product_id, '_job_listing_duration', true );
			$_job_listing_featured = get_post_meta( $product_id, '_job_listing_featured', true );
		}
		
		$pos_counter = 4;
		if( WCFM_Dependencies::wcfmu_plugin_active_check() ) $pos_counter = 6;
		
		$general_fields = array_slice($general_fields, 0, $pos_counter, true) +
																	array( 
																				"_job_listing_package_subscription_type" => array( 'label' => __('Subscription Type', 'wp-job-manager-wc-paid-listings' ), 'type' => 'select', 'options' => array( 'package' => __( 'Link the subscription to the package (renew listing limit every subscription term)', 'wp-job-manager-wc-paid-listings' ), 'listing' => __( 'Link the subscription to posted listings (renew posted listings every subscription term)', 'wp-job-manager-wc-paid-listings' ) ), 'class' => 'wcfm-select wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'hints' => __( 'Choose how subscriptions affect this package', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_package_subscription_type ),
																				"_job_listing_limit" => array( 'label' => __('Job listing limit', 'wp-job-manager-wc-paid-listings' ), 'placeholder' => __( 'Unlimited', 'wc-frontend-manager'), 'type' => 'number', 'class' => 'wcfm-text wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'attributes' => array( 'min'   => '', 'step' 	=> '1' ), 'hints' => __( 'The number of job listings a user can post with this package.', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_limit ),
																				"_job_listing_duration" => array( 'label' => __('Job listing duration', 'wp-job-manager-wc-paid-listings' ), 'placeholder' => 0, 'type' => 'number', 'class' => 'wcfm-text wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title wcfm_ele job_package', 'attributes' => array( 'min'   => '', 'step' 	=> '1' ), 'hints' => __( 'The number of days that the job listing will be active.', 'wp-job-manager-wc-paid-listings' ), 'value' => $_job_listing_duration ),
																				"_job_listing_featured" => array( 'label' => __('Feature Listings?', 'wp-job-manager-wc-paid-listings' ), 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele job_package_price_ele job_package', 'label_class' => 'wcfm_title checkbox_title wcfm_ele job_package', 'hints' => __( 'Feature this job listing - it will be styled differently and sticky.', 'wp-job-manager-wc-paid-listings' ), 'value' => 'yes', 'dfvalue' => $_job_listing_featured ),
																				) +
																	array_slice($general_fields, $pos_counter, count($general_fields) - 1, true) ;
		return $general_fields;
	}
	
	/**
   * WC Rental Product Type
   */
  function wcfm_wcrental_product_types( $pro_types ) {
  	global $WCFM;
  	
  	$pro_types['redq_rental'] = __( 'Rental Product', 'wc-frontend-manager' );
  	
  	return $pro_types;
  }
  
  /**
	 * WC Rental Product General options
	 */
	function wcfm_wcrental_product_manage_fields( $product_id = 0, $product_type ) {
		global $WCFM;
		
		$pricing_type = '';
		$hourly_price = '';
		$general_price = '';
		
		$redq_rental_availability = array();
		
		if( $product_id ) {
			$pricing_type = get_post_meta( $product_id, 'pricing_type', true );
			$hourly_price = get_post_meta( $product_id, 'hourly_price', true );
			$general_price = get_post_meta( $product_id, 'general_price', true );
			
			$redq_rental_availability = (array) get_post_meta( $product_id, 'redq_rental_availability', true );
		}
		
		
		?>
		
		<div class="page_collapsible products_manage_redq_rental redq_rental non-variable-subscription" id="wcfm_products_manage_form_redq_rental_head"><label class="fa fa-cab"></label><?php _e('Rental', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container redq_rental non-variable-subscription">
			<div id="wcfm_products_manage_form_redq_rental_expander" class="wcfm-content">
				<?php
				$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_redq_rental_fields', array( 
					"pricing_type" => array( 'label' => __('Set Price Type', 'wc-frontend-manager') , 'type' => 'select', 'options' => apply_filters( 'wcfm_redq_rental_pricing_options', array( 'general_pricing' => __( 'General Pricing', 'wc-frontend-manager' ) ) ), 'class' => 'wcfm-select wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $pricing_type, 'hints' => __( 'Choose a price type - this controls the schema.', 'wc-frontend-manager' ) ),
					"hourly_price" => array( 'label' => __('Hourly Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'value' => $hourly_price, 'hints' => __( 'Hourly price will be applicabe if booking or rental days min 1day', 'wc-frontend-manager' ), 'placeholder' => __( 'Enter price here', 'wc-frontend-manager' ) ),
					"general_price" => array( 'label' => __('General Price', 'wc-frontend-manager') . '(' . get_woocommerce_currency_symbol() . ')' , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele rentel_pricing rental_general_pricing redq_rental', 'label_class' => 'wcfm_title rentel_pricing rental_general_pricing redq_rental', 'value' => $general_price, 'placeholder' => __( 'Enter price here', 'wc-frontend-manager' ) ),
					) ) );
				?>
			</div>
		</div>
		
		<div class="page_collapsible products_manage_redq_rental_availabillity redq_rental non-variable-subscription" id="wcfm_products_manage_form_redq_rental_availabillity_head"><label class="fa fa-clock-o"></label><?php _e('Availability', 'wc-frontend-manager'); ?><span></span></div>
		<div class="wcfm-container redq_rental non-variable-subscription">
			<div id="wcfm_products_manage_form_redq_rental_availabillity_expander" class="wcfm-content">
			<?php
			$WCFM->wcfm_fields->wcfm_generate_form_field( array( 
				"redq_rental_availability" =>   array('label' => __('Product Availabilities', 'wc-frontend-manager') , 'type' => 'multiinput', 'class' => 'wcfm-text wcfm_ele redq_rental', 'label_class' => 'wcfm_title redq_rental', 'desc' => __( 'Please select the date range to be disabled for the product.', 'wc-frontend-manager' ), 'desc_class' => 'avail_rules_desc', 'value' => $redq_rental_availability, 'options' => array(
											"type" => array('label' => __('Type', 'wc-frontend-manager'), 'type' => 'select', 'options' => array( 'custom_date' => __( 'Custom Date', 'wc-frontend-manager' )), 'class' => 'wcfm-select wcfm_ele avail_range_type redq_rental', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label redq_rental' ),
											"from" => array('label' => __('From', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
											"to" => array('label' => __('To', 'wc-frontend-manager'), 'type' => 'text', 'class' => 'wcfm-text wcfm_datepicker avail_rule_field avail_rule_custom avail_rules_ele avail_rules_text', 'label_class' => 'wcfm_title avail_rule_field avail_rule_custom avail_rules_ele avail_rules_label' ),
											"rentable" => array('label' => __('Bookable', 'wc-frontend-manager'), 'type' => 'select', 'options' => array( 'no' => __('NO', 'wc-frontend-manager') ), 'class' => 'wcfm-select wcfm_ele avail_rules_ele avail_rules_text redq_rental', 'label_class' => 'wcfm_title avail_rules_ele avail_rules_label' ),
											)	)
				) );
			?>
		</div>
	</div>
	<?php	
	}
}
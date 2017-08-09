<?php

/**
 * WCFM plugin core
 *
 * Booking WC Booking Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   2.1.0
 */
 
class WCFM_WCBookings {
	
	public function __construct() {
    global $WCFM;
    
    if( wcfm_is_booking() ) {
    	if ( current_user_can( 'manage_bookings' ) ) {
    		// WCFM Query Var Filter
    		add_filter( 'wcfm_query_vars', array( &$this, 'wcb_wcfm_query_vars' ), 20 );
    		add_filter( 'wcfm_endpoint_title', array( &$this, 'wcb_wcfm_endpoint_title' ), 20, 2 );
    		add_action( 'init', array( &$this, 'wcb_wcfm_init' ), 20 );
    		
    		// WCFM Menu Filter
				add_filter( 'wcfm_menus', array( &$this, 'wcb_wcfm_menus' ), 20 );
				
				// Bookable Product Type
				add_filter( 'wcfm_product_types', array( &$this, 'wcb_product_types' ), 20 );
				
				// Booking General Block
				add_action( 'after_wcfm_products_manage_general', array( &$this, 'wcb_product_manage_general' ), 10, 2 );
				
				// Booking Product Manage View
				add_action( 'end_wcfm_products_manage', array( &$this, 'wcb_wcfm_products_manage_form_load_views' ), 20 );
			}
    }
  }
  
  /**
   * WC Booking Query Var
   */
  function wcb_wcfm_query_vars( $query_vars ) {
  	$wcfm_modified_endpoints = (array) get_option( 'wcfm_endpoints' );
  	
		$query_booking_vars = array(
			'wcfm-bookings-dashboard'       => ! empty( $wcfm_modified_endpoints['wcfm-bookings-dashboard'] ) ? $wcfm_modified_endpoints['wcfm-bookings-dashboard'] : 'wcfm-bookings-dashboard',
			'wcfm-bookings'                 => ! empty( $wcfm_modified_endpoints['wcfm-bookings'] ) ? $wcfm_modified_endpoints['wcfm-bookings'] : 'wcfm-bookings',
			'wcfm-bookings-resources'       => ! empty( $wcfm_modified_endpoints['wcfm-bookings-resources'] ) ? $wcfm_modified_endpoints['wcfm-bookings-resources'] : 'wcfm-bookings-resources',
			'wcfm-bookings-resources-manage'=> ! empty( $wcfm_modified_endpoints['wcfm-bookings-resources-manage'] ) ? $wcfm_modified_endpoints['wcfm-bookings-resources-manage'] : 'wcfm-bookings-resources-manage',
			'wcfm-bookings-manual'          => ! empty( $wcfm_modified_endpoints['wcfm-bookings-manual'] ) ? $wcfm_modified_endpoints['wcfm-bookings-manual'] : 'wcfm-bookings-manual',
			'wcfm-bookings-calendar'        => ! empty( $wcfm_modified_endpoints['wcfm-bookings-calendar'] ) ? $wcfm_modified_endpoints['wcfm-bookings-calendar'] : 'wcfm-bookings-calendar',
			'wcfm-bookings-details'         => ! empty( $wcfm_modified_endpoints['wcfm-bookings-details'] ) ? $wcfm_modified_endpoints['wcfm-bookings-details'] : 'wcfm-bookings-details',
			'wcfm-bookings-settings'        => ! empty( $wcfm_modified_endpoints['wcfm-bookings-settings'] ) ? $wcfm_modified_endpoints['wcfm-bookings-settings'] : 'wcfm-bookings-settings',
		);
		
		$query_vars = array_merge( $query_vars, $query_booking_vars );
		
		return $query_vars;
  }
  
  /**
   * WC Booking End Point Title
   */
  function wcb_wcfm_endpoint_title( $title, $endpoint ) {
  	
  	switch ( $endpoint ) {
  		case 'wcfm-bookings-dashboard' :
				$title = __( 'Bookings Dashboard', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings' :
				$title = __( 'Bookings List', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-resources' :
				$title = __( 'Bookings Resources', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-resources-manage' :
				$title = __( 'Bookings Resources Manage', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-manual' :
				$title = __( 'Create Bookings', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-calendar' :
				$title = __( 'Bookings Calendar', 'wc-frontend-manager' );
			break;
			case 'wcfm-bookings-details' :
				$title = sprintf( __( 'Booking Details #%s', 'wc-frontend-manager' ), $wp->query_vars['wcfm-bookings-details'] );
			break;
			case 'wcfm-bookings-settings' :
				$title = __( 'Bookings settings', 'wc-frontend-manager' );
			break;
  	}
  	
  	return $title;
  }
  
  /**
   * WC Booking Endpoint Intialize
   */
  function wcb_wcfm_init() {
  	global $WCFM_Query;
	
		// Intialize WCFM End points
		$WCFM_Query->init_query_vars();
		$WCFM_Query->add_endpoints();
		
		if( !get_option( 'wcfm_updated_end_point_wc_bookings' ) ) {
			// Flush rules after endpoint update
			flush_rewrite_rules();
			update_option( 'wcfm_updated_end_point_wc_bookings', 1 );
		}
  }
  
  /**
   * WC Booking Menu
   */
  function wcb_wcfm_menus( $menus ) {
  	global $WCFM;
  	
  	if ( current_user_can( 'manage_bookings' ) ) {
			$menus = array_slice($menus, 0, 3, true) +
													array( 'wcfm-bookings-dashboard' => array(   'label'  => __( 'Bookings', 'woocommerce-bookings'),
																											 'url'     => get_wcfm_bookings_dashboard_url(),
																											 'icon'    => 'calendar-check-o'
																											) )	 +
														array_slice($menus, 3, count($menus) - 3, true) ;
		}
		
  	return $menus;
  }
  
  /**
   * WC Booking Product Type
   */
  function wcb_product_types( $pro_types ) {
  	global $WCFM;
  	if ( current_user_can( 'manage_bookings' ) ) {
  		$pro_types['booking'] = __( 'Bookable product', 'woocommerce-bookings' );
  	}
  	
  	return $pro_types;
  }
  
  /**
   * WC Booking Product General Options
   */
  function wcb_product_manage_general( $product_id, $product_type ) {
  	global $WCFM, $WCFM;
  	
  	$bookable_product = new WC_Product_Booking( $product_id );
  	
  	$duration_type = $bookable_product->get_duration_type( 'edit' );
		$duration      = $bookable_product->get_duration( 'edit' );
		$duration_unit = $bookable_product->get_duration_unit( 'edit' );
		
		$min_duration = $bookable_product->get_min_duration( 'edit' );
		$max_duration = $bookable_product->get_max_duration( 'edit' );
		$enable_range_picker = $bookable_product->get_enable_range_picker( 'edit' ) ? 'yes' : 'no';
		
		$calendar_display_mode = $bookable_product->get_calendar_display_mode( 'edit' );
		$requires_confirmation = $bookable_product->get_requires_confirmation( 'edit' ) ? 'yes' : 'no';
		
		$user_can_cancel = $bookable_product->get_user_can_cancel( 'edit' ) ? 'yes' : 'no';
		$cancel_limit = $bookable_product->get_cancel_limit( 'edit' );
		$cancel_limit_unit = $bookable_product->get_cancel_limit_unit( 'edit' );
  	?>
  	<!-- collapsible Booking 1 -->
	  <div class="page_collapsible products_manage_downloadable booking" id="wcfm_products_manage_form_downloadable_head"><label class="fa fa-calendar"></label><?php _e('Booking Options', 'woocommerce-bookings'); ?><span></span></div>
		<div class="wcfm-container booking">
			<div id="wcfm_products_manage_form_downloadable_expander" class="wcfm-content">
			  <?php
					$WCFM->wcfm_fields->wcfm_generate_form_field( array(  
						
						"_wc_booking_duration_type" => array('label' => __('Booking Duration', 'woocommerce-bookings') , 'type' => 'select', 'options' => array( 'fixed' => __( 'Fixed blocks of', 'woocommerce-bookings'), 'customer' => __( 'Customer defined blocks of', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $duration_type ),
						"_wc_booking_duration" => array('type' => 'number', 'class' => 'wcfm-text wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $duration ),
						"_wc_booking_duration_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-bookings'), 'day' => __( 'Day(s)', 'woocommerce-bookings' ), 'hour' => __( 'Hour(s)', 'woocommerce-bookings' ), 'minute' => __( 'Minute(s)', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $duration_unit ),
						"_wc_booking_min_duration" => array('label' => __('Minimum duration', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele duration_type_customer_ele booking', 'label_class' => 'wcfm_title duration_type_customer_ele booking', 'value' => $min_duration, 'hints' => __( 'The minimum allowed duration the user can input.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '1' ) ),
						"_wc_booking_max_duration" => array('label' => __('Maximum duration', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele duration_type_customer_ele booking', 'label_class' => 'wcfm_title duration_type_customer_ele booking', 'value' => $max_duration, 'hints' => __( 'The maximum allowed duration the user can input.', 'woocommerce-bookings' ), 'attributes' => array( 'min' => '', 'step' => '1' ) ),
						"_wc_booking_enable_range_picker" => array('label' => __('Enable Calendar Range Picker?', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele duration_type_customer_ele booking', 'label_class' => 'wcfm_title duration_type_customer_ele booking', 'value' => 'yes', 'dfvalue' => $enable_range_picker, 'hints' => __( 'Lets the user select a start and end date on the calendar - duration will be calculated automatically.', 'woocommerce-bookings' ) ),
						"_wc_booking_calendar_display_mode" => array('label' => __('Calendar display mode', 'woocommerce-bookings') , 'type' => 'select', 'options' => array( '' => __( 'Display calendar on click', 'woocommerce-bookings'), 'always_visible' => __( 'Calendar always visible', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele booking', 'label_class' => 'wcfm_title booking', 'value' => $calendar_display_mode ),
						"_wc_booking_requires_confirmation" => array('label' => __('Requires confirmation?', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking', 'label_class' => 'wcfm_title checkbox_title booking', 'value' => 'yes', 'dfvalue' => $requires_confirmation, 'hints' => __( 'Check this box if the booking requires admin approval/confirmation. Payment will not be taken during checkout.', 'woocommerce-bookings' ) ),
						"_wc_booking_user_can_cancel" => array('label' => __('Can be cancelled?', 'woocommerce-bookings') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele booking', 'label_class' => 'wcfm_title checkbox_title booking', 'value' => 'yes', 'dfvalue' => $user_can_cancel, 'hints' => __( 'Check this box if the booking can be cancelled by the customer after it has been purchased. A refund will not be sent automatically.', 'woocommerce-bookings' ) ),
						"_wc_booking_cancel_limit" => array('label' => __('Booking can be cancelled until', 'woocommerce-bookings') , 'type' => 'number', 'class' => 'wcfm-text wcfm_ele can_cancel_ele booking', 'label_class' => 'wcfm_title can_cancel_ele booking', 'value' => $cancel_limit ),
						"_wc_booking_cancel_limit_unit" => array('type' => 'select', 'options' => array( 'month' => __( 'Month(s)', 'woocommerce-bookings'), 'day' => __( 'Day(s)', 'woocommerce-bookings' ), 'hour' => __( 'Hour(s)', 'woocommerce-bookings' ), 'minute' => __( 'Minute(s)', 'woocommerce-bookings' ) ), 'class' => 'wcfm-select wcfm_ele can_cancel_ele booking', 'label_class' => 'wcfm_title can_cancel_ele booking', 'desc_class' => 'can_cancel_ele booking', 'value' => $cancel_limit_unit, 'desc' => __( 'before the start date.', 'woocommerce-bookings' ) )
						
																															) );
			  
			  ?>
		  </div>
		</div>
		<!-- end collapsible Booking -->
		<div class="wcfm_clearfix"></div>
  	<?php
  }
  
  /**
   * WC Booking load views
   */
  function wcb_wcfm_products_manage_form_load_views( ) {
		global $WCFM;
	  
	 require_once( $WCFM->library->views_path . 'wcfm-view-wcbookings-products-manage.php' );
	}
}
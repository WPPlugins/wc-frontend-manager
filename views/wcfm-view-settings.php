<?php
/**
 * WCFM plugin view
 *
 * WCFM Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   1.1.6
 */

global $WCFM;

if( !current_user_can( 'manage_options' ) ) {
	wcfm_restriction_message_show( "Settings" );
	return;
}

$wcfm_options = (array) get_option( 'wcfm_options' );

$is_menu_disabled = isset( $wcfm_options['menu_disabled'] ) ? $wcfm_options['menu_disabled'] : 'no';
$is_headpanel_disabled = isset( $wcfm_options['headpanel_disabled'] ) ? $wcfm_options['headpanel_disabled'] : 'no';
$ultimate_notice_disabled = isset( $wcfm_options['ultimate_notice_disabled'] ) ? $wcfm_options['ultimate_notice_disabled'] : 'no';
$noloader = isset( $wcfm_options['noloader'] ) ? $wcfm_options['noloader'] : 'no';
$logo = ! empty( get_option( 'wcfm_site_logo' ) ) ? get_option( 'wcfm_site_logo' ) : '';
$logo_image_url = wp_get_attachment_image_src( $logo, 'full' );

if ( !empty( $logo_image_url ) ) {
	$logo_image_url = $logo_image_url[0];
}

$wcfm_capability_options = (array) get_option( 'wcfm_capability_options' );

// Product Capabilities
$submit_products = ( isset( $wcfm_capability_options['submit_products'] ) ) ? $wcfm_capability_options['submit_products'] : 'no';
$publish_products = ( isset( $wcfm_capability_options['publish_products'] ) ) ? $wcfm_capability_options['publish_products'] : 'no';
$edit_live_products = ( isset( $wcfm_capability_options['edit_live_products'] ) ) ? $wcfm_capability_options['edit_live_products'] : 'no';
$delete_products = ( isset( $wcfm_capability_options['delete_products'] ) ) ? $wcfm_capability_options['delete_products'] : 'no';

$simple = ( isset( $wcfm_capability_options['simple'] ) ) ? $wcfm_capability_options['simple'] : 'no';
$variable = ( isset( $wcfm_capability_options['variable'] ) ) ? $wcfm_capability_options['variable'] : 'no';
$grouped = ( isset( $wcfm_capability_options['grouped'] ) ) ? $wcfm_capability_options['grouped'] : 'no';
$external = ( isset( $wcfm_capability_options['external'] ) ) ? $wcfm_capability_options['external'] : 'no';
$virtual = ( isset( $wcfm_capability_options['virtual'] ) ) ? $wcfm_capability_options['virtual'] : 'no';
$downloadable = ( isset( $wcfm_capability_options['downloadable'] ) ) ? $wcfm_capability_options['downloadable'] : 'no';
$booking = ( isset( $wcfm_capability_options['booking'] ) ) ? $wcfm_capability_options['booking'] : 'no';
$appointment = ( isset( $wcfm_capability_options['appointment'] ) ) ? $wcfm_capability_options['appointment'] : 'no';
$job_package = ( isset( $wcfm_capability_options['job_package'] ) ) ? $wcfm_capability_options['job_package'] : 'no';
$resume_package = ( isset( $wcfm_capability_options['resume_package'] ) ) ? $wcfm_capability_options['resume_package'] : 'no';
$auction = ( isset( $wcfm_capability_options['auction'] ) ) ? $wcfm_capability_options['auction'] : 'no';
$rental = ( isset( $wcfm_capability_options['rental'] ) ) ? $wcfm_capability_options['rental'] : 'no';
$subscription = ( isset( $wcfm_capability_options['subscription'] ) ) ? $wcfm_capability_options['subscription'] : 'no';
$variable_subscription = ( isset( $wcfm_capability_options['variable-subscription'] ) ) ? $wcfm_capability_options['variable-subscription'] : 'no';

$inventory = ( isset( $wcfm_capability_options['inventory'] ) ) ? $wcfm_capability_options['inventory'] : 'no';
$shipping = ( isset( $wcfm_capability_options['shipping'] ) ) ? $wcfm_capability_options['shipping'] : 'no';
$taxes = ( isset( $wcfm_capability_options['taxes'] ) ) ? $wcfm_capability_options['taxes'] : 'no';
$linked = ( isset( $wcfm_capability_options['linked'] ) ) ? $wcfm_capability_options['linked'] : 'no';
$attributes = ( isset( $wcfm_capability_options['attributes'] ) ) ? $wcfm_capability_options['attributes'] : 'no';
$advanced = ( isset( $wcfm_capability_options['advanced'] ) ) ? $wcfm_capability_options['advanced'] : 'no';

// Miscellaneous Capabilities
$manage_booking = ( isset( $wcfm_capability_options['manage_booking'] ) ) ? $wcfm_capability_options['manage_booking'] : 'no';
$manage_appointment = ( isset( $wcfm_capability_options['manage_appointment'] ) ) ? $wcfm_capability_options['manage_appointment'] : 'no';
$manage_subscription = ( isset( $wcfm_capability_options['manage_subscription'] ) ) ? $wcfm_capability_options['manage_subscription'] : 'no';
$associate_listings = ( isset( $wcfm_capability_options['associate_listings'] ) ) ? $wcfm_capability_options['associate_listings'] : 'no';

$submit_coupons = ( isset( $wcfm_capability_options['submit_coupons'] ) ) ? $wcfm_capability_options['submit_coupons'] : 'no';
$publish_coupons = ( isset( $wcfm_capability_options['publish_coupons'] ) ) ? $wcfm_capability_options['publish_coupons'] : 'no';
$edit_live_coupons = ( isset( $wcfm_capability_options['edit_live_coupons'] ) ) ? $wcfm_capability_options['edit_live_coupons'] : 'no';
$delete_coupons = ( isset( $wcfm_capability_options['delete_coupons'] ) ) ? $wcfm_capability_options['delete_coupons'] : 'no';

$view_orders  = ( isset( $wcfm_capability_options['view_orders'] ) ) ? $wcfm_capability_options['view_orders'] : 'no';
$view_order_details = ( isset( $wcfm_capability_options['view_order_details'] ) ) ? $wcfm_capability_options['view_order_details'] : 'no';
$view_comments  = ( isset( $wcfm_capability_options['view_comments'] ) ) ? $wcfm_capability_options['view_comments'] : 'no';
$submit_comments  = ( isset( $wcfm_capability_options['submit_comments'] ) ) ? $wcfm_capability_options['submit_comments'] : 'no';
$view_email  = ( isset( $wcfm_capability_options['view_email'] ) ) ? $wcfm_capability_options['view_email'] : 'no';
$export_csv  = ( isset( $wcfm_capability_options['export_csv'] ) ) ? $wcfm_capability_options['export_csv'] : 'no';
$pdf_invoice = ( isset( $wcfm_capability_options['pdf_invoice'] ) ) ? $wcfm_capability_options['pdf_invoice'] : 'no';

$view_reports  = ( isset( $wcfm_capability_options['view_reports'] ) ) ? $wcfm_capability_options['view_reports'] : 'no';

$vnd_wpadmin = ( isset( $wcfm_capability_options['vnd_wpadmin'] ) ) ? $wcfm_capability_options['vnd_wpadmin'] : 'no';
$sm_wpadmin = ( isset( $wcfm_capability_options['sm_wpadmin'] ) ) ? $wcfm_capability_options['sm_wpadmin'] : 'no';

$is_marketplece = wcfm_is_marketplace();
?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-cogs"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Settings', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'before_wcfm_settings' ); ?>
		<form id="wcfm_settings_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_settings_form' ); ?>
			
			<?php if( $is_marketplece ) { ?>
				<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_form_vendor_head">
					<label class="fa fa-user-plus"></label>
					<?php _e('Vendors Capability', 'wc-frontend-manager'); ?>
				</div>                                                                            
				<div class="wcfm-container">
					<div id="wcfm_settings_form_vendor_expander" class="wcfm-content">
						<div class="capability_head_message"><?php _e( "Configure what to hide from all vendors", 'wc-frontend-manager' ); ?></div>
					
					  <div class="vendor_capability">
					  	
							<div class="vendor_product_capability">
							  <div class="vendor_capability_heading"><h3><?php _e( 'Products', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_products', array("submit_products" => array('label' => __('Submit Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[submit_products]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $submit_products),
																																																												   "publish_products" => array('label' => __('Publish Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[publish_products]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $publish_products),
																																																												   "edit_live_products" => array('label' => __('Edit Live Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[edit_live_products]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $edit_live_products),
																																																												   "delete_products" => array('label' => __('Delete Products', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[delete_products]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $delete_products)
																													) ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Types', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_product_types', array("simple" => array('label' => __('Simple', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[simple]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $simple),
																																																												        "variable" => array('label' => __('Variable', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[variable]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $variable),
																																																												        "grouped" => array('label' => __('Grouped', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[grouped]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $grouped),
																																																												        "external" => array('label' => __('External / Affiliate', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[external]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $external),
																																																												        "virtual" => array('label' => __('Virtual', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[virtual]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $virtual),
																																																												        "downloadable" => array('label' => __('Downloadable', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[downloadable]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $downloadable),
																																																												        "booking" => array('label' => __('Bookable', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[booking]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $booking),
																																																												        "appointment" => array('label' => __('Appointment', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[appointment]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $appointment),
																																																												        "job_package" => array('label' => __('Job Package', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[job_package]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $job_package),
																																																												        "resume_package" => array('label' => __('Resume Package', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[resume_package]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $resume_package),
																																																												        "auction" => array('label' => __('Auction', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[auction]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $auction),
																																																												        "rental" => array('label' => __('Rental', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[rental]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $rental),
																																																												        "subscription" => array('label' => __('Subscriptions', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[subscription]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $subscription),
																																																												        "variable-subscription" => array('label' => __('Variable Subscriptions', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[variable-subscription]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $variable_subscription)
																													) ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Panels', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_product_panels', array("inventory" => array('label' => __('Inventory', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[inventory]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $inventory),
																																																												         "shipping" => array('label' => __('Shipping', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[shipping]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $shipping),
																																																												         "taxes" => array('label' => __('Taxes', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[taxes]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $taxes),
																																																												         "linked" => array('label' => __('Linked', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[linked]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $linked),
																																																												         "attributes" => array('label' => __('Attributes', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[attributes]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $attributes),
																																																												         "advanced" => array('label' => __('Advanced', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[advanced]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $advanced),
																													) ) );
								
								do_action( 'wcfm_capability_settings_product', $wcfm_capability_options );
								?>
							</div>
							
							<div class="vendor_other_capability">
							  <div class="vendor_capability_heading"><h3><?php _e( 'Miscellaneous', 'wc-frontend-manager' ); ?></h3></div>
							  
							  <?php
							  if( wcfm_is_booking() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_booking', array(  "manage_booking" => array('label' => __('Manage Bookings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_booking]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_booking),
																															) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_booking', array(  "manage_booking" => array('label' => __('Manage Bookings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_booking]', 'type' => 'checkbox', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WC Bookings to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_booking),
																															) ) );
								}
								
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									if( WCFMu_Dependencies::wcfm_wc_appointments_active_check() ) {
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_appointment', array(  "manage_appointment" => array('label' => __('Manage Appointments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_appointment]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_appointment),
																																) ) );
									} else {
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_appointment', array(  "manage_appointment" => array('label' => __('Manage Appointments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_appointment]', 'type' => 'checkbox', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WC Appointments to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_appointment),
																																) ) );
									}
								}
								
								if( wcfm_is_subscription() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_subscription', array(  "manage_subscription" => array('label' => __('Manage Subscriptions', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_subscription]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_subscription),
																															) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_subscription', array(  "manage_subscription" => array('label' => __('Manage Subscriptions', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[manage_subscription]', 'type' => 'checkbox', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WC Subscriptions to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $manage_subscription),
																															) ) );
								}
								
								if( WCFM_Dependencies::wcfm_wp_job_manager_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_listings', array(  "associate_listings" => array('label' => __('Associate Listings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[associate_listings]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'desc' => __( 'by WP Job Manager.', 'wc-frontend-manager' ), 'dfvalue' => $associate_listings),
																															) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_listings', array(  "associate_listings" => array('label' => __('Associate Listings', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[associate_listings]', 'type' => 'checkbox', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WP Job Manager to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $associate_listings),
																															) ) );
								}
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Coupons', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_coupons', array("submit_coupons" => array('label' => __('Submit Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[submit_coupons]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $submit_coupons),
																																																												   "publish_coupons" => array('label' => __('Publish Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[publish_coupons]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $publish_coupons),
																																																												   "edit_live_coupons" => array('label' => __('Edit Live Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[edit_live_coupons]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $edit_live_coupons),
																																																												   "delete_coupons" => array('label' => __('Delete Coupons', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[delete_coupons]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $delete_coupons)
																													) ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Orders', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_orders', array(  "view_orders" => array('label' => __('View Orders', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_orders]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_orders),
																																																													 "view_order_details" => array('label' => __('View Order Details', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_order_details]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_order_details),
																																																												   "view_comments" => array('label' => __('View Comments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_comments]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_comments),
																																																												   "submit_comments" => array('label' => __('Submit Comments', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[submit_comments]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $submit_comments),
																																																												   "view_email" => array('label' => __('Customer Email', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_email]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_email),
																																																												   "export_csv" => array('label' => __('Export CSV', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[export_csv]','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $export_csv),
																														 ) ) );
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_invoice', array(  
																																							 "pdf_invoice" => array('label' => __('PDF Invoice', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[pdf_invoice]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $pdf_invoice),
																																) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_invoice', array(
																																						 "pdf_invoice" => array('label' => __('PDF Invoice', 'wc-frontend-manager'), 'name' => 'wcfm_capability_options[pdf_invoice]', 'type' => 'checkbox', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WCFM Ultimate to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $pdf_invoice),
																															) ) );
								}
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Reports', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_reports', array("view_reports" => array('label' => __('View Reports', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[view_reports]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $view_reports),
																														 ) ) );
								?>
								
								<div class="wcfm_clearfix"></div>
								<div class="vendor_capability_sub_heading"><h3><?php _e( 'Access', 'wc-frontend-manager' ); ?></h3></div>
								
								<?php
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_access', array(  
																																							 "vnd_wpadmin" => array('label' => __('Vendor Backend Access', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[vnd_wpadmin]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $vnd_wpadmin),
																																							 "sm_wpadmin" => array('label' => __('Shop Manager Backend Access', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[sm_wpadmin]', 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $sm_wpadmin),
																																) ) );
								} else {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_vendor_access', array(
																																						 "vnd_wpadmin" => array('label' => __('Vendor Backend Access', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[vnd_wpadmin]', 'type' => 'checkbox', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WCFM Ultimate to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $vnd_wpadmin),
																																						 "sm_wpadmin" => array('label' => __('Shop Manager Backend Access', 'wc-frontend-manager') , 'name' => 'wcfm_capability_options[sm_wpadmin]', 'type' => 'checkbox', 'custom_tags' => array( 'disabled' => 'disabled' ), 'desc' => __( 'Install WCFM Ultimate to enable this feature.', 'wc-frontend-manager' ), 'class' => 'wcfm-checkbox wcfm-checkbox-disabled wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $sm_wpadmin),
																															) ) );
								}
								do_action( 'wcfm_capability_settings_miscellaneous', $wcfm_capability_options );
								?>
							</div>
						</div>
						
						<div class="vendor_advanced_capability">
						  <?php
						  if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
						  	if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						  		wcfmu_feature_help_text_show( __( 'Advanced Capability', 'wc-frontend-manager' ) );
						  	}
							} else {
								do_action( 'wcfm_settings_capability', $wcfm_capability_options );
							}
							?>
						</div>
					</div>
				</div>
				<div class="wcfm_clearfix"></div><br />
				<!-- end collapsible -->
			<?php } ?>
			
			<!-- collapsible -->
			<div class="page_collapsible" id="wcfm_settings_form_style_head">
				<label class="fa fa-image"></label>
				<?php _e('Style', 'wc-frontend-manager'); ?><span></span>
			</div>
			<div class="wcfm-container">
				<div id="wcfm_settings_form_style_expander" class="wcfm-content">
					<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_style', array(
																																															"wcfm_logo" => array('label' => __('Logo', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $logo_image_url ),
																																															"menu_disabled" => array('label' => __('Disabled WCFM Menu', 'wc-frontend-manager') , 'name' => 'menu_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_menu_disabled),
																																															"headpanel_disabled" => array('label' => __('Disabled WCFM Header Panel', 'wc-frontend-manager') , 'name' => 'headpanel_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $is_headpanel_disabled),
																																															"ultimate_notice_disabled" => array('label' => __('Disabled Ultimate Notice', 'wc-frontend-manager') , 'name' => 'ultimate_notice_disabled','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $ultimate_notice_disabled),
																																															//"noloader" => array('label' => __('Disabled WCFM Loader', 'wc-frontend-manager') , 'name' => 'noloader','type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'value' => 'yes', 'label_class' => 'wcfm_title checkbox_title', 'dfvalue' => $noloader),
																																															) ) );
						$color_options = $WCFM->wcfm_color_setting_options();
						$color_options_array = array();
		
						foreach( $color_options as $color_option_key => $color_option ) {
							$color_options_array[$color_option['name']] = array( 'label' => $color_option['label'] , 'type' => 'colorpicker', 'class' => 'wcfm-text wcfm_ele colorpicker', 'label_class' => 'wcfm_title wcfm_ele', 'value' => ( isset($wcfm_options[$color_option['name']]) ) ? $wcfm_options[$color_option['name']] : $color_option['default'] );
						}
						$WCFM->wcfm_fields->wcfm_generate_form_field( $color_options_array );
					?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div>
			<!-- end collapsible -->
			
			<!-- collapsible -->
			<div class="page_collapsible" id="wcfm_settings_form_pages_head">
				<label class="fa fa-newspaper-o"></label>
				<?php _e('WCFM Pages', 'wc-frontend-manager'); ?><span></span>
			</div>
			<div class="wcfm-container">
				<div id="wcfm_settings_form_pages_expander" class="wcfm-content">
					<?php
						$wcfm_page_options = get_option( 'wcfm_page_options' );
						$pages = get_pages(); 
						$pages_array = array();
						$woocommerce_pages = array ( wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
						foreach ( $pages as $page ) {
							if(!in_array($page->ID, $woocommerce_pages)) {
								$pages_array[$page->ID] = $page->post_title;
							}
						}
						
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_settings_fields_pages', array(
																																															"wc_frontend_manager_page_id" => array( 'label' => __('Dashboard', 'wc-frontend-manager'), 'type' => 'select', 'name' => 'wcfm_page_options[wc_frontend_manager_page_id]', 'options' => $pages_array, 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_page_options['wc_frontend_manager_page_id'], 'desc_class' => 'wcfm_page_options_desc', 'desc' => __( 'This page should have shortcode - wc_frontend_manager', 'wc-frontend-manager') )
																																															) ) );
					
						if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
							if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
								wcfmu_feature_help_text_show( __( 'WCFM Endpoints', 'wc-frontend-manager' ) );
							}
						} else {
							do_action( 'wcfm_settings_endpoints' );
						}
					?>
				</div>
			</div>
			<div class="wcfm_clearfix"></div>
			<!-- end collapsible -->
			
			<?php do_action( 'end_wcfm_settings', $wcfm_options ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_settings_submit">
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfmsettings_save_button" class="wcfm_submit_button" />
			</div>
		</form>	
		<?php
		do_action( 'after_wcfm_settings' );
		?>
	</div>
</div>
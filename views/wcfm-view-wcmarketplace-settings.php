<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Marketplace Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   2.2.5
 */

global $WCFM;

$user_id = get_current_user_id();

$shop_name = get_user_meta( $user_id, '_vendor_page_title', true );
$logo_image_url = get_user_meta( $user_id, '_vendor_image', true );
$shop_description = get_user_meta( $user_id, '_vendor_description', true );

$wcfm_vacation_mode = ( get_user_meta( $user_id, 'wcfm_vacation_mode', true ) ) ? get_user_meta( $user_id, 'wcfm_vacation_mode', true ) : 'no';
$wcfm_vacation_mode_msg = get_user_meta( $user_id, 'wcfm_vacation_mode_msg', true );

if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
	$banner_image_url = get_user_meta( $user_id, '_vendor_banner', true );
	$shop_phone = get_user_meta( $user_id, '_vendor_phone', true );
	//$shop_email = get_user_meta( $user_id, '_vendor_email', true );
	
	$addr_1  = get_user_meta( $user_id, '_vendor_address_1', true );
	$addr_2  = get_user_meta( $user_id, '_vendor_address_2', true );
	$country  = get_user_meta( $user_id, '_vendor_country', true );
	$city  = get_user_meta( $user_id, '_vendor_city', true );
	$state  = get_user_meta( $user_id, '_vendor_state', true );
	$zip  = get_user_meta( $user_id, '_vendor_postcode', true );
}

$is_marketplece = wcfm_is_marketplace();

// Policy
$wcmp_policy_settings = get_option("wcmp_general_policies_settings_name");
$wcmp_capabilities_settings_name = get_option("wcmp_general_policies_settings_name");
$can_vendor_edit_policy_tab_label_field = apply_filters('can_vendor_edit_policy_tab_label_field', true);
$can_vendor_edit_cancellation_policy_field = apply_filters('can_vendor_edit_cancellation_policy_field', true);
$can_vendor_edit_refund_policy_field = apply_filters('can_vendor_edit_refund_policy_field', true);
$can_vendor_edit_shipping_policy_field = apply_filters('can_vendor_edit_shipping_policy_field', true);

?>

<div class="collapse wcfm-collapse" id="">
  <div class="wcfm-page-headig">
		<span class="fa fa-cogs"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Settings', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
	  <div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'before_wcfm_wcmarketplace_settings' ); ?>
		<form id="wcfm_settings_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_wcmarketplace_settings_form' ); ?>
			
			  <!-- collapsible - Store -->
				<div class="page_collapsible" id="wcfm_settings_form_vendor_head">
					<label class="fa fa-shopping-bag"></label>
					<?php _e('Store', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_vendor_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcmarketplace_settings_fields_general', array(
																																																"wcfm_logo" => array('label' => __('Logo', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $logo_image_url ),
																																																"shop_name" => array('label' => __('Shop Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $shop_name, 'hints' => __( 'Your shop name is public and must be unique.', 'wc-frontend-manager' ) ),
																																																"shop_description" => array('label' => __('Shop Description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $shop_description, 'hints' => __( 'This is displayed on your shop page.', 'wc-frontend-manager' ) ),
																																																) ) );
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div><br />
				<!-- end collapsible -->
				
				<!-- collapsible - Brand -->
				<?php if( $wcfm_is_allow_brand_settings = apply_filters( 'wcfm_is_allow_brand_settings', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_settings_form_identity_head">
						<label class="fa fa-id-card-o"></label>
						<?php _e('Brand', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_settings_form_identity_expander" class="wcfm-content">
							<?php
								// WC Vendors Pro Settings
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcvendors_settings_fields_identity', array(
																																								"banner" => array('label' => __('Banner', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 250, 'value' => $banner_image_url ),
																																								//"shop_email" => array('label' => __('Shop Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $shop_email, 'hints' => __( 'Your store Email address.', 'wc-frontend-manager' ) ),
																																								"shop_phone" => array('label' => __('Shop Phone', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $shop_phone, 'hints' => __( 'Your store phone no.', 'wc-frontend-manager' ) ),
																																								) ) );
							?>
							
								<div class="wcfm_clearfix"></div>
								<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'Store Address', 'wc-frontend-manager' ); ?></h3></div>
								<div class="store_address">
									<?php
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcvendors_settings_fields_address', array(
																																																			"addr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $addr_1 ),
																																																			"addr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $addr_2 ),
																																																			"country" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $country ),
																																																			"city" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $city ),
																																																			"state" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $state ),
																																																			"zip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $zip ),
																																																			) ) );
									?>
								</div>
							<?php
							} else {
								if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
									wcfmu_feature_help_text_show( __( 'Advanced Settings', 'wc-frontend-manager' ) );
								}
							}
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
				<!-- collapsible - Billing -->
				<?php if( $wcfm_is_allow_billing_settings = apply_filters( 'wcfm_is_allow_billing_settings', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_settings_form_payment_head">
						<label class="fa fa-money"></label>
						<?php _e('Billing', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_settings_form_payment_expander" class="wcfm-content">
							<?php
								if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
										wcfmu_feature_help_text_show( __( 'Billing Details', 'wc-frontend-manager' ) );
									}
								} else {
									do_action( 'wcfm_wcmarketplace_settings_fields', $user_id );
								}
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
				<!-- collapsible - Policies -->
				<?php if( $wcfm_is_allow_policy_settings = apply_filters( 'wcfm_is_allow_policy_settings', true ) ) { ?>
					<?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_capabilities_settings_name['can_vendor_edit_policy_tab_label']) && $can_vendor_edit_policy_tab_label_field && (isset($wcmp_capabilities_settings_name['can_vendor_edit_policy_tab_label']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_cancellation_policy']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_refund_policy']) || isset($wcmp_capabilities_settings_name['can_vendor_edit_shipping_policy']) )) { ?>
						<div class="page_collapsible" id="wcfm_settings_form_policies_head">
							<label class="fa fa-ambulance"></label>
							<?php _e('Policies', 'wc-frontend-manager'); ?><span></span>
						</div>
						<div class="wcfm-container">
							<div id="wcfm_settings_form_policies_expander" class="wcfm-content">
								<?php
								  $vendor_policy_tab_title = get_user_meta( $user_id, '_vendor_policy_tab_title', true ); 
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcmp_settings_fields_policies', array(
																																																														"vendor_policy_tab_title" => array('label' => __('Policy Tab Label', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_policy_tab_title )
																																																													 ) ) );
									
									if ( isset($wcmp_policy_settings['is_shipping_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_shipping_policy']) && $can_vendor_edit_shipping_policy_field) {
										$vendor_shipping_policy = get_user_meta( $user_id, '_vendor_shipping_policy', true ); 
										$vendor_shipping_policy = $vendor_shipping_policy ? $vendor_shipping_policy : $wcmp_policy_settings['shipping_policy'];
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcmp_settings_fields_policies_shipping', array(
																																																														"vendor_shipping_policy" => array('label' => __('Shipping Policy', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_shipping_policy )
																																																													 ) ) );
									}
									
									if (isset($wcmp_policy_settings['is_refund_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_refund_policy']) && $can_vendor_edit_refund_policy_field) {
										$vendor_refund_policy = get_user_meta( $user_id, '_vendor_shipping_policy', true ); 
										$vendor_refund_policy = $vendor_refund_policy ? $vendor_refund_policy : $wcmp_policy_settings['refund_policy'];
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcmp_settings_fields_policies_refund', array(
																																																														"vendor_refund_policy" => array('label' => __('Refund Policy', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_refund_policy )
																																																													 ) ) );
									}
									
									if (isset($wcmp_policy_settings['is_cancellation_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_cancellation_policy']) && $can_vendor_edit_cancellation_policy_field) {
										$vendor_cancellation_policy = get_user_meta( $user_id, '_vendor_cancellation_policy', true ); 
										$vendor_cancellation_policy = $vendor_cancellation_policy ? $vendor_cancellation_policy : $wcmp_policy_settings['cancellation_policy'];
										$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcmp_settings_fields_policies_refund', array(
																																																														"vendor_cancellation_policy" => array('label' => __('Cancellation Policy', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_cancellation_policy )
																																																													 ) ) );
									}
								?>
							</div>
						</div>
						<div class="wcfm_clearfix"></div>
					<?php } ?>
				<?php } ?>
				<!-- end collapsible -->
				
				<!-- collapsible - Vacation -->
				<?php if( $wcfm_is_allow_vacation_settings = apply_filters( 'wcfm_is_allow_vacation_settings', true ) ) { ?>
					<div class="page_collapsible" id="wcfm_settings_form_vacation_head">
						<label class="fa fa-tripadvisor"></label>
						<?php _e('Vacation Mode', 'wc-frontend-manager'); ?><span></span>
					</div>
					<div class="wcfm-container">
						<div id="wcfm_settings_form_vacation_expander" class="wcfm-content">
							<?php
							if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_vacation', array(
																																																													"wcfm_vacation_mode" => array('label' => __('Enable Vacation Mode', 'wc-frontend-manager') , 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => 'yes', 'dfvalue' => $wcfm_vacation_mode ),
																																																													"wcfm_vacation_mode_msg" => array('label' => __('Vacation Message', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $wcfm_vacation_mode_msg )
																																																												 ) ) );
							} else {
								if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
									wcfmu_feature_help_text_show( __( 'Vacation Mode', 'wc-frontend-manager' ) );
								}
							}
							?>
						</div>
					</div>
					<div class="wcfm_clearfix"></div>
				<?php } ?>
				<!-- end collapsible -->
				
				<!-- collapsible - Customer Support -->
				<?php if( $wcfm_is_allow_customer_support_settings = apply_filters( 'wcfm_is_allow_customer_support_settings', true ) ) { ?>
					<?php if (get_wcmp_vendor_settings ('can_vendor_add_customer_support_details', 'general', 'customer_support_details') == 'Enable' && get_wcmp_vendor_settings ('is_customer_support_details', 'general') == 'Enable') { ?>
						<div class="page_collapsible" id="wcfm_settings_form_customer_support_head">
							<label class="fa fa-thumbs-o-up"></label>
							<?php _e('Customer Support', 'wc-frontend-manager'); ?><span></span>
						</div>
						<div class="wcfm-container">
							<div id="wcfm_settings_form_customer_support_expander" class="wcfm-content">
							  <?php
							    $vendor_customer_phone = get_user_meta( $user_id, '_vendor_customer_phone', true );
									$vendor_customer_email = get_user_meta( $user_id, '_vendor_customer_email', true );
									$vendor_csd_return_address1 = get_user_meta( $user_id, '_vendor_csd_return_address1', true );
									$vendor_csd_return_address2 = get_user_meta( $user_id, '_vendor_csd_return_address2', true );
									$vendor_csd_return_country = get_user_meta( $user_id, '_vendor_csd_return_country', true );
									$vendor_csd_return_city = get_user_meta( $user_id, '_vendor_csd_return_city', true );
									$vendor_csd_return_state = get_user_meta( $user_id, '_vendor_csd_return_state', true );
									$vendor_csd_return_zip = get_user_meta( $user_id, '_vendor_csd_return_zip', true );
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcvendors_settings_fields_address', array(
																																																		"vendor_customer_phone" => array('label' => __('Phone', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_customer_phone ),
																																																		"vendor_customer_email" => array('label' => __('Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_customer_email ),
																																																		"vendor_csd_return_address1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_csd_return_address1 ),
																																																		"vendor_csd_return_address2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_csd_return_address2 ),
																																																		"vendor_csd_return_country" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $vendor_csd_return_country ),
																																																		"vendor_csd_return_city" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_csd_return_city ),
																																																		"vendor_csd_return_state" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_csd_return_state ),
																																																		"vendor_csd_return_zip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $vendor_csd_return_zip )
																																																		) ) );
								?>
							</div>
						</div>
						<div class="wcfm_clearfix"></div>
					<?php } ?>
				<?php } ?>
				
				<?php do_action( 'end_wcfm_wcmarketplace_settings', $user_id ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_settings_submit">
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfmsettings_save_button" class="wcfm_submit_button" />
			</div>
			
		</form>
		<?php
		do_action( 'after_wcfm_wcmarketplace_settings' );
		?>
	</div>
</div>
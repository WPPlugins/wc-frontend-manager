<?php
/**
 * WCFM plugin view
 *
 * WCFM WC Vendors Settings View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @version   2.1.1
 */

global $WCFM;

$user_id = get_current_user_id();

$shop_name = get_user_meta( $user_id, 'pv_shop_name', true );
$logo = get_user_meta( $user_id, '_wcv_store_icon_id', true );
$paypal = get_user_meta( $user_id, 'pv_paypal', true );
$seller_info = get_user_meta( $user_id, 'pv_seller_info', true );
$shop_description = get_user_meta( $user_id, 'pv_shop_description', true );
$_wcv_company_url = get_user_meta( $user_id, '_wcv_company_url', true );
$_wcv_store_phone = get_user_meta( $user_id, '_wcv_store_phone', true );

$logo_image_url = wp_get_attachment_image_src( $logo, 'full' );
if ( !empty( $logo_image_url ) ) {
	$logo_image_url = $logo_image_url[0];
}

$wcfm_vacation_mode = ( get_user_meta( $user_id, 'wcfm_vacation_mode', true ) ) ? get_user_meta( $user_id, 'wcfm_vacation_mode', true ) : 'no';
$wcfm_vacation_mode_msg = get_user_meta( $user_id, 'wcfm_vacation_mode_msg', true );

if( WCFM_Dependencies::wcvpro_plugin_active_check() ) {
	if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
		$banner = get_user_meta( $user_id, '_wcv_store_banner_id', true );
		$banner_image_url = wp_get_attachment_image_src( $banner, 'full' );
		if ( !empty( $banner_image_url ) ) {
			$banner_image_url = $banner_image_url[0];
		}
		
		$_wcv_company_url = get_user_meta( $user_id, '_wcv_company_url', true );
		$_wcv_store_phone = get_user_meta( $user_id, '_wcv_store_phone', true );
		
		$addr_1  = get_user_meta( $user_id, '_wcv_store_address1', true );
		$addr_2  = get_user_meta( $user_id, '_wcv_store_address2', true );
		$country  = get_user_meta( $user_id, '_wcv_store_country', true );
		$city  = get_user_meta( $user_id, '_wcv_store_city', true );
		$state  = get_user_meta( $user_id, '_wcv_store_state', true );
		$zip  = get_user_meta( $user_id, '_wcv_store_postcode', true );
		
		$wcv_shipping = get_user_meta( $user_id, '_wcv_shipping', true );
		
		$product_handling_fee = ( isset( $wcv_shipping['product_handling_fee'] ) ) ? $wcv_shipping['product_handling_fee'] : '';
		$max_charge = ( isset( $wcv_shipping['max_charge'] ) ) ? $wcv_shipping['max_charge'] : '';
		$min_charge = ( isset( $wcv_shipping['min_charge'] ) ) ? $wcv_shipping['min_charge'] : '';
		$free_shipping_order = ( isset( $wcv_shipping['free_shipping_order'] ) ) ? $wcv_shipping['free_shipping_order'] : '';
		$max_charge_product = ( isset( $wcv_shipping['max_charge_product'] ) ) ? $wcv_shipping['max_charge_product'] : '';
		$free_shipping_product = ( isset( $wcv_shipping['free_shipping_product'] ) ) ? $wcv_shipping['free_shipping_product'] : '';
		$shipping_policy = ( isset( $wcv_shipping['shipping_policy'] ) ) ? $wcv_shipping['shipping_policy'] : '';
		$return_policy = ( isset( $wcv_shipping['return_policy'] ) ) ? $wcv_shipping['return_policy'] : '';
		
		$saddr_1  = ( isset( $wcv_shipping['shipping_address']['address1'] ) ) ? $wcv_shipping['shipping_address']['address1'] : '';
		$saddr_2  = ( isset( $wcv_shipping['shipping_address']['address2'] ) ) ? $wcv_shipping['shipping_address']['address2'] : '';
		$scountry  = ( isset( $wcv_shipping['shipping_address']['country'] ) ) ? $wcv_shipping['shipping_address']['country'] : '';
		$scity  = ( isset( $wcv_shipping['shipping_address']['city'] ) ) ? $wcv_shipping['shipping_address']['city'] : '';
		$sstate  = ( isset( $wcv_shipping['shipping_address']['state'] ) ) ? $wcv_shipping['shipping_address']['state'] : '';
		$szip  = ( isset( $wcv_shipping['shipping_address']['zip'] ) ) ? $wcv_shipping['shipping_address']['zip'] : '';
	}
}

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
		<?php do_action( 'before_wcfm_wcvendors_settings' ); ?>
		<form id="wcfm_settings_form" class="wcfm">
	
			<?php do_action( 'begin_wcfm_wcvendors_settings_form' ); ?>
			
			<!-- collapsible -->
				<div class="page_collapsible" id="wcfm_settings_form_vendor_head">
					<label class="fa fa-shopping-bag"></label>
				  <?php _e('Store', 'wc-frontend-manager'); ?><span></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_settings_form_store_expander" class="wcfm-content">
						<?php
							$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcvendors_settings_fields_general', array(
																																																"wcfm_logo" => array('label' => __('Logo', 'wc-frontend-manager') , 'type' => 'upload', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title', 'prwidth' => 150, 'value' => $logo_image_url ),
																																																"shop_name" => array('label' => __('Shop Name', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $shop_name, 'hints' => __( 'Your shop name is public and must be unique.', 'wc-frontend-manager' ) ),
																																																"paypal" => array('label' => __('Paypal Email', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $paypal, 'hints' => __( 'Your PayPal address is used to send you your commission.', 'wc-frontend-manager' ) ),
																																																"seller_info" => array('label' => __('Seller Info', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $seller_info, 'hints' => __( 'This is displayed on each of your products.', 'wc-frontend-manager' ) ),
																																																"shop_description" => array('label' => __('Shop Description', 'wc-frontend-manager') , 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $shop_description, 'hints' => __( 'This is displayed on your shop page.', 'wc-frontend-manager' ) ),
																																																) ) );
						?>
					</div>
				</div>
				<div class="wcfm_clearfix"></div><br />
				<!-- end collapsible -->
				
				<!-- collapsible -->
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
				
				<!-- collapsible -->
				<?php if( WCFM_Dependencies::wcvpro_plugin_active_check() ) { ?>
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
																																									"_wcv_company_url" => array('label' => __('Store Website / Blog URL', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $_wcv_company_url, 'hints' => __( 'Your company/blog URL here.', 'wc-frontend-manager' ) ),
																																									"_wcv_store_phone" => array('label' => __('Store Phone', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $_wcv_store_phone, 'hints' => __( 'This is your store contact number.', 'wc-frontend-manager' ) ),
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
										wcfmu_feature_help_text_show( __( 'WCV Pro Settings', 'wc-frontend-manager' ) );
									}
								}
								?>
							</div>
						</div>
						<div class="wcfm_clearfix"></div>
					<?php } ?>
					
					<!-- collapsible -->
					<?php if( $wcfm_is_allow_vshipping_settings = apply_filters( 'wcfm_is_allow_vshipping_settings', true ) ) { ?>
						<div class="page_collapsible" id="wcfm_settings_form_shipping_head">
							<label class="fa fa-truck"></label>
							<?php _e('Shipping', 'wc-frontend-manager'); ?><span></span>
						</div>
						<div class="wcfm-container">
							<div id="wcfm_settings_form_shipping_expander" class="wcfm-content">
								<?php
								// WC Vendors Pro Settings
								if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
									$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_vendors_settings_fields_shipping', array(
																																								"product_handling_fee" => array('label' => __('Product handling fee', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $product_handling_fee, 'hints' => __('The product handling fee, this can be overridden on a per product basis. Amount (5.00) or Percentage (5%).', 'wc-frontend-manager') ),
																																								"max_charge" => array('label' => __('Max Charge Order', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $max_charge, 'hints' => __('The maximum shipping fee charged for an order.', 'wc-frontend-manager') ),
																																								"min_charge" => array('label' => __('Min Charge Order', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $min_charge, 'hints' => __('The minimum shipping fee charged for an order.', 'wc-frontend-manager') ),
																																								"free_shipping_order" => array('label' => __('Free Shipping Order', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $free_shipping_order, 'hints' => __('Free shipping for order spends over this amount. This will override the max shipping charge above.', 'wc-frontend-manager') ),
																																								"max_charge_product" => array('label' => __('Max Charge Product', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $max_charge_product, 'hints' => __('The maximum shipping charged per product no matter the quantity.', 'wc-frontend-manager') ),
																																								"free_shipping_product" => array('label' => __('Free Shipping Product', 'wc-frontend-manager'), 'placeholder' => '0', 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $free_shipping_product, 'hints' => __('Free shipping if the spend per product is over this amount. This will override the max shipping charge above.', 'wc-frontend-manager') ),
																																								"shipping_policy" => array('label' => __('Shipping Policy', 'wc-frontend-manager'), 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $shipping_policy ),
																																								"return_policy" => array('label' => __('Refund Policy', 'wc-frontend-manager'), 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $return_policy ),
																																							 ) ) );
									?>
									<div class="wcfm_clearfix"></div>
									<div class="wcfm_vendor_settings_heading"><h3><?php _e( 'From Address', 'wc-frontend-manager' ); ?></h3></div>
									<div class="store_address">
										<?php
											$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_wcvendors_settings_fields_shipping_address', array(
																																									"saddr_1" => array('label' => __('Address 1', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_1 ),
																																									"saddr_2" => array('label' => __('Address 2', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $saddr_2 ),
																																									"scountry" => array('label' => __('Country', 'wc-frontend-manager') , 'type' => 'country', 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'attributes' => array( 'style' => 'width: 60%;' ), 'value' => $scountry ),
																																									"scity" => array('label' => __('City/Town', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $scity ),
																																									"sstate" => array('label' => __('State/County', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $sstate ),
																																									"szip" => array('label' => __('Postcode/Zip', 'wc-frontend-manager') , 'type' => 'text', 'class' => 'wcfm-text wcfm_ele', 'label_class' => 'wcfm_title wcfm_ele', 'value' => $szip ),
																																									) ) );
										?>
									</div>
									<?php
								} else {
									if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
										wcfmu_feature_help_text_show( __( 'WCV Pro Settings', 'wc-frontend-manager' ) );
									}
								}
								?>
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				<!-- end collapsible -->
				
				<?php do_action( 'end_wcfm_wcvendors_settings', $user_id ); ?>
			
			<div class="wcfm-message" tabindex="-1"></div>
			
			<div id="wcfm_settings_submit">
				<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfmsettings_save_button" class="wcfm_submit_button" />
			</div>
			
		</form>
		<?php
		do_action( 'after_wcfm_wcvendors_settings' );
		?>
	</div>
</div>
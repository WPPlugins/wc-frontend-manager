<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Marketplace Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.2.5
 */

class WCFM_Settings_WCMarketplace_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$user_id = get_current_user_id();
		
		$wcfm_settings_form_data = array();
	  parse_str($_POST['wcfm_settings_form'], $wcfm_settings_form);
	  
	  // sanitize
		$wcfm_settings_form = array_map( 'sanitize_text_field', $wcfm_settings_form );
		$wcfm_settings_form = array_map( 'stripslashes', $wcfm_settings_form );
		
		// sanitize html editor content
		$wcfm_settings_form['shop_description'] = ! empty( $_POST['profile'] ) ? wp_kses_post( stripslashes( $_POST['profile'] ) ) : '';
		update_user_meta( $user_id, '_vendor_description', $wcfm_settings_form['shop_description'] );
		
		$wcfm_setting_store_fields = array( 
																					'_vendor_page_title'          => 'shop_name',
																					'_vendor_image'               => 'wcfm_logo',
																					'_vendor_policy_tab_title'    => 'vendor_policy_tab_title',
																					'_vendor_shipping_policy'     => 'vendor_shipping_policy',
																					'_vendor_refund_policy'       => 'vendor_refund_policy',
																					'_vendor_cancellation_policy' => 'vendor_cancellation_policy',
																					'_vendor_customer_phone'      => 'vendor_customer_phone',
																					'_vendor_customer_email'      => 'vendor_customer_email',
																					'_vendor_csd_return_address1' => 'vendor_csd_return_address1',
																					'_vendor_csd_return_address2' => 'vendor_csd_return_address2',
																					'_vendor_csd_return_country'  => 'vendor_csd_return_country',
																					'_vendor_csd_return_state'    => 'vendor_csd_return_state',
																					'_vendor_csd_return_city'     => 'vendor_csd_return_city',
																					'_vendor_csd_return_zip'      => 'vendor_csd_return_zip'
																			  );
		foreach( $wcfm_setting_store_fields as $wcfm_setting_store_key => $wcfm_setting_store_field ) {
			if( isset( $wcfm_settings_form[$wcfm_setting_store_field] ) ) {
				update_user_meta( $user_id, $wcfm_setting_store_key, $wcfm_settings_form[$wcfm_setting_store_field] );
			}
		}
		
		do_action( 'wcfm_wcmarketplace_settings_update', $user_id, $wcfm_settings_form );
		
		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}
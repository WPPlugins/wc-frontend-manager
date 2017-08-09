<?php
/**
 * WCFM plugin controllers
 *
 * Plugin WC Vendors Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.1.1
 */

class WCFM_Settings_WCVendors_Controller {
	
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
		
		update_user_meta( $user_id, 'pv_shop_name', $wcfm_settings_form['shop_name'] );
		update_user_meta( $user_id, 'pv_paypal', $wcfm_settings_form['paypal'] );
		update_user_meta( $user_id, 'pv_seller_info', $wcfm_settings_form['seller_info'] );
		update_user_meta( $user_id, 'pv_shop_description', $wcfm_settings_form['shop_description'] );
		update_user_meta( $user_id, '_wcv_company_url', $wcfm_settings_form['_wcv_company_url'] );
		update_user_meta( $user_id, '_wcv_store_phone', $wcfm_settings_form['_wcv_store_phone'] );
		
		// Set Vendor Store Logo
		if(isset($wcfm_settings_form['wcfm_logo']) && !empty($wcfm_settings_form['wcfm_logo'])) {
			$wcfm_settings_form['wcfm_logo'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['wcfm_logo']);
		} else {
			$wcfm_settings_form['wcfm_logo'] = '';
		}
		update_user_meta( $user_id, '_wcv_store_icon_id', $wcfm_settings_form['wcfm_logo'] );
		
		do_action( 'wcfm_wcvendors_settings_update', $user_id, $wcfm_settings_form );
		
		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}
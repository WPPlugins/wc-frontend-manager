<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Setings Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.1.6
 */

class WCFM_Settings_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_settings_form_data = array();
	  parse_str($_POST['wcfm_settings_form'], $wcfm_settings_form);
	  
	  $options = get_option( 'wcfm_options' );
	  
	  // Menu Disabled
	  if( !isset($wcfm_settings_form['menu_disabled']) ) $options['menu_disabled'] = 'no';
	  else $options['menu_disabled'] = 'yes';
	  
	  // Header Panel Disabled
	  if( !isset($wcfm_settings_form['headpanel_disabled']) ) $options['headpanel_disabled'] = 'no';
	  else $options['headpanel_disabled'] = 'yes';
	  
	  // Ultimate Notice Disabled
	  if( !isset($wcfm_settings_form['ultimate_notice_disabled']) ) $options['ultimate_notice_disabled'] = 'no';
	  else $options['ultimate_notice_disabled'] = 'yes';
	  
	  // Loader Disabled
	  if( !isset($wcfm_settings_form['noloader']) ) $options['noloader'] = 'no';
	  else $options['noloader'] = 'yes';
	  
	  // Set Site Logo
		if(isset($wcfm_settings_form['wcfm_logo']) && !empty($wcfm_settings_form['wcfm_logo'])) {
			$options['site_logo'] = $WCFM->wcfm_get_attachment_id($wcfm_settings_form['wcfm_logo']);
			update_option( 'wcfm_site_logo', $options['site_logo'] );
		} else {
			update_option( 'wcfm_site_logo', '' );
		}
	  
	  $color_options = $WCFM->wcfm_color_setting_options();
		foreach( $color_options as $color_option_key => $color_option ) {
			if( isset( $wcfm_settings_form[ $color_option['name'] ] ) ) { $options[$color_option['name']] = $wcfm_settings_form[ $color_option['name'] ]; } else { $options[$color_option['name']] = $color_option['default']; }
		}
		
		// Save WCFM page option
		if( isset( $wcfm_settings_form['wcfm_page_options'] ) ) {
			update_option( 'wcfm_page_options', $wcfm_settings_form['wcfm_page_options'] );
		}
		
	  update_option( 'wcfm_options', $options );
	  
	  do_action( 'wcfm_capability_update', $wcfm_settings_form );
	  
	  // Save WCFM capability option
		if( isset( $wcfm_settings_form['wcfm_capability_options'] ) ) {
			update_option( 'wcfm_capability_options', $wcfm_settings_form['wcfm_capability_options'] );
		} else {
			update_option( 'wcfm_capability_options', array() ); 
		}
	  
		if( wcfm_is_marketplace() ) {
			$WCFM->wcfm_vendor_support->vendors_capability_option_updates();
		}
		
		// Init WCFM Custom CSS file
		$wcfm_style_custom = $WCFM->wcfm_create_custom_css();
		 
		$upload_dir      = wp_upload_dir();
		echo '{"status": true, "message": "' . __( 'Settings saved successfully', 'wc-frontend-manager' ) . '", "file": "' . trailingslashit( $upload_dir['baseurl'] ) . '/wcfm/' . $wcfm_style_custom . '"}';
		
		do_action( 'wcfm_settings_update', $wcfm_settings_form );
		 
		die;
	}
}
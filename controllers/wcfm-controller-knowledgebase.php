<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Knowledgebase Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.3.2
 */

class WCFM_Knowledgebase_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$wcfm_knowledgebase = ! empty( $_POST['wcfm_knowledgebase'] ) ? wp_kses_post( stripslashes( $_POST['wcfm_knowledgebase'] ) ) : '';
		
		update_option( 'wcfm_knowledgebase', $wcfm_knowledgebase );
		
		do_action( 'wcfm_knowledgebase_update', $wcfm_knowledgebase );
		
		echo '{"status": true, "message": "' . __( 'Knowledgebase saved successfully', 'wc-frontend-manager' ) . '"}';
		 
		die;
	}
}
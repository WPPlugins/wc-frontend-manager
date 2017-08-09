<?php
/**
 * WCFM plugin core
 *
 * Plugin Ajax Controler
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM_Ajax {
	
	public $controllers_path;

	public function __construct() {
		global $WCFM;
		
		$this->controllers_path = $WCFM->plugin_path . 'controllers/';
		
		add_action( 'wp_ajax_wcfm_ajax_controller', array( &$this, 'wcfm_ajax_controller' ) );
    add_action( 'wp_ajax_nopriv_wcfm_ajax_controller', array( &$this, 'wcfm_ajax_controller' ) );
    
    // Generate Variation Attributes
    add_action('wp_ajax_wcfm_generate_variation_attributes', array( &$this, 'wcfm_generate_variation_attributes' ) );
    add_action('wp_ajax_nopriv_wcfm_generate_variation_attributes', array( &$this, 'wcfm_generate_variation_attributes' ) );
    
    // Product Delete
		add_action( 'wp_ajax_delete_wcfm_product', array( &$this, 'delete_wcfm_product' ) );
    add_action( 'wp_ajax_nopriv_delete_wcfm_product', array( &$this, 'delete_wcfm_product' ) );
    
    // Message Mark as Read
		add_action( 'wp_ajax_wcfm_messages_mark_read', array( &$this, 'wcfm_messages_mark_read' ) );
    add_action( 'wp_ajax_nopriv_wcfm_messages_mark_read', array( &$this, 'wcfm_messages_mark_read' ) );
    
     // Message Auto Refresh Counter
		add_action( 'wp_ajax_wcfm_message_count', array( &$this, 'wcfm_message_count' ) );
    add_action( 'wp_ajax_nopriv_wcfm_message_count', array( &$this, 'wcfm_message_count' ) );
    
  }
  
  public function wcfm_ajax_controller() {
  	global $WCFM;
  	
  	do_action( 'after_wcfm_ajax_controller' );
  	
  	$controller = '';
  	if( isset( $_POST['controller'] ) ) {
  		$controller = $_POST['controller'];
  		
  		switch( $controller ) {
	  	
				case 'wc-products':
				case 'wcfm-products':
					require_once( $this->controllers_path . 'wcfm-controller-products.php' );
					new WCFM_Products_Controller();
			  break;
			  
			  case 'wcfm-products-manage':
			  	if( wcfm_is_booking() ) {
						require_once( $this->controllers_path . 'wcfm-controller-wcbookings-products-manage.php' );
						new WCFM_WCBookings_Products_Manage_Controller();
					}
					// Third Party Plugin Support
					require_once( $this->controllers_path . 'wcfm-controller-thirdparty-products-manage.php' );
					new WCFM_ThirdParty_Products_Manage_Controller();
					
					// Custom Field Plugin Support
					require_once( $this->controllers_path . 'wcfm-controller-customfield-products-manage.php' );
					new WCFM_Custom_Field_Products_Manage_Controller();
					
					require_once( $this->controllers_path . 'wcfm-controller-products-manage.php' );
					new WCFM_Products_Manage_Controller();
					
			  break;
					
			  case 'wcfm-coupons':
					require_once( $this->controllers_path . 'wcfm-controller-coupons.php' );
					new WCFM_Coupons_Controller();
				break;
				
				case 'wcfm-coupons-manage':
					require_once( $this->controllers_path . 'wcfm-controller-coupons-manage.php' );
					new WCFM_Coupons_Manage_Controller();
				break;
				
				case 'wcfm-orders':
					if( $WCFM->is_marketplece && wcfm_is_vendor() ) {
						require_once( $this->controllers_path . 'wcfm-controller-' . $WCFM->is_marketplece . '-orders.php' );
						if( $WCFM->is_marketplece == 'wcvendors' ) new WCFM_Orders_WCVendors_Controller();
						elseif( $WCFM->is_marketplece == 'wcpvendors' ) new WCFM_Orders_WCPVendors_Controller();
						elseif( $WCFM->is_marketplece == 'wcmarketplace' ) new WCFM_Orders_WCMarketplace_Controller();
					} else {
						require_once( $this->controllers_path . 'wcfm-controller-orders.php' );
						new WCFM_Orders_Controller();
					}
				break;
				
				case 'wcfm-reports-out-of-stock':
					require_once( $this->controllers_path . 'wcfm-controller-reports-out-of-stock.php' );
					new WCFM_Reports_Out_Of_Stock_Controller();
				break;
				
				case 'wcfm-profile':
					require_once( $this->controllers_path . 'wcfm-controller-profile.php' );
					new WCFM_Profile_Controller();
				break;
					
				case 'wcfm-settings':
					if( $WCFM->is_marketplece && wcfm_is_vendor() ) {
						require_once( $this->controllers_path . 'wcfm-controller-' . $WCFM->is_marketplece . '-settings.php' );
						if( $WCFM->is_marketplece == 'wcvendors' ) new WCFM_Settings_WCVendors_Controller();
						elseif( $WCFM->is_marketplece == 'wcpvendors' ) new WCFM_Settings_WCPVendors_Controller();
						elseif( $WCFM->is_marketplece == 'wcmarketplace' ) new WCFM_Settings_WCMarketplace_Controller();
					} else {
						require_once( $this->controllers_path . 'wcfm-controller-settings.php' );
						new WCFM_Settings_Controller();
					}
				break;
				
				case 'wcfm-knowledgebase':
					require_once( $this->controllers_path . 'wcfm-controller-knowledgebase.php' );
					new WCFM_Knowledgebase_Controller();
				break;
				
				case 'wcfm-messages':
					require_once( $this->controllers_path . 'wcfm-controller-messages.php' );
					new WCFM_Messages_Controller();
				break;
				
				case 'wcfm-message-sent':
					require_once( $this->controllers_path . 'wcfm-controller-message-sent.php' );
					new WCFM_Message_Sent_Controller();
				break;
			}
  	}
  	
  	do_action( 'before_wcfm_ajax_controller' );
  	die();
  }
  
  public function wcfm_generate_variation_attributes() {
		global $wpdb, $WCFM;
	  
	  $wcfm_products_manage_form_data = array();
	  parse_str($_POST['wcfm_products_manage_form'], $wcfm_products_manage_form_data);
	  //print_r($wcfm_products_manage_form_data);
	  
	  if(isset($wcfm_products_manage_form_data['attributes']) && !empty($wcfm_products_manage_form_data['attributes'])) {
			$pro_attributes = '{';
			$attr_first = true;
			foreach($wcfm_products_manage_form_data['attributes'] as $attributes) {
				if(isset($attributes['is_variation'])) {
					if(!empty($attributes['name']) && !empty($attributes['value'])) {
						if(!$attr_first) $pro_attributes .= ',';
						if($attr_first) $attr_first = false;
						
						if($attributes['is_taxonomy']) {
							$pro_attributes .= '"' . $attributes['tax_name'] . '": {';
							if( !is_array($attributes['value']) ) {
								$att_values = explode("|", $attributes['value']);
								$is_first = true;
								foreach($att_values as $att_value) {
									if(!$is_first) $pro_attributes .= ',';
									if($is_first) $is_first = false;
									$pro_attributes .= '"' . sanitize_title($att_value) . '": "' . trim($att_value) . '"';
								}
							} else {
								$att_values = $attributes['value'];
								$is_first = true;
								foreach($att_values as $att_value) {
									if(!$is_first) $pro_attributes .= ',';
									if($is_first) $is_first = false;
									$att_term = get_term( absint($att_value) );
									if( $att_term ) {
										$pro_attributes .= '"' . $att_term->slug . '": "' . $att_term->name . '"';
									} else {
										$pro_attributes .= '"' . sanitize_title($att_value) . '": "' . trim($att_value) . '"';
									}
								}
							}
							$pro_attributes .= '}';
						} else {
							$pro_attributes .= '"' . $attributes['name'] . '": {';
							$att_values = explode("|", $attributes['value']);
							$is_first = true;
							foreach($att_values as $att_value) {
								if(!$is_first) $pro_attributes .= ',';
								if($is_first) $is_first = false;
								$pro_attributes .= '"' . trim($att_value) . '": "' . trim($att_value) . '"';
							}
							$pro_attributes .= '}';
						}
					}
				}
			}
			$pro_attributes .= '}';
			echo $pro_attributes;
		}
		
		die();
	}
  
  /**
   * Handle Product Delete
   */
  public function delete_wcfm_product() {
  	global $WCFM, $WCFMu;
  	
  	$proid = $_POST['proid'];
		
		if($proid) {
			if(wp_delete_post($proid)) {
				echo 'success';
				die;
			}
			die;
		}
  }
  
  /**
   * Handle Message mark as Read
   *
   * @since 2.3.4
   */
  function wcfm_messages_mark_read() {
  	global $WCFM, $wpdb, $_POST;
  	
  	$messageid = absint( $_POST['messageid'] );
  	$author_id = apply_filters( 'wcfm_message_author', get_current_user_id() );
  	$todate = date('Y-m-d H:i:s');
  	
  	$wcfm_read_message     = "INSERT into {$wpdb->prefix}wcfm_messages_modifier 
																(`message`, `is_read`, `read_by`, `read_on`)
																VALUES
																({$messageid}, 1, {$author_id}, '{$todate}')";
		$wpdb->query($wcfm_read_message);
		
		die;
  }
  
  /**
   * WCFM Message Counter
   *
   * @since 2.3.4
   */
  function wcfm_message_count() {
  	global $WCFM;

		$unread_notice = $WCFM->frontend->unreadMessageCount( 'notice' );
		$unread_message = $WCFM->frontend->unreadMessageCount( 'message' );
		
		echo '{"notice": ' . $unread_notice . ', "message": ' .$unread_message . '}';
		die;
  }
}
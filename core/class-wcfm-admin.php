<?php

/**
 * WCFM Admin Class
 *
 * @version		1.0.0
 * @package		wcfm/core
 * @author 		WC Lovers
 */
class WCFM_Admin {
	
 	public function __construct() {
 		global $WCFM;
 		
 		if ( current_user_can( 'view_woocommerce_reports' ) || current_user_can( 'manage_woocommerce' ) || current_user_can( 'publish_shop_orders' ) ) {
 			// WCFM Dashboard widget
			add_action( 'wp_dashboard_setup', array( &$this, 'wcfm_admin_dashboard_init' ) );
 		
			// WCFM view meta boxes
			add_action( 'add_meta_boxes', array( &$this, 'wcfm_meta_boxes' ), 10, 2 );
			
			// WCFM View @dashboards
			add_action( 'restrict_manage_posts', array( $this, 'wcfm_view_manage_posts' ) );
		}
		
		/**
		 * Register our wcfm_settings_init to the admin_init action hook
		 */
		add_action( 'admin_init', array( &$this, 'wcfm_settings_init' ) );
		
		/**
		 * Register our wcfm_options_page to the admin_menu action hook
		 */
		add_action( 'admin_menu', array( &$this, 'wcfm_options_page' ) );
		
		// WCFM Admin Style
		add_action( 'admin_enqueue_scripts', array( &$this, 'wcfm_admin_script' ), 30 );
	}
	
	/**
	 * Admin dashboard widget init
	 */
	function wcfm_admin_dashboard_init() {
		global $WCFM;
		wp_add_dashboard_widget( 'wcfm_dashboard_status', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_status_widget' ) );
	}
	
	/**
	 * WCFM status widget
	 */
	function wcfm_status_widget() {
		global $wpdb, $WCFM;
		
		do_action('after_wcfm_dashboard_sales_report');
		?>
    <style>
    #sales-piechart {
			background: #fff;
			padding: 12px;
			height: 275px;
			margin: 10px;
		}
		#wcfm-logo {
			text-align: right;
			margin: 10px;
		}
		</style>
		<div class="postbox">
			<a href="<?php echo get_wcfm_page(); ?>">
				<div id="sales-piechart"></div>
				<div id="wcfm-logo"><img src="<?php echo $WCFM->plugin_url; ?>/assets/images/wcfm-30x30.png" alt="WCFM Home" /></div>
			</a>
		</div>
    <?php
	}
	
	/**
	 * Register WCFM Metabox
	 */
	function wcfm_meta_boxes( $post_type, $post ) {
		global $WCFM;
		
		if( in_array( $post_type, array( 'product', 'shop_coupon', 'shop_order' ) ) ) {
			add_meta_box( 'wcfm-view', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_view_metabox' ), 'product', 'side', 'high' );
			add_meta_box( 'wcfm-view', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_view_metabox' ), 'shop_coupon', 'side', 'high' );
			add_meta_box( 'wcfm-view', __( 'WCFM View', 'wc-frontend-manager' ), array( &$this, 'wcfm_view_metabox' ), 'shop_order', 'side', 'high' );
		}
 	}
	
	/**
	 * WCFM View Meta Box
	 */
	function wcfm_view_metabox( $post ) {
		global $WCFM;
		
		$wcfm_url = get_wcfm_page();
		if( $post->ID && $post->post_type ) {
			if( $post->post_type == 'product' ) $wcfm_url = get_wcfm_edit_product_url($post->ID);
			else if( $post->post_type == 'shop_coupon' ) $wcfm_url = get_wcfm_coupons_manage_url($post->ID);
			else if( $post->post_type == 'shop_order' ) $wcfm_url = get_wcfm_view_order_url($post->ID);
		}
		
		echo '<div style="text-align: center;"><a href="' . $wcfm_url . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a></div>';
	}
	
	/**
	 * WCFM View at dashboards
	 */
	function wcfm_view_manage_posts() {
		global $WCFM, $typenow;

		if ( in_array( $typenow, wc_get_order_types( 'order-meta-boxes' ) ) ) {
			echo '<a style="float: right;" href="' . get_wcfm_orders_url() . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
		} elseif ( 'product' == $typenow ) {
			echo '<a style="float: right;" href="' . get_wcfm_products_url() . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
		} elseif ( 'shop_coupon' == $typenow ) {
			echo '<a style="float: right;" href="' . get_wcfm_coupons_url() . '"><img src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
		}
	}
	
	/**
	 * Custom option and settings
	 */
	function wcfm_settings_init() {
		global $WCFM;
		 // register a new setting for "wcfm" page
		 register_setting( 'wcfm', 'wcfm_page_options' );
		 
		 // register a new section in the "wcfm" page
		 add_settings_section(
			 'wcfm_section_developers',
			 __( 'WCFM Page Settings', $WCFM->text_domain ),
			 array( &$this, 'wcfm_section_developers_cb'),
			 'wcfm'
		 );
		 
		 // register a new field in the "wcfm_section_developers" section, inside the "wcfm" page
		 add_settings_field(
			 'wcfm_field_page', 
			 __( 'WCFM Page', $WCFM->text_domain ),
			  array( &$this, 'wcfm_field_page_cb' ),
			 'wcfm',
			 'wcfm_section_developers',
			 [
			 'label_for' => 'wc_frontend_manager_page_id',
			 'class' => 'wcfm_row',
			 'wcfm_custom_data' => 'wc_frontend_manager_page',
			 ]
		 );
		 
	}
	
	/**
	 * custom option and settings:
	 * callback functions
	 */
	function wcfm_section_developers_cb( $args ) {
		global $WCFM;
		
		_e( 'This page should contain "[wc_frontend_manager]" short code', 'wc-frontend-manager' );
	}
	 
	function wcfm_field_page_cb( $args ) {
		global $WCFM;
	  // get the value of the setting we've registered with register_setting()
	  $options = get_option( 'wcfm_page_options' );
	  $pages = get_pages(); 
	  $pages_array = array();
		$woocommerce_pages = array ( wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
		foreach ( $pages as $page ) {
			if(!in_array($page->ID, $woocommerce_pages)) {
				$pages_array[$page->ID] = $page->post_title;
			}
		}
	 // output the field
	 ?>
	 <select id="<?php echo esc_attr( $args['label_for'] ); ?>"
	 data-custom="<?php echo esc_attr( $args['wcfm_custom_data'] ); ?>"
	 name="wcfm_page_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
	 >
	 <?php
	   foreach($pages_array as $p_id => $p_name) {
	   	 ?>
	   	 <option value="<?php echo $p_id; ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $p_id, false ) ) : ( '' ); ?>>
	   	 <?php esc_html_e( $p_name, $WCFM->text_domain ); ?>
	   	 </option>
	   	 <?php
	   }
	 ?>
	 </select>
	 <?php
	}
	
	/**
	 * top level menu
	 */
	function wcfm_options_page() {
		global $WCFM;
		 // add top level menu page
		 add_menu_page(
		 __( 'WC Frontend Manager', $WCFM->text_domain ),
		 __( 'WCFM Options', $WCFM->text_domain ),
		 'manage_options',
		 'wcfm_settings',
		 array( &$this, 'wcfm_options_page_html' )
		 );
	}
 
	/**
	 * top level menu:
	 * callback functions
	 */
	function wcfm_options_page_html() {
		global $WCFM;
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
		 return;
		}
		
		// add error/update messages
		
		if ( isset( $_GET['settings-updated'] ) ) {
		 // add settings saved message with the class of "updated"
		 add_settings_error( 'wcfm_messages', 'wcfm_message', __( 'Settings Saved', $WCFM->text_domain ), 'updated' );
		}
		
		// show error/update messages
		settings_errors( 'wcfm_messages' );
		?>
		<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<form action="options.php" method="post">
		 <?php
		 // output security fields for the registered setting "wcfm"
		 settings_fields( 'wcfm' );
		 // output setting sections and their fields
		 // (sections are registered for "wcfm", each field is registered to a specific section)
		 do_settings_sections( 'wcfm' );
		 // output save settings button
		 submit_button( 'Save Settings' );
		 ?>
		</form>
		</div>
		<?php
  }
	
  function wcfm_admin_script() {
  	global $WCFM;
  	
 	  $screen = get_current_screen(); 
 	 
 	  if ( in_array( $screen->id, array( 'toplevel_page_wcfm_settings' ) ) ) :
 	    $WCFM->library->load_colorpicker_lib();
 	  endif;
 	  
 	  // WC Icon set
	  //wp_enqueue_style( 'wcfm_icon_css',  $WCFM->library->css_lib_url . 'wcfm-style-icon.css', array(), $WCFM->version );
	  
	  // Font Awasome Icon set
	  //wp_enqueue_style( 'wcfm_fa_icon_css',  $WCFM->plugin_url . 'assets/fonts/font-awesome/css/font-awesome.min.css', array(), $WCFM->version );
	  
	  // Admin Bar CSS
	  wp_enqueue_style( 'wcfm_admin_bar_css',  $WCFM->library->css_lib_url . 'wcfm-style-adminbar.css', array(), $WCFM->version );
  }
  
}
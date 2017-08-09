<?php

/**
 * WCFM plugin core
 *
 * Plugin intiate
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM {

	public $plugin_base_name;
	public $plugin_url;
	public $plugin_path;
	public $version;
	public $token;
	public $text_domain;
	public $wcfm_query;
	public $library;
	public $shortcode;
	public $admin;
	public $frontend;
	public $ajax;
	public $non_ajax;
	private $file;
	public $wcfm_fields;
	public $is_marketplece;
	public $wcfm_marketplace;
	public $wcfm_vendor_capability;
	public $wcfm_vendor_support;
	public $wcfm_wcbooking;
	public $wcfm_wccsubscriptions;
	public $wcfm_thirdparty_support;
	public $wcfm_customfield_support;

	public function __construct($file) {

			$this->file = $file;
			$this->plugin_base_name = plugin_basename( $file );
			$this->plugin_url = trailingslashit(plugins_url('', $plugin = $file));
			$this->plugin_path = trailingslashit(dirname($file));
			$this->token = WCFM_TOKEN;
			$this->text_domain = WCFM_TEXT_DOMAIN;
			$this->version = WCFM_VERSION;
			
			// Updation Hook
			add_action( 'init', array( &$this, 'update_wcfm' ) );

			add_action( 'init', array(&$this, 'init' ) );
			
			// WC Vendors shop_order_vendor - register post type fix - since 2.0.4
			add_filter( 'woocommerce_register_post_type_shop_order_vendor', array( &$this, 'wcvendors_register_post_type_shop_order_vendor' ) );
	}
	
	/**
	 * initilize plugin on WP init
	 */
	function init() {
		global $WCFM;
		
		if( !session_id() ) session_start();
		
		// Init Text Domain
		$this->load_plugin_textdomain();
		
		if( ( version_compare( WC_VERSION, '3.0', '<' ) ) ) {
			add_action( 'admin_notices', 'wcfm_woocommerce_version_notice' );
			return;
		}

		// Check Marketplace
		$this->is_marketplece = wcfm_is_marketplace();
		if( $this->is_marketplece ) {
			$this->load_class( 'vendor-support' );
			$this->wcfm_vendor_support = new WCFM_Vendor_Support();
		}
		
		if (!is_admin() || defined('DOING_AJAX')) {
			if( $this->is_marketplece ) {
				if( wcfm_is_vendor()) {
					$this->load_class( 'vendor-capability' );
					$this->wcfm_vendor_capability = new WCFM_Vendor_Capability();
					
					$this->load_class( $this->is_marketplece );
					if( $this->is_marketplece == 'wcvendors' ) $this->wcfm_marketplace = new WCFM_WCVendors();
					elseif( $this->is_marketplece == 'wcmarketplace' ) $this->wcfm_marketplace = new WCFM_WCMarketplace();
					elseif( $this->is_marketplece == 'wcpvendors' ) $this->wcfm_marketplace = new WCFM_WCPVendors();
				}
			}
		}  
		
		// Check WC Booking
		if( wcfm_is_booking() ) {
			$this->load_class('wcbookings');
			$this->wcfm_wcbooking = new WCFM_WCBookings();
		} else {
			delete_option( 'wcfm_updated_end_point_wc_bookings' );
		}
		
		// Check WC Subscription
		if( wcfm_is_subscription() ) {
			$this->load_class('wcsubscriptions');
			$this->wcfm_wcsubscriptions = new WCFM_WCSubscriptions();
		}
		
		// Init library
		$this->load_class( 'library' );
		$this->library = new WCFM_Library();

		// Init ajax
		if ( defined('DOING_AJAX') ) {
			$this->load_class( 'ajax' );
			$this->ajax = new WCFM_Ajax();
		}

		if ( is_admin() ) {
			$this->load_class( 'admin' );
			$this->admin = new WCFM_Admin();
		}

		if ( !is_admin() || defined('DOING_AJAX') ) {
			$this->load_class( 'frontend' );
			$this->frontend = new WCFM_Frontend();
		}
		
		if ( !is_admin() || defined('DOING_AJAX') ) {
			$this->load_class( 'thirdparty-support' );
			$this->wcfm_thirdparty_support = new WCFM_ThirdParty_Support();
		}
		
		if ( !is_admin() || defined('DOING_AJAX') ) {
			$this->load_class( 'customfield-support' );
			$this->wcfm_customfield_support = new WCFM_Custom_Field_Support();
		}
		
		if( !defined('DOING_AJAX') ) {
			$this->load_class( 'non-ajax' );
			$this->non_ajax = new WCFM_Non_Ajax();
		}

		// init shortcode
		$this->load_class( 'shortcode' );
		$this->shortcode = new WCFM_Shortcode();
		
		// WCFM Fields Lib
		$this->wcfm_fields = $this->library->load_wcfm_fields();
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 *
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters('plugin_locale', get_locale(), $this->token);

		load_textdomain($this->text_domain, WP_LANG_DIR . "/wc-frontend-manager/wc-frontend-manager-$locale.mo");
		load_textdomain($this->text_domain, $this->plugin_path . "/lang/wc-frontend-manager-$locale.mo");
	}

	public function load_class($class_name = '') {
		if ('' != $class_name && '' != $this->token) {
			require_once ('class-' . esc_attr($this->token) . '-' . esc_attr($class_name) . '.php');
		} // End If Statement
	}

	// End load_class()
	
	// WCV Shop Vendor 
	function wcvendors_register_post_type_shop_order_vendor( $shop_order_vendor ) {
		$shop_order_vendor['exclude_from_order_reports'] = true;
		return $shop_order_vendor;
	}

	/**
	 * Install upon activation.
	 *
	 * @access public
	 * @return void
	 */
	static function activate_wcfm() {
		global $WCFM;

		require_once ( $WCFM->plugin_path . 'helpers/class-wcfm-install.php' );
		$WCFM_Install = new WCFM_Install();

		update_option('wcfm_installed', 1);
	}
	
	/**
	 * Check upon update.
	 *
	 * @access public
	 * @return void
	 */
	static function update_wcfm() {
		global $WCFM, $WCFM_Query;

		if( !get_option( 'wcfm_updated_2_4_2' ) ) {
			
			require_once ( $WCFM->plugin_path . 'helpers/class-wcfm-install.php' );
			$WCFM_Install = new WCFM_Install();
			
			delete_option( 'wcfm_updated_2_3_8' );
			update_option( 'wcfm_updated_2_4_2', 1 );
		}
	}

	/**
	 * UnInstall upon deactivation.
	 *
	 * @access public
	 * @return void
	 */
	static function deactivate_wcfm() {
		global $WCFM;
		delete_option('wcfm_installed');
	}
	
	function get_wcfm_menus() {
		global $WCFM;
		$wcfm_menus = apply_filters( 'wcfm_menus', array( 'wcfm-products' => array( 'label'  => __( 'Products', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_products_url(),
																																			 'icon'       => 'cubes',
																																			 'has_new'    => true,
																																			 'new_class'  => 'wcfm_sub_menu_items_product_manage',
																																			 'new_url'    => get_wcfm_edit_product_url(),
																																			 'capability' => 'edit_products'
																																			),
																									'wcfm-coupons' => array(  'label'      => __( 'Coupons', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_coupons_url(),
																																			 'icon'       => 'gift',
																																			 'has_new'    => true,
																																			 'new_class'  => 'wcfm_sub_menu_items_coupon_manage',
																																			 'new_url'    => get_wcfm_coupons_manage_url(),
																																			 'capability' => 'edit_shop_coupons'
																																			),
																									'wcfm-orders' => array(  'label'       => __( 'Orders', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_orders_url(),
																																			 'icon'       => 'shopping-cart'
																																			),
																									'wcfm-reports' => array(  'label'      => __( 'Reports', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_reports_url(),
																																			 'icon'       => 'pie-chart'
																																			),
																									'wcfm-settings' => array( 'label'      => __( 'Settings', 'wc-frontend-manager'),
																																			 'url'        => get_wcfm_settings_url(),
																																			 'icon'       => 'cogs'
																																			)
																								)
														);
		
		if ( !wc_coupons_enabled() ) unset( $wcfm_menus['wcfm-coupons'] );
		
		return $wcfm_menus;
	}
	
	function wcfm_color_setting_options() {
		global $WCFM;
		
		$color_options = apply_filters( 'wcfm_color_setting_options', array( 'wcfm_field_menu_bg_color' => array( 'label' => __( 'Menu Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_bg_color_settings', 'default' => '#cccccc', 'element' => '#wcfm_menu', 'style' => 'background' ),
																																				 'wcfm_field_menu_icon_bg_color' => array( 'label' => __( 'Menu Item Background', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_bg_color_settings', 'default' => '#f7f7f7', 'element' => '#wcfm_menu .wcfm_menu_items a.wcfm_menu_item, #wcfm_menu span.wcfm_sub_menu_items', 'style' => 'background' ),
																																				 'wcfm_field_menu_icon_color' => array( 'label' => __( 'Menu Item Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_color_settings', 'default' => '#555', 'element' => '#wcfm_menu .wcfm_menu_item span, #wcfm_menu span.wcfm_sub_menu_items a', 'style' => 'color' ),
																																				 'wcfm_field_menu_icon_active_bg_color' => array( 'label' => __( 'Menu Active Item Background', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_active_bg_color_settings', 'default' => '#00897b', 'element' => '#wcfm_menu .wcfm_menu_items a.active', 'style' => 'background' ),
																																				 'wcfm_field_menu_icon_active_color' => array( 'label' => __( 'Menu Active Item Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_menu_icon_active_color_settings', 'default' => '#fff', 'element' => '#wcfm_menu .wcfm_menu_items a.active span', 'style' => 'color' ),
																																				 'wcfm_field_primary_bg_color' => array( 'label' => __( 'Primary Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_primary_bg_color_settings', 'default' => '#cccccc', 'element' => '.page_collapsible, .collapse-close', 'style' => 'background' ),
																																				 'wcfm_field_primary_font_color' => array( 'label' => __( 'Primary Font Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_primary_font_color_settings', 'default' => '#000000', 'element' => '.page_collapsible, .collapse-close', 'style' => 'color' ),
																																				 'wcfm_field_secondary_bg_color' => array( 'label' => __( 'Secondary Background Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_secondary_bg_color_settings', 'default' => '#000000', 'element' => '.collapse-open', 'style' => 'background' ),
																																				 'wcfm_field_secondary_font_color' => array( 'label' => __( 'Secondary Font Color', 'wc-frontend-manager' ), 'name' => 'wc_frontend_manager_secondary_font_color_settings', 'default' => '#ffffff', 'element' => '.collapse-open', 'style' => 'color' ),
																																			) );
		
		return $color_options;
	}
	
	/**
	 * Create WCFM custom CSS
	 */
	function wcfm_create_custom_css() {
		global $WCFM;
		
		$wcfm_options = get_option('wcfm_options');
		$color_options = $WCFM->wcfm_color_setting_options();
		$custom_color_data = '';
		foreach( $color_options as $color_option_key => $color_option ) {
		  $custom_color_data .= $color_option['element'] . '{ ' . "\n";
			$custom_color_data .= "\t" . $color_option['style'] . ': ';
			if( isset( $wcfm_options[ $color_option['name'] ] ) ) { $custom_color_data .= $wcfm_options[ $color_option['name'] ]; } else { $custom_color_data .= $color_option['default']; }
			$custom_color_data .= ';' . "\n";
			$custom_color_data .= '}' . "\n\n";
		}
		
		$upload_dir      = wp_upload_dir();

		$files = array(
			array(
				'base' 		=> $upload_dir['basedir'] . '/wcfm',
				'file' 		=> 'wcfm-style-custom-' . time() . '.css',
				'content' 	=> $custom_color_data,
			)
		);

		$wcfm_style_custom = get_option( 'wcfm_style_custom' );
		if( file_exists( trailingslashit( $upload_dir['basedir'] ) . '/wcfm/' . $wcfm_style_custom ) ) {
			unlink( trailingslashit( $upload_dir['basedir'] ) . '/wcfm/' . $wcfm_style_custom );
		}
		
		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					$wcfm_style_custom = $file['file'];
					update_option( 'wcfm_style_custom', $file['file'] );
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
		return $wcfm_style_custom;
	}
	
	function wcfm_get_attachment_id($attachment_url) {
		global $wpdb;
		$upload_dir_paths = wp_upload_dir();
		
		if( class_exists('WPH') ) {
			global $wph;
			$new_upload_path = $wph->functions->get_module_item_setting('new_upload_path');
			$new_content_path = $wph->functions->get_module_item_setting('new_content_path');
			$attachment_url = str_replace( $new_content_path, 'wp-content', str_replace( $new_upload_path, 'uploads', $attachment_url ) );
		}
		
		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
			$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
		
			// Remove the upload path base directory from the attachment URL
			$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
			
			// Finally, run a custom database query to get the attachment ID from the modified attachment URL
			$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
		}
		return $attachment_id; 
	}
	
	/** Cache Helpers ******************************************************** */

	/**
	 * Sets a constant preventing some caching plugins from caching a page. Used on dynamic pages
	 *
	 * @access public
	 * @return void
	 */
	function nocache() {
			if (!defined('DONOTCACHEPAGE'))
					define("DONOTCACHEPAGE", "true");
			// WP Super Cache constant
	}

}
<?php

/**
 * WCFM plugin library
 *
 * Plugin intiate library
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM_Library {
	
	public $lib_path;
  
  public $lib_url;
  
  public $php_lib_path;
  
  public $php_lib_url;
  
  public $js_lib_path;
  
  public $js_lib_url;
  
  public $css_lib_path;
  
  public $css_lib_url;
  
  public $views_path;
  
  	/**
	 * Billing fields.
	 *
	 * @var array
	 */
	protected static $billing_fields = array();

	/**
	 * Shipping fields.
	 *
	 * @var array
	 */
	protected static $shipping_fields = array();
	
	public function __construct() {
    global $WCFM;
		
	  $this->lib_path = $WCFM->plugin_path . 'assets/';

    $this->lib_url = $WCFM->plugin_url . 'assets/';
    
    $this->php_lib_path = $this->lib_path . 'php/';
    
    $this->php_lib_url = $this->lib_url . 'php/';
    
    $this->js_lib_path = $this->lib_path . 'js/';
    
    $this->js_lib_url = $this->lib_url . 'js/';
    
    $this->css_lib_path = $this->lib_path . 'css/';
    
    $this->css_lib_url = $this->lib_url . 'css/';
    
    $this->views_path = $WCFM->plugin_path . 'views/';
	}
	
	public function load_scripts( $end_point ) {
	  global $WCFM;
	  
	  // Load Menu JS
	  wp_enqueue_script( 'wcfm_menu_js', $this->js_lib_url . 'wcfm-script-menu.js', array('jquery'), $WCFM->version, true );
	  wp_localize_script( 'wcfm_menu_js', 'wcfm_notification_sound', $this->lib_url . 'sounds/audio_file.mp3' );
	  
	  $noloader = 0;
	  $wcfm_options = get_option('wcfm_options');
	  $noloader = isset( $wcfm_options['noloader'] ) ? $wcfm_options['noloader'] : 'no';
	  wp_localize_script( 'wcfm_menu_js', 'wcfm_noloader', $noloader );
	  
	  do_action( 'before_wcfm_load_scripts', $end_point );
	  
	  switch( $end_point ) {
	  	
	  	case 'wcfm-dashboard':
        $this->load_flot_lib();
        wp_enqueue_script( 'wcfm_dashboard_js', $this->js_lib_url . 'wcfm-script-dashboard.js', array('jquery'), $WCFM->version, true );
      break;
      
	    case 'wcfm-products':
        $this->load_datatable_lib();
        wp_enqueue_script( 'wcfm_products_js', $this->js_lib_url . 'wcfm-script-products.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
      break;
      
      case 'wcfm-products-manage':
      	$this->load_tinymce_lib();
      	$this->load_upload_lib();
      	$this->load_select2_lib();
      	$this->load_datepicker_lib();
      	$this->load_collapsible_lib();
        wp_enqueue_script( 'wcfm_products_manage_js', $this->js_lib_url . 'wcfm-script-products-manage.js', array('jquery', 'select2_js'), $WCFM->version, true );
        
        // WC Booking Support
        if( wcfm_is_booking() ) {
		  		wp_enqueue_script( 'wcfm_wcbookings_products_manage_js', $this->js_lib_url . 'wcfm-script-wcbookings-products-manage.js', array('jquery'), $WCFM->version, true );
		  	}
		  	
		  	// WC Subscription Support
		  	if( wcfm_is_subscription() ) {
		  		wp_enqueue_script( 'wcfm_wcsubscriptions_products_manage_js', $this->js_lib_url . 'wcfm-script-wcsubscriptions-products-manage.js', array('jquery'), $WCFM->version, true );
		  	}
		  	
        // Localized Script
        $wcfm_messages = get_wcfm_products_manager_messages();
			  wp_localize_script( 'wcfm_products_manage_js', 'wcfm_products_manage_messages', $wcfm_messages );
      break;
      
      case 'wcfm-products-export':
      	//wp_register_script( 'wc-product-export', WC()->plugin_url() . '/assets/js/admin/wc-product-export.js', array( 'jquery' ), WC_VERSION );
				//wp_enqueue_script( 'wc-product-export' );
				$this->load_select2_lib();
        wp_enqueue_script( 'wc-product-export', $this->js_lib_url . 'wcfm-script-products-export.js', array('jquery'), $WCFM->version, true );
        wp_localize_script( 'wc-product-export', 'wc_product_export_params', array(
					'export_nonce' => wp_create_nonce( 'wc-product-export' ),
				) );
      break;
        
        
      case 'wcfm-coupons':
        $this->load_datatable_lib();
        wp_enqueue_script( 'wcfm_coupons_js', $this->js_lib_url . 'wcfm-script-coupons.js', array('jquery', 'dataTables_js' ), $WCFM->version, true );
      break;
      
      case 'wcfm-coupons-manage':
      	$this->load_collapsible_lib();
      	$this->load_datepicker_lib();
        wp_enqueue_script( 'wcfm_coupons_manage_js', $this->js_lib_url . 'wcfm-script-coupons-manage.js', array('jquery'), $WCFM->version, true );
        // Localized Script
        $wcfm_messages = get_wcfm_coupons_manage_messages();
			  wp_localize_script( 'wcfm_coupons_manage_js', 'wcfm_coupons_manage_messages', $wcfm_messages );
      break;
      
      case 'wcfm-orders':
        $this->load_datatable_lib();
        wp_enqueue_script( 'wcfm_orders_js', $this->js_lib_url . 'wcfm-script-orders.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
      break;
      
      case 'wcfm-orders-details':
        wp_enqueue_script( 'wcfm_orders_details_js', $this->js_lib_url . 'wcfm-script-orders-details.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-bookings-dashboard':
	    	wp_enqueue_script( 'wcfmu_bookings_dashboard_js', $this->js_lib_url . 'wcfm-script-wcbookings-dashboard.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-reports-sales-by-date':
      	$this->load_flot_lib();
      	$this->load_tiptip_lib();
        wp_enqueue_script( 'wcfm_reports_js', $this->js_lib_url . 'wcfm-script-reports-sales-by-date.js', array('jquery'), $WCFM->version, true );
      break;
      
      case 'wcfm-reports-out-of-stock':
      	$this->load_datatable_lib();
        wp_enqueue_script( 'wcfm_reports_js', $this->js_lib_url . 'wcfm-script-reports-out-of-stock.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
      break;
      
      case 'wcfm-profile':
      	$this->load_select2_lib();
      	$this->load_collapsible_lib();
      	$this->load_tinymce_lib();
      	wp_enqueue_script( 'wcfm_profile_js', $this->js_lib_url . 'wcfm-script-profile.js', array('jquery','select2_js'), $WCFM->version, true );
      break;
      
      case 'wcfm-settings':
      	if( $WCFM->is_marketplece && wcfm_is_vendor() ) {
      		$this->load_tinymce_lib();
      		$this->load_select2_lib();
      	}
      	$this->load_collapsible_lib();
      	$this->load_upload_lib();
				$this->load_colorpicker_lib();
				wp_enqueue_script( 'iris', admin_url('js/iris.min.js'),array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
				wp_enqueue_script( 'wp-color-picker', admin_url('js/color-picker.min.js'), array('iris'), false,1);
				wp_enqueue_script( 'wcfm_settings_js', $this->js_lib_url . 'wcfm-script-settings.js', array('jquery'), $WCFM->version, true );
				
				$colorpicker_l10n = array('clear' => __('Clear'), 'defaultString' => __('Default'), 'pick' => __('Select Color'));
				wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
      break;
      
       case 'wcfm-knowledgebase':
      	$this->load_tinymce_lib();
      	wp_enqueue_script( 'wcfm_knowledgebase_js', $this->js_lib_url . 'wcfm-script-knowledgebase.js', array('jquery'), $WCFM->version, true );
      break;
      
       case 'wcfm-messages':
      	$this->load_tinymce_lib();
      	$this->load_datatable_lib();
      	$this->load_select2_lib();
      	wp_enqueue_script( 'wcfm_messages_js', $this->js_lib_url . 'wcfm-script-messages.js', array('jquery', 'dataTables_js', 'select2_js'), $WCFM->version, true );
      break;
      
      default :
        do_action( 'wcfm_load_scripts', $end_point );
      break;
        
    }
    
    do_action( 'after_wcfm_load_scripts', $end_point );
	}
	
	public function load_styles( $end_point ) {
	  global $WCFM;
	  
	  // Load Menu Style
	  wp_enqueue_style( 'wcfm_menu_css',  $this->css_lib_url . 'wcfm-style-menu.css', array(), $WCFM->version );
	  
	  // Load No-menu style
	  $wcfm_options = get_option('wcfm_options');
	  $is_menu_disabled = isset( $wcfm_options['menu_disabled'] ) ? $wcfm_options['menu_disabled'] : 'no';
	  if( $is_menu_disabled == 'yes' ) {
	  	wp_enqueue_style( 'wcfm_no_menu_css',  $this->css_lib_url . 'wcfm-style-no-menu.css', array('wcfm_menu_css'), $WCFM->version );
	  }
	  
	  do_action( 'before_wcfm_load_styles', $end_point );
	  
	  switch( $end_point ) {
	  	
	  	case 'wcfm-dashboard':
	  		//wp_enqueue_style( 'dashicons' );
		    wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_dashboard_css',  $this->css_lib_url . 'wcfm-style-dashboard.css', array(), $WCFM->version );
		  break;
	  	
	    case 'wcfm-products':
		    wp_enqueue_style( 'wcfm_products_css',  $this->css_lib_url . 'wcfm-style-products.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-products-manage':
		    wp_enqueue_style( 'wcfm_products_manage_css',  $this->css_lib_url . 'wcfm-style-products-manage.css', array(), $WCFM->version );
		    
		    // WC Bookings Support
		    if( wcfm_is_booking() ) {
		  		wp_enqueue_style( 'wcfm_wcbookings_products_manage_css',  $this->css_lib_url . 'wcfm-style-wcbookings-products-manage.css', array(), $WCFM->version );
		  	}
		  	
		  	// WC Subscriptions Support
		    if( wcfm_is_subscription() ) {
		  		wp_enqueue_style( 'wcfm_wcsubscriptions_products_manage_css',  $this->css_lib_url . 'wcfm-style-wcsubscriptions-products-manage.css', array(), $WCFM->version );
		  	}
		  break;
		  
		  case 'wcfm-products-export':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_products_export_css',  $this->css_lib_url . 'wcfm-style-products-export.css', array(), $WCFM->version );
		  break;
		    
		  case 'wcfm-coupons':
		    wp_enqueue_style( 'wcfm_coupons_css',  $this->css_lib_url . 'wcfm-style-coupons.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-coupons-manage':
		    wp_enqueue_style( 'wcfm_coupons_manage_css',  $this->css_lib_url . 'wcfm-style-coupons-manage.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-orders':
		    wp_enqueue_style( 'wcfm_orders_css',  $this->css_lib_url . 'wcfm-style-orders.css', array(), $WCFM->version );
		  break;                                                                                                                                    
		  
		  case 'wcfm-orders-details':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_orders_details_css',  $this->css_lib_url . 'wcfm-style-orders-details.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-bookings-dashboard':
	    	wp_enqueue_style( 'wcfmu_bookings_dashboard_css',  $this->css_lib_url . 'wcfm-style-wcbookings-dashboard.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-reports-sales-by-date':
		  	wp_enqueue_style( 'reports_menus_css',  $this->css_lib_url . 'wcfm-style-reports-menus.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_reports_css',  $this->css_lib_url . 'wcfm-style-reports-sales-by-date.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-reports-out-of-stock':
		  	wp_enqueue_style( 'reports_menus_css',  $this->css_lib_url . 'wcfm-style-reports-menus.css', array(), $WCFM->version );
		    //wp_enqueue_style( 'wcfm_reports_css',  $this->css_lib_url . 'wcfm-style-reports.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-profile':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_profile_css',  $this->css_lib_url . 'wcfm-style-profile.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-settings':
		  	wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
		    wp_enqueue_style( 'wcfm_settings_css',  $this->css_lib_url . 'wcfm-style-settings.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-knowledgebase':
		    wp_enqueue_style( 'wcfm_knowledgebase_css',  $this->css_lib_url . 'wcfm-style-knowledgebase.css', array(), $WCFM->version );
		  break;
		  
		  case 'wcfm-messages':
		    wp_enqueue_style( 'wcfm_messages_css',  $this->css_lib_url . 'wcfm-style-messages.css', array(), $WCFM->version );
		  break;
		  
		  default :
        do_action( 'wcfm_load_styles', $end_point );
      break;
		    
		}
		
		// WCFM Custom CSS
		$upload_dir      = wp_upload_dir();
		$wcfm_style_custom = get_option( 'wcfm_style_custom' );
		if( file_exists( trailingslashit( $upload_dir['basedir'] ) . '/wcfm/' . $wcfm_style_custom ) ) {
			wp_enqueue_style( 'wcfm_custom_css',  trailingslashit( $upload_dir['baseurl'] ) . '/wcfm/' . $wcfm_style_custom, array( 'wcfm_menu_css' ), $WCFM->version );
		}
		
		do_action( 'after_wcfm_load_styles', $end_point );
	}
	
	public function load_views( $end_point, $menu = true ) {
	  global $WCFM;
	  
	  // WCFM Menu
	  if( $menu )
	  	require_once( $this->views_path . 'wcfm-view-menu.php' );
	  
	  do_action( 'before_wcfm_load_views', $end_point );
    
	  switch( $end_point ) {
	  	
	  	case 'wcfm-dashboard':
	  		if( $WCFM->is_marketplece && wcfm_is_vendor() ) {
					require_once( $this->views_path . 'wcfm-view-' . $WCFM->is_marketplece . '-dashboard.php' );
				} else {
					require_once( $this->views_path . 'wcfm-view-dashboard.php' );
				}
      break;
	  	
	    case 'wcfm-products':
        require_once( $this->views_path . 'wcfm-view-products.php' );
      break;
      
      case 'wcfm-products-manage':
        require_once( $this->views_path . 'wcfm-view-products-manage.php' );
      break;
      
      case 'wcfm-products-export':
        require_once( $this->views_path . 'wcfm-view-products-export.php' );
      break;
        
      case 'wcfm-coupons':
        require_once( $this->views_path . 'wcfm-view-coupons.php' );
      break;
      
      case 'wcfm-coupons-manage':
        require_once( $this->views_path . 'wcfm-view-coupons-manage.php' );
      break;
      
      case 'wcfm-orders':
        require_once( $this->views_path . 'wcfm-view-orders.php' );
      break;
      
      case 'wcfm-orders-details':
        require_once( $this->views_path . 'wcfm-view-orders-details.php' );
      break;
      
      case 'wcfm-bookings-dashboard':
        require_once( $this->views_path . 'wcfm-view-wcbookings-dashboard.php' );
      break;
      
      case 'wcfm-reports-sales-by-date':
      	if( $WCFM->is_marketplece && wcfm_is_vendor() ) {
					require_once( $this->views_path . 'wcfm-view-reports-' . $WCFM->is_marketplece . '-sales-by-date.php' );
				} else {
					require_once( $this->views_path . 'wcfm-view-reports-sales-by-date.php' );
				}
      break;
      
      case 'wcfm-reports-out-of-stock':
        require_once( $this->views_path . 'wcfm-view-reports-out-of-stock.php' );
      break;
      
      case 'wcfm-profile':
        require_once( $this->views_path . 'wcfm-view-profile.php' );
      break;
      
      case 'wcfm-settings':
      	if( $WCFM->is_marketplece && wcfm_is_vendor() ) {
					require_once( $this->views_path . 'wcfm-view-' . $WCFM->is_marketplece . '-settings.php' );
				} else {
					require_once( $this->views_path . 'wcfm-view-settings.php' );
				}
      break;
      
      case 'wcfm-knowledgebase':
        require_once( $this->views_path . 'wcfm-view-knowledgebase.php' );
      break;
      
      case 'wcfm-messages':
        require_once( $this->views_path . 'wcfm-view-messages.php' );
      break;
      
      default :
        do_action( 'wcfm_load_views', $end_point );
      break;
        
    }
    
    do_action( 'after_wcfm_load_views', $end_point );
	}
	
	/**
	 * PHP WCFM fields Library
	*/
	public function load_wcfm_fields() {
	  global $WCFM;
	  require_once ( $WCFM->plugin_path . 'includes/libs/php/class-wcfm-fields.php');
	  $WCFM_Fields = new WCFM_Fields(); 
	  return $WCFM_Fields;
	}
	
	/**
	 * Jquery dataTable library
	 */
	function load_datatable_lib() {
		global $WCFM;
		
		// JS
		wp_enqueue_script( 'dataTables_js', '//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js', array('jquery'), $WCFM->version, true );
		wp_enqueue_script( 'dataTables_responsive_js', '//cdn.datatables.net/responsive/2.1.1/js/dataTables.responsive.min.js', array('jquery', 'dataTables_js'), $WCFM->version, true );
		
		$dataTables_language = '{"processing": "' . __('Processing...', 'wc-frontend-manager' ) . '" , "search": "' . __('Search:', 'wc-frontend-manager' ) . '", "lengthMenu": "' . __('Show _MENU_ entries', 'wc-frontend-manager' ) . '", "info": " ' . __('Showing _START_ to _END_ of _TOTAL_ entries', 'wc-frontend-manager' ) . '", "infoEmpty": "' . __('Showing 0 to 0 of 0 entries', 'wc-frontend-manager' ) . '", "infoFiltered": "' . __('(filtered _MAX_ entries of total)', 'wc-frontend-manager' ) . '", "loadingRecords": "' . __('Loading...', 'wc-frontend-manager' ) . '", "zeroRecords": "' . __('No matching records found', 'wc-frontend-manager' ) . '", "emptyTable": "' . __('No data in the table', 'wc-frontend-manager' ) . '", "paginate": {"first": "' . __('First', 'wc-frontend-manager' ) . '", "previous": "' . __('Previous', 'wc-frontend-manager' ) . '", "next": "' . __('Next', 'wc-frontend-manager' ) . '", "last": "' .  __('Last', 'wc-frontend-manager') . '"}}';
		wp_localize_script( 'dataTables_js', 'dataTables_language', $dataTables_language );
		
		// CSS
		wp_enqueue_style( 'dataTables_css',  '//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css', array(), $WCFM->version );
		wp_enqueue_style( 'dataTables_responsive_css',  '//cdn.datatables.net/responsive/2.1.1/css/responsive.dataTables.min.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery TinyMCE library
	 */
	public function load_tinymce_lib() {
	  global $WCFM;
	  wp_enqueue_script('tinymce_js', '//cdnjs.cloudflare.com/ajax/libs/tinymce/4.5.6/tinymce.min.js', array('jquery'), $WCFM->version, true);
	  wp_enqueue_script('jquery_tinymce_js', '//cdnjs.cloudflare.com/ajax/libs/tinymce/4.5.6/jquery.tinymce.min.js', array('jquery'), $WCFM->version, true);
	}
	
	/**
	 * Jquery qTip library
	*/
	public function load_qtip_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'wcfm_qtip_js', $WCFM->plugin_url . 'includes/libs/qtip/qtip.js', array('jquery'), $WCFM->version, true );
		wp_enqueue_style( 'wcfm_qtip_css',  $WCFM->plugin_url . 'includes/libs/qtip/qtip.css', array(), $WCFM->version );
	}
	
	/**
	 * WP Media library
	*/
	public function load_upload_lib() {
	  global $WCFM;
	  wp_enqueue_media();
	  wp_enqueue_script( 'upload_js', $WCFM->plugin_url . 'includes/libs/upload/media-upload.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_style( 'upload_css',  $WCFM->plugin_url . 'includes/libs/upload/media-upload.css', array(), $WCFM->version );
	}
	
	/**
	 * WP ColorPicker library
	*/
	public function load_colorpicker_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_script( 'colorpicker_init', $WCFM->plugin_url . 'includes/libs/colorpicker/colorpicker.js', array( 'jquery', 'wp-color-picker' ), $WCFM->version, true );
    wp_enqueue_style( 'wp-color-picker' );
	}
	
	/**
	 * Select2 library
	*/
	public function load_select2_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'select2_js', $WCFM->plugin_url . 'includes/libs/select2/select2.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_style( 'select2_css',  $WCFM->plugin_url . 'includes/libs/select2/select2.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery Accordian library
	 */
	public function load_collapsible_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'collapsible_js', $this->js_lib_url . 'jquery.collapsiblepanel.js', array('jquery'), $WCFM->version, true );
	  //wp_enqueue_script( 'collapsible_cookie_js', $this->js_lib_url . 'jquery.cookie.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_style( 'collapsible_css',  $this->css_lib_url . 'wcfm-style-collapsible.css', array(), $WCFM->version );
	}
	
	/**
	 * WP DatePicker library
	*/
	public function load_datepicker_lib() {
	  global $WCFM;
	  wp_enqueue_script( 'jquery-ui-datepicker' );
	  wp_enqueue_style( 'jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css', array(), $WCFM->version );
	}
	
	/**
	 * Jquery Flot library
	*/
	public function load_flot_lib() {
	  global $WCFM;
	  
	  wp_enqueue_script( 'jquery-flot_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.min.js', array('jquery'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-resize_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.resize.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-timme_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.time.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-pie_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.pie.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	  wp_enqueue_script( 'jquery-flot-stack_js', $WCFM->plugin_url . 'includes/libs/jquery-flot/jquery.flot.stack.min.js', array('jquery', 'jquery-flot_js'), $WCFM->version, true );
	}
	
	/**
	 * Jquery tiptip library
	*/
	public function load_tiptip_lib() {
	  global $WCFM;
	  
	  wp_enqueue_script( 'jquery-tip_js', $WCFM->plugin_url . 'includes/libs/jquery-tiptip/jquery.tipTip.min.js', array('jquery'), $WCFM->version, true );
	}
	
	public static function init_address_fields() {

		self::$billing_fields = apply_filters( 'woocommerce_admin_billing_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'class'   => 'js_field-country select short',
				'type'    => 'select',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
			),
			'email' => array(
				'label' => __( 'Email', 'woocommerce' ),
			),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
			),
		) );

		self::$shipping_fields = apply_filters( 'woocommerce_admin_shipping_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'type'    => 'select',
				'class'   => 'js_field-country select short',
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_shipping_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',
				'show'  => false
			),
		) );
	}
	
	/**
	 * Get sales report data.
	 * @return object
	 */
	private function get_sales_report_data() {
		include_once( dirname( WC_PLUGIN_FILE ) . '/includes/admin/reports/class-wc-report-sales-by-date.php' );

		$sales_by_date                 = new WC_Report_Sales_By_Date();
		$sales_by_date->start_date     = strtotime( date( 'Y-m-01', current_time( 'timestamp' ) ) );
		$sales_by_date->end_date       = current_time( 'timestamp' );
		$sales_by_date->chart_groupby  = 'day';
		$sales_by_date->group_by_query = 'YEAR(posts.post_date), MONTH(posts.post_date), DAY(posts.post_date)';

		return $sales_by_date->get_report_data();
	}
	
	/**
	 * Get top seller from DB.
	 * @return object
	 */
	private function get_top_seller() {
		global $wpdb;

		$query            = array();
		$query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id
			FROM {$wpdb->posts} as posts";
		$query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
		$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
		$query['where']   = "WHERE posts.post_type IN ( 'shop_order','shop_order_refund' ) ";
		$query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'woocommerce_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "' ) ";
		$query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
		$query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
		$query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-d', strtotime( '-7 DAY', current_time( 'timestamp' ) ) ) . "' ";
		$query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
		$query['groupby'] = "GROUP BY product_id";
		$query['orderby'] = "ORDER BY qty DESC";
		$query['limits']  = "LIMIT 1";
		
		return $wpdb->get_row( implode( ' ', apply_filters( 'woocommerce_dashboard_status_widget_top_seller_query', $query ) ) );
	}
	
	/**
	 * Sort an array by 'title'
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return array
	 */
	public function sort_by_title( array $a, array $b ) {
		return strcasecmp( $a[ 'title' ], $b[ 'title' ] );
	}
	
	// Generate Taxonomy HTML
	function generateTaxonomyHTML( $taxonomy, $product_categories, $categories, $nbsp = '' ) {
		global $WCFM;
		
		foreach ( $product_categories as $cat ) {
			echo '<option value="' . esc_attr( $cat->term_id ) . '"' . selected( in_array( $cat->term_id, $categories ), true, false ) . '>' . $nbsp . esc_html( $cat->name ) . '</option>';
			$product_child_categories   = get_terms( $taxonomy, 'orderby=name&hide_empty=0&parent=' . absint( $cat->term_id ) );
			if ( $product_child_categories ) {
				$this->generateTaxonomyHTML( $taxonomy, $product_child_categories, $categories, $nbsp . '&nbsp;&nbsp;' );
			}
		}
	}
}
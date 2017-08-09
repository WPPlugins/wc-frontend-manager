<?php

/**
 * WCFM Frontend Class
 *
 * @version		1.0.0
 * @package		wcfm/core
 * @author 		WC Lovers
 */
class WCFM_Frontend {
	
 	public function __construct() {
 		
 		// WCFM End Point Title
 		add_filter( 'the_title', array( &$this, 'wcfm_page_endpoint_title' ) );
 		
 		// Check User Authentication to Access WCFM Pages
 		add_action( 'template_redirect', array(&$this, 'wcfm_template_redirect' ));
 		
 		// WCFM Icon at Shop
 		add_action( 'woocommerce_before_shop_loop', array( &$this, 'wcfm_home' ), 5 );
 		add_action( 'woocommerce_after_shop_loop', array( &$this, 'wcfm_home' ), 5 );
 		
 		// Product Manage from Archive Pages
 		add_action( 'woocommerce_before_shop_loop_item', array(&$this, 'wcfm_product_manage'), 5 );
		add_action( 'woocommerce_before_single_product_summary', array(&$this, 'wcfm_product_manage'), 5 );
		
		// WCFM Page Header panels
    add_action( 'wcfm_page_heading', array($this, 'wcfm_page_heading'), 10 );
    
    // WCFM Analytics Data Save - Version 2.2.5
    add_action( 'wp_footer', array( &$this, 'wcfm_save_page_analytics_data') );
    
    // WCFM Ultimate Inactive Notice
    add_filter( 'is_wcfmu_inactive_notice_show', array( &$this, 'is_wcfmu_inactive_notice_show') );
		
		//enqueue scripts
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_scripts'));
		//enqueue styles
		add_action('wp_enqueue_scripts', array(&$this, 'wcfm_styles'));
		
 	}
 	
 	/**
	 * Replace a page title with the endpoint title.
	 * @param  string $title
	 * @return string
	 */
	function wcfm_page_endpoint_title( $title ) {
		global $WCFM, $WCFM_Query, $wp_query;
	
		if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && is_wcfm_endpoint_url() ) {
			$endpoint = $WCFM_Query->get_current_endpoint();
	
			if ( $endpoint_title = $WCFM_Query->get_endpoint_title( $endpoint ) ) {
				$title = $endpoint_title;
			}
	
			remove_filter( 'the_title', array( &$this, 'wcfm_page_endpoint_title' ) );
		}
	
		return $title;
	}
	
	/**
	 * Template redirect function
	 * @return void
	*/
	function wcfm_template_redirect() {
		global $WCFM;
		
		// If user not loggedin then reirect to Home page
		if( !is_user_logged_in() && is_wcfm_page() ) {
      wp_safe_redirect( get_permalink( wc_get_page_id( 'myaccount' ) ) );
      exit();
    }
    
    // If user loggedin but not admin or shop manager then reirect to MyAccount page
		if( is_user_logged_in() && is_wcfm_page() ) {
			$user = wp_get_current_user();
			$allowed_roles = apply_filters( 'wcfm_allwoed_user_rols',  array( 'administrator', 'shop_manager' ) );
			if ( !array_intersect( $allowed_roles, (array) $user->roles ) )  {
				wp_safe_redirect(  get_permalink( wc_get_page_id( 'myaccount' ) ) );
				exit();
			}
		}
	}
	
	/**
	 * WCFM Home at Archive Pages
	 */
	function wcfm_home() {
 		global $WCFM;
 		
 		if( !is_user_logged_in() ) return;
		$user = wp_get_current_user();
		$allowed_roles = apply_filters( 'wcfm_allwoed_user_rols',  array( 'administrator', 'shop_manager' ) );
		if ( !array_intersect( $allowed_roles, (array) $user->roles ) )  return;
		
 		echo '<a href="' . get_wcfm_page() . '"><img class="text_tip" data-tip="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" id="wcfm_home" src="' . $WCFM->plugin_url . '/assets/images/wcfm-30x30.png" alt="' . __( 'WCFM Home', 'wc-frontend-manager' ) . '" /></a>';
 	}
	
	/**
	 * WCFM Product Manage from Archive Pages
	 */
	function wcfm_product_manage() {
		global $WCFM, $post, $woocommerce_loop;

		if( !is_user_logged_in() ) return;
		$user = wp_get_current_user();
		$allowed_roles = apply_filters( 'wcfm_allwoed_user_rols',  array( 'administrator', 'shop_manager' ) );
		if ( !array_intersect( $allowed_roles, (array) $user->roles ) )  return;
				
		if( wcfm_is_vendor() && ( get_current_user_id() != $post->post_author ) ) return;
		
		$pro_id = $post->ID;
		$_product = wc_get_product($pro_id);
		
		?>
		<div class="wcfm_buttons">
		  <?php do_action( 'wcfm_product_manage', $pro_id, $_product ); ?>
		  <?php if( current_user_can( 'edit_published_products' ) ) { ?>
				<a class="wcfm_button" href="<?php echo get_wcfm_edit_product_url( $pro_id, $_product ); ?>"> <span class="fa fa-edit text_tip" data-tip="<?php echo esc_attr__( 'Edit', 'wc-frontend-manager' ); ?>"></span> </a>
		  <?php } ?>
		  <?php if( current_user_can( 'delete_published_products' ) ) { ?>
		  	<span class="wcfm_button_separator">|</span>
		  	<a class="wcfm_button wcfm_delete_product" href="#" data-proid="<?php echo $pro_id; ?>"><span class="fa fa-trash-o text_tip" data-tip="<?php echo esc_attr__( 'Delete', 'wc-frontend-manager' ); ?>"></span> </a>
		  <?php } ?>
		</div>
		<?php
		
	}
	
	/**
	 * WCFM Pages Header Panels
	 * 
	 * @since 2.3.2
	 */
	function wcfm_page_heading() {
		global $WCFM, $wpdb;
		require_once( $WCFM->library->views_path . 'wcfm-view-header-panels.php' );
	}
	
	/**
	 * WCFM unread message count
	 *
	 * @since 2.3.4
	 */
	function unreadMessageCount( $message_type = 'notice' ) {
		global $WCFM, $wpdb;
		
		$sql = 'SELECT COUNT(wcfm_messages.ID) FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
		$sql .= ' WHERE 1=1';
		
		if( $message_type == 'notice' ) {
			$status_filter = " AND `is_notice` = 1";
		} elseif( $message_type == 'message' ) {
			$status_filter = " AND `is_direct_message` = 1";
		}
		$sql .= $status_filter;
		
		$message_to = apply_filters( 'wcfm_message_author', get_current_user_id() );
		if( wcfm_is_vendor() ) { 
			//$vendor_filter = " AND `author_is_admin` = 1";
			$vendor_filter = " AND ( `author_id` = {$message_to} OR `message_to` = -1 OR `message_to` = {$message_to} )";
			$sql .= $vendor_filter;
		}
		
		$message_status_filter = " AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}wcfm_messages_modifier as wcfm_messages_modifier_2 WHERE wcfm_messages.ID = wcfm_messages_modifier_2.message AND wcfm_messages_modifier_2.read_by={$message_to})";
		$sql .= $message_status_filter;
		
		$total_mesaages = $wpdb->get_var( $sql );
		
		return  $total_mesaages;
	}
	
	/**
	 * Saving WCFM Page Analytics Data
	 * @since 2.2.5
	 */
	function wcfm_save_page_analytics_data() {
		global $WCFM, $_SERVER, $post, $wpdb, $_SESSION;
		
		//$_SESSION['wcfm_pages'] = array( 'shop' => 'no', 'stores' => array(), 'products' => array() );
		
		$todate = date('Y-m-d');
		
		if( !isset($_SERVER['HTTP_REFERER']) ) $_SERVER['HTTP_REFERER'] = '';
		
		// vendor store
		$is_marketplece = wcfm_is_marketplace();
		
		if( is_shop() ) {
			$wc_shop = true;
			if( $is_marketplece == 'wcvendors' ) {
		  	if ( WCV_Vendors::is_vendor_page() ) {
		  		$wc_shop = false;
		  		$vendor_shop 		= urldecode( get_query_var( 'vendor_shop' ) );
		  		$vendor_id   		= WCV_Vendors::get_vendor_id( $vendor_shop );
		  		if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['stores'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['stores'] ) && !in_array( $vendor_id, $_SESSION['wcfm_pages']['stores'] ) ) ) {
						// wcfm_detailed_analysis Query
						$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_id}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}')";
						$wpdb->query($wcfm_detailed_analysis);
						
						// wcfm_daily_analysis Query
						$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_id}, 1, '{$todate}')
																			ON DUPLICATE KEY UPDATE
																			count = count+1";
						$wpdb->query($wcfm_daily_analysis);
						
						// Session store
						$_SESSION['wcfm_pages']['stores'][] = $vendor_id;
					}
		  	}
		  }
		  
		  if( $wc_shop ) {
		  	if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['shop'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['shop'] ) && ( 'no' == $_SESSION['wcfm_pages']['shop'] ) ) ) {
					// wcfm_detailed_analysis Query
					$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																		(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`)
																		VALUES
																		(1, 0, 0, 0, {$post->post_author}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}')";
					$wpdb->query($wcfm_detailed_analysis);
					
					// wcfm_daily_analysis Query
					$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																		(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																		VALUES
																		(1, 0, 0, 0, {$post->post_author}, 1, '{$todate}')
																		ON DUPLICATE KEY UPDATE
																		count = count+1";
					$wpdb->query($wcfm_daily_analysis);
					
					// Session store
					$_SESSION['wcfm_pages']['shop'] = 'yes';
				}
			}
		} elseif( is_product() ) {
			if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['products'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['products'] ) && !in_array( $post->ID, $_SESSION['wcfm_pages']['products'] ) ) ) {
				// wcfm_detailed_analysis Query
				$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																	(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`)
																	VALUES
																	(0, 0, 1, {$post->ID}, {$post->post_author}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}')";
				$wpdb->query($wcfm_detailed_analysis);
				
				// wcfm_daily_analysis Query
				$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																	(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																	VALUES
																	(0, 0, 1, {$post->ID}, {$post->post_author}, 1, '{$todate}')
																	ON DUPLICATE KEY UPDATE
																	count = count+1";
				$wpdb->query($wcfm_daily_analysis);
				
				$wcfm_product_views = (int) get_post_meta( $post->ID, '_wcfm_product_views', true );
				if( !$wcfm_product_views ) $wcfm_product_views = 1;
				else $wcfm_product_views += 1;
				update_post_meta( $post->ID, '_wcfm_product_views', $wcfm_product_views );
				
				// Session store
				$_SESSION['wcfm_pages']['products'][] = $post->ID;
			}
		} else {
		  if( $is_marketplece == 'wcmarketplace' ) {
		  	if (is_tax('dc_vendor_shop')) {
		  		$vendor = get_wcmp_vendor_by_term(get_queried_object()->term_id);
		  		if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['stores'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['stores'] ) && !in_array( $vendor->id, $_SESSION['wcfm_pages']['stores'] ) ) ) {
						// wcfm_detailed_analysis Query
						$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`)
																			VALUES
																			(0, 1, 0, -1, {$vendor->id}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}')";
						$wpdb->query($wcfm_detailed_analysis);
						
						// wcfm_daily_analysis Query
						$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																			VALUES
																			(0, 1, 0, -1, {$vendor->id}, 1, '{$todate}')
																			ON DUPLICATE KEY UPDATE
																			count = count+1";
						$wpdb->query($wcfm_daily_analysis);
						
						// Session store
						$_SESSION['wcfm_pages']['stores'][] = $vendor->id;
					}
		  	}
		  } elseif( $is_marketplece == 'wcpvendors' ) {
		  	if (is_tax('wcpv_product_vendors')) {
		  		$vendor_shop = get_queried_object()->term_id;
		  		if( !isset( $_SESSION['wcfm_pages'] ) || !isset( $_SESSION['wcfm_pages']['stores'] ) || ( isset( $_SESSION['wcfm_pages'] ) && isset( $_SESSION['wcfm_pages']['stores'] ) && !in_array( $vendor_shop, $_SESSION['wcfm_pages']['stores'] ) ) ) {
						// wcfm_detailed_analysis Query
						$wcfm_detailed_analysis = "INSERT into {$wpdb->prefix}wcfm_detailed_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `referer`, `ip_address`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_shop}, '{$_SERVER['HTTP_REFERER']}', '{$_SERVER['REMOTE_ADDR']}')";
						$wpdb->query($wcfm_detailed_analysis);
						
						// wcfm_daily_analysis Query
						$wcfm_daily_analysis = "INSERT into {$wpdb->prefix}wcfm_daily_analysis 
																			(`is_shop`, `is_store`, `is_product`, `product_id`, `author_id`, `count`, `visited`)
																			VALUES
																			(0, 1, 0, -1, {$vendor_shop}, 1, '{$todate}')
																			ON DUPLICATE KEY UPDATE
																			count = count+1";
						$wpdb->query($wcfm_daily_analysis);
						
						// Session store
						$_SESSION['wcfm_pages']['stores'][] = $vendor_shop;
					}
		  	}
		  }
		}
		
		//print_R($_SERVER);
	}
	
	/**
	 * Is WCFM Ultimate Inactive Notice Show
	 */
	function is_wcfmu_inactive_notice_show( $show ) {
		$wcfm_options = get_option('wcfm_options');
	  $is_ultimate_notice_disabled = isset( $wcfm_options['ultimate_notice_disabled'] ) ? $wcfm_options['ultimate_notice_disabled'] : 'no';
	  if( $is_ultimate_notice_disabled == 'yes' ) $show = false;
		return $show;
	}
	
	/**
	 * WCFM Core JS
	 */
	function wcfm_scripts() {
 		global $WCFM;
 		
 		// Libs
	  $WCFM->library->load_qtip_lib();
 		
 		// Core JS
	  wp_enqueue_script( 'wcfm_core_js', $WCFM->library->js_lib_url . 'wcfm-script-core.js', array('jquery', 'wcfm_qtip_js' ), $WCFM->version, true );
	  
	  // Localize Script
	  wp_localize_script( 'wcfm_core_js', 'wcfm_params', array( 'ajax_url'    => WC()->ajax_url(), 'shop_url' => get_permalink( wc_get_page_id( 'shop' ) ) ) );
 	}
 	
 	/**
 	 * WCFM Core CSS
 	 */
 	function wcfm_styles() {
 		global $WCFM;
 		
 		// WC Icon set
	  wp_enqueue_style( 'wcfm_wc_icon_css',  $WCFM->library->css_lib_url . 'wcfm-style-icon.css', array(), $WCFM->version );
	  
	  // Font Awasome Icon set
	  wp_enqueue_style( 'wcfm_fa_icon_css',  $WCFM->plugin_url . 'assets/fonts/font-awesome/css/font-awesome.min.css', array(), $WCFM->version );
	  
	  // Admin Bar CSS
	  wp_enqueue_style( 'wcfm_admin_bar_css',  $WCFM->library->css_lib_url . 'wcfm-style-adminbar.css', array(), $WCFM->version );
	  
	  // WCFM Core CSS
	  wp_enqueue_style( 'wcfm_core_css',  $WCFM->library->css_lib_url . 'wcfm-style-core.css', array(), $WCFM->version );
 	}
 	
}
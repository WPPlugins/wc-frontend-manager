<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Orders Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Orders_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$args = array(
							'posts_per_page'   => $length,
							'offset'           => $offset,
							'category'         => '',
							'category_name'    => '',
							'orderby'          => 'date',
							'order'            => 'DESC',
							'include'          => '',
							'exclude'          => '',
							'meta_key'         => '',
							'meta_value'       => '',
							'post_type'        => 'shop_order',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => 'any',
							'suppress_filters' => true 
						);
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		$args = apply_filters( 'wcfm_orders_args', $args );
		
		$wcfm_orders_array = get_posts( $args );
		
		// Get Product Count
		$order_count = 0;
		$filtered_order_count = 0;
		$wcfm_orders_count = wp_count_posts('shop_order');
		$order_count = count($wcfm_orders_array);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_orders_array = get_posts( $args );
		$filtered_order_count = count($wcfm_filterd_orders_array);
		
		
		// Generate Products JSON
		$wcfm_orders_json = '';
		$wcfm_orders_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $order_count . ',
															"recordsFiltered": ' . $filtered_order_count . ',
															"data": ';
		if(!empty($wcfm_orders_array)) {
			$index = 0;
			$wcfm_orders_json_arr = array();
			foreach($wcfm_orders_array as $wcfm_orders_single) {
				$the_order = wc_get_order( $wcfm_orders_single );
				// Status
				$wcfm_orders_json_arr[$index][] =  '<span class="order-status tips wcicon-status-' . sanitize_title( $the_order->get_status() ) . ' text_tip" data-tip="' . wc_get_order_status_name( $the_order->get_status() ) . '"></span>';
				
				// Order
				if ( $the_order->user_id ) {
					$user_info = get_userdata( $the_order->user_id );
				}

				if ( ! empty( $user_info ) ) {

					$username = '';

					if ( $user_info->first_name || $user_info->last_name ) {
						$username .= esc_html( sprintf( _x( '%1$s %2$s', 'full name', 'wc-frontend-manager' ), ucfirst( $user_info->first_name ), ucfirst( $user_info->last_name ) ) );
					} else {
						$username .= esc_html( ucfirst( $user_info->display_name ) );
					}

				} else {
					if ( $the_order->billing_first_name || $the_order->billing_last_name ) {
						$username = trim( sprintf( _x( '%1$s %2$s', 'full name', 'wc-frontend-manager' ), $the_order->billing_first_name, $the_order->billing_last_name ) );
					} else if ( $the_order->billing_company ) {
						$username = trim( $the_order->billing_company );
					} else {
						$username = __( 'Guest', 'wc-frontend-manager' );
					}
				}

				$wcfm_orders_json_arr[$index][] =  '<a href="' . get_wcfm_view_order_url($wcfm_orders_single->ID, $the_order) . '" class="wcfm_order_title">#' . esc_attr( $the_order->get_order_number() ) . '</a> by ' . $username;
				
				// Purchased
				$order_item_details = '<div class="order_items" cellspacing="0">';
				$items = $the_order->get_items();
				foreach ($items as $key => $item) {
					$product        = apply_filters( 'woocommerce_order_item_product', $the_order->get_product_from_item( $item ), $item );
					$item_meta      = new WC_Order_Item_Meta( $item, $product );
					$item_meta_html = $item_meta->display( true, true );
				
					$order_item_details .= '<div class=""><span class="qty">' . $item['qty'] . 'x</span><span class="name">' . $item['name'];
					if ( ! empty( $item_meta_html ) ) $order_item_details .= '<span class="img_tip" data-tip="' . $item_meta_html . '"></span>';
					$order_item_details .= '</td></div>';
				}
				$order_item_details .= '</div>';
				$wcfm_orders_json_arr[$index][] =  '<a href="#" class="show_order_items">' . apply_filters( 'woocommerce_admin_order_item_count', sprintf( _n( '%d item', '%d items', $the_order->get_item_count(), 'wc-frontend-manager' ), $the_order->get_item_count() ), $the_order ) . '</a>' . $order_item_details;
				
				// Total
				$total = '<span class="order_total">' . $the_order->get_formatted_order_total() . '</span>';

				if ( $the_order->payment_method_title ) {
					$total .= '<br /><small class="meta">' . __( 'Via', 'wc-frontend-manager' ) . ' ' . esc_html( $the_order->payment_method_title ) . '</small>';
				}
				$wcfm_orders_json_arr[$index][] =  $total;
				
				// Date
				if ( '0000-00-00 00:00:00' == $wcfm_orders_single->post_date ) {
					$t_time = $h_time = __( 'Unpublished', 'wc-frontend-manager' );
				} else {
					$t_time = get_the_time( __( 'Y/m/d g:i:s A', 'wc-frontend-manager' ), $wcfm_orders_single );
					$h_time = get_the_time( __( 'Y/m/d', 'wc-frontend-manager' ), $wcfm_orders_single );
				}

				$wcfm_orders_json_arr[$index][] = '<abbr title="' . esc_attr( $t_time ) . '">' . esc_html( apply_filters( 'post_date_column_time', $h_time, $wcfm_orders_single ) ) . '</abbr>';
				
				// Action
				$actions = '<a class="wcfm-action-icon" href="' . get_wcfm_view_order_url($wcfm_orders_single->ID, $the_order) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View Details', 'wc-frontend-manager' ) . '"></span></a>';
				
				if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {      
						$actions .= '<a class="wcfm_order_mark_complete_dummy wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-check-square-o text_tip" data-tip="' . esc_attr__( 'Mark as Complete', $WCFMu->text_domain ) . '"></span></a>';
					}
				}
				
				if( WCFM_Dependencies::wcfmu_plugin_active_check() && WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) {
					$actions .= '<a class="wcfm_pdf_invoice wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
				} else {
					if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
						$actions .= '<a class="wcfm_pdf_invoice_dummy wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
					}
				}
				
				$wcfm_orders_json_arr[$index][] =  apply_filters ( 'wcfm_orders_actions', $actions, $wcfm_orders_single, $the_order );
				
				$index++;
			}												
		}
		if( !empty($wcfm_orders_json_arr) ) $wcfm_orders_json .= json_encode($wcfm_orders_json_arr);
		else $wcfm_orders_json .= '[]';
		$wcfm_orders_json .= '
													}';
													
		echo $wcfm_orders_json;
	}
}
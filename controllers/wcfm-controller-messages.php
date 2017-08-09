<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Messages Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   2.3.2
 */

class WCFM_Messages_Controller {
	
	public function __construct() {
		global $WCFM;
		
		$this->processing();
	}
	
	public function processing() {
		global $WCFM, $wpdb, $_POST, $start_date, $end_date;
		
		$length = $_POST['length'];
		$offset = $_POST['start'];
		
		$message_to = apply_filters( 'wcfm_message_author', get_current_user_id() );
		
		$the_orderby = ! empty( $_POST['orderby'] ) ? sanitize_text_field( $_POST['orderby'] ) : 'ID';
		$the_order   = ( ! empty( $_POST['order'] ) && 'asc' === $_POST['order'] ) ? 'ASC' : 'DESC';

		$items_per_page = $length;

		$sql = 'SELECT COUNT(wcfm_messages.ID) FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
		$sql .= ' LEFT JOIN ' . $wpdb->prefix . 'wcfm_messages_modifier as wcfm_messages_modifier';
		$sql .= ' ON wcfm_messages.ID = wcfm_messages_modifier.message';

		$sql .= ' WHERE 1=1';

		if ( ! empty( $_POST['message_type'] ) ) {
			$message_type = esc_sql( $_POST['message_type'] );

			if( $message_type == 'notice' ) {
				$status_filter = " AND `is_notice` = 1";
			} elseif( $message_type == 'message' ) {
				$status_filter = " AND `is_direct_message` = 1";
			}
			$sql .= $status_filter;
		}
		
		if( wcfm_is_vendor() ) { 
			//$vendor_filter = " AND `author_is_admin` = 1";
			$vendor_filter = " AND ( `author_id` = {$message_to} OR `message_to` = -1 OR `message_to` = {$message_to} )";
			$sql .= $vendor_filter;
		}
		
		$message_status = '';
		if ( ! empty( $_POST['message_status'] ) ) {
			$message_status = esc_sql( $_POST['message_status'] );

			if( $message_status == 'read' ) {
				$message_status_filter = " AND wcfm_messages_modifier.is_read = 1 AND wcfm_messages_modifier.read_by = {$message_to}";
			} elseif( $message_status == 'unread' ) {
				$message_status_filter = " AND NOT EXISTS (SELECT * FROM {$wpdb->prefix}wcfm_messages_modifier as wcfm_messages_modifier_2 WHERE wcfm_messages.ID = wcfm_messages_modifier_2.message AND wcfm_messages_modifier_2.read_by={$message_to})";
			}
			$sql .= $message_status_filter;
		}
		
		$total_mesaages = $wpdb->get_var( $sql );

		$sql = 'SELECT wcfm_messages.*, wcfm_messages_modifier.is_read FROM ' . $wpdb->prefix . 'wcfm_messages AS wcfm_messages';
		$sql .= ' LEFT JOIN ' . $wpdb->prefix . 'wcfm_messages_modifier as wcfm_messages_modifier';
		$sql .= ' ON wcfm_messages.ID = wcfm_messages_modifier.message';

		$sql .= ' WHERE 1=1';

		if ( ! empty( $_POST['message_type'] ) ) {
			$sql .= $status_filter;
		}
		
		if( wcfm_is_vendor() ) { 
			$sql .= $vendor_filter;
		}
		
		if ( ! empty( $_POST['message_status'] ) ) {
			$sql .= $message_status_filter;
		}

		$sql .= " ORDER BY wcfm_messages.`{$the_orderby}` {$the_order}";

		$sql .= " LIMIT {$items_per_page}";

		$sql .= " OFFSET {$offset}";

		$wcfm_messages = $wpdb->get_results( $sql );
		
		// Generate Products JSON
		$wcfm_messages_json = '';
		$wcfm_messages_json = '{
														"draw": ' . $_POST['draw'] . ',
														"recordsTotal": ' . $total_mesaages . ',
														"recordsFiltered": ' . $total_mesaages . ',
														"data": ';
		
		$index = 0;
		$wcfm_messages_json_arr = array();
		if ( !empty( $wcfm_messages ) ) {
			foreach ( $wcfm_messages as $wcfm_message ) {
	
				// Message
				$wcfm_messages_json_arr[$index][] =  htmlspecialchars_decode($wcfm_message->message);
				
				// From
				if( $wcfm_message->author_is_admin ) {
					$wcfm_messages_json_arr[$index][] =  __( 'Store', 'wc-frontend-manager' );
				} else {
					$is_marketplece = wcfm_is_marketplace();
					if( $is_marketplece == 'wcpvendors' ) {
						if( !wcfm_is_vendor() ) {
							$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $wcfm_message->author_id );
							$wcfm_messages_json_arr[$index][] = ! empty( $vendor_data['shop_name'] ) ? $vendor_data['shop_name'] : '';
						} else {
							$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
						}
					} else {
							if( !wcfm_is_vendor() ) {
								$wcfm_messages_json_arr[$index][] =  get_user_meta( $wcfm_message->author_id, 'nickname', true );
							} else {
								$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
							}
					}
				}
				
				// TO
				if( $wcfm_message->message_to == -1 ) {
					$wcfm_messages_json_arr[$index][] =  __( 'Vendors', 'wc-frontend-manager' );
				} else if( $wcfm_message->message_to == 0 ) {
					$wcfm_messages_json_arr[$index][] =  __( 'Store', 'wc-frontend-manager' );
				} else {
					$is_marketplece = wcfm_is_marketplace();
					if( $is_marketplece == 'wcpvendors' ) {
						if( !wcfm_is_vendor() ) {
							$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_by_id( $wcfm_message->message_to );
							$wcfm_messages_json_arr[$index][] = ! empty( $vendor_data['shop_name'] ) ? $vendor_data['shop_name'] : '';
						} else {
							$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
						}
					} else {
						if( !wcfm_is_vendor() ) {
							$wcfm_messages_json_arr[$index][] =  get_user_meta( $wcfm_message->message_to, 'nickname', true );
						} else {
							$wcfm_messages_json_arr[$index][] =  __( 'You', 'wc-frontend-manager' );
						}
					}
				}
				
				// Type
				if( $wcfm_message->is_notice ) {
					$wcfm_messages_json_arr[$index][] = '<span class="wcfm-notice-message">' . esc_html__( 'NOTICE', 'wc-frontend-manager' ) . '</span>';
				} else {
					$wcfm_messages_json_arr[$index][] = '<span class="wcfm-direct-message">' . esc_html__( 'MESSAGE', 'wc-frontend-manager' ) . '</span>';
				}

				
				// Date
				$wcfm_messages_json_arr[$index][] = date_i18n( wc_date_format(), strtotime( $wcfm_message->created ) );
				
				// Action
				$actions = '';
				if( $message_status == 'unread' ) $actions .= '<a class="wcfm_messages_mark_read wcfm-action-icon" href="#" data-messageid="' . $wcfm_message->ID . '"><span class="fa fa-check-square-o text_tip" data-tip="' . esc_attr__( 'Mark Read', 'wc-frontend-manager' ) . '"></span></a>';
				
				/*if( $wcfm_is_allow_pdf_invoice = apply_filters( 'wcfm_is_allow_pdf_invoice', true ) ) {
					if( WCFM_Dependencies::wcfmu_plugin_active_check() && WCFM_Dependencies::wcfm_wc_pdf_invoices_packing_slips_plugin_active_check() ) {
						$actions .= '<a class="wcfm_pdf_invoice wcfm-action-icon" href="#" data-orderid="' . $the_order->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
					} else {
						if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
							$actions .= '<a class="wcfm_pdf_invoice_vendor_dummy wcfm-action-icon" href="#" data-orderid="' . $wcfm_orders_single->ID . '"><span class="fa fa-file-pdf-o text_tip" data-tip="' . esc_attr__( 'PDF Invoice', 'wc-frontend-manager' ) . '"></span></a>';
						}
					}
				}*/
				
				$wcfm_messages_json_arr[$index][] =  apply_filters ( 'wcfm_messages_actions', $actions );
				
				$index++;
			}
		}
		if( !empty($wcfm_messages_json_arr) ) $wcfm_messages_json .= json_encode($wcfm_messages_json_arr);
		else $wcfm_messages_json .= '[]';
		$wcfm_messages_json .= '
													}';
													
		echo $wcfm_messages_json;
	}
}
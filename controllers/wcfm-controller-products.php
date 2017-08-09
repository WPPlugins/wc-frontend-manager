<?php
/**
 * WCFM plugin controllers
 *
 * Plugin Products Controller
 *
 * @author 		WC Lovers
 * @package 	wcfm/controllers
 * @version   1.0.0
 */

class WCFM_Products_Controller {
	
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
							'post_type'        => 'product',
							'post_mime_type'   => '',
							'post_parent'      => '',
							//'author'	   => get_current_user_id(),
							'post_status'      => array('draft', 'pending', 'publish'),
							'suppress_filters' => true 
						);
		$for_count_args = $args;
		
		if( isset( $_POST['search'] ) && !empty( $_POST['search']['value'] )) $args['s'] = $_POST['search']['value'];
		
		$args = apply_filters( 'wcfm_products_args', $args );
		
		$wcfm_products_array = get_posts( $args );
		
		// Get Product Count
		$pro_count = 0;
		$filtered_pro_count = 0;
		$for_count_args['posts_per_page'] = -1;
		$for_count_args['offset'] = 0;
		$for_count_args = apply_filters( 'wcfm_products_args', $for_count_args );
		$wcfm_products_count = get_posts( $for_count_args );
		$pro_count = count($wcfm_products_count);
		// Get Filtered Post Count
		$args['posts_per_page'] = -1;
		$args['offset'] = 0;
		$wcfm_filterd_products_array = get_posts( $args );
		$filtered_pro_count = count($wcfm_filterd_products_array);
		
		
		// Generate Products JSON
		$wcfm_products_json = '';
		$wcfm_products_json = '{
															"draw": ' . $_POST['draw'] . ',
															"recordsTotal": ' . $pro_count . ',
															"recordsFiltered": ' . $filtered_pro_count . ',
															"data": ';
		if(!empty($wcfm_products_array)) {
			$index = 0;
			$wcfm_products_json_arr = array();
			foreach($wcfm_products_array as $wcfm_products_single) {
				$the_product = wc_get_product( $wcfm_products_single );
				// Thumb
				$wcfm_products_json_arr[$index][] =  '<a href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '">' . $the_product->get_image( 'thumbnail' ) . '</a>';
				
				// Title
				if( current_user_can( 'edit_published_products' ) ) {
					$wcfm_products_json_arr[$index][] =  '<a href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '" class="wcfm_product_title">' . $wcfm_products_single->post_title . '</a>';
				} else {
					$wcfm_products_json_arr[$index][] =  $wcfm_products_single->post_title;
				}
				
				// SKU
				$wcfm_products_json_arr[$index][] =  ( get_post_meta($wcfm_products_single->ID, '_sku', true) ) ? get_post_meta($wcfm_products_single->ID, '_sku', true) : '-';
				
				// Status
				$wcfm_products_json_arr[$index][] =  '<span class="product-status product-status-' . $wcfm_products_single->post_status . '">' . ucfirst( $wcfm_products_single->post_status ) . '</span>';
				
				// Stock
				if ( $the_product->is_in_stock() ) {
					$stock_html = '<span class="instock">' . __( 'In stock', 'woocommerce' ) . '</span>';
				} else {
					$stock_html = '<span class="outofstock">' . __( 'Out of stock', 'woocommerce' ) . '</span>';
				}
		
				// If the product has children, a single stock level would be misleading as some could be -ve and some +ve, some managed/some unmanaged etc so hide stock level in this case.
				if ( $the_product->managing_stock() && ! sizeof( $the_product->get_children() ) ) {
					$stock_html .= ' (' . $the_product->get_total_stock() . ')';
				}
				$wcfm_products_json_arr[$index][] =  apply_filters( 'woocommerce_admin_stock_html', $stock_html, $the_product );
				
				// Price
				$wcfm_products_json_arr[$index][] =  $the_product->get_price_html() ? $the_product->get_price_html() : '<span class="na">&ndash;</span>';
				
				// Type
				$pro_type = '';
				if ( 'grouped' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips grouped wcicon-grouped text_tip" data-tip="' . esc_attr__( 'Grouped', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'external' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips external wcicon-external text_tip" data-tip="' . esc_attr__( 'External/Affiliate', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'simple' == $the_product->product_type ) {
		
					if ( $the_product->is_virtual() ) {
						$pro_type = '<span class="product-type tips virtual wcicon-virtual text_tip" data-tip="' . esc_attr__( 'Virtual', 'wc-frontend-manager' ) . '"></span>';
					} elseif ( $the_product->is_downloadable() ) {
						$pro_type = '<span class="product-type tips downloadable wcicon-downloadable text_tip" data-tip="' . esc_attr__( 'Downloadable', 'wc-frontend-manager' ) . '"></span>';
					} else {
						$pro_type = '<span class="product-type tips simple wcicon-simple text_tip" data-tip="' . esc_attr__( 'Simple', 'wc-frontend-manager' ) . '"></span>';
					}
		
				} elseif ( 'variable' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips variable wcicon-variable text_tip" data-tip="' . esc_attr__( 'Variable', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'subscription' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips wcicon-variable text_tip" data-tip="' . esc_attr__( 'Subscription', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'variable-subscription' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips wcicon-variable text_tip" data-tip="' . esc_attr__( 'Variable Subscription', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'job_package' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips fa fa-briefcase text_tip" data-tip="' . esc_attr__( 'Job Package', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'resume_package' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips fa fa-suitcase text_tip" data-tip="' . esc_attr__( 'Resume Package', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'auction' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips fa fa-gavel text_tip" data-tip="' . esc_attr__( 'Auction', 'wc-frontend-manager' ) . '"></span>';
				} elseif ( 'redq_rental' == $the_product->product_type ) {
					$pro_type = '<span class="product-type tips fa fa-cab text_tip" data-tip="' . esc_attr__( 'Rental', 'wc-frontend-manager' ) . '"></span>';
				} else {
					// Assuming that we have other types in future
					$pro_type = '<span class="product-type tips wcicon-' . $the_product->product_type . ' text_tip ' . $the_product->product_type . '" data-tip="' . ucfirst( $the_product->product_type ) . '"></span>';
				}
				$wcfm_products_json_arr[$index][] =  $pro_type;
				
				// Views
				$wcfm_products_json_arr[$index][] =  '<span class="view_count">' . (int) get_post_meta( $wcfm_products_single->ID, '_wcfm_product_views', true ) . '</span>';
				
				// Date
				$wcfm_products_json_arr[$index][] =  date( 'F j, Y', strtotime($wcfm_products_single->post_date));
				
				// Action
				$actions = '<a class="wcfm-action-icon" target="_blank" href="' . get_permalink( $wcfm_products_single->ID ) . '"><span class="fa fa-eye text_tip" data-tip="' . esc_attr__( 'View', 'wc-frontend-manager' ) . '"></span></a>';
				if( $wcfm_products_single->post_status == 'publish' ) {
					$actions .= ( current_user_can( 'edit_published_products' ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
					$actions .= ( current_user_can( 'delete_published_products' ) ) ? '<a class="wcfm-action-icon wcfm_product_delete" href="#" data-proid="' . $wcfm_products_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>' : '';
				} else {
					$actions .= ( current_user_can( 'edit_products' ) ) ? '<a class="wcfm-action-icon" href="' . get_wcfm_edit_product_url($wcfm_products_single->ID, $the_product) . '"><span class="fa fa-edit text_tip" data-tip="' . esc_attr__( 'Edit', 'wc-frontend-manager' ) . '"></span></a>' : '';
					$actions .= ( current_user_can( 'delete_products' ) ) ? '<a class="wcfm_product_delete wcfm-action-icon" href="#" data-proid="' . $wcfm_products_single->ID . '"><span class="fa fa-trash-o text_tip" data-tip="' . esc_attr__( 'Delete', 'wc-frontend-manager' ) . '"></span></a>' : '';
				}
				$wcfm_products_json_arr[$index][] =  apply_filters ( 'wcfm_products_actions',  $actions, $the_product );
				
				
				$index++;
			}												
		}
		if( !empty($wcfm_products_json_arr) ) $wcfm_products_json .= json_encode($wcfm_products_json_arr);
		else $wcfm_products_json .= '[]';
		$wcfm_products_json .= '
													}';
													
		echo $wcfm_products_json;
	}
}
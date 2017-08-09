<?php
/**
 * WCFMu plugin view
 *
 * Marketplace WC Product Vendors Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.1.0
 */
 
global $WCFM, $wpdb, $start_date, $end_date;

$vendor_product_ids = WC_Product_Vendors_Utils::get_vendor_product_ids();

$sql = "SELECT SUM( commission.product_amount ) AS total_product_amount FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";

$sql .= " LEFT JOIN {$wpdb->posts} AS posts";
$sql .= " ON commission.order_id = posts.ID";
$sql .= " WHERE 1=1";
$sql .= " AND commission.vendor_id = %d";
$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() )";

$total_product_amount = $wpdb->get_var( $wpdb->prepare( $sql, WC_Product_Vendors_Utils::get_logged_in_vendor() ) );
if( !$total_product_amount ) $total_product_amount = 0;

// Get top seller
$query            = array();
$query['fields']  = "SELECT SUM( order_item_meta.meta_value ) as qty, order_item_meta_2.meta_value as product_id
	FROM {$wpdb->posts} as posts";
$query['join']    = "INNER JOIN {$wpdb->prefix}woocommerce_order_items AS order_items ON posts.ID = order_id ";
$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id ";
$query['join']   .= "INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta_2 ON order_items.order_item_id = order_item_meta_2.order_item_id ";
$query['where']   = "WHERE posts.post_type IN ( '" . implode( "','", wc_get_order_types( 'order-count' ) ) . "' ) ";
$query['where']  .= "AND posts.post_status IN ( 'wc-" . implode( "','wc-", apply_filters( 'wcpv_reports_order_statuses', array( 'completed', 'processing', 'on-hold' ) ) ) . "' ) ";
$query['where']  .= "AND order_item_meta.meta_key = '_qty' ";
$query['where']  .= "AND order_item_meta_2.meta_key = '_product_id' ";
$query['where']  .= "AND posts.post_date >= '" . date( 'Y-m-01', current_time( 'timestamp' ) ) . "' ";
$query['where']  .= "AND posts.post_date <= '" . date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) . "' ";
$query['where']  .= "AND order_item_meta_2.meta_value IN ( '" . implode( "','", $vendor_product_ids ) . "' ) ";
$query['groupby'] = "GROUP BY product_id";
$query['orderby'] = "ORDER BY qty DESC";
$query['limits']  = "LIMIT 1";

$top_seller = $wpdb->get_row( implode( ' ', apply_filters( 'wcpv_dashboard_status_widget_top_seller_query', $query ) ) );

// Commission
if ( WC_Product_Vendors_Utils::commission_table_exists() ) {

	$sql = "SELECT SUM( commission.total_commission_amount ) FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
	$sql .= " WHERE 1=1";
	$sql .= " AND commission.vendor_id = %d";
	$sql .= " AND commission.commission_status = 'paid'";
	$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() )";

	$commission = $wpdb->get_var( $wpdb->prepare( $sql, WC_Product_Vendors_Utils::get_logged_in_vendor() ) );
	if( !$commission ) $commission = 0;
}

// Total item sell
if ( WC_Product_Vendors_Utils::commission_table_exists() ) {

	$sql = "SELECT SUM( commission.product_quantity ) FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
	$sql .= " WHERE 1=1";
	$sql .= " AND commission.vendor_id = %d";
	$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() )";

	$total_sell = $wpdb->get_var( $wpdb->prepare( $sql, WC_Product_Vendors_Utils::get_logged_in_vendor() ) );
	if( !$total_sell ) $total_sell = 0;
}

// Awaiting shipping
if ( WC_Product_Vendors_Utils::commission_table_exists() ) {

	$sql = "SELECT COUNT( commission.id ) FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
	$sql .= " INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS order_item_meta ON commission.order_item_id = order_item_meta.order_item_id";
	$sql .= " WHERE 1=1";
	$sql .= " AND commission.vendor_id = %d";
	$sql .= " AND order_item_meta.meta_key = '_fulfillment_status'";
	$sql .= " AND order_item_meta.meta_value = 'unfulfilled'";

	$unfulfilled_products = $wpdb->get_var( $wpdb->prepare( $sql, WC_Product_Vendors_Utils::get_logged_in_vendor() ) );
	if( !$unfulfilled_products ) $unfulfilled_products = 0;
}

// Counts
$order_count = 0;
$on_hold_count    = 0;
$processing_count = 0;

$sql = "SELECT commission.order_id FROM " . WC_PRODUCT_VENDORS_COMMISSION_TABLE . " AS commission";
$sql .= " WHERE 1=1";
$sql .= " AND commission.vendor_id = %d";
$sql .= " AND MONTH( commission.order_date ) = MONTH( NOW() )";
$sql .= " GROUP BY commission.order_id";

$vendor_orders = $wpdb->get_results( $wpdb->prepare( $sql, WC_Product_Vendors_Utils::get_logged_in_vendor() ) );
if( !empty($vendor_orders) ) {
	$order_count = count( $vendor_orders );
	foreach( $vendor_orders as $vendor_order ) {
		if( $vendor_order->order_id ) {
			$vendor_order_data = new WC_Order( $vendor_order->order_id );
			if( $vendor_order_data->get_status() == 'processing' ) $processing_count++;
			if( $vendor_order_data->get_status() == 'on-hold' ) $on_hold_count++;
		}
	}
}

$stock          = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
$nostock        = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );
$transient_name = 'wc_low_stock_count';

$query_from = apply_filters( 'wcpv_report_low_in_stock_query_from', "FROM {$wpdb->posts} as posts
	INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
	INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
	WHERE 1=1
	AND posts.post_type IN ( 'product', 'product_variation' )
	AND posts.post_status = 'publish'
	AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
	AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
	AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'
	AND posts.ID IN ( '" . implode( "','", $vendor_product_ids ) . "' )
" );

$lowinstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );

$query_from = apply_filters( 'wcpv_report_out_of_stock_query_from', "FROM {$wpdb->posts} as posts
	INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
	INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
	WHERE 1=1
	AND posts.post_type IN ( 'product', 'product_variation' )
	AND posts.post_status = 'publish'
	AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
	AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$nostock}'
	AND posts.ID IN ( '" . implode( "','", $vendor_product_ids ) . "' )
" );

$outofstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );

include_once( $WCFM->plugin_path . 'includes/reports/class-wcpvendors-report-sales-by-date.php' );

$wcfm_report_sales_by_date = new WC_Product_Vendors_Vendor_Report_Sales_By_Date();

$wcfm_report_sales_by_date->chart_colors = apply_filters( 'wcfm_vendor_sales_by_date_chart_colors',  array(
	'sales_amount'     => '#b1d4ea',
	'net_sales_amount' => '#3498db',
	'average'          => '#95a5a6',
	'order_count'      => '#dbe1e3',
	'item_count'       => '#ecf0f1',
	'shipping_amount'  => '#FF7400',
	'earned'           => '#4096EE',
	'commission'       => '#00897b',
) );

$wcfm_report_sales_by_date->calculate_current_range( '7day' );
$report_data   = $wcfm_report_sales_by_date->get_report_data();

$date_diff = date_diff( date_create(date('Ymd', $start_date)), date_create(date('Ymd', $end_date)) );

$vendor_data = WC_Product_Vendors_Utils::get_vendor_data_from_user();

do_action( 'before_wcfm_dashboard' );
?>

<div class="collapse wcfm-collapse" id="wcfm_order_details">

  <div class="wcfm-page-headig">
		<span class="fa fa-dashboard"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Dashboard', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'begin_wcfm_dashboard' ); ?>
		
		<div class="wcfm_dashboard_wc_status">
			<div class="wcfm_dashboard_wc_status_data">
				<div class="page_collapsible" id="wcfm_dashboard_wc_status">
					<span class="fa fa-line-chart"></span>
					<span class="dashboard_widget_head"><?php _e('Store Status', 'wc-frontend-manager'); ?></span>
				</div>
				<div class="wcfm-container">
					<div id="wcfm_dashboard_wc_status_expander" class="wcfm-content">
						<ul class="wc_status_list">
						  <?php if( $wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true ) ) { ?>
								<?php if ( WC_Product_Vendors_Utils::is_admin_vendor() ) { ?>
								<li class="sales-count-this-month">
									<span class="fa fa-dollar"></span>
									<a href="<?php echo get_wcfm_reports_url( ); ?>">
										<?php printf( __( '<strong>%s</strong><br /> net sales in this month', 'wc-frontend-manager' ),wc_price( $total_product_amount ) ); ?>
									</a>
								</li>
								<li class="sales-this-month">
									<span class="fa fa-money"></span>
									<a href="<?php echo get_wcfm_reports_url( ); ?>">
										<?php printf( __( '<strong>%s</strong><br /> commission this month', 'wc-frontend-manager' ), wc_price( $commission ) ); ?>
									</a>
								</li>
								<?php } ?>
							<?php } ?>
							<?php if( $wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true ) ) { ?>
								<li class="sales-count-this-month">
									<span class="fa fa-cubes"></span>
									<a href="<?php echo apply_filters( 'sales_by_product_report_url', get_wcfm_reports_url( ), '' ); ?>">
										<?php printf( __( '<strong>%s items</strong><br /> net sales in this month', 'wc-frontend-manager' ), $total_sell ); ?>
									</a>
								</li>
								<?php
								if ( empty( $top_seller ) || ! $top_seller->qty ) {
									$top_seller_id = 0;
									$top_seller_title = __( 'N/A', 'woocommerce-product-vendors' );
									$top_seller_qty = '0';
								} else {
									$top_seller_id = $top_seller->product_id;
									$top_seller_title = get_the_title( $top_seller->product_id );
									$top_seller_qty = $top_seller->qty;
								}
								?>
								<li class="best-seller-this-month">
									<span class="fa fa-cube"></span>
									<a href="<?php echo apply_filters( 'sales_by_product_report_url',  get_wcfm_reports_url( ), $top_seller_id ); ?>">
										<?php printf( __( "%s top seller this month (sold %d)", 'wc-frontend-manager' ), "<strong>" . $top_seller_title . "</strong><br />", $top_seller_qty ); ?>
									</a>
								</li>
							<?php } ?>
							<?php if( $wcfm_is_allow_orders = apply_filters( 'wcfm_is_allow_orders', true ) ) { ?>
								<li class="total-orders">
									<span class="fa fa-cart-plus"></span>
									<a href="<?php echo get_wcfm_orders_url( ); ?>">
										<?php printf( _n( "<strong>%s order</strong><br /> received", "<strong>%s orders</strong><br /> received", $order_count, 'wc-frontend-manager' ), $order_count ); ?>
									</a>
								</li>
								<li class="processing-orders">
									<span class="fa fa-life-ring"></span>
									<a href="<?php echo get_wcfm_orders_url( ); ?>">
										<?php printf( _n( "<strong>%s order</strong><br /> processing", "<strong>%s orders</strong><br /> processing", $processing_count, 'wc-frontend-manager' ), $processing_count ); ?>
									</a>
								</li>
								<li class="on-hold-orders">
									<span class="fa fa-truck"></span>
									<a href="<?php echo get_wcfm_orders_url( ); ?>">
										<?php printf( _n( "<strong>%s product</strong><br /> awaiting fulfillment", "<strong>%s products</strong><br /> awaiting fulfillment", $unfulfilled_products, 'wc-frontend-manager' ), $unfulfilled_products ); ?>
									</a>
								</li>
							<?php } ?>
							<?php if( $wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true ) ) { ?>
								<li class="low-in-stock">
									<span class="fa fa-sort-amount-desc"></span>
									<a href="<?php echo apply_filters( 'low_in_stock_report_url',  get_wcfm_reports_url( ) ); ?>">
										<?php printf( _n( "<strong>%s product</strong><br /> low in stock", "<strong>%s products</strong><br /> low in stock", $lowinstock_count, 'wc-frontend-manager' ), $lowinstock_count ); ?>
									</a>
								</li>
								<li class="out-of-stock">
									<span class="fa fa-times-circle-o"></span>
									<a href="<?php echo get_wcfm_reports_url( '', 'wcfm-reports-out-of-stock' ); ?>">
										<?php printf( _n( "<strong>%s product</strong><br /> out of stock", "<strong>%s products</strong><br /> out of stock", $outofstock_count, 'wc-frontend-manager' ), $outofstock_count ); ?>
									</a>
								</li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
			
		
			<?php if( $wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true ) ) { ?>
				<div class="wcfm_dashboard_wc_status_graph">
					<div class="wcfm_dashboard_wc_reports">
						<div class="page_collapsible" id="wcfm_dashboard_wc_reports"><span class="fa fa-pie-chart"></span><span class="dashboard_widget_head"><?php _e('Sales by Product', 'wc-frontend-manager'); ?></span></div>
						<div class="wcfm-container">
							<div id="wcfm_dashboard_wc_reports_expander" class="wcfm-content">
								<a href="<?php echo apply_filters( 'sales_by_product_report_url',  get_wcfm_reports_url( ), ( $top_seller ) ? $top_seller->product_id : '' ); ?>">
									<div id="sales-piechart"></div>
								</a>
							</div>
						</div>
					</div>
					
					<div class="wcfm_dashboard_wc_reports">
						<div class="page_collapsible" id="wcfm_dashboard_wc_reports"><span class="fa fa-bar-chart"></span><span class="dashboard_widget_head"><?php _e('Sales by Date', 'wc-frontend-manager'); ?></span></div>
						<div class="wcfm-container">
							<div id="wcfm_dashboard_wc_reports_expander" class="wcfm-content">
								<div id="poststuff" class="woocommerce-reports-wide">
									<div class="postbox">
										<div class="inside">
											<a href="<?php echo get_wcfm_reports_url( 'month' ); ?>">
												<?php $wcfm_report_sales_by_date->get_main_chart(); ?>
											</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
			
					<?php do_action('after_wcfm_dashboard_sales_report'); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
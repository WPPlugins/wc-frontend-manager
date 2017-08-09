<?php
/**
 * WCFMu plugin view
 *
 * Marketplace WC Marketplace Support
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   1.1.0
 */
 
global $WCMp, $WCFM, $wpdb;

$user_id = $current_user_id = get_current_user_id();
$vendor_term = get_user_meta( $user_id, '_vendor_term_id', true );

// Get products using a query - this is too advanced for get_posts :(
$stock          = absint( max( get_option( 'woocommerce_notify_low_stock_amount' ), 1 ) );
$nostock        = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) );

$query_from = apply_filters( 'wcfm_report_low_in_stock_query_from', "FROM {$wpdb->posts} as posts
	INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
	INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
	WHERE 1=1
	AND posts.post_type IN ( 'product', 'product_variation' )
	AND posts.post_status = 'publish'
	AND posts.post_author = {$user_id}
	AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
	AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$stock}'
	AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) > '{$nostock}'
", $stock, $nostock );
$lowinstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );

$query_from = apply_filters( 'wcfm_report_out_of_stock_query_from', "FROM {$wpdb->posts} as posts
	INNER JOIN {$wpdb->postmeta} AS postmeta ON posts.ID = postmeta.post_id
	INNER JOIN {$wpdb->postmeta} AS postmeta2 ON posts.ID = postmeta2.post_id
	WHERE 1=1
	AND posts.post_type IN ( 'product', 'product_variation' )
	AND posts.post_status = 'publish'
	AND postmeta2.meta_key = '_manage_stock' AND postmeta2.meta_value = 'yes'
	AND postmeta.meta_key = '_stock' AND CAST(postmeta.meta_value AS SIGNED) <= '{$nostock}'
", $nostock );

$outofstock_count = absint( $wpdb->get_var( "SELECT COUNT( DISTINCT posts.ID ) {$query_from};" ) );

$products = array();
if ($user_id) {
	$vendor = get_wcmp_vendor($user_id);
	if ($vendor)
		$vendor_products = $vendor->get_products();
	if (!empty($vendor_products)) {
		foreach ($vendor_products as $vendor_product) {
			$products[] = $vendor_product->ID;
			if( $vendor_product->post_type == 'product_variation' ) $products[] = $vendor_product->post_parent;
		}
	}
}

// Total Earned
$today_date = @date('Y-m-d');
$curent_week_range = wcmp_rangeWeek($today_date);
$sale_results_whole_week = $wpdb->get_results("SELECT COALESCE( SUM(`commission_amount`), 0 ) as commission, COALESCE( SUM(`shipping`), 0 ) as shipping, COALESCE( SUM(`tax`), 0 ) as tax FROM " . $wpdb->prefix . "wcmp_vendor_orders WHERE vendor_id = " . $current_user_id . " AND MONTH( created ) = MONTH( NOW() ) and `commission_id` != 0 and `commission_id` != '' and `is_trashed` != 1", OBJECT);
$earned = 0;
if( isset($sale_results_whole_week[0]) && !empty( $sale_results_whole_week[0] ) ) {
	$earned = $sale_results_whole_week[0]->commission;
	if($WCMp->vendor_caps->vendor_payment_settings('give_shipping')) {
		$earned += $sale_results_whole_week[0]->shipping;
	}
	if($WCMp->vendor_caps->vendor_payment_settings('give_tax')) {
		$earned += $sale_results_whole_week[0]->tax;
	}
}

// Total Paid Commission
$query            = array();
$query['fields']  = "SELECT SUM( post_meta.meta_value ) as commission
	FROM {$wpdb->posts} as posts";
$query['join']    = "INNER JOIN {$wpdb->postmeta} AS post_meta ON posts.ID = post_meta.post_id ";
$query['join']   .= "INNER JOIN {$wpdb->postmeta} AS post_meta_2 ON posts.ID = post_meta_2.post_id ";
$query['join']   .= "INNER JOIN {$wpdb->postmeta} AS post_meta_3 ON posts.ID = post_meta_3.post_id ";
$query['where']   = "WHERE posts.post_type = 'dc_commission' ";
$query['where']  .= "AND post_meta.meta_key = '_commission_amount' ";
$query['where']  .= "AND MONTH( posts.post_date ) = MONTH( NOW() ) ";
$query['where']  .= "AND post_meta_2.meta_key = '_commission_vendor' ";
$query['where']  .= "AND post_meta_2.meta_value = {$vendor_term} ";
$query['where']  .= "AND post_meta_3.meta_key = '_paid_status' ";
$query['where']  .= "AND post_meta_3.meta_value = 'paid' ";

$commission = $wpdb->get_var( implode( ' ', apply_filters( 'wcmp_dashboard_paid_commission_query', $query ) ) );
if( !$commission ) $commission = 0;

// Total item sold
$sql = "SELECT COUNT( commission.product_id ) FROM {$wpdb->prefix}wcmp_vendor_orders AS commission";
$sql .= " WHERE 1=1";
$sql .= " AND commission.vendor_id = %d";
$sql .= " AND MONTH( commission.created ) = MONTH( NOW() )";

$total_sell = $wpdb->get_var( $wpdb->prepare( $sql, $user_id ) );
if( !$total_sell ) $total_sell = 0;

// Counts
$order_count = 0;
$on_hold_count    = 0;
$processing_count = 0;

$sql = "SELECT commission.order_id FROM {$wpdb->prefix}wcmp_vendor_orders AS commission";
$sql .= " WHERE 1=1";
$sql .= " AND commission.vendor_id = %d";
$sql .= " AND MONTH( commission.created ) = MONTH( NOW() )";
$sql .= " GROUP BY commission.order_id";

$vendor_orders = $wpdb->get_results( $wpdb->prepare( $sql, $user_id ) );
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

// unfulfilled_products
$unfulfilled_products = 0;
$sql  = "SELECT  COUNT(DISTINCT(commission.order_id)) FROM {$wpdb->prefix}wcmp_vendor_orders AS commission";
$sql .= " WHERE 1=1";
$sql .= " AND commission.vendor_id = %d";
$sql .= " AND commission.shipping_status = 0";
$sql .= " AND MONTH( commission.created ) = MONTH( NOW() )";

$unfulfilled_products = $wpdb->get_var( $wpdb->prepare( $sql, $user_id ) );
if( !$unfulfilled_products ) $unfulfilled_products = 0;

include_once( $WCFM->plugin_path . 'includes/reports/class-wcmarketplace-report-sales-by-date.php' );
$wcfm_report_sales_by_date = new WC_Marketplace_Report_Sales_By_Date();
$wcfm_report_sales_by_date->chart_colors = apply_filters( 'wcfm_vendor_sales_by_date_chart_colors', array(
			'average'          => '#95a5a6',
			'order_count'      => '#dbe1e3',
			'shipping_amount'  => '#FF7400',
			'earned'           => '#4096EE',
			'commission'       => '#00897b',
		) );
$wcfm_report_sales_by_date->calculate_current_range( '7day' );
$report_data   = $wcfm_report_sales_by_date->get_report_data();

$date_diff = date_diff( date_create(date('Ymd', strtotime($curent_week_range['start']))), date_create(date('Ymd', strtotime($curent_week_range['end']))) );

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
		
		<!-- collapsible -->
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
								<li class="sales-this-month">
									<span class="fa fa-dollar"></span>
									<a href="<?php echo get_wcfm_reports_url( ); ?>">
										<?php printf( __( '<strong>%s</strong><br /> net commission in this month', 'wc-frontend-manager' ), wc_price( $earned ) ); ?>
									</a>
								</li>
								<li class="sales-this-month">
									<span class="fa fa-money"></span>
									<a href="<?php echo get_wcfm_reports_url( ); ?>">
										<?php printf( __( '<strong>%s</strong><br /> paid commission in this month', 'wc-frontend-manager' ), wc_price( $commission ) ); ?>
									</a>
								</li>
							<?php } ?>
							<?php if( $wcfm_is_allow_reports = apply_filters( 'wcfm_is_allow_reports', true ) ) { ?>
								<li class="sales-count-this-month">
									<span class="fa fa-cubes"></span>
									<a href="<?php echo apply_filters( 'sales_by_product_report_url', get_wcfm_reports_url( ), '' ); ?>">
										<?php printf( __( '<strong>%s items</strong><br /> net sales in this month', 'wc-frontend-manager' ), $total_sell ); ?>
									</a>
								</li>
								<?php
								if ( ( $top_seller = $this->get_top_seller() ) && $top_seller->qty ) {
									?>
									<li class="best-seller-this-month">
										<span class="fa fa-cube"></span>
										<a href="<?php echo apply_filters( 'sales_by_product_report_url',  get_wcfm_reports_url( ), $top_seller->product_id ); ?>">
											<?php printf( __( '%s top seller in last 7 days (sold %d)', 'wc-frontend-manager' ), '<strong>' . get_the_title( $top_seller->product_id ) . '</strong><br />', $top_seller->qty ); ?>
										</a>
									</li>
								<?php
								}
								?>
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
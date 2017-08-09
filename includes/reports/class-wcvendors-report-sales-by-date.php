<?php
/**
 * Report class responsible for handling sales by date reports.
 *
 * @since      2.1.0
 *
 * @package    WooCommerce Frontend Manager
 * @subpackage wcfm/includes/reports
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( WC()->plugin_path() . '/includes/admin/reports/class-wc-admin-report.php' );

class WC_Vendors_Report_Sales_By_Date extends WC_Admin_Report {
	public $chart_colors = array();
	public $current_range;
	private $report_data;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return bool
	 */
	public function __construct() {
		global $WCFM;
		$current_range = ! empty( $_GET['range'] ) ? sanitize_text_field( $_GET['range'] ) : '7day';

		if ( ! in_array( $current_range, array( 'custom', 'year', 'last_month', 'month', '7day' ) ) ) {
			$current_range = '7day';
		}

		$this->current_range = $current_range;
	}

	/**
	 * Get the report data
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return array of objects
	 */
	public function get_report_data() {
		global $WCFM;
		if ( empty( $this->report_data ) ) {
			$this->query_report_data();
		}

		return $this->report_data;
	}

	/**
	 * Get the report based on parameters
	 *
	 * @access public
	 * @since 2.1.0
	 * @version 2.1.0
	 * @return array of objects
	 */
	public function query_report_data() {
		global $wpdb, $WCFM;

		$this->report_data = new stdClass;

		$sql = "SELECT * FROM {$wpdb->prefix}pv_commission AS commission";

		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.status != 'reversed'";

		switch( $this->current_range ) {
			case 'year' :
				$sql .= " AND YEAR( commission.time ) = YEAR( CURDATE() )";
				break;

			case 'last_month' :
				$sql .= " AND MONTH( commission.time ) = MONTH( NOW() ) - 1";
				break;

			case 'month' :
				$sql .= " AND MONTH( commission.time ) = MONTH( NOW() )";
				break;

			case 'custom' :
				$start_date = ! empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
				$end_date = ! empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';

				$sql .= " AND DATE( commission.time ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
				break;

			case 'default' :
			case '7day' :
				$sql .= " AND DATE( commission.time ) BETWEEN DATE_SUB( NOW(), INTERVAL 7 DAY ) AND NOW()";
				break;
		}

		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );

		$results = $wpdb->get_results( $wpdb->prepare( $sql, get_current_user_id() ) );

		$total_shipping_amount          = 0.00;
		$total_tax_amount               = 0.00;
		$total_earned_commission_amount = 0.00;
		$total_commission_amount        = 0.00;
		$total_items                    = 0;

		$total_orders = array();

		foreach( $results as $data ) {

			$total_orders[] = $data->order_id;
			
			$total_tax_amount               += (float) sanitize_text_field( $data->tax );
			$total_shipping_amount          += (float) sanitize_text_field( $data->total_shipping );
			$total_earned_commission_amount += (float) sanitize_text_field( $data->total_due );
			$total_items                    += (int)   sanitize_text_field( $data->qty );

			// show only paid commissions
			if ( 'paid' === $data->status ) {
				$total_commission_amount   += (float) sanitize_text_field( $data->total_due );
				if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { $total_commission_amount += (float) sanitize_text_field( $data->tax ); } 
				if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { $total_commission_amount += (float) sanitize_text_field( $data->total_shipping ); }
			}
		}

		$total_orders = count( array_unique( $total_orders ) );
		$total_sales = $total_earned_commission_amount;
		if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { $total_sales += $total_tax_amount; } 
		if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { $total_sales += $total_shipping_amount; }

		$this->report_data->average_sales         = wc_format_decimal( $total_sales / ( $this->chart_interval + 1 ), 2 );
		$this->report_data->total_orders          = $total_orders;
		$this->report_data->total_items           = $total_items;
		$this->report_data->total_shipping        = wc_format_decimal( $total_shipping_amount );
		$this->report_data->total_commission      = wc_format_decimal( $total_commission_amount );
		$this->report_data->total_earned          = wc_format_decimal( $total_sales );
		$this->report_data->total_tax             = wc_format_decimal( $total_tax_amount );
	}

	/**
	 * Get the legend for the main chart sidebar
	 * @return array
	 */
	public function get_chart_legend() {
		global $WCFM;
		$legend = array();
		$data   = $this->get_report_data();

		switch ( $this->chart_groupby ) {
			case 'day' :
				$average_sales_title = sprintf( __( '%s average daily sales', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
			case 'month' :
			default :
				$average_sales_title = sprintf( __( '%s average monthly sales', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->average_sales ) . '</strong>' );
			break;
		}
		
		$legend[] = array(
			'title'            => sprintf( __( '%s total earned commission', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_earned ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the earned commission including shipping and taxes if applicable.', 'wc-frontend-manager' ),
			'color'            => $this->chart_colors['earned'],
			'highlight_series' => 4
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s total paid commission', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_commission ) . '</strong>' ),
			'placeholder'      => __( 'This is the sum of the commission paid including shipping and taxes if applicable.', 'wc-frontend-manager' ),
			'color'            => $this->chart_colors['commission'],
			'highlight_series' => 5
		);

		if ( $data->average_sales > 0 ) {
			$legend[] = array(
				'title'            => $average_sales_title,
				'color'            => $this->chart_colors['average'],
				'highlight_series' => 3
			);
		}

		$legend[] = array(
			'title'            => sprintf( __( '%s orders placed', 'wc-frontend-manager' ), '<strong>' . $data->total_orders . '</strong>' ),
			'color'            => $this->chart_colors['order_count'],
			'highlight_series' => 0
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s items purchased', 'wc-frontend-manager' ), '<strong>' . $data->total_items . '</strong>' ),
			'color'            => $this->chart_colors['item_count'],
			'highlight_series' => 1
		);

		$legend[] = array(
			'title'            => sprintf( __( '%s charged for shipping', 'wc-frontend-manager' ), '<strong>' . wc_price( $data->total_shipping ) . '</strong>' ),
			'color'            => $this->chart_colors['shipping_amount'],
			'highlight_series' => 2
		);

		return $legend;
	}

	/**
	 * Output the report
	 */
	public function output_report() {
		global $WCFM;
		$ranges = array(
			'year'         => __( 'Year', 'wc-frontend-manager' ),
			'last_month'   => __( 'Last Month', 'wc-frontend-manager' ),
			'month'        => __( 'This Month', 'wc-frontend-manager' ),
			'7day'         => __( 'Last 7 Days', 'wc-frontend-manager' ),
		);

		$this->chart_colors = array(
			'average'          => '#95a5a6',
			'order_count'      => '#dbe1e3',
			'item_count'       => '#ecf0f1',
			'shipping_amount'  => '#FF7400',
			'earned'           => '#4096EE',
			'commission'       => '#008C00',
		);

		$current_range = $this->current_range;

		$this->calculate_current_range( $this->current_range );

		include( WC()->plugin_path() . '/includes/admin/views/html-report-by-date.php' );
	}

	/**
	 * Output an export link
	 */
	public function get_export_button() {
		global $WCFM;
		?>
		<a
			href="#"
			download="report-<?php echo esc_attr( $this->current_range ); ?>-<?php echo date_i18n( 'Y-m-d', current_time('timestamp') ); ?>.csv"
			class="export_csv"
			data-export="chart"
			data-xaxes="<?php esc_attr_e( 'Date', 'wc-frontend-manager' ); ?>"
			data-exclude_series="2"
			data-groupby="<?php echo $this->chart_groupby; ?>"
			data-range="<?php echo $this->current_range; ?>"
			data-custom-range="<?php echo 'custom' === $this->current_range ? $this->start_date . '-' . $this->end_date : ''; ?>"
		>
			<?php esc_html_e( 'Export CSV', 'wc-frontend-manager' ); ?>
		</a>
		<?php
	}

	/**
	 * Round our totals correctly
	 * @param  string $amount
	 * @return string
	 */
	private function round_chart_totals( $amount ) {
		global $WCFM;
		
		if ( is_array( $amount ) ) {
			return array( $amount[0], wc_format_decimal( $amount[1], wc_get_price_decimals() ) );
		} else {
			return wc_format_decimal( $amount, wc_get_price_decimals() );
		}
	}

	/**
	 * Get the main chart
	 *
	 * @return string
	 */
	public function get_main_chart() {
		global $wp_locale, $wpdb, $WCFM;
		
		// Generate Data for total earned commision
		$select = "SELECT COUNT( DISTINCT commission.order_id ) AS count, SUM( commission.qty ) AS order_item_count, SUM( commission.total_shipping ) AS total_shipping, SUM( commission.tax ) AS total_tax, SUM( commission.total_due ) AS total_commission, commission.time";

		$sql = $select;
		$sql .= " FROM {$wpdb->prefix}pv_commission AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.status != 'reversed'";

		switch( $this->current_range ) {
			case 'year' :
				$sql .= " AND YEAR( commission.time ) = YEAR( CURDATE() )";
				break;

			case 'last_month' :
				$sql .= " AND MONTH( commission.time ) = MONTH( NOW() ) - 1";
				break;

			case 'month' :
				$sql .= " AND MONTH( commission.time ) = MONTH( NOW() )";
				break;

			case 'custom' :
				$start_date = ! empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
				$end_date = ! empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';

				$sql .= " AND DATE( commission.time ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
				break;

			case 'default' :
			case '7day' :
				$sql .= " AND DATE( commission.time ) BETWEEN DATE_SUB( NOW(), INTERVAL 7 DAY ) AND NOW()";
				break;
		}
			
		$sql .= " GROUP BY DATE( commission.time )";
			
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
		
		$results = $wpdb->get_results( $wpdb->prepare( $sql, get_current_user_id() ) );

		// Prepare data for report
		$order_counts         = $this->prepare_chart_data( $results, 'time', 'count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$order_item_counts    = $this->prepare_chart_data( $results, 'time', 'order_item_count', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$shipping_amounts     = $this->prepare_chart_data( $results, 'time', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$tax_amounts          = $this->prepare_chart_data( $results, 'time', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_commission     = $this->prepare_chart_data( $results, 'time', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );

		$total_earned = array();
		foreach ( $total_commission as $order_amount_key => $order_amount_value ) {
			$total_earned[ $order_amount_key ] = $order_amount_value;
			if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { 
			  $total_earned[ $order_amount_key ][1] += $tax_amounts[ $order_amount_key ][1]; 
			} 
			if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { 
			  $total_earned[ $order_amount_key ][1] += $shipping_amounts[ $order_amount_key ][1]; 
			}
		}
		
		// Generate Data for Paid Commissions
		$select = "SELECT COUNT( DISTINCT commission.order_id ) AS count, SUM( commission.qty ) AS order_item_count, SUM( commission.total_shipping ) AS total_shipping, SUM( commission.tax ) AS total_tax, SUM( commission.total_due ) AS total_commission, commission.time";

		$sql = $select;
		$sql .= " FROM {$wpdb->prefix}pv_commission AS commission";
		$sql .= " WHERE 1=1";
		$sql .= " AND commission.vendor_id = %d";
		$sql .= " AND commission.status = 'paid'";

		switch( $this->current_range ) {
			case 'year' :
				$sql .= " AND YEAR( commission.time ) = YEAR( CURDATE() )";
				break;

			case 'last_month' :
				$sql .= " AND MONTH( commission.time ) = MONTH( NOW() ) - 1";
				break;

			case 'month' :
				$sql .= " AND MONTH( commission.time ) = MONTH( NOW() )";
				break;

			case 'custom' :
				$start_date = ! empty( $_GET['start_date'] ) ? sanitize_text_field( $_GET['start_date'] ) : '';
				$end_date = ! empty( $_GET['end_date'] ) ? sanitize_text_field( $_GET['end_date'] ) : '';

				$sql .= " AND DATE( commission.time ) BETWEEN '" . $start_date . "' AND '" . $end_date . "'";
				break;

			case 'default' :
			case '7day' :
				$sql .= " AND DATE( commission.time ) BETWEEN DATE_SUB( NOW(), INTERVAL 7 DAY ) AND NOW()";
				break;
		}
			
		$sql .= " GROUP BY DATE( commission.time )";
			
		// Enable big selects for reports
		$wpdb->query( 'SET SESSION SQL_BIG_SELECTS=1' );
		
		$results = $wpdb->get_results( $wpdb->prepare( $sql, get_current_user_id() ) );
		
		$shipping_amounts     = $this->prepare_chart_data( $results, 'time', 'total_shipping', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$tax_amounts          = $this->prepare_chart_data( $results, 'time', 'total_tax', $this->chart_interval, $this->start_date, $this->chart_groupby );
		
		$total_commission     = $this->prepare_chart_data( $results, 'time', 'total_commission', $this->chart_interval, $this->start_date, $this->chart_groupby );
	
		$total_paid_commission = array();
		foreach ( $total_commission as $order_amount_key => $order_amount_value ) {
			$total_paid_commission[ $order_amount_key ] = $order_amount_value;
			if ( WC_Vendors::$pv_options->get_option( 'give_tax' ) ) { 
				$total_paid_commission[ $order_amount_key ][1] += $tax_amounts[ $order_amount_key ][1];
			} 
			if ( WC_Vendors::$pv_options->get_option( 'give_shipping' ) ) { 
				$total_paid_commission[ $order_amount_key ][1] += $shipping_amounts[ $order_amount_key ][1];
			}
		}
	
		// Encode in json format
		$chart_data = json_encode( array(
			'order_counts'      => array_values( $order_counts ),
			'order_item_counts' => array_values( $order_item_counts ),
			'shipping_amounts'  => array_map( array( $this, 'round_chart_totals' ), array_values( $shipping_amounts ) ),
			'total_earned'      => array_map( array( $this, 'round_chart_totals' ), array_values( $total_earned ) ),
			'total_commission'  => array_map( array( $this, 'round_chart_totals' ), array_values( $total_paid_commission ) ),
		) );
		?>
		<div class="chart-container">
			<div class="chart-placeholder main"></div>
		</div>
		<script type="text/javascript">

			var main_chart;

			jQuery(function(){
				var order_data = jQuery.parseJSON( '<?php echo $chart_data; ?>' );
				var drawGraph = function( highlight ) {
					var series = [
						{
							label: "<?php echo esc_js( __( 'Number of orders', 'wc-frontend-manager' ) ) ?>",
							data: order_data.order_counts,
							color: '<?php echo $this->chart_colors['order_count']; ?>',
							bars: { fillColor: '<?php echo $this->chart_colors['order_count']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'left' },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Number of items sold', 'wc-frontend-manager' ) ) ?>",
							data: order_data.order_item_counts,
							color: '<?php echo $this->chart_colors['item_count']; ?>',
							bars: { fillColor: '<?php echo $this->chart_colors['item_count']; ?>', fill: true, show: true, lineWidth: 0, barWidth: <?php echo $this->barwidth; ?> * 0.5, align: 'center' },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Shipping amount', 'wc-frontend-manager' ) ) ?>",
							data: order_data.shipping_amounts,
							yaxis: 2,
							color: '<?php echo $this->chart_colors['shipping_amount']; ?>',
							points: { show: true, radius: 5, lineWidth: 2, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							prepend_tooltip: "<?php echo get_woocommerce_currency_symbol(); ?>"
						},
						{
							label: "<?php echo esc_js( __( 'Average sales amount', 'wc-frontend-manager' ) ) ?>",
							data: [ [ <?php echo min( array_keys( $total_commission ) ); ?>, <?php echo $this->report_data->average_sales; ?> ], [ <?php echo max( array_keys( $total_commission ) ); ?>, <?php echo $this->report_data->average_sales; ?> ] ],
							yaxis: 2,
							color: '<?php echo $this->chart_colors['average']; ?>',
							points: { show: false },
							lines: { show: true, lineWidth: 2, fill: false },
							shadowSize: 0,
							hoverable: false
						},
						{
							label: "<?php echo esc_js( __( 'Total Earned Commission Amount', 'wc-frontend-manager' ) ) ?>",
							data: order_data.total_earned,
							yaxis: 2,
							color: '<?php echo $this->chart_colors['earned']; ?>',
							points: { show: true, radius: 6, lineWidth: 4, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 3, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
						},
						{
							label: "<?php echo esc_js( __( 'Total Paid Commission Amount', 'wc-frontend-manager' ) ) ?>",
							data: order_data.total_commission,
							yaxis: 2,
							color: '<?php echo $this->chart_colors['commission']; ?>',
							points: { show: true, radius: 6, lineWidth: 4, fillColor: '#fff', fill: true },
							lines: { show: true, lineWidth: 3, fill: false },
							shadowSize: 0,
							<?php echo $this->get_currency_tooltip(); ?>
						}
					];

					if ( highlight !== 'undefined' && series[ highlight ] ) {
						highlight_series = series[ highlight ];

						highlight_series.color = '#73880A';

						if ( highlight_series.bars ) {
							highlight_series.bars.fillColor = '#D15600';
						}

						if ( highlight_series.lines ) {
							highlight_series.lines.lineWidth = 3;
						}
					}

					main_chart = jQuery.plot(
						jQuery('.chart-placeholder.main'),
						series,
						{
							legend: {
								show: false
							},
							grid: {
								color: '#aaa',
								borderColor: 'transparent',
								borderWidth: 0,
								hoverable: true
							},
							xaxes: [ {
								color: '#aaa',
								position: "bottom",
								tickColor: 'transparent',
								mode: "time",
								timeformat: "<?php if ( $this->chart_groupby == 'day' ) echo '%d %b'; else echo '%b'; ?>",
								monthNames: <?php echo json_encode( array_values( $wp_locale->month_abbrev ) ) ?>,
								tickLength: 1,
								minTickSize: [1, "<?php echo $this->chart_groupby; ?>"],
								font: {
									color: "#aaa"
								}
							} ],
							yaxes: [
								{
									min: 0,
									minTickSize: 1,
									tickDecimals: 0,
									color: '#d4d9dc',
									font: { color: "#aaa" }
								},
								{
									position: "right",
									min: 0,
									tickDecimals: 2,
									alignTicksWithAxis: 1,
									color: 'transparent',
									font: { color: "#aaa" }
								}
							],
						}
					);

					jQuery('.chart-placeholder').resize();
				}

				drawGraph();

				jQuery('.highlight_series').hover(
					function() {
						drawGraph( jQuery(this).data('series') );
					},
					function() {
						drawGraph();
					}
				);
			});
		</script>
		<?php
	}
}

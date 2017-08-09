$wcfm_orders_table = '';
$order_status = '';	
$filter_by_date = '';
$commission_status = '';

jQuery(document).ready(function($) {
		
	// Dummy Mark Complete Dummy
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_order_mark_complete_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( "Please upgrade your WC Frontend Manager to Ultimate version and avail this feature." );
				return false;
			});
		});
	});
	
	// Invoice Dummy
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_invoice_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( "Install WC Frontend Manager Ultimate and WooCommerce PDF Invoices & Packing Slips to avail this feature." );
				return false;
			});
		});
	});
	
	// Invoice dummy - vendor
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_pdf_invoice_vendor_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( "Please contact your Store Admin to enable this feature for you." );
				return false;
			});
		});
	});
	
	// Mark Shipped dummy - vendor
	$( document.body ).on( 'updated_wcfm-orders', function() {
		$('.wcfm_wcvendors_order_mark_shipped_dummy').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				alert( "Please contact your Store Admin to enable this feature for you." );
				return false;
			});
		});
	});
	
	$wcfm_orders_table = $('#wcfm-orders').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 4 },
										{ responsivePriority: 5 },
										{ responsivePriority: 3 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }, 
										{ "targets": 5, "orderable" : false },
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action            = 'wcfm_ajax_controller',
				d.controller        = 'wcfm-orders',
				d.order_status      = GetURLParameter( 'order_status' ),
				d.m                 = $filter_by_date,
				d.commission_status = $commission_status
			},
			"complete" : function () {
				initiateTip();
				
				$('.show_order_items').click(function(e) {
					e.preventDefault();
					$(this).next('div.order_items').toggleClass( "order_items_visible" );
					return false;
				});
				
				// Fire wcfm-orders table refresh complete
				$( document.body ).trigger( 'updated_wcfm-orders' );
			}
		}
	} );
	
	// Dashboard FIlter
	if( $('.wcfm_filters_wrap').length > 0 ) {
		$('.dataTable').before( $('.wcfm_filters_wrap') );
	}
	
} );
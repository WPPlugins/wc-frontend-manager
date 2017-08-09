$product_type = '';	
$product_cat = '';

jQuery(document).ready(function($) {
	
	$wcfm_products_table = $('#wcfm-products').DataTable( {
		"processing": true,
		"serverSide": true,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 2 },
										{ responsivePriority: 1 },
										{ responsivePriority: 3 },
										{ responsivePriority: 7 },
										{ responsivePriority: 6 },
										{ responsivePriority: 4 },
										{ responsivePriority: 5 },
										{ responsivePriority: 7 },
										{ responsivePriority: 1 }
								],
		"columnDefs": [ { "targets": 0, "orderable" : false }, 
									  { "targets": 1, "orderable" : false }, 
										{ "targets": 2, "orderable" : false }, 
										{ "targets": 3, "orderable" : false }, 
										{ "targets": 4, "orderable" : false }, 
										{ "targets": 5, "orderable" : false },
										{ "targets": 6, "orderable" : false },
										{ "targets": 7, "orderable" : false },
										{ "targets": 8, "orderable" : false }
									],
		'ajax': {
			"type"   : "POST",
			"url"    : wcfm_params.ajax_url,
			"data"   : function( d ) {
				d.action     = 'wcfm_ajax_controller',
				d.controller = 'wcfm-products',
				d.product_type     = $product_type,
				d.product_cat      = $product_cat,
				d.product_status   = GetURLParameter( 'product_status' )
			},
			"complete" : function () {
				initiateTip();
				if (typeof intiateWCFMuQuickEdit !== 'undefined' && $.isFunction(intiateWCFMuQuickEdit)) intiateWCFMuQuickEdit();
				
				// Fire wcfm-products table refresh complete
				$( document.body ).trigger( 'updated_wcfm-products' );
			}
		}
	} );
	
	// Delete Product
	$( document.body ).on( 'updated_wcfm-products', function() {
		$('.wcfm_product_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm("Are you sure and want to delete this 'Product'?\nYou can't undo this action ...");
				if(rconfirm) deleteWCFMProduct($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMProduct(item) {
		jQuery('#wcfm-products_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'delete_wcfm_product',
			proid : item.data('proid')
		}	
		jQuery.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if($wcfm_products_table) $wcfm_products_table.ajax.reload();
				jQuery('#wcfm-products_wrapper').unblock();
			}
		});
	}
	
	// Product FIlter
	if( $('.wcfm_products_filter_wrap').length > 0 ) {
		$('#wcfm-products').before( $('.wcfm_products_filter_wrap') );
	}
	
} );
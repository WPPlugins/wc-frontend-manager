jQuery(document).ready(function($) {
	// Invoice Dummy
	$('.wcfm_pdf_invoice_dummy').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			alert( "Install WC Frontend Manager Ultimate and WooCommerce PDF Invoices & Packing Slips to avail this feature." );
			return false;
		});
	});
	
	// Invoice dummy - vendor
	$('.wcfm_pdf_invoice_vendor_dummy').each(function() {
		$(this).click(function(event) {
			event.preventDefault();
			alert( "Please contact your Store Admin to enable this feature for you." );
			return false;
		});
	});
	
} );
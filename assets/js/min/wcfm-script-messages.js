jQuery(document).ready( function($) {
	$wcfm_messages_table = '';
	
	// TinyMCE intialize - Description
	if( $('#wcfm_messages').length > 0 ) {
		var descTinyMCE = tinymce.init({
																	selector: '#wcfm_messages',
																	height: 150,
																	menubar: false,
																	plugins: [
																		'advlist autolink lists link image charmap print preview anchor',
																		'searchreplace visualblocks code fullscreen',
																		'insertdatetime media table contextmenu paste code'
																	],
																	toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify |  bullist numlist outdent indent | link image',
																	content_css: '//www.tinymce.com/css/codepen.min.css',
																	statusbar: false
																});
	}
	
	if( $("#direct_to").length > 0 ) {
		$("#direct_to").select2({
			placeholder: "Choose Vendor ..."
		});
	}
	
	// Save Settings
	$('#wcfm_messages_save_button').click(function(event) {
	  event.preventDefault();
	  
	  var wcfm_messages = '';
	  if( typeof tinymce != 'undefined' ) wcfm_messages = tinymce.get('wcfm_messages').getContent();
	  var direct_to = 0;
	  if( $('#direct_to').length > 0 ) direct_to = $('#direct_to').val();
  
	  // Validations
	  $is_valid = true; //wcfm_coupons_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm_messages_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-message-sent',
				wcfm_messages      : wcfm_messages,
				direct_to          : direct_to
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						tinymce.get('wcfm_messages').setContent('');
						$('#direct_to').val('');
						audio.play();
						$wcfm_messages_table.ajax.reload();
						$('#wcfm_messages_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
						if( $response_json.file ) $('#wcfm_custom_css-css').attr( 'href', $response_json.file );
					} else {
						audio.play();
						$('#wcfm_messages_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_messages_form').unblock();
				}
			});	
		}
	});
	
	$message_status = 'unread';
	if( $('#filter-by-status').length > 0 ) {
		$('#filter-by-status').on('change', function() {
			$message_status = $('#filter-by-status').val();
			$wcfm_messages_table.ajax.reload();
		});
	}
	
	$message_type = 'all';
	$message_type = GetURLParameter( 'type' );
	if( !$message_type ) $message_type = 'all';
	
	if( $('#filter-by-type').length > 0 ) {
		$('#filter-by-type').on('change', function() {
			$message_type = $('#filter-by-type').val();
			$wcfm_messages_table.ajax.reload();
		});
	}
	
	$wcfm_messages_table = $('#wcfm-messages').DataTable( {
		"processing": true,
		"serverSide": true,
		"bFilter"   : false,
		"responsive": true,
		"language"  : $.parseJSON(dataTables_language),
		"columns"   : [
										{ responsivePriority: 1 },
										{ responsivePriority: 2 },
										{ responsivePriority: 4 },
										{ responsivePriority: 3 },
										{ responsivePriority: 5 },
										{ responsivePriority: 1 },
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
				d.controller        = 'wcfm-messages',
				d.message_type      = $message_type,
				d.message_status    = $message_status
			},
			"complete" : function () {
				initiateTip();
				
				// Fire wcfm-orders table refresh complete
				$( document.body ).trigger( 'updated_wcfm-messages' );
			}
		}
	} );
	
	// Mark Messages as Read
	$( document.body ).on( 'updated_wcfm-messages', function() {
		$('.wcfm_messages_mark_read').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				//var rconfirm = confirm("Are you sure and want to 'Mark as Complete' this Order?");
				markReadWCFMMessage($(this));
				return false;
			});
		});
	});
	
	function markReadWCFMMessage(item) {
		$('#wcfm-messages_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'wcfm_messages_mark_read',
			messageid : item.data('messageid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_messages_table.ajax.reload();
				$('#wcfm-messages_wrapper').unblock();
			}
		});
	}
	
	// Delete Order
	$( document.body ).on( 'updated_wcfm-messages', function() {
		$('.wcfm_message_delete').each(function() {
			$(this).click(function(event) {
				event.preventDefault();
				var rconfirm = confirm("Are you sure and want to delete this 'Message'?\nYou can't undo this action ...");
				if(rconfirm) deleteWCFMMessage($(this));
				return false;
			});
		});
	});
	
	function deleteWCFMMessage(item) {
		$('#wcfm-messages_wrapper').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action    : 'delete_wcfm_message',
			messageid : item.data('messageid')
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				$wcfm_messages_table.ajax.reload();
				$('#wcfm-messages_wrapper').unblock();
			}
		});
	}
	
	// Message Board auto Refresher
	var messageBoardRefrsherTime = '';
	function messageBoardRefrsher() {
		clearTimeout(messageBoardRefrsherTime);
		messageBoardRefrsherTime = setTimeout(function() {
			$wcfm_messages_table.ajax.reload();
			messageBoardRefrsher();
		}, 30000 );
	}
	messageBoardRefrsher();
	
	// Orders Filter
	if( $('.wcfm_messages_filter_wrap').length > 0 ) {
		$('#wcfm-messages').before( $('.wcfm_messages_filter_wrap') );
	}
		
});
jQuery(document).ready( function($) {
		
	// TinyMCE intialize - Description
	if( $('#wcfm_knowledgebase').length > 0 ) {
		var descTinyMCE = tinymce.init({
																	selector: '#wcfm_knowledgebase',
																	height: 500,
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
	
	// Save Settings
	$('#wcfm_knowledgebase_save_button').click(function(event) {
	  event.preventDefault();
	  
	  var wcfm_knowledgebase = '';
	  if( typeof tinymce != 'undefined' ) wcfm_knowledgebase = tinymce.get('wcfm_knowledgebase').getContent();
  
	  // Validations
	  $is_valid = true; //wcfm_coupons_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm_knowledgebase_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-knowledgebase',
				wcfm_knowledgebase : wcfm_knowledgebase
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_knowledgebase_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
						if( $response_json.file ) $('#wcfm_custom_css-css').attr( 'href', $response_json.file );
					} else {
						audio.play();
						$('#wcfm_knowledgebase_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_knowledgebase_form').unblock();
				}
			});	
		}
	});
		
});
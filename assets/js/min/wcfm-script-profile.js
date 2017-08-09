jQuery(document).ready( function($) {
	// Collapsible
  $('.page_collapsible').collapsible({
		defaultOpen: 'wcfm_profile_personal_head',
		speed: 'slow',
		loadOpen: function (elem) { //replace the standard open state with custom function
		  elem.next().show();
		},
		loadClose: function (elem, opts) { //replace the close state with custom function
			elem.next().hide();
		}
	});	
	
	// TinyMCE intialize - About
	if( $('#about').length > 0 ) {
		var descTinyMCE = tinymce.init({
																	selector: '#about',
																	height: 120,
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
		
	if( $(".country_select").length > 0 ) {
		$(".country_select").select2({
			placeholder: "Choose ..."
		});
	}
	
	// Save Profile
	$('#wcfmprofile_save_button').click(function(event) {
	  event.preventDefault();
	  
	  var about = '';
		if( typeof tinymce != 'undefined' ) about = tinymce.get('about').getContent();
  
	  // Validations
	  $is_valid = true; //wcfm_coupons_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm_profile_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-profile',
				wcfm_profile_form  : $('#wcfm_profile_form').serialize(),
				about              : about
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_profile_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
					} else {
						audio.play();
						$('#wcfm_profile_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_profile_form').unblock();
				}
			});	
		}
	});
});
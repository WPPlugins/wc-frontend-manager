jQuery(document).ready( function($) {
	// Collapsible
  $('.page_collapsible').collapsible({
		defaultOpen: 'wcfm_settings_form_vendor_head',
		speed: 'slow',
		loadOpen: function (elem) { //replace the standard open state with custom function
		  elem.next().show();
		},
		loadClose: function (elem, opts) { //replace the close state with custom function
			elem.next().hide();
		}
	});
	
	if( $("#timezone").length > 0 ) {
		$("#timezone").select2({
			placeholder: "Choose ..."
		});
	}
	
	// TinyMCE intialize - Description
	if( $('#shop_description').length > 0 ) {
		var descTinyMCE = tinymce.init({
																	selector: '#shop_description',
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
	
	// Save Settings
	$('#wcfmsettings_save_button').click(function(event) {
	  event.preventDefault();
	  
	  var profile = '';
	  if( typeof tinymce != 'undefined' ) profile = tinymce.get('shop_description').getContent();
  
	  // Validations
	  $is_valid = true; //wcfm_coupons_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm_settings_form').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var data = {
				action             : 'wcfm_ajax_controller',
				controller         : 'wcfm-settings',
				wcfm_settings_form : $('#wcfm_settings_form').serialize(),
				profile            : profile
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_settings_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown();
						if( $response_json.file ) $('#wcfm_custom_css-css').attr( 'href', $response_json.file );
					} else {
						audio.play();
						$('#wcfm_settings_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					$('#wcfm_settings_form').unblock();
				}
			});	
		}
	});
	
	$('.multi_input_holder').each(function() {
	  var multi_input_holder = $(this);
	  addMultiInputProperty(multi_input_holder);
	});
	
	function addMultiInputProperty(multi_input_holder) {
		var multi_input_limit = multi_input_holder.data('limit');
		if( typeof multi_input_limit == 'undefined' ) multi_input_limit = -1;
	  if(multi_input_holder.children('.multi_input_block').length == 1) multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
	  if( multi_input_holder.children('.multi_input_block').length == multi_input_limit )  multi_input_holder.find('.add_multi_input_block').hide();
	  else multi_input_holder.find('.add_multi_input_block').show();
    multi_input_holder.children('.multi_input_block').each(function() {
      if($(this)[0] != multi_input_holder.children('.multi_input_block:last')[0]) {
        $(this).children('.add_multi_input_block').remove();
      }
    });
    
    multi_input_holder.children('.multi_input_block').children('.add_multi_input_block').off('click').on('click', function() {
      var holder_id = multi_input_holder.attr('id');
      var holder_name = multi_input_holder.data('name');
      var multi_input_blockCount = multi_input_holder.data('length');
      multi_input_blockCount++;
      var multi_input_blockEle = multi_input_holder.children('.multi_input_block:first').clone(false);
      
      multi_input_blockEle.find('textarea,input:not(input[type=button],input[type=submit],input[type=checkbox],input[type=radio])').val('');
       multi_input_blockEle.find('input[type=checkbox]').attr('checked', false);
      multi_input_blockEle.children('.wcfm-wp-fields-uploader,.multi_input_block_element:not(.multi_input_holder)').each(function() {
        var ele = $(this);
        var ele_name = ele.data('name');
				ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
				ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
      });
      
      // Nested multi-input block property
      multi_input_blockEle.children('.multi_input_holder').each(function() {
        setNestedMultiInputIndex($(this), holder_id, holder_name, multi_input_blockCount);
      });
       
      
      multi_input_blockEle.children('.remove_multi_input_block').off('click').on('click', function() {
      	var remove_ele_parent = $(this).parent().parent();
				var addEle = remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').clone(true);
				$(this).parent().remove();
				remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').remove();
				remove_ele_parent.children('.multi_input_block:last').append(addEle);
				if( remove_ele_parent.children('.multi_input_block').length == multi_input_limit ) remove_ele_parent.find('.add_multi_input_block').hide();
				else remove_ele_parent.find('.add_multi_input_block').show();
				if(remove_ele_parent.children('.multi_input_block').length == 1) remove_ele_parent.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
			});
      
      multi_input_blockEle.children('.add_multi_input_block').remove();
      multi_input_holder.append(multi_input_blockEle);
      multi_input_holder.children('.multi_input_block:last').append($(this));
      if(multi_input_holder.children('.multi_input_block').length > 1) multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'block');
      if( multi_input_holder.children('.multi_input_block').length == multi_input_limit ) multi_input_holder.find('.add_multi_input_block').hide();
      else multi_input_holder.find('.add_multi_input_block').show();
      multi_input_holder.data('length', multi_input_blockCount);
      
      // Fields Type Property
			multi_input_holder.find('.field_type_options').each(function() {
				$(this).off('change').on('change', function() {
					$(this).parent().find('.field_type_select_options').hide();
					if( $(this).val() == 'select' ) $(this).parent().find('.field_type_select_options').show();
				} ).change();
			} );
    });
    
    if(!multi_input_holder.hasClass('multi_input_block_element')) {
			//multi_input_holder.children('.multi_input_block').css('padding-bottom', '40px');
		}
		if(multi_input_holder.children('.multi_input_block').children('.multi_input_holder').length > 0) {
			//multi_input_holder.children('.multi_input_block').css('padding-bottom', '40px');
		}
    
    multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').off('click').on('click', function() {
    	var remove_ele_parent = $(this).parent().parent();
      var addEle = remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').clone(true);
      $(this).parent().remove();
      remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').remove();
      remove_ele_parent.children('.multi_input_block:last').append(addEle);
      if(remove_ele_parent.children('.multi_input_block').length == 1) remove_ele_parent.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
      if( remove_ele_parent.children('.multi_input_block').length == multi_input_limit ) remove_ele_parent.find('.add_multi_input_block').hide();
      else remove_ele_parent.find('.add_multi_input_block').show();
    });
    
    // Fields Type Property
		multi_input_holder.find('.field_type_options').each(function() {
			$(this).off('change').on('change', function() {
				$(this).parent().find('.field_type_select_options').hide();
				if( $(this).val() == 'select' ) $(this).parent().find('.field_type_select_options').show();
			} ).change();
		} );
  }
  
  function resetMultiInputIndex(multi_input_holder) {
  	var holder_id = multi_input_holder.attr('id');
		var holder_name = multi_input_holder.data('name');
		var multi_input_blockCount = 0;
		
		multi_input_holder.find('.multi_input_block').each(function() {
			$(this).children('.wcfm-wp-fields-uploader,.multi_input_block_element:not(.multi_input_holder)').each(function() {
				var ele = $(this);
				var ele_name = ele.data('name');
				var multiple = ele.attr('multiple');
				if (typeof multiple !== typeof undefined && multiple !== false) {
					ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+'][]');
				} else {
					ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
				}
				ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
			});
			multi_input_blockCount++;
		});
  }
  
  function setNestedMultiInputIndex(nested_multi_input, holder_id, holder_name, multi_input_blockCount) {
		nested_multi_input.children('.multi_input_block:not(:last)').remove();
		var multi_input_id = nested_multi_input.attr('id');
		multi_input_id = multi_input_id.replace(holder_id + '_', '');
		var multi_input_id_splited = multi_input_id.split('_');
		var multi_input_name = '';
		for(var i = 0; i < (multi_input_id_splited.length -1); i++) {
		 if(multi_input_name != '') multi_input_name += '_';
		 multi_input_name += multi_input_id_splited[i];
		}
		nested_multi_input.attr('data-name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']');
		nested_multi_input.attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount);
		nested_multi_input.children('.multi_input_block').children('.wcfm-wp-fields-uploader,.multi_input_block_element:not(.multi_input_holder)').each(function() {
		  var ele = $(this);
		  var ele_name = ele.data('name');
			var multiple = ele.attr('multiple');
			if (typeof multiple !== typeof undefined && multiple !== false) {
				ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+'][0]['+ele_name+'][]');
			} else {
				ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+'][0]['+ele_name+']');
			}
			ele.attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount + '_' + ele_name + '_0');
		});
		
		addMultiInputProperty(nested_multi_input);
		
		if(nested_multi_input.children('.multi_input_block').children('.multi_input_holder').length > 0) nested_multi_input.children('.multi_input_block').css('padding-bottom', '40px');
		
		nested_multi_input.children('.multi_input_block').children('.multi_input_holder').each(function() {
			setNestedMultiInputIndex($(this), holder_id+'_'+multi_input_name+'_0', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']', 0);
		});
	}
	
	/*$('.custom_field_is_group').each( function() {
		$(this).change( function() {
			if( $(this).is(':checked') ) {
				$('.custom_field_is_group_name').show();
			} else {
				$('.custom_field_is_group_name').hide();
			}
		} ).change();
	} );*/
});
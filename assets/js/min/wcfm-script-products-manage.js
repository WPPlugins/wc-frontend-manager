var removed_person_types = [];
jQuery( document ).ready( function( $ ) {
	// Collapsible
  $('.page_collapsible').collapsible({
		defaultOpen: 'wcfm_products_manage_form_inventory_head',
		speed: 'slow',
		loadOpen: function (elem) { //replace the standard open state with custom function
			//console.log(elem);
		  elem.next().show();
		},
		loadClose: function (elem, opts) { //replace the close state with custom function
			//console.log(elem);
			elem.next().hide();
		},
		animateOpen: function(elem, opts){
			$('.collapse-open').addClass('collapse-close').removeClass('collapse-open');
			elem.addClass('collapse-open');
			$('.wcfm-container:not(:first)').stop(true, true).slideUp(opts.speed);
			elem.next().stop(true, true).slideDown(opts.speed);
		}
	});
	
	if( $("#product_cats").length > 0 ) {
		$("#product_cats").select2({
			placeholder: "Choose Categoies ...",
			maximumSelectionLength: $("#product_cats").data('catlimit')
		});
	}
	
	if( $('.product_taxonomies').length > 0 ) {
		$('.product_taxonomies').each(function() {
			$("#" + $(this).attr('id')).select2({
				placeholder: "Choose " + $(this).attr('id') + " ..."
			});
		});
	}
	
	if( $("#upsell_ids").length > 0 ) {
		$("#upsell_ids").select2({
			placeholder: "Choose ..."
		});
	}
	
	if( $("#crosssell_ids").length > 0 ) {
		$("#crosssell_ids").select2({
			placeholder: "Choose ..."
		});
	}
	
	if( $("#grouped_products").length > 0 ) {
		$("#grouped_products").select2({
			placeholder: "Choose ..."
		});
	}
	
	if($('#product_type').length > 0) {
		var pro_types = [ "simple", "variable", "grouped", "external", "booking" ];
		$('#product_type').change(function() {
			var product_type = $(this).val();
			$('#wcfm_products_manage_form .page_collapsible').addClass('wcfm_head_hide');
			$('#wcfm_products_manage_form .wcfm-container').addClass('wcfm_block_hide');
			$('.wcfm_ele').addClass('wcfm_ele_hide');
			
			$('.'+product_type).removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
			
			if( $.inArray( product_type, pro_types ) == -1 ) {
				$('.simple').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
				$('.non-'+product_type).addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
				product_type = 'simple';
			}
			
			if( product_type != 'simple' ) {
				$('#is_downloadable').attr( 'checked', false );
				$('#is_virtual').attr( 'checked', false );
			}
			$('#is_downloadable').change();
			//$('#is_virtual').change();
			
			// Tabheight  
			collapsHeight = 0;
			$('.page_collapsible').each(function() {
				if( !$(this).hasClass('wcfm_head_hide') ) {
					collapsHeight += $(this).height() + 20;
				}
			});  
			resetCollapsHeight($('#sku'));
		}).change();
		
		// Downloadable
		$('#is_downloadable').change(function() {
		  if($(this).is(':checked')) {
		  	$('.downlodable').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  } else {
		  	$('.downlodable').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  }
		}).change();
		$('.is_downloadable_hidden').change(function() {
		  if($(this).val() == 'enable') {
		  	$('.downlodable').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  } else {
		  	$('.downlodable').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  }
		}).change();
		if($('#is_downloadable').length == 0) $('.downlodable').addClass('downloadable_ele_hide');
		
		// Virtual
		$('#is_virtual').change(function() {
		  if($(this).is(':checked')) {
		  	$('.nonvirtual').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  } else {
		  	$('.nonvirtual').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  }
		}).change();
		$('.is_virtual_hidden').change(function() {
		  if($(this).val() == 'enable') {
		  	$('.nonvirtual').addClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  } else {
		  	$('.nonvirtual').removeClass('wcfm_ele_hide wcfm_block_hide wcfm_head_hide');
		  }
		}).change();
	} else {
		$('#wcfm_products_manage_form .page_collapsible').addClass('wcfm_head_hide');
		$('#wcfm_products_manage_form .wcfm-container').addClass('wcfm_block_hide');
		$('.wcfm_ele').addClass('wcfm_ele_hide');
	}
	
		$('.variations').click(function() {
		if($(this).hasClass('collapse-open')) {
			resetVariationsAttributes();
		}
	});
	
	function addVariationManageStockProperty() {
		$('.variation_manage_stock_ele').each(function() {
			$(this).off('change').on('change', function() {
				if($(this).is(':checked')) {
					$(this).parent().find('.variation_non_manage_stock_ele').removeClass('non_stock_ele_hide');
					resetCollapsHeight($('#variations'));
				} else {
					$(this).parent().find('.variation_non_manage_stock_ele').addClass('non_stock_ele_hide');
					resetCollapsHeight($('#variations'));
				}
			}).change();
		});
		
		$('.variation_is_virtual_ele').each(function() {
			$(this).off('change').on('change', function() {
				if($(this).is(':checked')) {
					$(this).parent().find('.variation_non_virtual_ele').addClass('non_virtual_ele_hide');
					resetCollapsHeight($('#variations'));
				} else {
					$(this).parent().find('.variation_non_virtual_ele').removeClass('non_virtual_ele_hide');
					resetCollapsHeight($('#variations'));
				}
			}).change();
		});
		
		$('.variation_is_downloadable_ele').each(function() {
			$(this).off('change').on('change', function() {
				if($(this).is(':checked')) {
					$(this).parent().find('.variation_downloadable_ele').removeClass('downloadable_ele_hide');
					$(this).parent().find('.variation_downloadable_ele').next('.upload_button').removeClass('downloadable_ele_hide');
					resetCollapsHeight($('#variations'));
				} else {
					$(this).parent().find('.variation_downloadable_ele').addClass('downloadable_ele_hide');
					$(this).parent().find('.variation_downloadable_ele').next('.upload_button').addClass('downloadable_ele_hide');
					resetCollapsHeight($('#variations'));
				}
			}).change();
		});
	}
	addVariationManageStockProperty();
	
	$('.manage_stock_ele').change(function() {
	  if($(this).is(':checked')) {
	  	$(this).parent().find('.non_manage_stock_ele').removeClass('non_stock_ele_hide');
	  } else {
	  	$(this).parent().find('.non_manage_stock_ele').addClass('non_stock_ele_hide');
	  }
	}).change();
	
	$('.variation_manage_stock').change(function() {
	  if($(this).is(':checked')) {
	  	$(this).parent().find('.variation_non_manage_stock').removeClass('non_stock_ele_hide');
	  	resetCollapsHeight($('#variations'));
	  } else {
	  	$(this).parent().find('.variation_non_manage_stock').addClass('non_stock_ele_hide');
	  	resetCollapsHeight($('#variations'));
	  }
	}).change();
	
	$('.sales_schedule').click( function() {
	  $('.sales_schedule_ele').toggleClass('sales_schedule_ele_show');
	} );
	
	$('.wcfm_datepicker').each(function() {
	  $(this).datepicker({
      dateFormat : $(this).data('date_format'),
      changeMonth: true,
      changeYear: true
    });
  });
  
  $( "#sale_date_from" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		onClose: function( selectedDate ) {
			$( "#sale_date_upto" ).datepicker( "option", "minDate", selectedDate );
		}
	});
	$( "#sale_date_upto" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'yy-mm-dd',
		onClose: function( selectedDate ) {
			$( "#sale_date_from" ).datepicker( "option", "maxDate", selectedDate );
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
        if(ele.hasClass('wcfm-wp-fields-uploader')) {
					var uploadEle = ele;
					ele_name = uploadEle.find('.multi_input_block_element').data('name');
					uploadEle.find('img').attr('src', '').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_display').addClass('placeHolder');
					uploadEle.find('.multi_input_block_element').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount).attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
					uploadEle.find('.upload_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_button').show();
					uploadEle.find('.remove_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_remove_button').hide();
					addWCFMUploaderProperty(uploadEle);
				} else {
					ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
					ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
        }
        
        if(ele.hasClass('wcfm_datepicker')) {
          ele.removeClass('hasDatepicker').datepicker({
            dateFormat : ele.data('date_format'),
            changeMonth: true,
            changeYear: true
          });
        } else if(ele.hasClass('time_picker')) {
          $('.time_picker').timepicker('remove').timepicker({ 'step': 15 });
          ele.timepicker('remove').timepicker({ 'step': 15 });
        }
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
				resetCollapsHeight(multi_input_holder);
			});
      
      multi_input_blockEle.children('.add_multi_input_block').remove();
      multi_input_holder.append(multi_input_blockEle);
      multi_input_holder.children('.multi_input_block:last').append($(this));
      if(multi_input_holder.children('.multi_input_block').length > 1) multi_input_holder.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'block');
      if( multi_input_holder.children('.multi_input_block').length == multi_input_limit ) multi_input_holder.find('.add_multi_input_block').hide();
      else multi_input_holder.find('.add_multi_input_block').show();
      multi_input_holder.data('length', multi_input_blockCount);
      
      addVariationManageStockProperty();
      resetCollapsHeight(multi_input_holder);
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
      // For Attributes
      if( $(this).parent().find( $('input[data-name="is_taxonomy"]').data('name') == 1 ) ) {
				$taxonomy = $(this).parent().find( $('input[data-name="tax_name"]') ).val();
				$( 'select.wcfm_attribute_taxonomy' ).find( 'option[value="' + $taxonomy + '"]' ).removeAttr( 'disabled' );
			}
      $(this).parent().remove();
      remove_ele_parent.children('.multi_input_block').children('.add_multi_input_block').remove();
      remove_ele_parent.children('.multi_input_block:last').append(addEle);
      if(remove_ele_parent.children('.multi_input_block').length == 1) remove_ele_parent.children('.multi_input_block').children('.remove_multi_input_block').css('display', 'none');
      if( remove_ele_parent.children('.multi_input_block').length == multi_input_limit ) remove_ele_parent.find('.add_multi_input_block').hide();
      else remove_ele_parent.find('.add_multi_input_block').show();
      
      resetCollapsHeight(multi_input_holder);
    });
  }
  
  function resetMultiInputIndex(multi_input_holder) {
  	var holder_id = multi_input_holder.attr('id');
		var holder_name = multi_input_holder.data('name');
		var multi_input_blockCount = 0;
		
		multi_input_holder.find('.multi_input_block').each(function() {
			$(this).children('.wcfm-wp-fields-uploader,.multi_input_block_element:not(.multi_input_holder)').each(function() {
				var ele = $(this);
				var ele_name = ele.data('name');
				if(ele.hasClass('wcfm-wp-fields-uploader')) {
					var uploadEle = ele;
					ele_name = uploadEle.find('.multi_input_block_element').data('name');
					uploadEle.find('img').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_display').addClass('placeHolder');
					uploadEle.find('.multi_input_block_element').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount).attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
					uploadEle.find('.upload_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_button').show();
					uploadEle.find('.remove_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_remove_button').hide();
				} else {
					var multiple = ele.attr('multiple');
					if (typeof multiple !== typeof undefined && multiple !== false) {
						ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+'][]');
					} else {
						ele.attr('name', holder_name+'['+multi_input_blockCount+']['+ele_name+']');
					}
					ele.attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount);
				}
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
		  if(ele.hasClass('wcfm-wp-fields-uploader')) {
				var uploadEle = ele;
				ele_name = uploadEle.find('.multi_input_block_element').data('name');
				uploadEle.find('img').attr('src', '').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_display').addClass('placeHolder');
				uploadEle.find('.multi_input_block_element').attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount + '_' + ele_name + '_0').attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+'][0]['+ele_name+']');
				uploadEle.find('.upload_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_button').show();
				uploadEle.find('.remove_button').attr('id', holder_id + '_' + ele_name + '_' + multi_input_blockCount + '_remove_button').hide();
				addWCFMUploaderProperty(uploadEle);
			} else {
				var multiple = ele.attr('multiple');
				if (typeof multiple !== typeof undefined && multiple !== false) {
					ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+'][0]['+ele_name+'][]');
				} else {
					ele.attr('name', holder_name+'['+multi_input_blockCount+']['+multi_input_name+'][0]['+ele_name+']');
				}
				ele.attr('id', holder_id+'_'+multi_input_name+'_'+multi_input_blockCount + '_' + ele_name + '_0');
		  }
		  
		  if(ele.hasClass('wcfm_datepicker')) {
				ele.removeClass('hasDatepicker').datepicker({
					dateFormat : ele.data('date_format'),
					changeMonth: true,
					changeYear: true
				});
			} else if(ele.hasClass('time_picker')) {
				$('.time_picker').timepicker('remove').timepicker({ 'step': 15 });
				ele.timepicker('remove').timepicker({ 'step': 15 });
			}
		});
		
		addMultiInputProperty(nested_multi_input);
		
		if(nested_multi_input.children('.multi_input_block').children('.multi_input_holder').length > 0) nested_multi_input.children('.multi_input_block').css('padding-bottom', '40px');
		
		nested_multi_input.children('.multi_input_block').children('.multi_input_holder').each(function() {
			setNestedMultiInputIndex($(this), holder_id+'_'+multi_input_name+'_0', holder_name+'['+multi_input_blockCount+']['+multi_input_name+']', 0);
		});
	}
	
	// Add Taxonomy Attribute Rows.
	$( 'button.wcfm_add_attribute' ).on( 'click', function() {
		var attribute    = $( 'select.wcfm_attribute_taxonomy' ).val();
		
		if(attribute) {
			var data         = {
				action:   'wcfmu_generate_taxonomy_attributes',
				taxonomy: attribute
			};
	
			$('#attributes').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			
			$.ajax({
				type:		'POST',
				url: wcfm_params.ajax_url,
				data: data,
				success:	function(response) {
					if(response) {
						$response = $(response);
						$('#attributes').append($response.find('.multi_input_block'));
						addMultiInputProperty($('#attributes'));
						resetMultiInputIndex($('#attributes'));
						$('#product_type').change();
						
						$('#attributes').find('.multi_input_block:last').each(function() {
							$(this).find('input[data-name="is_variation"]').change(function() {
								resetVariationsAttributes();
							});
						});
						
						resetCollapsHeight($('#attributes'));
						
						// Fire wcfm-orders table refresh complete
						$( document.body ).trigger( 'updated_taxonomy_attribute' );
					}
				}
			});
		}

		if ( attribute ) {
			$( 'select.wcfm_attribute_taxonomy' ).find( 'option[value="' + attribute + '"]' ).attr( 'disabled','disabled' );
			$( 'select.wcfm_attribute_taxonomy' ).val( '' );
		}
		
		$('#attributes').unblock();
		
		return false;
	});
	
	if($('#select_attributes').length > 0) {
		$('#attributes').append($('#select_attributes').html());
		$('#select_attributes').remove();
		addMultiInputProperty($('#attributes'));
		resetMultiInputIndex($('#attributes'));
		$('#attributes').find('.multi_input_block').find('select').select2({
			placeholder: "Search for a attribute ..."
		});
	}
	
	$('#attributes').find('.multi_input_block').each(function() {
	  if( $(this).find( $('input[data-name="is_taxonomy"]').data('name') == 1 ) ) {
	  	$taxonomy = $(this).find( 'input[data-name="tax_name"]' ).val();
	  	$( 'select.wcfm_attribute_taxonomy' ).find( 'option[value="' + $taxonomy + '"]' ).attr( 'disabled','disabled' );
	  }
	});
	
	$('#attributes').find('.multi_input_block').each(function() {
	  $(this).find('input[data-name="is_variation"]').change(function() {
	    resetVariationsAttributes();
	  });
	});
	
	function resetVariationsAttributes() {
		$('#variations').block({
			message: null,
			overlayCSS: {
				background: '#fff',
				opacity: 0.6
			}
		});
		var data = {
			action : 'wcfm_generate_variation_attributes',
			wcfm_products_manage_form : $('#wcfm_products_manage_form').serialize()
		}	
		$.ajax({
			type:		'POST',
			url: wcfm_params.ajax_url,
			data: data,
			success:	function(response) {
				if(response) {
					$.each($.parseJSON(response), function(attr_name, attr_value) {
						// Default Attributes
						var default_select_html = '<select name="default_attributes[attribute_'+attr_name.toLowerCase()+']" class="wcfm-select wcfm_ele wcfm_half_ele default_attribute_ele attribute_ele attribute_ele_new variable" data-name="default_attribute_'+attr_name.toLowerCase()+'"><option value="">Any ' + jsUcfirst( attr_name.replace( "pa_", "" ) ) + ' ..</option>';
						$.each(attr_value, function(k, attr_val) {
							default_select_html += '<option value="'+k+'">'+attr_val+'</option>';
						});
						default_select_html += '</select>';
						$('.default_attributes_holder').each(function() {
							if($(this).find('select[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').length > 0) {
								$attr_selected_val = $(this).find('select[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').val();
								$(this).find('select[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').replaceWith($(default_select_html));
								$(this).find('select[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').val($attr_selected_val);
							} else if($(this).find('input[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').length > 0) {
								$attr_selected_val = $(this).find('input[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').val();
								$(this).find('input[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').replaceWith($(default_select_html));
								$(this).find('select[data-name="default_attribute_'+attr_name.toLowerCase()+'"]').val($attr_selected_val);
							} else {
								$(this).append(default_select_html);
							}
						});
						
						// Variation Attributes
						var select_html = '<select name="attribute_'+attr_name.toLowerCase()+'" class="wcfm-select wcfm_ele wcfm_half_ele attribute_ele attribute_ele_new variable multi_input_block_element" data-name="attribute_'+attr_name.toLowerCase()+'"><option value="">Any ' + jsUcfirst( attr_name.replace( "pa_", "" ) ) + ' ..</option>';
						$.each(attr_value, function(k, attr_val) {
							select_html += '<option value="'+k+'">'+attr_val+'</option>';
						});
						select_html += '</select>';
						$('#variations').find('.multi_input_block').each(function() {
							if($(this).find('select[data-name="attribute_'+attr_name.toLowerCase()+'"]').length > 0) {
								$attr_selected_val = $(this).find('select[data-name="attribute_'+attr_name.toLowerCase()+'"]').val();
								$(this).find('select[data-name="attribute_'+attr_name.toLowerCase()+'"]').replaceWith($(select_html));
								$(this).find('select[data-name="attribute_'+attr_name.toLowerCase()+'"]').val($attr_selected_val);
							} else if($(this).find('input[data-name="attribute_'+attr_name.toLowerCase()+'"]').length > 0) {
								$attr_selected_val = $(this).find('input[data-name="attribute_'+attr_name.toLowerCase()+'"]').val();
								$(this).find('input[data-name="attribute_'+attr_name.toLowerCase()+'"]').replaceWith($(select_html));
								$(this).find('select[data-name="attribute_'+attr_name.toLowerCase()+'"]').val($attr_selected_val);
							} else {
								$(this).prepend(select_html);
							}
						});
					});
					$('.default_attribute_ele').change();
					$('.attribute_ele_old').remove();
					$('.attribute_ele_new').addClass('attribute_ele_old').removeClass('attribute_ele_new');
					resetMultiInputIndex($('#variations'));
					$('#variations').unblock();
				}
			},
			dataType: 'html'
		});	
	}
	resetVariationsAttributes();
	
	// Creating Default attributes
	$default_attributes = $('input[data-name="default_attributes_hidden"]');
	if($default_attributes.length > 0) {
		$default_attributes_val = $default_attributes.val();
		if($default_attributes_val.length > 0) {
			$.each($.parseJSON($default_attributes_val), function(attr_key, attr_val) {
				$('.default_attributes_holder').append('<input type="hidden" name="default_attribute_'+attr_key+'" data-name="default_attribute_'+attr_key+'" value="'+attr_val+'" />');
			});
		}
	}
	
	// Creating Variation attributes
	$('#variations').find('.multi_input_block').each(function() {
		$multi_input_block = $(this);
		$multi_input_block.prepend('<div class="wcfm_clearfix"></div>');
	  $attributes = $multi_input_block.find('input[data-name="attributes"]');
	  $attributes_val = $attributes.val();
	  if($attributes_val.length > 0) {
	  	$.each($.parseJSON($attributes_val), function(attr_key, attr_val) {
	  		$multi_input_block.prepend('<input type="hidden" name="'+attr_key+'" data-name="'+attr_key+'" value="'+attr_val+'" />');
	  	});
	  }
	});
	
	// Track Deleting Variation
	var removed_variations = [];
	$('#variations').find('.remove_multi_input_block').click(function() {
	  removed_variations.push($(this).parent().find('.variation_id').val());
	});
	
	if( typeof gmw_forms != 'undefined' ) {
		// Geo my WP Support
		tinymce.PluginManager.add('geomywp', function(editor, url) {
			// Add a button that opens a window
			editor.addButton('geomywp', {
				text: 'GMW Form',
				icon: false,
				onclick: function() {
					// Open window
					editor.windowManager.open({
						title: 'GMW Form',
						body: [
							{type: 'listbox', name: 'form_type', label: 'Form Type', values: [{text: 'Form', value: 'form'}, {text: 'Map', value: 'map'}, {text: 'Results', value: 'results'}]},
							{type: 'listbox', name: 'gmw_forms', label: 'Select Form', values: gmw_forms}
						],
						onsubmit: function(e) {
							// Insert content when the window form is submitted
							if(e.data.form_type == 'results') {
								editor.insertContent('[gmw form="results"]');
							} else if(e.data.form_type == 'map') {
								editor.insertContent('[gmw map="' + e.data.gmw_forms + '"]');
							} else {
								editor.insertContent('[gmw form="' + e.data.gmw_forms + '"]');
							}
						}
					});
				}
			});
		});
		// TinyMCE intialize - Short description
		var shdescTinyMCE = tinymce.init({
																	selector: '#excerpt',
																	height: 75,
																	menubar: false,
																	plugins: [
																		'advlist autolink lists link image charmap print preview anchor',
																		'searchreplace visualblocks code fullscreen',
																		'insertdatetime media table contextmenu paste code geomywp'
																	],
																	toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify |  bullist numlist outdent indent | link image | geomywp',
																	content_css: '//www.tinymce.com/css/codepen.min.css',
																	statusbar: false
																});
		
		// TinyMCE intialize - Description
		var descTinyMCE = tinymce.init({
																	selector: '#description',
																	height: 120,
																	menubar: false,
																	plugins: [
																		'advlist autolink lists link image charmap print preview anchor',
																		'searchreplace visualblocks code fullscreen',
																		'insertdatetime media table contextmenu paste code geomywp'
																	],
																	toolbar: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify |  bullist numlist outdent indent | link image | geomywp',
																	content_css: '//www.tinymce.com/css/codepen.min.css',
																	statusbar: false
																});
	} else {
		// TinyMCE intialize - Short description
		var shdescTinyMCE = tinymce.init({
																	selector: '#excerpt',
																	height: 75,
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
		
		// TinyMCE intialize - Description
		var descTinyMCE = tinymce.init({
																	selector: '#description',
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
	
	function wcfm_products_manage_form_validate() {
		$is_valid = true;
		$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
		var title = $.trim($('#wcfm_products_manage_form').find('#title').val());
		if(title.length == 0) {
			$is_valid = false;
			$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + wcfm_products_manage_messages.no_title).addClass('wcfm-error').slideDown();
			audio.play();
		}
		return $is_valid;
	}
	
	// Draft Product
	$('#wcfm_products_simple_draft_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_products_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var excerpt = tinymce.get('excerpt').getContent();
			var description = tinymce.get('description').getContent();
			var data = {
				action : 'wcfm_ajax_controller',
				controller : 'wcfm-products-manage', 
				wcfm_products_manage_form : $('#wcfm_products_manage_form').serialize(),
				excerpt     : excerpt,
				description : description,
				status : 'draft',
				removed_variations : removed_variations,
				removed_person_types : removed_person_types
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
							if( $response_json.redirect ) window.location = $response_json.redirect;	
						} );
					} else {
						audio.play();
						$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#pro_id').val($response_json.id);
					$('#wcfm-content').unblock();
				}
			});	
		}
	});
	
	// Submit Product
	$('#wcfm_products_simple_submit_button').click(function(event) {
	  event.preventDefault();
	  
	  // Validations
	  $is_valid = wcfm_products_manage_form_validate();
	  
	  if($is_valid) {
			$('#wcfm-content').block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
			var excerpt = tinymce.get('excerpt').getContent();
			var description = tinymce.get('description').getContent();
			var data = {
				action : 'wcfm_ajax_controller',
				controller : 'wcfm-products-manage',
				wcfm_products_manage_form : $('#wcfm_products_manage_form').serialize(),
				excerpt     : excerpt,
				description : description,
				status : 'submit',
				removed_variations : removed_variations,
				removed_person_types : removed_person_types
			}	
			$.post(wcfm_params.ajax_url, data, function(response) {
				if(response) {
					$response_json = $.parseJSON(response);
					$('.wcfm-message').html('').removeClass('wcfm-error').removeClass('wcfm-success').slideUp();
					if($response_json.status) {
						audio.play();
						$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-completed"></span>' + $response_json.message).addClass('wcfm-success').slideDown( "slow", function() {
						  if( $response_json.redirect ) window.location = $response_json.redirect;	
						} );
					} else {
						audio.play();
						$('#wcfm_products_manage_form .wcfm-message').html('<span class="wcicon-status-cancelled"></span>' + $response_json.message).addClass('wcfm-error').slideDown();
					}
					if($response_json.id) $('#pro_id').val($response_json.id);
					$('#wcfm-content').unblock();
				}
			});
		}
	});
	
	
	function playSound(filename) {
	  document.getElementById("sound").innerHTML='<audio autoplay="autoplay"><source src="' + filename + '.mp3" type="audio/mpeg" /><source src="' + filename + '.ogg" type="audio/ogg" /><embed hidden="true" autostart="true" loop="false" src="' + filename +'.mp3" /></audio>';
	}
	
	function jsUcfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }
} );
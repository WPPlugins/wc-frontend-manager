jQuery( document ).ready( function( $ ) {
	// Removing loader slowly
	/*if( wcfm_noloader == 'yes' ) {
		$('#wcfm_page_load').remove();
		$('.wcfm-collapse-content').css( 'opacity', '1' );
	} else {
		$opacity = 9;
		$content_opaticy = 1;
		function removingLoader() {
			if( $opacity == 0 ) {
				$('#wcfm_page_load').fadeOut("slow", function() {  $('#wcfm_page_load').remove(); $('.wcfm-collapse-content').css( 'opacity', '1' ); } );
			} else {
				setTimeout( function() { 
					$('#wcfm_page_load').css( 'opacity', '0.' + $opacity );
					$('.wcfm-collapse-content').css( 'opacity', '0.' + $content_opaticy );
					$opacity -= 1;
					$content_opaticy += 1;
					removingLoader();
				}, 250);
			}
		}
		removingLoader();
	}*/
	
  jQuery('.menu_tip').each(function() {                                                  
		jQuery(this).qtip({
			content: jQuery(this).attr('data-tip'),
			position: {
				my: 'center right',
				at: 'center left',
				viewport: jQuery(window)
			},
			show: {
				event: 'mouseover',
				solo: true
			},
			hide: {
				inactive: 6000,
				fixed: true
			},
			style: {
				classes: 'qtip-dark qtip-shadow qtip-rounded qtip-wcfm-menu-css'
			}
		});
	});
	
	$( '#wcfm_menu .wcfm_menu_item' ).each( function() {
		$(this).mouseover( function() {
			var hideTime;
			$hover_block = $(this).find( '.wcfm_sub_menu_items' );
			clearTimeout(hideTime);
			$hover_block.show( 'slow', function() {
				hideTime = setTimeout(function() {
					$( '.wcfm_sub_menu_items' ).hide( 'slow' );
					$hover_block.removeClass( 'moz_class' );
				}, 30000);  
			} );
		} );
	} );
} );

var audio = new Audio(wcfm_notification_sound);
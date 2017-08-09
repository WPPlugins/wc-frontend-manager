<?php
/**
 * WCFM plugin shortcode
 *
 * Plugin Shortcode output
 *
 * @author 		WC Lovers
 * @package 	wcfm/includes/shortcode
 * @version   1.0.0
 */
 
class WCFM_Frontend_Manager_Shortcode {

	public function __construct() {

	}

	/**
	 * Output the WC Frontend Manager shortcode.
	 *
	 * @access public
	 * @param array $atts
	 * @return void
	 */
	static public function output( $attr ) {
		global $WCFM, $wp, $WCFM_Query;
		$WCFM->nocache();
		
		echo '<div id="wcfm-main-contentainer"> <div id="wcfm-content">';
		
		if ( isset( $wp->query_vars['page'] ) ) {
			
			// Load Scripts
			$WCFM->library->load_scripts( 'wcfm-dashboard' );
			
			// Load Styles
			$WCFM->library->load_styles( 'wcfm-dashboard' );
			
			// Load View
			$WCFM->library->load_views( 'wcfm-dashboard' );
		} else {
			$wcfm_endpoints = $WCFM_Query->get_query_vars();
			
			foreach ( $wcfm_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					// Load Scripts
					$WCFM->library->load_scripts( $key );
					
					// Load Styles
					$WCFM->library->load_styles( $key );
					
					// Load View
					$WCFM->library->load_views( $key );
				}
			}
		}
		
		echo '</div></div>';
	}
}

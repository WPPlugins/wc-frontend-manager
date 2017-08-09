<?php
/**
 * WCFM plugin core
 *
 * Plugin shortcode
 *
 * @author 		WC Lovers
 * @package 	wcfm/core
 * @version   1.0.0
 */
 
class WCFM_Shortcode {

	public $list_product;

	public function __construct() {
		// WC Frontend Manager Shortcode
		add_shortcode('wc_frontend_manager', array(&$this, 'wc_frontend_manager'));
		
		// WC Frontend Manager Endpoint as Shortcode
		add_shortcode('wcfm', array(&$this, 'wcfm_endpoint_shortcode'));
	}

	public function wc_frontend_manager($attr) {
		global $WCFM;
		$this->load_class('wc-frontend-manager');
		return $this->shortcode_wrapper(array('WCFM_Frontend_Manager_Shortcode', 'output'));
	}
	
	/**
	 * WCFM End point as Short Code
	 */
	public function wcfm_endpoint_shortcode( $attr ) {
		global $WCFM, $wp, $WCFM_Query;
		$WCFM->nocache();
		
		echo '<div id="wcfm-main-contentainer"> <div id="wcfm-content">';
		
		$menu = true;
		if ( isset( $attr['menu'] ) && !empty( $attr['menu'] ) && ( 'false' == $attr['menu'] ) ) { $menu = false; } 
		
		if ( !isset( $attr['endpoint'] ) || ( isset( $attr['endpoint'] ) && empty( $attr['endpoint'] ) ) ) {
			
			// Load Scripts
			$WCFM->library->load_scripts( 'wcfm-dashboard' );
			
			// Load Styles
			$WCFM->library->load_styles( 'wcfm-dashboard' );
			
			// Load View
			$WCFM->library->load_views( 'wcfm-dashboard', $menu );
		} else {
			$wcfm_endpoints = $WCFM_Query->get_query_vars();
			
			foreach ( $wcfm_endpoints as $key => $value ) {
				if ( isset( $attr['endpoint'] ) && !empty( $attr['endpoint'] ) && ( $key == $attr['endpoint'] ) ) {
					// Load Scripts
					$WCFM->library->load_scripts( $key );
					
					// Load Styles
					$WCFM->library->load_styles( $key );
					
					// Load View
					$WCFM->library->load_views( $key, $menu );
				}
			}
		}
		
		echo '</div></div>';
	}

	/**
	 * Helper Functions
	 */

	/**
	 * Shortcode Wrapper
	 *
	 * @access public
	 * @param mixed $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public function shortcode_wrapper($function, $atts = array()) {
		ob_start();
		call_user_func($function, $atts);
		return ob_get_clean();
	}

	/**
	 * Shortcode CLass Loader
	 *
	 * @access public
	 * @param mixed $class_name
	 * @return void
	 */
	public function load_class($class_name = '') {
		global $WCFM;
		if ('' != $class_name && '' != $WCFM->token) {
			require_once ( $WCFM->plugin_path . 'includes/shortcodes/class-' . esc_attr($WCFM->token) . '-shortcode-' . esc_attr($class_name) . '.php' );
		}
	}

}
?>
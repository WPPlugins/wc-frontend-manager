<?php
/**
 * WCFMu plugin view
 *
 * WCFM Knowledgebase view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.3.2
 */
 
global $WCFM;

$wcfm_knowledgebase = get_option( 'wcfm_knowledgebase' );

?>

<div class="collapse wcfm-collapse" id="wcfm_knowledgebase_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-graduation-cap"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Knowledgebase', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<form id="wcfm_knowledgebase_form" class="wcfm">
		  <h2><?php _e('Guidelines for Store Vendors', 'wc-frontend-manager' ); ?></h2>
		  <div class="wcfm-clearfix"></div>
		  <?php do_action( 'before_wcfm_knowledgebase' ); ?>
			<div class="wcfm-container">
				<div id="wcfm_knowledgebase_listing_expander" class="wcfm-content">
					<?php
					if( wcfm_is_vendor() ) {
						?>
						<div><?php echo $wcfm_knowledgebase; ?></div>
						<?php
					} else {
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_knowledgebase_fields', array(
																																																	"wcfm_knowledgebase" => array( 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_knowledgebase ),
																																																	) ) );
						?>
						
						<div class="wcfm-message" tabindex="-1"></div>
				
						<div id="wcfm_knowledgebase_submit">
							<input type="submit" name="save-data" value="<?php _e( 'Save', 'wc-frontend-manager' ); ?>" id="wcfm_knowledgebase_save_button" class="wcfm_submit_button" />
						</div>
						<div class="wcfm-clearfix"></div>
						<?php
					}
					?>
				</div>
			</div>
			<?php do_action( 'after_wcfm_knowledgebase' ); ?>
		</form>
	</div>
</div>

<?php
/**
 * WCFMu plugin view
 *
 * WCFM Messages view
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.3.2
 */
 
global $WCFM;

$wcfm_messages = '';

$user_arr = array( 0 => __('All', 'wc-frontend-manager' ) );
if( !wcfm_is_vendor() ) {
	$is_marketplece = wcfm_is_marketplace();
	if( $is_marketplece == 'wcpvendors' ) {
		$vendors = WC_Product_Vendors_Utils::get_vendors();
		if( !empty( $vendors ) ) {
			foreach ( $vendors as $vendor ) {
				$user_arr[$vendor->term_id] = esc_html( $vendor->name );
			}
		}
	} else {
		$args = array(
			'role__in'     => array( 'dc_vendor', 'vendor' ),
			'orderby'      => 'login',
			'order'        => 'ASC',
			'count_total'  => false,
			'fields'       => array( 'ID', 'display_name' )
		 ); 
		$all_users = get_users( $args );
		if( !empty( $all_users ) ) {
			foreach( $all_users as $all_user ) {
				$user_arr[$all_user->ID] = $all_user->display_name;
			}
		}
	}
}

$message_status = 'unread';
$message_type = 'all';
if( isset( $_GET['type'] ) ) $message_type =  $_GET['type'];

?>

<div class="collapse wcfm-collapse" id="wcfm_messages_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-bullhorn"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Message Dashboard', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		
		<?php if( !wcfm_is_vendor() || ( wcfm_is_vendor() && WCFM_Dependencies::wcfmu_plugin_active_check() ) ) { ?>
			<?php do_action( 'before_wcfm_messages_form' ); ?>
			<form id="wcfm_messages_form" class="wcfm">
				<h2><?php if( wcfm_is_vendor() ) { _e('To Store Admin', 'wc-frontend-manager' ); } else { _e('To Store Vendors', 'wc-frontend-manager' ); } ?></h2>
				<div class="wcfm-clearfix"></div>
				<div class="wcfm-container">
					<div id="wcfm_messages_listing_expander" class="wcfm-content">
						<?php
						$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_messages_field_users', array(
																																																		"wcfm_messages" => array( 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele', 'label_class' => 'wcfm_title', 'value' => $wcfm_messages ),
																																																		) ) );
						?>
						
						<div id="wcfm_messages_users_block">
							<?php
							if( !wcfm_is_vendor() && WCFM_Dependencies::wcfmu_plugin_active_check() ) {
								$WCFM->wcfm_fields->wcfm_generate_form_field( apply_filters( 'wcfm_messages_fields', array(
																																																	"direct_to" => array( 'label' => __( 'Direct TO:', 'wc-frontend-manager' ), 'type' => 'select', 'options' => $user_arr, 'attributes' => array( 'style' => 'width: 150px;' ), 'class' => 'wcfm-select wcfm_ele', 'label_class' => 'wcfm_title', 'value' => 1 ),
																																																	) ) );
							}
							?>
						</div>
						<div id="wcfm_messages_submit">
							<input type="submit" name="save-data" value="<?php _e( 'Sent', 'wc-frontend-manager' ); ?>" id="wcfm_messages_save_button" class="wcfm_submit_button" />
						</div>
						<div class="wcfm-clearfix"></div>
						<div class="wcfm-message" tabindex="-1"></div>
						<div class="wcfm-clearfix"></div>
					</div>
				</div>
			</form>
			<?php do_action( 'after_wcfm_messages_form' ); ?>
			<div class="wcfm-clearfix"></div><br />
		<?php } ?>
		
		<?php do_action( 'before_wcfm_messages' ); ?>
		<h2><?php _e('Messages', 'wc-frontend-manager' ); ?></h2>
		
		<div class="wcfm_messages_filter_wrap wcfm_filters_wrap">
		  <select name="filter-by-status" id="filter-by-status">
				<option value='unread' <?php echo selected( $message_status, 'unread', false ); ?>><?php esc_html_e( 'Only Unread', 'wc-frontend-manager' ); ?></option>
				<option value='read' <?php echo selected( $message_status, 'read', false ); ?>><?php esc_html_e( 'Only Read', 'wc-frontend-manager' ); ?></option>
			</select>
			<?php
			if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
					?>
					<select name="filter-by-type" id="dummy-filter-by-type" disabled="disabled" title="<?php wcfmu_feature_help_text_show( 'Message Filter', false, true ); ?>">
						<option value='0'><?php esc_html_e( 'Show all', 'wc-frontend-manager' ); ?></option>
					</select>
					<?php
				}
			} else {
				?>
				<select name="filter-by-type" id="filter-by-type">
					<option value='all' <?php echo selected( $message_type, 'all', false ); ?>><?php esc_html_e( 'Show all', 'wc-frontend-manager' ); ?></option>
					<option value='notice' <?php echo selected( $message_type, 'notice', false ); ?>><?php esc_html_e( 'Only Notice', 'wc-frontend-manager' ); ?></option>
					<option value='message' <?php echo selected( $message_type, 'message', false ); ?>><?php esc_html_e( 'Only Messages', 'wc-frontend-manager' ); ?></option>
				</select>
				<?php
			}
			?>
		</div>
		
		<div class="wcfm-clearfix"></div>
		<div class="wcfm-container">
			<div id="wcfm_messages_listing_expander" class="wcfm-content">
				<table id="wcfm-messages" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>                                                                                      
							<th><?php _e( 'Message', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'From', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'To', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Type', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php _e( 'Message', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'From', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'To', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Type', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_messages' );
		?>
	</div>
</div>

<?php
global $WCFM;

if( !current_user_can( 'edit_products' ) ) {
	wcfm_restriction_message_show( "Products" );
	return;
}

?>

<div class="collapse wcfm-collapse" id="wcfm_products_listing">
	
	<div class="wcfm-page-headig">
		<span class="fa fa-cubes"></span>
		<span class="wcfm-page-heading-text"><?php _e( 'Products', 'wc-frontend-manager' ); ?></span>
		<?php do_action( 'wcfm_page_heading' ); ?>
	</div>
	<div class="wcfm-collapse-content">
		<div id="wcfm_page_load"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
		<?php do_action( 'before_wcfm_products' ); ?>
		<?php
		if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
			?>
			<h2><?php _e('Products Listing', 'wc-frontend-manager' ); ?></h2>
			<?php
			if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
				?>
				<div class="wcfm_products_filter_wrap wcfm_filters_wrap">
					<select name="dummu_dropdown_product_cat" id="dummu_dropdown_product_cat" disabled="disabled" title="<?php wcfmu_feature_help_text_show( 'Product Filter', false, true ); ?>">
						<option value='0'><?php esc_html_e( 'Choose Category', 'wc-frontend-manager' ); ?></option>
					</select>
					
					<select name="dummy_dropdown_product_type" id="dummy_dropdown_product_type" disabled="disabled" title="<?php wcfmu_feature_help_text_show( 'Product Filter', false, true ); ?>">
						<option value=''><?php esc_html_e( 'Product Type', 'wc-frontend-manager' ); ?></option>
					</select>
				</div>
				<?php
			}
		}
		?>
		<?php
		if( $allow_wp_admin_view = apply_filters( 'wcfm_allow_wp_admin_view', true ) ) {
			if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
					?>
					<span class="wcfm_screen_manager_dummy text_tip" data-tip="<?php wcfmu_feature_help_text_show( 'Screen Manager', false, true ); ?>"><span class="fa fa-cog"></span></span>
					<?php
				}
			} else {
				?>
				<a class="wcfm_screen_manager text_tip" href="#" data-screen="product" data-tip="<?php _e( 'Screen Manager', 'wc-frontend-manager' ); ?>"><span class="fa fa-cog"></span></a>
				<?php
			}
			?>
			<a target="_blank" class="wcfm_wp_admin_view text_tip" href="<?php echo admin_url('edit.php?post_type=product'); ?>" data-tip="<?php _e( 'WP Admin View', 'wc-frontend-manager' ); ?>"><span class="fa fa-user-secret"></span></a>
			<?php
		}
		
		if( $is_allow_products_export = apply_filters( 'wcfm_is_allow_products_export', true ) ) {
			?>
			<a class="wcfm_import_export text_tip" href="<?php echo get_wcfm_export_product_url(); ?>" data-screen="product" data-tip="<?php _e( 'Products Export', 'wc-frontend-manager' ); ?>"><span class="fa fa-download"></span></a>
			<?php
		}
		
		if( $is_allow_products_import = apply_filters( 'wcfm_is_allow_products_import', true ) ) {
			if( !WCFM_Dependencies::wcfmu_plugin_active_check() ) {
				if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
					?>
					<a class="wcfm_import_export text_tip" href="#" onclick="return false;" data-tip="<?php wcfmu_feature_help_text_show( 'Products Import', false, true ); ?>"><span class="fa fa-upload"></span></a>
					<?php
				}
			} else {
				?>
				<a class="wcfm_import_export text_tip" href="<?php echo get_wcfm_import_product_url(); ?>" data-tip="<?php _e( 'Products Import', 'wc-frontend-manager' ); ?>"><span class="fa fa-upload"></span></a>
				<?php
			}
		}
		
		if( $has_new = apply_filters( 'wcfm_add_new_product_sub_menu', true ) ) {
			echo '<a id="add_new_product_dashboard" class="add_new_wcfm_ele_dashboard text_tip" href="'.get_wcfm_edit_product_url().'" data-tip="' . __('Add New Product', 'wc-frontend-manager') . '"><span class="fa fa-cube"></span><span class="text">' . __( 'Add New', 'wc-frontend-manager') . '</span></a>';
		}
		?>
		<div class="wcfm-clearfix"></div>
		<div class="wcfm-container">
			<div id="wcfm_products_listing_expander" class="wcfm-content">
				<table id="wcfm-products" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><span class="fa fa-image text_tip" data-tip="<?php _e( 'Image', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Name', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'SKU', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Stock', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Price', 'wc-frontend-manager' ); ?></th>
							<th><span class="fa fa-cubes text_tip" data-tip="<?php _e( 'Type', 'wc-frontend-manager' ); ?>"></span></th>
							<th><span class="fa fa-eye text_tip" data-tip="<?php _e( 'Views', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><span class="fa fa-image text_tip" data-tip="<?php _e( 'Image', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Name', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'SKU', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Status', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Stock', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Price', 'wc-frontend-manager' ); ?></th>
							<th><span class="fa fa-cubes text_tip" data-tip="<?php _e( 'Type', 'wc-frontend-manager' ); ?>"></span></th>
							<th><span class="fa fa-eye text_tip" data-tip="<?php _e( 'Views', 'wc-frontend-manager' ); ?>"></span></th>
							<th><?php _e( 'Date', 'wc-frontend-manager' ); ?></th>
							<th><?php _e( 'Actions', 'wc-frontend-manager' ); ?></th>
						</tr>
					</tfoot>
				</table>
				<div class="wcfm-clearfix"></div>
			</div>
		</div>
		<?php
		do_action( 'after_wcfm_products' );
		?>
	</div>
</div>
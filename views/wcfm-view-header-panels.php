<?php
/**
 * WCFM plugin view
 *
 * WCFM Header Panels View
 *
 * @author 		WC Lovers
 * @package 	wcfm/view
 * @since     2.3.2
 */

global $WCFM, $wpdb, $wp;

$wcfm_options = get_option('wcfm_options');

$is_headpanel_disabled = isset( $wcfm_options['headpanel_disabled'] ) ? $wcfm_options['headpanel_disabled'] : 'no';
if( $is_headpanel_disabled == 'yes' ) return;

$unread_notice = $WCFM->frontend->unreadMessageCount( 'notice' );
$unread_message = $WCFM->frontend->unreadMessageCount( 'message' ); 

$message_type = 'notice';
if( isset( $_GET['type'] ) ) $message_type =  $_GET['type'];
?>

<div class="wcfm_header_panel">
  <?php if( $wcfm_is_allow_notice = apply_filters( 'wcfm_is_allow_notice', true ) ) { ?>
    <a href="<?php echo get_wcfm_messages_url( 'notice' ); ?>" class="fa fa-bell text_tip <?php if( isset( $wp->query_vars['wcfm-messages'] ) && ( $message_type == 'notice' ) ) echo 'active'; ?>" data-tip="<?php _e( 'Notice', 'wc-frontend-manager' ); ?>"><?php if( wcfm_is_vendor() ) { ?><span class="unread_notification_count notice_count"><?php echo $unread_notice; ?></span><?php } ?></a>
  <?php } ?>
  
  <?php 
   if( $wcfm_is_allow_direct_message = apply_filters( 'wcfm_is_allow_direct_message', true ) ) {
   	 if( WCFM_Dependencies::wcfmu_plugin_active_check() ) {
  ?>
    <a href="<?php echo get_wcfm_messages_url( 'message' ); ?>" class="fa fa-send text_tip <?php if( isset( $wp->query_vars['wcfm-messages'] ) && ( $message_type == 'message' ) ) echo 'active'; ?>" data-tip="<?php _e( 'Messages', 'wc-frontend-manager' ); ?>"><span class="unread_notification_count message_count"><?php echo $unread_message; ?></span></a>
  <?php 
  	 } else {
				if( $is_wcfmu_inactive_notice_show = apply_filters( 'is_wcfmu_inactive_notice_show', true ) ) {
					?>
					<span class="fa fa-send text_tip" data-tip="<?php wcfmu_feature_help_text_show( __( 'Direct Message', 'wc-frontend-manager' ), false, true ); ?>"></span>
					<?php
				}
			}
    }
  ?>
  
  <?php if( $wcfm_is_allow_knowledgebase = apply_filters( 'wcfm_is_allow_knowledgebase', true ) ) { ?>
    <a href="<?php echo get_wcfm_knowledgebase_url(); ?>" class="fa fa-book text_tip <?php if( isset( $wp->query_vars['wcfm-knowledgebase'] ) ) echo 'active'; ?>" data-tip="<?php _e( 'Knowledgebase', 'wc-frontend-manager' ); ?>"></a>
  <?php } ?>
  
  <?php if( $wcfm_is_allow_profile = apply_filters( 'wcfm_is_allow_profile', true ) ) { ?>
    <a href="<?php echo get_wcfm_profile_url(); ?>" class="fa fa-user-circle-o text_tip <?php if( isset( $wp->query_vars['wcfm-profile'] ) ) echo 'active'; ?>" data-tip="<?php _e( 'Profile', 'wc-frontend-manager' ); ?>"></a>
  <?php } ?>
  
  <a href="<?php echo esc_url(wp_logout_url( get_wcfm_url() ) ); ?>" class="fa fa-power-off text_tip" data-tip="<?php _e( 'Logout', 'wc-frontend-manager' ); ?>"></a>
</div>